@extends('layouts.guest')
@section('title', 'Home')
@section('page_class', 'guest-page')
@section('full_width', true)

@push('styles')
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
@endpush

@section('content')

{{-- Hero Section --}}
<section class="guest-hero" id="home">
    <div class="guest-hero-bg"></div>
    <div class="guest-hero-overlay"></div>
    <div class="guest-hero-content" data-aos="fade-up" data-aos-duration="1000">
        <p class="guest-hero-subtitle">SHIPTICKETING — INTERNATIONAL FERRY</p>
        <h1 class="guest-hero-title">Let's Explore Together</h1>
        <p class="guest-hero-hashtag">#connectingthousandsislands</p>
        <a href="{{ route('schedules') }}" class="guest-hero-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            Book Ticket
        </a>
    </div>
</section>

{{-- Features Section --}}
<section class="guest-section guest-section-bg-white" id="features">
    <div class="guest-container">
        <p class="guest-section-label" data-aos="fade-up" data-aos-duration="800">Our Services</p>
        <h2 class="guest-section-title" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">Why Choose Us?</h2>
        <p class="guest-section-subtitle" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">We are committed to providing the best sea travel experience with safety, comfort, and affordable prices.</p>
        <div class="features-grid">
            <div class="feature-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
                </div>
                <h3 class="feature-title">Safety</h3>
                <p class="feature-desc">Our vessels are equipped with modern safety equipment and manned by professional, experienced crew members.</p>
            </div>
            <div class="feature-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <h3 class="feature-title">Save Your Time</h3>
                <p class="feature-desc">Fast and easy online ticket booking. Just a few minutes, your ticket is ready without queuing.</p>
            </div>
            <div class="feature-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="300">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1v22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <h3 class="feature-title">Best Price</h3>
                <p class="feature-desc">Enjoy the best prices for every shipping route with a variety of class options to suit your needs.</p>
            </div>
        </div>
    </div>
</section>

{{-- Company Story Section --}}
<section class="guest-section guest-section-bg-light" id="about">
    <div class="story-wrapper">
        <div class="story-image" data-aos="fade-right" data-aos-duration="1000">
            <img src="{{ asset('images/ferry-story.jpeg') }}" alt="Ferry">
        </div>
        <div class="story-content" data-aos="fade-left" data-aos-duration="1000">
            <p class="story-label">WHO WE ARE</p>
            <h2 class="story-title">Company Story</h2>
            <p class="story-text">
                ShipTicketing is a leading shipping company serving international Malaysia - Philippines routes since 2010. We are committed to providing safe, comfortable, and punctual sea transportation services for all passengers.
            </p>
            <div class="story-stat-grid">
                <div class="story-stat-item" data-aos="zoom-in" data-aos-duration="600" data-aos-delay="100">
                    <span class="story-stat-number">35+</span>
                    <span class="story-stat-label">Total Fleet</span>
                </div>
                <div class="story-stat-item" data-aos="zoom-in" data-aos-duration="600" data-aos-delay="200">
                    <span class="story-stat-number">4</span>
                    <span class="story-stat-label">Branch Offices</span>
                </div>
                <div class="story-stat-item" data-aos="zoom-in" data-aos-duration="600" data-aos-delay="300">
                    <span class="story-stat-number">37+</span>
                    <span class="story-stat-label">Routes Served</span>
                </div>
            </div>
            <div style="margin-top:32px;" data-aos="fade-up" data-aos-duration="800" data-aos-delay="400">
                <a href="{{ route('informasi') }}" class="guest-hero-btn" style="display:inline-flex;padding:14px 32px;font-size:15px;">
                    Learn More →
                </a>
            </div>
        </div>
    </div>
</section>

{{-- Announcement Section --}}
<section class="guest-section guest-section-bg-white" id="announcement">
    <div class="guest-container">
        <p class="guest-section-label" data-aos="fade-up" data-aos-duration="800">Announcements</p>
        <h2 class="guest-section-title" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">News & Announcements</h2>
        <p class="guest-section-subtitle" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">Get the latest information about sailing schedules, ticket promos, and other important announcements.</p>
        <div class="announce-grid">
            <div class="announce-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">
                <div class="announce-date">June 15, 2026</div>
                <h3 class="announce-title">New Schedule Route Bongao - Lahad Datu</h3>
                <p class="announce-excerpt">Starting July 1, 2026, we will add sailing schedules to 3 times daily for the Bongao - Lahad Datu route.</p>
                <a href="{{ route('pengumuman.detail', 1) }}" class="announce-link">Read More →</a>
            </div>
            <div class="announce-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
                <div class="announce-date">June 10, 2026</div>
                <h3 class="announce-title">Special Independence Month Ticket Promo</h3>
                <p class="announce-excerpt">Enjoy up to 20% discount on all routes during July - August 2026 in celebration of Independence Day.</p>
                <a href="{{ route('pengumuman.detail', 2) }}" class="announce-link">Read More →</a>
            </div>
            <div class="announce-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="300">
                <div class="announce-date">June 5, 2026</div>
                <h3 class="announce-title">Routine Maintenance MV Auralis 8</h3>
                <p class="announce-excerpt">MV Auralis 8 will undergo routine maintenance on June 20-25, 2026. Schedules will be diverted to other vessels.</p>
                <a href="{{ route('pengumuman.detail', 3) }}" class="announce-link">Read More →</a>
            </div>
        </div>
        <div class="text-center" style="margin-top:48px;" data-aos="fade-up" data-aos-duration="800">
            <a href="{{ route('pengumuman') }}" class="announce-link">See All Announcements →</a>
        </div>
    </div>
