<x-layout>
    <div class="login-reference-wrap">
        <section class="login-reference-card" aria-labelledby="forgot-password-title">
            <h1 id="forgot-password-title" class="login-reference-title">Confirm Email</h1>

            <p class="text-sm text-gray-600 mb-6">
                Enter your account email to confirm your account and continue to reset your password.
            </p>

            <form method="POST" action="{{ route('password.email') }}" class="login-reference-form" novalidate>
                @csrf

                <div class="login-field">
                    <x-form.group mode="input" name="email" label="Email" :value="old('email')" type="email"
                        placeholder="Email" autocomplete="email" required autofocus />
                </div>

                <button type="submit" class="login-submit-btn">Continue</button>

                <p class="text-center mt-6">
                    <a href="{{ route('login') }}" class="forgot-link">Back to sign in</a>
                </p>
            </form>
        </section>
    </div>
</x-layout>
