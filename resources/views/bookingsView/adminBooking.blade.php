<div class="card">
    <h2 class="h2-dashboard">Recent Bookings</h2>

    @if($recentBookings->count() > 0)
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #ddd;">
                    <th style="padding: 1rem; text-align: left;">Student</th>
                    <th style="padding: 1rem; text-align: left;">Adviser</th>
                    <th style="padding: 1rem; text-align: left;">Topic</th>
                    <th style="padding: 1rem; text-align: left;">Date & Time</th>
                    <th style="padding: 1rem; text-align: left;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentBookings as $booking)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 1rem;">{{ $booking->student->name }}</td>
                    <td style="padding: 1rem;">{{ $booking->adviser->name }}</td>
                    <td style="padding: 1rem;">{{ $booking->topic }}</td>
                    <td style="padding: 1rem;">{{ $booking->preferred_datetime->format('M d, Y - H:i') }}</td>
                    <td style="padding: 1rem;">
                        <span style="padding: 0.25rem 0.75rem; border-radius: 15px; font-size: 0.875rem; 
                            background: {{ $booking->status === 'confirmed' ? '#28a745' : ($booking->status === 'pending' ? '#ffc107' : '#dc3545') }}; 
                            color: {{ $booking->status === 'pending' ? '#333' : 'white' }};">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="text-align: center; color: #999; padding: 2rem;">No bookings yet.</p>
    @endif
</div>