<?php

use App\Models\Booking;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('bookings:prune-expired', function () {
    $deleted = Booking::whereNotNull('scheduled_deletion_at')
        ->where('scheduled_deletion_at', '<=', now())
        ->delete();

    $this->info("Deleted {$deleted} expired booking(s).");
})->purpose('Delete bookings whose retention period has expired');

Schedule::command('bookings:prune-expired')
    ->dailyAt('01:00')
    ->withoutOverlapping();
