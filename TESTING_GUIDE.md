# SMARTBooking Testing Guide

## Overview

This guide explains how to test your SMARTBooking application with focus on unit tests, feature tests, and integration testing for calendar and notification systems.

## Test Structure

```
tests/
├── Feature/                           # HTTP endpoint & integration tests
│   ├── AuthTest.php                  # Authentication flows
│   ├── BookingTest.php               # Booking CRUD operations
│   ├── CalendarIntegrationTest.php   # Calendar sync tests
│   └── NotificationTest.php          # Notification system tests
├── Unit/
│   ├── Models/                       # Model relationship & scope tests
│   │   ├── BookingTest.php
│   │   ├── UserTest.php
│   │   └── NotificationTest.php
│   └── Services/                     # Service logic tests
│       └── MicrosoftGraphServiceTest.php
├── Pest.php                          # Global test configuration
└── TestCase.php                      # Base test class
```

## Running Tests

```bash
# Run all tests
composer test

# Run tests with coverage
composer test -- --coverage

# Run specific test file
composer test tests/Feature/BookingTest.php

# Run tests matching a pattern
composer test --filter=BookingTest

# Run only unit tests
composer test tests/Unit

# Run only feature tests
composer test tests/Feature

# Run tests and stop on first failure
composer test -- --stop-on-failure
```

## Testing Calendar Integration

### Overview
Calendar testing involves mocking Microsoft Graph API calls. This prevents external API dependencies during testing.

### Key Testing Scenarios

#### 1. Calendar Event Creation
```php
// When a booking is confirmed, verify calendar event is created
#[\PHPUnit\Framework\Attributes\Test]
public function confirming_booking_creates_calendar_event()
{
    $this->mock(MicrosoftGraphService::class, function ($mock) {
        $mock->shouldReceive('createCalendarEvent')
            ->once()
            ->andReturn(['id' => 'event-123']);
    });

    $booking = Booking::factory()->pending()->create();
    $adviser = $booking->adviser;

    $this->actingAs($adviser)
        ->putJson("/api/bookings/{$booking->id}/confirm", [
            'confirmed_datetime' => now()->addDays(2)->toDateTimeString(),
        ])
        ->assertStatus(200);
}
```

#### 2. Calendar Event Deletion
```php
// When booking is denied, calendar events are deleted
#[\PHPUnit\Framework\Attributes\Test]
public function denying_booking_deletes_calendar_events()
{
    $mockGraphService = Mockery::mock(MicrosoftGraphService::class);
    $mockGraphService->shouldReceive('deleteCalendarEvent')
        ->twice()
        ->andReturn(true);

    $this->instance(MicrosoftGraphService::class, $mockGraphService);

    $booking = Booking::factory()->pending()->create([
        'student_outlook_event_id' => 'student-event-123',
        'adviser_outlook_event_id' => 'adviser-event-456',
    ]);

    $adviser = $booking->adviser;

    $this->actingAs($adviser)
        ->putJson("/api/bookings/{$booking->id}/deny", [
            'denial_reason' => 'Not available',
        ])
        ->assertStatus(200);
}
```

### Mocking Strategies

#### Using Mockery
```php
use Mockery;

// Create a mock instance
$mock = Mockery::mock(MicrosoftGraphService::class);

// Define expectations
$mock->shouldReceive('createCalendarEvent')
    ->andReturn(['id' => 'event-123']);

// Bind to container
$this->instance(MicrosoftGraphService::class, $mock);
```

#### Using Laravel Test Mocking
```php
// Simpler syntax for mocking
$this->mock(MicrosoftGraphService::class, function ($mock) {
    $mock->shouldReceive('createCalendarEvent')
        ->andReturn(['id' => 'event-123']);
});
```

### Real Integration Testing (Optional)
If you want to test actual Microsoft Graph API calls:

1. Create a separate test environment with real credentials
2. Use database transactions to rollback changes
3. Mark tests with `@group integration` to skip in CI/CD

```php
#[\PHPUnit\Framework\Attributes\Group('integration')]
#[\PHPUnit\Framework\Attributes\Test]
public function can_create_real_calendar_event()
{
    $student = User::factory()->student()->create();
    $adviser = User::factory()->adviser()->create();
    
    // This will call real Microsoft Graph API
    $service = app(MicrosoftGraphService::class);
    $result = $service->createCalendarEvent([...]);
    
    $this->assertArrayHasKey('id', $result);
}
```

Run only integration tests:
```bash
composer test -- --group=integration
```

## Testing Notifications

### Key Testing Scenarios

#### 1. Notification Creation on Booking Confirmation
```php
#[\PHPUnit\Framework\Attributes\Test]
public function student_receives_notification_when_booking_confirmed()
{
    $booking = Booking::factory()->pending()->create();
    $student = $booking->student;
    $adviser = $booking->adviser;

    $this->actingAs($adviser)
        ->putJson("/api/bookings/{$booking->id}/confirm", [
            'confirmed_datetime' => now()->addDays(2)->toDateTimeString(),
        ])
        ->assertStatus(200);

    $this->assertDatabaseHas('notifications', [
        'user_id' => $student->id,
        'booking_id' => $booking->id,
        'type' => 'booking_confirmed',
    ]);
}
```

