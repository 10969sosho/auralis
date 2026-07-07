<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name')) - Auralis8</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/app.css">
    @stack('styles')
</head>
<body>

    {{-- Top Bar --}}
    <div class="top-bar">
        <div class="top-bar-inner">
            <div class="top-bar-left">
                <div class="top-bar-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <a href="https://wa.me/60178632188" target="_blank" rel="noopener noreferrer" style="color:inherit;text-decoration:none;">+60 17-863 2188</a>
                </div>
                <div class="top-bar-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span>Mon - Sat: 08:00 - 17:00</span>
                </div>
            </div>
            <div class="top-bar-right">
                <a href="https://www.instagram.com/auralis8.official/" target="_blank" rel="noopener noreferrer" class="top-bar-social" aria-label="Instagram">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                </a>
                <a href="#" class="top-bar-social" aria-label="Facebook">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                </a>
                @auth
                @else
                <a href="{{ route('login') }}" class="top-bar-cta">Sign In</a>
                @endauth
            </div>
        </div>
    </div>

    {{-- Navbar --}}
    <nav class="guest-nav" id="navbar">
        <div class="guest-nav-inner">
            <a href="{{ route('home') }}" class="guest-nav-brand">
                <img src="{{ asset('images/logo.png') }}" alt="Auralis8" class="guest-nav-logo" style="width:32px;height:32px;object-fit:contain;">
                <span class="guest-nav-name">Auralis8</span>
            </a>

            <button class="guest-nav-toggle" id="navToggle" aria-label="Toggle menu">
                <span></span><span></span><span></span>
            </button>

            <div class="guest-nav-collapse" id="navCollapse">
                <div class="guest-nav-menu">
                    {{-- Main navigation — same for ALL users --}}
                    <a href="{{ route('home') }}" class="guest-nav-link" data-translate-en="Home" data-translate-id="Beranda">Home</a>
                    <a href="{{ route('schedules') }}" class="guest-nav-link" data-translate-en="Booking" data-translate-id="Pemesanan">Booking</a>
                    <a href="{{ route('prices') }}" class="guest-nav-link" data-translate-en="Prices" data-translate-id="Harga">Prices</a>
                    <a href="{{ route('announcements') }}" class="guest-nav-link" data-translate-en="Announcements" data-translate-id="Pengumuman">Announcements</a>
                    <a href="{{ route('information') }}" class="guest-nav-link" data-translate-en="About" data-translate-id="Tentang">About</a>
                </div>

                <div class="guest-nav-actions">
                    @auth
                        @php
                            $user = auth()->user();
                            $isBoardingOnly = $user->hasRole('boarding_officer') && !$user->hasRole('admin');
                            $isCounterOnly = $user->hasRole('ticket_counter_officer') && !$user->hasRole('admin');
                            $isSpecialStaff = $isBoardingOnly || $isCounterOnly;
                        @endphp
                        <div class="guest-nav-profile" id="navProfile">
                            <div class="guest-nav-profile-trigger" id="profileTrigger">
                                <div class="guest-nav-avatar">
                                    <span>{{ substr(auth()->user()->name, 0, 1) }}</span>
                                </div>
                                <div class="guest-nav-profile-info">
                                    <span class="guest-nav-profile-name">{{ auth()->user()->name }}</span>
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
                                    <span class="guest-nav-role-badge {{ $roleClass }}">{{ $roleLabel }}</span>
                                </div>
                                <svg class="guest-nav-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"/>
                                </svg>
                            </div>

                            <div class="guest-nav-dropdown" id="navDropdown">
                                {{-- Regular passenger links --}}
                                @if(!$isSpecialStaff)
                                    <a href="{{ route('booking.history') }}" class="guest-nav-dropdown-item">My Bookings</a>
                                    <a href="{{ route('profiles.index') }}" class="guest-nav-dropdown-item">My Passengers</a>
                                    <a href="{{ route('notifications.index') }}" class="guest-nav-dropdown-item">Notifications</a>
                                    <div class="guest-nav-dropdown-divider"></div>
                                @endif

                                {{-- Staff-specific links --}}
                                @if($isCounterOnly)
                                    <a href="{{ route('counter.dashboard') }}" class="guest-nav-dropdown-item">Ticket Counter</a>
                                    <a href="{{ route('counter.history') }}" class="guest-nav-dropdown-item">Counter History</a>
                                    <div class="guest-nav-dropdown-divider"></div>
                                @endif
                                @if($user->hasRole('boarding_officer') || $user->hasRole('admin'))
                                    <a href="{{ route('boarding.scanner') }}" class="guest-nav-dropdown-item">Boarding</a>
                                @endif
                                @if($user->hasRole('ticket_counter_officer') && $user->hasRole('admin'))
                                    <a href="{{ route('counter.dashboard') }}" class="guest-nav-dropdown-item">Ticket Counter</a>
                                @endif
                                @if($user->hasRole('admin'))
                                    <a href="/admin" class="guest-nav-dropdown-item">Admin Panel</a>
                                @endif

                                @if($user->hasRole('boarding_officer') || $user->hasRole('ticket_counter_officer') || $user->hasRole('admin'))
                                <div class="guest-nav-dropdown-divider"></div>
                                @endif

                                {{-- Logout --}}
                                <form action="{{ route('logout') }}" method="POST" class="guest-nav-dropdown-item guest-nav-dropdown-logout">
                                    @csrf
                                    <button type="submit">Logout</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="guest-nav-btn guest-nav-btn-primary">Sign In</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    @section('main_content')
    <main class="main @yield('page_class', 'main-padded')">
        @if(trim($__env->yieldContent('full_width')))
            @yield('content')
        @else
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif
            @yield('content')
        </div>
        @endif
    </main>
    @show

    {{-- Footer --}}
    <footer class="guest-footer">
        <div class="guest-footer-inner">
            <div class="guest-footer-grid">
                <div class="guest-footer-col">
                    <div class="guest-footer-brand">
                        <img src="{{ asset('images/logo.png') }}" alt="Auralis8" style="width:32px;height:32px;object-fit:contain;">
                        <span>Auralis8</span>
                    </div>
                    <p class="guest-footer-desc">
                        Auralis 8 is an international sea transportation company providing ferry services between Lahad Datu, Sabah, Malaysia and Bongao, Tawi-Tawi, Philippines.
                    </p>
                    <div class="guest-footer-social">
                        <a href="https://www.instagram.com/auralis8.official/" target="_blank" rel="noopener noreferrer" class="guest-footer-social-icon" aria-label="Instagram"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg></a>
                        <a href="#" class="guest-footer-social-icon" aria-label="Facebook"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg></a>
                        <a href="#" class="guest-footer-social-icon" aria-label="Twitter"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg></a>
                        <a href="#" class="guest-footer-social-icon" aria-label="YouTube"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58 2.78 2.78 0 0 0 1.94 2C5.12 20 12 20 12 20s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02"/></svg></a>
                    </div>
                </div>
                <div class="guest-footer-col">
                    <h4 class="guest-footer-heading" data-translate-en="Contact Info" data-translate-id="Info Kontak">Contact Info</h4>
                    <ul class="guest-footer-contact">
                        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg><span>Lahad Datu, Sabah, Malaysia</span></li>
                        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg><span>Bongao, Tawi-Tawi, Philippines</span></li>
                    </ul>
                    <p style="font-size:13px;font-weight:600;color:#fff;margin:16px 0 8px;" data-translate-en="Contact Number (WhatsApp only)" data-translate-id="Nomor Kontak (WhatsApp saja)">Contact Number (WhatsApp only)</p>
                    <ul class="guest-footer-contact" style="margin-top:0;">
                        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg><span><a href="https://wa.me/60178349331" target="_blank" rel="noopener noreferrer" style="color:inherit;text-decoration:none;">+60 17-834 9331</a></span></li>
                        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg><span><a href="https://wa.me/60178632188" target="_blank" rel="noopener noreferrer" style="color:inherit;text-decoration:none;">+60 17-863 2188</a></span></li>
                        <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg><span><a href="https://wa.me/60102030488" target="_blank" rel="noopener noreferrer" style="color:inherit;text-decoration:none;">+60 10-203 0488</a></span></li>
                    </ul>
                </div>
                <div class="guest-footer-col">
                    <h4 class="guest-footer-heading" data-translate-en="Navigation" data-translate-id="Navigasi">Navigation</h4>
                    <ul class="guest-footer-links">
                        <li><a href="{{ route('home') }}" data-translate-en="Home" data-translate-id="Beranda">Home</a></li>
                        <li><a href="{{ route('schedules') }}" data-translate-en="Booking" data-translate-id="Pemesanan">Booking</a></li>
                        <li><a href="{{ route('prices') }}" data-translate-en="Prices" data-translate-id="Harga">Prices</a></li>
                        <li><a href="{{ route('announcements') }}" data-translate-en="Announcements" data-translate-id="Pengumuman">Announcements</a></li>
                        <li><a href="{{ route('information') }}" data-translate-en="About" data-translate-id="Tentang">About</a></li>
                    </ul>
                </div>
                <div class="guest-footer-col">
                    <h4 class="guest-footer-heading" data-translate-en="Services" data-translate-id="Layanan">Services</h4>
                    <ul class="guest-footer-links">
                        <li><a href="{{ route('schedules') }}" data-translate-en="Ticket Booking" data-translate-id="Pemesanan Tiket">Ticket Booking</a></li>
                        <li><a href="https://wa.me/60178349331" target="_blank" rel="noopener noreferrer" data-translate-en="Customer Service" data-translate-id="Layanan Pelanggan">Customer Service</a></li>
                        <li><a href="#" data-translate-en="Route Info" data-translate-id="Info Rute">Route Info</a></li>
                        <li><a href="#" data-translate-en="FAQ" data-translate-id="FAQ">FAQ</a></li>
                        <li><a href="#" data-translate-en="Privacy Policy" data-translate-id="Kebijakan Privasi">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="guest-footer-bottom">
            <div class="guest-footer-bottom-inner">
                &copy; {{ date('Y') }} Auralis8. All rights reserved.
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/laravel-echo/1.18.1/echo.iife.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/8.3.0/pusher.min.js"></script>
    @stack('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Navbar toggle
        var toggle = document.getElementById('navToggle');
        var collapse = document.getElementById('navCollapse');
        if (toggle && collapse) {
            toggle.addEventListener('click', function () {
                collapse.classList.toggle('show');
                toggle.classList.toggle('active');
            });
        }

        // Navbar scroll effect
        var nav = document.getElementById('navbar');
        if (nav) {
            window.addEventListener('scroll', function () {
                if (window.scrollY > 50) {
                    nav.classList.add('guest-nav-scrolled');
                } else {
                    nav.classList.remove('guest-nav-scrolled');
                }
            });
        }

        // Profile dropdown
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

        // Echo / Pusher
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

        // Active nav link highlighting
        var currentPath = window.location.pathname;
        var navLinks = document.querySelectorAll('.guest-nav-link');
        navLinks.forEach(function(link) {
            link.classList.remove('active');
            var href = link.getAttribute('href');
            if (href) {
                var linkPath = new URL(href, window.location.origin).pathname;
                if (currentPath === linkPath || (linkPath === '/' && currentPath === '/')) {
                    link.classList.add('active');
                }
            }
        });

        // Smooth scroll to section on home page anchor clicks
        if (currentPath === '/') {
            document.querySelectorAll('.guest-nav-link[href*="#"]').forEach(function(link) {
                link.addEventListener('click', function(e) {
                    var hash = this.getAttribute('href').split('#')[1];
                    if (!hash) return;
                    var target = document.getElementById(hash);
                    if (target) {
                        e.preventDefault();
                        var topBarHeight = document.querySelector('.top-bar') ? document.querySelector('.top-bar').offsetHeight : 50;
                        var navHeight = nav ? nav.offsetHeight : 90;
                        window.scrollTo({
                            top: target.offsetTop - topBarHeight - navHeight,
                            behavior: 'smooth'
                        });
                        var collapse2 = document.getElementById('navCollapse');
                        var toggle2 = document.getElementById('navToggle');
                        if (collapse2) collapse2.classList.remove('show');
                        if (toggle2) toggle2.classList.remove('active');
                    }
                });
            });
        }
    });


    </script>

</body>
</html>
