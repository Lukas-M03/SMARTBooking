<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use App\Models\Expertise;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_receives_notification_when_booking_confirmed()
    {
        $booking = Booking::factory()->pending()->create();
        $student = $booking->student;
        $adviser = $booking->adviser;

        $this->actingAs($adviser)
            ->putJson("/bookings/{$booking->id}/confirm", [
                'confirmed_datetime' => now()->addDays(2)->toDateTimeString(),
            ])
            ->assertStatus(200);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $student->id,
            'booking_id' => $booking->id,
            'type' => 'booking_confirmed',
        ]);
    }

    public function test_adviser_can_view_unread_notifications()
    {
        $adviser = User::factory()->adviser()->create();
        
        Notification::factory(3)->unread()->create(['user_id' => $adviser->id]);
        Notification::factory(2)->read()->create(['user_id' => $adviser->id]);

        $this->actingAs($adviser)
            ->getJson('/notifications?unread=true')
            ->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_student_receives_notification_when_booking_denied()
    {
        $booking = Booking::factory()->pending()->create();
        $student = $booking->student;
        $adviser = $booking->adviser;

        $this->actingAs($adviser)
            ->putJson("/bookings/{$booking->id}/deny", [
                'denial_reason' => 'Not available at requested time',
            ])
            ->assertStatus(200);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $student->id,
            'booking_id' => $booking->id,
            'type' => 'booking_denied',
        ]);
    }

    public function test_user_can_mark_notification_as_read()
    {
        $user = User::factory()->create();
        $notification = Notification::factory()->unread()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->putJson("/notifications/{$notification->id}/read")
            ->assertStatus(200);

        $this->assertTrue($notification->fresh()->is_read);
    }

    public function test_user_can_mark_all_notifications_as_read()
    {
        $user = User::factory()->create();
        Notification::factory(5)->unread()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->putJson('/notifications/read-all')
            ->assertStatus(200);

        $unread = Notification::where('user_id', $user->id)->unread()->count();
        $this->assertEquals(0, $unread);
    }

    public function test_notification_includes_booking_details()
    {
        $booking = Booking::factory()->create(['topic' => 'Assignment Help']);
        $notification = Notification::factory()->create([
            'booking_id' => $booking->id,
            'title' => 'New Booking Request',
            'message' => 'You have received a new booking request for: ' . $booking->topic,
        ]);

        $this->actingAs($booking->adviser)
            ->getJson("/notifications/{$notification->id}")
            ->assertStatus(200)
            ->assertJsonFragment(['message' => 'You have received a new booking request for: Assignment Help']);
    }

    public function test_adviser_receives_notification_when_student_creates_booking()
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
                'description' => 'Need help with data structures',
                'preferred_datetime' => now()->addDays(3)->toDateTimeString(),
                'meeting_type' => 'online',
            ])
            ->assertStatus(201);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $adviser->id,
            'type' => 'booking_created',
        ]);
    }

    public function test_user_can_delete_notification()
    {
        $user = User::factory()->create();
        $notification = Notification::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->deleteJson("/notifications/{$notification->id}")
            ->assertStatus(200);

        $this->assertDatabaseMissing('notifications', ['id' => $notification->id]);
    }

    public function test_user_cannot_view_another_users_notification()
    {
        $notification = Notification::factory()->create();
        /** @var User $other */
        $other = User::factory()->create();

        $this->actingAs($other)
            ->getJson("/notifications/{$notification->id}")
            ->assertStatus(404);
    }

    public function test_user_cannot_delete_another_users_notification()
    {
        $notification = Notification::factory()->create();
        /** @var User $other */
        $other = User::factory()->create();

        $this->actingAs($other)
            ->deleteJson("/notifications/{$notification->id}")
            ->assertStatus(404);

        $this->assertDatabaseHas('notifications', ['id' => $notification->id]);
    }
}
