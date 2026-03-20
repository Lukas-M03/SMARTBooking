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
                <div
                    class="div-notifications-list {{ $notification->is_read ? 'bg-gray-50' : 'bg-blue-50' }} {{ $notification->type === 'success' ? 'border-l-green-600' : ($notification->type === 'warning' ? 'border-l-amber-400' : ($notification->type === 'error' ? 'border-l-red-600' : 'border-l-indigo-500')) }}">

                    <div class="flex-1">
                        <strong class="text-[17px]">{{ $notification->title }}</strong>
                        <p class="mt-2 text-gray-600">{{ $notification->message }}</p>
                        <small class="text-gray-400">{{ $notification->created_at->diffForHumans() }}</small>
                    </div>
                    @if (!$notification->is_read)
                        <form method="POST" action="{{ route('notifications.mark-read', $notification->id) }}">
                            @csrf
                            <button type="submit" class="bg-transparent border-none text-indigo-500 cursor-pointer text-sm underline">
                                Mark as read
                            </button>
                        </form>
                    @endif
                </div>
            @endforeach

            <div class="mt-7">
                {{ $notifications->links() }}
            </div>
        @else
            <p class="text-center text-gray-400 p-7">No notifications yet.</p>
        @endif
    </div>
</x-layout>
