@extends('layouts.app')
@section('title', 'Book Ticket')

@section('content')
<h1 class="text-2xl font-bold text-gray-900">Book Your Ticket</h1>

<div class="mt-4 alert alert-info">
    <p class="font-semibold">{{ $schedule->vessel->name }}</p>
    <p class="text-gray-600">{{ $schedule->route->origin_port }} → {{ $schedule->route->destination_port }}</p>
    <p class="text-sm text-gray-500">Departure: {{ $schedule->departure_time->format('d M Y, H:i') }}</p>
</div>

@auth
<div class="mt-4 alert alert-success" id="autoFillNotice" style="display:none;">
    Your profile data has been auto-filled for Passenger 1.
</div>
@endauth

<form action="{{ route('booking.store') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-6" id="bookingForm">
    @csrf
    <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
    <input type="hidden" id="passenger-count-input" name="passenger_count" value="{{ $passengerCount }}">

    <div class="form-group">
        <label for="promo_code" class="form-label">Promo Code (optional)</label>
        <input type="text" name="promo_code" id="promo_code" class="form-input max-w-xs" onchange="recalculateTotal()">
        @if($autoPromos->isNotEmpty())
            <p class="mt-1 text-sm text-green-600">Available promos: {{ $autoPromos->pluck('name')->join(', ') }}</p>
        @endif
    </div>

    @auth
    <div class="card mb-6" id="savedProfilesCard">
        <h3 class="text-lg font-semibold border-b pb-2 mb-4">Quick Add: Saved Passengers</h3>
        <div class="flex flex-wrap gap-2" id="savedProfilesList">
            @forelse($savedProfiles as $profile)
            <button type="button" class="btn btn-sm btn-outline profile-select-btn"
                onclick="addFromProfile({
                    full_name: '{{ addslashes($profile->full_name) }}',
                    gender: '{{ $profile->gender }}',
                    birth_date: '{{ $profile->birth_date?->format('Y-m-d') }}',
                    nationality: '{{ addslashes($profile->nationality ?? '') }}',
                    passport_number: '{{ addslashes($profile->passport_number ?? '') }}',
                    phone: '{{ addslashes($profile->phone ?? '') }}'
                })"
                title="{{ $profile->birth_date?->format('d M Y') }} | {{ $profile->passport_number }}">
                <span class="profile-avatar-sm">{{ substr($profile->full_name, 0, 1) }}</span>
                {{ $profile->full_name }}
            </button>
            @empty
            <p class="text-sm text-gray-500">No saved passengers. <a href="{{ route('profiles.index') }}" class="link">Save some first</a></p>
            @endforelse
        </div>
    </div>
    @endauth

    <div id="passengers-container">
        @for($i = 0; $i < $passengerCount; $i++)
        <div class="card mb-6 passenger-card" id="passenger-{{ $i }}" data-index="{{ $i }}">
            <div class="flex justify-between items-center border-b pb-2 mb-4">
                <h3 class="text-lg font-semibold">Passenger {{ $i + 1 }}</h3>
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium age-category-badge" id="age-badge-{{ $i }}" style="display:none;"></span>
                    <span class="text-sm text-gray-500 passenger-price-label" id="price-label-{{ $i }}"></span>
                </div>
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="passengers[{{ $i }}][full_name]" required class="form-input passenger-name"
                        id="name-{{ $i }}"
                        @if($i === 0 && $userProfile) value="{{ $userProfile['name'] }}" @endif>
                </div>
                <div class="form-group">
                    <label class="form-label">Gender *</label>
                    <select name="passengers[{{ $i }}][gender]" required class="form-select"
                        id="gender-{{ $i }}">
                        <option value="">Select</option>
                        <option value="male" @if($i === 0 && $userProfile && $userProfile['gender'] === 'male') selected @endif>Male</option>
                        <option value="female" @if($i === 0 && $userProfile && $userProfile['gender'] === 'female') selected @endif>Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Date of Birth *</label>
                    <input type="date" name="passengers[{{ $i }}][birth_date]" required max="{{ date('Y-m-d') }}" class="form-input passenger-birth"
                        id="birth-{{ $i }}"
                        @if($i === 0 && $userProfile && $userProfile['birth_date']) value="{{ $userProfile['birth_date'] }}" @endif
                        onchange="updateAgeInfo({{ $i }})">
                </div>
                <div class="form-group">
                    <label class="form-label">Age <span class="text-xs text-gray-400">(auto)</span></label>
                    <input type="text" class="form-input bg-gray-100" id="age-display-{{ $i }}" readonly placeholder="Select birth date">
                    <input type="hidden" id="age-category-id-{{ $i }}" value="">
                </div>
                <div class="form-group">
                    <label class="form-label">Nationality *</label>
                    <input type="text" name="passengers[{{ $i }}][nationality]" required placeholder="e.g. Malaysian" class="form-input"
                        id="nationality-{{ $i }}"
                        @if($i === 0 && $userProfile && $userProfile['nationality']) value="{{ $userProfile['nationality'] }}" @endif>
                </div>
                <div class="form-group">
                    <label class="form-label">Passport/ID Number *</label>
                    <input type="text" name="passengers[{{ $i }}][passport_number]" required class="form-input"
                        id="passport-{{ $i }}"
                        @if($i === 0 && $userProfile && $userProfile['passport_number']) value="{{ $userProfile['passport_number'] }}" @endif>
                </div>
                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="text" name="passengers[{{ $i }}][phone_number]" class="form-input"
                        id="phone-{{ $i }}"
                        @if($i === 0 && $userProfile && $userProfile['phone']) value="{{ $userProfile['phone'] }}" @endif>
                </div>
                <div class="form-group">
                    <label class="form-label">Ticket Class *</label>
                    <select name="passengers[{{ $i }}][ticket_class]" required class="form-select passenger-class"
                        id="class-{{ $i }}" onchange="recalculateTotal()">
                        <option value="regular">Regular — RM {{ number_format($schedule->regular_price, 2) }}</option>
                        <option value="vip">VIP — RM {{ number_format($schedule->vip_price, 2) }}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Passport/ID Upload *</label>
                    <input type="file" name="passengers[{{ $i }}][passport_file]" required accept=".pdf,.jpg,.jpeg,.png" class="form-input">
                    <p class="form-hint">PDF, JPG, PNG (max 5MB)</p>
                </div>
                <div class="form-group">
                    <label class="form-label">Travel Permit (optional)</label>
                    <input type="file" name="passengers[{{ $i }}][travel_permit]" accept=".pdf,.jpg,.jpeg,.png" class="form-input">
                </div>
            </div>
        </div>
        @endfor
    </div>

    <div class="card" id="bookingSummary">
        <h3 class="text-lg font-semibold">Booking Summary</h3>
        <div class="mt-3 space-y-2 text-sm" id="summaryDetails">
        </div>
        <div class="mt-3 pt-3 border-t">
            <p class="text-lg font-bold text-blue-600" id="totalAmount">Total: RM 0.00</p>
        </div>
        <p class="mt-2 text-sm text-gray-600">Free baggage: {{ $schedule->vessel->free_baggage }}kg per passenger</p>
        <p class="mt-2 text-sm text-gray-500">Booking will be held for 30 minutes after submission.</p>
        <button type="submit" class="btn btn-primary btn-lg mt-4 sm:w-auto btn-block">Continue to Payment</button>
    </div>
