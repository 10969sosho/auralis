@extends('layouts.app')
@section('title', 'Jadwal Kapal')

@section('content')
<section class="guest-section" style="padding:80px 0;border-radius:16px;margin-bottom:40px;background:linear-gradient(135deg, rgba(11,125,218,0.85), rgba(78,162,255,0.75)), url('{{ asset("images/hero-banner.jpeg") }}') center/cover no-repeat;min-height:400px;display:flex;align-items:center;">
    <div class="guest-container" style="max-width:1280px;margin:0 auto;padding:0 24px;">
        <p class="guest-section-label" style="color:rgba(255,255,255,0.7);text-align:center;" data-translate-en="Ship Schedule" data-translate-id="Jadwal Kapal">Ship Schedule</p>
        <h2 class="guest-section-title" style="text-align:center;color:#fff;" data-translate-en="Ferry Schedule" data-translate-id="Jadwal Feri">Ferry Schedule</h2>
        <p class="guest-section-subtitle" style="text-align:center;color:rgba(255,255,255,0.8);" data-translate-en="Browse our ferry schedules and check availability for your travel date." data-translate-id="Jelajahi jadwal feri kami dan periksa ketersediaan untuk tanggal perjalanan Anda.">Browse our ferry schedules and check availability for your travel date.</p>

        {{-- Date Filter --}}
        <div style="max-width:500px;margin:32px auto 0;display:flex;gap:12px;align-items:center;background:rgba(255,255,255,0.15);backdrop-filter:blur(10px);border-radius:12px;padding:16px 20px;">
            <label for="schedule-date" style="color:#fff;font-weight:600;font-size:14px;white-space:nowrap;" data-translate-en="Select Date:" data-translate-id="Pilih Tanggal:">Select Date:</label>
            <input type="date" id="schedule-date" value="{{ $date }}" style="flex:1;padding:10px 14px;border:none;border-radius:8px;font-size:15px;font-family:inherit;background:#fff;color:#252B42;outline:none;">
            <button onclick="filterByDate()" style="padding:10px 24px;background:#0E9AEF;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;transition:background 0.2s;font-family:inherit;" onmouseover="this.style.background='#0b86d4'" onmouseout="this.style.background='#0E9AEF'" data-translate-en="View" data-translate-id="Lihat">View</button>
        </div>
    </div>
</section>

