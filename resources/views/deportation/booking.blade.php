@extends('layouts.app')
@section('title', 'Beli Tiket Deportasi')

@section('content')
<div class="deportation-page">
    <div style="margin-bottom:24px;">
        <a href="{{ route('deportation.dashboard') }}" style="color:#64748b;text-decoration:none;font-size:14px;display:inline-flex;align-items:center;gap:4px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><polyline points="15 18 9 12 15 6"/></svg>
            Kembali ke Dashboard
        </a>
    </div>

    <h1 style="font-size:26px;font-weight:700;color:#1e293b;margin-bottom:6px;">Beli Tiket Kapal Deportasi</h1>
    <p style="color:#64748b;margin-bottom:24px;">Titik penampungan: <strong>{{ $user->shelter_point_name }}</strong> | Tambang bas: <strong>+RM{{ number_format($user->shelter_fee, 2) }}</strong></p>

    @if($schedules->isEmpty())
        <div style="background:#fff;border-radius:16px;padding:60px 20px;text-align:center;box-shadow:0 1px 2px rgba(0,0,0,0.05);border:1px solid #e5e7eb;">
            <svg viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" style="width:56px;height:56px;margin:0 auto 16px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <p style="font-size:16px;color:#64748b;">Tiada jadual kapal tersedia buat masa ini.</p>
            <p style="font-size:14px;color:#94a3b8;margin-top:4px;">Sila semak semula kemudian.</p>
        </div>
    @else
        <div style="display:grid;gap:16px;">
            @foreach($schedules as $schedule)
            <div style="background:#fff;border-radius:16px;padding:20px;box-shadow:0 1px 2px rgba(0,0,0,0.05);border:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
                <div>
                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                        <span style="font-weight:700;font-size:16px;">{{ $schedule->route->origin_port }}</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" style="width:18px;height:18px;"><polyline points="5 12 19 12"/><polyline points="12 5 19 12 12 19"/></svg>
                        <span style="font-weight:700;font-size:16px;">{{ $schedule->route->destination_port }}</span>
                    </div>
                    <div style="display:flex;gap:20px;margin-top:8px;font-size:13px;color:#64748b;">
                        <span>{{ $schedule->vessel->name }}</span>
                        <span>{{ $schedule->departure_time->format('d M Y, H:i') }}</span>
                    </div>
                    <div style="display:flex;gap:16px;margin-top:6px;font-size:13px;">
                        <span style="color:#2563EB;font-weight:600;">VIP: RM{{ number_format($schedule->vip_price, 2) }}</span>
                        <span style="color:#059669;font-weight:600;">Regular: RM{{ number_format($schedule->regular_price, 2) }}</span>
                    </div>
                </div>
                <button onclick="openBookingForm('{{ $schedule->id }}', '{{ $schedule->route->origin_port }} → {{ $schedule->route->destination_port }}', '{{ $schedule->departure_time->format('d M Y, H:i') }}', '{{ $schedule->vessel->name }}', {{ $schedule->vip_price }}, {{ $schedule->regular_price }})"
                    style="background:#2563EB;color:#fff;border:none;padding:10px 24px;border-radius:8px;font-weight:600;font-size:14px;cursor:pointer;white-space:nowrap;">
                    Pilih Jadual
                </button>
            </div>
            @endforeach
        </div>
    @endif
</div>

