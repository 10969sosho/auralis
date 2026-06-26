@extends('layouts.app')
@section('title', 'Ship Schedule')

@section('content')
<section class="guest-section" style="padding:80px 0;border-radius:16px;margin-bottom:40px;background:linear-gradient(135deg, rgba(11,125,218,0.85), rgba(78,162,255,0.75)), url('{{ asset("images/hero-banner.jpeg") }}') center/cover no-repeat;min-height:400px;display:flex;align-items:center;">
    <div class="guest-container" style="max-width:1280px;margin:0 auto;padding:0 24px;">
        <p class="guest-section-label" style="color:rgba(255,255,255,0.7);text-align:center;">Ship Schedule</p>
        <h2 class="guest-section-title" style="text-align:center;color:#fff;">Ferry Schedule</h2>
        <p class="guest-section-subtitle" style="text-align:center;color:rgba(255,255,255,0.8);">Browse our latest ferry schedules for Malaysia - Philippines routes and beyond.</p>
        <div class="schedule-grid">
            <div class="schedule-card">
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
            <div class="schedule-card">
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
            <div class="schedule-card">
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
    </div>
</section>

<div class="text-center">
    <a href="{{ route('schedules') }}" class="ticket-book-btn" style="display:inline-flex;margin:0 auto;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        Search Tickets & Book
    </a>
</div>
@endsection
