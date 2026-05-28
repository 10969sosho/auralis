@extends('layouts.app')
@section('title', 'Booking Detail')

@section('content')

<div class="detail-page">
    <div class="detail-top">
        <a href="{{ route('booking.history') }}" class="detail-back">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            My Bookings
        </a>
        @php
            $bsMap = [
                'paid' => ['label' => 'Paid', 'class' => 'bs-green'],
                'pending_payment' => ['label' => 'Pending Payment', 'class' => 'bs-yellow'],
                'used' => ['label' => 'Completed', 'class' => 'bs-blue'],
                'expired' => ['label' => 'Expired', 'class' => 'bs-red'],
                'cancelled' => ['label' => 'Cancelled', 'class' => 'bs-red'],
                'refund_requested' => ['label' => 'Refund Requested', 'class' => 'bs-orange'],
                'refunded' => ['label' => 'Refunded', 'class' => 'bs-gray'],
            ];
            $bs = $bsMap[$booking->booking_status] ?? ['label' => str_replace('_', ' ', ucfirst($booking->booking_status)), 'class' => 'bs-gray'];
        @endphp
        <div class="detail-top-right">
            <span class="detail-status {{ $bs['class'] }}">{{ $bs['label'] }}</span>
        </div>
    </div>

    <div class="detail-hero">
        <div class="detail-hero-code">#{{ $booking->booking_code }}</div>
        <p class="detail-hero-sub">Booking confirmed — {{ $booking->created_at->format('d M Y, H:i') }}</p>
    </div>

    <div class="detail-grid">
        <div class="detail-card">
            <div class="detail-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 21h20M6 18l2-6h8l2 6M9 12V7M15 12V7M12 7V3"/><path d="M5 7h14l-2 5H7L5 7Z"/><circle cx="12" cy="7" r="1.5"/></svg>
                Trip Details
            </div>
            <div class="detail-card-body">
                <div class="detail-row">
                    <span class="detail-row-label">Vessel</span>
                    <span class="detail-row-value">{{ $booking->schedule->vessel->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-row-label">Route</span>
                    <span class="detail-row-value">{{ $booking->schedule->route->origin_port }} → {{ $booking->schedule->route->destination_port }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-row-label">Departure</span>
                    <span class="detail-row-value">{{ $booking->schedule->departure_time->format('d M Y, H:i') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-row-label">Arrival</span>
                    <span class="detail-row-value">{{ $booking->schedule->arrival_time->format('d M Y, H:i') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-row-label">Duration</span>
                    <span class="detail-row-value">
                        {{ $booking->schedule->departure_time->diffInMinutes($booking->schedule->arrival_time) >= 60
                            ? floor($booking->schedule->departure_time->diffInMinutes($booking->schedule->arrival_time) / 60).'h '.($booking->schedule->departure_time->diffInMinutes($booking->schedule->arrival_time) % 60).'m'
                            : $booking->schedule->departure_time->diffInMinutes($booking->schedule->arrival_time).'m' }}
                    </span>
                </div>
                <div class="detail-divider"></div>
                <div class="detail-row">
                    <span class="detail-row-label">Total Amount</span>
                    <span class="detail-row-value detail-row-amount">MYR {{ number_format($booking->total_amount, 2) }}</span>
                </div>
                @if($booking->discount_amount > 0)
                <div class="detail-row">
                    <span class="detail-row-label">Discount</span>
                    <span class="detail-row-value" style="color:#059669">-MYR {{ number_format($booking->discount_amount, 2) }}</span>
                </div>
                @endif
                @if($booking->payment)
                <div class="detail-divider"></div>
                <div class="detail-row">
                    <span class="detail-row-label">Payment Method</span>
                    <span class="detail-row-value capitalize">{{ $booking->payment->payment_method ?? '—' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-row-label">Transaction ID</span>
                    <span class="detail-row-value">{{ $booking->payment->transaction_id ?? '—' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-row-label">Paid At</span>
                    <span class="detail-row-value">{{ $booking->paid_at ? $booking->paid_at->format('d M Y, H:i') : '—' }}</span>
                </div>
                @endif
            </div>
        </div>

        <div class="detail-card">
            <div class="detail-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Passengers ({{ $booking->passengers->count() }})
            </div>
            <div class="detail-card-body">
                <div class="detail-passengers">
                    @foreach($booking->passengers as $passenger)
                        <div class="detail-passenger-card">
                            <div class="detail-passenger-top">
                                <div class="detail-passenger-avatar">{{ substr($passenger->full_name, 0, 1) }}</div>
                                <div class="detail-passenger-main">
                                    <span class="detail-passenger-name">{{ $passenger->full_name }}</span>
                                    <span class="detail-passenger-meta">
                                        {{ ucfirst($passenger->passenger_type) }} · {{ ucfirst($passenger->ticket_class) }}
                                    </span>
                                </div>
                                @if($passenger->ticket)
                                    <span class="detail-passenger-ticket-status {{ $passenger->ticket->ticket_status === 'active' ? 'ts-active' : ($passenger->ticket->ticket_status === 'used' ? 'ts-used' : 'ts-inactive') }}">
                                        {{ ucfirst($passenger->ticket->ticket_status) }}
                                    </span>
                                @endif
                            </div>
                            <div class="detail-passenger-body">
                                <div class="detail-passenger-info">
                                    <span class="detail-row-label">Passport</span>
                                    <span class="detail-row-value">{{ $passenger->passport_number }}</span>
                                </div>
                                @if($passenger->ticket)
                                <div class="detail-passenger-info">
                                    <span class="detail-row-label">Ticket No.</span>
                                    <span class="detail-row-value" style="font-size:0.75rem;word-break:break-all">{{ $passenger->ticket->ticket_number }}</span>
                                </div>
                                @endif
                            </div>
                            @if($passenger->ticket)
                            <div class="detail-passenger-actions">
                                <a href="{{ route('tickets.show', $passenger->ticket) }}" class="detail-passenger-btn">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                    View E-Ticket
                                </a>
                                <a href="{{ route('tickets.download', $passenger->ticket) }}" class="detail-passenger-btn detail-passenger-btn-secondary">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                    PDF
                                </a>
                            </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @if($booking->refund)
        <div class="detail-refund-card">
            <div class="detail-refund-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            </div>
            <div class="detail-refund-body">
                <h3 class="detail-refund-title">Refund {{ ucfirst($booking->refund->refund_status) }}</h3>
                <div class="detail-refund-meta">
                    <span>Amount: <strong>MYR {{ number_format($booking->refund->refund_amount, 2) }}</strong></span>
                    <span>Status: <strong class="capitalize">{{ $booking->refund->refund_status }}</strong></span>
                </div>
                @if($booking->refund->refund_reason)
                    <p class="detail-refund-reason"><span>Reason:</span> {{ $booking->refund->refund_reason }}</p>
                @endif
                @if($booking->refund->admin_notes)
                    <p class="detail-refund-reason"><span>Admin notes:</span> {{ $booking->refund->admin_notes }}</p>
                @endif
                <p class="detail-refund-notice">Refund will be processed manually via WhatsApp by admin.</p>
            </div>
        </div>
    @elseif($booking->booking_status === 'paid' && !$booking->schedule->isH6Passed)
        <div class="detail-refund-card detail-refund-card-orange">
            <div class="detail-refund-icon" style="color:#EA580C">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <div class="detail-refund-body">
                <h3 class="detail-refund-title">Request Refund</h3>
                <p class="detail-refund-info">Refund policy: <strong>25%</strong> of total amount (MYR {{ number_format($booking->total_amount * 0.25, 2) }}). Request must be made before H-6 departure.</p>
                <form action="{{ route('booking.refund', $booking->booking_code) }}" method="POST" class="detail-refund-form">
                    @csrf
                    <textarea name="refund_reason" required placeholder="Tell us your reason for refund..." class="detail-refund-textarea" rows="3"></textarea>
                    <button type="submit" class="detail-refund-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                        Request Refund
                    </button>
                </form>
            </div>
        </div>
    @endif

    <div class="detail-actions">
        <a href="{{ route('booking.history') }}" class="detail-action-btn detail-action-outline">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to My Bookings
        </a>
    </div>
</div>

@endsection
