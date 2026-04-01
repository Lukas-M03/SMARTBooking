<x-layout>
    <div class="card bookings-page bookings-page-create">
        <h1 class="h1";>Request a New Booking</h1>

        <form method="POST" action="{{ route('bookings.store') }}">
            @csrf

            <div class="form-group">
                <x-form.group mode="input" name="topic" label="Meeting Topic" :value="old('topic')" type="text" required
                    placeholder="e.g., Dissertation guidance, Assignment help" />
            </div>

            <div class="form-group">
                <x-form.group mode="textarea" name="description" label="Description (Optional)" :value="old('description')"
                    rows="4" placeholder="Provide more details about your query..." />
            </div>

            <div class="form-group">
                <x-form.group mode="input" name="preferred_datetime" label="Preferred Date & Time" :value="old('preferred_datetime')"
                    type="datetime-local" required />
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
