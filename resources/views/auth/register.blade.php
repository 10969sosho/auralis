@extends('layouts.app')
@section('title', 'Register')
@section('page_class', '')

@section('content')
<div class="flex-center">
    <div class="w-auth">
        <div class="card card-lg">
            <h2 class="text-2xl font-bold text-gray-900">Register</h2>
            <form action="{{ route('register') }}" method="POST" class="mt-6 space-y-4">
                @csrf
                <div class="form-group">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required class="form-input">
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required class="form-input">
                    @error('email') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label for="phone" class="form-label">Phone (optional)</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="form-input">
                </div>
                <div class="form-group">
                    <label for="nationality" class="form-label">Nationality (optional)</label>
                    <input type="text" name="nationality" id="nationality" value="{{ old('nationality') }}" class="form-input">
                </div>
                <div class="form-group">
                    <label for="passport_number" class="form-label">Passport / KTP Number (optional)</label>
                    <input type="text" name="passport_number" id="passport_number" value="{{ old('passport_number') }}" class="form-input">
                </div>
                <div class="form-group">
                    <label for="birth_date" class="form-label">Birth Date (optional)</label>
                    <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date') }}" class="form-input">
                </div>
                <div class="form-group">
                    <label for="gender" class="form-label">Gender (optional)</label>
                    <select name="gender" id="gender" class="form-input">
                        <option value="">Select</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" id="password" required class="form-input">
                    @error('password') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required class="form-input">
                </div>
                <button type="submit" class="btn btn-primary btn-block">Register</button>
            </form>
            <p class="mt-4 text-center text-sm text-gray-600">
                Already have an account? <a href="{{ route('login') }}" class="link">Login</a>
            </p>
        </div>
    </div>
</div>
@endsection
