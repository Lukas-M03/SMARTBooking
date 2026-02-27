<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name', 'SMART Booking System') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.css" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="welcome-body">
    <!-- Guest Navbar -->
    @guest
    <nav class="navbar">
        <div class="logo">{{ config('app.name', 'SMART Booking') }}</div>
        <div class="nav-buttons">
            <a href="{{ route('login') }}" class="btn-login">Login</a>
            <a href="{{ route('register') }}" class="btn-register">Register</a>
        </div>
    </nav>
    @endguest

    <!-- Authenticated Navbar -->
    @auth
    <nav class="navbar">
        <div class="logo">SMART Booking</div>
        <nav class="nav-buttons">
            @if(Auth::user()->isStudent())
                <a href="{{ route('student.dashboard') }} " class="btn-nav-page">Dashboard</a>
                <a href="{{ route('bookings.create') }}" class="btn-nav-page">New Booking</a>
                <a href="{{ route('bookings.index') }}" class="btn-nav-page">My Bookings</a>
            @elseif(Auth::user()->isAdviser())
                <a href="{{ route('adviser.dashboard') }}" class="btn-nav-page">Dashboard</a>
                <a href="{{ route('bookings.index') }}" class="btn-nav-page">Bookings</a>
            @elseif(Auth::user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="btn-nav-page">Dashboard</a>
            @endif
            <a href="{{ route('notifications.index') }}" class="btn-nav-page">Notifications</a>
            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn-logout">Logout</button>
            </form>
        </nav>
    </nav>
    @endauth

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <main class="page-content">
            <x-alert />
            {{ $slot }}
        </main>
    </div>

    <footer class="footer">
        <p>&copy; 2025 SMART Booking System. Developed by Lukas Mickevicius. All rights reserved.</p>
    </footer>
    @livewireScripts
</body>
</html>