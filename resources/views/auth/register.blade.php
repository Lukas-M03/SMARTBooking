<x-layout>
    <div class="login-reference-wrap">
        <section class="login-reference-card" aria-labelledby="register-title">
            <h1 id="register-title" class="login-reference-title">Register</h1>

            <form method="POST" action="{{ route('register') }}" class="login-reference-form" id="registerForm" novalidate
                data-advisers='@json($advisers)' data-old-adviser="{{ old('preferred_adviser_id') }}"
                data-old-module="{{ old('modules') }}">
                @csrf

                <div class="login-field">
                    <x-form.group mode="input" name="name" label="Full Name" :value="old('name')" type="text"
                        required placeholder="Full Name" />
                </div>

                <div class="login-field">
                    <x-form.group mode="input" name="email" label="Email Address" :value="old('email')" type="email"
                        required placeholder="Email Address" />
                </div>

                <div class="login-field">
                    <x-form.group mode="input" name="password" label="Password" type="password" required
                        placeholder="Password" />
                </div>

                <div class="login-field">
                    <x-form.group mode="input" name="password_confirmation" label="Confirm Password" type="password"
                        required placeholder="Confirm Password" />
                </div>

                <div class="login-field">
                    <x-form.group mode="select" name="role" label="I am a" :value="old('role')" :options="[
                        'student' => 'Student',
                        'adviser' => 'Studies Adviser',
                    ]"
                        required />
                </div>

                <div class="login-field hidden" id="studentIdField">
                    <x-form.group mode="input" name="student_id" label="Student ID (Optional)" :value="old('student_id')"
                        type="text" placeholder="Student ID (Optional)" />
                </div>

                <div class="login-field">
                    <x-form.group mode="input" name="phone" label="Phone Number (Optional)" :value="old('phone')"
                        type="text" placeholder="Phone Number (Optional)" />
                </div>

                {{-- ── STUDENT: Module picker ─────────────────────────────────────── --}}
                <div class="login-field hidden" id="modulesField">
                    <x-form.group mode="select" name="modules" label="Module / Area You Need Help With"
                        :value="old('modules')" :options="$expertiseList->pluck('name', 'id')" placeholder="-Select a Module-"
                        hint="Select one module - the adviser list below will automatically filter to show advisers who specialise in the area."
                        hintClass="text-sm text-gray-500 mb-4" />
                </div>

                {{-- ── STUDENT: Adviser picker (filtered by selected modules) ────── --}}
                <div class="login-field hidden" id="adviserField">
                    <x-form.group mode="select" name="preferred_adviser_id" label="Choose Your Adviser (Optional)"
                        :value="old('preferred_adviser_id')" hint="Showing all advisers. Select a module above to filter by expertise."
                        hintId="adviserHint" hintClass="text-sm text-gray-500 mb-3" placeholder="-No preference-">
                        {{-- Options are injected by filterAdvisers() on page load --}}
                    </x-form.group>
                </div>

                {{-- ── ADVISER: Single module picker ─────────────────────────────── --}}
                <div class="login-field hidden" id="expertiseField">
                    <x-form.group mode="select" name="expertise_id" label="Module You Teach" :value="old('expertise_id')"
                        :options="$expertiseList->pluck('name', 'id')" placeholder="Select a module" />
                </div>

                <button type="submit" class="login-submit-btn">Register</button>

                <p class="text-center mt-6">
                    Already have an account? <a href="{{ route('login') }}" class="forgot-link">Login here</a>
                </p>
            </form>
        </section>
    </div>

</x-layout>
