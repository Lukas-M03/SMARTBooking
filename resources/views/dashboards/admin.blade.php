<x-layout>
<div class="card">
    <h1 style="color: #667eea;">Admin Dashboard</h1>
    <p style="color: #666; margin-top: 0.5rem;">System Overview</p>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin: 2rem 0;">
    <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <h3>Total Bookings</h3>
        <p style="font-size: 2.5rem; font-weight: bold; margin-top: 1rem;">{{ $totalBookings }}</p>
    </div>
    <div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
        <h3>Total Users</h3>
        <p style="font-size: 2.5rem; font-weight: bold; margin-top: 1rem;">{{ $totalUsers }}</p>
    </div>
    <div class="card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
        <h3>Pending Requests</h3>
        <p style="font-size: 2.5rem; font-weight: bold; margin-top: 1rem;">{{ $pendingBookings }}</p>
    </div>
    <div class="card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;">
        <h3>Confirmed</h3>
        <p style="font-size: 2.5rem; font-weight: bold; margin-top: 1rem;">{{ $confirmedBookings }}</p>
    </div>
</div>

<div class="card">
    <h2 style="margin-bottom: 1.5rem;">Recent Bookings</h2>

    @if($recentBookings->count() > 0)
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #ddd;">
                    <th style="padding: 1rem; text-align: left;">Student</th>
                    <th style="padding: 1rem; text-align: left;">Adviser</th>
                    <th style="padding: 1rem; text-align: left;">Topic</th>
                    <th style="padding: 1rem; text-align: left;">Date & Time</th>
                    <th style="padding: 1rem; text-align: left;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentBookings as $booking)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 1rem;">{{ $booking->student->name }}</td>
                    <td style="padding: 1rem;">{{ $booking->adviser->name }}</td>
                    <td style="padding: 1rem;">{{ $booking->topic }}</td>
                    <td style="padding: 1rem;">{{ $booking->preferred_datetime->format('M d, Y - H:i') }}</td>
                    <td style="padding: 1rem;">
                        <span style="padding: 0.25rem 0.75rem; border-radius: 15px; font-size: 0.875rem; 
                            background: {{ $booking->status === 'confirmed' ? '#28a745' : ($booking->status === 'pending' ? '#ffc107' : '#dc3545') }}; 
                            color: {{ $booking->status === 'pending' ? '#333' : 'white' }};">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="text-align: center; color: #999; padding: 2rem;">No bookings yet.</p>
    @endif
</div>
</x-layout>
