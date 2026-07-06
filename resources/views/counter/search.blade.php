@extends('layouts.app')
@section('title', 'Search Bookings - Counter')

@section('content')

<div class="bookings-page">
    <div class="bookings-header">
        <div>
            <h1 class="bookings-title" data-translate-en="Search Bookings" data-translate-id="Cari Pemesanan">Search Bookings</h1>
            <p class="bookings-sub" data-translate-en="Find counter or online bookings" data-translate-id="Cari pemesanan loket atau online">Find counter or online bookings</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="{{ route('counter.history') }}" class="btn btn-outline btn-sm" data-translate-en="Counter History" data-translate-id="Riwayat Loket">Counter History</a>
        </div>
    </div>

    <div class="bookings-list">
        <div class="card" style="margin-bottom:20px;">
            <form action="{{ route('counter.search') }}" method="GET" style="display:flex;gap:12px;">
                <input type="text" name="query" value="{{ request('query') }}" placeholder="Search booking code, passenger name or passport..." class="form-input" style="flex:1;" required minlength="3" data-translate-en="Search booking code, passenger name or passport..." data-translate-id="Cari kode pemesanan, nama penumpang atau paspor...">
                <button type="submit" class="btn btn-primary" data-translate-en="Search" data-translate-id="Cari">Search</button>
            </form>
        </div>

        @if(request('query'))
        <p class="text-sm text-gray-500 mb-4">
            <span data-translate-en="Result for:" data-translate-id="Hasil untuk:">Result for:</span> <strong class="text-blue-600">"{{ request('query') }}"</strong>
            @if($bookings->count() > 0)
            <span data-translate-en="&middot; {count} booking found" data-translate-id="&middot; {count} pemesanan ditemukan">· {{ $bookings->count() }} booking{{ $bookings->count() > 1 ? 's' : '' }} found</span>
            @endif
        </p>
        @endif

        @forelse($bookings as $booking)
            @php
                $statusLabel = \App\Helpers\StatusHelper::effectiveStatusLabel($booking);
                $badgeClass = \App\Helpers\StatusHelper::effectiveBadgeClass($booking);
            @endphp

            <div class="booking-card">
                <div class="booking-card-top">
                    <div class="booking-card-code">
                        <span class="booking-code-label" data-translate-en="Booking Code" data-translate-id="Kode Pemesanan">Booking Code</span>
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
                        <span>{{ $booking->total_passengers }} <span data-translate-en="passenger(s)" data-translate-id="penumpang">passenger{{ $booking->total_passengers > 1 ? 's' : '' }}</span></span>
                    </div>
                    <div class="booking-card-info-item booking-card-info-amount">
                        <span>RM {{ number_format($booking->total_amount, 2) }}</span>
                    </div>
                </div>

                <div class="booking-card-bottom">
                    <a href="{{ route('counter.detail', $booking->booking_code) }}" class="booking-card-btn" data-translate-en="View Details" data-translate-id="Lihat Detail">
                        View Details
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                    <div style="display:flex;gap:4px;margin-left:auto;align-items:center;">
                        @if($booking->payment)
                            <span class="booking-card-status bs-green" style="font-size:0.65rem;">{{ $booking->payment->payment_method }}</span>
                        @endif
                        @if($booking->booking_status === 'paid')
                            @foreach($booking->passengers as $p)
                                @if($p->ticket)
                                    <a href="{{ route('tickets.download', $p->ticket) }}" target="_blank" style="display:inline-flex;align-items:center;gap:3px;padding:4px 10px;border:1px solid #059669;color:#059669;border-radius:6px;font-size:0.7rem;font-weight:700;text-decoration:none;" data-translate-en="Print" data-translate-id="Cetak">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:12px;height:12px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                        Print
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bookings-empty">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <h3 class="bookings-empty-title" data-translate-en="No results found" data-translate-id="Tidak ada hasil ditemukan">No results found</h3>
                <p class="bookings-empty-desc" data-translate-en="Try a different search term." data-translate-id="Coba kata pencarian lain.">Try a different search term.</p>
            </div>
        @endforelse
    </div>
</div>

@endsection
