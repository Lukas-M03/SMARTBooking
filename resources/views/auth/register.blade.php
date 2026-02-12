<x-layout>
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

            <div class="form-group" id="expertiseField" style="display: none;">
                <x-form.label for="expertise">Areas of Expertise</x-form.label>
                @foreach ($expertiseList as $expertise)
                    <label style="display: flex; align-items: center; margin: 0.5rem 0;">
                        <input type="checkbox" name="expertise[]" value="{{ $expertise->id }}"
                            style="width: auto; margin-right: 0.5rem;">
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
        function toggleRoleFields() {
            const role = document.getElementById('role').value;
            const studentIdField = document.getElementById('studentIdField');
            const expertiseField = document.getElementById('expertiseField');

            if (role === 'student') {
                studentIdField.style.display = 'block';
                expertiseField.style.display = 'none';
            } else if (role === 'adviser') {
                studentIdField.style.display = 'none';
                expertiseField.style.display = 'block';
            } else {
                studentIdField.style.display = 'none';
                expertiseField.style.display = 'none';
            }
        }

        // Call on page load if role is already selected
        document.addEventListener('DOMContentLoaded', function() {
            toggleRoleFields();
        });
    </script>
</x-layout>
