<div class="card">
    <h2 class="h2-notifications">Recent Notifications</h2>
    @if ($notifications->count() > 0)
        @foreach ($notifications as $notification)
            <div class="div-notifications"
                style="border-left-color: 
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
