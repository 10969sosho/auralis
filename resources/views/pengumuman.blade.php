@extends('layouts.app')
@section('title', 'Pengumuman')

@section('content')
<div class="announce-page">
    <div class="announce-page-header">
        <p class="guest-section-label">Pengumuman</p>
        <h1 class="announce-page-title">Informasi & Pengumuman</h1>
        <p class="announce-page-sub">Dapatkan informasi terbaru seputar jadwal pelayaran, promo tiket, dan pengumuman penting lainnya.</p>
    </div>

    <div class="announce-grid">
        <article class="announce-card">
            <div class="announce-date">15 Juni 2026</div>
            <h3 class="announce-title">Jadwal Pelayaran Baru Rute Bongao - Lahad Datu</h3>
            <p class="announce-excerpt">Mulai 1 Juli 2026, kami akan menambah jadwal pelayaran menjadi 3 kali sehari untuk rute Bongao - Lahad Datu.</p>
            <a href="{{ route('pengumuman.detail', 1) }}" class="announce-link">Baca Selengkapnya →</a>
        </article>
        <article class="announce-card">
            <div class="announce-date">10 Juni 2026</div>
            <h3 class="announce-title">Promo Tiket Spesial Bulan Kemerdekaan</h3>
            <p class="announce-excerpt">Nikmati diskon hingga 20% untuk semua rute selama bulan Juli - Agustus 2026 dalam rangka Hari Kemerdekaan.</p>
            <a href="{{ route('pengumuman.detail', 2) }}" class="announce-link">Baca Selengkapnya →</a>
        </article>
        <article class="announce-card">
            <div class="announce-date">5 Juni 2026</div>
            <h3 class="announce-title">Pemeliharaan Rutin Kapal MV Auralis 8</h3>
            <p class="announce-excerpt">MV Auralis 8 akan menjalani pemeliharaan rutin pada 20-25 Juni 2026. Jadwal pelayaran akan dialihkan ke kapal lain.</p>
            <a href="{{ route('pengumuman.detail', 3) }}" class="announce-link">Baca Selengkapnya →</a>
        </article>
        <article class="announce-card">
            <div class="announce-date">28 Mei 2026</div>
            <h3 class="announce-title">Pembukaan Rute Baru Bongao - Sandakan</h3>
            <p class="announce-excerpt">Rute baru Bongao - Sandakan resmi dibuka mulai 15 Juni 2026. Tiket early bird tersedia dengan harga spesial.</p>
            <a href="{{ route('pengumuman.detail', 4) }}" class="announce-link">Baca Selengkapnya →</a>
        </article>
        <article class="announce-card">
            <div class="announce-date">20 Mei 2026</div>
            <h3 class="announce-title">Peningkatan Layanan Boarding QR</h3>
            <p class="announce-excerpt">Sistem boarding dengan QR code telah ditingkatkan untuk proses yang lebih cepat dan akurat.</p>
            <a href="{{ route('pengumuman.detail', 5) }}" class="announce-link">Baca Selengkapnya →</a>
        </article>
        <article class="announce-card">
            <div class="announce-date">15 Mei 2026</div>
            <h3 class="announce-title">Kerjasama dengan Maskapai Penerbangan</h3>
            <p class="announce-excerpt">Kami menjalin kerjasama dengan maskapai penerbangan untuk paket tiket ferry + pesawat. Nikmati perjalanan yang lebih fleksibel.</p>
            <a href="{{ route('pengumuman.detail', 6) }}" class="announce-link">Baca Selengkapnya →</a>
        </article>
    </div>
</div>

<style>
.announce-page {
    max-width: 1280px;
    margin: 0 auto;
}
.announce-page-header {
    text-align: center;
    margin-bottom: 48px;
}
.announce-page-title {
    font-size: 42px;
    font-weight: 700;
    color: #252B42;
    margin-bottom: 12px;
}
.announce-page-sub {
    font-size: 16px;
    color: #6C757D;
    max-width: 600px;
    margin: 0 auto;
    line-height: 1.8;
}
.announce-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 35px;
}
@media (max-width: 992px) {
    .announce-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 768px) {
    .announce-page-title { font-size: 28px; }
    .announce-grid { grid-template-columns: 1fr; }
}
</style>
@endsection
