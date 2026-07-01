@extends('layouts.app')
@section('title', 'Counter Bookings')

@section('content')

<div class="bookings-page">
    <div class="bookings-header">
        <div>
            <h1 class="bookings-title">Counter Bookings</h1>
            <p class="bookings-sub">All tickets checked out by counter</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;justify-content:flex-end;">
            <form action="{{ route('counter.search') }}" method="GET" style="display:flex;gap:8px;">
                <input type="text" name="query" placeholder="Search booking code, passenger..." class="form-input" style="width:200px;" required>
                <button type="submit" class="btn btn-primary btn-sm">Search</button>
            </form>
        </div>
    </div>

    <div class="bookings-tabs">
        <a href="{{ route('counter.history') }}" class="bookings-tab {{ !request('status') ? 'bookings-tab-active' : '' }}">All</a>
        <a href="{{ route('counter.history') }}?status=paid" class="bookings-tab {{ request('status') === 'paid' ? 'bookings-tab-active' : '' }}">Paid</a>
        <a href="{{ route('counter.history') }}?status=used" class="bookings-tab {{ request('status') === 'used' ? 'bookings-tab-active' : '' }}">Completed</a>
        <a href="{{ route('counter.history') }}?status=cancelled" class="bookings-tab {{ request('status') === 'cancelled' ? 'bookings-tab-active' : '' }}">Cancelled</a>
        <a href="{{ route('counter.history') }}?status=refund_requested" class="bookings-tab {{ request('status') === 'refund_requested' ? 'bookings-tab-active' : '' }}">Refund Requested</a>
        <a href="{{ route('counter.history') }}?status=refunded" class="bookings-tab {{ request('status') === 'refunded' ? 'bookings-tab-active' : '' }}">Refunded</a>
    </div>

    <div class="bookings-list">
        @forelse($bookings as $booking)
            @php
                $statusLabel = \App\Helpers\StatusHelper::effectiveStatusLabel($booking);
                $badgeClass = \App\Helpers\StatusHelper::effectiveBadgeClass($booking);
            @endphp

            <div class="booking-card">
                <div class="booking-card-top">
                    <div class="booking-card-code">
                        <span class="booking-code-label">Booking Code</span>
                        <span class="booking-code-value">#{{ $booking->booking_code }}</span>
                    </div>
                    <span class="booking-card-status {{ $badgeClass }}">{{ $statusLabel }}</span>
                </div>

                <div class="booking-card-route">
                    <div class="booking-card-route-point">
                        <span class="booking-card-port">{{ $booking->schedule->route->origin_port }}</span>
                        </div>
                    <div class="booking-card-route-line">
                        <div class="booking-card-line-dot"></div>
                        <div class="booking-card-line-bar"></div>
                        <div class="booking-card-line-dot"></div>
                    </div>
                    <div class="booking-card-route-point booking-card-route-point-right">
                        <span class="booking-card-port">{{ $booking->schedule->route->destination_port }}</span>
                    </div>
                </div>

                <div class="booking-card-info">
                    <div class="booking-card-info-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 21h20M6 18l2-6h8l2 6M9 12V7M15 12V7M12 7V3"/><path d="M5 7h14l-2 5H7L5 7Z"/><circle cx="12" cy="7" r="1.5"/></svg>
                        <span>{{ $booking->schedule->vessel->name }}</span>
                    </div>
                    <div class="booking-card-info-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <span>{{ $booking->schedule->departure_time->format('d M Y, H:i') }}</span>
                    </div>
                    <div class="booking-card-info-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        <span>{{ $booking->total_passengers }} passenger{{ $booking->total_passengers > 1 ? 's' : '' }}</span>
                    </div>
                    <div class="booking-card-info-item booking-card-info-amount">
                        <span>RM {{ number_format($booking->total_amount, 2) }}</span>
                    </div>
                </div>

                <div class="booking-card-bottom">
                    <a href="{{ route('counter.detail', $booking->booking_code) }}" class="booking-card-btn">
                        View Details
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                    @if($booking->booking_status === 'paid')
                        <div style="display:flex;gap:4px;margin-left:auto;">
                            @foreach($booking->passengers as $p)
                                @if($p->ticket)
                                    <a href="{{ route('tickets.download', $p->ticket) }}" target="_blank" style="display:inline-flex;align-items:center;gap:3px;padding:4px 10px;border:1px solid #059669;color:#059669;border-radius:6px;font-size:0.7rem;font-weight:700;text-decoration:none;">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                        Print
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bookings-empty">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                <h3 class="bookings-empty-title">No counter bookings found</h3>
                <p class="bookings-empty-desc">No bookings match the selected filter.</p>
                <a href="{{ route('counter.dashboard') }}" class="bookings-empty-btn">Go to Counter</a>
            </div>
        @endforelse
    </div>

    @if($bookings->hasPages())
        <div class="bookings-pagination">
            {{ $bookings->appends(request()->query())->links() }}
        </div>
    @endif
</div>

@endsection