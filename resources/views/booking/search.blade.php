@extends('layouts.app')
@section('title', 'Search Schedules')
@section('page_class', 'search-schedules-page')
@section('full_width', true)

@section('content')
{{-- Search Form --}}
<div class="search-hero">
    <div class="search-hero-content">
        <p class="search-hero-label">Book Ticket</p>
        <h1 class="search-hero-title">Ferry Schedule</h1>
        <p class="search-hero-desc">Browse all available ferry schedules. Filter by route to find your trip.</p>

        <form action="{{ route('schedules') }}" method="GET" id="searchForm" class="search-form">
            <div class="search-form-grid">
                <div>
                    <label class="search-label">Origin Port</label>
                    <select name="origin_port" id="origin_port" class="search-input" onchange="updateDestinations()">
                        <option value="">All Ports</option>
                        @foreach($routes->unique('origin_port') as $route)
                            <option value="{{ $route->origin_port }}" {{ request('origin_port') === $route->origin_port ? 'selected' : '' }}>{{ $route->origin_port }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="search-label">Destination Port</label>
                    <select name="destination_port" id="destination_port" class="search-input">
                        <option value="">All Ports</option>
                        @php
                            $destPorts = isset($destinationPorts) && $destinationPorts->isNotEmpty() ? $destinationPorts : $routes->unique('destination_port')->pluck('destination_port');
                        @endphp
                        @foreach($destPorts as $port)
                            <option value="{{ $port }}" {{ request('destination_port') === $port ? 'selected' : '' }}>{{ $port }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="search-label">Passengers</label>
                    <div class="search-counter-group">
                        <button type="button" class="search-counter-btn" onclick="adjustPax(-1)">−</button>
                        <span class="search-counter-value" id="paxCount">{{ request('passenger_count', 1) }}</span>
                        <button type="button" class="search-counter-btn" onclick="adjustPax(1)">+</button>
                    </div>
                    <input type="hidden" name="passenger_count" id="paxInput" value="{{ request('passenger_count', 1) }}">
                </div>
                <button type="submit" class="search-submit">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    Filter
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Results --}}
<div class="search-results">
    <div class="search-results-content">
    @if(count($schedules) > 0)
        <div class="search-results-header">
            <p class="search-results-count">{{ count($schedules) }} schedule{{ count($schedules) > 1 ? 's' : '' }} found</p>
        </div>
        <div class="search-results-list">
            @foreach($schedules as $schedule)
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
                        </div>
                        <div class="ticket-route-line">
                            <div class="ticket-route-dot"></div>
                            <div class="ticket-route-arrow"></div>
                            <div class="ticket-route-dot"></div>
                        </div>
                        <div class="ticket-route-point ticket-route-dest">
                            <span class="ticket-route-label">Arrival</span>
                            <span class="ticket-route-port">{{ $schedule->route->destination_port }}</span>
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
                            <span class="ticket-price-value">RM {{ number_format($schedule->vip_price, 2) }}</span>
                        </div>
                        <div class="ticket-price-item">
                            <span class="ticket-price-label">Regular</span>
                            <span class="ticket-price-value">RM {{ number_format($schedule->regular_price, 2) }}</span>
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
                                        (RM {{ $applicablePromo->value }} OFF)
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
            @endforeach
        </div>
    @else
        <div class="search-empty">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="search-empty-icon"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <h3 class="search-empty-title">No Schedules Found</h3>
            <p class="search-empty-desc">Try adjusting your filter criteria.</p>
        </div>
    @endif
    </div>
</div>

<script>
const routeMap = {
    @foreach($routes->groupBy('origin_port') as $origin => $group)
        "{{ $origin }}": [
            @foreach($group as $route)
                "{{ $route->destination_port }}",
            @endforeach
        ],
    @endforeach
};

function updateDestinations() {
    const originSelect = document.getElementById('origin_port');
    const destSelect = document.getElementById('destination_port');
    const selectedOrigin = originSelect.value;
    const currentDest = "{{ request('destination_port') }}";

    const destinations = routeMap[selectedOrigin] || [];
    destSelect.innerHTML = '<option value="">Select Port</option>';
    destinations.forEach(function(port) {
        const selected = (port === currentDest) ? 'selected' : '';
        destSelect.innerHTML += '<option value="' + port + '" ' + selected + '>' + port + '</option>';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('origin_port').value) {
        updateDestinations();
    }
});

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