</section>

{{-- Schedule Section (Blue Gradient) --}}
<section class="guest-section schedule-section" id="schedule">
    <div class="guest-container">
        <p class="guest-section-label" style="color:rgba(255,255,255,0.7);" data-aos="fade-up" data-aos-duration="800">Ship Schedule</p>
        <h2 class="guest-section-title" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">Ferry Schedule</h2>
        <p class="guest-section-subtitle" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">Browse our latest ferry schedules for Malaysia - Philippines routes and beyond.</p>
        <div class="schedule-grid">
            <div class="schedule-card" data-aos="flip-up" data-aos-duration="800" data-aos-delay="100">
                <div class="schedule-route">Bongao → Lahad Datu</div>
                <div class="schedule-detail">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span>Departure: <span class="schedule-time">08:00 WITA</span></span>
                </div>
                <div class="schedule-detail">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span>Arrival: <span class="schedule-time">11:30 WITA</span></span>
                </div>
                <div class="schedule-detail">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span>Vessel: <span class="schedule-time">MV Auralis 8</span></span>
                </div>
            </div>
            <div class="schedule-card" data-aos="flip-up" data-aos-duration="800" data-aos-delay="200">
                <div class="schedule-route">Lahad Datu → Bongao</div>
                <div class="schedule-detail">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span>Departure: <span class="schedule-time">13:00 WITA</span></span>
                </div>
                <div class="schedule-detail">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span>Arrival: <span class="schedule-time">16:30 WITA</span></span>
                </div>
                <div class="schedule-detail">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span>Vessel: <span class="schedule-time">MV Auralis 8</span></span>
                </div>
            </div>
            <div class="schedule-card" data-aos="flip-up" data-aos-duration="800" data-aos-delay="300">
                <div class="schedule-route">Bongao → Sandakan</div>
                <div class="schedule-detail">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span>Departure: <span class="schedule-time">09:00 WITA</span></span>
                </div>
                <div class="schedule-detail">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span>Arrival: <span class="schedule-time">14:00 WITA</span></span>
                </div>
                <div class="schedule-detail">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span>Vessel: <span class="schedule-time">MV Auralis 8</span></span>
                </div>
            </div>
        </div>
        <div class="text-center" style="margin-top:48px;" data-aos="fade-up" data-aos-duration="800">
            <a href="{{ route('jadwal') }}" style="display:inline-flex;align-items:center;gap:8px;padding:14px 35px;font-size:16px;font-weight:600;color:#fff;background:rgba(255,255,255,0.15);border:2px solid rgba(255,255,255,0.3);border-radius:5px;text-decoration:none;transition:all 0.2s;">
                View Full Schedule →
            </a>
        </div>
    </div>
</section>

