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
        <p class="guest-hero-subtitle" data-translate-en="AURALIS8 — INTERNATIONAL FERRY" data-translate-id="AURALIS8 — FERI INTERNASIONAL">AURALIS8 — INTERNATIONAL FERRY</p>
        <h1 class="guest-hero-title" data-translate-en="Let's Explore Together" data-translate-id="Mari Jelajahi Bersama">Let's Explore Together</h1>
        <p class="guest-hero-hashtag" data-translate-en="#connectingthousandsislands" data-translate-id="#menghubungkanratusanpulau">#connectingthousandsislands</p>
        <a href="{{ route('schedules') }}" class="guest-hero-btn" data-translate-en="Book Ticket" data-translate-id="Pesan Tiket">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            Book Ticket
        </a>
    </div>
</section>

{{-- Features Section --}}
<section class="guest-section guest-section-bg-white" id="features">
    <div class="guest-container">
        <p class="guest-section-label" data-aos="fade-up" data-aos-duration="800" data-translate-en="Our Services" data-translate-id="Layanan Kami">Our Services</p>
        <h2 class="guest-section-title" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100" data-translate-en="Why Choose Us?" data-translate-id="Mengapa Memilih Kami?">Why Choose Us?</h2>
        <p class="guest-section-subtitle" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200" data-translate-en="We are committed to providing the best sea travel experience with safety, comfort, and affordable prices." data-translate-id="Kami berkomitmen untuk memberikan pengalaman perjalanan laut terbaik dengan keselamatan, kenyamanan, dan harga terjangkau.">We are committed to providing the best sea travel experience with safety, comfort, and affordable prices.</p>
        <div class="features-grid">
            <div class="feature-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
                </div>
                <h3 class="feature-title" data-translate-en="Safety" data-translate-id="Keamanan">Safety</h3>
                <p class="feature-desc" data-translate-en="Our vessels are equipped with modern safety equipment and manned by professional, experienced crew members." data-translate-id="Kapal kami dilengkapi dengan peralatan keselamatan modern dan diawaki oleh kru profesional dan berpengalaman.">Our vessels are equipped with modern safety equipment and manned by professional, experienced crew members.</p>
            </div>
            <div class="feature-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <h3 class="feature-title" data-translate-en="Save Your Time" data-translate-id="Hemat Waktu Anda">Save Your Time</h3>
                <p class="feature-desc" data-translate-en="Fast and easy online ticket booking. Just a few minutes, your ticket is ready without queuing." data-translate-id="Pemesanan tiket online dengan cepat dan mudah. Hanya beberapa menit, tiket Anda siap tanpa mengantre.">Fast and easy online ticket booking. Just a few minutes, your ticket is ready without queuing.</p>
            </div>
            <div class="feature-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="300">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1v22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <h3 class="feature-title" data-translate-en="Best Price" data-translate-id="Harga Terbaik">Best Price</h3>
                <p class="feature-desc" data-translate-en="Enjoy the best prices for every shipping route with a variety of class options to suit your needs." data-translate-id="Nikmati harga terbaik untuk setiap rute pelayaran dengan berbagai pilihan kelas sesuai kebutuhan Anda.">Enjoy the best prices for every shipping route with a variety of class options to suit your needs.</p>
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
            <p class="story-label" data-translate-en="WHO WE ARE" data-translate-id="SIAPA KAMI">WHO WE ARE</p>
            <h2 class="story-title" data-translate-en="Company Story" data-translate-id="Tentang Perusahaan">Company Story</h2>
            <p class="story-text" data-translate-en="Auralis 8 is an international sea transportation company providing ferry services between Lahad Datu, Sabah, Malaysia and Bongao, Tawi-Tawi, Philippines. We serve as a trusted link for cross-border mobility by prioritizing safety, comfort, reliability, and professional service." data-translate-id="Auralis 8 adalah perusahaan transportasi laut internasional yang menyediakan layanan feri antara Lahad Datu, Sabah, Malaysia dan Bongao, Tawi-Tawi, Filipina. Kami menjadi penghubung tepercaya untuk mobilitas lintas batas dengan mengutamakan keselamatan, kenyamanan, keandalan, dan pelayanan profesional.">
                Auralis 8 is an international sea transportation company providing ferry services between Lahad Datu, Sabah, Malaysia and Bongao, Tawi-Tawi, Philippines. We serve as a trusted link for cross-border mobility by prioritizing safety, comfort, reliability, and professional service.
            </p>
            <div style="margin-top:32px;" data-aos="fade-up" data-aos-duration="800" data-aos-delay="400">
                <a href="{{ route('information') }}" class="guest-hero-btn" style="display:inline-flex;padding:14px 32px;font-size:15px;" data-translate-en="Learn More →" data-translate-id="Pelajari Lebih Lanjut →">
                    Learn More →
                </a>
            </div>
        </div>
    </div>
