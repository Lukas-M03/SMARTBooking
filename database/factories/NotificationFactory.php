<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'booking_id' => Booking::factory(),
            'title' => $this->faker->sentence(),
            'message' => $this->faker->paragraph(),
            'type' => $this->faker->randomElement(['booking_confirmed', 'booking_denied', 'booking_created', 'reminder']),
            'is_read' => false,
        ];
    }

    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => true,
        ]);
    }

    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => false,
        ]);
    }

    public function bookingConfirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'booking_confirmed',
            'title' => 'Booking Confirmed',
            'message' => 'Your booking has been confirmed by the adviser.',
        ]);
    }

    public function bookingDenied(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'booking_denied',
            'title' => 'Booking Denied',
            'message' => 'Your booking has been denied by the adviser.',
        ]);
    }
}
