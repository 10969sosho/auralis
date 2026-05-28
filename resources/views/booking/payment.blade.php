@extends('layouts.app')
@section('title', 'Payment')

@section('content')
<h1 class="text-2xl font-bold text-gray-900">Payment</h1>
<div class="mt-4 alert alert-warning">
    Booking will expire in <strong>{{ $booking->expires_at->diffForHumans(null, true) }}</strong>.
</div>

<div class="mt-6 grid lg:grid-cols-2 gap-6">
    <div class="card">
        <h2 class="text-lg font-semibold">Booking #{{ $booking->booking_code }}</h2>
        <div class="mt-4 space-y-2 text-sm">
            <p><strong>Vessel:</strong> {{ $booking->schedule->vessel->name }}</p>
            <p><strong>Route:</strong> {{ $booking->schedule->route->origin_port }} → {{ $booking->schedule->route->destination_port }}</p>
            <p><strong>Departure:</strong> {{ $booking->schedule->departure_time->format('d M Y, H:i') }}</p>
            <p><strong>Passengers:</strong> {{ $booking->total_passengers }}</p>
            @if($booking->discount_amount > 0)
                <p><strong>Discount:</strong> -MYR {{ number_format($booking->discount_amount, 2) }}</p>
            @endif
            <p class="text-lg font-bold text-blue-600">Total: MYR {{ number_format($booking->total_amount, 2) }}</p>
        </div>
    </div>

    <div class="card">
        <h2 class="text-lg font-semibold">Select Payment Method</h2>
        <form action="{{ route('booking.process-payment', $booking->booking_code) }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <label class="flex items-center gap-3 border rounded p-3 cursor-pointer" style="cursor:pointer">
                <input type="radio" name="payment_method" value="fpx" required>
                <span class="font-medium">FPX (Online Banking)</span>
            </label>
            <label class="flex items-center gap-3 border rounded p-3 cursor-pointer" style="cursor:pointer">
                <input type="radio" name="payment_method" value="ewallet" required>
                <span class="font-medium">E-Wallet</span>
            </label>
            <label class="flex items-center gap-3 border rounded p-3 cursor-pointer" style="cursor:pointer">
                <input type="radio" name="payment_method" value="online_banking" required>
                <span class="font-medium">Online Banking</span>
            </label>
            <button type="submit" class="btn btn-success btn-block btn-lg">Pay MYR {{ number_format($booking->total_amount, 2) }}</button>
        </form>
    </div>
</div>

<div class="mt-6 card">
    <h2 class="text-lg font-semibold">Passenger List</h2>
    <div class="mt-4 table-wrap">
        <table>
            <thead><tr><th>Name</th><th>Class</th><th>Type</th><th>Passport</th></tr></thead>
            <tbody>
                @foreach($booking->passengers as $p)
                <tr>
                    <td>{{ $p->full_name }}</td>
                    <td class="capitalize">{{ $p->ticket_class }}</td>
                    <td class="capitalize">{{ $p->passenger_type }}</td>
                    <td>{{ $p->passport_number }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
