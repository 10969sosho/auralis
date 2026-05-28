@extends('layouts.app')
@section('title', 'Login')
@section('page_class', '')

@section('content')
<div class="flex-center">
    <div class="w-auth">
        <div class="card card-lg">
            <h2 class="text-2xl font-bold text-gray-900">Login</h2>
            <form action="{{ route('login') }}" method="POST" class="mt-6 space-y-4">
                @csrf
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required class="form-input">
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" required class="form-input">
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember" class="text-sm text-gray-600">Remember me</label>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
            <p class="mt-4 text-center text-sm text-gray-600">
                Don't have an account? <a href="{{ route('register') }}" class="link">Register</a>
            </p>
        </div>
    </div>
</div>
@endsection
