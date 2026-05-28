@extends('layouts.app')
@section('title', 'My Passengers')

@section('content')
<div class="profiles-page">
    <div class="profiles-header">
        <div>
            <h1 class="profiles-title">My Passengers</h1>
            <p class="profiles-sub">Saved passenger profiles for faster booking</p>
        </div>
        <button class="btn btn-primary" onclick="document.getElementById('addProfileModal').classList.add('open')">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Passenger
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="profiles-grid">
        @forelse($profiles as $profile)
            <div class="card profile-card">
                <div class="profile-card-avatar">
                    <span>{{ substr($profile->full_name, 0, 1) }}</span>
                </div>
                <div class="profile-card-body">
                    <h3 class="profile-card-name">{{ $profile->full_name }}</h3>
                    @if($profile->relationship)
                        <span class="profile-card-rel">{{ $profile->relationship }}</span>
                    @endif
                    <div class="profile-card-details">
                        @if($profile->nationality)
                            <span>{{ $profile->nationality }}</span>
                        @endif
                        @if($profile->passport_number)
                            <span>Passport: {{ $profile->passport_number }}</span>
                        @endif
                        @if($profile->gender)
                            <span>{{ ucfirst($profile->gender) }}</span>
                        @endif
                        @if($profile->birth_date)
                            <span>{{ $profile->birth_date->format('d M Y') }}</span>
                        @endif
                        @if($profile->phone)
                            <span>{{ $profile->phone }}</span>
                        @endif
                    </div>
                </div>
                <div class="profile-card-actions">
                    <form action="{{ route('profiles.destroy', $profile) }}" method="POST" onsubmit="return confirm('Remove this passenger profile?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="empty-state" style="grid-column:1/-1;">
                <h3>No saved passengers yet</h3>
                <p>Add your family or group members for faster booking next time.</p>
            </div>
        @endforelse
    </div>
</div>

<div class="modal" id="addProfileModal">
    <div class="modal-backdrop" onclick="document.getElementById('addProfileModal').classList.remove('open')"></div>
    <div class="modal-content card card-lg">
        <div class="modal-header">
            <h2>Add Passenger</h2>
            <button type="button" class="modal-close" onclick="document.getElementById('addProfileModal').classList.remove('open')">&times;</button>
        </div>
        <form action="{{ route('profiles.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="full_name" class="form-label">Full Name</label>
                <input type="text" name="full_name" id="full_name" required class="form-input">
            </div>
            <div class="form-group">
                <label for="relationship" class="form-label">Relationship (optional)</label>
                <input type="text" name="relationship" id="relationship" placeholder="e.g. Spouse, Child, Parent" class="form-input">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="gender" class="form-label">Gender</label>
                    <select name="gender" id="gender" class="form-input">
                        <option value="">Select</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="birth_date" class="form-label">Birth Date</label>
                    <input type="date" name="birth_date" id="birth_date" class="form-input">
                </div>
            </div>
            <div class="form-group">
                <label for="nationality" class="form-label">Nationality</label>
                <input type="text" name="nationality" id="nationality" class="form-input">
            </div>
            <div class="form-group">
                <label for="passport_number" class="form-label">Passport / KTP Number</label>
                <input type="text" name="passport_number" id="passport_number" class="form-input">
            </div>
            <div class="form-group">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" name="phone" id="phone" class="form-input">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Save Profile</button>
        </form>
    </div>
</div>
@endsection
