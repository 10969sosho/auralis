@extends('layouts.app')
@section('title', 'Boarding Manifest')

@section('content')
<h1 class="text-2xl font-bold text-gray-900">Boarding Manifest</h1>
<p class="text-gray-600">{{ $schedule->vessel->name }} — {{ $schedule->route->origin_port }} → {{ $schedule->route->destination_port }}</p>
<p class="text-gray-500">Departure: {{ $schedule->departure_time->format('d M Y, H:i') }}</p>

<div class="mt-6 space-y-6">
    @foreach($bookings as $booking)
    <div class="card">
        <h3 class="font-semibold">Booking: {{ $booking->booking_code }} ({{ $booking->user->name }})</h3>
        <div class="mt-4 table-wrap">
            <table>
                <thead><tr><th>Name</th><th>Class</th><th>Type</th><th>Passport</th><th>Status</th></tr></thead>
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
        <p class="text-gray-500">No passengers in manifest yet.</p>
    </div>
    @endif
</div>
@endsection
