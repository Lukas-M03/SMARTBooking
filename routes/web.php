<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MicrosoftAuthController;
use App\Http\Controllers\CalendarController;

// Landing page
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->isStudent()) {
            return redirect()->route('student.dashboard');
        } elseif ($user->isAdviser()) {
            return redirect()->route('adviser.dashboard');
        } elseif ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
    }
    return view('welcome');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Student Routes
Route::middleware(['auth'])->prefix('student')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'student'])->name('student.dashboard');
});

// Adviser Routes
Route::middleware(['auth'])->prefix('adviser')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'adviser'])->name('adviser.dashboard');
});

// Admin Routes
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
});

// Booking Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('/bookings/{booking}/deny', [BookingController::class, 'deny'])->name('bookings.deny');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
});

// Notification Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
});

// Microsoft OAuth Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/microsoft/connect', [MicrosoftAuthController::class, 'redirectToMicrosoft'])->name('microsoft.redirect');
    Route::get('/microsoft/disconnect', [MicrosoftAuthController::class, 'disconnect'])->name('microsoft.disconnect');
    Route::post('/microsoft/refresh-token', [MicrosoftAuthController::class, 'refreshToken'])->name('microsoft.refresh-token');
});

Route::get('/auth/callback', [MicrosoftAuthController::class, 'handleCallback'])->name('microsoft.callback');

// Calendar API
Route::middleware(['auth'])->group(function () {
    Route::get('/calendar/events', [CalendarController::class, 'events'])->name('calendar.events');
});
