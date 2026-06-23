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
        <h1 class="guest-hero-title">Let's Explore Indonesia Together</h1>
        <p class="guest-hero-hashtag">#menjangkauseribupulau</p>
        <a href="{{ route('schedules') }}" class="guest-hero-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            Pesan Tiket
        </a>
    </div>
</section>

{{-- Features Section --}}
<section class="guest-section guest-section-bg-white" id="features">
    <div class="guest-container">
        <p class="guest-section-label" data-aos="fade-up" data-aos-duration="800">Layanan Kami</p>
        <h2 class="guest-section-title" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">Mengapa Memilih Kami?</h2>
        <p class="guest-section-subtitle" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">Kami berkomitmen memberikan pengalaman perjalanan laut terbaik dengan keamanan, kenyamanan, dan harga terjangkau.</p>
        <div class="features-grid">
            <div class="feature-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
                </div>
                <h3 class="feature-title">Safety</h3>
                <p class="feature-desc">Kapal kami dilengkapi dengan peralatan keselamatan modern dan awak kapal yang profesional dan berpengalaman.</p>
            </div>
            <div class="feature-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <h3 class="feature-title">Save Your Time</h3>
                <p class="feature-desc">Pemesanan tiket online yang cepat dan mudah. Cukup beberapa menit, tiket Anda siap tanpa harus mengantri.</p>
            </div>
            <div class="feature-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="300">
                <div class="feature-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1v22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <h3 class="feature-title">Best Price</h3>
                <p class="feature-desc">Nikmati harga terbaik untuk setiap rute pelayaran dengan berbagai pilihan kelas yang sesuai kebutuhan Anda.</p>
            </div>
        </div>
    </div>
</section>

{{-- Company Story Section --}}
<section class="guest-section guest-section-bg-light" id="about">
    <div class="story-wrapper">
        <div class="story-image" data-aos="fade-right" data-aos-duration="1000">
            <img src="https://images.unsplash.com/photo-1568027343401-1c46d8d16de3?w=800&q=80" alt="Kapal Ferry">
        </div>
        <div class="story-content" data-aos="fade-left" data-aos-duration="1000">
            <p class="story-label">WHO WE ARE</p>
            <h2 class="story-title">Company Story</h2>
            <p class="story-text">
                ShipTicketing adalah perusahaan pelayaran terkemuka yang melayani rute internasional Malaysia - Filipina sejak 2010. Kami berkomitmen untuk menyediakan layanan transportasi laut yang aman, nyaman, dan tepat waktu bagi seluruh penumpang.
            </p>
            <div class="story-stat-grid">
                <div class="story-stat-item" data-aos="zoom-in" data-aos-duration="600" data-aos-delay="100">
                    <span class="story-stat-number">35+</span>
                    <span class="story-stat-label">Total Armada</span>
                </div>
                <div class="story-stat-item" data-aos="zoom-in" data-aos-duration="600" data-aos-delay="200">
                    <span class="story-stat-number">4</span>
                    <span class="story-stat-label">Kantor Cabang</span>
                </div>
                <div class="story-stat-item" data-aos="zoom-in" data-aos-duration="600" data-aos-delay="300">
                    <span class="story-stat-number">37+</span>
                    <span class="story-stat-label">Rute Dilayani</span>
                </div>
            </div>
            <div style="margin-top:32px;" data-aos="fade-up" data-aos-duration="800" data-aos-delay="400">
                <a href="{{ route('informasi') }}" class="guest-hero-btn" style="display:inline-flex;padding:14px 32px;font-size:15px;">
                    Pelajari Lebih Lanjut →
                </a>
            </div>
        </div>
    </div>
</section>

