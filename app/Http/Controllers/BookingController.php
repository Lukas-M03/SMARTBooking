<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Expertise;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isStudent()) {
            $bookings = Booking::where('student_id', $user->id)
                ->with(['adviser', 'expertise'])
                ->orderBy('preferred_datetime', 'desc')
                ->get();
        } else {
            $bookings = Booking::where('adviser_id', $user->id)
                ->with(['student', 'expertise'])
                ->orderBy('preferred_datetime', 'desc')
                ->get();
        }

        return view('bookings.index', compact('bookings'));
    }

    public function create()
    {
        $expertiseList = Expertise::all();
        return view('bookings.create', compact('expertiseList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expertise_id' => ['required', 'exists:expertise,id'],
            'topic' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'preferred_datetime' => ['required', 'date', 'after:now'],
            'meeting_type' => ['required', 'in:in-person,online,phone'],
        ]);

        $adviser = User::where('role', 'adviser')
            ->whereHas('expertise', function ($query) use ($validated) {
                $query->where('expertise.id', $validated['expertise_id']);
            })
            ->first();

        if (!$adviser) {
            return back()->with('error', 'No adviser available for the selected expertise.');
        }

        $booking = Booking::create([
            'student_id' => Auth::id(),
            'adviser_id' => $adviser->id,
            'expertise_id' => $validated['expertise_id'],
            'topic' => $validated['topic'],
            'description' => $validated['description'],
            'preferred_datetime' => $validated['preferred_datetime'],
            'meeting_type' => $validated['meeting_type'],
            'status' => 'pending',
        ]);

        Notification::create([
            'user_id' => Auth::id(),
            'booking_id' => $booking->id,
            'title' => 'Booking Request Submitted',
            'message' => "Your booking request for '" . $validated['topic'] . "' has been submitted and is awaiting confirmation.",
            'type' => 'success',
        ]);

        Notification::create([
            'user_id' => $adviser->id,
            'booking_id' => $booking->id,
            'title' => 'New Booking Request',
            'message' => "New booking request from " . Auth::user()->name . " for '" . $validated['topic'] . "'.",
            'type' => 'info',
        ]);

        return redirect()->route('bookings.show', $booking)->with('success', 'Booking request submitted successfully!');
    }

    public function show(Booking $booking)
    {
        $user = Auth::user();

        if ($booking->student_id !== $user->id && $booking->adviser_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        return view('bookings.show', compact('booking'));
    }

    public function confirm(Booking $booking)
    {
        $user = Auth::user();

        if ($booking->adviser_id !== $user->id) {
            abort(403);
        }

        $booking->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
            'scheduled_deletion_at' => now()->addDays(90),
        ]);

        Notification::create([
            'user_id' => $booking->student_id,
            'booking_id' => $booking->id,
            'title' => 'Booking Confirmed',
            'message' => "Your booking for '" . $booking->topic . "' has been confirmed by " . $user->name . ".",
            'type' => 'success',
        ]);

        return back()->with('success', 'Booking confirmed successfully!');
    }

    public function deny(Request $request, Booking $booking)
    {
        $user = Auth::user();

        if ($booking->adviser_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'adviser_notes' => ['nullable', 'string'],
        ]);

        $booking->update([
            'status' => 'denied',
            'adviser_notes' => $validated['adviser_notes'] ?? 'Booking request denied.',
        ]);

        Notification::create([
            'user_id' => $booking->student_id,
            'booking_id' => $booking->id,
            'title' => 'Booking Denied',
            'message' => "Your booking for '" . $booking->topic . "' has been denied. Reason: " . ($validated['adviser_notes'] ?? 'Not specified.'),
            'type' => 'warning',
        ]);

        return back()->with('success', 'Booking denied.');
    }

    public function cancel(Booking $booking)
    {
        $user = Auth::user();

        if ($booking->student_id !== $user->id && $booking->adviser_id !== $user->id) {
            abort(403);
        }

        $booking->update(['status' => 'cancelled']);

        $otherUserId = ($booking->student_id === $user->id) ? $booking->adviser_id : $booking->student_id;

        Notification::create([
            'user_id' => $otherUserId,
            'booking_id' => $booking->id,
            'title' => 'Booking Cancelled',
            'message' => "The booking for '" . $booking->topic . "' has been cancelled by " . $user->name . ".",
            'type' => 'warning',
        ]);

        return back()->with('success', 'Booking cancelled successfully.');
    }
}
