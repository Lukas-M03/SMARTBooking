<div class="card">
    <h2 style="margin-bottom: 14px">Recent Notifications</h2>
    @if ($notifications->count() > 0)
        @foreach ($notifications as $notification)
            <div class="div-recent-notifications"
                style="border-left-color: {{ $notification->type === 'success' ? '#28a745' : ($notification->type === 'warning' ? '#ffc107' : '#667eea') }};">
                <strong>{{ $notification->title }}</strong>
                <p style="margin-top: 8px; color: #666;">{{ $notification->message }}</p>
                <small style="color: #999;">{{ $notification->created_at->diffForHumans() }}</small>
            </div>
        @endforeach
        <a href="{{ route('notifications.index') }}" style="color: #667eea;">View All Notifications</a>
    @else
        <p style="text-align: center; color: #999;">No notifications yet.</p>
    @endif
</div>
