<x-layout>
    <div class="card" style="max-width: 800px; margin: 2rem auto;">
        <h1 class="h1";>Request a New Booking</h1>

        <form method="POST" action="{{ route('bookings.store') }}">
            @csrf

            <div class="form-group">
                <x-form.label for="expertise_id">Area of Expertise / Subject</x-form.label>
                <x-form.select id="expertise_id" name="expertise_id" required>
                    @foreach ($expertiseList as $expertise)
                        <option value="{{ $expertise->id }}"
                            {{ old('expertise_id') == $expertise->id ? 'selected' : '' }}>
                            {{ $expertise->name }}
                        </option>
                    @endforeach
                </x-form.select>
                <x-form.error name="expertise_id" />
                <small class=" text-gray-500 display-block mt-10px">
                    You will be automatically matched with an adviser who specializes in this area.
                </small>
            </div>

            <div class="form-group">
                <x-form.label for="topic">Meeting Topic</x-form.label>
                <x-form.input type="text" id="topic" name="topic" value="{{ old('topic') }}" required
                    placeholder="e.g., Dissertation guidance, Assignment help" />
                <x-form.error name="topic" />
            </div>

            <div class="form-group">
                <x-form.label for="description">Description (Optional)</x-form.label>
                <x-form.textarea id="description" name="description" rows="4"
                    placeholder="Provide more details about your query...">{{ old('description') }}</x-form.textarea>
                <x-form.error name="description" />
            </div>

            <div class="form-group">
                <x-form.label for="preferred_datetime">Preferred Date & Time</x-form.label>
                <x-form.input type="datetime-local" id="preferred_datetime" name="preferred_datetime"
                    value="{{ old('preferred_datetime') }}" required />
                <x-form.error name="preferred_datetime" />
            </div>

            <div class="form-group">
                <x-form.label for="meeting_type">Meeting Type</x-form.label>
                <x-form.select id="meeting_type" name="meeting_type" required>
                    <option value="in-person" {{ old('meeting_type') == 'in-person' ? 'selected' : '' }}>In-Person
                    </option>
                    <option value="online" {{ old('meeting_type') == 'online' ? 'selected' : '' }}>Online (Video Call)
                    </option>
                    <option value="phone" {{ old('meeting_type') == 'phone' ? 'selected' : '' }}>Phone Call</option>
                </x-form.select>
                <x-form.error name="meeting_type" />
            </div>

            <div class="display-flex gap-16px" style="justify-content: space-between;">
                <button type="submit" class="btn btn-primary flex-1" >Submit Booking Request</button>
                <a href="{{ route('student.dashboard') }}" class="btn btn-warning">Cancel</a>
            </div>
        </form>
    </div>
</x-layout>
