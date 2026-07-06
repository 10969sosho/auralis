@extends('layouts.app')
@section('title', 'Deportation Manifest')

@section('content')
<h1 class="text-2xl font-bold text-gray-900" data-translate-en="Deportation Manifest" data-translate-id="Manifest Deportasi">Manifest: {{ $manifest->manifest_code }}</h1>
<p class="text-gray-600">{{ $manifest->schedule->vessel->name }} — {{ $manifest->schedule->route->origin_port }} → {{ $manifest->schedule->route->destination_port }}</p>
<p class="text-sm text-gray-500"><span data-translate-en="Departure:" data-translate-id="Keberangkatan:">Dep:</span> {{ $manifest->schedule->departure_time->format('d M Y, H:i') }} | <span data-translate-en="Officer:" data-translate-id="Petugas:">Officer:</span> {{ $manifest->officer->name }} | <span data-translate-en="Total Passengers:" data-translate-id="Total Penumpang:">Total:</span> {{ $manifest->total_passengers }}</p>

<div class="mt-6 grid lg:grid-cols-2 gap-6">
    <div class="card">
        <h2 class="text-lg font-semibold" data-translate-en="Add Passenger" data-translate-id="Tambah Penumpang">Add Passenger</h2>
        <form action="{{ route('deportation.passengers.store', $manifest) }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <div class="form-group">
                <label class="form-label" data-translate-en="Full Name *" data-translate-id="Nama Lengkap *">Full Name *</label>
                <input type="text" name="full_name" required class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label" data-translate-en="Gender *" data-translate-id="Jenis Kelamin *">Gender *</label>
                <select name="gender" required class="form-select">
                    <option value="" data-translate-en="Select" data-translate-id="Pilih">Select</option>
                    <option value="male" data-translate-en="Male" data-translate-id="Laki-laki">Male</option>
                    <option value="female" data-translate-en="Female" data-translate-id="Perempuan">Female</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" data-translate-en="Nationality *" data-translate-id="Kewarganegaraan *">Nationality *</label>
                <input type="text" name="nationality" required class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label" data-translate-en="Passport Number *" data-translate-id="Nomor Paspor *">Passport Number *</label>
                <input type="text" name="passport_number" required class="form-input">
            </div>
            <button type="submit" class="btn btn-primary btn-sm" data-translate-en="Add Passenger" data-translate-id="Tambah Penumpang">Add Passenger</button>
        </form>
    </div>

    <div class="card">
        <h2 class="text-lg font-semibold" data-translate-en="Passenger List" data-translate-id="Daftar Penumpang">Passenger List</h2>
        <div class="mt-4 table-wrap">
            <table>
                <thead><tr><th data-translate-en="Name" data-translate-id="Nama Penumpang">Name</th><th data-translate-en="Gender" data-translate-id="Jenis Kelamin">Gender</th><th data-translate-en="Nationality" data-translate-id="Kewarganegaraan">Nationality</th><th data-translate-en="Passport" data-translate-id="Paspor/ID">Passport</th><th data-translate-en="Status" data-translate-id="Status">Status</th></tr></thead>
                <tbody>
                    @forelse($manifest->passengers as $p)
                    <tr>
                        <td>{{ $p->full_name }}</td>
                        <td class="capitalize">{{ $p->gender }}</td>
                        <td>{{ $p->nationality }}</td>
                        <td>{{ $p->passport_number }}</td>
                        <td><span class="badge {{ $p->boarding_status === 'boarded' ? 'badge-green' : 'badge-yellow' }}">{{ $p->boarding_status }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-gray-500 py-4" data-translate-en="No passengers added yet." data-translate-id="Belum ada penumpang ditambahkan.">No passengers added yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
