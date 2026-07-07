@extends('layouts.guest')
@section('title', 'Login')

@section('content')
<div class="auth-page">
    <div class="auth-box">
        <div class="auth-card">
            <div class="auth-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="#0E9AEF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 21h20M6 18l2-6h8l2 6M9 12V7M15 12V7M12 7V3"/><path d="M5 7h14l-2 5H7L5 7Z"/></svg>
                <h2 data-translate-en="Welcome Back" data-translate-id="Selamat Datang Kembali">Welcome Back</h2>
                <p data-translate-en="Sign in to your Auralis8 account" data-translate-id="Masuk ke akun Auralis8 Anda">Sign in to your Auralis8 account</p>
            </div>
            <form action="{{ route('login') }}" method="POST" class="auth-form">
                @csrf
                <div class="auth-field">
                    <label for="email" class="auth-label" data-translate-en="Email" data-translate-id="Email">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required class="auth-input" placeholder="your@email.com">
                    @error('email') <p class="auth-error">{{ $message }}</p> @enderror
                </div>
                <div class="auth-field">
                    <label for="password" class="auth-label">Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" name="password" id="password" required class="auth-input" placeholder="Enter your password">
                        <button type="button" class="password-toggle" onclick="togglePassword('password', this)" tabindex="-1" aria-label="Toggle password visibility">
                            <svg class="eye-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg class="eye-off-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                </div>
                <label class="auth-checkbox">
                    <input type="checkbox" name="remember" id="remember">
                    <span data-translate-en="Remember me" data-translate-id="Ingat saya">Remember me</span>
                </label>
                <button type="submit" class="auth-btn" data-translate-en="Sign In" data-translate-id="Masuk">Sign In</button>
            </form>
            <p class="auth-footer-text">
                <span data-translate-en="Don't have an account?" data-translate-id="Belum punya akun?">Don't have an account?</span> <a href="{{ route('register') }}" data-translate-en="Register" data-translate-id="Daftar">Register</a>
            </p>
        </div>
    </div>
</div>
<script>
function togglePassword(id, btn) {
    var input = document.getElementById(id);
    if (!input) return;
    var isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';
    btn.querySelector('.eye-icon').style.display = isPassword ? 'none' : '';
    btn.querySelector('.eye-off-icon').style.display = isPassword ? '' : 'none';
}
</script>
@endsection