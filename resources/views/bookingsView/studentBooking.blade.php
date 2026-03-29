<div class="card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="font-bold text-lg">Upcoming Bookings</h2>
        <a href="{{ route('bookings.create') }}" class="btn btn-primary">+ New Booking</a>
    </div>
    @if ($upcomingBookings->count() > 0)
        <div class="table-responsive">
            <table class="w-full border-collapse min-w-175">
                <thead>
                    <tr class="border-b-2 border-gray-300">
                        <th class="py-4 px-4 text-left">Topic</th>
                        <th class="py-4 px-4 text-left hide-mobile">Adviser</th>
                        <th class="py-4 px-4 text-left">Date & Time</th>
                        <th class="py-4 px-4 text-left">Status</th>
                        <th class="py-4 px-4 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($upcomingBookings as $booking)
                        <tr class="border-b border-gray-200">
                            <td class="py-4 px-4">{{ $booking->topic }}</td>
                            <td class="py-4 px-4 hide-mobile">{{ $booking->adviser->name }}</td>
                            <td class="py-4 px-4">{{ $booking->preferred_datetime->format('M d, Y - H:i') }}</td>
                            <td class="py-4 px-4">
                                <span class="span-status"
                                    style="background: {{ $booking->status === 'confirmed' ? '#28a745' : ($booking->status === 'pending' ? '#ffc107' : ($booking->status === 'denied' ? '#dc3545' : '#6c757d')) }}; color: {{ $booking->status === 'pending' ? '#333' : 'white' }};">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <a href="{{ route('bookings.show', $booking) }}"
                                    class="btn btn-primary text-sm py-3 px-5">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-center text-gray-500 p-8">No upcoming bookings. <a href="{{ route('bookings.create') }}"
                class="text-blue-600 hover:underline">Create your first booking!</a></p>
    @endif
</div>
