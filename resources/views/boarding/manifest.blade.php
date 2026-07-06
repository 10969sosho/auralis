@extends('layouts.app')
@section('title', 'Boarding Manifest')

@section('content')
<h1 class="text-2xl font-bold text-gray-900" data-translate-en="Boarding Manifest" data-translate-id="Manifest Boarding">Boarding Manifest</h1>
<p class="text-gray-600">{{ $schedule->vessel->name }} — {{ $schedule->route->origin_port }} → {{ $schedule->route->destination_port }}</p>
<p class="text-gray-500" data-translate-en="Departure:" data-translate-id="Keberangkatan:">Departure: {{ $schedule->departure_time->format('d M Y, H:i') }}</p>

<div class="mt-6 space-y-6">
    @foreach($bookings as $booking)
    <div class="card">
        <h3 class="font-semibold" data-translate-en="Booking:" data-translate-id="Pemesanan:">Booking: {{ $booking->booking_code }} ({{ $booking->user?->name ?? 'Counter Sale' }})</h3>
        <div class="mt-4 table-wrap">
            <table>
                <thead><tr><th data-translate-en="Name" data-translate-id="Nama Penumpang">Name</th><th data-translate-en="Class" data-translate-id="Kelas">Class</th><th data-translate-en="Type" data-translate-id="Tipe">Type</th><th data-translate-en="Passport" data-translate-id="Paspor/ID">Passport</th><th data-translate-en="Status" data-translate-id="Status Boarding">Status</th></tr></thead>
                <tbody>
                    @foreach($booking->passengers as $p)
                    <tr>
                        <td>{{ $p->full_name }}</td>
                        <td class="capitalize">{{ $p->ticket_class }}</td>
                        <td class="capitalize">{{ $p->passenger_type }}</td>
                        <td>{{ $p->passport_number }}</td>
                        <td class="capitalize">{{ $p->ticket?->ticket_status ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach

    @if($bookings->isEmpty())
    <div class="card text-center p-8">
        <p class="text-gray-500" data-translate-en="No passengers in manifest yet." data-translate-id="Belum ada penumpang dalam manifest.">No passengers in manifest yet.</p>
    </div>
    @endif
</div>
@endsection
