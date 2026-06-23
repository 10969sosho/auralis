@extends('layouts.app')
@section('title', 'Jadwal Kapal')

@section('content')
<section class="guest-section schedule-section" style="padding:60px 0;border-radius:16px;margin-bottom:40px;">
    <div class="guest-container" style="max-width:1280px;margin:0 auto;padding:0 24px;">
        <p class="guest-section-label" style="color:rgba(255,255,255,0.7);text-align:center;">Jadwal Kapal</p>
        <h2 class="guest-section-title" style="text-align:center;color:#fff;">Jadwal Pelayaran</h2>
        <p class="guest-section-subtitle" style="text-align:center;color:rgba(255,255,255,0.8);">Lihat jadwal kapal terbaru untuk rute Melayu - Filipina dan sekitarnya.</p>
        <div class="schedule-grid">
            <div class="schedule-card">
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
            <div class="schedule-card">
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
            <div class="schedule-card">
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
    </div>
</section>

<div class="text-center">
    <a href="{{ route('schedules') }}" class="ticket-book-btn" style="display:inline-flex;margin:0 auto;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        Cari Tiket & Booking
    </a>
</div>
@endsection
