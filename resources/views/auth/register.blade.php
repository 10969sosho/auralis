@extends('layouts.guest')
@section('title', 'Register')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
<style>
.ts-wrapper {
    display: block;
    position: relative;
    --ts-pr-caret: 0;
    --ts-pr-min: 0;
    --ts-pr-clear-button: 0;
}
.ts-wrapper.single .ts-control {
    height: 50px !important;
    min-height: 50px !important;
    line-height: 50px !important;
    border: 1px solid #E9ECEF !important;
    border-radius: 6px !important;
    padding: 0 16px !important;
    font-size: 15px !important;
    font-family: 'Poppins', sans-serif !important;
    color: #252B42 !important;
    background: #fff !important;
    box-shadow: none !important;
    display: flex !important;
    align-items: center !important;
    flex-wrap: nowrap !important;
    cursor: text;
    transition: border-color 0.2s, box-shadow 0.2s;
    outline: none !important;
    overflow: hidden !important;
}
.ts-wrapper.single.is-disabled .ts-control {
    background: #f8f9fa !important;
    opacity: 0.6;
}
.ts-wrapper.single .ts-control .item {
    line-height: 1 !important;
    color: #252B42;
    margin: 0;
}
.ts-wrapper.single .ts-control .placeholder {
    color: #ADB5BD !important;
}
.ts-wrapper.single .ts-control input {
    font-family: 'Poppins', sans-serif !important;
    font-size: 15px !important;
    color: #252B42 !important;
    height: auto !important;
    min-height: 0 !important;
    line-height: 50px !important;
    margin: 0 !important;
    padding: 0 !important;
    border: none !important;
    box-shadow: none !important;
    background: transparent !important;
}
.ts-wrapper.single .ts-control::after {
    content: '' !important;
    display: none !important;
}
.ts-wrapper.focus .ts-control {
    border-color: #0E9AEF !important;
    box-shadow: 0 0 0 3px rgba(14, 154, 239, 0.1) !important;
}
.ts-wrapper.dropdown-active .ts-control {
    border-radius: 6px 6px 0 0 !important;
}
.ts-wrapper .ts-dropdown {
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    color: #252B42;
    border: 1px solid #E9ECEF !important;
    border-top: none !important;
    border-radius: 0 0 6px 6px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    margin: 0 !important;
    z-index: 100;
}
.ts-wrapper .ts-dropdown .option {
    padding: 10px 16px;
    cursor: pointer;
}
.ts-wrapper .ts-dropdown .option.active {
    background: #f0f7ff;
    color: #0E9AEF;
}
.ts-wrapper .ts-dropdown .option:hover {
    background: #f0f7ff;
}
.ts-wrapper .ts-dropdown .option.highlight {
    background: #e3f0ff;
    color: #0E9AEF;
}
.ts-wrapper .ts-dropdown .no-results {
    padding: 10px 16px;
    color: #6C757D;
}
</style>
@endpush

