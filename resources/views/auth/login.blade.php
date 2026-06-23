@extends('layouts.guest')
@section('title', 'Login')

@section('content')
<div class="auth-page">
    <div class="auth-box">
        <div class="auth-card">
            <div class="auth-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="#0E9AEF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 21h20M6 18l2-6h8l2 6M9 12V7M15 12V7M12 7V3"/><path d="M5 7h14l-2 5H7L5 7Z"/></svg>
                <h2>Welcome Back</h2>
                <p>Sign in to your ShipTicketing account</p>
            </div>
            <form action="{{ route('login') }}" method="POST" class="auth-form">
                @csrf
                <div class="auth-field">
                    <label for="email" class="auth-label">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required class="auth-input" placeholder="your@email.com">
                    @error('email') <p class="auth-error">{{ $message }}</p> @enderror
                </div>
                <div class="auth-field">
                    <label for="password" class="auth-label">Password</label>
                    <input type="password" name="password" id="password" required class="auth-input" placeholder="Enter your password">
                </div>
                <label class="auth-checkbox">
                    <input type="checkbox" name="remember" id="remember">
                    <span>Remember me</span>
                </label>
                <button type="submit" class="auth-btn">Sign In</button>
            </form>
            <p class="auth-footer-text">
                Don't have an account? <a href="{{ route('register') }}">Register</a>
            </p>
        </div>
    </div>
</div>
@endsection
