<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use App\Models\Expertise;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    
use RefreshDatabase;
    public function test_student_can_list_own_bookings()
    {
        $student = User::factory()->student()->create();
        Booking::factory(3)->create(['student_id' => $student->id]);
        Booking::factory(2)->create();

        $this->actingAs($student)
            ->getJson('/bookings')
            ->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_adviser_can_list_own_bookings()
    {
        $adviser = User::factory()->adviser()->create();
        Booking::factory(3)->create(['adviser_id' => $adviser->id]);
        Booking::factory(2)->create();

        $this->actingAs($adviser)
            ->getJson('/bookings')
            ->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_student_can_create_booking()
    {
        $student = User::factory()->student()->create();
        $adviser = User::factory()->adviser()->create();
        $expertise = Expertise::factory()->create();

        // Attach expertise to adviser
        $adviser->expertise()->attach($expertise->id);

        $this->actingAs($student)
            ->postJson('/bookings', [
                'adviser_id' => $adviser->id,
                'expertise_id' => $expertise->id,
                'topic' => 'Assignment Help',
                'description' => 'Need help with data structures',
                'preferred_datetime' => now()->addDays(3)->toDateTimeString(),
                'meeting_type' => 'online',
            ])
            ->assertStatus(201)
            ->assertJsonStructure(['data' => ['id', 'status', 'topic']]);

        $this->assertDatabaseHas('bookings', [
            'student_id' => $student->id,
            'adviser_id' => $adviser->id,
            'status' => 'pending',
        ]);
    }

    public function test_adviser_can_confirm_booking()
    {
        $booking = Booking::factory()->pending()->create();
        $adviser = $booking->adviser;

        $this->actingAs($adviser)
            ->putJson("/bookings/{$booking->id}/confirm", [
                'confirmed_datetime' => now()->addDays(2)->toDateTimeString(),
            ])
            ->assertStatus(200);

        $fresh = $booking->fresh();
        $this->assertEquals('confirmed', $fresh->status);
        $this->assertNotNull($fresh->confirmed_at);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $booking->student_id,
            'booking_id' => $booking->id,
            'type' => 'booking_confirmed',
        ]);
    }

    public function test_adviser_can_deny_booking()
    {
        $booking = Booking::factory()->pending()->create();
        $adviser = $booking->adviser;

        $this->actingAs($adviser)
            ->putJson("/bookings/{$booking->id}/deny", [
                'denial_reason' => 'Not available at requested time',
            ])
            ->assertStatus(200);

        $fresh = $booking->fresh();
        $this->assertEquals('denied', $fresh->status);
        $this->assertEquals('Not available at requested time', $fresh->denial_reason);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $booking->student_id,
            'booking_id' => $booking->id,
            'type' => 'booking_denied',
        ]);
    }

    public function test_student_cannot_create_booking_for_adviser_without_expertise()
    {
        $student = User::factory()->student()->create();
        $adviser = User::factory()->adviser()->create();
        $expertise = Expertise::factory()->create();

        $this->actingAs($student)
            ->postJson('/bookings', [
                'adviser_id' => $adviser->id,
                'expertise_id' => $expertise->id,
                'topic' => 'Assignment Help',
                'preferred_datetime' => now()->addDays(3),
                'meeting_type' => 'online',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['adviser_id']);
    }

    public function test_student_cannot_create_booking_with_past_datetime()
    {
        $student = User::factory()->student()->create();
        $adviser = User::factory()->adviser()->create();
        $expertise = Expertise::factory()->create();
        $adviser->expertise()->attach($expertise->id);

        $this->actingAs($student)
            ->postJson('/bookings', [
                'adviser_id' => $adviser->id,
                'expertise_id' => $expertise->id,
                'topic' => 'Assignment Help',
                'preferred_datetime' => now()->subDays(1),
                'meeting_type' => 'online',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['preferred_datetime']);
    }

    public function test_unauthenticated_user_cannot_create_booking()
    {
        $this->postJson('/bookings', [])
            ->assertStatus(401);
    }

    public function test_student_cannot_confirm_booking()
    {
        $booking = Booking::factory()->pending()->create();
        $student = User::factory()->student()->create();

        $this->actingAs($student)
            ->putJson("/bookings/{$booking->id}/confirm", [
                'confirmed_datetime' => now()->addDays(2)->toDateTimeString(),
            ])
            ->assertStatus(403);
    }

    public function test_third_party_cannot_cancel_booking()
    {
        $booking = Booking::factory()->pending()->create();
        $other = User::factory()->create();

        $this->actingAs($other)
            ->post("/bookings/{$booking->id}/cancel")
            ->assertStatus(403);
    }

    public function test_student_can_cancel_booking()
    {
        $booking = Booking::factory()->pending()->create();

        $this->actingAs($booking->student)
            ->post("/bookings/{$booking->id}/cancel")
            ->assertRedirect();

        $this->assertEquals('cancelled', $booking->fresh()->status);
    }

    public function test_adviser_can_cancel_booking()
    {
        $booking = Booking::factory()->pending()->create();

        $this->actingAs($booking->adviser)
            ->post("/bookings/{$booking->id}/cancel")
            ->assertRedirect();

        $this->assertEquals('cancelled', $booking->fresh()->status);
    }

    public function test_adviser_can_complete_confirmed_booking()
    {
        $booking = Booking::factory()->confirmed()->create();

        $this->actingAs($booking->adviser)
            ->post("/bookings/{$booking->id}/complete")
            ->assertRedirect();

        $this->assertEquals('completed', $booking->fresh()->status);
    }

    public function test_adviser_can_delete_denied_booking()
    {
        $booking = Booking::factory()->denied()->create();

        $this->actingAs($booking->adviser)
            ->delete("/bookings/{$booking->id}")
            ->assertRedirect();

        $this->assertDatabaseMissing('bookings', ['id' => $booking->id]);
    }
}