@section('content')
<div class="auth-page">
    <div class="auth-box auth-box-lg">
        <div class="auth-card">
            <div class="auth-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="#0E9AEF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 21h20M6 18l2-6h8l2 6M9 12V7M15 12V7M12 7V3"/><path d="M5 7h14l-2 5H7L5 7Z"/></svg>
                <h2 data-translate-en="Create Account" data-translate-id="Buat Akun">Create Account</h2>
                <p data-translate-en="Join Auralis8 for easy ferry booking" data-translate-id="Bergabung dengan Auralis8 untuk pemesanan tiket feri yang mudah">Join Auralis8 for easy ferry booking</p>
            </div>
            <form action="{{ route('register') }}" method="POST" class="auth-form">
                @csrf
                <div class="auth-form-row">
                    <div class="auth-field">
                        <label for="name" class="auth-label" data-translate-en="Full Name *" data-translate-id="Nama Lengkap *">Full Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required class="auth-input" placeholder="John Doe">
                        @error('name') <p class="auth-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="auth-field">
                        <label for="email" class="auth-label" data-translate-en="Email *" data-translate-id="Email *">Email *</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required class="auth-input" placeholder="your@email.com">
                        @error('email') <p class="auth-error">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="auth-form-row">
                    <div class="auth-field">
                        <label for="phone" class="auth-label" data-translate-en="Phone" data-translate-id="Telepon">Phone</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="auth-input" placeholder="+62...">
                    </div>
                    <div class="auth-field">
                        <label for="nationality" class="auth-label" data-translate-en="Nationality" data-translate-id="Kewarganegaraan">Nationality</label>
                        <select name="nationality" id="nationality" class="auth-input" placeholder="Search nationality...">
                            <option value="">Select nationality</option>
                        </select>
                    </div>
                </div>
                <div class="auth-form-row">
                    <div class="auth-field">
                        <label for="passport_number" class="auth-label" data-translate-en="Passport / KTP" data-translate-id="Paspor / KTP">Passport / KTP</label>
                        <input type="text" name="passport_number" id="passport_number" value="{{ old('passport_number') }}" class="auth-input" placeholder="Optional">
                    </div>
                    <div class="auth-field">
                        <label for="birth_date" class="auth-label" data-translate-en="Birth Date" data-translate-id="Tanggal Lahir">Birth Date</label>
                        <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date') }}" class="auth-input">
                    </div>
                </div>
                <div class="auth-form-row">
                    <div class="auth-field">
                        <label for="gender" class="auth-label" data-translate-en="Gender" data-translate-id="Jenis Kelamin">Gender</label>
                        <select name="gender" id="gender" class="auth-input">
                            <option value="" data-translate-en="Select" data-translate-id="Pilih">Select</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }} data-translate-en="Male" data-translate-id="Laki-laki">Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }} data-translate-en="Female" data-translate-id="Perempuan">Female</option>
                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }} data-translate-en="Other" data-translate-id="Lainnya">Other</option>
                        </select>
                    </div>
                    <div class="auth-field"></div>
                </div>
                <div class="auth-form-row">
                    <div class="auth-field">
                        <label for="password" class="auth-label">Password *</label>
                        <div class="password-input-wrapper">
                            <input type="password" name="password" id="password" required class="auth-input" placeholder="Min. 8 characters">
                            <button type="button" class="password-toggle" onclick="togglePassword('password', this)" tabindex="-1" aria-label="Toggle password visibility">
                                <svg class="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="eye-off-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                        @error('password') <p class="auth-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="auth-field">
                        <label for="password_confirmation" class="auth-label">Confirm Password *</label>
                        <div class="password-input-wrapper">
                            <input type="password" name="password_confirmation" id="password_confirmation" required class="auth-input" placeholder="Repeat password">
                            <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation', this)" tabindex="-1" aria-label="Toggle password visibility">
                                <svg class="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="eye-off-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
                <button type="submit" class="auth-btn" data-translate-en="Create Account" data-translate-id="Buat Akun">Create Account</button>
            </form>
            <p class="auth-footer-text">
                <span data-translate-en="Already have an account?" data-translate-id="Sudah punya akun?">Already have an account?</span> <a href="{{ route('login') }}" data-translate-en="Sign In" data-translate-id="Masuk">Sign In</a>
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
function togglePassword(fieldId, btn) {
    var input = document.getElementById(fieldId);
    if (input.type === 'password') {
        input.type = 'text';
        btn.querySelector('.eye-icon').style.display = 'none';
        btn.querySelector('.eye-off-icon').style.display = '';
    } else {
        input.type = 'password';
        btn.querySelector('.eye-icon').style.display = '';
        btn.querySelector('.eye-off-icon').style.display = 'none';
    }
}
document.addEventListener('DOMContentLoaded', function() {
    var select = document.getElementById('nationality');
    if (!select) return;

    var oldVal = '{{ old('nationality') }}';

    fetch('{{ route('api.countries') }}')
        .then(function(res) { return res.json(); })
        .then(function(options) {
            var ts = new TomSelect('#nationality', {
                valueField: 'value',
                labelField: 'text',
                searchField: 'text',
                options: options,
                placeholder: 'Search nationality...',
                maxOptions: null,
                create: true,
                onChange: function(value) {
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                },
                render: {
                    option: function(item, escape) {
                        return '<div>' + escape(item.text) + '</div>';
                    },
                    item: function(item, escape) {
                        return '<div>' + escape(item.text) + '</div>';
                    }
                }
            });

            if (oldVal) {
                ts.addOption({ value: oldVal, text: oldVal });
                ts.setValue(oldVal);
            }
        })
        .catch(function() {
            // Fallback: allow manual typing if API fails
            var input = document.createElement('input');
            input.type = 'text';
            input.name = 'nationality';
            input.id = 'nationality';
            input.className = 'auth-input';
            input.placeholder = 'e.g. Malaysian / Filipino';
            input.value = oldVal;
            select.parentNode.replaceChild(input, select);
        });
});
</script>
@endpush