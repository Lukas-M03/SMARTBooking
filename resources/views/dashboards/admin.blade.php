<x-layout>
<div class="card">
    <h1 class="h1-dashboard">Admin Dashboard</h1>
    <p class="text-gray-600 mt-2">System Overview</p>
</div>

<div class="grid-cards">
    <div class="card card-colour1">
        <h3>Total Bookings</h3>
        <p class="card-p">{{ $totalBookings }}</p>
    </div>
    <div class="card card-colour2">
        <h3>Total Users</h3>
        <p class="card-p">{{ $totalUsers }}</p>
    </div>
    <div class="card card-colour3">
        <h3>Pending Requests</h3>
        <p class="card-p">{{ $pendingBookings }}</p>
    </div>
    <div class="card card-colour4">
        <h3>Confirmed</h3>
        <p class="card-p">{{ $confirmedBookings }}</p>
    </div>
</div>

@include('bookingsView.adminBooking')

</x-layout>