{{-- Booking Section --}}
<section class="guest-section booking-section" id="booking">
    <div class="guest-container">
        <p class="guest-section-label" data-aos="fade-up" data-aos-duration="800">Ticket Booking</p>
        <h2 class="guest-section-title" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">Book Ferry Ticket</h2>
        <p class="guest-section-subtitle" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">Fill in the details below to start booking your ferry ticket.</p>
        <div class="booking-box" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="300">
            <form action="{{ route('schedules') }}" method="GET">
                <div class="booking-form-grid">
                    <div class="booking-form-full">
                        <label class="booking-label">Departure Date</label>
                        <input type="date" name="date" class="booking-input" required>
                    </div>
                    <div>
                        <label class="booking-label">Origin Port</label>
                        <select name="from" class="booking-input" required>
                            <option value="">Select Port</option>
                            <option value="Bongao">Bongao, Tawi-Tawi</option>
                            <option value="Lahad Datu">Lahad Datu, Sabah</option>
                            <option value="Sandakan">Sandakan, Sabah</option>
                        </select>
                    </div>
                    <div>
                        <label class="booking-label">Destination Port</label>
                        <select name="to" class="booking-input" required>
                            <option value="">Select Port</option>
                            <option value="Lahad Datu">Lahad Datu, Sabah</option>
                            <option value="Bongao">Bongao, Tawi-Tawi</option>
                            <option value="Sandakan">Sandakan, Sabah</option>
                        </select>
                    </div>
                    <div>
                        <label class="booking-label">Adults</label>
                        <div class="booking-counter-group">
                            <button type="button" class="booking-counter-btn" onclick="adjustCounter('adult', -1)">−</button>
                            <span class="booking-counter-value" id="adultCount">1</span>
                            <button type="button" class="booking-counter-btn" onclick="adjustCounter('adult', 1)">+</button>
                        </div>
                        <input type="hidden" name="adult" id="adultInput" value="1">
                    </div>
                    <div>
                        <label class="booking-label">Children</label>
                        <div class="booking-counter-group">
                            <button type="button" class="booking-counter-btn" onclick="adjustCounter('child', -1)">−</button>
                            <span class="booking-counter-value" id="childCount">0</span>
                            <button type="button" class="booking-counter-btn" onclick="adjustCounter('child', 1)">+</button>
                        </div>
                        <input type="hidden" name="child" id="childInput" value="0">
                    </div>
                    <button type="submit" class="booking-submit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        Search Tickets
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

{{-- Price Section --}}
<section class="guest-section guest-section-bg-light" id="price">
    <div class="guest-container">
        <p class="guest-section-label" data-aos="fade-up" data-aos-duration="800">Price List</p>
        <h2 class="guest-section-title" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">Ticket Prices</h2>
        <p class="guest-section-subtitle" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">Ticket price information for every shipping route we serve.</p>
        <div class="price-layout" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="300">
            <div class="price-sidebar">
                <div class="price-sidebar-title">Province Category</div>
                <div class="price-sidebar-item active">Tawi-Tawi</div>
                <div class="price-sidebar-item">Sabah</div>
            </div>
            <div class="price-content">
                <table class="price-table">
                    <thead>
                        <tr>
                            <th>Route</th>
                            <th>Class</th>
                            <th>Price (MYR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($prices as $schedule)
                        <tr>
                            <td>{{ $schedule->route->origin_port }} → {{ $schedule->route->destination_port }}</td>
                            <td>VIP</td>
                            <td class="price-amount">MYR {{ number_format($schedule->vip_price, 2) }}</td>
                        </tr>
                        <tr>
                            <td>{{ $schedule->route->origin_port }} → {{ $schedule->route->destination_port }}</td>
                            <td>Regular</td>
                            <td class="price-amount">MYR {{ number_format($schedule->regular_price, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="text-align:center;color:#6C757D;padding:40px;">
                                No price data available yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="price-view-all" data-aos="fade-up" data-aos-duration="800" data-aos-delay="400">
            <a href="{{ route('harga') }}">View All Routes & Prices →</a>
        </div>
    </div>
</section>

@push('scripts')
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
AOS.init({
    once: false,
    mirror: true,
    duration: 800,
    easing: 'ease-in-out',
    disable: 'mobile',
});
</script>
@endpush

<script>
function adjustCounter(type, delta) {
    var countEl = document.getElementById(type + 'Count');
    var inputEl = document.getElementById(type + 'Input');
    var current = parseInt(countEl.textContent);
    var newVal = Math.max(type === 'adult' ? 1 : 0, current + delta);
    countEl.textContent = newVal;
    inputEl.value = newVal;
}

// Price sidebar filter
document.addEventListener('DOMContentLoaded', function() {
    const sidebarItems = document.querySelectorAll('#price .price-sidebar-item');
    const rows = document.querySelectorAll('#price .price-table tbody tr');
    const rowGroups = [];
    for (let i = 0; i < rows.length; i += 2) {
        rowGroups.push([rows[i], rows[i + 1]]);
    }
    sidebarItems.forEach(function(item) {
        item.addEventListener('click', function() {
            sidebarItems.forEach(el => el.classList.remove('active'));
            this.classList.add('active');
            const province = this.textContent.trim().toLowerCase();
            rowGroups.forEach(function(group) {
                const firstRow = group[0];
                if (!firstRow) return;
                const routeCell = firstRow.cells[0];
                const routeText = routeCell ? routeCell.textContent.trim().toLowerCase() : '';
                if (province === '' || routeText.includes(province)) {
                    group.forEach(r => { if (r) r.style.display = ''; });
                } else {
                    group.forEach(r => { if (r) r.style.display = 'none'; });
                }
            });
        });
    });
});
</script>

@endsection
