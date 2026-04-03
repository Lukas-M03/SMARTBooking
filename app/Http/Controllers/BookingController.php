<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use App\Models\Notification;
use App\Services\MicrosoftGraphService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    /**
     * Retrieve all bookings for the authenticated user.
     * Students see bookings where they are the student.
     * Advisers see bookings where they are the adviser.
     * Results are ordered by preferred_datetime in descending order.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $status = $request->query('status');
        
        if ($user->isStudent()) {
            $query = Booking::where('student_id', $user->id)
                ->with(['adviser', 'expertise']);
        } else {
            $query = Booking::where('adviser_id', $user->id)
                ->with(['student', 'expertise']);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $bookings = $query->orderBy('preferred_datetime', 'asc')->get();

        return view('bookings.index', ['bookings' => $bookings]);
    }

    /**
     * Display the form to create a new booking.
     */
    public function create()
    {
        return view('bookings.create');
    }

    /**
     * Return available/unavailable 30-minute slots for a specific date.
     */
    public function availableSlots(Request $request)
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
        ]);

        $student = Auth::user();

        if (!$student || !$student->isStudent()) {
            abort(403);
        }

        $assignment = $this->resolveBookingAssignment($student);

        if (!$assignment) {
            return response()->json([
                'adviser' => null,
                'slots' => [],
                'message' => 'No adviser is currently available for your module mapping.',
            ], 422);
        }

        $adviser = $assignment['adviser'];
        $day = Carbon::parse($validated['date'])->startOfDay();

        return response()->json([
            'adviser' => [
                'id' => $adviser->id,
                'name' => $adviser->name,
            ],
            'date' => $day->toDateString(),
            'slots' => $this->buildDailySlots($adviser, $day),
        ]);
    }

    /**
     * Store a newly created booking in the database.
     * Validates input, resolves adviser/expertise from registration profile,
     * creates the booking with pending status, and notifies both student and adviser.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'topic' => ['required', 'string', 'max:40'],
            'description' => ['nullable', 'string'],
            'preferred_datetime' => ['required', 'date', 'after:now'],
            'meeting_type' => ['required', 'in:in-person,online,phone'],
        ], [
            'topic.required' => 'Please enter a meeting topic.',
        ]);

        $assignment = $this->resolveBookingAssignment(Auth::user());

        if (!$assignment) {
            return back()
                ->withInput()
                ->withErrors(['topic' => 'No adviser could be assigned from your registered modules. Please update your registration profile.']);
        }

        $adviser = $assignment['adviser'];
        $expertiseId = $assignment['expertise_id'];
        $preferredDateTime = Carbon::parse($validated['preferred_datetime']);

        if ($this->isAdviserSlotUnavailable($adviser->id, $preferredDateTime)) {
            return back()
                ->withInput()
                ->withErrors(['preferred_datetime' => 'This adviser is already booked around that time. Please choose a different slot.']);
        }

        $booking = Booking::create([
            'student_id' => Auth::id(),
            'adviser_id' => $adviser->id,
            'expertise_id' => $expertiseId,
            'topic' => $validated['topic'],
            'description' => $validated['description'],
            'preferred_datetime' => $preferredDateTime,
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

        // Best-effort Outlook sync: booking succeeds even if external API fails.
        $this->syncBookingToConnectedCalendars($booking);

        return redirect()->route('bookings.show', $booking)->with('success', 'Booking request submitted successfully!');
    }

    /**
     * Sync a booking event to any connected participant calendars.
     */
    private function syncBookingToConnectedCalendars(Booking $booking): void
    {
        $booking->loadMissing(['student', 'adviser', 'expertise']);

        // Sync to any connected participant calendars (student and/or adviser).
        $participants = collect([$booking->student, $booking->adviser])
            ->filter(fn ($user) => $user && $user->hasMicrosoftToken())
            ->unique('id')
            ->values();

        foreach ($participants as $participant) {
            try {
                $graph = new MicrosoftGraphService($participant);
                $eventData = MicrosoftGraphService::formatBookingAsEvent($booking, $participant);
                $eventId = $graph->createBookingEvent($eventData);

                if ($eventId) {
                    if ((int) $participant->id === (int) $booking->student_id) {
                        $booking->student_outlook_event_id = $eventId;
                    } elseif ((int) $participant->id === (int) $booking->adviser_id) {
                        $booking->adviser_outlook_event_id = $eventId;
                    }
                } else {
                    Log::warning('Booking created but Outlook event was not created.', [
                        'booking_id' => $booking->id,
                        'user_id' => $participant->id,
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('Booking created but Outlook sync failed.', [
                    'booking_id' => $booking->id,
                    'user_id' => $participant->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($booking->isDirty(['student_outlook_event_id', 'adviser_outlook_event_id'])) {
            $booking->save();
        }
    }

    /**
     * Determine whether an adviser already has a pending/confirmed booking
     * that overlaps the requested 30-minute slot.
     */
    private function isAdviserSlotUnavailable(int $adviserId, Carbon $requestedStart): bool
    {
        $windowStart = $requestedStart->copy()->subMinutes(30)->addSecond();
        $windowEnd = $requestedStart->copy()->addMinutes(30)->subSecond();

        return Booking::where('adviser_id', $adviserId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereBetween('preferred_datetime', [$windowStart, $windowEnd])
            ->exists();
    }

    /**
     * Build a day view of 30-minute slots and mark busy windows.
     */
    private function buildDailySlots(User $adviser, Carbon $day): array
    {
        $dayStart = $day->copy()->startOfDay();
        $dayEnd = $day->copy()->endOfDay();

        $busyWindows = $this->getBusyWindowsForAdviser($adviser, $dayStart, $dayEnd);
        $slots = [];

        for ($hour = 9; $hour <= 16; $hour++) {
            foreach ([0, 30] as $minute) {
                $slotStart = $day->copy()->setTime($hour, $minute);
                $slotEnd = $slotStart->copy()->addMinutes(30);

                $isPast = $slotStart->isPast();

                $isBusy = collect($busyWindows)->contains(function (array $window) use ($slotStart, $slotEnd) {
                    return $slotStart->lt($window['end']) && $slotEnd->gt($window['start']);
                });

                $slots[] = [
                    'start' => $slotStart->format('Y-m-d\TH:i'),
                    'label' => $slotStart->format('g:i A'),
                    'available' => !$isPast && !$isBusy,
                    'reason' => $isPast ? 'Past time' : ($isBusy ? 'Booked' : null),
                ];
            }
        }

        return $slots;
    }

    /**
     * Collect busy windows from internal bookings and Outlook calendar.
     */
    private function getBusyWindowsForAdviser(User $adviser, Carbon $start, Carbon $end): array
    {
        $windows = [];

        $bookings = Booking::where('adviser_id', $adviser->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereBetween('preferred_datetime', [$start, $end])
            ->get(['preferred_datetime']);

        foreach ($bookings as $booking) {
            $bookingStart = $booking->preferred_datetime->copy();
            $windows[] = [
                'start' => $bookingStart,
                'end' => $bookingStart->copy()->addMinutes(30),
            ];
        }

        if ($adviser->hasMicrosoftToken()) {
            try {
                $graph = new MicrosoftGraphService($adviser);
                $events = $graph->getAvailability($start, $end);

                foreach ($events as $event) {
                    $eventStartRaw = $event['start']['dateTime'] ?? null;
                    $eventEndRaw = $event['end']['dateTime'] ?? null;

                    if (!$eventStartRaw || !$eventEndRaw) {
                        continue;
                    }

                    $windows[] = [
                        'start' => Carbon::parse($eventStartRaw),
                        'end' => Carbon::parse($eventEndRaw),
                    ];
                }
            } catch (\Exception $e) {
                Log::warning('Slot picker Outlook availability fetch failed.', [
                    'adviser_id' => $adviser->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $windows;
    }

    /**
     * Sync booking status changes to connected Outlook calendars.
     * - confirmed/completed: create or update events
     * - denied/cancelled: delete events and clear stored IDs
     */
    private function syncBookingStatusToOutlook(Booking $booking): void
    {
        $booking->loadMissing(['student', 'adviser', 'expertise']);

        $participants = collect([$booking->student, $booking->adviser])
            ->filter(fn ($user) => $user && $user->hasMicrosoftToken())
            ->unique('id')
            ->values();

        $isCancelledOrDenied = in_array($booking->status, ['denied', 'cancelled', 'canceled'], true);

        foreach ($participants as $participant) {
            $eventColumn = ((int) $participant->id === (int) $booking->student_id)
                ? 'student_outlook_event_id'
                : 'adviser_outlook_event_id';

            $eventId = $booking->{$eventColumn};

            try {
                $graph = new MicrosoftGraphService($participant);

                if ($isCancelledOrDenied) {
                    if ($eventId) {
                        $graph->deleteBookingEvent($eventId);
                        $booking->{$eventColumn} = null;
                    }
                    continue;
                }

                $eventData = MicrosoftGraphService::formatBookingAsEvent($booking, $participant);

                if ($eventId) {
                    $updated = $graph->updateBookingEvent($eventId, $eventData);

                    if (!$updated) {
                        $replacementId = $graph->createBookingEvent($eventData);
                        if ($replacementId) {
                            $booking->{$eventColumn} = $replacementId;
                        }
                    }
                } else {
                    $createdEventId = $graph->createBookingEvent($eventData);
                    if ($createdEventId) {
                        $booking->{$eventColumn} = $createdEventId;
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Booking status updated but Outlook sync failed.', [
                    'booking_id' => $booking->id,
                    'user_id' => $participant->id,
                    'status' => $booking->status,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($booking->isDirty(['student_outlook_event_id', 'adviser_outlook_event_id'])) {
            $booking->save();
        }
    }

    /**
     * Resolve adviser and expertise for a student booking from registration data.
     * 1) Student's preferred adviser (if set and matches any student module)
     * 2) Any matching adviser with the lowest active workload
     */
    private function resolveBookingAssignment(User $student): ?array
    {
        $moduleIds = $student->modules()
            ->pluck('expertise.id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if (empty($moduleIds)) {
            return null;
        }

        $preferredAdviser = $student->preferredAdviser()
            ->where('role', 'adviser')
            ->whereHas('expertise', function ($query) use ($moduleIds) {
                $query->whereIn('expertise.id', $moduleIds);
            })
            ->first();

        if ($preferredAdviser) {
            $preferredExpertiseId = $preferredAdviser->expertise()
                ->whereIn('expertise.id', $moduleIds)
                ->value('expertise.id');

            if ($preferredExpertiseId) {
                return [
                    'adviser' => $preferredAdviser,
                    'expertise_id' => (int) $preferredExpertiseId,
                ];
            }
        }

        $fallbackAdviser = User::where('role', 'adviser')
            ->whereHas('expertise', function ($query) use ($moduleIds) {
                $query->whereIn('expertise.id', $moduleIds);
            })
            ->withCount([
                'adviserBookings as active_bookings_count' => function ($query) {
                    $query->whereIn('status', ['pending', 'confirmed']);
                },
            ])
            ->orderBy('active_bookings_count')
            ->orderBy('name')
            ->first();

        if (!$fallbackAdviser) {
            return null;
        }

        $fallbackExpertiseId = $fallbackAdviser->expertise()
            ->whereIn('expertise.id', $moduleIds)
            ->value('expertise.id');

        if (!$fallbackExpertiseId) {
            return null;
        }

        return [
            'adviser' => $fallbackAdviser,
            'expertise_id' => (int) $fallbackExpertiseId,
        ];
    }

    /**
     * Display a specific booking with full details.
     * Only the student, adviser, or admin can view the booking.
     * Aborts with 403 if unauthorized.
     */
    public function show(Booking $booking)
    {
        $user = Auth::user();

        if ($booking->student_id !== $user->id && $booking->adviser_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        return view('bookings.show', ['booking' => $booking]);
    }

    /**
     * Confirm a pending booking (adviser only).
     * Updates booking status to 'confirmed' and sets a 90-day scheduled deletion date.
     * Sends confirmation notification to the student.
     */
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

        $this->syncBookingStatusToOutlook($booking);

        Notification::create([
            'user_id' => $booking->student_id,
            'booking_id' => $booking->id,
            'title' => 'Booking Confirmed',
            'message' => "Your booking for '" . $booking->topic . "' has been confirmed by " . $user->name . ".",
            'type' => 'success',
        ]);

        return back()->with('success', 'Booking confirmed successfully!');
    }

    /**
     * Deny a pending booking with optional notes (adviser only).
     * Updates booking status to 'denied' and stores adviser notes.
     * Sends denial notification to the student with the reason.
     */
    public function deny(Request $request, Booking $booking)
    {
        $user = Auth::user();

        if ($booking->adviser_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'denial_reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $booking->update([
            'status' => 'denied',
            'denial_reason' => $validated['denial_reason'] ?? 'Booking request denied.',
        ]);

        $this->syncBookingStatusToOutlook($booking);

        Notification::create([
            'user_id' => $booking->student_id,
            'booking_id' => $booking->id,
            'title' => 'Booking Denied',
            'message' => "Your booking for '" . $booking->topic . "' has been denied. Reason: " . ($validated['denial_reason'] ?? 'Not specified.'),
            'type' => 'warning',
        ]);

        return back()->with('success', 'Booking denied.');
    }

    /**
     * Cancel a booking (student or adviser only).
     * Updates booking status to 'cancelled' and notifies the other party (student or adviser).
     */
    public function cancel(Booking $booking)
    {
        $user = Auth::user();

        if ($booking->student_id !== $user->id && $booking->adviser_id !== $user->id) {
            abort(403);
        }

        $booking->update(['status' => 'cancelled']);

        $this->syncBookingStatusToOutlook($booking);

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

    /**
     * Delete a booking (adviser only, for denied or cancelled bookings).
     */
    public function destroy(Booking $booking)
    {
        $user = Auth::user();
        if ($booking->adviser_id !== $user->id || !in_array($booking->status, ['denied', 'cancelled', 'canceled'])) {
            abort(403);
        }
        $booking->delete();
        return redirect()->route('bookings.index')->with('success', 'Booking deleted successfully.');
    }

    /**
     * Mark a booking as completed (adviser only, for confirmed bookings).
     */
    public function complete(Booking $booking)
    {
        $user = Auth::user();
        if ($booking->adviser_id !== $user->id || $booking->status !== 'confirmed') {
            abort(403);
        }
        $booking->update(['status' => 'completed']);

        $this->syncBookingStatusToOutlook($booking);
        // Optionally, notify the student
        Notification::create([
            'user_id' => $booking->student_id,
            'booking_id' => $booking->id,
            'title' => 'Booking Completed',
            'message' => "Your booking for '" . $booking->topic . "' has been marked as completed.",
            'type' => 'success',
        ]);
        return back()->with('success', 'Booking marked as completed.');
    }

    /**
     * Update adviser notes for a completed booking (assigned adviser only).
     */
    public function updateComment(Request $request, Booking $booking)
    {
        $user = Auth::user();

        if (!$user->isAdviser() || $booking->adviser_id !== $user->id || $booking->status !== 'completed') {
            abort(403);
        }

        $validated = $request->validate([
            'completion_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $booking->update([
            'completion_notes' => $validated['completion_notes'] ?? null,
        ]);

        return back()->with('success', 'Completion notes updated successfully.');
    }
}
