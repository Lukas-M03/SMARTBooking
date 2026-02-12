<x-layout>
<div class="card">
    <h1 class="h1-dashboard">Welcome, {{ Auth::user()->name }}!</h1>
    <p class="text-gray-600 mt-2">Studies Adviser Dashboard</p>
</div>

<div class="grid-cards">
    <div class="card card-colour1">
        <h3>Total Bookings</h3>
        <p class="card-p">{{ $stats['total'] }}</p>
    </div>
    <div class="card card-colour2">
        <h3>Pending Requests</h3>
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
    <h2 class="mb-20">Pending Booking Requests</h2>

    @if($pendingBookings->count() > 0)
        <table class="w-full border-collapse">
            <thead>
                <tr class="border-b-2 border-gray-300">
                    <th class="p-14 text-left">Student</th>
                    <th class="p-14 text-left">Topic</th>
                    <th class="p-14 text-left">Preferred Date & Time</th>
                    <th class="p-14 text-left">Meeting Type</th>
                    <th class="p-14 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pendingBookings as $booking)
                <tr class="border-b border-gray-200">
                    <td class="p-14">{{ $booking->student->name }}</td>
                    <td class="p-14">{{ $booking->topic }}</td>
                    <td class="p-14">{{ $booking->preferred_datetime->format('M d, Y - H:i') }}</td>
                    <td class="p-14">{{ ucfirst($booking->meeting_type) }}</td>
                    <td class="p-14">
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
                <tr class="border-b-2 border-gray-300">
                    <th class="p-14 text-left">Student</th>
                    <th class="p-14 text-left">Topic</th>
                    <th class="p-14 text-left">Date & Time</th>
                    <th class="p-14 text-left">Meeting Type</th>
                    <th class="p-14 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($upcomingBookings as $booking)
                <tr class="border-b border-gray-200">
                    <td class="p-14">{{ $booking->student->name }}</td>
                    <td class="p-14">{{ $booking->topic }}</td>
                    <td class="p-14">{{ $booking->preferred_datetime->format('M d, Y - H:i') }}</td>
                    <td class="p-14">{{ ucfirst($booking->meeting_type) }}</td>
                    <td class="p-14">
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

<div class="card">
    <h2 class="mb-6">Recent Notifications</h2>
    @if($notifications->count() > 0)
        @foreach($notifications as $notification)
        <div style="padding: 1rem; margin-bottom: 1rem; border-left: 4px solid 
            {{ $notification->type === 'success' ? '#28a745' : ($notification->type === 'warning' ? '#ffc107' : '#667eea') }}; 
            background: #f8f9fa; border-radius: 5px;">
            <strong>{{ $notification->title }}</strong>
            <p style="margin-top: 0.5rem; color: #666;">{{ $notification->message }}</p>
            <small style="color: #999;">{{ $notification->created_at->diffForHumans() }}</small>
        </div>
        @endforeach
        <a href="{{ route('notifications.index') }}" style="color: #667eea;">View All Notifications</a>
    @else
        <p style="text-align: center; color: #999;">No notifications yet.</p>
    @endif
</div>
</x-layout>
