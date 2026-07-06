@extends('layouts.guest')
@section('title', 'Register')

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
                        <input type="text" name="nationality" id="nationality" value="{{ old('nationality') }}" class="auth-input" placeholder="e.g. Malaysian / Filipino">
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
                        <label for="password" class="auth-label" data-translate-en="Password *" data-translate-id="Kata Sandi *">Password *</label>
                        <input type="password" name="password" id="password" required class="auth-input" placeholder="Min. 8 characters">
                        @error('password') <p class="auth-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="auth-field">
                        <label for="password_confirmation" class="auth-label" data-translate-en="Confirm Password *" data-translate-id="Konfirmasi Kata Sandi *">Confirm Password *</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required class="auth-input" placeholder="Repeat password">
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