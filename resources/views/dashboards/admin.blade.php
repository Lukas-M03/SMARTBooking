<x-layout>
<div class="card">
    <h1 class="h1-dashboard">Admin Dashboard</h1>
    <p class="text-gray-600 mt-2">System Overview</p>
</div>

<div class="grid-cards">
    <a href="{{ route('admin.users') }}" class="card card-colour">
        <h3 class="text-black font-bold">Users</h3>
        <p class="card-p text-black">{{ $totalUsers }}</p>
    </a>

    <a href="{{ route('admin.bookings.completed') }}" class="card card-colour">
        <h3 class="text-black font-bold">Completed Bookings</h3>
        <p class="card-p text-black">{{ $completedBookings }}</p>
    </a>
</div>

</x-layout>
