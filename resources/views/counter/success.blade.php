@extends('layouts.app')
@section('title', 'Payment Successful - Counter')

@section('content')
<div class="counter-success-page">
    <div class="success-card">
        <div class="success-icon">✅</div>
        <h2 class="success-title">Payment Successful</h2>
        <p class="success-sub">Booking: <strong>{{ $booking->booking_code }}</strong></p>

        @if($changeAmount > 0)
        <div class="change-box">
            <span class="change-label">Change to return:</span>
            <span class="change-value">RM {{ number_format($changeAmount, 2) }}</span>
        </div>
        @endif

        <div class="booking-details">
            <p><strong>{{ $booking->schedule->vessel->name }}</strong></p>
            <p>{{ $booking->schedule->route->origin_port }} → {{ $booking->schedule->route->destination_port }}</p>
            <p>Departure: {{ $booking->schedule->departure_time->format('d M Y, H:i') }}</p>
            <p>Total: <strong>RM {{ number_format($booking->total_amount, 2) }}</strong></p>
            <p>Passengers: {{ $booking->total_passengers }}</p>
        </div>

        <div class="passengers-mini">
            @foreach($booking->passengers as $p)
            <div class="passenger-row">
                <span>{{ $p->full_name }}</span>
                <span class="text-sm">{{ $p->passenger_type }} · {{ ucfirst($p->ticket_class) }}</span>
                <span class="text-sm" style="color:#2563EB;">{{ $p->ticket->ticket_number ?? '—' }}</span>
                <a href="{{ route('tickets.show', $p->ticket) }}" target="_blank" class="btn btn-outline btn-xs">Print</a>
            </div>
            @endforeach
        </div>

        <div class="success-actions">
            <a href="{{ route('counter.dashboard') }}" class="btn btn-primary">New Sale</a>
            <a href="{{ route('counter.search', ['query' => $booking->booking_code]) }}" class="btn btn-outline">View Booking</a>
        </div>
    </div>
</div>

<style>
.counter-success-page { padding: 40px 0; display: flex; justify-content: center; }
.success-card { background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); padding: 40px; max-width: 560px; width: 100%; text-align: center; }
.success-icon { font-size: 48px; margin-bottom: 12px; }
.success-title { font-size: 24px; font-weight: 700; color: #059669; }
.success-sub { color: #6b7280; margin-top: 6px; margin-bottom: 16px; }
.change-box { background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 10px; padding: 16px; margin-bottom: 20px; }
.change-label { font-size: 0.85rem; color: #065F46; }
.change-value { display: block; font-size: 1.8rem; font-weight: 700; color: #059669; }
.booking-details { text-align: left; background: #f9fafb; border-radius: 8px; padding: 16px; margin-bottom: 16px; }
.booking-details p { margin: 4px 0; font-size: 0.9rem; }
.passengers-mini { text-align: left; margin-bottom: 20px; }
.passenger-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 12px; background: #f9fafb; border-radius: 6px; margin-bottom: 6px; font-size: 0.9rem; flex-wrap: wrap; gap: 6px; border-bottom: 1px solid #e5e7eb; }
.passenger-row:last-child { border-bottom: none; }
.success-actions { display: flex; gap: 10px; justify-content: center; }
</style>
@endsection
