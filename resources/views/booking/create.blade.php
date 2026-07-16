@extends('layouts.app')
@section('title', 'Book Ticket')

@section('content')
@push('styles')
<style>
.country-wrapper { position: relative; }
.country-dropdown {
    position: absolute; top: 100%; left: 0; right: 0; z-index: 50;
    background: #fff; border: 1px solid #d1d5db; border-top: none;
    border-radius: 0 0 8px 8px; max-height: 200px; overflow-y: auto;
    display: none; box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.country-dropdown .country-option {
    padding: 10px 14px; cursor: pointer; font-size: 14px;
}
.country-dropdown .country-option:hover { background: #f0f7ff; color: #2563EB; }
.country-dropdown .country-option.selected { background: #eff6ff; font-weight: 600; }
.country-dropdown .no-results { padding: 10px 14px; color: #94a3b8; font-size: 13px; text-align: center; }
</style>
@endpush

@section('content')
<h1 class="text-2xl font-bold text-gray-900" data-translate-en="Book Your Ticket" data-translate-id="Pesan Tiket Anda">Book Your Ticket</h1>

<div class="mt-4 alert alert-info">
    <p class="font-semibold">{{ $schedule->vessel->name }}</p>
    <p class="text-gray-600">{{ $schedule->route->origin_port }} → {{ $schedule->route->destination_port }}</p>
    <p class="text-sm text-gray-500" data-translate-en="Departure:" data-translate-id="Keberangkatan:">Departure: {{ $schedule->departure_time->format('d M Y, H:i') }}</p>
</div>

@auth
<div class="mt-4 alert alert-success" id="autoFillNotice" style="display:none;" data-translate-en="Your profile data has been auto-filled for Passenger 1." data-translate-id="Data profil Anda telah diisi otomatis untuk Penumpang 1.">
    Your profile data has been auto-filled for Passenger 1.
</div>
@endauth

<form action="{{ route('booking.store') }}" method="POST" enctype="multipart/form-data" class="mt-6 space-y-6" id="bookingForm">
    @csrf
    <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
    <input type="hidden" id="passenger-count-input" name="passenger_count" value="{{ $passengerCount }}">

    <div class="form-group">
        <label for="promo_code" class="form-label" data-translate-en="Promo Code (optional)" data-translate-id="Kode Promo (opsional)">Promo Code (optional)</label>
        <input type="text" name="promo_code" id="promo_code" class="form-input max-w-xs" onchange="recalculateTotal()">
        @if($autoPromos->isNotEmpty())
            <p class="mt-1 text-sm text-green-600"><span data-translate-en="Available promos:" data-translate-id="Promo tersedia:">Available promos:</span> {{ $autoPromos->pluck('name')->join(', ') }}</p>
        @endif
    </div>

    @guest
    <div class="card mb-6" id="guestEmailCard">
        <div class="flex items-center gap-2 mb-4">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;color:#2563eb;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            <h3 class="text-lg font-semibold" data-translate-en="Email for Booking Confirmation" data-translate-id="Email untuk Konfirmasi Pemesanan">Email for Booking Confirmation</h3>
        </div>
        <p class="text-sm text-gray-500 mb-3" data-translate-en="Enter your email to receive booking confirmation and ticket links." data-translate-id="Masukkan email Anda untuk menerima konfirmasi pemesanan dan tautan tiket.">Enter your email to receive booking confirmation and ticket links.</p>
        <div class="form-group">
            <label for="guest_email" class="form-label" data-translate-en="Email Address *" data-translate-id="Alamat Email *">Email Address *</label>
            <input type="email" name="guest_email" id="guest_email" required class="form-input max-w-md" placeholder="you@example.com">
            @error('guest_email')
                <span class="text-sm text-red-600">{{ $message }}</span>
            @enderror
        </div>
    </div>
    @endguest

    @auth
    <div class="card mb-6" id="savedProfilesCard">
        <h3 class="text-lg font-semibold border-b pb-2 mb-4" data-translate-en="Quick Add: Saved Passengers" data-translate-id="Tambah Cepat: Penumpang Tersimpan">Quick Add: Saved Passengers</h3>
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
            <p class="text-sm text-gray-500"><span data-translate-en="No saved passengers." data-translate-id="Tidak ada penumpang tersimpan.">No saved passengers.</span> <a href="{{ route('profiles.index') }}" class="link" data-translate-en="Save some first" data-translate-id="Simpan beberapa dulu">Save some first</a></p>
            @endforelse
        </div>
    </div>
    @endauth

    <div id="passengers-container">
        @for($i = 0; $i < $passengerCount; $i++)
        <div class="card mb-6 passenger-card" id="passenger-{{ $i }}" data-index="{{ $i }}">
            <div class="flex justify-between items-center border-b pb-2 mb-4">
                <h3 class="text-lg font-semibold" data-translate-en="Passenger" data-translate-id="Penumpang">Passenger {{ $i + 1 }}</h3>
                <div class="flex items-center gap-2">
                    <span class="text-sm font-medium age-category-badge" id="age-badge-{{ $i }}" style="display:none;"></span>
                    <span class="text-sm text-gray-500 passenger-price-label" id="price-label-{{ $i }}"></span>
                </div>
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label" data-translate-en="Full Name *" data-translate-id="Nama Lengkap *">Full Name *</label>
                    <input type="text" name="passengers[{{ $i }}][full_name]" required class="form-input passenger-name"
                        id="name-{{ $i }}"
                        @if($i === 0 && $userProfile) value="{{ $userProfile['name'] }}" @endif>
                </div>
                <div class="form-group">
                    <label class="form-label" data-translate-en="Gender *" data-translate-id="Jenis Kelamin *">Gender *</label>
                    <select name="passengers[{{ $i }}][gender]" required class="form-select"
                        id="gender-{{ $i }}">
                        <option value="" data-translate-en="Select" data-translate-id="Pilih">Select</option>
                        <option value="male" @if($i === 0 && $userProfile && $userProfile['gender'] === 'male') selected @endif data-translate-en="Male" data-translate-id="Laki-laki">Male</option>
                        <option value="female" @if($i === 0 && $userProfile && $userProfile['gender'] === 'female') selected @endif data-translate-en="Female" data-translate-id="Perempuan">Female</option>
                        <option value="other" data-translate-en="Other" data-translate-id="Lainnya">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" data-translate-en="Date of Birth *" data-translate-id="Tanggal Lahir *">Date of Birth *</label>
                    <input type="date" name="passengers[{{ $i }}][birth_date]" required max="{{ date('Y-m-d') }}" class="form-input passenger-birth"
                        id="birth-{{ $i }}"
                        @if($i === 0 && $userProfile && $userProfile['birth_date']) value="{{ $userProfile['birth_date'] }}" @endif
                        onchange="updateAgeInfo({{ $i }})">
                </div>
                <div class="form-group">
                    <label class="form-label"><span data-translate-en="Age" data-translate-id="Usia">Age</span> <span class="text-xs text-gray-400" data-translate-en="(auto)" data-translate-id="(otomatis)">(auto)</span></label>
                    <input type="text" class="form-input bg-gray-100" id="age-display-{{ $i }}" readonly placeholder="Select birth date" data-translate-en="Select birth date" data-translate-id="Pilih tanggal lahir">
                    <input type="hidden" id="age-category-id-{{ $i }}" value="">
                </div>
                <div class="form-group">
                    <label class="form-label" data-translate-en="Nationality *" data-translate-id="Kewarganegaraan *">Nationality *</label>
                    <div class="country-wrapper">
                        <input type="text" name="passengers[{{ $i }}][nationality]" required placeholder="e.g. Malaysian" class="form-input country-input passenger-nationality"
                            id="nationality-{{ $i }}"
                            @if($i === 0 && $userProfile && $userProfile['nationality']) value="{{ $userProfile['nationality'] }}" @endif
                            data-translate-en="e.g. Malaysian" data-translate-id="mis. Malaysia" autocomplete="off">
                        <div class="country-dropdown" id="country-dropdown-{{ $i }}"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" data-translate-en="Passport/ID Number *" data-translate-id="Nomor Paspor/KTP *">Passport/ID Number *</label>
                    <input type="text" name="passengers[{{ $i }}][passport_number]" required class="form-input"
                        id="passport-{{ $i }}"
                        @if($i === 0 && $userProfile && $userProfile['passport_number']) value="{{ $userProfile['passport_number'] }}" @endif>
                </div>
                <div class="form-group">
                    <label class="form-label" data-translate-en="Phone Number" data-translate-id="Nomor Telepon">Phone Number</label>
                    <input type="text" name="passengers[{{ $i }}][phone_number]" class="form-input"
                        id="phone-{{ $i }}"
                        @if($i === 0 && $userProfile && $userProfile['phone']) value="{{ $userProfile['phone'] }}" @endif>
                </div>
                <div class="form-group">
                    <label class="form-label" data-translate-en="Ticket Class *" data-translate-id="Kelas Tiket *">Ticket Class *</label>
                    <select name="passengers[{{ $i }}][ticket_class]" required class="form-select passenger-class"
                        id="class-{{ $i }}" onchange="recalculateTotal()">
                        <option value="regular" data-translate-en="Regular — RM" data-translate-id="Regular — RM">Regular — RM {{ number_format($schedule->regular_price, 2) }}</option>
                        @if($schedule->vessel->vip_capacity > 0)
                        <option value="vip" data-translate-en="VIP — RM" data-translate-id="VIP — RM">VIP — RM {{ number_format($schedule->vip_price, 2) }}</option>
                        @endif
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" data-translate-en="Passport/ID Upload *" data-translate-id="Unggah Paspor/KTP *">Passport/ID Upload *</label>
                    <input type="file" name="passengers[{{ $i }}][passport_file]" required accept=".pdf,.jpg,.jpeg,.png" class="form-input">
                    <p class="form-hint" data-translate-en="PDF, JPG, PNG (max 5MB)" data-translate-id="PDF, JPG, PNG (maks 5MB)">PDF, JPG, PNG (max 5MB)</p>
                </div>
                <div class="form-group">
                    <label class="form-label" data-translate-en="Travel Permit (optional)" data-translate-id="Izin Perjalanan (opsional)">Travel Permit (optional)</label>
                    <input type="file" name="passengers[{{ $i }}][travel_permit]" accept=".pdf,.jpg,.jpeg,.png" class="form-input">
                </div>
            </div>
        </div>
        @endfor
    </div>

    <div class="card" id="bookingSummary">
        <h3 class="text-lg font-semibold" data-translate-en="Booking Summary" data-translate-id="Ringkasan Pemesanan">Booking Summary</h3>
        <div class="mt-3 space-y-2 text-sm" id="summaryDetails">
        </div>
        <div class="mt-3 pt-3 border-t">
            <p class="text-lg font-bold text-blue-600" id="totalAmount" data-translate-en="Total:" data-translate-id="Total:">Total: RM 0.00</p>
        </div>
        <p class="mt-2 text-sm text-gray-600">Free baggage: {{ $schedule->vessel->free_baggage }}kg <span data-translate-en="per passenger" data-translate-id="per penumpang">per passenger</span></p>
        <p class="mt-2 text-sm text-gray-500" data-translate-en="Booking will be held for 30 minutes after submission." data-translate-id="Pemesanan akan ditahan selama 30 menit setelah pengiriman.">Booking will be held for 30 minutes after submission.</p>
        <button type="submit" class="btn btn-primary btn-lg mt-4 sm:w-auto btn-block" data-translate-en="Continue to Payment" data-translate-id="Lanjutkan ke Pembayaran">Continue to Payment</button>
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
    const INSURANCE = 10;
    const cards = document.querySelectorAll('.passenger-card');
    const summaryDetails = document.getElementById('summaryDetails');
    const totalEl = document.getElementById('totalAmount');
    let summaryHtml = '';
    let total = 0;
    let pax = 0;

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
            pax++;
            priceLabel.textContent = 'RM ' + price.toFixed(2);

            const className = ticketClass === 'vip' ? 'VIP' : 'Regular';
            const catName = category ? category.name : (age <= 2 ? 'Infant' : age <= 12 ? 'Child' : 'Adult');
            const pName = nameInput.value || 'Passenger ' + (parseInt(idx) + 1);
            summaryHtml += '<div class="flex justify-between"><span>' + pName + ' (' + catName + ' · ' + className + ')</span><span>RM ' + price.toFixed(2) + '</span></div>';
        }
    });

    const insurance = pax * INSURANCE;
    total += insurance;

    if (pax > 0) {
        summaryHtml += '<div class="flex justify-between pt-2 border-t mt-2 text-gray-500"><span>Insurance (' + pax + ' × RM ' + INSURANCE.toFixed(2) + ')</span><span>RM ' + insurance.toFixed(2) + '</span></div>';
    }

    summaryDetails.innerHTML = summaryHtml || '<p class="text-gray-400" data-translate-en="Complete passenger details to see pricing" data-translate-id="Lengkapi detail penumpang untuk melihat harga">Complete passenger details to see pricing</p>';
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

@push('scripts')
<script>
// Country autocomplete for nationality fields
document.addEventListener('DOMContentLoaded', function() {
    var countries = [];

    fetch('{{ route("api.countries") }}')
        .then(function(res) { return res.json(); })
        .then(function(data) { countries = data; })
        .catch(function() {});

    function setupCountryDropdown(inputId, dropdownId) {
        var input = document.getElementById(inputId);
        var dropdown = document.getElementById(dropdownId);
        if (!input || !dropdown) return;
        var selectedIndex = -1;

        input.addEventListener('input', function() {
            var val = this.value.trim().toLowerCase();
            dropdown.innerHTML = '';
            selectedIndex = -1;

            if (!val || countries.length === 0) {
                dropdown.style.display = 'none';
                return;
            }

            var matches = countries.filter(function(c) {
                return c.text.toLowerCase().includes(val);
            }).slice(0, 50);

            if (matches.length === 0) {
                dropdown.innerHTML = '<div class="no-results">No results found</div>';
                dropdown.style.display = 'block';
                return;
            }

            matches.forEach(function(c, i) {
                var div = document.createElement('div');
                div.className = 'country-option';
                div.textContent = c.text;
                div.dataset.value = c.value;
                div.addEventListener('click', function() {
                    input.value = this.dataset.value;
                    dropdown.style.display = 'none';
                });
                dropdown.appendChild(div);
            });

            dropdown.style.display = 'block';
        });

        input.addEventListener('keydown', function(e) {
            var items = dropdown.querySelectorAll('.country-option');
            if (items.length === 0) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (selectedIndex < items.length - 1) selectedIndex++;
                updateSelected(items, selectedIndex);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (selectedIndex > 0) selectedIndex--;
                updateSelected(items, selectedIndex);
            } else if (e.key === 'Enter' && selectedIndex >= 0) {
                e.preventDefault();
                items[selectedIndex].click();
            }
        });

        input.addEventListener('focus', function() {
            if (this.value.trim() && countries.length > 0) {
                this.dispatchEvent(new Event('input'));
            }
        });

        document.addEventListener('click', function(e) {
            if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
    }

    function updateSelected(items, index) {
        items.forEach(function(item, i) {
            item.classList.toggle('selected', i === index);
        });
        if (index >= 0) {
            items[index].scrollIntoView({ block: 'nearest' });
        }
    }

    // Init for all passenger nationality fields
    var passengerCount = {{ $passengerCount }};
    for (var i = 0; i < passengerCount; i++) {
        setupCountryDropdown('nationality-' + i, 'country-dropdown-' + i);
    }
});
</script>
@endpush