{{-- Announcement Section --}}
<section class="guest-section guest-section-bg-white" id="announcement">
    <div class="guest-container">
        <p class="guest-section-label" data-aos="fade-up" data-aos-duration="800">Pengumuman</p>
        <h2 class="guest-section-title" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">Informasi & Pengumuman</h2>
        <p class="guest-section-subtitle" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">Dapatkan informasi terbaru seputar jadwal pelayaran, promo tiket, dan pengumuman penting lainnya.</p>
        <div class="announce-grid">
            <div class="announce-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">
                <div class="announce-date">15 Juni 2026</div>
                <h3 class="announce-title">Jadwal Pelayaran Baru Rute Bongao - Lahad Datu</h3>
                <p class="announce-excerpt">Mulai 1 Juli 2026, kami akan menambah jadwal pelayaran menjadi 3 kali sehari untuk rute Bongao - Lahad Datu.</p>
                <a href="{{ route('pengumuman.detail', 1) }}" class="announce-link">Baca Selengkapnya →</a>
            </div>
            <div class="announce-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
                <div class="announce-date">10 Juni 2026</div>
                <h3 class="announce-title">Promo Tiket Spesial Bulan Kemerdekaan</h3>
                <p class="announce-excerpt">Nikmati diskon hingga 20% untuk semua rute selama bulan Juli - Agustus 2026 dalam rangka Hari Kemerdekaan.</p>
                <a href="{{ route('pengumuman.detail', 2) }}" class="announce-link">Baca Selengkapnya →</a>
            </div>
            <div class="announce-card" data-aos="fade-up" data-aos-duration="800" data-aos-delay="300">
                <div class="announce-date">5 Juni 2026</div>
                <h3 class="announce-title">Pemeliharaan Rutin Kapal MV Auralis 8</h3>
                <p class="announce-excerpt">MV Auralis 8 akan menjalani pemeliharaan rutin pada 20-25 Juni 2026. Jadwal pelayaran akan dialihkan ke kapal lain.</p>
                <a href="{{ route('pengumuman.detail', 3) }}" class="announce-link">Baca Selengkapnya →</a>
            </div>
        </div>
        <div class="text-center" style="margin-top:48px;" data-aos="fade-up" data-aos-duration="800">
            <a href="{{ route('pengumuman') }}" class="announce-link">Lihat Semua Pengumuman →</a>
        </div>
    </div>
</section>

{{-- Schedule Section (Blue Gradient) --}}
<section class="guest-section schedule-section" id="schedule">
    <div class="guest-container">
        <p class="guest-section-label" style="color:rgba(255,255,255,0.7);" data-aos="fade-up" data-aos-duration="800">Jadwal Kapal</p>
        <h2 class="guest-section-title" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">Jadwal Pelayaran</h2>
        <p class="guest-section-subtitle" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">Lihat jadwal kapal terbaru untuk rute Melayu - Filipina dan sekitarnya.</p>
        <div class="schedule-grid">
            <div class="schedule-card" data-aos="flip-up" data-aos-duration="800" data-aos-delay="100">
                <div class="schedule-route">Bongao → Lahad Datu</div>
                <div class="schedule-detail">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span>Keberangkatan: <span class="schedule-time">08:00 WITA</span></span>
                </div>
                <div class="schedule-detail">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span>Tiba: <span class="schedule-time">11:30 WITA</span></span>
                </div>
                <div class="schedule-detail">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span>Kapal: <span class="schedule-time">MV Auralis 8</span></span>
                </div>
            </div>
            <div class="schedule-card" data-aos="flip-up" data-aos-duration="800" data-aos-delay="200">
                <div class="schedule-route">Lahad Datu → Bongao</div>
                <div class="schedule-detail">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span>Keberangkatan: <span class="schedule-time">13:00 WITA</span></span>
                </div>
                <div class="schedule-detail">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span>Tiba: <span class="schedule-time">16:30 WITA</span></span>
                </div>
                <div class="schedule-detail">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span>Kapal: <span class="schedule-time">MV Auralis 8</span></span>
                </div>
            </div>
            <div class="schedule-card" data-aos="flip-up" data-aos-duration="800" data-aos-delay="300">
                <div class="schedule-route">Bongao → Sandakan</div>
                <div class="schedule-detail">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span>Keberangkatan: <span class="schedule-time">09:00 WITA</span></span>
                </div>
                <div class="schedule-detail">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span>Tiba: <span class="schedule-time">14:00 WITA</span></span>
                </div>
                <div class="schedule-detail">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span>Kapal: <span class="schedule-time">MV Auralis 8</span></span>
                </div>
            </div>
        </div>
        <div class="text-center" style="margin-top:48px;" data-aos="fade-up" data-aos-duration="800">
            <a href="{{ route('jadwal') }}" style="display:inline-flex;align-items:center;gap:8px;padding:14px 35px;font-size:16px;font-weight:600;color:#fff;background:rgba(255,255,255,0.15);border:2px solid rgba(255,255,255,0.3);border-radius:5px;text-decoration:none;transition:all 0.2s;">
                Lihat Jadwal Lengkap →
            </a>
        </div>
    </div>
