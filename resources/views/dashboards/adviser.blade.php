<x-layout>
    <div class="card">
        <div class="flex justify-between items-start gap-4 flex-wrap">
            <div>
                <h1 class="h1-dashboard">Welcome, {{ Auth::user()->name }}!</h1>
                <p class="text-gray-600 mt-2 font-bold">Adviser Dashboard</p>
                <div>
                    <h3 class="text-gray-600">Microsoft Outlook Calendar</h3>
                    <div class="text-gray-600 mt-2">
                        @if (Auth::user()->hasMicrosoftToken())
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
            <h3 class="text-black font-bold">Total Bookings</h3>
            <p class="card-p text-black">{{ $stats['total'] }}</p>
        </a>
        
        <a href="{{ route('bookings.index', ['status' => 'pending']) }}" class="card card-colour">
            <h3 class="text-black font-bold">Pending Requests</h3>
            <p class="card-p text-black">{{ $stats['pending'] }}</p>
        </a>

        <a href="{{ route('bookings.index', ['status' => 'confirmed']) }}" class="card card-colour">
            <h3 class="text-black font-bold">Confirmed</h3>
            <p class="card-p text-black">{{ $stats['confirmed'] }}</p>
        </a>

        <a href="{{ route('bookings.index', ['status' => 'completed']) }}" class="card card-colour">
            <h3 class="text-black font-bold">Completed</h3>
            <p class="card-p text-black">{{ $stats['completed'] }}</p>
        </a>
    </div>

    @include('bookingsView.adviserBooking')

    <div class="calendar-card-info calendar-card">
        <div class="calendar-header">
            <h3 class="text-gray-600 mt-2 font-bold">Your Bookings Calendar</h3>
            <p class="text-gray-600 mt-2">All your booking requests and confirmed sessions.</p>
        </div>
        <div class="booking-calendar js-booking-calendar" data-events-url="{{ route('calendar.events') }}"></div>
    </div>

    

</x-layout>
