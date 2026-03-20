<x-layout>
    <div class="card max-w-[66.67vw] mx-auto my-12">
        <h1 class="h1">Register</h1>

        <form method="POST" action="{{ route('register') }}" id="registerForm"
              data-advisers='@json($advisers)'
              data-old-adviser="{{ old('preferred_adviser_id') }}"
              data-old-modules='@json(old('modules', []))'>
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
                <x-form.select id="role" name="role" required>
                    <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                    <option value="adviser" {{ old('role') == 'adviser' ? 'selected' : '' }}>Studies Adviser</option>
                </x-form.select>
                <x-form.error name="role" />
            </div>

            <div class="form-group hidden" id="studentIdField">
                <x-form.label for="student_id">Student ID (Optional)</x-form.label>
                <x-form.input type="text" name="student_id" value="{{ old('student_id') }}" />
            </div>

            <div class="form-group">
                <x-form.label for="phone">Phone Number (Optional)</x-form.label>
                <x-form.input type="text" name="phone" value="{{ old('phone') }}" />
            </div>

            {{-- ── STUDENT: Module picker ─────────────────────────────────────── --}}
            <div class="form-group hidden" id="modulesField">
                <x-form.label for="modules_group">Modules / Areas You Need Help With</x-form.label>
                <input type="hidden" id="modules_group" aria-hidden="true">
                <p class="text-sm text-gray-500 mb-3">
                    Select one Module — the adviser list below will automatically filter to show advisers who specialise in the area.
                </p>
                @foreach ($expertiseList as $expertise)
                    <label class="flex items-center my-1.5 cursor-pointer">
                        <input type="checkbox" class="module-checkbox w-auto mr-2.5" name="modules[]" value="{{ $expertise->id }}"
                            {{ is_array(old('modules')) && in_array($expertise->id, old('modules')) ? 'checked' : '' }}>
                        <span>
                            <strong>{{ $expertise->name }}</strong>
                        </span>
                    </label>
                @endforeach
                <x-form.error name="modules" />
            </div>

            {{-- ── STUDENT: Adviser picker (filtered by selected modules) ────── --}}
            <div class="form-group hidden" id="adviserField">
                <x-form.label for="preferred_adviser_id">Choose Your Adviser (Optional)</x-form.label>
                <p class="text-sm text-gray-500 mb-3" id="adviserHint">
                    Showing all advisers. Select a module above to filter by expertise.
                </p>
                <select id="preferred_adviser_id" name="preferred_adviser_id" class="w-full py-2 px-3 border border-gray-300 rounded-md">
                    <option value="">— No preference —</option>
                    {{-- Options are injected by filterAdvisers() on page load --}}
                </select>
                <x-form.error name="preferred_adviser_id" />
            </div>

            {{-- ── ADVISER: Single module picker ─────────────────────────────── --}}
            <div class="form-group hidden" id="expertiseField">
                <x-form.label for="expertise_id">Module You Teach</x-form.label>
                <x-form.select id="expertise_id" name="expertise_id">
                    <option value="">Select a module</option>
                    @foreach ($expertiseList as $expertise)
                        <option value="{{ $expertise->id }}" {{ old('expertise_id') == $expertise->id ? 'selected' : '' }}>
                            {{ $expertise->name }}
                        </option>
                    @endforeach
                </x-form.select>
                <x-form.error name="expertise_id" />
            </div>

            <button type="submit" class="btn btn-primary w-full">Register</button>

            <p class="text-center mt-6">
                Already have an account? <a href="{{ route('login') }}" class="text-indigo-500">Login here</a>
            </p>
        </form>
    </div>

</x-layout>
