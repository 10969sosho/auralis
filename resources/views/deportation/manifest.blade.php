@extends('layouts.app')
@section('title', 'Deportation Manifest')

@section('content')
<h1 class="text-2xl font-bold text-gray-900">Manifest: {{ $manifest->manifest_code }}</h1>
<p class="text-gray-600">{{ $manifest->schedule->vessel->name }} — {{ $manifest->schedule->route->origin_port }} → {{ $manifest->schedule->route->destination_port }}</p>
<p class="text-sm text-gray-500">Dep: {{ $manifest->schedule->departure_time->format('d M Y, H:i') }} | Officer: {{ $manifest->officer->name }} | Total: {{ $manifest->total_passengers }}</p>

<div class="mt-6 grid lg:grid-cols-2 gap-6">
    <div class="card">
        <h2 class="text-lg font-semibold">Add Passenger</h2>
        <form action="{{ route('deportation.passengers.store', $manifest) }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <div class="form-group">
                <label class="form-label">Full Name *</label>
                <input type="text" name="full_name" required class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Gender *</label>
                <select name="gender" required class="form-select">
                    <option value="">Select</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Nationality *</label>
                <input type="text" name="nationality" required class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Passport Number *</label>
                <input type="text" name="passport_number" required class="form-input">
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Add Passenger</button>
        </form>
    </div>

    <div class="card">
        <h2 class="text-lg font-semibold">Passenger List</h2>
        <div class="mt-4 table-wrap">
            <table>
                <thead><tr><th>Name</th><th>Gender</th><th>Nationality</th><th>Passport</th><th>Status</th></tr></thead>
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
                    <tr><td colspan="5" class="text-center text-gray-500 py-4">No passengers added yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
