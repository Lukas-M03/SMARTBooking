<div class="card">
    <h2 class="mb-3.5">Recent Notifications</h2>
    @if ($notifications->count() > 0)
        @foreach ($notifications as $notification)
            <div
                class="div-recent-notifications {{ $notification->type === 'success' ? 'border-l-green-600' : ($notification->type === 'warning' ? 'border-l-amber-400' : 'border-l-indigo-500') }}">
                <strong>{{ $notification->title }}</strong>
                <p class="mt-2 text-gray-600">{{ $notification->message }}</p>
                <small class="text-gray-400">{{ $notification->created_at->diffForHumans() }}</small>
            </div>
        @endforeach
        <a href="{{ route('notifications.index') }}" class="text-indigo-500">View All Notifications</a>
    @else
        <p class="text-center text-gray-400">No notifications yet.</p>
    @endif
</div>
