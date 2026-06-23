@extends('layouts.app')
@section('title', 'Informasi')

@section('content')
<div class="info-page">
    <div class="info-hero">
        <div class="info-hero-content">
            <p class="info-label">TENTANG KAMI</p>
            <h1 class="info-title">ShipTickening — Mitra Pelayaran Terpercaya</h1>
            <p class="info-desc">Kami menyediakan layanan transportasi laut yang aman, nyaman, dan tepat waktu untuk menghubungkan nusantara.</p>
        </div>
    </div>

    <div class="info-section">
        <h2>Visi Kami</h2>
        <p>Menjadi perusahaan pelayaran terdepan di Asia Tenggara yang memberikan pengalaman perjalanan laut terbaik bagi setiap penumpang.</p>
    </div>

    <div class="info-section">
        <h2>Misi Kami</h2>
        <div class="info-mission-grid">
            <div class="info-mission-item">
                <div class="info-mission-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <h3>Keamanan</h3>
                <p>Mengedepankan keselamatan sebagai prioritas utama dalam setiap pelayaran.</p>
            </div>
            <div class="info-mission-item">
                <div class="info-mission-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <h3>Keterjangkauan</h3>
                <p>Memberikan harga terbaik untuk setiap rute pelayaran.</p>
            </div>
            <div class="info-mission-item">
                <div class="info-mission-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <h3>Ketepatan Waktu</h3>
                <p>Berkomitmen untuk memberikan pelayaran yang tepat waktu.</p>
            </div>
            <div class="info-mission-item">
                <div class="info-mission-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                </div>
                <h3>Pelayanan</h3>
                <p>Memberikan pelayanan terbaik dengan crew yang profesional dan ramah.</p>
            </div>
        </div>
    </div>

    <div class="info-section">
        <h2>Kontak Kami</h2>
        <div class="info-contact-grid">
            <div class="info-contact-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <div>
                    <strong>Alamat</strong>
                    <span>Jl. Pelabuhan No. 1, Bongao, Tawi-Tawi</span>
                </div>
            </div>
            <div class="info-contact-item">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                <div>
                    <strong>Telepon</strong>
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
                    <strong>Jam Operasional</strong>
                    <span>Mon - Sat: 08:00 - 17:00</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.info-page { max-width: 1280px; margin: 0 auto; padding: 20px 0; }
.info-hero {
    background: linear-gradient(135deg, #0B7DDA, #4EA2FF);
    border-radius: 16px;
    padding: 60px 48px;
    margin-bottom: 48px;
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
.info-section {
    margin-bottom: 48px;
}
.info-section h2 {
    font-size: 28px;
    font-weight: 700;
    color: #252B42;
    margin-bottom: 20px;
}
.info-section p {
    font-size: 16px;
    color: #6C757D;
    line-height: 1.8;
    max-width: 700px;
}
.info-mission-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
}
.info-mission-item {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #E9ECEF;
    padding: 32px 24px;
    text-align: center;
    box-shadow: 0 4px 16px rgba(0,0,0,0.04);
}
.info-mission-icon {
    width: 56px;
    height: 56px;
    margin: 0 auto 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(14,154,239,0.08);
    border-radius: 50%;
}
.info-mission-icon svg { width: 28px; height: 28px; color: #0E9AEF; }
.info-mission-item h3 {
    font-size: 18px;
    font-weight: 700;
    color: #252B42;
    margin-bottom: 8px;
}
.info-mission-item p {
    font-size: 14px;
    color: #6C757D;
    line-height: 1.6;
    max-width: 100%;
}
.info-contact-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}
.info-contact-item {
    display: flex;
    align-items: center;
    gap: 16px;
    background: #fff;
    border-radius: 12px;
    border: 1px solid #E9ECEF;
    padding: 24px;
}
.info-contact-item svg {
    width: 32px;
    height: 32px;
    color: #0E9AEF;
    flex-shrink: 0;
}
.info-contact-item div {
    display: flex;
    flex-direction: column;
}
.info-contact-item strong {
    font-size: 14px;
    font-weight: 700;
    color: #252B42;
    margin-bottom: 2px;
}
.info-contact-item span {
    font-size: 14px;
    color: #6C757D;
}
@media (max-width: 992px) {
    .info-mission-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 768px) {
    .info-title { font-size: 28px; }
    .info-hero { padding: 40px 24px; }
    .info-mission-grid { grid-template-columns: 1fr; }
    .info-contact-grid { grid-template-columns: 1fr; }
}
</style>
@endsection
