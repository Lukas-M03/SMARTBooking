<x-layout>
<div class="card">
    <h1 class="h1-dashboard">Completed Bookings</h1>
    <p class="text-gray-600 mt-2">Includes adviser notes for completed sessions.</p>
    <p class="mt-4">
        <a href="{{ route('admin.dashboard') }}" class="forgot-link">Back to Admin Dashboard</a>
    </p>
</div>

<div class="card mt-6">
    @if ($completedBookings->isEmpty())
        <p class="text-gray-600">No completed bookings found.</p>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-300">
                        <th class="py-3 pr-4">Date</th>
                        <th class="py-3 pr-4">Student</th>
                        <th class="py-3 pr-4">Adviser</th>
                        <th class="py-3 pr-4">Expertise</th>
                        <th class="py-3 pr-4">Topic</th>
                        <th class="py-3">Adviser Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($completedBookings as $booking)
                        <tr class="border-b border-gray-200 align-top">
                            <td class="py-3 pr-4">{{ $booking->preferred_datetime?->format('M d, Y h:i A') ?? 'N/A' }}</td>
                            <td class="py-3 pr-4">{{ $booking->student?->name ?? 'N/A' }}</td>
                            <td class="py-3 pr-4">{{ $booking->adviser?->name ?? 'N/A' }}</td>
                            <td class="py-3 pr-4">{{ $booking->expertise?->name ?? 'N/A' }}</td>
                            <td class="py-3 pr-4">{{ $booking->topic ?? 'N/A' }}</td>
                            <td class="py-3">{{ $booking->completion_notes ?: 'No notes provided.' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
</x-layout>
