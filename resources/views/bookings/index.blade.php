<x-layout>
    <div class="card">
        <div class="div-mybookings">
            <h1 class="h1-dashboard">My Bookings</h1>
            @if (Auth::user()->isStudent())
                <a href="{{ route('bookings.create') }}" class="btn btn-primary">+ New Booking</a>
            @endif
        </div>

        @if ($bookings->count() > 0)
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr class="tr">
                        @if (Auth::user()->isStudent())
                            <th class="th">Adviser</th>
                        @else
                            <th class="th">Student</th>
                        @endif
                        <th class="th">Topic</th>
                        <th class="th">Expertise</th>
                        <th class="th">Date & Time</th>
                        <th class="th">Type</th>
                        <th class="th">Status</th>
                        <th class="th">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bookings as $booking)
                        <tr class="tr">
                            @if (Auth::user()->isStudent())
                                <td class="td">{{ $booking->adviser->name }}</td>
                            @else
                                <td class="td">{{ $booking->student->name }}</td>
                            @endif
                            <td class="td">{{ $booking->topic }}</td>
                            <td class="td">{{ $booking->expertise->name }}</td>
                            <td class="td">{{ $booking->preferred_datetime->format('M d, Y - H:i') }}</td>
                            <td class="td">{{ ucfirst($booking->meeting_type) }}</td>
                            <td class="td">
                                <span class="span-status-adviser"
                                    style="background: {{ $booking->status === 'confirmed' ? '#28a745' : ($booking->status === 'pending' ? '#ffc107' : ($booking->status === 'denied' ? '#dc3545' : '#6c757d')) }}; color: {{ $booking->status === 'pending' ? '#333' : 'white' }};">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td class="td">
                                <a href="{{ route('bookings.show', $booking) }}" class="btn btn-primary"
                                    style="padding: 7px 14px; font-size: 14px;">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="text-align: center; color: #999; padding: 28px;">
                No bookings found.
                @if (Auth::user()->isStudent())
                    <a href="{{ route('bookings.create') }}" style="color: #667eea;">Create your first booking!</a>
                @endif
            </p>
        @endif
    </div>
</x-layout>
