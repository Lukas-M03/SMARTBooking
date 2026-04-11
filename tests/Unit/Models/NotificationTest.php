<?php

namespace Tests\Unit\Models;

use App\Models\Notification;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function notification_belongs_to_user()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create();
        $notification = Notification::factory()->create(['user_id' => $user->id, 'booking_id' => $booking->id]);

        $this->assertInstanceOf(User::class, $notification->user);
        $this->assertEquals($user->id, $notification->user->id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function notification_belongs_to_booking()
    {
        $booking = Booking::factory()->create();
        $notification = Notification::factory()->create(['booking_id' => $booking->id]);

        $this->assertInstanceOf(Booking::class, $notification->booking);
        $this->assertEquals($booking->id, $notification->booking->id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function unread_scope_filters_unread_notifications()
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create();

        Notification::factory(3)->unread()->create(['user_id' => $user->id, 'booking_id' => $booking->id]);
        Notification::factory(2)->read()->create(['user_id' => $user->id, 'booking_id' => $booking->id]);

        $unread = Notification::unread()->get();

        $this->assertCount(3, $unread);
        $unread->each(fn ($notif) => $this->assertFalse($notif->is_read));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function notification_can_be_marked_as_read()
    {
        $notification = Notification::factory()->unread()->create();

        $this->assertFalse($notification->is_read);

        $notification->markAsRead();

        $this->assertTrue($notification->fresh()->is_read);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function notification_has_correct_type()
    {
        $notifConfirmed = Notification::factory()->bookingConfirmed()->create();
        $notifDenied = Notification::factory()->bookingDenied()->create();

        $this->assertEquals('booking_confirmed', $notifConfirmed->type);
        $this->assertEquals('booking_denied', $notifDenied->type);
    }
}