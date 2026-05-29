<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name')) - Ship Ticketing</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>

    <nav class="nav" id="navbar">
        <div class="nav-inner">

            <a href="{{ route('home') }}" class="nav-brand">
                <svg class="nav-brand-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 21h20M6 18l2-6h8l2 6M9 12V7M15 12V7M12 7V3"/>
                    <path d="M5 7h14l-2 5H7L5 7Z"/>
                    <circle cx="12" cy="7" r="1.5"/>
                </svg>
                <span class="nav-brand-text">
                    <span class="nav-brand-name">ShipTicketing</span>
                    <span class="nav-brand-sub">International Ferry</span>
                </span>
            </a>

            <button class="nav-toggle" id="navToggle" aria-label="Toggle menu">
                <span></span><span></span><span></span>
            </button>

            <div class="nav-collapse" id="navCollapse">

                <div class="nav-menu" id="navMenu">
                    @auth
                        @php
                            $user = auth()->user();
                            $isBoardingOnly = $user->hasRole('boarding_officer') && !$user->hasRole('admin');
                            $isCounterOnly = $user->hasRole('ticket_counter_officer') && !$user->hasRole('admin');
                            $isSpecialStaff = $isBoardingOnly || $isCounterOnly;
                        @endphp

                        @if(!$isSpecialStaff)
                            <a href="{{ route('schedules') }}" class="nav-link">Search</a>
                            <a href="{{ route('seat-availability') }}" class="nav-link">Seat Availability</a>
                            <a href="{{ route('booking.history') }}" class="nav-link">My Bookings</a>
                            <a href="{{ route('profiles.index') }}" class="nav-link">My Passengers</a>
                        @endif

                        @if($isCounterOnly)
                            <a href="{{ route('counter.dashboard') }}" class="nav-link">Ticket Counter</a>
                            <a href="{{ route('counter.search', ['query' => '']) }}" class="nav-link">Find Booking</a>
                        @endif

                        @if($user->hasRole('boarding_officer') || $user->hasRole('admin'))
                            <a href="{{ route('boarding.scanner') }}" class="nav-link">Boarding</a>
                        @endif
                        @if($user->hasRole('ticket_counter_officer') && $user->hasRole('admin'))
                            <a href="{{ route('counter.dashboard') }}" class="nav-link">Ticket Counter</a>
                        @endif
                        @if($user->hasRole('admin'))
                            <a href="/admin" class="nav-link">Admin</a>
                        @endif
                    @else
                        <a href="{{ route('schedules') }}" class="nav-link">Search Schedule</a>
                    @endauth
                </div>

                <div class="nav-actions">
                    @auth
                        <div class="nav-profile" id="navProfile">
                            <div class="nav-profile-trigger" id="profileTrigger">
                                <div class="nav-avatar">
                                    <span>{{ substr(auth()->user()->name, 0, 1) }}</span>
                                </div>
                                <div class="nav-profile-info">
                                    <span class="nav-profile-name">{{ auth()->user()->name }}</span>
                                    @php
                                        $role = auth()->user()->getRoleNames()->first() ?? 'passenger';
                                        $roleClass = match($role) {
                                            'admin' => 'badge-admin',
                                            'boarding_officer' => 'badge-boarding',
                                            'deportation_officer' => 'badge-deportation',
                                            'ticket_counter_officer' => 'badge-counter',
                                            default => 'badge-passenger',
                                        };
                                        $roleLabel = match($role) {
                                            'admin' => 'Admin',
                                            'boarding_officer' => 'Boarding',
                                            'deportation_officer' => 'Deportation',
                                            'ticket_counter_officer' => 'Counter',
                                            default => 'Passenger',
                                        };
                                    @endphp
                                    <span class="nav-role-badge {{ $roleClass }}">{{ $roleLabel }}</span>
                                </div>
                                <svg class="nav-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"/>
                                </svg>
                            </div>

                            <div class="nav-dropdown" id="navDropdown">
                                @if($isSpecialStaff)
                                    <form action="{{ route('logout') }}" method="POST" class="nav-dropdown-item nav-dropdown-logout">
                                        @csrf
                                        <button type="submit">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                            Logout
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('booking.history') }}" class="nav-dropdown-item">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                        My Bookings
                                    </a>
                                    <a href="{{ route('profiles.index') }}" class="nav-dropdown-item">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                        My Passengers
                                    </a>
                                    <a href="{{ route('notifications.index') }}" class="nav-dropdown-item">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                                        Notifications
                                    </a>
                                    @if(auth()->user()->hasRole('admin'))
                                    <a href="/admin" class="nav-dropdown-item">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                                        Admin Panel
                                    </a>
                                    @endif
                                    <div class="nav-dropdown-divider"></div>
                                    <form action="{{ route('logout') }}" method="POST" class="nav-dropdown-item nav-dropdown-logout">
                                        @csrf
                                        <button type="submit">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                            Logout
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="nav-btn nav-btn-outline">Login</a>
                    @endauth
                </div>

            </div>

        </div>
    </nav>

    <main class="main @yield('page_class', 'main-padded')">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif
            @yield('content')
        </div>
    </main>

    <footer class="footer">
        &copy; {{ date('Y') }} Ship Ticketing. All rights reserved.
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.18.1/echo.iife.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/8.3.0/pusher.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {

        var toggle = document.getElementById('navToggle');
        var collapse = document.getElementById('navCollapse');
        if (toggle && collapse) {
            toggle.addEventListener('click', function () {
                collapse.classList.toggle('show');
                toggle.classList.toggle('active');
            });
        }

        var trigger = document.getElementById('profileTrigger');
        var dropdown = document.getElementById('navDropdown');
        if (trigger && dropdown) {
            trigger.addEventListener('click', function (e) {
                e.stopPropagation();
                dropdown.classList.toggle('show');
            });
            document.addEventListener('click', function () {
                dropdown.classList.remove('show');
            });
            dropdown.addEventListener('click', function (e) {
                e.stopPropagation();
            });
        }

        if (typeof Pusher !== 'undefined' && typeof Echo === 'undefined') {
            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: '{{ env("PUSHER_APP_KEY") }}',
                wsHost: '{{ env("PUSHER_HOST") }}',
                wsPort: {{ env("PUSHER_PORT", 6001) }},
                forceTLS: false,
                disableStats: true,
                enabledTransports: ['ws', 'wss'],
            });
        }
    });
    </script>

</body>
</html>
