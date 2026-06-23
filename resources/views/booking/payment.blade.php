@extends('layouts.app')
@section('title', 'Payment')

@section('content')
<div class="payment-page">
    <h1 class="payment-title">Complete Payment</h1>

    <div class="payment-expiry">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        <span>Booking will expire in <strong>{{ $booking->expires_at->diffForHumans(null, true) }}</strong>.</span>
    </div>

    <div class="payment-grid">
        <div class="payment-card">
            <div class="payment-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 21h20M6 18l2-6h8l2 6M9 12V7M15 12V7M12 7V3"/><path d="M5 7h14l-2 5H7L5 7Z"/></svg>
                Booking #{{ $booking->booking_code }}
            </div>
            <div class="payment-card-body">
                <div class="payment-info-row">
                    <span class="payment-info-label">Vessel</span>
                    <span class="payment-info-value">{{ $booking->schedule->vessel->name }}</span>
                </div>
                <div class="payment-info-row">
                    <span class="payment-info-label">Route</span>
                    <span class="payment-info-value">{{ $booking->schedule->route->origin_port }} → {{ $booking->schedule->route->destination_port }}</span>
                </div>
                <div class="payment-info-row">
                    <span class="payment-info-label">Departure</span>
                    <span class="payment-info-value">{{ $booking->schedule->departure_time->format('d M Y, H:i') }}</span>
                </div>
                <div class="payment-info-row">
                    <span class="payment-info-label">Passengers</span>
                    <span class="payment-info-value">{{ $booking->total_passengers }}</span>
                </div>
                @if($booking->discount_amount > 0)
                <div class="payment-info-row">
                    <span class="payment-info-label">Discount</span>
                    <span class="payment-info-value" style="color:#059669">-MYR {{ number_format($booking->discount_amount, 2) }}</span>
                </div>
                @endif
                <div class="payment-divider"></div>
                <div class="payment-info-row">
                    <span class="payment-info-label">Total</span>
                    <span class="payment-info-value payment-total">MYR {{ number_format($booking->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="payment-card">
            <div class="payment-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                Payment Method
            </div>
            <div class="payment-card-body">
                <form action="{{ route('booking.process-payment', $booking->booking_code) }}" method="POST" class="payment-form">
                    @csrf
                    <div class="payment-options">
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="fpx" required>
                            <div class="payment-option-content">
                                <span class="payment-option-name">FPX (Online Banking)</span>
                                <span class="payment-option-desc">Pay via Malaysian online banking</span>
                            </div>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="ewallet" required>
                            <div class="payment-option-content">
                                <span class="payment-option-name">E-Wallet</span>
                                <span class="payment-option-desc">Touch 'n Go, GrabPay, etc.</span>
                            </div>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="online_banking" required>
                            <div class="payment-option-content">
                                <span class="payment-option-name">Online Banking</span>
                                <span class="payment-option-desc">Manual bank transfer</span>
                            </div>
                        </label>
                    </div>
                    <button type="submit" class="payment-submit">Pay MYR {{ number_format($booking->total_amount, 2) }}</button>
                </form>
            </div>
        </div>
    </div>

    <div class="payment-card" style="margin-top:20px;">
        <div class="payment-card-header">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            Passenger List ({{ $booking->passengers->count() }})
        </div>
        <div class="payment-card-body p-0">
            <div class="payment-table-wrap">
                <table class="payment-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Class</th>
                            <th>Type</th>
                            <th>Passport</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($booking->passengers as $p)
                        <tr>
                            <td class="payment-passenger-name">{{ $p->full_name }}</td>
                            <td class="capitalize">{{ $p->ticket_class }}</td>
                            <td class="capitalize">{{ $p->passenger_type }}</td>
                            <td>{{ $p->passport_number }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
