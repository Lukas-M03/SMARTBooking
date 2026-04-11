<?php

namespace Tests\Unit\Models;

use App\Models\Booking;
use App\Models\Expertise;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_password_is_hashed_on_creation()
    {
        $user = User::factory()->create(['password' => 'plain-password']);

        $this->assertNotSame('plain-password', $user->password);
        $this->assertTrue(Hash::check('plain-password', $user->password));
    }

    public function test_student_can_have_modules()
    {
        $student = User::factory()->student()->create();
        $expertise = Expertise::factory()->create();

        $student->modules()->attach($expertise->id);

        $this->assertTrue($student->fresh()->modules->contains($expertise->id));
    }

    public function test_adviser_can_have_expertise()
    {
        $adviser = User::factory()->adviser()->create();
        $expertise = Expertise::factory()->create();

        $adviser->expertise()->attach($expertise->id);

        $this->assertTrue($adviser->fresh()->expertise->contains($expertise->id));
    }

    public function test_user_can_have_bookings()
    {
        $adviser = User::factory()->adviser()->create();
        Booking::factory()->count(2)->create(['adviser_id' => $adviser->id]);

        $this->assertCount(2, $adviser->fresh()->adviserBookings);
    }

    public function test_has_microsoft_token_returns_false_when_no_token()
    {
        $user = User::factory()->create(['microsoft_token' => null]);

        $this->assertFalse($user->hasMicrosoftToken());
    }

    public function test_has_microsoft_token_returns_false_when_token_is_expired()
    {
        $user = User::factory()->create([
            'microsoft_token' => 'some-token',
            'microsoft_token_expires_at' => now()->subMinutes(5),
        ]);

        $this->assertFalse($user->hasMicrosoftToken());
    }

    public function test_has_microsoft_token_returns_true_when_token_is_valid()
    {
        $user = User::factory()->create([
            'microsoft_token' => 'some-token',
            'microsoft_token_expires_at' => now()->addHour(),
        ]);

        $this->assertTrue($user->hasMicrosoftToken());
    }
}
