@extends('layouts.guest')
@section('title', 'Register')

@section('content')
<div class="auth-page">
    <div class="auth-box auth-box-lg">
        <div class="auth-card">
            <div class="auth-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="#0E9AEF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 21h20M6 18l2-6h8l2 6M9 12V7M15 12V7M12 7V3"/><path d="M5 7h14l-2 5H7L5 7Z"/></svg>
                <h2>Create Account</h2>
                <p>Join ShipTicketing for easy ferry booking</p>
            </div>
            <form action="{{ route('register') }}" method="POST" class="auth-form">
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
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="auth-input" placeholder="+62...">
                    </div>
                    <div class="auth-field">
                        <label for="nationality" class="auth-label">Nationality</label>
                        <input type="text" name="nationality" id="nationality" value="{{ old('nationality') }}" class="auth-input" placeholder="e.g. Malaysian / Filipino">
                    </div>
                </div>
                <div class="auth-form-row">
                    <div class="auth-field">
                        <label for="passport_number" class="auth-label">Passport / KTP</label>
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
                    <div class="auth-field"></div>
                </div>
                <div class="auth-form-row">
                    <div class="auth-field">
                        <label for="password" class="auth-label">Password *</label>
                        <input type="password" name="password" id="password" required class="auth-input" placeholder="Min. 8 characters">
                        @error('password') <p class="auth-error">{{ $message }}</p> @enderror
                    </div>
                    <div class="auth-field">
                        <label for="password_confirmation" class="auth-label">Confirm Password *</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required class="auth-input" placeholder="Repeat password">
                    </div>
                </div>
                <button type="submit" class="auth-btn">Create Account</button>
            </form>
            <p class="auth-footer-text">
                Already have an account? <a href="{{ route('login') }}">Sign In</a>
            </p>
        </div>
    </div>
</div>
@endsection
