@extends('layouts.app')
@section('title', 'About')

@section('content')
<div class="info-page">
    <div class="info-hero">
        <div class="info-hero-content">
            <p class="info-label">ABOUT US</p>
            <h1 class="info-title">ShipTicketing — Your Trusted Ferry Partner</h1>
            <p class="info-desc">We provide safe, comfortable, and punctual sea transportation services connecting the archipelago.</p>
        </div>
    </div>

    <div class="info-section">
        <h2>Our Vision</h2>
        <p>To become the leading shipping company in Southeast Asia that provides the best sea travel experience for every passenger.</p>
    </div>

    <div class="info-section">
        <h2>Our Mission</h2>
        <div class="info-mission-grid">
            <div class="info-mission-item">
                <div class="info-mission-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <h3>Safety</h3>
                <p>Prioritizing safety as the main focus in every voyage.</p>
            </div>
            <div class="info-mission-item">
                <div class="info-mission-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <h3>Affordability</h3>
                <p>Providing the best prices for every shipping route.</p>
            </div>
            <div class="info-mission-item">
                <div class="info-mission-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <h3>Punctuality</h3>
                <p>Committed to providing on-time sailing services.</p>
            </div>
            <div class="info-mission-item">
                <div class="info-mission-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                </div>
                <h3>Service</h3>
                <p>Providing the best service with professional and friendly crew.</p>
            </div>
        </div>
    </div>

    <div class="info-section">
        <h2>Contact Us</h2>
        <div class="info-contact-grid">
            <div class="info-contact-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <div>
                    <strong>Address</strong>
                    <span>Jl. Pelabuhan No. 1, Bongao, Tawi-Tawi</span>
                </div>
            </div>
            <div class="info-contact-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                <div>
                    <strong>Phone</strong>
                    <span>+62 852 1234 5678</span>
                </div>
            </div>
            <div class="info-contact-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                <div>
                    <strong>Email</strong>
                    <span>info@shipticketing.com</span>
                </div>
            </div>
            <div class="info-contact-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <div>
                    <strong>Operating Hours</strong>
                    <span>Mon - Sat: 08:00 - 17:00</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.info-page { max-width: 1280px; margin: 0 auto; padding: 20px 0; }
.info-hero {
    background: linear-gradient(135deg, rgba(11,125,218,0.85), rgba(78,162,255,0.75)), url('{{ asset("images/hero-banner.jpeg") }}') center/cover no-repeat;
    border-radius: 16px;
    padding: 80px 48px;
    margin-bottom: 48px;
    min-height: 300px;
    display: flex;
    align-items: center;
}
.info-hero-content { max-width: 700px; }
.info-label {
    font-size: 14px;
    font-weight: 600;
    color: rgba(255,255,255,0.8);
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-bottom: 12px;
}
.info-title {
    font-size: 42px;
    font-weight: 700;
    color: #fff;
    margin-bottom: 16px;
    line-height: 1.2;
}
.info-desc {
    font-size: 16px;
    color: rgba(255,255,255,0.9);
    line-height: 1.8;
    max-width: 600px;
}
.info-section { max-width: 800px; margin: 0 auto 60px; }
.info-section h2 { font-size: 28px; font-weight: 700; color: #252B42; margin-bottom: 20px; }
.info-section > p { font-size: 16px; color: #6C757D; line-height: 1.8; }
.info-mission-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 30px;
}
.info-mission-item {
    background: #fff;
    border: 1px solid #E9ECEF;
    border-radius: 12px;
    padding: 28px;
    transition: box-shadow 0.2s, transform 0.2s;
}
.info-mission-item:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.06); transform: translateY(-2px); }
.info-mission-icon { width: 48px; height: 48px; color: #0E9AEF; margin-bottom: 16px; }
.info-mission-icon svg { width: 48px; height: 48px; }
.info-mission-item h3 { font-size: 18px; font-weight: 700; color: #252B42; margin-bottom: 8px; }
.info-mission-item p { font-size: 14px; color: #6C757D; line-height: 1.7; }
.info-contact-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
.info-contact-item {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 20px;
    background: #fff;
    border: 1px solid #E9ECEF;
    border-radius: 10px;
}
.info-contact-item svg { width: 22px; height: 22px; color: #0E9AEF; flex-shrink: 0; margin-top: 2px; }
.info-contact-item strong { display: block; font-size: 14px; font-weight: 700; color: #252B42; margin-bottom: 4px; }
.info-contact-item span { font-size: 13px; color: #6C757D; }
@media (max-width: 768px) {
    .info-title { font-size: 28px; }
    .info-mission-grid, .info-contact-grid { grid-template-columns: 1fr; }
    .info-hero { padding: 40px 24px; }
}
</style>
@endsection
