<x-layout>
    <div class="card bookings-page bookings-page-index">
        <div class="div-mybookings">
            <h1 class="h1-dashboard">My Bookings</h1>
            @if (Auth::user()->isStudent())
                <a href="{{ route('bookings.create') }}" class="btn btn-primary">+ New Booking</a>
            @endif
        </div>

        @if ($bookings->count() > 0)
            <div class="table-responsive bookings-table-wrap">
                <table class="w-full border-collapse min-w-175">
                    <thead>
                        <tr class="tr">
                            @if (Auth::user()->isStudent())
                                <th class="th">Adviser</th>
                            @else
                                <th class="th">Student</th>
                            @endif
                            <th class="th">Topic</th>
                            <th class="th hide-mobile">Expertise</th>
                            <th class="th">Date & Time</th>
                            <th class="th hide-mobile">Type</th>
                            <th class="th">Status</th>
                            <th class="th">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bookings as $booking)
                            <tr class="tr">
                                @if (Auth::user()->isStudent())
                                    <td class="td">{{ $booking->adviser->name }}</td>
                                @else
                                    <td class="td">{{ $booking->student->name }}</td>
                                @endif
                                <td class="td">{{ $booking->topic }}</td>
                                <td class="td hide-mobile">{{ $booking->expertise->name }}</td>
                                <td class="td">{{ $booking->preferred_datetime->format('M d, Y - H:i') }}</td>
                                <td class="td hide-mobile">{{ ucfirst($booking->meeting_type) }}</td>
                                <td class="td">
                                    <span class="span-status"
                                        style="background: {{ $booking->status === 'confirmed'
                                            ? '#28a745'
                                            : ($booking->status === 'pending'
                                                ? '#ffc107'
                                                : ($booking->status === 'denied'
                                                    ? '#dc3545'
                                                    : ($booking->status === 'completed'
                                                        ? '#2563eb'
                                                        : '#6c757d'))) }}; color: {{ $booking->status === 'pending' ? '#333' : 'white' }};">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td class="td">
                                    <a href="{{ route('bookings.show', $booking) }}"
                                        class="mr-4 text-indigo-500 hover:text-indigo-400 text-base font-semibold py-1 px-3 cursor-pointer hover:underline">View</a>
                                    @if (Auth::user()->isAdviser() && in_array($booking->status, ['denied', 'cancelled']))
                                        <x-modal.open target="delete-booking-{{ $booking->id }}"
                                            class="ml-4 text-red-500 hover:text-red-400 text-base font-semibold py-1 px-3 cursor-pointer hover:underline">Delete</x-modal.open>

                                        <x-modal.index name="delete-booking-{{ $booking->id }}">
                                            <x-slot:header>
                                                <div class="ml-2">
                                                    Confirm Deletion
                                                </div>
                                            </x-slot:header>

                                            <p>Are you sure you want to permanently delete this booking?</p>

                                            <x-slot:footer>
                                                <div class="flex gap-3 justify-end">
                                                    <x-modal.close target="delete-booking-{{ $booking->id }}"
                                                        class="btn hover:bg-green-500 hover:text-white">Keep
                                                        Booking</x-modal.close>
                                                    <form method="POST"
                                                        action="{{ route('bookings.destroy', $booking) }}"
                                                        class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn hover:bg-red-500 hover:text-white">Yes,
                                                            Delete</button>
                                                    </form>
                                                </div>
                                            </x-slot:footer>
                                        </x-modal.index>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="text-align: center; color: #999; padding: 28px;">
                No bookings found.
                @if (Auth::user()->isStudent())
                    <a href="{{ route('bookings.create') }}" style="color: #667eea;">Create your first booking!</a>
                @endif
            </p>
        @endif
    </div>
</x-layout>
