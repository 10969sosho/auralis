@extends('layouts.app')
@section('title', 'Booking Detail')

@section('content')

<div class="detail-page">
    <div class="detail-top">
        <a href="{{ route('booking.history') }}" class="detail-back">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            <span data-translate-en="My Bookings" data-translate-id="Pemesanan Saya">My Bookings</span>
        </a>
        @php
            $statusLabel = \App\Helpers\StatusHelper::effectiveStatusLabel($booking);
            $badgeClass = \App\Helpers\StatusHelper::effectiveBadgeClass($booking);
        @endphp
        <div class="detail-top-right">
            <span class="detail-status {{ $badgeClass }}">{{ $statusLabel }}</span>
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
                <span data-translate-en="Trip Details" data-translate-id="Detail Perjalanan">Trip Details</span>
            </div>
            <div class="detail-card-body">
                <div class="detail-row">
                    <span class="detail-row-label" data-translate-en="Vessel" data-translate-id="Kapal">Vessel</span>
                    <span class="detail-row-value">{{ $booking->schedule->vessel->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-row-label" data-translate-en="Route" data-translate-id="Rute">Route</span>
                    <span class="detail-row-value">{{ $booking->schedule->route->origin_port }} → {{ $booking->schedule->route->destination_port }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-row-label" data-translate-en="Departure" data-translate-id="Keberangkatan">Departure</span>
                    <span class="detail-row-value">{{ $booking->schedule->departure_time->format('d M Y, H:i') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-row-label" data-translate-en="Arrival" data-translate-id="Kedatangan">Arrival</span>
                    <span class="detail-row-value">{{ $booking->schedule->arrival_time->format('d M Y, H:i') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-row-label" data-translate-en="Duration" data-translate-id="Durasi">Duration</span>
                    <span class="detail-row-value">
                        {{ $booking->schedule->departure_time->diffInMinutes($booking->schedule->arrival_time) >= 60
                            ? floor($booking->schedule->departure_time->diffInMinutes($booking->schedule->arrival_time) / 60).'h '.($booking->schedule->departure_time->diffInMinutes($booking->schedule->arrival_time) % 60).'m'
                            : $booking->schedule->departure_time->diffInMinutes($booking->schedule->arrival_time).'m' }}
                    </span>
                </div>
                <div class="detail-divider"></div>
                <div class="detail-row">
                    <span class="detail-row-label" data-translate-en="Total Amount" data-translate-id="Jumlah Total">Total Amount</span>
                    <span class="detail-row-value detail-row-amount">RM {{ number_format($booking->total_amount, 2) }}</span>
                </div>
                @if($booking->discount_amount > 0)
                <div class="detail-row">
                    <span class="detail-row-label" data-translate-en="Discount" data-translate-id="Diskon">Discount</span>
                    <span class="detail-row-value" style="color:#059669">-RM {{ number_format($booking->discount_amount, 2) }}</span>
                </div>
                @endif
                @if($booking->payment)
                <div class="detail-divider"></div>
                <div class="detail-row">
                    <span class="detail-row-label" data-translate-en="Payment Method" data-translate-id="Metode Pembayaran">Payment Method</span>
                    <span class="detail-row-value capitalize">{{ $booking->payment->payment_method ?? '—' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-row-label" data-translate-en="Transaction ID" data-translate-id="ID Transaksi">Transaction ID</span>
                    <span class="detail-row-value">{{ $booking->payment->transaction_id ?? '—' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-row-label" data-translate-en="Paid At" data-translate-id="Dibayar Pada">Paid At</span>
                    <span class="detail-row-value">{{ $booking->paid_at ? $booking->paid_at->format('d M Y, H:i') : '—' }}</span>
                </div>
                @endif
            </div>
        </div>

        <div class="detail-card">
            <div class="detail-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                <span data-translate-en="Passengers" data-translate-id="Penumpang">Passengers ({{ $booking->passengers->count() }})</span>
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
                                    <span class="detail-row-label" data-translate-en="Passport" data-translate-id="Paspor">Passport</span>
                                    <span class="detail-row-value">{{ $passenger->passport_number }}</span>
                                </div>
                                @if($passenger->ticket)
                                <div class="detail-passenger-info">
                                    <span class="detail-row-label" data-translate-en="Ticket No." data-translate-id="No. Tiket">Ticket No.</span>
                                    <span class="detail-row-value" style="font-size:0.75rem;word-break:break-all">{{ $passenger->ticket->ticket_number }}</span>
                                </div>
                                @endif
                            </div>
                            @if($passenger->ticket)
                            <div class="detail-passenger-actions">
                                <a href="{{ route('tickets.show', $passenger->ticket) }}" class="detail-passenger-btn" data-translate-en="View E-Ticket" data-translate-id="Lihat E-Tiket">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                    View E-Ticket
                                </a>
                                <a href="{{ route('tickets.download', $passenger->ticket) }}" class="detail-passenger-btn detail-passenger-btn-secondary" data-translate-en="PDF" data-translate-id="PDF">
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

    @if($booking->booking_status === 'pending_payment' && $booking->payment_status === 'pending')
    <div class="detail-refund-card" style="border-color:#fbbf24;background:#fffbeb;">
        <div class="detail-refund-icon" style="color:#d97706">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
        </div>
        <div class="detail-refund-body">
            <h3 class="detail-refund-title" style="color:#d97706" data-translate-en="Payment Pending" data-translate-id="Pembayaran Tertunda">Payment Pending</h3>
            <p class="detail-refund-info" style="color:#6b7280;margin-bottom:16px;" data-translate-en="Your booking is waiting for payment. Please complete the payment to confirm your tickets." data-translate-id="Pemesanan Anda menunggu pembayaran. Silakan selesaikan pembayaran untuk mengonfirmasi tiket Anda.">Your booking is waiting for payment. Please complete the payment to confirm your tickets.</p>
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <a href="{{ route('booking.payment', $booking->booking_code) }}" class="detail-refund-btn" style="background:#d97706;color:#fff;" data-translate-en="Continue to Payment" data-translate-id="Lanjutkan ke Pembayaran">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    Continue to Payment
                </a>
            </div>
        </div>
    </div>
    @endif

    @if($booking->payment_status === 'rejected' && $booking->payment)
    <div class="detail-refund-card" style="border-color:#fecaca;background:#fef2f2;">
        <div class="detail-refund-icon" style="color:#dc2626">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        </div>
        <div class="detail-refund-body">
            <h3 class="detail-refund-title" style="color:#dc2626" data-translate-en="Payment Rejected" data-translate-id="Pembayaran Ditolak">Payment Rejected</h3>
            @if($booking->payment->rejection_reason)
                <div class="detail-refund-meta">
                    <span><strong data-translate-en="Reason:" data-translate-id="Alasan:">Reason:</strong> {{ $booking->payment->rejection_reason }}</span>
                </div>
            @endif
            <p class="detail-refund-notice" style="color:#6b7280;margin-bottom:16px;" data-translate-en="Your proof of transfer was not accepted. Please re-upload a valid proof or contact support." data-translate-id="Bukti transfer Anda tidak diterima. Silakan unggah ulang bukti yang valid atau hubungi dukungan.">Your proof of transfer was not accepted. Please re-upload a valid proof or contact support.</p>
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <a href="{{ route('booking.payment', $booking->booking_code) }}" class="detail-refund-btn" data-translate-en="Re-upload Proof" data-translate-id="Unggah Ulang Bukti">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    Re-upload Proof
                </a>
                <a href="https://wa.me/6285212345678?text=Help%3A%20Booking%20%23{{ $booking->booking_code }}%20-%20Payment%20Rejected" target="_blank" class="detail-refund-btn" style="background:#25D366;color:#fff;" data-translate-en="Contact Support" data-translate-id="Hubungi Dukungan">
                    <svg viewBox="0 0 24 24" fill="currentColor" stroke="none" style="width:16px;height:16px;"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Contact Support
                </a>
            </div>
        </div>
    </div>
    @endif

    @if($booking->refund)
        <div class="detail-refund-card">
            <div class="detail-refund-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            </div>
            <div class="detail-refund-body">
                <h3 class="detail-refund-title">Refund {{ ucfirst($booking->refund->refund_status) }}</h3>
                <div class="detail-refund-meta">
                    <span><strong data-translate-en="Amount:" data-translate-id="Jumlah:">Amount:</strong> <strong>RM {{ number_format($booking->refund->refund_amount, 2) }}</strong></span>
                    <span><strong data-translate-en="Status:" data-translate-id="Status:">Status:</strong> <strong class="capitalize">{{ $booking->refund->refund_status }}</strong></span>
                </div>
                @if($booking->refund->refund_reason)
                    <p class="detail-refund-reason"><span data-translate-en="Reason:" data-translate-id="Alasan:">Reason:</span> {{ $booking->refund->refund_reason }}</p>
                @endif
                @if($booking->refund->admin_notes)
                    <p class="detail-refund-reason"><span data-translate-en="Admin notes:" data-translate-id="Catatan admin:">Admin notes:</span> {{ $booking->refund->admin_notes }}</p>
                @endif
                <p class="detail-refund-notice" data-translate-en="Refund will be processed manually via WhatsApp by admin." data-translate-id="Pengembalian dana akan diproses secara manual melalui WhatsApp oleh admin.">Refund will be processed manually via WhatsApp by admin.</p>
            </div>
        </div>
    @elseif($booking->booking_status === 'paid' && !$booking->schedule->isH6Passed)
        <div class="detail-refund-card detail-refund-card-orange">
            <div class="detail-refund-icon" style="color:#EA580C">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <div class="detail-refund-body">
                <h3 class="detail-refund-title" data-translate-en="Request Refund" data-translate-id="Ajukan Pengembalian Dana">Request Refund</h3>
                <p class="detail-refund-info" data-translate-en="Refund policy: 25% of total amount (RM ...). Request must be made before H-6 departure." data-translate-id="Kebijakan pengembalian dana: 25% dari jumlah total (RM ...). Permintaan harus diajukan sebelum H-6 keberangkatan.">Refund policy: <strong>25%</strong> of total amount (RM {{ number_format($booking->total_amount * 0.25, 2) }}). Request must be made before H-6 departure.</p>
                <form action="{{ route('booking.refund', $booking->booking_code) }}" method="POST" class="detail-refund-form">
                    @csrf
                    <textarea name="refund_reason" required placeholder="Tell us your reason for refund..." class="detail-refund-textarea" rows="3" data-translate-en="Tell us your reason for refund..." data-translate-id="Ceritakan alasan Anda untuk pengembalian dana..."></textarea>
                    <button type="submit" class="detail-refund-btn" data-translate-en="Request Refund" data-translate-id="Ajukan Pengembalian Dana">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                        Request Refund
                    </button>
                </form>
            </div>
        </div>
    @endif

    <div class="detail-actions">
        <a href="{{ route('booking.history') }}" class="detail-action-btn detail-action-outline" data-translate-en="Back to My Bookings" data-translate-id="Kembali ke Pemesanan Saya">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to My Bookings
        </a>
    </div>
</div>

@endsection
