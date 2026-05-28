@extends('layouts.app')
@section('title', 'Booking Successful')

@section('content')
<div class="max-w-2xl mx-auto text-center">
    <div class="card alert-success p-8">
        <h1 class="text-3xl font-bold" style="color:#166534">Booking Confirmed!</h1>
        <p class="mt-2 text-gray-600">Your booking #{{ $booking->booking_code }} has been confirmed.</p>
    </div>

    <div class="mt-6 card text-left">
        <h2 class="text-lg font-semibold">Booking Details</h2>
        <dl class="mt-4 space-y-2 text-sm">
            <div class="flex justify-between"><dt class="text-gray-600">Vessel</dt><dd>{{ $booking->schedule->vessel->name }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-600">Route</dt><dd>{{ $booking->schedule->route->origin_port }} → {{ $booking->schedule->route->destination_port }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-600">Departure</dt><dd>{{ $booking->schedule->departure_time->format('d M Y, H:i') }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-600">Amount</dt><dd>MYR {{ number_format($booking->total_amount, 2) }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-600">Payment</dt><dd>{{ $booking->payment->payment_method }}</dd></div>
        </dl>
    </div>

    <div class="mt-6 card">
        <h2 class="text-lg font-semibold">Your Tickets</h2>
        <div class="mt-4 space-y-3">
            @foreach($booking->passengers as $passenger)
            <div class="flex items-center justify-between border rounded p-3">
                <div class="text-left">
                    <p class="font-medium">{{ $passenger->full_name }}</p>
                    <p class="text-sm text-gray-500">{{ $passenger->ticket->ticket_number }} | {{ ucfirst($passenger->ticket_class) }}</p>
                </div>
                <a href="{{ route('tickets.show', $passenger->ticket) }}" class="link">View Ticket</a>
            </div>
            @endforeach
        </div>
    </div>

    <div class="mt-6 flex justify-center gap-4">
        <a href="{{ route('home') }}" class="btn btn-outline">Home</a>
        <a href="{{ route('booking.history') }}" class="btn btn-primary">My Bookings</a>
    </div>
</div>
@endsection
