@extends('layouts.app')
@section('title', 'Cari Tiket - Booking')

@section('content')
<div class="guest-section booking-section" style="padding:40px 0;">
    <div class="guest-container">
        <p class="guest-section-label">Booking Tiket</p>
        <h2 class="guest-section-title" style="font-size:32px;">Pesan Tiket Kapal</h2>
        <p class="guest-section-subtitle">Cari jadwal kapal ferry sesuai rute dan tanggal keberangkatan Anda.</p>
        <div class="booking-box">
            <form action="{{ route('schedules') }}" method="GET">
                <div class="booking-form-grid">
                    <div class="booking-form-full">
                        <label class="booking-label">Tanggal Keberangkatan</label>
                        <input type="date" name="departure_date" value="{{ request('departure_date') }}" class="booking-input" required min="{{ date('Y-m-d') }}">
                    </div>
                    <div>
                        <label class="booking-label">Pelabuhan Asal</label>
                        <select name="origin_port" id="origin_port" class="booking-input" required>
                            <option value="">Pilih Pelabuhan</option>
                            @foreach($routes->unique('origin_port') as $route)
                                <option value="{{ $route->origin_port }}" {{ request('origin_port') === $route->origin_port ? 'selected' : '' }}>{{ $route->origin_port }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="booking-label">Pelabuhan Tujuan</label>
                        <select name="destination_port" id="destination_port" class="booking-input" required>
                            <option value="">Pilih Pelabuhan</option>
                            @foreach($routes->unique('destination_port') as $route)
                                <option value="{{ $route->destination_port }}" {{ request('destination_port') === $route->destination_port ? 'selected' : '' }}>{{ $route->destination_port }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="booking-label">Jumlah Penumpang</label>
                        <div class="booking-counter-group">
                            <button type="button" class="booking-counter-btn" onclick="adjustPax(-1)">−</button>
                            <span class="booking-counter-value" id="paxCount">{{ request('passenger_count', 1) }}</span>
                            <button type="button" class="booking-counter-btn" onclick="adjustPax(1)">+</button>
                        </div>
                        <input type="hidden" name="passenger_count" id="paxInput" value="{{ request('passenger_count', 1) }}">
                    </div>
                    <button type="submit" class="booking-submit">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        Cari Tiket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="guest-container" style="margin-bottom:60px;">
<div class="results-section">
    @if(count($schedules) > 0)
        <div class="results-header">
            <p class="results-count">{{ count($schedules) }} ferry schedule{{ count($schedules) > 1 ? 's' : '' }} found</p>
        </div>
    @endif

    <div class="results-list">
        @forelse($schedules as $schedule)
            <div class="ticket-card">
                <div class="ticket-card-body">
                    <div class="ticket-top">
                        <div class="ticket-vessel">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ticket-vessel-icon"><path d="M2 21h20M6 18l2-6h8l2 6M9 12V7M15 12V7M12 7V3"/><path d="M5 7h14l-2 5H7L5 7Z"/><circle cx="12" cy="7" r="1.5"/></svg>
                            <span class="ticket-vessel-name">{{ $schedule->vessel->name }}</span>
                            <span class="ticket-vessel-badge">International Ferry</span>
                        </div>
                    </div>

                    <div class="ticket-route">
                        <div class="ticket-route-point ticket-route-origin">
                            <span class="ticket-route-label">Departure</span>
                            <span class="ticket-route-port">{{ $schedule->route->origin_port }}</span>
                            <span class="ticket-route-flag">&#127477;&#127472;</span>
                        </div>
                        <div class="ticket-route-line">
                            <div class="ticket-route-dot"></div>
                            <div class="ticket-route-arrow"></div>
                            <div class="ticket-route-dot"></div>
                        </div>
                        <div class="ticket-route-point ticket-route-dest">
                            <span class="ticket-route-label">Arrival</span>
                            <span class="ticket-route-port">{{ $schedule->route->destination_port }}</span>
                            <span class="ticket-route-flag">&#127474;&#127473;</span>
                        </div>
                    </div>

                    <div class="ticket-times">
                        <div class="ticket-time-block">
                            <span class="ticket-time-value">{{ $schedule->departure_time->format('H:i') }}</span>
                            <span class="ticket-time-date">{{ $schedule->departure_time->format('d M Y') }}</span>
                        </div>
                        <div class="ticket-time-duration">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            <span>{{ $schedule->departure_time->diffInMinutes($schedule->arrival_time) >= 60 ? floor($schedule->departure_time->diffInMinutes($schedule->arrival_time) / 60).'h '.($schedule->departure_time->diffInMinutes($schedule->arrival_time) % 60).'m' : $schedule->departure_time->diffInMinutes($schedule->arrival_time).'m' }}</span>
                        </div>
                        <div class="ticket-time-block ticket-time-block-right">
                            <span class="ticket-time-value">{{ $schedule->arrival_time->format('H:i') }}</span>
                            <span class="ticket-time-date">{{ $schedule->arrival_time->format('d M Y') }}</span>
                        </div>
                    </div>

                    @php
                        $vipLeft = $schedule->vessel->vip_capacity - (int)$schedule->vipBooked;
                        $regularLeft = $schedule->vessel->regular_capacity - (int)$schedule->regularBooked;
                        $vipPct = $schedule->vessel->vip_capacity > 0 ? ($vipLeft / $schedule->vessel->vip_capacity) * 100 : 0;
                        $regularPct = $schedule->vessel->regular_capacity > 0 ? ($regularLeft / $schedule->vessel->regular_capacity) * 100 : 0;
                        $vipSeatClass = $vipPct > 50 ? 'seat-good' : ($vipPct > 20 ? 'seat-warn' : 'seat-low');
                        $regularSeatClass = $regularPct > 50 ? 'seat-good' : ($regularPct > 20 ? 'seat-warn' : 'seat-low');
                    @endphp

                    <div class="ticket-seats">
                        <div class="ticket-seat-item">
                            <span class="ticket-seat-label">
                                <span class="ticket-seat-dot vip-dot"></span>
                                VIP
                            </span>
                            <span class="ticket-seat-count {{ $vipSeatClass }}">{{ $vipLeft }} left</span>
                            <div class="ticket-seat-bar">
                                <div class="ticket-seat-fill {{ $vipSeatClass }}" style="width: {{ $vipPct }}%"></div>
                            </div>
                        </div>
                        <div class="ticket-seat-item">
                            <span class="ticket-seat-label">
                                <span class="ticket-seat-dot regular-dot"></span>
                                Regular
                            </span>
                            <span class="ticket-seat-count {{ $regularSeatClass }}">{{ $regularLeft }} left</span>
                            <div class="ticket-seat-bar">
                                <div class="ticket-seat-fill {{ $regularSeatClass }}" style="width: {{ $regularPct }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="ticket-prices">
                        <div class="ticket-price-item">
                            <span class="ticket-price-label">VIP</span>
                            <span class="ticket-price-value">MYR {{ number_format($schedule->vip_price, 2) }}</span>
                        </div>
                        <div class="ticket-price-item">
                            <span class="ticket-price-label">Regular</span>
                            <span class="ticket-price-value">MYR {{ number_format($schedule->regular_price, 2) }}</span>
                        </div>
                    </div>

                    @php
                        $applicablePromo = $autoPromos->first(fn($p) => $p->isApplicableToSchedule($schedule, (int)(request('passenger_count', 1)), 'regular'));
                    @endphp

                    <div class="ticket-bottom">
                        <div class="ticket-bottom-left">
                            @if($applicablePromo)
                                <span class="ticket-promo-badge">
                                    <span>🔥</span>
                                    {{ $applicablePromo->name }}
                                    @if($applicablePromo->type === 'percentage')
                                        ({{ $applicablePromo->value }}% OFF)
                                    @else
                                        (MYR {{ $applicablePromo->value }} OFF)
                                    @endif
                                </span>
                            @endif
                        </div>
                        <div class="ticket-bottom-right">
                            @if(!$schedule->isFullyBooked && !$schedule->isH6Passed)
                                <a href="{{ route('booking.create', $schedule) }}?passenger_count={{ request('passenger_count', 1) }}" class="ticket-book-btn">
                                    Book Now
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                </a>
                            @elseif($schedule->isH6Passed)
                                <span class="ticket-status-closed">Booking Closed</span>
                            @else
                                <span class="ticket-status-closed">Fully Booked</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="empty-icon"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <h3 class="empty-title">No schedules found</h3>
                <p class="empty-desc">Try adjusting your search criteria or choose a different date.</p>
            </div>
        @endforelse
    </div>
</div>
</div>

<script>
function adjustPax(delta) {
    var countEl = document.getElementById('paxCount');
    var inputEl = document.getElementById('paxInput');
    var current = parseInt(countEl.textContent);
    var newVal = Math.max(1, Math.min(8, current + delta));
    countEl.textContent = newVal;
    inputEl.value = newVal;
}
</script>
@endsection
