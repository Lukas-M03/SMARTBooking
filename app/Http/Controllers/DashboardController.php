<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function student()
    {
        $user = Auth::user();
        
        $upcomingBookings = Booking::where('student_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('preferred_datetime', '>=', now())
            ->orderBy('preferred_datetime', 'asc')
            ->take(5)
            ->get();

        $recentBookings = Booking::where('student_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $stats = [
            'total' => Booking::where('student_id', $user->id)->count(),
            'pending' => Booking::where('student_id', $user->id)->where('status', 'pending')->count(),
            'confirmed' => Booking::where('student_id', $user->id)->where('status', 'confirmed')->count(),
            'completed' => Booking::where('student_id', $user->id)->where('status', 'completed')->count(),
        ];

        return view('dashboards.student', compact('upcomingBookings', 'recentBookings', 'notifications', 'stats'));
    }

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
        ];

        return view('dashboards.adviser', compact('pendingBookings', 'upcomingBookings', 'notifications', 'stats'));
    }

    public function admin()
    {
        $totalBookings = Booking::count();
        $totalUsers = \App\Models\User::count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $confirmedBookings = Booking::where('status', 'confirmed')->count();

        $recentBookings = Booking::with(['student', 'adviser', 'expertise'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('dashboards.admin', compact('totalBookings', 'totalUsers', 'pendingBookings', 'confirmedBookings', 'recentBookings'));
    }
}
