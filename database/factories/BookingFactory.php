<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\User;
use App\Models\Expertise;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        return [
            'student_id' => User::factory()->student(),
            'adviser_id' => User::factory()->adviser(),
            'expertise_id' => Expertise::factory(),
            'topic' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'preferred_datetime' => $this->faker->dateTimeBetween('+1 day', '+30 days'),
            'meeting_type' => $this->faker->randomElement(['online', 'in-person']),
            'status' => 'pending',
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    public function denied(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'denied',
            'denial_reason' => $this->faker->sentence(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'confirmed_at' => now()->subHours(2),
            'completion_notes' => $this->faker->paragraph(),
        ]);
    }
}