{{-- Booking Modal --}}
<div id="bookingModal" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;padding:20px;">
    <div style="background:#fff;border-radius:16px;max-width:700px;width:100%;max-height:85vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,0.15);">
        <div style="padding:20px 24px;border-bottom:1px solid #e5e7eb;display:flex;justify-content:space-between;align-items:center;">
            <h2 style="font-size:18px;font-weight:700;">Butiran Tempahan</h2>
            <button onclick="closeBookingForm()" style="background:none;border:none;font-size:24px;cursor:pointer;color:#64748b;line-height:1;">&times;</button>
        </div>

        <form action="{{ route('deportation.booking.store') }}" method="POST" style="padding:20px 24px;">
            @csrf
            <input type="hidden" name="schedule_id" id="modalScheduleId">

            <div style="background:#f8fafc;border-radius:10px;padding:14px;margin-bottom:16px;">
                <div style="font-size:13px;color:#64748b;">Laluan</div>
                <div style="font-weight:700;" id="modalRoute">—</div>
                <div style="font-size:13px;color:#64748b;margin-top:4px;">Jadual / Kapal</div>
                <div style="font-weight:600;font-size:14px;" id="modalSchedule">—</div>
                <div style="display:flex;gap:16px;margin-top:8px;">
                    <span style="font-size:13px;color:#2563EB;font-weight:600;">VIP: RM<span id="modalVipPrice">0</span></span>
                    <span style="font-size:13px;color:#059669;font-weight:600;">Regular: RM<span id="modalRegularPrice">0</span></span>
                </div>
                <div style="margin-top:8px;font-size:13px;color:#ea580c;font-weight:600;">
                    + Tambang Bas ({{ $user->shelter_point_name }}): RM{{ number_format($user->shelter_fee, 2) }} (sekali sahaja)
                </div>
            </div>

            <div id="passengerContainer">
                <div class="passenger-row" style="border:1px solid #e5e7eb;border-radius:12px;padding:16px;margin-bottom:12px;">
                    <h4 style="font-size:14px;font-weight:700;margin-bottom:12px;display:flex;justify-content:space-between;">
                        Penumpang <span class="passenger-index">1</span>
                    </h4>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                        <input type="text" name="passengers[0][full_name]" placeholder="Nama Penuh *" required class="dep-input">
                        <select name="passengers[0][gender]" required class="dep-input">
                            <option value="">Jantina</option>
                            <option value="male">Lelaki</option>
                            <option value="female">Perempuan</option>
                        </select>
                        <input type="date" name="passengers[0][birth_date]" placeholder="Tarikh Lahir *" required class="dep-input">
                        <input type="text" name="passengers[0][nationality]" placeholder="Kewarganegaraan *" required class="dep-input">
                        <input type="text" name="passengers[0][passport_number]" placeholder="No. Pasport *" required class="dep-input">
                        <input type="text" name="passengers[0][phone_number]" placeholder="Telefon" class="dep-input">
                        <select name="passengers[0][ticket_class]" required class="dep-input" style="grid-column:span 2;">
                            <option value="">Pilih Kelas Tiket</option>
                            <option value="vip">VIP</option>
                            <option value="regular">Regular</option>
                        </select>
                    </div>
                </div>
            </div>

            <button type="button" onclick="addPassenger()" id="addPassengerBtn" style="background:#f1f5f9;border:1px dashed #cbd5e1;border-radius:10px;padding:12px;width:100%;text-align:center;font-weight:600;color:#64748b;cursor:pointer;margin-bottom:16px;font-size:14px;">
                + Tambah Penumpang (Maks 8)
            </button>

            <div style="background:#fefce8;border-radius:10px;padding:14px;margin-bottom:16px;border:1px solid #fef08a;">
                <h4 style="font-size:13px;font-weight:700;color:#a16207;margin-bottom:8px;">Ringkasan Kos</h4>
                <div style="font-size:13px;color:#64748b;display:flex;justify-content:space-between;"><span>Tiket Kapal:</span> <span id="summaryTicket">RM0.00</span></div>
                <div style="font-size:13px;color:#64748b;display:flex;justify-content:space-between;"><span>Tambang Bas ({{ $user->shelter_point_name }}):</span> <span>RM{{ number_format($user->shelter_fee, 2) }}</span></div>
                <div style="font-size:13px;color:#64748b;display:flex;justify-content:space-between;"><span>Insurans (RM10/pax):</span> <span id="summaryInsurance">RM0.00</span></div>
                <div style="border-top:1px solid #fef08a;margin-top:8px;padding-top:8px;font-weight:700;font-size:14px;display:flex;justify-content:space-between;"><span>Jumlah:</span> <span id="summaryTotal">RM0.00</span></div>
            </div>

            <button type="submit" style="background:#2563EB;color:#fff;border:none;padding:14px;width:100%;border-radius:10px;font-weight:700;font-size:15px;cursor:pointer;">
            Lanjutkan ke Pembayaran
            </button>
        </form>
    </div>
</div>

