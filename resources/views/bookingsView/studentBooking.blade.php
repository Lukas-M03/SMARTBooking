<div class="card">
    <div class="card-upcoming-bookings">
        <h2>Upcoming Bookings</h2>
        <a href="{{ route('bookings.create') }}" class="btn btn-primary">+ New Booking</a>
    </div>

    @if($upcomingBookings->count() > 0)
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr class="border-bottom: 2px solid #ddd;">
                    <th style="padding: 1rem; text-align: left;">Topic</th>
                    <th style="padding: 1rem; text-align: left;">Adviser</th>
                    <th style="padding: 1rem; text-align: left;">Date & Time</th>
                    <th style="padding: 1rem; text-align: left;">Status</th>
                    <th style="padding: 1rem; text-align: left;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($upcomingBookings as $booking)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 1rem;">{{ $booking->topic }}</td>
                    <td style="padding: 1rem;">{{ $booking->adviser->name }}</td>
                    <td style="padding: 1rem;">{{ $booking->preferred_datetime->format('M d, Y - H:i') }}</td>
                    <td style="padding: 1rem;">
                        <span style="padding: 0.25rem 0.75rem; border-radius: 15px; font-size: 0.875rem; 
                            background: {{ $booking->status === 'confirmed' ? '#28a745' : '#ffc107' }}; 
                            color: {{ $booking->status === 'confirmed' ? 'white' : '#333' }};">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </td>
                    <td style="padding: 1rem;">
                        <a href="{{ route('bookings.show', $booking) }}" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">View</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p class="text-center text-gray-500 p-8">No upcoming bookings. <a href="{{ route('bookings.create') }}" class="text-blue-600 hover:underline">Create your first booking!</a></p>
    @endif
</div>