</section>

{{-- Announcement Section --}}
<section class="guest-section guest-section-bg-white" id="announcement">
    <div class="guest-container">
        <p class="guest-section-label" data-aos="fade-up" data-aos-duration="800" data-translate-en="Announcements" data-translate-id="Pengumuman">Announcements</p>
        <h2 class="guest-section-title" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100" data-translate-en="News &amp; Announcements" data-translate-id="Berita &amp; Pengumuman">News &amp; Announcements</h2>
        <p class="guest-section-subtitle" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200" data-translate-en="Get the latest information about sailing schedules, ticket promos, and other important announcements." data-translate-id="Dapatkan informasi terbaru tentang jadwal keberangkatan, promo tiket, dan pengumuman penting lainnya.">Get the latest information about sailing schedules, ticket promos, and other important announcements.</p>
        <div class="announce-grid">
            <div class="announce-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">
                <div class="announce-date" data-translate-en="June 15, 2026" data-translate-id="15 Juni 2026">June 15, 2026</div>
                <h3 class="announce-title" data-translate-en="New Schedule Route Bongao - Lahad Datu" data-translate-id="Jadwal Baru Rute Bongao - Lahad Datu">New Schedule Route Bongao - Lahad Datu</h3>
                <p class="announce-excerpt" data-translate-en="Starting July 1, 2026, we will add sailing schedules to 3 times daily for the Bongao - Lahad Datu route." data-translate-id="Mulai 1 Juli 2026, kami akan menambah jadwal keberangkatan menjadi 3 kali sehari untuk rute Bongao - Lahad Datu.">Starting July 1, 2026, we will add sailing schedules to 3 times daily for the Bongao - Lahad Datu route.</p>
                <a href="{{ route('announcements.detail', 1) }}" class="announce-link" data-translate-en="Read More →" data-translate-id="Baca Selengkapnya →">Read More →</a>
            </div>
            <div class="announce-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
                <div class="announce-date" data-translate-en="June 10, 2026" data-translate-id="10 Juni 2026">June 10, 2026</div>
                <h3 class="announce-title" data-translate-en="Special Independence Month Ticket Promo" data-translate-id="Promo Tiket Bulan Kemerdekaan">Special Independence Month Ticket Promo</h3>
                <p class="announce-excerpt" data-translate-en="Enjoy up to 20% discount on all routes during July - August 2026 in celebration of Independence Day." data-translate-id="Nikmati diskon hingga 20% untuk semua rute selama Juli - Agustus 2026 dalam rangka merayakan Hari Kemerdekaan.">Enjoy up to 20% discount on all routes during July - August 2026 in celebration of Independence Day.</p>
                <a href="{{ route('announcements.detail', 2) }}" class="announce-link" data-translate-en="Read More →" data-translate-id="Baca Selengkapnya →">Read More →</a>
            </div>
            <div class="announce-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="300">
                <div class="announce-date" data-translate-en="June 5, 2026" data-translate-id="5 Juni 2026">June 5, 2026</div>
                <h3 class="announce-title" data-translate-en="Routine Maintenance MV Auralis 8" data-translate-id="Perawatan Rutin MV Auralis 8">Routine Maintenance MV Auralis 8</h3>
                <p class="announce-excerpt" data-translate-en="MV Auralis 8 will undergo routine maintenance on June 20-25, 2026. Schedules will be diverted to other vessels." data-translate-id="MV Auralis 8 akan menjalani perawatan rutin pada 20-25 Juni 2026. Jadwal akan dialihkan ke kapal lain.">MV Auralis 8 will undergo routine maintenance on June 20-25, 2026. Schedules will be diverted to other vessels.</p>
                <a href="{{ route('announcements.detail', 3) }}" class="announce-link" data-translate-en="Read More →" data-translate-id="Baca Selengkapnya →">Read More →</a>
            </div>
        </div>
        <div class="text-center" style="margin-top:48px;" data-aos="fade-up" data-aos-duration="800">
            <a href="{{ route('announcements') }}" class="announce-link" data-translate-en="See All Announcements →" data-translate-id="Lihat Semua Pengumuman →">See All Announcements →</a>
        </div>
    </div>
</section>

