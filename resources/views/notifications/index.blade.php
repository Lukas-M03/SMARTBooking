<x-layout>
    <div class="card">
        <div class="div-mark-read">
            <h1 class="h1">Notifications</h1>
            @if ($notifications->where('is_read', false)->count() > 0)
                <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">Mark All as Read</button>
                </form>
            @endif
        </div>

        @if ($notifications->count() > 0)
            @foreach ($notifications as $notification)
                <div class="div-notifications-list"
                    style="border-left-color: {{ $notification->type === 'success' ? '#28a745' : ($notification->type === 'warning' ? '#ffc107' : ($notification->type === 'error' ? '#dc3545' : '#667eea')) }}; background: {{ $notification->is_read ? '#f8f9fa' : '#e7f3ff' }};">

                    <div style="flex: 1;">
                        <strong style="font-size: 17px;">{{ $notification->title }}</strong>
                        <p style="margin-top: 7px; color: #666;">{{ $notification->message }}</p>
                        <small style="color: #999;">{{ $notification->created_at->diffForHumans() }}</small>
                    </div>
                    @if (!$notification->is_read)
                        <form method="POST" action="{{ route('notifications.mark-read', $notification->id) }}">
                            @csrf
                            <button type="submit"
                                style="background: none; border: none; color: #667eea; cursor: pointer; font-size: 14px; text-decoration: underline;">
                                Mark as read
                            </button>
                        </form>
                    @endif
                </div>
            @endforeach

            <div style="margin-top: 28px;">
                {{ $notifications->links() }}
            </div>
        @else
            <p style="text-align: center; color: #999; padding: 28px;">No notifications yet.</p>
        @endif
    </div>
</x-layout>
