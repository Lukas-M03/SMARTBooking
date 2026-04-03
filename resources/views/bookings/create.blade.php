<x-layout>
    <div class="card bookings-page bookings-page-create">
        <h1 class="h1";>Request a New Booking</h1>

        <form method="POST" action="{{ route('bookings.store') }}">
            @csrf

            <div class="form-group">
                <x-form.group mode="input" name="topic" label="Meeting Topic" :value="old('topic')" type="text"
                    placeholder="e.g., Dissertation guidance, Assignment help" />
            </div>

            <div class="form-group">
                <x-form.group mode="textarea" name="description" label="Description (Optional)" :value="old('description')"
                    rows="4" placeholder="Provide more details about your query..." />
            </div>

            <div class="form-group booking-slot-picker" id="bookingSlotPicker" data-slots-url="{{ route('bookings.available-slots') }}"
                data-old-datetime="{{ old('preferred_datetime') }}">
                <x-form.label for="booking_slot_date">Preferred Date & Time</x-form.label>
                <p class="booking-slot-hint" id="bookingSlotHint">Select a date to load available 30-minute slots.</p>

                <input id="booking_slot_date" type="date" class="booking-slot-date-input" required>
                <input type="hidden" name="preferred_datetime" id="preferred_datetime" value="{{ old('preferred_datetime') }}" required>

                <div class="booking-slot-grid" id="bookingSlotGrid" aria-live="polite"></div>

                <x-form.error name="preferred_datetime" />
            </div>

            <div class="form-group">
                <x-form.group mode="select" name="meeting_type" label="Meeting Type" :value="old('meeting_type')"
                    :options="[
                        'in-person' => 'In-Person',
                        'online' => 'Online (Video Call)',
                        'phone' => 'Phone Call',
                    ]" required />
            </div>

            <div class="booking-form-actions">
                <button type="submit" class="btn btn-primary flex-1">Submit Booking Request</button>
                <a href="{{ route('student.dashboard') }}" class="btn btn-warning">Cancel</a>
            </div>
        </form>
    </div>
</x-layout>