<style>
.dep-input {
    padding: 10px 12px;
    border: 1.5px solid #d1d5db;
    border-radius: 8px;
    font-size: 13px;
    outline: none;
    width: 100%;
    transition: border-color 0.15s;
    font-family: inherit;
}
.dep-input:focus { border-color: #2563EB; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
</style>

<script>
let passengerCount = 1;
let vipPrice = 0;
let regularPrice = 0;

function openBookingForm(scheduleId, route, schedule, vessel, vip, regular) {
    document.getElementById('modalScheduleId').value = scheduleId;
    document.getElementById('modalRoute').textContent = route;
    document.getElementById('modalSchedule').textContent = schedule + ' | ' + vessel;
    document.getElementById('modalVipPrice').textContent = vip;
    document.getElementById('modalRegularPrice').textContent = regular;
    vipPrice = vip;
    regularPrice = regular;
    document.getElementById('bookingModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
    updateSummary();
}

function closeBookingForm() {
    document.getElementById('bookingModal').style.display = 'none';
    document.body.style.overflow = '';
}

function addPassenger() {
    if (passengerCount >= 8) return;
    const container = document.getElementById('passengerContainer');
    const row = document.createElement('div');
    row.className = 'passenger-row';
    row.style.cssText = 'border:1px solid #e5e7eb;border-radius:12px;padding:16px;margin-bottom:12px;';
    row.innerHTML = `
        <h4 style="font-size:14px;font-weight:700;margin-bottom:12px;display:flex;justify-content:space-between;">
            Penumpang <span class="passenger-index">${passengerCount + 1}</span>
            <button type="button" onclick="this.closest('.passenger-row').remove(); passengerCount--; reindexPassengers(); updateSummary();" style="background:#fef2f2;color:#dc2626;border:none;padding:4px 10px;border-radius:6px;font-size:12px;cursor:pointer;">Buang</button>
        </h4>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <input type="text" name="passengers[${passengerCount}][full_name]" placeholder="Nama Penuh *" required class="dep-input">
            <select name="passengers[${passengerCount}][gender]" required class="dep-input">
                <option value="">Jantina</option>
                <option value="male">Lelaki</option>
                <option value="female">Perempuan</option>
            </select>
            <input type="date" name="passengers[${passengerCount}][birth_date]" placeholder="Tarikh Lahir *" required class="dep-input">
            <input type="text" name="passengers[${passengerCount}][nationality]" placeholder="Kewarganegaraan *" required class="dep-input">
            <input type="text" name="passengers[${passengerCount}][passport_number]" placeholder="No. Pasport *" required class="dep-input">
            <input type="text" name="passengers[${passengerCount}][phone_number]" placeholder="Telefon" class="dep-input">
            <select name="passengers[${passengerCount}][ticket_class]" required class="dep-input" style="grid-column:span 2;" onchange="updateSummary()">
                <option value="">Pilih Kelas Tiket</option>
                <option value="vip">VIP</option>
                <option value="regular">Regular</option>
            </select>
        </div>
    `;
    container.appendChild(row);
    passengerCount++;
    updateSummary();

    if (passengerCount >= 8) {
        document.getElementById('addPassengerBtn').style.display = 'none';
    }
}

function reindexPassengers() {
    document.querySelectorAll('.passenger-row').forEach((row, i) => {
        row.querySelector('.passenger-index').textContent = i + 1;
        row.querySelectorAll('[name^="passengers["]').forEach(input => {
            const name = input.getAttribute('name');
            input.setAttribute('name', name.replace(/passengers\[\d+\]/, `passengers[${i}]`));
        });
    });
    document.getElementById('addPassengerBtn').style.display = passengerCount < 8 ? 'block' : 'none';
}

function updateSummary() {
    const rows = document.querySelectorAll('.passenger-row');
    const count = rows.length;
    let ticketTotal = 0;

    rows.forEach(row => {
        const classSelect = row.querySelector('[name$="[ticket_class]"]');
        if (classSelect && classSelect.value === 'vip') ticketTotal += vipPrice;
        else if (classSelect && classSelect.value === 'regular') ticketTotal += regularPrice;
    });

    document.getElementById('summaryTicket').textContent = 'RM' + ticketTotal.toFixed(2);
    document.getElementById('summaryInsurance').textContent = 'RM' + (count * 10).toFixed(2);
    const shelter = {{ $user->shelter_fee }};
    document.getElementById('summaryTotal').textContent = 'RM' + (ticketTotal + count * 10 + shelter).toFixed(2);
}

document.getElementById('bookingModal').addEventListener('click', function(e) {
    if (e.target === this) closeBookingForm();
});
</script>
@endsection
