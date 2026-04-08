<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the student dashboard.
     * Shows upcoming and recent bookings, recent notifications, and booking statistics.
     * Stats include total, pending, confirmed, and completed bookings.
     */
    public function student()
    {
        $user = Auth::user();
        
        $upcomingBookings = Booking::where('student_id', $user->id)
            ->with(['adviser'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('preferred_datetime', '>=', now())
            ->orderBy('preferred_datetime', 'asc')
            ->paginate(5);

        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $stats = [
            'total' => Booking::where('student_id', $user->id)->count(),
            'pending' => Booking::where('student_id', $user->id)->where('status', 'pending')->count(),
            'confirmed' => Booking::where('student_id', $user->id)->where('status', 'confirmed')->count(),
            'completed' => Booking::where('student_id', $user->id)->where('status', 'completed')->count(),
            'notifications_total' => Notification::where('user_id', $user->id)->count(),
        ];

        return view('dashboards.student', [
            'upcomingBookings' => $upcomingBookings,
            'notifications' => $notifications,
            'stats' => $stats,
        ]);
    }

    /**
     * Display the adviser dashboard.
     * Shows pending booking requests, upcoming confirmed bookings, recent notifications,
     * and booking statistics (total, pending, confirmed, completed).
     */
    public function adviser()
    {
        $user = Auth::user();
        
        $pendingBookings = Booking::where('adviser_id', $user->id)
            ->where('status', 'pending')
            ->orderBy('preferred_datetime', 'asc')
            ->get();

        $upcomingBookings = Booking::where('adviser_id', $user->id)
            ->where('status', 'confirmed')
            ->where('preferred_datetime', '>=', now())
            ->orderBy('preferred_datetime', 'asc')
            ->take(5)
            ->get();

        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $stats = [
            'total' => Booking::where('adviser_id', $user->id)->count(),
            'pending' => Booking::where('adviser_id', $user->id)->where('status', 'pending')->count(),
            'confirmed' => Booking::where('adviser_id', $user->id)->where('status', 'confirmed')->count(),
            'completed' => Booking::where('adviser_id', $user->id)->where('status', 'completed')->count(),
            'notifications_total' => Notification::where('user_id', $user->id)->count(),
        ];

        return view('dashboards.adviser', [
            'pendingBookings' => $pendingBookings,
            'upcomingBookings' => $upcomingBookings,
            'notifications' => $notifications,
            'stats' => $stats,
        ]);
    }

    /**
     * Display the admin dashboard.
     * Shows system-wide statistics including total bookings, total users,
     * pending and confirmed booking counts, and the 10 most recent bookings.
     */
    public function admin()
    {
        $totalUsers = User::count();
        $completedBookings = Booking::where('status', 'completed')->count();

        return view('dashboards.admin', [
            'totalUsers' => $totalUsers,
            'completedBookings' => $completedBookings,
        ]);
    }

    public function adminUsers()
    {
        $students = User::where('role', 'student')
            ->orderBy('name')
            ->get();

        $advisers = User::where('role', 'adviser')
            ->orderBy('name')
            ->get();

        return view('dashboards.admin-users', [
            'students' => $students,
            'advisers' => $advisers,
        ]);
    }

    public function adminCompletedBookings()
    {
        $completedBookings = Booking::with(['student', 'adviser', 'expertise'])
            ->where('status', 'completed')
            ->orderByDesc('preferred_datetime')
            ->get();

        return view('dashboards.admin-completed-bookings', [
            'completedBookings' => $completedBookings,
        ]);
    }
}
