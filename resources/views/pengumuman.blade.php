@extends('layouts.app')
@section('title', 'Announcements')

@section('content')
<div class="announce-page">
    <div class="announce-page-header">
        <p class="guest-section-label">Announcements</p>
        <h1 class="announce-page-title">News & Announcements</h1>
        <p class="announce-page-sub">Get the latest information about sailing schedules, ticket promos, and other important announcements.</p>
    </div>

    <div class="announce-grid">
        <article class="announce-card">
            <div class="announce-date">June 15, 2026</div>
            <h3 class="announce-title">New Schedule Route Bongao - Lahad Datu</h3>
            <p class="announce-excerpt">Starting July 1, 2026, we will add sailing schedules to 3 times daily for the Bongao - Lahad Datu route.</p>
            <a href="{{ route('pengumuman.detail', 1) }}" class="announce-link">Read More →</a>
        </article>
        <article class="announce-card">
            <div class="announce-date">June 10, 2026</div>
            <h3 class="announce-title">Special Independence Month Ticket Promo</h3>
            <p class="announce-excerpt">Enjoy up to 20% discount on all routes during July - August 2026 in celebration of Independence Day.</p>
            <a href="{{ route('pengumuman.detail', 2) }}" class="announce-link">Read More →</a>
        </article>
        <article class="announce-card">
            <div class="announce-date">June 5, 2026</div>
            <h3 class="announce-title">Routine Maintenance MV Auralis 8</h3>
            <p class="announce-excerpt">MV Auralis 8 will undergo routine maintenance on June 20-25, 2026. Schedules will be diverted to other vessels.</p>
            <a href="{{ route('pengumuman.detail', 3) }}" class="announce-link">Read More →</a>
        </article>
        <article class="announce-card">
            <div class="announce-date">May 28, 2026</div>
            <h3 class="announce-title">New Route Launch Bongao - Sandakan</h3>
            <p class="announce-excerpt">The new Bongao - Sandakan route officially opens on June 15, 2026. Early bird tickets available at special prices.</p>
            <a href="{{ route('pengumuman.detail', 4) }}" class="announce-link">Read More →</a>
        </article>
        <article class="announce-card">
            <div class="announce-date">May 20, 2026</div>
            <h3 class="announce-title">Enhanced QR Boarding Service</h3>
            <p class="announce-excerpt">The QR code boarding system has been upgraded for faster and more accurate processing.</p>
            <a href="{{ route('pengumuman.detail', 5) }}" class="announce-link">Read More →</a>
        </article>
        <article class="announce-card">
            <div class="announce-date">May 15, 2026</div>
            <h3 class="announce-title">Partnership with Airlines</h3>
            <p class="announce-excerpt">We have partnered with airlines for ferry + flight ticket packages. Enjoy more flexible travel options.</p>
            <a href="{{ route('pengumuman.detail', 6) }}" class="announce-link">Read More →</a>
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
