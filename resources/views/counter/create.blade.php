@extends('layouts.app')
@section('title', 'Sell Ticket - Counter')

@section('content')
<h1 class="text-2xl font-bold text-gray-900">Sell Ticket — Counter</h1>

<div class="mt-4 alert alert-info">
    <p class="font-semibold">{{ $schedule->vessel->name }}</p>
    <p class="text-gray-600">{{ $schedule->route->origin_port }} → {{ $schedule->route->destination_port }}</p>
    <p class="text-sm text-gray-500">Departure: {{ $schedule->departure_time->format('d M Y, H:i') }}</p>
    <p class="text-sm text-gray-500 mt-1">VIP: RM {{ number_format($schedule->vip_price, 2) }} · Regular: RM {{ number_format($schedule->regular_price, 2) }}</p>
</div>

<form action="{{ route('counter.store') }}" method="POST" class="mt-6 space-y-6" id="counterForm">
    @csrf
    <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">

    <div class="flex items-center gap-2 mb-4">
        <button type="button" class="btn btn-primary btn-sm" onclick="addPassenger()">+ Add Passenger</button>
        <button type="button" class="btn btn-outline btn-sm" onclick="removePassenger()" id="removeBtn" disabled>− Remove Last</button>
        <span class="text-sm text-gray-500 ml-auto" id="passengerCount">1 passenger</span>
    </div>

    <div id="passengers-container">
        <div class="card mb-6 passenger-card" data-index="0">
            <div class="flex justify-between items-center border-b pb-2 mb-4">
                <h3 class="text-lg font-semibold">Passenger 1</h3>
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium age-category-badge" id="age-badge-0" style="display:none;"></span>
                    <span class="text-sm text-gray-500 passenger-price-label" id="price-label-0"></span>
                </div>
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="passengers[0][full_name]" required class="form-input" id="name-0" placeholder="Passenger name">
                </div>
                <div class="form-group">
                    <label class="form-label">Gender *</label>
                    <select name="passengers[0][gender]" required class="form-select" id="gender-0">
                        <option value="">Select</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Date of Birth *</label>
                    <input type="date" name="passengers[0][birth_date]" required max="{{ date('Y-m-d') }}" class="form-input" id="birth-0" onchange="updateAgeInfo(0)">
                </div>
                <div class="form-group">
                    <label class="form-label">Age <span class="text-xs text-gray-400">(auto)</span></label>
                    <input type="text" class="form-input bg-gray-100" id="age-display-0" readonly placeholder="Select birth date">
                </div>
                <div class="form-group">
                    <label class="form-label">Nationality *</label>
                    <input type="text" name="passengers[0][nationality]" required placeholder="e.g. Malaysian" class="form-input" id="nationality-0">
                </div>
                <div class="form-group">
                    <label class="form-label">Passport/ID *</label>
                    <input type="text" name="passengers[0][passport_number]" required class="form-input" id="passport-0" placeholder="Passport number">
                </div>
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="text" name="passengers[0][phone_number]" class="form-input" id="phone-0" placeholder="Optional">
                </div>
                <div class="form-group">
                    <label class="form-label">Class *</label>
                    <select name="passengers[0][ticket_class]" required class="form-select" id="class-0" onchange="recalculateTotal()">
                        <option value="regular">Regular — RM {{ number_format($schedule->regular_price, 2) }} ({{ $regularAvailable }} left)</option>
                        <option value="vip">VIP — RM {{ number_format($schedule->vip_price, 2) }} ({{ $vipAvailable }} left)</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <h3 class="text-lg font-semibold border-b pb-2 mb-4">Payment Summary</h3>
        <div class="space-y-2 text-sm" id="summaryDetails">
            <p class="text-gray-400">Complete passenger details to see pricing</p>
        </div>
        <div class="mt-3 pt-3 border-t">
            <p class="text-xl font-bold text-blue-600" id="totalAmount">Total: RM 0.00</p>
        </div>

        <div class="mt-4 grid sm:grid-cols-2 gap-4">
            <div class="form-group">
                <label class="form-label">Payment Method *</label>
                <select name="payment_method" required class="form-select">
                    <option value="cash">Cash</option>
                    <option value="card">Card</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Amount Received *</label>
                <div class="flex gap-2">
                    <input type="number" name="amount_received" required step="0.01" min="0" class="form-input" id="amountReceived" oninput="calculateChange()" placeholder="0.00" style="flex:1;">
                    <button type="button" class="btn btn-outline btn-sm" onclick="fillExactAmount()" style="white-space:nowrap;">Exact</button>
                </div>
            </div>
        </div>

        <div class="mt-3" id="changeAmount" style="display:none;">
            <div id="changeDisplay"></div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg mt-4 btn-block" style="background:#059669;border-color:#059669;">
            Confirm Payment
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
const INSURANCE = 10;
const schedulePrices = {
    vip: {{ $schedule->vip_price }},
    regular: {{ $schedule->regular_price }},
    agePrices: {!! json_encode($schedule->agePrices->mapWithKeys(fn($ap) => [$ap->age_category_id => (float)$ap->price])) !!}
};
const ageCategories = {!! json_encode($ageCategories->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'min' => $c->min_age, 'max' => $c->max_age])) !!};
let passengerCount = 1;

function calculateAge(birthDate) {
    const today = new Date();
    const birth = new Date(birthDate);
    let age = today.getFullYear() - birth.getFullYear();
    const m = today.getMonth() - birth.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
    return age;
}

function findAgeCategory(age) {
    for (let cat of ageCategories) {
        if (age >= cat.min && age <= cat.max) return cat;
    }
    return null;
}

function getPassengerPrice(age, ticketClass) {
    const category = findAgeCategory(age);
    if (category && schedulePrices.agePrices[category.id] !== undefined) {
        return schedulePrices.agePrices[category.id];
    }
    return ticketClass === 'vip' ? schedulePrices.vip : schedulePrices.regular;
}

function updateAgeInfo(index) {
    const birthInput = document.getElementById('birth-' + index);
    const ageDisplay = document.getElementById('age-display-' + index);
    const ageBadge = document.getElementById('age-badge-' + index);
    const priceLabel = document.getElementById('price-label-' + index);
    if (!birthInput || !birthInput.value) {
        if (ageDisplay) ageDisplay.value = '';
        if (ageBadge) ageBadge.style.display = 'none';
        if (priceLabel) priceLabel.textContent = '';
        recalculateTotal();
        return;
    }
    const age = calculateAge(birthInput.value);
    const category = findAgeCategory(age);
    if (ageDisplay) ageDisplay.value = age + ' years';
    if (ageBadge && category) {
        ageBadge.textContent = category.name;
        ageBadge.style.display = 'inline-block';
        ageBadge.className = 'text-sm font-medium age-category-badge ' +
            (category.name === 'Infant' || category.name === 'Bayi' ? 'badge-age-infant' :
             category.name === 'Child' || category.name === 'Anak' ? 'badge-age-child' : 'badge-age-adult');
    } else if (ageBadge) {
        ageBadge.style.display = 'none';
    }
    recalculateTotal();
}

function recalculateTotal() {
    const cards = document.querySelectorAll('.passenger-card');
    const summaryDetails = document.getElementById('summaryDetails');
    const totalEl = document.getElementById('totalAmount');
    let summaryHtml = '';
    let total = 0;
    let pax = 0;

    cards.forEach(card => {
        const idx = card.dataset.index;
        const b = document.getElementById('birth-' + idx);
        const c = document.getElementById('class-' + idx);
        const n = document.getElementById('name-' + idx);
        const pl = document.getElementById('price-label-' + idx);
        if (b && b.value && c && c.value) {
            const age = calculateAge(b.value);
            const price = getPassengerPrice(age, c.value);
            const cat = findAgeCategory(age);
            total += price;
            pax++;
            if (pl) pl.textContent = 'RM ' + price.toFixed(2);
            const cn = cat ? cat.name : (age <= 2 ? 'Infant' : age <= 12 ? 'Child' : 'Adult');
            const nm = n && n.value ? n.value : 'Passenger ' + (parseInt(idx) + 1);
            summaryHtml += '<div class="flex justify-between"><span>' + nm + ' (' + cn + ' · ' + (c.value === 'vip' ? 'VIP' : 'Reg') + ')</span><span>RM ' + price.toFixed(2) + '</span></div>';
        }
    });

    const ins = pax * INSURANCE;
    total += ins;

    if (pax > 0) {
        summaryHtml += '<div class="flex justify-between pt-2 border-t mt-2 text-gray-500"><span>Insurance (' + pax + ' × RM ' + INSURANCE.toFixed(2) + ')</span><span>RM ' + ins.toFixed(2) + '</span></div>';
    }

    summaryDetails.innerHTML = summaryHtml || '<p class="text-gray-400">Complete passenger details to see pricing</p>';
    totalEl.textContent = 'Total: RM ' + total.toFixed(2);
    totalEl.dataset.total = total;
    document.getElementById('passengerCount').textContent = pax + ' passenger' + (pax !== 1 ? 's' : '');
    calculateChange();
}

function calculateChange() {
    const total = parseFloat(document.getElementById('totalAmount').dataset.total) || 0;
    const received = parseFloat(document.getElementById('amountReceived').value) || 0;
    const div = document.getElementById('changeAmount');
    const disp = document.getElementById('changeDisplay');
    if (received > 0) {
        div.style.display = 'block';
        if (received < total) {
            const short = total - received;
            disp.innerHTML = '<div class="p-3 rounded" style="background:#FEF2F2;border:1px solid #FECACA;"><span class="font-semibold text-red-600">Shortfall: RM ' + short.toFixed(2) + '</span><span class="text-gray-600 ml-2">— Need RM ' + total.toFixed(2) + '</span></div>';
        } else {
            const change = received - total;
            disp.innerHTML = '<div class="p-3 rounded" style="background:#ECFDF5;border:1px solid #A7F3D0;"><span class="font-semibold text-green-600">Change to return: RM ' + change.toFixed(2) + '</span></div>';
        }
    } else {
        div.style.display = 'none';
    }
}

function fillExactAmount() {
    const total = parseFloat(document.getElementById('totalAmount').dataset.total) || 0;
    if (total > 0) {
        document.getElementById('amountReceived').value = total.toFixed(2);
        calculateChange();
    } else {
        alert('Complete passenger details first.');
    }
}

function addPassenger() {
    if (passengerCount >= 8) { alert('Max 8 passengers.'); return; }
    const idx = passengerCount;
    const container = document.getElementById('passengers-container');
    const card = document.createElement('div');
    card.className = 'card mb-6 passenger-card';
    card.dataset.index = idx;
    card.innerHTML = `
        <div class="flex justify-between items-center border-b pb-2 mb-4">
            <h3 class="text-lg font-semibold">Passenger ${idx + 1}</h3>
            <div class="flex items-center gap-2">
                <span class="text-sm font-medium age-category-badge" id="age-badge-${idx}" style="display:none;"></span>
                <span class="text-sm text-gray-500 passenger-price-label" id="price-label-${idx}"></span>
            </div>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div class="form-group"><label class="form-label">Full Name *</label><input type="text" name="passengers[${idx}][full_name]" required class="form-input" id="name-${idx}" placeholder="Passenger name"></div>
            <div class="form-group"><label class="form-label">Gender *</label><select name="passengers[${idx}][gender]" required class="form-select" id="gender-${idx}"><option value="">Select</option><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select></div>
            <div class="form-group"><label class="form-label">Date of Birth *</label><input type="date" name="passengers[${idx}][birth_date]" required max="${new Date().toISOString().split('T')[0]}" class="form-input" id="birth-${idx}" onchange="updateAgeInfo(${idx})"></div>
            <div class="form-group"><label class="form-label">Age <span class="text-xs text-gray-400">(auto)</span></label><input type="text" class="form-input bg-gray-100" id="age-display-${idx}" readonly placeholder="Select birth date"></div>
            <div class="form-group"><label class="form-label">Nationality *</label><input type="text" name="passengers[${idx}][nationality]" required placeholder="e.g. Malaysian" class="form-input" id="nationality-${idx}"></div>
            <div class="form-group"><label class="form-label">Passport/ID *</label><input type="text" name="passengers[${idx}][passport_number]" required class="form-input" id="passport-${idx}" placeholder="Passport number"></div>
            <div class="form-group"><label class="form-label">Phone</label><input type="text" name="passengers[${idx}][phone_number]" class="form-input" id="phone-${idx}" placeholder="Optional"></div>
            <div class="form-group"><label class="form-label">Class *</label><select name="passengers[${idx}][ticket_class]" required class="form-select" id="class-${idx}" onchange="recalculateTotal()"><option value="regular">Regular — RM ${schedulePrices.regular.toFixed(2)} (available)</option><option value="vip">VIP — RM ${schedulePrices.vip.toFixed(2)} (available)</option></select></div>
        </div>`;
    container.appendChild(card);
    passengerCount++;
    document.getElementById('removeBtn').disabled = false;
    recalculateTotal();
}

function removePassenger() {
    if (passengerCount <= 1) return;
    const cards = document.querySelectorAll('.passenger-card');
    cards[cards.length - 1].remove();
    passengerCount--;
    if (passengerCount <= 1) document.getElementById('removeBtn').disabled = true;
    recalculateTotal();
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('totalAmount').dataset.total = 0;
    recalculateTotal();
});
</script>
@endpush

<style>
.age-category-badge { padding: 2px 10px; border-radius: 20px; font-size: 0.75rem; }
.badge-age-infant { background: #FEF3C7; color: #92400E; }
.badge-age-child { background: #DBEAFE; color: #1E40AF; }
.badge-age-adult { background: #D1FAE5; color: #065F46; }
</style>