</form>

<script>
const schedulePrices = {
    vip: {{ $schedule->vip_price }},
    regular: {{ $schedule->regular_price }},
    agePrices: {!! json_encode($schedule->agePrices->mapWithKeys(fn($ap) => [$ap->age_category_id => (float)$ap->price])) !!}
};
const ageCategories = {!! json_encode($ageCategories->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'min' => $c->min_age, 'max' => $c->max_age])) !!};

let usedProfileIndex = 0;

@auth
@if($userProfile && $userProfile['birth_date'])
document.addEventListener('DOMContentLoaded', function() {
    updateAgeInfo(0);
    document.getElementById('autoFillNotice').style.display = 'block';
});
@endif
@endauth

function calculateAge(birthDate) {
    const today = new Date();
    const birth = new Date(birthDate);
    let age = today.getFullYear() - birth.getFullYear();
    const m = today.getMonth() - birth.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) {
        age--;
    }
    return age;
}

function findAgeCategory(age) {
    for (let cat of ageCategories) {
        if (age >= cat.min && age <= cat.max) {
            return cat;
        }
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
    const categoryIdInput = document.getElementById('age-category-id-' + index);

    if (!birthInput.value) {
        ageDisplay.value = '';
        ageBadge.style.display = 'none';
        priceLabel.textContent = '';
        categoryIdInput.value = '';
        recalculateTotal();
        return;
    }

    const age = calculateAge(birthInput.value);
    const category = findAgeCategory(age);
    ageDisplay.value = age + ' years';

    if (category) {
        ageBadge.textContent = category.name;
        ageBadge.style.display = 'inline-block';
        ageBadge.className = 'text-sm font-medium age-category-badge ' +
            (category.name === 'Infant' || category.name === 'Bayi' ? 'badge-age-infant' :
             category.name === 'Child' || category.name === 'Anak' ? 'badge-age-child' : 'badge-age-adult');
        categoryIdInput.value = category.id;
    } else {
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

    cards.forEach(card => {
        const idx = card.dataset.index;
        const birthInput = document.getElementById('birth-' + idx);
        const classSelect = document.getElementById('class-' + idx);
        const nameInput = document.getElementById('name-' + idx);
        const priceLabel = document.getElementById('price-label-' + idx);

        if (birthInput.value && classSelect.value) {
            const age = calculateAge(birthInput.value);
            const ticketClass = classSelect.value;
            const price = getPassengerPrice(age, ticketClass);
            const category = findAgeCategory(age);
            total += price;
            priceLabel.textContent = 'RM ' + price.toFixed(2);

            const className = ticketClass === 'vip' ? 'VIP' : 'Regular';
            const catName = category ? category.name : (age <= 2 ? 'Infant' : age <= 12 ? 'Child' : 'Adult');
            const pName = nameInput.value || 'Passenger ' + (parseInt(idx) + 1);
            summaryHtml += '<div class="flex justify-between"><span>' + pName + ' (' + catName + ' · ' + className + ')</span><span>RM ' + price.toFixed(2) + '</span></div>';
        }
    });

    summaryDetails.innerHTML = summaryHtml || '<p class="text-gray-400">Complete passenger details to see pricing</p>';
    totalEl.textContent = 'Total: RM ' + total.toFixed(2);
}

function addFromProfile(profile) {
    const cards = document.querySelectorAll('.passenger-card');
    for (let card of cards) {
        const idx = parseInt(card.dataset.index);
        const nameInput = document.getElementById('name-' + idx);
        if (nameInput && !nameInput.value) {
            document.getElementById('name-' + idx).value = profile.full_name || '';
            if (profile.gender) {
                document.getElementById('gender-' + idx).value = profile.gender;
            }
            if (profile.birth_date) {
                document.getElementById('birth-' + idx).value = profile.birth_date;
                updateAgeInfo(idx);
            }
            if (profile.nationality) {
                document.getElementById('nationality-' + idx).value = profile.nationality;
            }
            if (profile.passport_number) {
                document.getElementById('passport-' + idx).value = profile.passport_number;
            }
            if (profile.phone) {
                document.getElementById('phone-' + idx).value = profile.phone;
            }
            recalculateTotal();
            return;
        }
    }
    alert('All passenger slots are filled. Remove one first.');
}

// Listen for class changes to recalculate
document.querySelectorAll('.passenger-class').forEach(select => {
    select.addEventListener('change', recalculateTotal);
});

document.querySelectorAll('.passenger-birth').forEach(input => {
    input.addEventListener('change', function() {
        const idx = this.closest('.passenger-card').dataset.index;
        updateAgeInfo(idx);
    });
});
</script>

<style>
.age-category-badge {
    padding: 2px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
}
.badge-age-infant { background: #FEF3C7; color: #92400E; }
.badge-age-child { background: #DBEAFE; color: #1E40AF; }
.badge-age-adult { background: #D1FAE5; color: #065F46; }
.profile-select-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: all 0.15s;
}
.profile-select-btn:hover {
    border-color: #2563EB;
    color: #2563EB;
}
.profile-avatar-sm {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #EFF6FF;
    color: #2563EB;
    font-size: 0.7rem;
    font-weight: 700;
}
</style>
@endsection