<div class="guest-container" style="max-width:1280px;margin:0 auto;padding:0 24px;">
    <div style="margin-bottom:32px;">
        <p style="font-size:15px;color:#6C757D;" data-translate-en="Showing schedules for" data-translate-id="Menampilkan jadwal untuk">
            Showing schedules for <strong style="color:#252B42;">{{ \Carbon\Carbon::parse($date)->format('l, F d, Y') }}</strong>
        </p>
    </div>

    @if($schedules->count() > 0)
        <div class="schedule-grid" style="margin-bottom:40px;">
            @foreach($schedules as $schedule)
                <div class="schedule-card" data-aos="flip-up" data-aos-duration="600" data-aos-delay="{{ $loop->index * 80 }}">
                    <div class="schedule-route">{{ $schedule->route->origin_port }} → {{ $schedule->route->destination_port }}</div>
                    <div class="schedule-detail">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span data-translate-en="Departure:" data-translate-id="Keberangkatan:">Departure: <span class="schedule-time">{{ \Carbon\Carbon::parse($schedule->departure_time)->format('H:i') }} WITA</span></span>
                    </div>
                    <div class="schedule-detail">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span data-translate-en="Arrival:" data-translate-id="Kedatangan:">Arrival: <span class="schedule-time">{{ \Carbon\Carbon::parse($schedule->arrival_time)->format('H:i') }} WITA</span></span>
                    </div>
                    <div class="schedule-detail">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <span data-translate-en="Vessel:" data-translate-id="Kapal:">Vessel: <span class="schedule-time">{{ $schedule->vessel->name }}</span></span>
                    </div>
                    <div style="margin-top:16px;display:flex;gap:8px;">
                        <span style="font-size:13px;background:#E8F4FD;color:#0E9AEF;padding:4px 12px;border-radius:20px;font-weight:500;">RM {{ number_format($schedule->regular_price, 2) }}</span>
                        @if($schedule->vip_price)
                            <span style="font-size:13px;background:#FFF3E0;color:#E65100;padding:4px 12px;border-radius:20px;font-weight:500;">VIP: RM {{ number_format($schedule->vip_price, 2) }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center" style="margin-bottom:60px;">
            <a href="{{ route('schedules', ['date' => $date]) }}" class="ticket-book-btn" style="display:inline-flex;margin:0 auto;" data-translate-en="Book Ticket for" data-translate-id="Pesan Tiket untuk">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                Book Ticket for {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}
            </a>
        </div>
    @else
        <div style="text-align:center;padding:60px 24px;background:#fff;border-radius:16px;border:1px solid #E9ECEF;margin-bottom:60px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="#6C757D" stroke-width="1.5" style="width:64px;height:64px;margin-bottom:16px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <h3 style="font-size:20px;color:#252B42;margin-bottom:8px;" data-translate-en="No Schedules Available" data-translate-id="Tidak Ada Jadwal">No Schedules Available</h3>
            <p style="color:#6C757D;font-size:15px;max-width:400px;margin:0 auto 24px;" data-translate-en="There are no ferry schedules available for. Please try a different date." data-translate-id="Tidak ada jadwal feri yang tersedia untuk. Silakan coba tanggal lain.">
                There are no ferry schedules available for {{ \Carbon\Carbon::parse($date)->format('l, F d, Y') }}. Please try a different date.
            </p>
            <button onclick="document.getElementById('schedule-date').valueAsDate = new Date(Date.now() + 86400000);filterByDate();" style="padding:12px 28px;background:#0E9AEF;color:#fff;border:none;border-radius:8px;font-size:15px;font-weight:600;cursor:pointer;font-family:inherit;" data-translate-en="View Tomorrow's Schedule" data-translate-id="Lihat Jadwal Besok">View Tomorrow's Schedule</button>
        </div>
    @endif

    {{-- Vessel Information --}}
    @if($vessels->count() > 0)
    <div style="margin-bottom:60px;">
        <h2 style="font-size:24px;font-weight:700;color:#252B42;margin-bottom:24px;text-align:center;" data-translate-en="Our Vessels" data-translate-id="Kapal Kami">Our Vessels</h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:24px;">
            @foreach($vessels as $vessel)
                <div style="background:#fff;border-radius:12px;border:1px solid #E9ECEF;padding:24px;text-align:center;transition:box-shadow 0.2s,transform 0.2s;" onmouseover="this.style.boxShadow='0 8px 24px rgba(0,0,0,0.06)';this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='none';this.style.transform='none'">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#0E9AEF" stroke-width="1.5" style="width:48px;height:48px;margin-bottom:12px;">
                        <path d="M2 21h20M6 18l2-6h8l2 6M9 12V7M15 12V7M12 7V3"/>
                        <path d="M5 7h14l-2 5H7L5 7Z"/>
                    </svg>
                    <h3 style="font-size:18px;font-weight:700;color:#252B42;margin-bottom:4px;">{{ $vessel->name }}</h3>
                    @if($vessel->capacity)
                        <p style="font-size:14px;color:#6C757D;" data-translate-en="Capacity:" data-translate-id="Kapasitas:">Capacity: {{ $vessel->capacity }} passengers</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<script>
function filterByDate() {
    const dateInput = document.getElementById('schedule-date');
    if (dateInput.value) {
        window.location.href = '{{ route("jadwal") }}?date=' + dateInput.value;
    }
}

// Allow Enter key on date input
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('schedule-date');
    if (dateInput) {
        dateInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') filterByDate();
        });
    }
});
</script>
@endsection