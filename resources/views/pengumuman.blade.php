@extends('layouts.app')
@section('title', 'Announcements')

@section('content')
<div class="announce-page">
    <div class="announce-page-header">
        <p class="guest-section-label" data-translate-en="Announcements" data-translate-id="Pengumuman">Announcements</p>
        <h1 class="announce-page-title" data-translate-en="News &amp; Announcements" data-translate-id="Berita &amp; Pengumuman">News & Announcements</h1>
        <p class="announce-page-sub" data-translate-en="Get the latest information about sailing schedules, ticket promos, and other important announcements." data-translate-id="Dapatkan informasi terbaru tentang jadwal kapal, promo tiket, dan pengumuman penting lainnya.">Get the latest information about sailing schedules, ticket promos, and other important announcements.</p>
    </div>

    <div class="announce-grid">
        <article class="announce-card">
            <div class="announce-date">June 15, 2026</div>
            <h3 class="announce-title" data-translate-en="New Schedule Route Bongao - Lahad Datu" data-translate-id="Jadwal Baru Rute Bongao - Lahad Datu">New Schedule Route Bongao - Lahad Datu</h3>
            <p class="announce-excerpt" data-translate-en="Starting July 1, 2026, we will add sailing schedules to 3 times daily for the Bongao - Lahad Datu route." data-translate-id="Mulai 1 Juli 2026, kami akan menambah jadwal pelayaran menjadi 3 kali sehari untuk rute Bongao - Lahad Datu.">Starting July 1, 2026, we will add sailing schedules to 3 times daily for the Bongao - Lahad Datu route.</p>
            <a href="{{ route('pengumuman.detail', 1) }}" class="announce-link" data-translate-en="Read More →" data-translate-id="Baca Selengkapnya &rarr;">Read More →</a>
        </article>
        <article class="announce-card">
            <div class="announce-date">June 10, 2026</div>
            <h3 class="announce-title" data-translate-en="Special Independence Month Ticket Promo" data-translate-id="Promo Tiket Spesial Bulan Kemerdekaan">Special Independence Month Ticket Promo</h3>
            <p class="announce-excerpt" data-translate-en="Enjoy up to 20% discount on all routes during July - August 2026 in celebration of Independence Day." data-translate-id="Nikmati diskon hingga 20% untuk semua rute selama Juli - Agustus 2026 dalam rangka merayakan Hari Kemerdekaan.">Enjoy up to 20% discount on all routes during July - August 2026 in celebration of Independence Day.</p>
            <a href="{{ route('pengumuman.detail', 2) }}" class="announce-link" data-translate-en="Read More →" data-translate-id="Baca Selengkapnya &rarr;">Read More →</a>
        </article>
        <article class="announce-card">
            <div class="announce-date">June 5, 2026</div>
            <h3 class="announce-title" data-translate-en="Routine Maintenance MV Auralis 8" data-translate-id="Pemeliharaan Rutin MV Auralis 8">Routine Maintenance MV Auralis 8</h3>
            <p class="announce-excerpt" data-translate-en="MV Auralis 8 will undergo routine maintenance on June 20-25, 2026. Schedules will be diverted to other vessels." data-translate-id="MV Auralis 8 akan menjalani pemeliharaan rutin pada 20-25 Juni 2026. Jadwal akan dialihkan ke kapal lain.">MV Auralis 8 will undergo routine maintenance on June 20-25, 2026. Schedules will be diverted to other vessels.</p>
            <a href="{{ route('pengumuman.detail', 3) }}" class="announce-link" data-translate-en="Read More →" data-translate-id="Baca Selengkapnya &rarr;">Read More →</a>
        </article>
        <article class="announce-card">
            <div class="announce-date">May 28, 2026</div>
            <h3 class="announce-title" data-translate-en="New Route Launch Bongao - Sandakan" data-translate-id="Peluncuran Rute Baru Bongao - Sandakan">New Route Launch Bongao - Sandakan</h3>
            <p class="announce-excerpt" data-translate-en="The new Bongao - Sandakan route officially opens on June 15, 2026. Early bird tickets available at special prices." data-translate-id="Rute baru Bongao - Sandakan resmi dibuka pada 15 Juni 2026. Tiket early bird tersedia dengan harga spesial.">The new Bongao - Sandakan route officially opens on June 15, 2026. Early bird tickets available at special prices.</p>
            <a href="{{ route('pengumuman.detail', 4) }}" class="announce-link" data-translate-en="Read More →" data-translate-id="Baca Selengkapnya &rarr;">Read More →</a>
        </article>
        <article class="announce-card">
            <div class="announce-date">May 20, 2026</div>
            <h3 class="announce-title" data-translate-en="Enhanced QR Boarding Service" data-translate-id="Layanan Boarding QR yang Ditingkatkan">Enhanced QR Boarding Service</h3>
            <p class="announce-excerpt" data-translate-en="The QR code boarding system has been upgraded for faster and more accurate processing." data-translate-id="Sistem boarding kode QR telah ditingkatkan untuk pemrosesan yang lebih cepat dan akurat.">The QR code boarding system has been upgraded for faster and more accurate processing.</p>
            <a href="{{ route('pengumuman.detail', 5) }}" class="announce-link" data-translate-en="Read More →" data-translate-id="Baca Selengkapnya &rarr;">Read More →</a>
        </article>
        <article class="announce-card">
            <div class="announce-date">May 15, 2026</div>
            <h3 class="announce-title" data-translate-en="Partnership with Airlines" data-translate-id="Kemitraan dengan Maskapai Penerbangan">Partnership with Airlines</h3>
            <p class="announce-excerpt" data-translate-en="We have partnered with airlines for ferry + flight ticket packages. Enjoy more flexible travel options." data-translate-id="Kami telah bermitra dengan maskapai penerbangan untuk paket tiket feri + penerbangan. Nikmati pilihan perjalanan yang lebih fleksibel.">We have partnered with airlines for ferry + flight ticket packages. Enjoy more flexible travel options.</p>
            <a href="{{ route('pengumuman.detail', 6) }}" class="announce-link" data-translate-en="Read More →" data-translate-id="Baca Selengkapnya &rarr;">Read More →</a>
        </article>
    </div>
</div>

<style>
.announce-page {
    max-width: 1100px;
    margin: 0 auto;
}
.announce-page-header {
    text-align: center;
    margin-bottom: 48px;
    background: linear-gradient(135deg, rgba(11,125,218,0.85), rgba(78,162,255,0.75)), url('{{ asset("images/ferry-story.jpeg") }}') center/cover no-repeat;
    border-radius: 16px;
    padding: 60px 48px;
    min-height: 250px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.announce-page-header .guest-section-label { color: rgba(255,255,255,0.8); }
.announce-page-title {
    font-size: 42px;
    font-weight: 700;
    color: #fff;
    margin-bottom: 12px;
}
.announce-page-sub {
    font-size: 16px;
    color: rgba(255,255,255,0.8);
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
    .announce-page-header { padding: 40px 24px; }
    .announce-grid { grid-template-columns: 1fr; }
}
</style>
@endsection