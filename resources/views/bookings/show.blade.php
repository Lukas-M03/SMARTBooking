<x-layout>
    <div class="card">
        <div class="div-show-status">
            <h1 class="h1">Booking Details</h1>
            <span class="span-status"
                style="background: {{ $booking->status === 'confirmed' ? '#28a745' : ($booking->status === 'pending' ? '#ffc107' : ($booking->status === 'denied' ? '#dc3545' : '#6c757d')) }}; color: {{ $booking->status === 'pending' ? '#333' : 'white' }};">
                {{ ucfirst($booking->status) }}
            </span>
        </div>

        <div class="div-info">
            <div >
                <h3 class="h3-info">Student Information</h3>
                <p><strong>Name:</strong> {{ $booking->student->name }}</p>
                <p><strong>Email:</strong> {{ $booking->student->email }}</p>
                @if ($booking->student->student_id)
                    <p><strong>Student ID:</strong> {{ $booking->student->student_id }}</p>
                @endif
                @if ($booking->student->phone)
                    <p><strong>Phone:</strong> {{ $booking->student->phone }}</p>
                @endif
            </div>

            <div>
                <h3 class="h3-info">Adviser Information</h3>
                <p><strong>Name:</strong> {{ $booking->adviser->name }}</p>
                <p><strong>Email:</strong> {{ $booking->adviser->email }}</p>
                @if ($booking->adviser->phone)
                    <p><strong>Phone:</strong> {{ $booking->adviser->phone }}</p>
                @endif
            </div>
        </div>

        <hr class="hr-show">

        <div>
            <h3 class="h3-info">Meeting Details</h3>
            <p><strong>Topic:</strong> {{ $booking->topic }}</p>
            <p><strong>Expertise Area:</strong> {{ $booking->expertise->name }}</p>
            <p><strong>Preferred Date & Time:</strong>
                {{ $booking->preferred_datetime->format('l, F j, Y \a\t g:i A') }}</p>
            <p><strong>Meeting Type:</strong> {{ ucfirst($booking->meeting_type) }}</p>
            @if ($booking->description)
                <p><strong>Description:</strong></p>
                <p class="p-description">
                    {{ $booking->description }}</p>
            @endif
        </div>

        @if ($booking->adviser_notes)
            <hr class="border: none; border-top: 1px solid #ddd; margin: 14px 0;">
            <div>
                <h3 class="h3-info">Adviser Notes</h3>
                <p style="background: #fff3cd; padding: 1rem; border-radius: 5px;">{{ $booking->adviser_notes }}</p>
            </div>
        @endif

        <hr class="hr-show">

        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            @if (Auth::user()->isAdviser() && $booking->status === 'pending')
                <form method="POST" action="{{ route('bookings.confirm', $booking) }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success">✓ Confirm Booking</button>
                </form>

                <button onclick="showDenyForm()" class="btn btn-danger">✗ Deny Booking</button>
            @endif

            @if (
                (($booking->status === 'pending' || $booking->status === 'confirmed') && $booking->student_id === Auth::id()) ||
                    $booking->adviser_id === Auth::id())
                <form method="POST" action="{{ route('bookings.cancel', $booking) }}"
                    onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                    @csrf
                    <button type="submit" class="btn btn-warning">Cancel Booking</button>
                </form>
            @endif

            <a href="{{ route('bookings.index') }}" class="btn btn-primary">← Back to Bookings</a>
        </div>

        @if (Auth::user()->isAdviser() && $booking->status === 'pending')
            <div id="denyForm"
                style="display: none; margin-top: 2rem; padding: 1.5rem; background: #f8f9fa; border-radius: 5px;">
                <h3 style="margin-bottom: 1rem;">Deny Booking</h3>
                <form method="POST" action="{{ route('bookings.deny', $booking) }}">
                    @csrf
                    <div class="form-group">
                        <label for="adviser_notes">Reason for Denial (Optional)</label>
                        <textarea id="adviser_notes" name="adviser_notes" rows="3"
                            placeholder="Provide a reason for denying this booking..."></textarea>
                    </div>
                    <div style="display: flex; gap: 1rem;">
                        <button type="submit" class="btn btn-danger">Confirm Denial</button>
                        <button type="button" onclick="hideDenyForm()" class="btn btn-warning">Cancel</button>
                    </div>
                </form>
            </div>

            <script>
                function showDenyForm() {
                    document.getElementById('denyForm').style.display = 'block';
                }

                function hideDenyForm() {
                    document.getElementById('denyForm').style.display = 'none';
                }
            </script>
        @endif
    </div>
</x-layout>
