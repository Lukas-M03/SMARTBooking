<x-layout>
<div class="card">
    <h1 class="h1-dashboard">Welcome, {{ Auth::user()->name }}!</h1>
    <p class="text-gray-600 mt-2">Student Dashboard</p>
</div>

<div class="grid-cards">
    <div class="card card-colour1">
        <h3>Total Bookings</h3>
        <p class="card-p">{{ $stats['total'] }}</p>
    </div>
    <div class="card card-colour2">
        <h3>Pending</h3>
        <p class="card-p">{{ $stats['pending'] }}</p>
    </div>
    <div class="card card-colour3">
        <h3>Confirmed</h3>
        <p class="card-p">{{ $stats['confirmed'] }}</p>
    </div>
    <div class="card card-colour4">
        <h3>Completed</h3>
        <p class="card-p">{{ $stats['completed'] }}</p>
    </div>
</div>

<div class="card">
    <div class="card-upcoming-bookings">
        <h2>Upcoming Bookings</h2>
        <a href="{{ route('bookings.create') }}" class="btn btn-primary">+ New Booking</a>
    </div>

    @if($upcomingBookings->count() > 0)
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #ddd;">
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

<div class="card">
    <h2 class="h2-notifications">Recent Notifications</h2>
    @if($notifications->count() > 0)
        @foreach($notifications as $notification)
        <div class="div-notifications" style="border-left-color: 
            {{ $notification->type === 'success' ? '#28a745' : ($notification->type === 'warning' ? '#ffc107' : '#667eea') }};">
            <strong>{{ $notification->title }}</strong>
            <p class="mt-2 text-gray-600">{{ $notification->message }}</p>
            <small class="text-gray-500">{{ $notification->created_at->diffForHumans() }}</small>
        </div>
        @endforeach
        <a href="{{ route('notifications.index') }}" class="text-blue-600 hover:underline">View All Notifications</a>
    @else
        <p style="text-align: center; color: #999;">No notifications yet.</p>
    @endif
</div>
</x-layout>
