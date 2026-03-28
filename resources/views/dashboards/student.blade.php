<x-layout>
<div class="card">
    <div class="flex justify-between items-start gap-4 flex-wrap">
        <div>
            <h1 class="h1-dashboard">Welcome, {{ Auth::user()->name }}!</h1>
            <p class="text-gray-600 mt-2 font-bold">Student Dashboard</p>
            <div>
                <h3 class="text-gray-600">Microsoft Outlook Calendar</h3>
                <div class="text-gray-600 mt-2">
                    @if(Auth::user()->hasMicrosoftToken())
                        <span class="text-green-700 font-semibold">Connected</span>
                        <p class="text-sm text-gray-500 mt-1">Your bookings will automatically sync to Outlook</p>
                    @else
                        <span class="text-orange-700">Not Connected</span>
                    @endif
                </div>
            </div>
         </div>
        <div class="text-right ml-auto">
            <p class="text-sm text-gray-500 mb-2">
                Total Notifications:
                <span class="font-semibold text-gray-700">{{ $stats['notifications_total'] }}</span>
            </p>
            <a href="{{ route('notifications.index') }}" class="nav-link">View All Notifications</a>
        </div>
    </div>
</div>

<div class="grid-cards">
    <a href="{{ route('bookings.index') }}" class="card card-colour">
        <h3>Total Bookings</h3>
        <p class="card-p">{{ $stats['total'] }}</p>
    </a>
    <a href="{{ route('bookings.index' , ['status' => 'pending']) }}" class="card card-colour">
        <h3>Pending</h3>
        <p class="card-p">{{ $stats['pending'] }}</p>
    </a>
    <a href="{{ route('bookings.index' , ['status' => 'confirmed']) }}" class="card card-colour">
        <h3>Confirmed</h3>
        <p class="card-p">{{ $stats['confirmed'] }}</p>
    </a>
    <div class="card card-colour">
        <h3>Completed</h3>
        <p class="card-p">{{ $stats['completed'] }}</p>
    </div>
</div>

<div class="calendar-card-info calendar-card">
    <div class="calendar-header">
        <h3 class="text-gray-600 mt-2 font-bold">Adviser Availability + Your Bookings</h3>
        <p class="text-gray-600 mt-2">Busy times are shaded and cannot be booked.</p>
    </div>
    <div class="booking-calendar js-booking-calendar" data-events-url="{{ route('calendar.events') }}"></div>
</div>

@include('bookingsView.studentBooking')

</x-layout>