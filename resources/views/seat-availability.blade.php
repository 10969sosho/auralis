@extends('layouts.app')
@section('title', 'Seat Availability')

@section('content')
<div class="seat-avail-page">
    <div class="sa-header">
        <div>
            <h1 class="sa-title" data-translate-en="Seat Availability" data-translate-id="Ketersediaan Kursi">Seat Availability</h1>
            <p class="sa-sub" data-translate-en="Real-time seat availability for all upcoming schedules" data-translate-id="Ketersediaan kursi real-time untuk semua jadwal mendatang">Real-time seat availability for all upcoming schedules</p>
        </div>
    </div>

    <div class="sa-grid">
        @forelse($schedules as $schedule)
            @php
                $vipBooked = (int)$schedule->vipBooked;
                $regularBooked = (int)$schedule->regularBooked;
                $vipCap = $schedule->vessel->vip_capacity;
                $regCap = $schedule->vessel->regular_capacity;
                $totalCap = $vipCap + $regCap;
                $totalBooked = $vipBooked + $regularBooked;
                $totalAvail = $totalCap - $totalBooked;
                $occPct = $totalCap > 0 ? round(($totalBooked / $totalCap) * 100, 1) : 0;
            @endphp
            <div class="sa-card" id="schedule-{{ $schedule->id }}" data-schedule-id="{{ $schedule->id }}">
                <div class="sa-card-top">
                    <div class="sa-card-vessel">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 21h20M6 18l2-6h8l2 6M9 12V7M15 12V7M12 7V3"/><path d="M5 7h14l-2 5H7L5 7Z"/><circle cx="12" cy="7" r="1.5"/></svg>
                        <span class="sa-vessel-name">{{ $schedule->vessel->name }}</span>
                    </div>
                    <span class="sa-departure" data-translate-en="Departure:" data-translate-id="Keberangkatan:">Departure: {{ $schedule->departure_time->format('d M Y, H:i') }}</span>
                </div>

                <div class="sa-route">
                    <span>{{ $schedule->route->origin_port }}</span>
                    <span class="sa-arrow">→</span>
                    <span>{{ $schedule->route->destination_port }}</span>
                </div>

                <div class="sa-stats">
                    <div class="sa-stat sa-stat-total">
                        <span class="sa-stat-num">{{ $totalCap }}</span>
                        <span class="sa-stat-label" data-translate-en="Total Seats" data-translate-id="Total Kursi">Total Seat</span>
                    </div>
                    <div class="sa-stat sa-stat-booked">
                        <span class="sa-stat-num" id="booked-{{ $schedule->id }}">{{ $totalBooked }}</span>
                        <span class="sa-stat-label" data-translate-en="Booked" data-translate-id="Dipesan">Seat Booked</span>
                    </div>
                    <div class="sa-stat sa-stat-paid">
                        <span class="sa-stat-num" id="paid-{{ $schedule->id }}">—</span>
                        <span class="sa-stat-label" data-translate-en="Paid" data-translate-id="Dibayar">Seat Paid</span>
                    </div>
                    <div class="sa-stat sa-stat-avail">
                        <span class="sa-stat-num" id="avail-{{ $schedule->id }}">{{ $totalAvail }}</span>
                        <span class="sa-stat-label" data-translate-en="Available" data-translate-id="Tersedia">Seat Available</span>
                    </div>
                </div>

                <div class="sa-classes">
                    <div class="sa-class">
                        <div class="sa-class-header">
                            <span class="sa-class-dot vip-dot"></span>
                            <span data-translate-en="VIP" data-translate-id="VIP">VIP</span>
                            <span class="sa-class-price">RM {{ number_format($schedule->vip_price, 2) }}</span>
                        </div>
                        <div class="sa-class-bar">
                            @php $vipPct = $vipCap > 0 ? ($vipBooked / $vipCap) * 100 : 0; @endphp
                            <div class="sa-class-fill" style="width: {{ $vipPct }}%" data-class="vip"></div>
                        </div>
                        <span class="sa-class-info" id="vip-info-{{ $schedule->id }}" data-translate-en="booked" data-translate-id="dipesan">{{ $vipBooked }}/{{ $vipCap }} booked</span>
                    </div>
                    <div class="sa-class">
                        <div class="sa-class-header">
                            <span class="sa-class-dot regular-dot"></span>
                            <span data-translate-en="Regular" data-translate-id="Regular">Regular</span>
                            <span class="sa-class-price">RM {{ number_format($schedule->regular_price, 2) }}</span>
                        </div>
                        <div class="sa-class-bar">
                            @php $regPct = $regCap > 0 ? ($regularBooked / $regCap) * 100 : 0; @endphp
                            <div class="sa-class-fill" style="width: {{ $regPct }}%" data-class="regular"></div>
                        </div>
                        <span class="sa-class-info" id="reg-info-{{ $schedule->id }}" data-translate-en="booked" data-translate-id="dipesan">{{ $regularBooked }}/{{ $regCap }} booked</span>
                    </div>
                </div>

                <div class="sa-occupancy">
                    <div class="sa-occ-bar">
                        <div class="sa-occ-fill" style="width: {{ $occPct }}%"></div>
                    </div>
                    <span class="sa-occ-text" data-translate-en="% Occupied" data-translate-id="% Terisi">{{ $occPct }}% Occupied</span>
                </div>
            </div>
        @empty
            <div class="empty-state" style="grid-column:1/-1;">
                <h3 data-translate-en="No upcoming schedules" data-translate-id="Tidak ada jadwal mendatang">No upcoming schedules</h3>
                <p data-translate-en="Check back later for available ferry schedules." data-translate-id="Periksa kembali nanti untuk jadwal feri yang tersedia.">Check back later for available ferry schedules.</p>
            </div>
        @endforelse
    </div>