#### 2. Notification Unread Filtering
```php
#[\PHPUnit\Framework\Attributes\Test]
public function adviser_can_view_unread_notifications()
{
    $adviser = User::factory()->adviser()->create();
    
    Notification::factory(3)->unread()->create(['user_id' => $adviser->id]);
    Notification::factory(2)->read()->create(['user_id' => $adviser->id]);

    $this->actingAs($adviser)
        ->getJson('/api/notifications?unread=true')
        ->assertStatus(200)
        ->assertJsonCount(3, 'data');
}
```

#### 3. Mark as Read Functionality
```php
#[\PHPUnit\Framework\Attributes\Test]
public function user_can_mark_notification_as_read()
{
    $user = User::factory()->create();
    $notification = Notification::factory()->unread()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->putJson("/api/notifications/{$notification->id}/mark-read")
        ->assertStatus(200);

    $this->assertTrue($notification->fresh()->is_read);
}
```

### Database Assertions for Notifications

```php
// Check notification exists with specific data
$this->assertDatabaseHas('notifications', [
    'user_id' => $user->id,
    'type' => 'booking_confirmed',
    'is_read' => false,
]);

// Check notification doesn't exist
$this->assertDatabaseMissing('notifications', [
    'id' => $notification->id,
]);
```

## Testing Factories

Factories generate test data consistently:

```php
// Create a single booking
$booking = Booking::factory()->create();

// Create multiple bookings
$bookings = Booking::factory()->count(5)->create();

// Create with specific state
$pendingBooking = Booking::factory()->pending()->create();
$confirmedBooking = Booking::factory()->confirmed()->create();

// Create with custom attributes
$booking = Booking::factory()->create([
    'topic' => 'Custom Topic',
    'status' => 'completed',
]);

// Create related models
$booking = Booking::factory()
    ->for(User::factory()->adviser(), 'adviser')
    ->create();
```

## Common Testing Patterns

### Testing Authentication
```php
// Act as a user
$this->actingAs($user)
    ->getJson('/api/route')
    ->assertStatus(200);

// Test without authentication
$this->getJson('/api/protected-route')
    ->assertStatus(401);
```

### Testing Validation
```php
$this->postJson('/api/bookings', [
    'adviser_id' => 'invalid',
    'preferred_datetime' => 'not-a-date',
])
->assertStatus(422)
->assertJsonValidationErrors(['adviser_id', 'preferred_datetime']);
```

### Testing Relationships
```php
$adviser = User::factory()->adviser()->create();
$booking = Booking::factory()->create(['adviser_id' => $adviser->id]);

// Test relationship is loaded
$this->assertEquals($adviser->id, $booking->adviser->id);

// Test relationship count
$this->assertCount(5, $adviser->fresh()->bookings);
```

### Testing Scopes
```php
Booking::factory(3)->pending()->create();
Booking::factory(2)->confirmed()->create();

$pending = Booking::pending()->get();
$this->assertCount(3, $pending);
```

## Database Transactions

Tests automatically wrap each test in a transaction and rollback, preventing test pollution:

```php
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookingTest extends TestCase
{
    use RefreshDatabase;  // Automatic rollback after each test
    
    #[\PHPUnit\Framework\Attributes\Test]
    public function test_something()
    {
        // Database changes are isolated to this test
        $booking = Booking::factory()->create();
    }
    // Database is rolled back after test
}
```

## CI/CD Integration

Add to your CI/CD pipeline:

```bash
# Run all tests with coverage report
composer test -- --coverage --coverage-html=/tmp/coverage

# Run tests with specific config
composer test -- --configuration=phpunit-ci.xml

# Fail on coverage threshold
composer test -- --coverage --coverage-check=80
```

## Debugging Failed Tests

### View Database State
```php
#[\PHPUnit\Framework\Attributes\Test]
public function debugging_test()
{
    Booking::factory()->create();
    
    // Dump database content
    dump(Booking::all()->toArray());
    
    $this->assertTrue(true);
}
```

### Verbose Output
```bash
composer test -- --verbose
```

### Stop on First Failure
```bash
composer test -- --stop-on-failure
```

### Run Single Test
```bash
composer test --filter=TestName::testMethodName
```

## Best Practices

1. **Keep Tests Independent**: Each test should work in isolation
2. **Use Factories**: Always use factories for test data, not hardcoded values
3. **Mock External APIs**: Use Mockery to mock services like Microsoft Graph
4. **Clear Naming**: Test names should describe what they test
5. **Arrange-Act-Assert**: Organize tests with clear setup, execution, and verification
6. **No Side Effects**: Tests shouldn't affect other tests
7. **Test One Thing**: Each test should verify a single behavior
8. **Use Database Assertions**: Prefer `assertDatabaseHas` over querying models directly

## Troubleshooting

### Tests Won't Run
```bash
# Clear cache
php artisan config:clear
php artisan cache:clear

# Regenerate autoloader
composer dump-autoload

# Run tests with fresh database
php artisan migrate:fresh --seed
composer test
```

### Mock Not Working
```php
// Make sure to use correct namespace
use Mockery;
use App\Services\MicrosoftGraphService;

// Verify mock is created BEFORE test executes
$this->mock(MicrosoftGraphService::class, function ($mock) {
    $mock->shouldReceive('method')->andReturn('value');
});
```

### Database Locked
```bash
# Kill all connections to test database
php artisan db:wipe

# Reset migrations
php artisan migrate:fresh

# Run tests
composer test
```

## Resources

- [Laravel Testing Docs](https://laravel.com/docs/testing)
- [Pest PHP Docs](https://pestphp.com)
- [Mockery Docs](http://docs.mockery.io)
- [PHPUnit Docs](https://phpunit.de)
