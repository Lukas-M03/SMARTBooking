<x-layout>
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <h1 style="color: #667eea;">My Bookings</h1>
        @if(Auth::user()->isStudent())
            <a href="{{ route('bookings.create') }}" class="btn btn-primary">+ New Booking</a>
        @endif
    </div>

    @if($bookings->count() > 0)
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #ddd;">
                    @if(Auth::user()->isStudent())
                        <th style="padding: 1rem; text-align: left;">Adviser</th>
                    @else
                        <th style="padding: 1rem; text-align: left;">Student</th>
                    @endif
                    <th style="padding: 1rem; text-align: left;">Topic</th>
                    <th style="padding: 1rem; text-align: left;">Expertise</th>
                    <th style="padding: 1rem; text-align: left;">Date & Time</th>
                    <th style="padding: 1rem; text-align: left;">Type</th>
                    <th style="padding: 1rem; text-align: left;">Status</th>
                    <th style="padding: 1rem; text-align: left;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $booking)
                <tr style="border-bottom: 1px solid #eee;">
                    @if(Auth::user()->isStudent())
                        <td style="padding: 1rem;">{{ $booking->adviser->name }}</td>
                    @else
                        <td style="padding: 1rem;">{{ $booking->student->name }}</td>
                    @endif
                    <td style="padding: 1rem;">{{ $booking->topic }}</td>
                    <td style="padding: 1rem;">{{ $booking->expertise->name }}</td>
                    <td style="padding: 1rem;">{{ $booking->preferred_datetime->format('M d, Y - H:i') }}</td>
                    <td style="padding: 1rem;">{{ ucfirst($booking->meeting_type) }}</td>
                    <td style="padding: 1rem;">
                        <span style="padding: 0.25rem 0.75rem; border-radius: 15px; font-size: 0.875rem; 
                            background: {{ $booking->status === 'confirmed' ? '#28a745' : ($booking->status === 'pending' ? '#ffc107' : ($booking->status === 'denied' ? '#dc3545' : '#6c757d')) }}; 
                            color: {{ $booking->status === 'pending' ? '#333' : 'white' }};">
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
        <p style="text-align: center; color: #999; padding: 2rem;">
            No bookings found. 
            @if(Auth::user()->isStudent())
                <a href="{{ route('bookings.create') }}" style="color: #667eea;">Create your first booking!</a>
            @endif
        </p>
    @endif
</div>
</x-layout>
