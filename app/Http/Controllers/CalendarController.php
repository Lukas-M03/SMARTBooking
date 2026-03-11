<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use App\Services\MicrosoftGraphService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CalendarController extends Controller
{
    /**
     * Return calendar events for the authenticated user and requested date range.
     *
     * FullCalendar sends `start` and `end` query params; we normalize both to
     * full-day boundaries and return role-specific event payloads.
     */
    public function events(Request $request)
    {
        $user = Auth::user();
        $start = Carbon::parse($request->query('start'))->startOfDay();
        $end = Carbon::parse($request->query('end'))->endOfDay();

        if ($user->isStudent()) {
            return response()->json($this->buildStudentEvents($user, $start, $end));
        }

        if ($user->isAdviser()) {
            return response()->json($this->buildAdviserEvents($user, $start, $end));
        }

        return response()->json([]);
    }

    /**
     * Build student-facing events:
     * - Student's own bookings (normal events)
     * - Adviser bookings/outlook availability (background blocked times)
     */
    private function buildStudentEvents(User $student, Carbon $start, Carbon $end): array
    {
        // 1) Student's own bookings in the selected range.
        $studentBookings = Booking::with('adviser')
            ->where('student_id', $student->id)
            ->whereBetween('preferred_datetime', [$start, $end])
            ->orderBy('preferred_datetime')
            ->get();

        $events = $studentBookings->map(function (Booking $booking) {
            return $this->formatBookingEvent($booking, true);
        })->all();

        // 2) Determine the most relevant adviser from recent bookings.
        $adviserId = Booking::where('student_id', $student->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderByDesc('preferred_datetime')
            ->value('adviser_id');

        if (!$adviserId) {
            $adviserId = Booking::where('student_id', $student->id)
                ->orderByDesc('preferred_datetime')
                ->value('adviser_id');
        }

        if (!$adviserId) {
            return $events;
        }

        try {
            $adviser = User::findOrFail($adviserId);
        } catch (\Exception $e) {
            return $events;
        }

        // 3) Add adviser's pending/confirmed bookings as background blocks.
        $adviserBookings = Booking::with('student')
            ->where('adviser_id', $adviser->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereBetween('preferred_datetime', [$start, $end])
            ->orderBy('preferred_datetime')
            ->get();

        $backgroundEvents = $adviserBookings->map(function (Booking $booking) {
            return $this->formatBookingEvent($booking, false, true);
        })->all();

        // 4) Add unavailable periods from adviser Outlook calendar (if connected).
        $outlookEvents = $this->getOutlookAvailabilityEvents($adviser, $start, $end);

        return array_merge($events, $backgroundEvents, $outlookEvents);
    }

    /**
     * Build adviser-facing events (their own bookings only).
     */
    private function buildAdviserEvents(User $adviser, Carbon $start, Carbon $end): array
    {
        $adviserBookings = Booking::with('student')
            ->where('adviser_id', $adviser->id)
            ->whereBetween('preferred_datetime', [$start, $end])
            ->orderBy('preferred_datetime')
            ->get();

        return $adviserBookings->map(function (Booking $booking) {
            return $this->formatBookingEvent($booking, false);
        })->all();
    }

    /**
     * Convert a booking record into FullCalendar event format.
     *
     * When `$isBackground` is true, the event is rendered as an availability
     * block (non-primary visual layer) instead of a normal foreground event.
     */
    private function formatBookingEvent(Booking $booking, bool $forStudent, bool $isBackground = false): array
    {
        $start = $booking->preferred_datetime->copy();
        $end = $booking->preferred_datetime->copy()->addHour();
        $status = $booking->status;

        $studentName = $booking->student?->name ?? 'Student';
        $title = $forStudent
            ? 'Booking: ' . $booking->topic . ' (' . ucfirst($status) . ')'
            : 'Booking: ' . $booking->topic . ' - ' . $studentName;

        $colors = [
            'pending' => '#f59e0b',
            'confirmed' => '#10b981',
            'completed' => '#6366f1',
            'cancelled' => '#9ca3af',
            'denied' => '#ef4444',
        ];

        $color = $colors[$status] ?? '#3b82f6';

        return [
            'id' => 'booking-' . $booking->id,
            'title' => $title,
            'start' => $start->toIso8601String(),
            'end' => $end->toIso8601String(),
            'backgroundColor' => $isBackground ? '#e5e7eb' : $color,
            'borderColor' => $isBackground ? '#e5e7eb' : $color,
            'textColor' => $isBackground ? '#6b7280' : '#ffffff',
            'display' => $isBackground ? 'background' : 'auto',
        ];
    }

    /**
     * Fetch adviser Outlook events and map them into FullCalendar background
     * availability blocks to indicate already-occupied times.
     */
    private function getOutlookAvailabilityEvents(User $adviser, Carbon $start, Carbon $end): array
    {
        // Only attempt Graph API calls when the adviser has a valid token.
        if (!$adviser->hasMicrosoftToken()) {
            return [];
        }

        try {
            $graph = new MicrosoftGraphService($adviser);
            $availability = $graph->getAvailability($start, $end);

            return collect($availability)->map(function (array $event) {
                $eventStart = Carbon::parse($event['start']['dateTime'] ?? null);
                $eventEnd = Carbon::parse($event['end']['dateTime'] ?? null);

                // Skip malformed events returned by external API.
                if (!$eventStart || !$eventEnd) {
                    return null;
                }

                return [
                    'id' => 'outlook-' . ($event['id'] ?? uniqid()),
                    'title' => $event['subject'] ?? 'Unavailable',
                    'start' => $eventStart->toIso8601String(),
                    'end' => $eventEnd->toIso8601String(),
                    'backgroundColor' => '#e5e7eb',
                    'borderColor' => '#e5e7eb',
                    'textColor' => '#6b7280',
                    'display' => 'background',
                ];
            })->filter()->values()->all();
        } catch (\Exception $e) {
            // Graceful fallback: keep calendar functional even if Outlook fails.
            Log::warning('Outlook availability fetch failed: ' . $e->getMessage(), [
                'adviser_id' => $adviser->id,
            ]);

            return [];
        }
    }
}
