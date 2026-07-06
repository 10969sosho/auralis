@extends('layouts.app')
@section('title', 'My Passengers')

@section('content')
<div class="profiles-page">
    <div class="profiles-header">
        <div>
            <h1 class="profiles-title" data-translate-en="My Passengers" data-translate-id="Data Penumpang Saya">My Passengers</h1>
            <p class="profiles-sub" data-translate-en="Saved passenger profiles for faster booking" data-translate-id="Profil penumpang tersimpan untuk pemesanan lebih cepat">Saved passenger profiles for faster booking</p>
        </div>
        <button class="btn btn-primary" onclick="document.getElementById('addProfileModal').classList.add('open')" data-translate-en="Add Passenger" data-translate-id="Tambah Penumpang">
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
                        <button type="submit" class="btn btn-sm btn-danger" data-translate-en="Remove" data-translate-id="Hapus">Remove</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="empty-state" style="grid-column:1/-1;">
                <h3 data-translate-en="No saved passengers yet" data-translate-id="Belum ada data penumpang">No saved passengers yet</h3>
                <p data-translate-en="Add your family or group members for faster booking next time." data-translate-id="Tambahkan anggota keluarga atau grup Anda untuk pemesanan lebih cepat nanti.">Add your family or group members for faster booking next time.</p>
            </div>
        @endforelse
    </div>
</div>

<div class="modal" id="addProfileModal">
    <div class="modal-backdrop" onclick="document.getElementById('addProfileModal').classList.remove('open')"></div>
    <div class="modal-content card card-lg">
        <div class="modal-header">
            <h2 data-translate-en="Add Passenger" data-translate-id="Tambah Penumpang">Add Passenger</h2>
            <button type="button" class="modal-close" onclick="document.getElementById('addProfileModal').classList.remove('open')">&times;</button>
        </div>
        <form action="{{ route('profiles.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="full_name" class="form-label" data-translate-en="Full Name" data-translate-id="Nama Lengkap">Full Name</label>
                <input type="text" name="full_name" id="full_name" required class="form-input">
            </div>
            <div class="form-group">
                <label for="relationship" class="form-label" data-translate-en="Relationship (optional)" data-translate-id="Hubungan (opsional)">Relationship (optional)</label>
                <input type="text" name="relationship" id="relationship" placeholder="e.g. Spouse, Child, Parent" class="form-input" data-translate-en="e.g. Spouse, Child, Parent" data-translate-id="Misal: Pasangan, Anak, Orang Tua">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="gender" class="form-label" data-translate-en="Gender" data-translate-id="Jenis Kelamin">Gender</label>
                    <select name="gender" id="gender" class="form-input">
                        <option value="" data-translate-en="Select" data-translate-id="Pilih">Select</option>
                        <option value="male" data-translate-en="Male" data-translate-id="Laki-laki">Male</option>
                        <option value="female" data-translate-en="Female" data-translate-id="Perempuan">Female</option>
                        <option value="other" data-translate-en="Other" data-translate-id="Lainnya">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="birth_date" class="form-label" data-translate-en="Birth Date" data-translate-id="Tanggal Lahir">Birth Date</label>
                    <input type="date" name="birth_date" id="birth_date" class="form-input">
                </div>
            </div>
            <div class="form-group">
                <label for="nationality" class="form-label" data-translate-en="Nationality" data-translate-id="Kewarganegaraan">Nationality</label>
                <input type="text" name="nationality" id="nationality" class="form-input">
            </div>
            <div class="form-group">
                <label for="passport_number" class="form-label" data-translate-en="Passport / KTP Number" data-translate-id="Nomor Paspor / KTP">Passport / KTP Number</label>
                <input type="text" name="passport_number" id="passport_number" class="form-input">
            </div>
            <div class="form-group">
                <label for="phone" class="form-label" data-translate-en="Phone" data-translate-id="Telepon">Phone</label>
                <input type="text" name="phone" id="phone" class="form-input">
            </div>
            <button type="submit" class="btn btn-primary btn-block" data-translate-en="Save Profile" data-translate-id="Simpan Profil">Save Profile</button>
        </form>
    </div>
</div>
@endsection
