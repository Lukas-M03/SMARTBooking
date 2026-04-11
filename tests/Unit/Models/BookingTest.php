<?php

namespace Tests\Unit\Models;

use App\Models\Booking;
use App\Models\Expertise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_belongs_to_student()
    {
        $student = User::factory()->student()->create();
        $booking = Booking::factory()->create(['student_id' => $student->id]);

        $this->assertSame($student->id, $booking->student->id);
    }

    public function test_booking_belongs_to_adviser()
    {
        $adviser = User::factory()->adviser()->create();
        $booking = Booking::factory()->create(['adviser_id' => $adviser->id]);

        $this->assertSame($adviser->id, $booking->adviser->id);
    }

    public function test_booking_belongs_to_expertise()
    {
        $expertise = Expertise::factory()->create();
        $booking = Booking::factory()->create(['expertise_id' => $expertise->id]);

        $this->assertSame($expertise->id, $booking->expertise->id);
    }
}

