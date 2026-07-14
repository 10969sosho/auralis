@extends('layouts.app')
@section('title', 'Buy Deportation Ticket')

@section('content')
<div class="deportation-page">
    <div style="margin-bottom:24px;">
        <a href="{{ route('deportation.dashboard') }}" style="color:#64748b;text-decoration:none;font-size:14px;display:inline-flex;align-items:center;gap:4px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><polyline points="15 18 9 12 15 6"/></svg>
            Back to Dashboard
        </a>
    </div>

    <h1 style="font-size:26px;font-weight:700;color:#1e293b;margin-bottom:6px;">Buy Deportation Ship Ticket</h1>
    <p style="color:#64748b;margin-bottom:24px;">Shelter point: <strong>{{ $user->shelter_point_name }}</strong> | Bus fare: <strong>+RM{{ number_format($user->shelter_fee, 2) }}</strong></p>

    @if($routes->isEmpty())
        <div style="background:#fff;border-radius:16px;padding:60px 20px;text-align:center;box-shadow:0 1px 2px rgba(0,0,0,0.05);border:1px solid #e5e7eb;">
            <svg viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.5" style="width:56px;height:56px;margin:0 auto 16px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <p style="font-size:16px;color:#64748b;">No routes available at this time.</p>
            <p style="font-size:14px;color:#94a3b8;margin-top:4px;">Please check back later.</p>
        </div>
    @else
        <div style="display:grid;gap:16px;">
            @foreach($routes as $route)
            <div style="background:#fff;border-radius:16px;padding:20px;box-shadow:0 1px 2px rgba(0,0,0,0.05);border:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
                <div>
                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                        <span style="font-weight:700;font-size:16px;">{{ $route->route->origin_port }}</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" style="width:18px;height:18px;"><polyline points="5 12 19 12"/><polyline points="12 5 19 12 12 19"/></svg>
                        <span style="font-weight:700;font-size:16px;">{{ $route->route->destination_port }}</span>
                    </div>
                    <div style="display:flex;gap:20px;margin-top:8px;font-size:13px;color:#64748b;">
                        <span>{{ $route->vessel->name }}</span>
                    </div>
                    <div style="display:flex;gap:16px;margin-top:6px;font-size:13px;">
                        @if($route->vessel->vip_capacity > 0)
                        <span style="color:#2563EB;font-weight:600;">VIP: RM{{ number_format($route->vip_price, 2) }}</span>
                        @endif
                        <span style="color:#059669;font-weight:600;">Regular: RM{{ number_format($route->regular_price, 2) }}</span>
                    </div>
                </div>
                <button onclick="openBookingForm('{{ $route->id }}', '{{ $route->route->origin_port }} → {{ $route->route->destination_port }}', '{{ $route->vessel->name }}', {{ $route->vip_price }}, {{ $route->regular_price }}, {{ $route->vessel->vip_capacity > 0 ? 'true' : 'false' }})"
                    style="background:#2563EB;color:#fff;border:none;padding:10px 24px;border-radius:8px;font-weight:600;font-size:14px;cursor:pointer;white-space:nowrap;">
                    Select Route
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
            <h2 style="font-size:18px;font-weight:700;">Booking Details</h2>
            <button onclick="closeBookingForm()" style="background:none;border:none;font-size:24px;cursor:pointer;color:#64748b;line-height:1;">&times;</button>
        </div>

        <form action="{{ route('deportation.booking.store') }}" method="POST" style="padding:20px 24px;">
            @csrf
            <input type="hidden" name="schedule_id" id="modalScheduleId">

            <div style="background:#f8fafc;border-radius:10px;padding:14px;margin-bottom:16px;">
                <div style="font-size:13px;color:#64748b;">Route</div>
                <div style="font-weight:700;" id="modalRoute">—</div>
                <div style="font-size:13px;color:#64748b;margin-top:4px;">Vessel</div>
                <div style="font-weight:600;font-size:14px;" id="modalVessel">—</div>
                <div style="display:flex;gap:16px;margin-top:8px;">
                    <span id="vipPriceRow" style="font-size:13px;color:#2563EB;font-weight:600;display:none;">VIP: RM<span id="modalVipPrice">0</span></span>
                    <span style="font-size:13px;color:#059669;font-weight:600;">Regular: RM<span id="modalRegularPrice">0</span></span>
                </div>
                <div style="margin-top:8px;font-size:13px;color:#ea580c;font-weight:600;">
                    + Bus Fare ({{ $user->shelter_point_name }}): RM{{ number_format($user->shelter_fee, 2) }} (one time)
                </div>
            </div>

            <div id="passengerContainer">
                <div class="passenger-row" style="border:1px solid #e5e7eb;border-radius:12px;padding:16px;margin-bottom:12px;">
                    <h4 style="font-size:14px;font-weight:700;margin-bottom:12px;">Passenger</h4>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                        <input type="text" name="passengers[0][full_name]" value="{{ old('passengers.0.full_name', $user->name) }}" placeholder="Full Name *" required class="dep-input">
                        <select name="passengers[0][gender]" required class="dep-input">
                            <option value="">Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                        <input type="date" name="passengers[0][birth_date]" value="{{ old('passengers.0.birth_date', $user->birth_date ? $user->birth_date->format('Y-m-d') : '') }}" placeholder="Birth Date *" required class="dep-input">
                        <input type="text" name="passengers[0][nationality]" value="{{ old('passengers.0.nationality', $user->nationality) }}" placeholder="Nationality *" required class="dep-input">
                        <input type="text" name="passengers[0][passport_number]" value="{{ old('passengers.0.passport_number', $user->passport_number) }}" placeholder="Passport No. *" required class="dep-input">
                        <input type="text" name="passengers[0][phone_number]" value="{{ old('passengers.0.phone_number', $user->phone) }}" placeholder="Phone" class="dep-input">
                        <select name="passengers[0][ticket_class]" id="ticketClassSelect" required class="dep-input" style="grid-column:span 2;" onchange="updateSummary()">
                            <option value="">Select Ticket Class</option>
                            <option value="vip">VIP</option>
                            <option value="regular">Regular</option>
                        </select>
                    </div>
                </div>
            </div>

            <div style="background:#fefce8;border-radius:10px;padding:14px;margin-bottom:16px;border:1px solid #fef08a;">
                <h4 style="font-size:13px;font-weight:700;color:#a16207;margin-bottom:8px;">Cost Summary</h4>
                <div style="font-size:13px;color:#64748b;display:flex;justify-content:space-between;"><span>Ship Ticket:</span> <span id="summaryTicket">RM0.00</span></div>
                <div style="font-size:13px;color:#64748b;display:flex;justify-content:space-between;"><span>Bus Fare ({{ $user->shelter_point_name }}):</span> <span>RM{{ number_format($user->shelter_fee, 2) }}</span></div>
                <div style="font-size:13px;color:#64748b;display:flex;justify-content:space-between;"><span>Insurance (RM10/pax):</span> <span id="summaryInsurance">RM0.00</span></div>
                <div style="border-top:1px solid #fef08a;margin-top:8px;padding-top:8px;font-weight:700;font-size:14px;display:flex;justify-content:space-between;"><span>Total:</span> <span id="summaryTotal">RM0.00</span></div>
            </div>

            <button type="submit" style="background:#2563EB;color:#fff;border:none;padding:14px;width:100%;border-radius:10px;font-weight:700;font-size:15px;cursor:pointer;">
                Proceed to Payment
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
let vipPrice = 0;
let regularPrice = 0;

