@extends('layouts.app')
@section('title', 'Announcement Detail')

@section('content')
<div class="article-page">
    <a href="{{ route('pengumuman') }}" class="article-back">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Back to Announcements
    </a>

    <article class="article-card">
        <span class="article-category">Information</span>
        <h1 class="article-title">New Schedule Route Bongao - Lahad Datu</h1>
        <div class="article-meta">
            <span class="article-date">June 15, 2026</span>
            <span class="article-divider">|</span>
            <span class="article-author">Admin ShipTicketing</span>
        </div>
        <div class="article-content">
            <p>We are pleased to announce the addition of new sailing schedules for the Bongao - Lahad Datu route starting July 1, 2026.</p>
            <p>With this addition, sailing schedules will be 3 times daily, providing more departure time options for passengers.</p>
            <h2>New Schedule:</h2>
            <ul>
                <li>Departure 1: 08:00 WITA - 11:30 WITA</li>
                <li>Departure 2: 13:00 WITA - 16:30 WITA</li>
                <li>Departure 3: 18:00 WITA - 21:30 WITA</li>
            </ul>
            <p>For more information, please contact our customer service.</p>
        </div>
    </article>
</div>

<style>
.article-page {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px 0;
}
.article-back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 15px;
    font-weight: 600;
    color: #6C757D;
    text-decoration: none;
    margin-bottom: 32px;
    transition: color 0.2s;
}
.article-back svg { width: 20px; height: 20px; }
.article-back:hover { color: #0E9AEF; }
.article-card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #E9ECEF;
    padding: 48px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.04);
}
.article-category {
    display: inline-block;
    padding: 4px 12px;
    font-size: 12px;
    font-weight: 600;
    color: #0E9AEF;
    background: rgba(14,154,239,0.08);
    border-radius: 4px;
    margin-bottom: 16px;
}
.article-title {
    font-size: 32px;
    font-weight: 700;
    color: #252B42;
    line-height: 1.3;
    margin-bottom: 16px;
}
.article-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 14px;
    color: #6C757D;
    margin-bottom: 32px;
    padding-bottom: 24px;
    border-bottom: 1px solid #E9ECEF;
}
.article-content {
    font-size: 16px;
    color: #6C757D;
    line-height: 1.8;
}
.article-content p {
    margin-bottom: 16px;
}
.article-content h2 {
    font-size: 24px;
    font-weight: 700;
    color: #252B42;
    margin: 32px 0 16px;
}
.article-content ul {
    margin: 0 0 16px 24px;
}
.article-content li {
    margin-bottom: 8px;
}
@media (max-width: 768px) {
    .article-card { padding: 28px 20px; }
    .article-title { font-size: 24px; }
}
</style>
@endsection
