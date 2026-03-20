<x-layout>
<div class="card">
    <h1 class="h1-dashboard">Welcome, {{ Auth::user()->name }}!</h1>
    <p class="text-gray-600 mt-2">Student Dashboard</p>
</div>

<!-- Microsoft Calendar Connection -->
<div class="card border-l-4 border-[#0078d4]">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="m-0">Microsoft Outlook Calendar</h3>
            <div class="text-gray-600 mt-2">
                @if(Auth::user()->hasMicrosoftToken())
                    <span class="text-green-700 font-semibold">✓ Connected</span>
                    <p class="text-sm text-gray-500 mt-1">Your bookings will automatically sync to Outlook</p>
                @else
                    <span class="text-orange-700">Not Connected</span>
                    <p class="text-sm text-gray-500 mt-1">Connect your Outlook calendar to receive booking notifications</p>
                @endif
            </div>
        </div>
        <div>
            @if(Auth::user()->hasMicrosoftToken())
                <a href="{{ route('microsoft.disconnect') }}" class="text-red-600 hover:text-red-800">Disconnect</a>
            @else
                <a href="{{ route('microsoft.redirect') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Connect Outlook
                </a>
            @endif
        </div>
    </div>
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

<div class="card calendar-card">
    <div class="calendar-header">
        <h3>Adviser Availability + Your Bookings</h3>
        <p class="text-gray-600 mt-2">Busy times are shaded and cannot be booked.</p>
    </div>
    <div class="booking-calendar js-booking-calendar" data-events-url="{{ route('calendar.events') }}"></div>
</div>

@include('bookingsView.studentBooking')
@include('notifications.studentNotification')

</x-layout>
