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

@include('bookingsView.adviserBooking')
@include('notifications.adviserNotification')

</x-layout>
