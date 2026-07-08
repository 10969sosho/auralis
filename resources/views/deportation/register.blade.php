@extends('layouts.guest')
@section('title', 'Register Deportation Account')

@push('styles')
<style>
.country-wrapper { position: relative; }
.country-input { width: 100%; }
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
<div class="auth-page">
    <div class="auth-box auth-box-lg">
        <div class="auth-card">
            <div class="auth-header">
                <h2>Register Deportation Account</h2>
                <p>Register for deportation ship ticket purchases with bus fare from shelter point</p>
            </div>

            <form action="{{ route('deportation.register.store') }}" method="POST" class="auth-form">
                @csrf

                <div class="auth-form-row">
                    <div class="auth-field">
                        <label for="name" class="auth-label">Full Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required class="auth-input" placeholder="John Doe">
                        @error('name') <p class="auth-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="auth-field">
                        <label for="email" class="auth-label">Email *</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required class="auth-input" placeholder="your@email.com">
                        @error('email') <p class="auth-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="auth-form-row">
                    <div class="auth-field">
                        <label for="phone" class="auth-label">Phone</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="auth-input" placeholder="+60...">
                    </div>
                    <div class="auth-field">
                        <label for="nationality" class="auth-label">Nationality</label>
                        <div class="country-wrapper">
                            <input type="text" name="nationality" id="nationality" class="auth-input country-input" placeholder="Search nationality..." value="{{ old('nationality') }}" autocomplete="off">
                            <div id="countryDropdown" class="country-dropdown"></div>
                        </div>
                        @error('nationality') <p class="auth-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="auth-form-row">
                    <div class="auth-field">
                        <label for="passport_number" class="auth-label">Passport Number</label>
                        <input type="text" name="passport_number" id="passport_number" value="{{ old('passport_number') }}" class="auth-input" placeholder="Optional">
                    </div>
                    <div class="auth-field">
                        <label for="birth_date" class="auth-label">Birth Date</label>
                        <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date') }}" class="auth-input">
                    </div>
                </div>

                <div class="auth-form-row">
                    <div class="auth-field">
                        <label for="gender" class="auth-label">Gender</label>
                        <select name="gender" id="gender" class="auth-input">
                            <option value="">Select</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="auth-field">
                        <label for="shelter_point" class="auth-label">Shelter Point *</label>
                        <select name="shelter_point" id="shelter_point" required class="auth-input">
                            <option value="">Select shelter point</option>
                            <option value="tawau" {{ old('shelter_point') == 'tawau' ? 'selected' : '' }}>Tawau (+RM30)</option>
                            <option value="sandakan" {{ old('shelter_point') == 'sandakan' ? 'selected' : '' }}>Sandakan (+RM30)</option>
                            <option value="kinabalu_papar" {{ old('shelter_point') == 'kinabalu_papar' ? 'selected' : '' }}>Kinabalu (Papar) (+RM55)</option>
                            <option value="kinabalu_menggatal" {{ old('shelter_point') == 'kinabalu_menggatal' ? 'selected' : '' }}>Kinabalu (Menggatal) (+RM50)</option>
                        </select>
                        @error('shelter_point') <p class="auth-error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="auth-form-row">
                    <div class="auth-field">
                        <label for="password" class="auth-label">Password *</label>
                        <div class="pw-field">
                            <input type="password" name="password" id="password" required class="auth-input" placeholder="Min. 8 characters">
                            <button type="button" class="pw-show" onclick="var i=this.previousElementSibling;var p=i.type==='password';i.type=p?'text':'password';this.classList.toggle('pw-visible',p)" aria-label="Toggle password visibility">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                        @error('password') <p class="auth-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="auth-field">
                        <label for="password_confirmation" class="auth-label">Confirm Password *</label>
                        <div class="pw-field">
                            <input type="password" name="password_confirmation" id="password_confirmation" required class="auth-input" placeholder="Repeat password">
                            <button type="button" class="pw-show" onclick="var i=this.previousElementSibling;var p=i.type==='password';i.type=p?'text':'password';this.classList.toggle('pw-visible',p)" aria-label="Toggle password visibility">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="submit" class="auth-btn">Register Deportation Account</button>
            </form>

            <p class="auth-footer-text">
                Already have an account? <a href="{{ route('login') }}">Sign In</a>
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var input = document.getElementById('nationality');
    var dropdown = document.getElementById('countryDropdown');
    var countries = [];
    var selectedIndex = -1;

    if (!input) return;

    fetch('{{ route("api.countries") }}')
        .then(function(res) { return res.json(); })
        .then(function(data) {
            countries = data;
        })
        .catch(function() {});

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

        if (e.key === 'ArrowDown') { e.preventDefault(); if (selectedIndex < items.length - 1) selectedIndex++; updateSelected(items); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); if (selectedIndex > 0) selectedIndex--; updateSelected(items); }
        else if (e.key === 'Enter' && selectedIndex >= 0) { e.preventDefault(); items[selectedIndex].click(); }
    });

    function updateSelected(items) {
        items.forEach(function(item, i) { item.classList.toggle('selected', i === selectedIndex); });
        if (selectedIndex >= 0) items[selectedIndex].scrollIntoView({ block: 'nearest' });
    }

    document.addEventListener('click', function(e) {
        if (!input.contains(e.target) && !dropdown.contains(e.target)) dropdown.style.display = 'none';
    });

    input.addEventListener('focus', function() {
        if (this.value.trim() && countries.length > 0) {
            this.dispatchEvent(new Event('input'));
        }
    });
});
</script>
@endpush