</div>

<script>
var scheduleIds = {!! json_encode($schedules->pluck('id')) !!};

if (scheduleIds.length > 0 && typeof Echo !== 'undefined') {
    scheduleIds.forEach(function(id) {
        Echo.channel('schedule.' + id)
            .listen('.seat.availability.updated', function(e) {
                updateCard(e.data);
            });
    });
}

function updateCard(data) {
    var card = document.getElementById('schedule-' + data.schedule_id);
    if (!card) return;

    var bookedEl = document.getElementById('booked-' + data.schedule_id);
    var availEl = document.getElementById('avail-' + data.schedule_id);
    var paidEl = document.getElementById('paid-' + data.schedule_id);

    if (bookedEl) bookedEl.textContent = data.total.booked;
    if (availEl) availEl.textContent = data.total.available || data.total.remaining;
    if (paidEl && data.total.paid !== undefined) paidEl.textContent = data.total.paid;

    var vipInfo = document.getElementById('vip-info-' + data.schedule_id);
    if (vipInfo) vipInfo.textContent = data.vip.booked + '/' + data.vip.capacity + ' booked';

    var regInfo = document.getElementById('reg-info-' + data.schedule_id);
    if (regInfo) regInfo.textContent = data.regular.booked + '/' + data.regular.capacity + ' booked';

    var vipBars = card.querySelectorAll('.sa-class-fill[data-class="vip"]');
    vipBars.forEach(function(bar) {
        bar.style.width = (data.vip.capacity > 0 ? (data.vip.booked / data.vip.capacity) * 100 : 0) + '%';
    });

    var regBars = card.querySelectorAll('.sa-class-fill[data-class="regular"]');
    regBars.forEach(function(bar) {
        bar.style.width = (data.regular.capacity > 0 ? (data.regular.booked / data.regular.capacity) * 100 : 0) + '%';
    });

    var occFill = card.querySelector('.sa-occ-fill');
    if (occFill) occFill.style.width = data.total.occupancy_percentage + '%';

    var occText = card.querySelector('.sa-occ-text');
    if (occText) occText.textContent = data.total.occupancy_percentage + '% Occupied';
}
</script>

<style>
.seat-avail-page { padding: 24px 0; }
.sa-header { margin-bottom: 24px; }
.sa-title { font-size: 24px; font-weight: 700; }
.sa-sub { color: #6b7280; margin-top: 4px; }
.sa-grid { display: grid; gap: 16px; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); }
.sa-card { background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); padding: 20px; }
.sa-card-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; }
.sa-card-vessel { display: flex; align-items: center; gap: 6px; }
.sa-card-vessel svg { width: 18px; height: 18px; color: #2563EB; }
.sa-vessel-name { font-weight: 600; }
.sa-departure { font-size: 0.8rem; color: #6b7280; }
.sa-route { display: flex; align-items: center; gap: 8px; font-size: 0.9rem; color: #374151; margin-bottom: 14px; }
.sa-arrow { color: #2563EB; font-weight: 700; }
.sa-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-bottom: 14px; }
.sa-stat { text-align: center; padding: 10px 6px; border-radius: 8px; background: #f9fafb; }
.sa-stat-num { display: block; font-size: 1.3rem; font-weight: 700; }
.sa-stat-label { display: block; font-size: 0.65rem; color: #6b7280; margin-top: 2px; text-transform: uppercase; letter-spacing: 0.5px; }
.sa-stat-total .sa-stat-num { color: #374151; }
.sa-stat-booked .sa-stat-num { color: #2563EB; }
.sa-stat-paid .sa-stat-num { color: #059669; }
.sa-stat-avail .sa-stat-num { color: #7C3AED; }
.sa-classes { display: flex; flex-direction: column; gap: 10px; margin-bottom: 14px; }
.sa-class-header { display: flex; align-items: center; gap: 6px; font-size: 0.85rem; }
.sa-class-dot { width: 8px; height: 8px; border-radius: 50%; }
.vip-dot { background: #F59E0B; }
.regular-dot { background: #0E9AEF; }
.sa-class-price { margin-left: auto; font-weight: 600; color: #252B42; }
.sa-class-bar { width: 100%; height: 6px; background: #E9ECEF; border-radius: 3px; overflow: hidden; margin-top: 4px; }
.sa-class-fill { height: 100%; border-radius: 3px; }
.sa-class-fill[data-class="vip"] { background: #F59E0B; }
.sa-class-fill[data-class="regular"] { background: #0E9AEF; }
.sa-class-info { font-size: 0.7rem; color: #6C757D; }
.sa-occupancy { display: flex; align-items: center; gap: 10px; }
.sa-occ-bar { flex: 1; height: 8px; background: #E9ECEF; border-radius: 4px; overflow: hidden; }
.sa-occ-fill { height: 100%; background: linear-gradient(90deg, #0E9AEF, #4EA2FF); border-radius: 4px; transition: width 0.5s ease; }
.sa-occ-text { font-size: 0.75rem; color: #6C757D; white-space: nowrap; }
</style>
@endsection