function openBookingForm(scheduleId, route, vessel, vip, regular, vipAvailable) {
    document.getElementById('modalScheduleId').value = scheduleId;
    document.getElementById('modalRoute').textContent = route;
    document.getElementById('modalVessel').textContent = vessel;
    document.getElementById('modalVipPrice').textContent = vip;
    document.getElementById('modalRegularPrice').textContent = regular;
    vipPrice = vip;
    regularPrice = regular;

    // Show/hide VIP price row
    document.getElementById('vipPriceRow').style.display = vipAvailable ? '' : 'none';

    // Show/hide VIP option in dropdown
    var vipOption = document.querySelector('#ticketClassSelect option[value="vip"]');
    if (vipOption) {
        vipOption.style.display = vipAvailable ? '' : 'none';
        if (!vipAvailable && document.getElementById('ticketClassSelect').value === 'vip') {
            document.getElementById('ticketClassSelect').value = '';
        }
    }

    document.getElementById('bookingModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
    updateSummary();
}

function closeBookingForm() {
    document.getElementById('bookingModal').style.display = 'none';
    document.body.style.overflow = '';
}

function updateSummary() {
    var classSelect = document.getElementById('ticketClassSelect');
    var ticketTotal = 0;
    if (classSelect && classSelect.value === 'vip') ticketTotal += vipPrice;
    else if (classSelect && classSelect.value === 'regular') ticketTotal += regularPrice;

    document.getElementById('summaryTicket').textContent = 'RM' + ticketTotal.toFixed(2);
    document.getElementById('summaryInsurance').textContent = 'RM10.00';
    const shelter = {{ $user->shelter_fee }};
    document.getElementById('summaryTotal').textContent = 'RM' + (ticketTotal + 10 + shelter).toFixed(2);
}

document.getElementById('bookingModal').addEventListener('click', function(e) {
    if (e.target === this) closeBookingForm();
});
</script>
@endsection
