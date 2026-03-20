<x-layout>

    <div class="card max-w-md mx-auto my-20">
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

            <button type="submit" class="btn btn-primary w-full">Login</button>

            
        </form>
        <p class="text-center mt-6">
            Don't have an account? <a href="{{ route('register') }}" class="text-indigo-500">Register here</a>
            </p>
    </div>

</x-layout>
