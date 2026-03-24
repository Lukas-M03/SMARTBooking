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
            <button type="button" class="nav-toggle js-nav-toggle" aria-expanded="false" aria-controls="guest-nav-menu"
                data-target="guest-nav-menu">
                Menu
            </button>
            <div class="nav-buttons" id="guest-nav-menu">
                <div class="nav-main-links">
                    <a href="{{ route('register') }}" class="nav-link">Features</a>
                    <div class="nav-item-mega">
                        <button type="button" class="nav-link nav-link-with-caret js-mega-toggle" aria-expanded="false">
                            Resources
                        </button>
                        <div class="mega-menu">
                            <div class="mega-col">
                                <p class="mega-col-title">Industry Insights</p>
                                <a href="{{ route('register') }}" class="mega-link">Student Workflows</a>
                                <a href="{{ route('register') }}" class="mega-link">Adviser Playbooks</a>
                                <a href="{{ route('register') }}" class="mega-link">Changelog</a>
                                <a href="{{ route('register') }}" class="mega-link">How-To Tutorials</a>
                            </div>
                            <div class="mega-col">
                                <p class="mega-col-title">SMART With</p>
                                <a href="{{ route('register') }}" class="mega-link">Outlook Calendar</a>
                                <a href="{{ route('register') }}" class="mega-link">Live Notifications</a>
                                <a href="{{ route('register') }}" class="mega-link">Adviser Matching</a>
                                <a href="{{ route('register') }}" class="mega-link">Availability Sync</a>
                            </div>
                            <div class="mega-col">
                                <p class="mega-col-title">For Developers</p>
                                <a href="{{ route('register') }}" class="mega-link">API Reference</a>
                                <a href="{{ route('register') }}" class="mega-link">Webhooks</a>
                                <a href="{{ route('register') }}" class="mega-link">Data Migration</a>
                                <a href="{{ route('register') }}" class="mega-link">Troubleshooting</a>
                            </div>
                            <a href="{{ route('register') }}" class="mega-card">
                                <x-svg icon="calendar-days" size="lg" class="mega-card-icon" />
                                <span class="mega-card-title">Product Demo</span>
                                <span class="mega-card-sub">See SMART Booking in 3 minutes</span>
                            </a>
                        </div>
                    </div>
                    <a href="{{ route('register') }}" class="nav-link">Pricing</a>
                    <a href="{{ route('register') }}" class="nav-link">Download</a>
                </div>
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
            <div class="logo">SMART Booking</div>

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
                    @elseif(Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="nav-link">Dashboard</a>
                    @endif

                    <div class="nav-item-mega">
                        <button type="button" class="nav-link nav-link-with-caret js-mega-toggle" aria-expanded="false">
                            Resources
                        </button>
                        <div class="mega-menu">
                            <div class="mega-col">
                                <p class="mega-col-title">Industry Insights</p>
                                <a href="{{ route('notifications.index') }}" class="mega-link">Notifications Guide</a>
                                <a href="{{ route('bookings.index') }}" class="mega-link">Booking Best Practices</a>
                                <a href="{{ route('bookings.index') }}" class="mega-link">Adviser Response Tips</a>
                                <a href="{{ route('bookings.index') }}" class="mega-link">Student FAQ</a>
                            </div>
                            <div class="mega-col">
                                <p class="mega-col-title">SMART With</p>
                                <a href="{{ route('bookings.index') }}" class="mega-link">Calendar Sync</a>
                                <a href="{{ route('bookings.index') }}" class="mega-link">Role-based Access</a>
                                <a href="{{ route('bookings.index') }}" class="mega-link">Live Alerts</a>
                                <a href="{{ route('bookings.index') }}" class="mega-link">Data Exports</a>
                            </div>
                            <div class="mega-col">
                                <p class="mega-col-title">For Developers</p>
                                <a href="{{ route('bookings.index') }}" class="mega-link">Routes Overview</a>
                                <a href="{{ route('bookings.index') }}" class="mega-link">Component Library</a>
                                <a href="{{ route('bookings.index') }}" class="mega-link">Webhooks</a>
                                <a href="{{ route('bookings.index') }}" class="mega-link">Troubleshooting</a>
                            </div>
                            <a href="{{ route('bookings.index') }}" class="mega-card">
                                <x-svg icon="calendar-days" size="lg" class="mega-card-icon" />
                                <span class="mega-card-title">Video Tutorials</span>
                                <span class="mega-card-sub">Short walkthroughs for teams</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="nav-utility-links">
                    @if(Auth::user()->hasMicrosoftToken())
                        <a href="{{ route('microsoft.disconnect') }}" class="outlook-link">Disconnect Outlook</a>
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
