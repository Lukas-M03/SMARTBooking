<div class="card">
    <h2 class="mb-2 font-bold text-lg">Pending Booking Requests</h2>

    @if($pendingBookings->count() > 0)
        <div class="table-responsive">
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
                            <a href="{{ route('bookings.show', $booking) }}" class="btn btn-primary">Review</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-center text-gray-500 p-14">No pending requests at the moment.</p>
    @endif
</div>

<div class="card">
    <h2 class="mb-2 font-bold text-lg">Upcoming Confirmed Bookings</h2>

    @if($upcomingBookings->count() > 0)
        <div class="table-responsive">
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
                            <a href="{{ route('bookings.show', $booking) }}" class="btn btn-primary">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-center text-gray-500 p-14">No upcoming bookings at the moment.</p>
    @endif
</div>