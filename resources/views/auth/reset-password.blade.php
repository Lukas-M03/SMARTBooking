<x-layout>
    <div class="login-reference-wrap">
        <section class="login-reference-card" aria-labelledby="new-password-title">
            <h1 id="new-password-title" class="login-reference-title">Choose New Password</h1>

            <form method="POST" action="{{ route('password.update') }}" class="login-reference-form" novalidate>
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="login-field">
                    <x-form.group mode="input" name="email" label="Email" :value="old('email', $email)" type="email"
                        placeholder="Email" autocomplete="email" required autofocus />
                </div>

                <div class="login-field">
                    <x-form.group mode="input" name="password" label="New Password" type="password"
                        placeholder="New Password" autocomplete="new-password" required />
                </div>

                <div class="login-field">
                    <x-form.group mode="input" name="password_confirmation" label="Confirm New Password" type="password"
                        placeholder="Confirm New Password" autocomplete="new-password" required />
                </div>

                <button type="submit" class="login-submit-btn">Reset Password</button>

                <p class="text-center mt-6">
                    <a href="{{ route('login') }}" class="forgot-link">Back to sign in</a>
                </p>
            </form>
        </section>
    </div>
</x-layout>
