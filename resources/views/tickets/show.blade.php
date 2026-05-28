@extends('layouts.app')
@section('title', 'E-Ticket')

@section('content')

<div class="ticket-page">
    <div class="ticket-page-top">
        <a href="{{ route('booking.detail', $ticket->booking->booking_code) }}" class="ticket-back">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to Booking
        </a>
        <span class="ticket-status-badge {{ $ticket->ticket_status === 'active' ? 'ts-active' : ($ticket->ticket_status === 'used' ? 'ts-used' : 'ts-inactive') }}">
            {{ ucfirst($ticket->ticket_status) }}
        </span>
    </div>

    <div class="ticket-boarding-pass">
        <div class="ticket-pass-header">
            <div class="ticket-pass-header-left" id="ticket-pass-header-left">
                <div class="ticket-pass-vessel">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ticket-pass-ship"><path d="M2 21h20M6 18l2-6h8l2 6M9 12V7M15 12V7M12 7V3"/><path d="M5 7h14l-2 5H7L5 7Z"/><circle cx="12" cy="7" r="1.5"/></svg>
                    <div>
                        <span class="ticket-pass-vessel-name">{{ $ticket->booking->schedule->vessel->name }}</span>
                        <span class="ticket-pass-badge">International Ferry</span>
                    </div>
                </div>
                <span class="ticket-pass-code">{{ $ticket->booking->booking_code }}</span>
            </div>
        </div>

        <div class="ticket-pass-qr-centered" style="text-align:center;margin:18px 0;">
            <img src="{{ $qrcode }}" alt="QR Code" style="width:120px;height:120px;border-radius:8px;background:#fff;padding:6px;display:block;margin:0 auto;">
            <div style="margin-top:6px;font-weight:600;">{{ $ticket->ticket_number }}</div>
        </div>

        <div class="ticket-pass-route" style="margin-top:10px;">
            <div class="ticket-pass-point">
                <span class="ticket-pass-point-label">Departure</span>
                <span class="ticket-pass-point-port">{{ $ticket->booking->schedule->route->origin_port }}</span>
            </div>
            <div class="ticket-pass-connect">
                <div class="ticket-pass-line">
                    <div class="ticket-pass-dot"></div>
                    <div class="ticket-pass-bar"></div>
                    <div class="ticket-pass-arrow-head"></div>
                    <div class="ticket-pass-dot"></div>
                </div>
            </div>
            <div class="ticket-pass-point ticket-pass-point-right">
                <span class="ticket-pass-point-label">Arrival</span>
                <span class="ticket-pass-point-port">{{ $ticket->booking->schedule->route->destination_port }}</span>
            </div>
        </div>

        <div class="ticket-pass-times">
            <div class="ticket-pass-time-block">
                <span class="ticket-pass-time-label">Departure</span>
                <span class="ticket-pass-time-value">{{ $ticket->booking->schedule->departure_time->format('H:i') }}</span>
                <span class="ticket-pass-time-date">{{ $ticket->booking->schedule->departure_time->format('d M Y') }}</span>
            </div>
            <div class="ticket-pass-time-block ticket-pass-time-block-right">
                <span class="ticket-pass-time-label">Arrival</span>
                <span class="ticket-pass-time-value">{{ $ticket->booking->schedule->arrival_time->format('H:i') }}</span>
                <span class="ticket-pass-time-date">{{ $ticket->booking->schedule->arrival_time->format('d M Y') }}</span>
            </div>
        </div>

        <div class="ticket-pass-passenger">
            <div class="ticket-pass-passenger-item">
                <span class="ticket-pass-info-label">Passenger</span>
                <span class="ticket-pass-info-value">{{ $ticket->passenger->full_name }}</span>
            </div>
            <div class="ticket-pass-passenger-grid">
                <div class="ticket-pass-passenger-item">
                    <span class="ticket-pass-info-label">Class</span>
                    <span class="ticket-pass-info-value capitalize">{{ $ticket->ticket_class }}</span>
                </div>
                <div class="ticket-pass-passenger-item">
                    <span class="ticket-pass-info-label">Passport</span>
                    <span class="ticket-pass-info-value">{{ $ticket->passenger->passport_number }}</span>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection
