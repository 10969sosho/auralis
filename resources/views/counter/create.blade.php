@extends('layouts.app')
@section('title', 'Sell Ticket - Counter')

@section('content')
<div class="counter-form-page">
    <a href="{{ route('counter.dashboard') }}" class="link mb-4" style="display:inline-block;">← Back to Counter</a>

    <div class="card mb-4">
        <div class="flex justify-between items-start flex-wrap gap-2">
            <div>
                <h2 class="text-lg font-semibold">{{ $schedule->vessel->name }}</h2>
                <p class="text-gray-600">{{ $schedule->route->origin_port }} → {{ $schedule->route->destination_port }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Departure: <strong>{{ $schedule->departure_time->format('d M Y, H:i') }}</strong></p>
                <p class="text-sm text-gray-500">VIP: MYR {{ number_format($schedule->vip_price, 2) }} | Regular: MYR {{ number_format($schedule->regular_price, 2) }}</p>
            </div>
        </div>
    </div>

    <form action="{{ route('counter.store') }}" method="POST" class="space-y-6" id="counterForm">
        @csrf
        <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">

        <div class="flex gap-3 mb-4">
            <button type="button" class="btn btn-outline btn-sm" onclick="addPassenger()">+ Add Passenger</button>
            <button type="button" class="btn btn-outline btn-sm btn-danger-outline" onclick="removePassenger()" id="removeBtn" disabled>Remove Last</button>
        </div>

        <div id="passengerContainer">
            <div class="card mb-4 passenger-card" data-index="0">
                <div class="flex justify-between items-center border-b pb-2 mb-4">
                    <h3 class="text-lg font-semibold">Passenger 1</h3>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium age-category-badge" id="age-badge-0" style="display:none;"></span>
                        <span class="text-sm text-gray-500" id="price-label-0"></span>
                    </div>
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="passengers[0][full_name]" required class="form-input" id="name-0">
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
                        <input type="date" name="passengers[0][birth_date]" required max="{{ date('Y-m-d') }}" class="form-input passenger-birth" id="birth-0" onchange="updateAgeInfo(0)">
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
                        <label class="form-label">Passport/ID Number *</label>
                        <input type="text" name="passengers[0][passport_number]" required class="form-input" id="passport-0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="passengers[0][phone_number]" class="form-input" id="phone-0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ticket Class *</label>
                        <select name="passengers[0][ticket_class]" required class="form-select passenger-class" id="class-0" onchange="recalculateTotal()">
                            <option value="regular">Regular — MYR {{ number_format($schedule->regular_price, 2) }}</option>
                            <option value="vip">VIP — MYR {{ number_format($schedule->vip_price, 2) }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" id="paymentSummary">
            <h3 class="text-lg font-semibold mb-4">Payment</h3>
            <div id="summaryDetails" class="text-sm space-y-2 mb-4"></div>
            <div class="text-xl font-bold text-blue-600 mb-4" id="totalAmount">Total: MYR 0.00</div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Payment Method *</label>
                    <select name="payment_method" required class="form-select">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Amount Received *</label>
                    <input type="number" name="amount_received" required step="0.01" min="0" class="form-input" id="amountReceived" oninput="calculateChange()" placeholder="Enter cash received">
                </div>
            </div>
            <div class="mt-3 text-lg font-semibold" id="changeAmount" style="display:none;">
                Change: <span id="changeValue" style="color:#059669;"></span>
            </div>

            <button type="submit" class="btn btn-primary btn-lg mt-4 btn-block" style="background:#059669;border-color:#059669;">
                Confirm Payment &amp; Print Ticket
            </button>
        </div>
    </form>
</div>

<script>
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
            (category.name === 'Infant' ? 'badge-age-infant' :
             category.name === 'Child' ? 'badge-age-child' : 'badge-age-adult');
    } else if (ageBadge) {
        ageBadge.style.display = 'none';
    }
    recalculateTotal();
}

function recalculateTotal() {
    const cards = document.querySelectorAll('#passengerContainer .passenger-card');
    let summaryHtml = '';
    let total = 0;
    cards.forEach(card => {
        const idx = card.dataset.index;
        const birthInput = document.getElementById('birth-' + idx);
        const classSelect = document.getElementById('class-' + idx);
        const nameInput = document.getElementById('name-' + idx);
        const priceLabel = document.getElementById('price-label-' + idx);
        if (birthInput && birthInput.value && classSelect && classSelect.value) {
            const age = calculateAge(birthInput.value);
            const price = getPassengerPrice(age, classSelect.value);
            const category = findAgeCategory(age);
            total += price;
            if (priceLabel) priceLabel.textContent = 'MYR ' + price.toFixed(2);
            const catName = category ? category.name : (age <= 2 ? 'Infant' : age <= 12 ? 'Child' : 'Adult');
            const pName = nameInput && nameInput.value ? nameInput.value : 'Passenger ' + (parseInt(idx) + 1);
            summaryHtml += '<div class="flex justify-between"><span>' + pName + ' (' + catName + ' · ' + (classSelect.value === 'vip' ? 'VIP' : 'Regular') + ')</span><span>MYR ' + price.toFixed(2) + '</span></div>';
        }
    });
    document.getElementById('summaryDetails').innerHTML = summaryHtml || '<p class="text-gray-400">Complete passenger details to see pricing</p>';
    document.getElementById('totalAmount').textContent = 'Total: MYR ' + total.toFixed(2);
    document.getElementById('totalAmount').dataset.total = total;
    calculateChange();
}

function calculateChange() {
    const total = parseFloat(document.getElementById('totalAmount').dataset.total) || 0;
    const received = parseFloat(document.getElementById('amountReceived').value) || 0;
    const changeDiv = document.getElementById('changeAmount');
    const changeValue = document.getElementById('changeValue');
    if (received > 0) {
        changeDiv.style.display = 'block';
        const change = Math.max(0, received - total);
        changeValue.textContent = 'MYR ' + change.toFixed(2);
        if (received < total) {
            changeValue.style.color = '#DC2626';
        } else {
            changeValue.style.color = '#059669';
        }
    } else {
        changeDiv.style.display = 'none';
    }
}

function addPassenger() {
    if (passengerCount >= 8) { alert('Maximum 8 passengers.'); return; }
    const idx = passengerCount;
    const container = document.getElementById('passengerContainer');
    const div = document.createElement('div');
    div.className = 'card mb-4 passenger-card';
    div.dataset.index = idx;
    div.innerHTML = `
        <div class="flex justify-between items-center border-b pb-2 mb-4">
            <h3 class="text-lg font-semibold">Passenger ${idx + 1}</h3>
            <div class="flex items-center gap-2">
                <span class="text-sm font-medium age-category-badge" id="age-badge-${idx}" style="display:none;"></span>
                <span class="text-sm text-gray-500" id="price-label-${idx}"></span>
            </div>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div class="form-group"><label class="form-label">Full Name *</label><input type="text" name="passengers[${idx}][full_name]" required class="form-input" id="name-${idx}"></div>
            <div class="form-group"><label class="form-label">Gender *</label><select name="passengers[${idx}][gender]" required class="form-select" id="gender-${idx}"><option value="">Select</option><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select></div>
            <div class="form-group"><label class="form-label">Date of Birth *</label><input type="date" name="passengers[${idx}][birth_date]" required max="${new Date().toISOString().split('T')[0]}" class="form-input passenger-birth" id="birth-${idx}" onchange="updateAgeInfo(${idx})"></div>
            <div class="form-group"><label class="form-label">Age <span class="text-xs text-gray-400">(auto)</span></label><input type="text" class="form-input bg-gray-100" id="age-display-${idx}" readonly placeholder="Select birth date"></div>
            <div class="form-group"><label class="form-label">Nationality *</label><input type="text" name="passengers[${idx}][nationality]" required placeholder="e.g. Malaysian" class="form-input" id="nationality-${idx}"></div>
            <div class="form-group"><label class="form-label">Passport/ID Number *</label><input type="text" name="passengers[${idx}][passport_number]" required class="form-input" id="passport-${idx}"></div>
            <div class="form-group"><label class="form-label">Phone Number</label><input type="text" name="passengers[${idx}][phone_number]" class="form-input" id="phone-${idx}"></div>
            <div class="form-group"><label class="form-label">Ticket Class *</label><select name="passengers[${idx}][ticket_class]" required class="form-select passenger-class" id="class-${idx}" onchange="recalculateTotal()"><option value="regular">Regular — MYR ${schedulePrices.regular.toFixed(2)}</option><option value="vip">VIP — MYR ${schedulePrices.vip.toFixed(2)}</option></select></div>
        </div>`;
    container.appendChild(div);
    passengerCount++;
    document.getElementById('removeBtn').disabled = false;
    recalculateTotal();
}

function removePassenger() {
    if (passengerCount <= 1) return;
    const cards = document.querySelectorAll('#passengerContainer .passenger-card');
    cards[cards.length - 1].remove();
    passengerCount--;
    if (passengerCount <= 1) document.getElementById('removeBtn').disabled = true;
    recalculateTotal();
}

document.querySelectorAll('.passenger-birth').forEach(el => {
    el.addEventListener('change', function() {
        updateAgeInfo(this.closest('.passenger-card').dataset.index);
    });
});
</script>

<style>
.counter-form-page { padding: 24px 0; max-width: 800px; }
.age-category-badge { padding: 2px 10px; border-radius: 20px; font-size: 0.75rem; }
.badge-age-infant { background: #FEF3C7; color: #92400E; }
.badge-age-child { background: #DBEAFE; color: #1E40AF; }
.badge-age-adult { background: #D1FAE5; color: #065F46; }
.btn-danger-outline { border-color: #DC2626; color: #DC2626; }
.btn-danger-outline:hover { background: #DC2626; color: #fff; }
</style>
@endsection
