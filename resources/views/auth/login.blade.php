<x-layout>

    <div class="card" style="max-width: 500px; margin: 80px auto;">
        <h1 class="h1">Login</h1>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <x-form.label for="email">Email Address</x-form.label>
                <x-form.input type="email"  name="email" value="{{ old('email') }}" required autofocus/>
                <x-form.error name="email" />
            </div>

            <div class="form-group">
                <x-form.label for="password">Password</x-form.label>
                <x-form.input type="password"  name="password" required />
                <x-form.error name="password" />
            </div>

            <div class="form-group remember-group">
                <x-form.label for="remember">Remember Me</x-form.label>
                <x-form.toggle name="remember" />
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>

            
        </form>
        <p style="text-align: center; margin-top: 24px;">
                Don't have an account? <a href="{{ route('register') }}" style="color: #667eea;">Register here</a>
            </p>
    </div>

</x-layout>