{{-- Schedule Section (Blue Gradient) --}}
<section class="guest-section schedule-section" id="schedule">
    <div class="guest-container">
        <p class="guest-section-label" style="color:rgba(255,255,255,0.7);" data-aos="fade-up" data-aos-duration="800" data-translate-en="Ship Schedule" data-translate-id="Jadwal Kapal">Ship Schedule</p>
        <h2 class="guest-section-title" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100" data-translate-en="Ferry Schedule" data-translate-id="Jadwal Feri">Ferry Schedule</h2>
        <p class="guest-section-subtitle" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200" data-translate-en="Browse our latest ferry schedules for Malaysia - Philippines routes and beyond." data-translate-id="Jelajahi jadwal feri terbaru kami untuk rute Malaysia - Filipina dan sekitarnya.">Browse our latest ferry schedules for Malaysia - Philippines routes and beyond.</p>
        <div class="schedule-grid">
            @forelse($schedules as $schedule)
                <div class="schedule-card" data-aos="flip-up" data-aos-duration="800" data-aos-delay="{{ $loop->index * 100 + 100 }}">
                    <div class="schedule-route">{{ $schedule->route->origin_port }} → {{ $schedule->route->destination_port }}</div>
                    <div class="schedule-detail">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span data-translate-en="Departure:" data-translate-id="Keberangkatan:">Departure: <span class="schedule-time">{{ $schedule->departure_time->format('H:i') }} WITA</span></span>
                    </div>
                    <div class="schedule-detail">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span data-translate-en="Arrival:" data-translate-id="Kedatangan:">Arrival: <span class="schedule-time">{{ $schedule->arrival_time->format('H:i') }} WITA</span></span>
                    </div>
                    <div class="schedule-detail">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <span data-translate-en="Vessel:" data-translate-id="Kapal:">Vessel: <span class="schedule-time">{{ $schedule->vessel->name }}</span></span>
                    </div>
                </div>
            @empty
                <div class="schedule-card" style="grid-column:1/-1;text-align:center;padding:40px;">
                    <p style="color:rgba(255,255,255,0.7);" data-translate-en="No schedules available at the moment. Please check back later." data-translate-id="Belum ada jadwal tersedia saat ini. Silakan periksa kembali nanti.">No schedules available at the moment. Please check back later.</p>
                </div>
            @endforelse
        </div>
        <div class="text-center" style="margin-top:48px;" data-aos="fade-up" data-aos-duration="800">
            <a href="{{ route('schedules') }}" style="display:inline-flex;align-items:center;gap:8px;padding:14px 35px;font-size:16px;font-weight:600;color:#fff;background:rgba(255,255,255,0.15);border:2px solid rgba(255,255,255,0.3);border-radius:5px;text-decoration:none;transition:all 0.2s;" data-translate-en="View Full Schedule →" data-translate-id="Lihat Jadwal Lengkap →">
                View Full Schedule →
            </a>
        </div>
    </div>
</section>

{{-- Price Section --}}
<section class="guest-section guest-section-bg-light" id="price">
    <div class="guest-container">
        <p class="guest-section-label" data-aos="fade-up" data-aos-duration="800" data-translate-en="Price List" data-translate-id="Daftar Harga">Price List</p>
        <h2 class="guest-section-title" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100" data-translate-en="Ticket Prices" data-translate-id="Harga Tiket">Ticket Prices</h2>
        <p class="guest-section-subtitle" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200" data-translate-en="Ticket price information for every shipping route we serve." data-translate-id="Informasi harga tiket untuk setiap rute pelayaran yang kami layani.">Ticket price information for every shipping route we serve.</p>
        <div class="price-layout" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="300">
            <div class="price-sidebar">
                <div class="price-sidebar-title" data-translate-en="Province Category" data-translate-id="Kategori Provinsi">Province Category</div>
                <div class="price-sidebar-item active">Tawi-Tawi</div>
                <div class="price-sidebar-item">Sabah</div>
            </div>
            <div class="price-content">
                <table class="price-table">
                    <thead>
                        <tr>
                            <th data-translate-en="Route" data-translate-id="Rute">Route</th>
                            <th data-translate-en="Class" data-translate-id="Kelas">Class</th>
                            <th data-translate-en="Price (RM)" data-translate-id="Harga (RM)">Price (RM)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($prices as $schedule)
                        <tr>
                            <td>{{ $schedule->route->origin_port }} → {{ $schedule->route->destination_port }}</td>
                            <td>VIP</td>
                            <td class="price-amount">RM {{ number_format($schedule->vip_price, 2) }}</td>
                        </tr>
                        <tr>
                            <td>{{ $schedule->route->origin_port }} → {{ $schedule->route->destination_port }}</td>
                            <td>Regular</td>
                            <td class="price-amount">RM {{ number_format($schedule->regular_price, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="text-align:center;color:#6C757D;padding:40px;" data-translate-en="No price data available yet." data-translate-id="Belum ada data harga tersedia.">
                                No price data available yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="price-view-all" data-aos="fade-up" data-aos-duration="800" data-aos-delay="400">
            <a href="{{ route('prices') }}" data-translate-en="View All Routes &amp; Prices →" data-translate-id="Lihat Semua Rute &amp; Harga →">View All Routes &amp; Prices →</a>
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
