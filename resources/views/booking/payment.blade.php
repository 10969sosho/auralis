@extends('layouts.app')
@section('title', 'Payment')

@section('content')
<div class="payment-page">
    <h1 class="payment-title" data-translate-en="Payment" data-translate-id="Pembayaran">Payment</h1>

    @if(session('success'))
    <div class="payment-alert payment-success">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="payment-alert payment-error">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    @if($booking->payment_status === 'awaiting_approval')
    <div class="payment-waiting">
        <div class="payment-waiting-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <h2 data-translate-en="Awaiting Admin Confirmation" data-translate-id="Menunggu Konfirmasi Admin">Awaiting Admin Confirmation</h2>
        <div class="payment-waiting-details">
            <div class="payment-waiting-row">
                <span data-translate-en="Booking Code" data-translate-id="Kode Pemesanan">Booking Code</span>
                <strong>#{{ $booking->booking_code }}</strong>
            </div>
            <div class="payment-waiting-row">
                <span data-translate-en="Status" data-translate-id="Status">Status</span>
                <span class="badge badge-warning" data-translate-en="Awaiting Verification" data-translate-id="Menunggu Verifikasi">Awaiting Verification</span>
            </div>
        </div>
        <div class="payment-actions">
            <a href="{{ route('booking.detail', $booking->booking_code) }}" class="btn btn-primary" data-translate-en="View Booking Details" data-translate-id="Lihat Detail Pemesanan">View Booking Details</a>
        </div>
    </div>
    @elseif(in_array($booking->payment_status, ['paid', 'approved']))
    <div class="payment-waiting">
        <div class="payment-waiting-icon" style="color:#059669;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        </div>
        <h2 data-translate-en="Payment Successful" data-translate-id="Pembayaran Berhasil">Payment Successful</h2>
        <div class="payment-actions">
            <a href="{{ route('booking.success', $booking->booking_code) }}" class="btn btn-primary" data-translate-en="View Tickets" data-translate-id="Lihat Tiket">View Tickets</a>
        </div>
    </div>
    @else
    @php $billCode = $booking->payment?->payment_meta['bill_code'] ?? null; @endphp
    @if($billCode && $booking->payment_status === 'pending')
    <script>window.location.href = '{{ $booking->payment->payment_meta["payment_url"] }}';</script>
    <div class="payment-waiting">
        <p data-translate-en="Redirecting to ToyibPay..." data-translate-id="Mengarahkan ke ToyibPay...">Redirecting to ToyibPay...</p>
        <div class="payment-actions">
            <a href="{{ $booking->payment->payment_meta['payment_url'] }}" class="btn btn-primary" data-translate-en="Proceed to Payment" data-translate-id="Lanjutkan ke Pembayaran">Proceed to Payment</a>
        </div>
    </div>
    @else
    <div class="payment-waiting">
        <p data-translate-en="No payment link available. Please contact support." data-translate-id="Tidak ada tautan pembayaran. Silakan hubungi dukungan.">No payment link available. Please contact support.</p>
        <div class="payment-actions">
            <a href="{{ route('booking.detail', $booking->booking_code) }}" class="btn btn-primary" data-translate-en="View Booking" data-translate-id="Lihat Pemesanan">View Booking</a>
        </div>
    </div>
    @endif
    @endif
</div>
@endsection