</section>

{{-- Booking Section --}}
<section class="guest-section booking-section" id="booking">
    <div class="guest-container">
        <p class="guest-section-label" data-aos="fade-up" data-aos-duration="800">Booking Tiket</p>
        <h2 class="guest-section-title" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">Pesan Tiket Kapal</h2>
        <p class="guest-section-subtitle" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">Isi data berikut untuk memulai pemesanan tiket kapal ferry Anda.</p>
        <div class="booking-box" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="300">
            <form action="{{ route('schedules') }}" method="GET">
                <div class="booking-form-grid">
                    <div class="booking-form-full">
                        <label class="booking-label">Tanggal Keberangkatan</label>
                        <input type="date" name="date" class="booking-input" required>
                    </div>
                    <div>
                        <label class="booking-label">Pelabuhan Asal</label>
                        <select name="from" class="booking-input" required>
                            <option value="">Pilih Pelabuhan</option>
                            <option value="Bongao">Bongao, Tawi-Tawi</option>
                            <option value="Lahad Datu">Lahad Datu, Sabah</option>
                            <option value="Sandakan">Sandakan, Sabah</option>
                        </select>
                    </div>
                    <div>
                        <label class="booking-label">Pelabuhan Tujuan</label>
                        <select name="to" class="booking-input" required>
                            <option value="">Pilih Pelabuhan</option>
                            <option value="Lahad Datu">Lahad Datu, Sabah</option>
                            <option value="Bongao">Bongao, Tawi-Tawi</option>
                            <option value="Sandakan">Sandakan, Sabah</option>
                        </select>
                    </div>
                    <div>
                        <label class="booking-label">Jumlah Dewasa</label>
                        <div class="booking-counter-group">
                            <button type="button" class="booking-counter-btn" onclick="adjustCounter('adult', -1)">−</button>
                            <span class="booking-counter-value" id="adultCount">1</span>
                            <button type="button" class="booking-counter-btn" onclick="adjustCounter('adult', 1)">+</button>
                        </div>
                        <input type="hidden" name="adult" id="adultInput" value="1">
                    </div>
                    <div>
                        <label class="booking-label">Jumlah Anak</label>
                        <div class="booking-counter-group">
                            <button type="button" class="booking-counter-btn" onclick="adjustCounter('child', -1)">−</button>
                            <span class="booking-counter-value" id="childCount">0</span>
                            <button type="button" class="booking-counter-btn" onclick="adjustCounter('child', 1)">+</button>
                        </div>
                        <input type="hidden" name="child" id="childInput" value="0">
                    </div>
                    <button type="submit" class="booking-submit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        Cari Tiket
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

{{-- Price Section --}}
<section class="guest-section guest-section-bg-light" id="price">
    <div class="guest-container">
        <p class="guest-section-label" data-aos="fade-up" data-aos-duration="800">Daftar Harga</p>
        <h2 class="guest-section-title" data-aos="fade-up" data-aos-duration="800" data-aos-delay="100">Harga Tiket</h2>
        <p class="guest-section-subtitle" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">Informasi harga tiket untuk setiap rute pelayaran yang kami layani.</p>
        <div class="price-layout" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="300">
            <div class="price-sidebar">
                <div class="price-sidebar-title">Kategori Provinsi</div>
                <div class="price-sidebar-item active">Tawi-Tawi</div>
                <div class="price-sidebar-item">Sabah</div>
            </div>
            <div class="price-content">
                <table class="price-table">
                    <thead>
                        <tr>
                            <th>Rute</th>
                            <th>Kelas</th>
                            <th>Harga (MYR)</th>
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
                                Belum ada data harga tiket tersedia.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="price-view-all" data-aos="fade-up" data-aos-duration="800" data-aos-delay="400">
            <a href="{{ route('harga') }}">Lihat Semua Rute & Harga →</a>
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
</script>

@endsection
