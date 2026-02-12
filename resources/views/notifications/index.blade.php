<x-layout>
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h1 style="color: #667eea;">Notifications</h1>
            @if ($notifications->where('is_read', false)->count() > 0)
                <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">Mark All as Read</button>
                </form>
            @endif
        </div>

        @if ($notifications->count() > 0)
            @foreach ($notifications as $notification)
                <div
                    style="padding: 1.5rem; margin-bottom: 1rem; 
                border-left: 4px solid {{ $notification->type === 'success' ? '#28a745' : ($notification->type === 'warning' ? '#ffc107' : ($notification->type === 'error' ? '#dc3545' : '#667eea')) }}; 
                background: {{ $notification->is_read ? '#f8f9fa' : '#e7f3ff' }}; 
                border-radius: 5px;
                display: flex;
                justify-content: space-between;
                align-items: start;">
                    <div style="flex: 1;">
                        <strong style="font-size: 1.1rem;">{{ $notification->title }}</strong>
                        <p style="margin-top: 0.5rem; color: #666;">{{ $notification->message }}</p>
                        <small style="color: #999;">{{ $notification->created_at->diffForHumans() }}</small>
                    </div>
                    @if (!$notification->is_read)
                        <form method="POST" action="{{ route('notifications.mark-read', $notification->id) }}">
                            @csrf
                            <button type="submit"
                                style="background: none; border: none; color: #667eea; cursor: pointer; font-size: 0.875rem; text-decoration: underline;">
                                Mark as read
                            </button>
                        </form>
                    @endif
                </div>
            @endforeach

            <div style="margin-top: 2rem;">
                {{ $notifications->links() }}
            </div>
        @else
            <p style="text-align: center; color: #999; padding: 2rem;">No notifications yet.</p>
        @endif
    </div>
</x-layout>
