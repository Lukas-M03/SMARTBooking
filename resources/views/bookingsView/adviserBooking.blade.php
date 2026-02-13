<div class="card">
    <h2 style="margin-bottom: 20px; ">Pending Booking Requests</h2>

    @if($pendingBookings->count() > 0)
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr class="tr">
                    <th class="th">Student</th>
                    <th class="th">Topic</th>
                    <th class="th">Preferred Date & Time</th>
                    <th class="th">Meeting Type</th>
                    <th class="th">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pendingBookings as $booking)
                <tr class="tr">
                    <td class="td">{{ $booking->student->name }}</td>
                    <td class="td">{{ $booking->topic }}</td>
                    <td class="td">{{ $booking->preferred_datetime->format('M d, Y - H:i') }}</td>
                    <td class="td">{{ ucfirst($booking->meeting_type) }}</td>
                    <td class="td">
                        <a href="{{ route('bookings.show', $booking) }}" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">Review</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-center text-gray-500 p-14">No pending requests at the moment.</p>
    @endif
</div>

<div class="card">
    <h2 class="mb-20">Upcoming Confirmed Bookings</h2>

    @if($upcomingBookings->count() > 0)
        <table class="w-full border-collapse">
            <thead>
                <tr class="tr">
                    <th class="th">Student</th>
                    <th class="th">Topic</th>
                    <th class="th">Date & Time</th>
                    <th class="th">Meeting Type</th>
                    <th class="th">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($upcomingBookings as $booking)
                <tr class="tr">
                    <td class="td">{{ $booking->student->name }}</td>
                    <td class="td">{{ $booking->topic }}</td>
                    <td class="td">{{ $booking->preferred_datetime->format('M d, Y - H:i') }}</td>
                    <td class="td">{{ ucfirst($booking->meeting_type) }}</td>
                    <td class="td">
                        <a href="{{ route('bookings.show', $booking) }}" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">View</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-center text-gray-500 p-14">No upcoming bookings.</p>
    @endif
</div>