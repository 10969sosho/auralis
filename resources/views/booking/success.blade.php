@extends('layouts.app')
@section('title', 'Booking Successful')

@section('content')
<div class="success-page">
    <div class="success-hero">
        <div class="success-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <h1 class="success-title">Booking Confirmed!</h1>
        <p class="success-sub">Your booking <strong>#{{ $booking->booking_code }}</strong> has been confirmed.</p>
    </div>

    <div class="success-card">
        <h2 class="success-card-title">Booking Details</h2>
        <div class="success-details">
            <div class="success-row">
                <span class="success-label">Vessel</span>
                <span class="success-value">{{ $booking->schedule->vessel->name }}</span>
            </div>
            <div class="success-row">
                <span class="success-label">Route</span>
                <span class="success-value">{{ $booking->schedule->route->origin_port }} → {{ $booking->schedule->route->destination_port }}</span>
            </div>
            <div class="success-row">
                <span class="success-label">Departure</span>
                <span class="success-value">{{ $booking->schedule->departure_time->format('d M Y, H:i') }}</span>
            </div>
            <div class="success-row">
                <span class="success-label">Amount</span>
                <span class="success-value success-amount">RM {{ number_format($booking->total_amount, 2) }}</span>
            </div>
            <div class="success-row">
                <span class="success-label">Payment</span>
                <span class="success-value capitalize">{{ $booking->payment->payment_method }}</span>
            </div>
        </div>
    </div>

    <div class="success-card">
        <h2 class="success-card-title">Your Tickets ({{ $booking->passengers->count() }})</h2>
        <div class="success-tickets">
            @foreach($booking->passengers as $passenger)
            <div class="success-ticket-item">
                <div class="success-ticket-info">
                    <span class="success-ticket-name">{{ $passenger->full_name }}</span>
                    <span class="success-ticket-meta">{{ $passenger->ticket->ticket_number }} | {{ ucfirst($passenger->ticket_class) }}</span>
                </div>
                <a href="{{ route('tickets.show', $passenger->ticket) }}" class="success-ticket-link">View Ticket</a>
            </div>
            @endforeach
        </div>
    </div>

    <div class="success-actions">
        <a href="{{ route('home') }}" class="success-btn success-btn-outline">Back to Home</a>
        <a href="{{ route('booking.history') }}" class="success-btn success-btn-primary">My Bookings</a>
    </div>
</div>
@endsection
