<x-layout>
    <div class="login-reference-wrap">
        <section class="login-reference-card" aria-labelledby="login-title">
            <h1 id="login-title" class="login-reference-title">Sign in to SMART Booking</h1>

            <form method="POST" action="{{ route('login') }}" class="login-reference-form" novalidate>
                @csrf

                <div class="login-field">
                    <x-form.group mode="input" name="email" label="Email" labelClass="login-visually-hidden"
                        :value="old('email')" type="email" placeholder="Email" autocomplete="email" required autofocus />
                </div>

                <div class="login-field login-field-password">
                    <x-form.group mode="input" name="password" label="Password" labelClass="login-visually-hidden"
                        type="password" placeholder="Password" autocomplete="current-password" required />
                </div>

                <div class="login-options-row">
                    <label for="remember" class="remember-option">
                        <input id="remember" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <x-form.label for="remember" class="remember-text">Remember me</x-form.label>
                    </label>

                    <a href="{{ route('password.request') }}" class="forgot-link">Reset Password</a>
                </div>

                <button type="submit" class="login-submit-btn">Sign in</button>
                <p class="text-center mt-6">
                    Not registered? <a href="{{ route('register') }}" class="forgot-link">Create an account</a>
                </p>
            </form>
        </section>
    </div>

    <style>
        @media (max-width: 640px) {
            .login-reference-wrap {
                min-height: auto;
                padding-top: 28px;
                padding-bottom: 20px;
            }

            .login-reference-card {
                padding: 34px 22px 24px;
                border-radius: 16px;
            }

            .login-reference-title {
                font-size: 34px;
                margin-bottom: 24px;
            }

            .login-submit-btn {
                font-size: 20px;
            }
        }
    </style>
</x-layout>
