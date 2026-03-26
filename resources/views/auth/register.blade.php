<x-layout>
    <div class="login-reference-wrap">
        <section class="login-reference-card" aria-labelledby="register-title">
        <h1 id="register-title" class="login-reference-title">Register</h1>

        <form method="POST" action="{{ route('register') }}" class="login-reference-form" id="registerForm" data-advisers='@json($advisers)'
            data-old-adviser="{{ old('preferred_adviser_id') }}" data-old-module="{{ old('modules') }}">
            @csrf

            <div class="login-field">
                <x-form.label for="name">Full Name</x-form.label>
                <x-form.input type="text" name="name" value="{{ old('name') }}" required />
                <x-form.error name="name" />
            </div>

            <div class="login-field">
                <x-form.label for="email">Email Address</x-form.label>
                <x-form.input type="email" name="email" value="{{ old('email') }}" required />
                <x-form.error name="email" />
            </div>

            <div class="login-field">
                <x-form.label for="password">Password</x-form.label>
                <x-form.input type="password" name="password" required />
                <x-form.error name="password" />
            </div>

            <div class="login-field">
                <x-form.label for="password_confirmation">Confirm Password</x-form.label>
                <x-form.input type="password" name="password_confirmation" required />
            </div>

            <div class="login-field">
                <x-form.label for="role">I am a</x-form.label>
                <x-form.select id="role" name="role" required>
                    <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                    <option value="adviser" {{ old('role') == 'adviser' ? 'selected' : '' }}>Studies Adviser</option>
                </x-form.select>
                <x-form.error name="role" />
            </div>

            <div class="login-field hidden" id="studentIdField">
                <x-form.label for="student_id">Student ID (Optional)</x-form.label>
                <x-form.input type="text" name="student_id" value="{{ old('student_id') }}" />
            </div>

            <div class="login-field">
                <x-form.label for="phone">Phone Number (Optional)</x-form.label>
                <x-form.input type="text" name="phone" value="{{ old('phone') }}" />
            </div>

            {{-- ── STUDENT: Module picker ─────────────────────────────────────── --}}
            <div class="login-field hidden" id="modulesField">
                <x-form.label for="modules">Module / Area You Need Help With</x-form.label>
                <p class="text-sm text-gray-500 mb-3">
                    Select one module — the adviser list below will automatically filter to show advisers who specialise
                    in the area.
                </p>
                <x-form.select id="modules" name="modules" value="" placeholder="-Select a Module-">
                    @foreach ($expertiseList as $expertise)
                        <option value="{{ $expertise->id }}" {{ old('modules') == $expertise->id ? 'selected' : '' }}>
                            {{ $expertise->name }}
                        </option>
                    @endforeach
                </x-form.select>
                <x-form.error name="modules" />
            </div>

            {{-- ── STUDENT: Adviser picker (filtered by selected modules) ────── --}}
            <div class="login-field hidden" id="adviserField">
                <x-form.label for="preferred_adviser_id">Choose Your Adviser (Optional)</x-form.label>
                <p class="text-sm text-gray-500 mb-3" id="adviserHint">
                    Showing all advisers. Select a module above to filter by expertise.
                </p>
                <select id="preferred_adviser_id" name="preferred_adviser_id">
                    <option value="">— No preference —</option>
                    {{-- Options are injected by filterAdvisers() on page load --}}
                </select>
                <x-form.error name="preferred_adviser_id" />
            </div>

            {{-- ── ADVISER: Single module picker ─────────────────────────────── --}}
            <div class="login-field hidden" id="expertiseField">
                <x-form.label for="expertise_id">Module You Teach</x-form.label>
                <x-form.select id="expertise_id" name="expertise_id" value="" placeholder="Select a module">
                    @foreach ($expertiseList as $expertise)
                        <option value="{{ $expertise->id }}"
                            {{ old('expertise_id') == $expertise->id ? 'selected' : '' }}>
                            {{ $expertise->name }}
                        </option>
                    @endforeach
                </x-form.select>
                <x-form.error name="expertise_id" />
            </div>

            <button type="submit" class="login-submit-btn">Register</button>

            <p class="text-center mt-6">
                Already have an account? <a href="{{ route('login') }}" class="forgot-link">Login here</a>
            </p>
        </form>
        </section>
    </div>

</x-layout>
