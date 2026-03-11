<x-layout>
    {{-- Embed adviser data as JSON so JS can filter without an extra HTTP request --}}
    <script>
        const ALL_ADVISERS = @json($advisers);
    </script>

    <div class="card" style="max-width: 66.67vw; margin: 3rem auto;">
        <h1 class="h1">Register</h1>

        <form method="POST" action="{{ route('register') }}" id="registerForm">
            @csrf

            <div class="form-group">
                <x-form.label for="name">Full Name</x-form.label>
                <x-form.input type="text" name="name" value="{{ old('name') }}" required />
                <x-form.error name="name" />
            </div>

            <div class="form-group">
                <x-form.label for="email">Email Address</x-form.label>
                <x-form.input type="email" name="email" value="{{ old('email') }}" required />
                <x-form.error name="email" />
            </div>

            <div class="form-group">
                <x-form.label for="password">Password</x-form.label>
                <x-form.input type="password" name="password" required />
                <x-form.error name="password" />
            </div>

            <div class="form-group">
                <x-form.label for="password_confirmation">Confirm Password</x-form.label>
                <x-form.input type="password" name="password_confirmation" required />
            </div>

            <div class="form-group">
                <x-form.label for="role">I am a</x-form.label>
                <x-form.select id="role" name="role" required onchange="toggleRoleFields()">
                    <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                    <option value="adviser" {{ old('role') == 'adviser' ? 'selected' : '' }}>Studies Adviser</option>
                </x-form.select>
                <x-form.error name="role" />
            </div>

            <div class="form-group" id="studentIdField" style="display: none;">
                <x-form.label for="student_id">Student ID (Optional)</x-form.label>
                <x-form.input type="text" name="student_id" value="{{ old('student_id') }}" />
            </div>

            <div class="form-group">
                <x-form.label for="phone">Phone Number (Optional)</x-form.label>
                <x-form.input type="text" name="phone" value="{{ old('phone') }}" />
            </div>

            {{-- ── STUDENT: Module picker ─────────────────────────────────────── --}}
            <div class="form-group" id="modulesField" style="display: none;">
                <x-form.label>Modules / Areas You Need Help With</x-form.label>
                <p class="text-sm text-gray-500" style="margin-bottom: 0.75rem;">
                    Select one or more areas — the adviser list below will automatically filter to show advisers who specialise in those areas.
                </p>
                @foreach ($expertiseList as $expertise)
                    <label style="display: flex; align-items: center; margin: 0.4rem 0; cursor: pointer;">
                        <input
                            type="checkbox"
                            class="module-checkbox"
                            name="modules[]"
                            value="{{ $expertise->id }}"
                            {{ is_array(old('modules')) && in_array($expertise->id, old('modules')) ? 'checked' : '' }}
                            style="width: auto; margin-right: 0.6rem;"
                            onchange="filterAdvisers()"
                        >
                        <span>
                            <strong>{{ $expertise->name }}</strong>
                            @if($expertise->description)
                                <span class="text-gray-500" style="font-size: 0.82rem; margin-left: 0.4rem;">— {{ $expertise->description }}</span>
                            @endif
                        </span>
                    </label>
                @endforeach
                <x-form.error name="modules" />
            </div>

            {{-- ── STUDENT: Adviser picker (filtered by selected modules) ────── --}}
            <div class="form-group" id="adviserField" style="display: none;">
                <x-form.label for="preferred_adviser_id">Choose Your Adviser (Optional)</x-form.label>
                <p class="text-sm text-gray-500" id="adviserHint" style="margin-bottom: 0.75rem;">
                    Showing all advisers. Select a module above to filter by expertise.
                </p>
                <select id="preferred_adviser_id" name="preferred_adviser_id" style="width:100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                    <option value="">— No preference —</option>
                    {{-- Options are injected by filterAdvisers() on page load --}}
                </select>
                <x-form.error name="preferred_adviser_id" />
            </div>

            {{-- ── ADVISER: Expertise checkboxes ─────────────────────────────── --}}
            <div class="form-group" id="expertiseField" style="display: none;">
                <x-form.label>Areas of Expertise</x-form.label>
                @foreach ($expertiseList as $expertise)
                    <label style="display: flex; align-items: center; margin: 0.5rem 0; cursor: pointer;">
                        <input
                            type="checkbox"
                            name="expertise[]"
                            value="{{ $expertise->id }}"
                            {{ is_array(old('expertise')) && in_array($expertise->id, old('expertise')) ? 'checked' : '' }}
                            style="width: auto; margin-right: 0.5rem;"
                        >
                        {{ $expertise->name }}
                    </label>
                @endforeach
                <x-form.error name="expertise" />
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Register</button>

            <p style="text-align: center; margin-top: 1.5rem;">
                Already have an account? <a href="{{ route('login') }}" style="color: #667eea;">Login here</a>
            </p>
        </form>
    </div>

    <script>
        const oldAdviser   = "{{ old('preferred_adviser_id') }}";
        const oldModules   = @json(old('modules', []));

        function toggleRoleFields() {
            const role           = document.getElementById('role').value;
            const studentIdField = document.getElementById('studentIdField');
            const modulesField   = document.getElementById('modulesField');
            const adviserField   = document.getElementById('adviserField');
            const expertiseField = document.getElementById('expertiseField');

            if (role === 'student') {
                studentIdField.style.display = 'block';
                modulesField.style.display   = 'block';
                adviserField.style.display   = 'block';
                expertiseField.style.display = 'none';
            } else if (role === 'adviser') {
                studentIdField.style.display = 'none';
                modulesField.style.display   = 'none';
                adviserField.style.display   = 'none';
                expertiseField.style.display = 'block';
            } else {
                studentIdField.style.display = 'none';
                modulesField.style.display   = 'none';
                adviserField.style.display   = 'none';
                expertiseField.style.display = 'none';
            }
        }

        /**
         * Re-builds the adviser <select> options based on which module
         * checkboxes are currently ticked.
         * If no modules are selected, all advisers are shown.
         */
        function filterAdvisers() {
            const checked = Array.from(
                document.querySelectorAll('.module-checkbox:checked')
            ).map(cb => parseInt(cb.value));

            const hint   = document.getElementById('adviserHint');
            const select = document.getElementById('preferred_adviser_id');

            // Determine which advisers qualify.
            const filtered = checked.length === 0
                ? ALL_ADVISERS
                : ALL_ADVISERS.filter(adviser =>
                    checked.some(moduleId => adviser.expertise.includes(moduleId))
                  );

            // Update hint text.
            if (checked.length === 0) {
                hint.textContent = 'Showing all advisers. Select a module above to filter by expertise.';
            } else {
                hint.textContent = filtered.length === 0
                    ? 'No advisers found for the selected modules.'
                    : `Showing ${filtered.length} adviser(s) who cover your selected module(s).`;
            }

            // Rebuild options, preserving any previously selected adviser (e.g. after validation error).
            const currentVal = select.value || oldAdviser;
            select.innerHTML = '<option value="">— No preference —</option>';
            filtered.forEach(adviser => {
                const opt     = document.createElement('option');
                opt.value     = adviser.id;
                opt.textContent = adviser.name;
                if (String(adviser.id) === String(currentVal)) opt.selected = true;
                select.appendChild(opt);
            });
        }

        // Initialise on page load.
        document.addEventListener('DOMContentLoaded', function () {
            toggleRoleFields();
            // Restore checked module state from old() after a validation error.
            if (oldModules.length) {
                oldModules.forEach(id => {
                    const cb = document.querySelector(`.module-checkbox[value="${id}"]`);
                    if (cb) cb.checked = true;
                });
            }
            filterAdvisers();
        });
    </script>
</x-layout>
