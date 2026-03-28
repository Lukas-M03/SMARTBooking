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
            <a href="{{ url('/') }}" class="logo">{{ config('app.name', 'SMART Booking') }}</a>
            <button type="button" class="nav-toggle js-nav-toggle" aria-expanded="false" aria-controls="guest-nav-menu"
                data-target="guest-nav-menu">
                Menu
            </button>
            <div class="nav-utility-links">
                <a href="{{ route('login') }}" class="nav-link">Log In</a>
                <a href="{{ route('register') }}" class="btn-pill-dark">Register</a>
            </div>
            </div>
        </nav>
    @endguest

    <!-- Authenticated Navbar -->
    @auth
        <nav class="navbar">
            <a href="{{ url('/') }}" class="logo">SMART Booking</a>

            <button type="button" class="nav-toggle js-nav-toggle" aria-expanded="false" aria-controls="auth-nav-menu"
                data-target="auth-nav-menu">
                Menu
            </button>
            <div class="nav-buttons" id="auth-nav-menu">
                <div class="nav-main-links">
                    @if (Auth::user()->isStudent())
                        <a href="{{ route('student.dashboard') }}" class="nav-link">Dashboard</a>
                        <a href="{{ route('bookings.create') }}" class="nav-link">New Booking</a>
                        <a href="{{ route('bookings.index') }}" class="nav-link">My Bookings</a>
                    @elseif(Auth::user()->isAdviser())
                        <a href="{{ route('adviser.dashboard') }}" class="nav-link">Dashboard</a>
                        <a href="{{ route('bookings.index') }}" class="nav-link">Bookings</a>
                        <a href="{{ route('bookings.index', ['status' => 'completed']) }}" class="nav-link">Completed Meetings</a>
                    @elseif(Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="nav-link">Dashboard</a>
                    @endif
                </div>

                <div class="nav-utility-links">
                    @if (Auth::user()->hasMicrosoftToken())
                        <a href="{{ route('microsoft.disconnect') }}" class="outlook-link-disconnect">Disconnect Outlook</a>
                    @else
                        <a href="{{ route('microsoft.redirect') }}" class="outlook-link">Connect Outlook</a>
                    @endif

                    <a href="{{ route('notifications.index') }}" class="nav-link">Notifications</a>

                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="btn-pill-dark">Logout</button>
                    </form>
                </div>
            </div>
        </nav>
    @endauth

    <div class="container">
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
