<x-layout>
    <div class="card relative bookings-page bookings-page-show pr-4 md:pr-14">
        <div class="table-responsive">
        <a href="{{ route('bookings.index') }}"
            class="absolute top-4 right-4 inline-flex items-center justify-center rounded-full p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700"
            aria-label="Close booking details">
            <x-svg icon="x-mark" size="md" />
        </a>

        <div class="div-show-status">
            <h1 class="h1">Booking Details</h1>
            <span class="span-status"
                style="background: {{
                    $booking->status === 'confirmed' ? '#28a745' :
                    ($booking->status === 'pending' ? '#ffc107' :
                    ($booking->status === 'denied' ? '#dc3545' :
                    ($booking->status === 'completed' ? '#2563eb' : '#6c757d')))
                }}; color: {{ $booking->status === 'pending' ? '#333' : 'white' }};">
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

        @if ($booking->status === 'denied' && $booking->denial_reason)
            <hr class="border-t border-gray-300 my-4">
            <div>
                <h3 class="h3-info">Denial Reason</h3>
                <p class="bg-yellow-50 p-4 rounded">{{ $booking->denial_reason }}</p>
            </div>
        @endif

        @if ($booking->status === 'completed' && $booking->completion_notes)
            <hr class="border-t border-gray-300 my-4">
            <div id="completionNotesDisplay">
                <h3 class="h3-info">Completion Notes</h3>
                <p class="bg-yellow-50 p-4 rounded">{{ $booking->completion_notes }}</p>
            </div>
        @endif

        @if (Auth::user()->isAdviser() && $booking->adviser_id === Auth::id() && $booking->status === 'completed')
            <hr class="border-t border-gray-300 my-4 completion-notes-divider">
            <div class="space-y-3 completion-notes-actions">
                <div class="completion-notes-edit-wrap">
                    <button type="button" onclick="toggleAdviserNotesEdit()" id="toggleNotesEditBtn" class="btn btn-warning inline-block">
                        Edit Notes
                    </button>
                </div>

                <form method="POST" action="{{ route('bookings.updateComment', $booking) }}" id="adviserNotesEditForm" class="space-y-3 hidden">
                    @csrf
                    @method('PUT')
                    <h3 class="h3-info">Edit Adviser Notes</h3>
                    <x-form.group mode="textarea" name="completion_notes"
                        :value="old('completion_notes', $booking->completion_notes)" rows="4"
                        placeholder="Add or update notes for this completed booking..." />

                    <div class="completion-notes-form-actions flex gap-3">
                        <button type="submit" class="btn btn-success">Save Notes</button>
                        <button type="button" onclick="toggleAdviserNotesEdit()" class="btn btn-warning">Cancel Edit</button>
                    </div>
                </form>
            </div>
        @endif

        <hr class="hr-show">

        <div class="flex gap-4 flex-wrap">
            @if (Auth::user()->isAdviser() && $booking->status === 'pending')
                <form method="POST" action="{{ route('bookings.confirm', $booking) }}" class="inline">
                    @csrf
                    <button type="submit" class="btn btn-success">Confirm Booking</button>
                </form>

                <button onclick="showDenyForm()" class="btn btn-danger">Deny Booking</button>
            @endif

            @if ($booking->adviser_id === Auth::id() || (($booking->status === 'pending' || $booking->status === 'confirmed') && $booking->student_id === Auth::id()))
                @if ($booking->status === 'cancelled' || $booking->status === 'denied' && Auth::user()->isAdviser())
                    <x-modal.open target="delete-booking" class="btn btn-danger">Delete Booking</x-modal.open>

                    <x-modal.index name="delete-booking">
                        <x-slot:header>
                            <div class="ml-2">
                            Confirm Deletion
                            </div>
                        </x-slot:header>

                        <p>Are you sure you want to permanently delete this booking?</p>

                        <x-slot:footer>
                            <div class="flex gap-3 justify-end">
                                <x-modal.close target="delete-booking" class="btn hover:bg-green-500 hover:text-white">Keep Booking</x-modal.close>
                                <form method="POST" action="{{ route('bookings.destroy', $booking) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn hover:bg-red-500 hover:text-white">Yes, Delete</button>
                                </form>
                            </div>
                        </x-slot:footer>
                    </x-modal.index>
                @elseif ($booking->status !== 'completed')
                    <x-modal.open target="cancel-booking" class="btn btn-warning">Cancel Booking</x-modal.open>

                    <x-modal.index name="cancel-booking">
                        <x-slot:header>
                            <div class="ml-2">
                            Confirm Cancellation
                            </div>
                        </x-slot:header>

                        <p>Are you sure you want to cancel this booking?</p>

                        <x-slot:footer>
                            <div class="flex gap-3 justify-end">
                                <x-modal.close target="cancel-booking" class="btn hover:bg-green-500 hover:text-white">Keep Booking</x-modal.close>
                                <form method="POST" action="{{ route('bookings.cancel', $booking) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="btn hover:bg-red-500 hover:text-white">Yes, Cancel</button>
                                </form>
                            </div>
                        </x-slot:footer>
                    </x-modal.index>
                @endif
            @endif

            @if (Auth::user()->isAdviser() && $booking->status === 'confirmed')
                <form method="POST" action="{{ route('bookings.complete', $booking) }}" class="inline">
                    @csrf
                    <button type="submit" class="btn btn-success">Completed</button>
                </form>
            @endif
        </div>

        @if (Auth::user()->isAdviser() && $booking->status === 'pending')
            <div id="denyForm" class="hidden mt-8 p-6 bg-gray-50 rounded">
                <h3 class="mb-4 mt-4 font-semibold text-lg">Deny Booking</h3>
                <form method="POST" action="{{ route('bookings.deny', $booking) }}">
                    @csrf
                    <div class="form-group mt-4">
                        <x-form.group mode="textarea" name="denial_reason" label="Reason for Denial (Optional)"
                            :value="old('denial_reason')" rows="3"
                            placeholder="Provide a reason for denying this booking..." />
                    </div>
                    <div class="flex gap-4">
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

        @if (Auth::user()->isAdviser() && $booking->adviser_id === Auth::id() && $booking->status === 'completed')
            <script>
                function toggleAdviserNotesEdit() {
                    const editForm = document.getElementById('adviserNotesEditForm');
                    const displayBlock = document.getElementById('completionNotesDisplay');
                    const toggleButton = document.getElementById('toggleNotesEditBtn');
                    const isHidden = editForm.classList.contains('hidden');

                    if (isHidden) {
                        editForm.classList.remove('hidden');
                        if (displayBlock) {
                            displayBlock.classList.add('hidden');
                        }
                        toggleButton.textContent = 'Hide Editor';
                    } else {
                        editForm.classList.add('hidden');
                        if (displayBlock) {
                            displayBlock.classList.remove('hidden');
                        }
                        toggleButton.textContent = 'Edit Notes';
                    }
                }
            </script>
        @endif
        </div>
    </div>
</x-layout>
