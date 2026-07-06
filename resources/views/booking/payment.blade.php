@extends('layouts.app')
@section('title', 'Payment')

@section('content')
<div class="payment-page">
    <h1 class="payment-title" data-translate-en="Complete Payment" data-translate-id="Selesaikan Pembayaran">Complete Payment</h1>

    @if(session('success'))
    <div class="payment-success-alert">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if($booking->payment_status === 'awaiting_approval')
    {{-- Waiting for approval state --}}
    <div class="payment-waiting">
        <div class="payment-waiting-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <h2 class="payment-waiting-title" data-translate-en="Awaiting Admin Confirmation" data-translate-id="Menunggu Konfirmasi Admin">Awaiting Admin Confirmation</h2>
        <p class="payment-waiting-desc" data-translate-en="Your proof of transfer has been uploaded. Admin will verify your payment shortly." data-translate-id="Bukti transfer Anda telah diunggah. Admin akan memverifikasi pembayaran Anda sebentar lagi.">Your proof of transfer has been uploaded. Admin will verify your payment shortly.</p>
        <div class="payment-waiting-details">
            <div class="payment-waiting-row">
                <span data-translate-en="Booking Code" data-translate-id="Kode Pemesanan">Booking Code</span>
                <strong>#{{ $booking->booking_code }}</strong>
            </div>
            <div class="payment-waiting-row">
                <span data-translate-en="Total Payment" data-translate-id="Total Pembayaran">Total Payment</span>
                <strong>RM {{ number_format($booking->total_amount, 2) }}</strong>
            </div>
            <div class="payment-waiting-row">
                <span data-translate-en="Status" data-translate-id="Status">Status</span>
                <span class="payment-status-badge bs-yellow" data-translate-en="Awaiting Verification" data-translate-id="Menunggu Verifikasi">Awaiting Verification</span>
            </div>
        </div>
        <p class="payment-waiting-hint" data-translate-en="Once admin approves, tickets will be issued automatically. Please check your booking page regularly." data-translate-id="Setelah admin menyetujui, tiket akan diterbitkan secara otomatis. Silakan periksa halaman pemesanan Anda secara berkala.">Once admin approves, tickets will be issued automatically. Please check your booking page regularly.</p>
        <div class="payment-actions">
            <a href="{{ route('booking.detail', $booking->booking_code) }}" class="payment-btn payment-btn-primary" data-translate-en="View Booking Details" data-translate-id="Lihat Detail Pemesanan">View Booking Details</a>
            <a href="{{ route('booking.history') }}" class="payment-btn payment-btn-outline" data-translate-en="My Bookings" data-translate-id="Pemesanan Saya">My Bookings</a>
        </div>
    </div>
    @else
    @if($booking->payment_status === 'rejected')
    <div class="payment-rejected-banner">
        <div class="payment-rejected-banner-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        </div>
        <div class="payment-rejected-banner-body">
            <h3 data-translate-en="Payment Rejected" data-translate-id="Pembayaran Ditolak">Payment Rejected</h3>
            @if($booking->payment->rejection_reason)
            <p><strong data-translate-en="Reason:" data-translate-id="Alasan:">Reason:</strong> {{ $booking->payment->rejection_reason }}</p>
            @endif
            <p class="payment-rejected-banner-hint" data-translate-en="Please re-upload a valid proof of transfer or contact support if you need assistance." data-translate-id="Silakan unggah ulang bukti transfer yang valid atau hubungi dukungan jika Anda memerlukan bantuan.">Please re-upload a valid proof of transfer or contact support if you need assistance.</p>
            <div style="display:flex;gap:8px;margin-top:12px;flex-wrap:wrap;">
                <a href="https://wa.me/6285212345678?text=Help%3A%20Booking%20%23{{ $booking->booking_code }}%20-%20Payment%20Rejected" target="_blank" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:#25D366;color:#fff;border-radius:8px;font-size:0.85rem;font-weight:600;text-decoration:none;" data-translate-en="Contact Support" data-translate-id="Hubungi Dukungan">
                    <svg viewBox="0 0 24 24" fill="currentColor" stroke="none" style="width:16px;height:16px;"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Contact Support
                </a>
            </div>
        </div>
    </div>
    @endif

    {{-- ToyibPay Payment Link --}}
    @if($booking->payment && $booking->payment->payment_method === 'toyibpay' && $booking->payment_status === 'pending')
        @php $toyibPayUrl = $booking->payment->payment_meta['payment_url'] ?? null; @endphp
        @if($toyibPayUrl)
        <div class="payment-toyibpay-cta" id="toyibPayCta" style="background:linear-gradient(135deg,#1a56db,#1e40af);color:#fff;border-radius:16px;padding:24px;display:flex;align-items:flex-start;gap:16px;margin-bottom:20px;">
            <div class="payment-toyibpay-cta-icon" style="width:48px;height:48px;background:rgba(255,255,255,0.2);border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:24px;height:24px;"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            </div>
            <div style="flex:1;">
                <h3 style="margin:0 0 4px;font-size:1.1rem;" data-translate-en="Pay via ToyibPay" data-translate-id="Bayar via ToyibPay">Pay via ToyibPay</h3>
                <p style="margin:0 0 8px;opacity:0.9;font-size:0.9rem;" data-translate-en="You will be redirected to ToyibPay secure payment page." data-translate-id="Anda akan diarahkan ke halaman pembayaran aman ToyibPay.">You will be redirected to ToyibPay secure payment page.</p>
                <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
                    <a href="{{ $toyibPayUrl }}" class="payment-btn" id="toyibPayBtn" style="display:inline-flex;background:#fff;color:#1e40af;font-weight:700;" onclick="showLoading()" data-translate-en="Proceed to Payment" data-translate-id="Lanjutkan ke Pembayaran">Proceed to Payment</a>
                    <button type="button" class="payment-btn" id="checkStatusBtn" style="display:inline-flex;background:rgba(255,255,255,0.2);color:#fff;border:1px solid rgba(255,255,255,0.4);" onclick="checkStatus()" data-translate-en="Check Payment Status" data-translate-id="Cek Status Pembayaran">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                        Check Payment Status
                    </button>
                </div>
                <div id="checkStatusResult" style="margin-top:8px;font-size:0.85rem;"></div>
            </div>
        </div>
        @endif
    @endif

    {{-- Loading Overlay --}}
    <div id="loadingOverlay" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.7);backdrop-filter:blur(4px);justify-content:center;align-items:center;flex-direction:column;gap:16px;">
        <div style="width:48px;height:48px;border:4px solid rgba(255,255,255,0.3);border-top-color:#fff;border-radius:50%;animation:spin 0.8s linear infinite;"></div>
        <p style="color:#fff;font-size:1rem;font-weight:600;" data-translate-en="Redirecting to ToyibPay..." data-translate-id="Mengarahkan ke ToyibPay...">Redirecting to ToyibPay...</p>
    </div>
    <style>
        @keyframes spin { to { transform: rotate(360deg); } }
        #toyibPayBtn:disabled, #checkStatusBtn:disabled { opacity:0.6; pointer-events:none; }
    </style>

    <div class="payment-expiry" id="paymentExpiry">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        <span data-translate-en="Booking expires in" data-translate-id="Pemesanan kedaluwarsa dalam">Booking expires in <strong id="expiryDisplay">{{ $booking->expires_at->diffForHumans(null, true) }}</strong>.</span>
    </div>

    {{-- Server-time countdown data --}}
    <input type="hidden" id="expiresAt" value="{{ $booking->expires_at->timestamp }}">
    <input type="hidden" id="serverNow" value="{{ now()->timestamp }}">

    <div class="payment-grid">
        <div class="payment-card">
            <div class="payment-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 21h20M6 18l2-6h8l2 6M9 12V7M15 12V7M12 7V3"/><path d="M5 7h14l-2 5H7L5 7Z"/></svg>
                Booking #{{ $booking->booking_code }}
            </div>
            <div class="payment-card-body">
                <div class="payment-info-row">
                    <span class="payment-info-label" data-translate-en="Vessel" data-translate-id="Kapal">Vessel</span>
                    <span class="payment-info-value">{{ $booking->schedule->vessel->name }}</span>
                </div>
                <div class="payment-info-row">
                    <span class="payment-info-label" data-translate-en="Route" data-translate-id="Rute">Route</span>
                    <span class="payment-info-value">{{ $booking->schedule->route->origin_port }} → {{ $booking->schedule->route->destination_port }}</span>
                </div>
                <div class="payment-info-row">
                    <span class="payment-info-label" data-translate-en="Departure" data-translate-id="Keberangkatan">Departure</span>
                    <span class="payment-info-value">{{ $booking->schedule->departure_time->format('d M Y, H:i') }}</span>
                </div>
                <div class="payment-info-row">
                    <span class="payment-info-label" data-translate-en="Passengers" data-translate-id="Penumpang">Passengers</span>
                    <span class="payment-info-value">{{ $booking->total_passengers }}</span>
                </div>
                @if($booking->discount_amount > 0)
                <div class="payment-info-row">
                    <span class="payment-info-label" data-translate-en="Discount" data-translate-id="Diskon">Discount</span>
                    <span class="payment-info-value" style="color:#059669">-RM {{ number_format($booking->discount_amount, 2) }}</span>
                </div>
                @endif
                <div class="payment-divider"></div>
                <div class="payment-info-row">
                    <span class="payment-info-label" data-translate-en="Total" data-translate-id="Total">Total</span>
                    <span class="payment-info-value payment-total">RM {{ number_format($booking->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="payment-card">
            <div class="payment-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                <span data-translate-en="Transfer Manual" data-translate-id="Transfer Manual">Transfer Manual</span>
            </div>
            <div class="payment-card-body">
                {{-- QR Code Display with Timer --}}
                @php
                    $qrValue = App\Models\Setting::getValue('payment_qr_image');
                @endphp
                @if($qrValue)
                <div class="payment-qr-section" id="qrSection">
                    <h3 class="payment-qr-title" data-translate-en="Scan QR Code to Pay" data-translate-id="Scan Kode QR untuk Membayar">Scan QR Code to Pay</h3>
                    <div class="payment-qr-timer" id="bookingTimer">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span data-translate-en="Booking expires in" data-translate-id="Pemesanan kedaluwarsa dalam">Booking expires in <strong id="timerDisplay">--:--</strong></span>
                    </div>
                    <div class="payment-qr-image-wrap" id="qrImageWrap" onclick="openQrModal(this)" style="cursor:pointer;">
                        <img src="{{ asset('storage/' . $qrValue) }}" alt="Payment QR Code" class="payment-qr-image">
                        <span class="payment-qr-zoom-hint" data-translate-en="Click to enlarge" data-translate-id="Klik untuk memperbesar">Click to enlarge</span>
                    </div>
                    <p class="payment-qr-hint" id="qrHint" data-translate-en="Scan the QR above using your e-wallet or mobile banking app." data-translate-id="Scan QR di atas menggunakan e-wallet atau aplikasi mobile banking Anda.">Scan the QR above using your e-wallet or mobile banking app.</p>
                    <div class="payment-qr-expired" id="qrExpired" style="display:none;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:32px;height:32px;color:#ef4444;margin:0 auto 10px;"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        <h4 style="color:#ef4444;margin:0 0 4px;" data-translate-en="Booking Cancelled" data-translate-id="Pemesanan Dibatalkan">Booking Cancelled</h4>
                        <p style="color:#6b7280;font-size:0.85rem;" data-translate-en="Payment time has expired. Please make a new booking." data-translate-id="Waktu pembayaran telah habis. Silakan buat pemesanan baru.">Payment time has expired. Please make a new booking.</p>
                        <a href="{{ route('schedules') }}" class="payment-btn payment-btn-primary" style="display:inline-flex;margin-top:16px;" data-translate-en="Book Again" data-translate-id="Pesan Lagi">Book Again</a>
                    </div>
                </div>
                @else
                <div class="payment-qr-section">
                    <h3 class="payment-qr-title" data-translate-en="Transfer to Account" data-translate-id="Transfer ke Rekening">Transfer to Account</h3>
                    <div class="payment-bank-info">
                        <p class="payment-bank-name" data-translate-en="Bank Muamalat" data-translate-id="Bank Muamalat">Bank Muamalat</p>
                        <p class="payment-bank-account">5706016718</p>
                        <p class="payment-bank-holder">a.n Fajar Pratama</p>
                    </div>
                </div>
                @endif

                <div class="payment-divider"></div>

                <form action="{{ route('booking.process-payment', $booking->booking_code) }}" method="POST" enctype="multipart/form-data" class="payment-form">
                    @csrf
                    <input type="hidden" name="payment_method" value="manual_transfer">

                    <div class="payment-upload-section">
                        <h3 class="payment-upload-title" data-translate-en="Upload Proof of Transfer" data-translate-id="Unggah Bukti Transfer">Upload Proof of Transfer</h3>
                        <p class="payment-upload-desc" data-translate-en="Upload a screenshot or photo of your transfer (max 5MB, format: JPG/PNG/WebP)" data-translate-id="Unggah tangkapan layar atau foto transfer Anda (maks 5MB, format: JPG/PNG/WebP)">Upload a screenshot or photo of your transfer (max 5MB, format: JPG/PNG/WebP)</p>
                        <div class="payment-upload-dropzone" id="dropzone">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="payment-upload-icon"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            <p class="payment-upload-text" data-translate-en="Click or drag &amp; drop your proof of transfer here" data-translate-id="Klik atau seret &amp; jatuhkan bukti transfer Anda di sini">Click or drag &amp; drop your proof of transfer here</p>
                            <span class="payment-upload-filename" id="filename"></span>
                            <input type="file" name="proof_of_transfer" id="proof_of_transfer" accept="image/*" required class="payment-upload-input">
                        </div>
                        @error('proof_of_transfer')
                            <span class="payment-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="payment-submit" id="submitBtn" data-translate-en="Upload Proof of Transfer &amp; Confirm" data-translate-id="Unggah Bukti Transfer &amp; Konfirmasi">
                        Upload Proof of Transfer &amp; Confirm
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="payment-card" style="margin-top:20px;">
        <div class="payment-card-header">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            <span data-translate-en="Passenger List" data-translate-id="Daftar Penumpang">Passenger List ({{ $booking->passengers->count() }})</span>
        </div>
        <div class="payment-card-body p-0">
            <div class="payment-table-wrap">
                <table class="payment-table">
                    <thead>
                        <tr>
                            <th data-translate-en="Name" data-translate-id="Nama">Name</th>
                            <th data-translate-en="Class" data-translate-id="Kelas">Class</th>
                            <th data-translate-en="Type" data-translate-id="Tipe">Type</th>
                            <th data-translate-en="Passport" data-translate-id="Paspor">Passport</th>
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
    @endif
</div>

{{-- QR Image Modal --}}
<div class="qr-modal-overlay" id="qrModalOverlay" onclick="closeQrModal()">
    <div class="qr-modal-content" onclick="event.stopPropagation()">
        <button class="qr-modal-close" onclick="closeQrModal()">&times;</button>
        <img class="qr-modal-image" id="qrModalImage" src="" alt="QR Code Full Size">
    </div>
</div>

<style>
.payment-success-alert {
    display:flex;
    align-items:center;
    gap:12px;
    padding:14px 18px;
    background:#ecfdf5;
    border:1px solid #a7f3d0;
    border-radius:10px;
    color:#065f46;
    font-size:0.9rem;
    margin-bottom:20px;
}
.payment-success-alert svg { width:22px;height:22px;flex-shrink:0;color:#059669; }
.payment-waiting { text-align:center;padding:40px 20px; }
.payment-waiting-icon svg { width:64px;height:64px;color:#f59e0b;margin:0 auto 16px; }
.payment-waiting-title { font-size:1.4rem;font-weight:700;margin-bottom:8px;color:#1f2937; }
.payment-waiting-desc { color:#6b7280;margin-bottom:24px; }
.payment-waiting-details { max-width:400px;margin:0 auto 20px;background:#f9fafb;border-radius:12px;padding:20px; }
.payment-waiting-row { display:flex;justify-content:space-between;padding:8px 0;font-size:0.9rem; }
.payment-waiting-row span { color:#6b7280; }
.payment-waiting-hint { font-size:0.85rem;color:#9ca3af;margin-bottom:24px; }
.payment-status-badge { font-size:0.8rem;padding:4px 12px;border-radius:20px;font-weight:600; }
.payment-actions { display:flex;gap:12px;justify-content:center;flex-wrap:wrap; }
.payment-btn { display:inline-flex;align-items:center;gap:8px;padding:12px 24px;border-radius:10px;font-weight:600;font-size:0.9rem;text-decoration:none;transition:all 0.2s; }
.payment-btn-primary { background:#2563eb;color:#fff; }
.payment-btn-primary:hover { background:#1d4ed8; }
.payment-btn-outline { border:2px solid #d1d5db;color:#374151; }
.payment-btn-outline:hover { border-color:#9ca3af; }
.payment-rejected { text-align:center;padding:40px 20px; }
.payment-rejected-icon svg { width:64px;height:64px;color:#ef4444;margin:0 auto 16px; }
.payment-rejected-title { font-size:1.4rem;font-weight:700;margin-bottom:8px;color:#dc2626; }
.payment-rejected-reason { background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:12px 16px;color:#dc2626;display:inline-block;margin-bottom:12px;font-size:0.9rem; }
.payment-rejected-desc { color:#6b7280;margin-bottom:20px; }
.payment-qr-section { text-align:center;padding:10px 0; }
.payment-qr-title { font-size:0.95rem;font-weight:600;color:#1f2937;margin-bottom:14px; }
.payment-qr-image-wrap { background:#fff;border:2px solid #e5e7eb;border-radius:12px;padding:12px;display:inline-block; }
.payment-qr-image { width:180px;height:180px;object-fit:contain;display:block; }
.payment-qr-hint { font-size:0.8rem;color:#9ca3af;margin-top:10px; }
.payment-bank-info { text-align:center;padding:10px 0; }
.payment-bank-name { font-weight:600;color:#1f2937;font-size:0.95rem;margin-bottom:4px; }
.payment-bank-account { font-size:1.2rem;font-weight:700;color:#2563eb;letter-spacing:2px;margin-bottom:4px; }
.payment-bank-holder { font-size:0.85rem;color:#6b7280; }
.payment-upload-section { padding:10px 0; }
.payment-upload-title { font-size:0.95rem;font-weight:600;color:#1f2937;margin-bottom:6px; }
.payment-upload-desc { font-size:0.8rem;color:#6b7280;margin-bottom:14px; }
.payment-upload-dropzone { position:relative;border:2px dashed #d1d5db;border-radius:12px;padding:30px 20px;text-align:center;cursor:pointer;transition:all 0.2s;background:#fafafa; }
.payment-upload-dropzone:hover { border-color:#2563eb;background:#eff6ff; }
.payment-upload-dropzone.dragover { border-color:#2563eb;background:#eff6ff; }
.payment-upload-icon { width:40px;height:40px;color:#9ca3af;margin:0 auto 10px; }
.payment-upload-text { font-size:0.85rem;color:#6b7280; }
.payment-upload-filename { display:block;font-size:0.85rem;color:#2563eb;font-weight:600;margin-top:8px; }
.payment-upload-input { position:absolute;top:0;left:0;width:100%;height:100%;opacity:0;cursor:pointer; }
.payment-error { display:block;color:#dc2626;font-size:0.8rem;margin-top:6px; }
.payment-rejected-banner { display:flex;align-items:flex-start;gap:14px;padding:16px 20px;background:#fef2f2;border:1px solid #fecaca;border-radius:12px;margin-bottom:20px; }
.payment-rejected-banner-icon svg { width:28px;height:28px;color:#ef4444;flex-shrink:0;margin-top:2px; }
.payment-rejected-banner-body h3 { font-weight:700;color:#dc2626;font-size:0.95rem;margin-bottom:4px; }
.payment-rejected-banner-body p { color:#991b1b;font-size:0.85rem; }
.payment-rejected-banner-hint { margin-top:6px !important;color:#6b7280 !important;font-size:0.8rem !important; }
.payment-qr-timer { display:flex;align-items:center;justify-content:center;gap:8px;font-size:0.9rem;color:#6b7280;margin-bottom:14px;padding:8px 16px;background:#fefce8;border:1px solid #fde68a;border-radius:8px; }
.payment-qr-timer svg { color:#f59e0b;flex-shrink:0; }
.payment-qr-timer strong { color:#d97706;font-family:monospace;font-size:1.1rem; }
.payment-qr-expired { text-align:center;padding:20px; }

/* QR Image Modal */
.qr-modal-overlay {
    display:none; position:fixed; z-index:9999; inset:0;
    background:rgba(0,0,0,0.75); backdrop-filter:blur(4px);
    align-items:center; justify-content:center;
    padding:24px;
}
.qr-modal-overlay.open { display:flex; }
.qr-modal-content {
    position:relative; max-width:90vw; max-height:90vh;
    background:#fff; border-radius:20px; padding:24px;
    box-shadow:0 24px 80px rgba(0,0,0,0.35);
    animation:qrModalFadeIn 0.25s ease;
}
@keyframes qrModalFadeIn {
    from { opacity:0; transform:scale(0.9); }
    to { opacity:1; transform:scale(1); }
}
.qr-modal-close {
    position:absolute; top:-14px; right:-14px;
    width:36px; height:36px; border-radius:50%;
    border:none; background:#fff; color:#374151;
    font-size:22px; font-weight:700; cursor:pointer;
    box-shadow:0 2px 12px rgba(0,0,0,0.15);
    display:flex; align-items:center; justify-content:center;
    transition:all 0.2s; line-height:1;
}
.qr-modal-close:hover { background:#ef4444; color:#fff; transform:scale(1.1); }
.qr-modal-image {
    max-width:70vw; max-height:75vh; object-fit:contain;
    display:block; border-radius:12px;
}
.payment-qr-zoom-hint {
    display:block; font-size:0.72rem; color:#3b82f6; font-weight:600;
    margin-top:6px; text-align:center;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Server-time based countdown (timezone-safe)
    const expiresAt = {{ $booking->expires_at->timestamp }} * 1000;
    const serverNow = {{ now()->timestamp }} * 1000;
    const clientOffset = new Date().getTime() - serverNow;

    const timerDisplay = document.getElementById('timerDisplay');
    const expiryDisplay = document.getElementById('expiryDisplay');
    const qrImageWrap = document.getElementById('qrImageWrap');
    const qrHint = document.getElementById('qrHint');
    const qrExpired = document.getElementById('qrExpired');
    const bookingTimer = document.getElementById('bookingTimer');
    const paymentExpiry = document.getElementById('paymentExpiry');

    function updateCountdown() {
        // Use server-adjusted time
        const clientNow = new Date().getTime();
        const adjustedNow = clientNow - clientOffset;
        const diff = expiresAt - adjustedNow;

        if (diff <= 0) {
            if (!window.bookingCancelled) {
                window.bookingCancelled = true;
                cancelBookingOnServer();
            }
            if (timerDisplay) timerDisplay.textContent = '00:00';
            if (expiryDisplay) expiryDisplay.textContent = 'expired';
            if (qrImageWrap) qrImageWrap.style.display = 'none';
            if (qrHint) qrHint.style.display = 'none';
            if (bookingTimer) bookingTimer.style.display = 'none';
            if (paymentExpiry) paymentExpiry.style.display = 'none';
            if (qrExpired) qrExpired.style.display = 'block';

            // Hide ToyibPay CTA if present
            const toyibPayCta = document.getElementById('toyibPayCta');
            if (toyibPayCta) toyibPayCta.style.display = 'none';

            clearInterval(timerInterval);
            return;
        }

        const totalSeconds = Math.floor(diff / 1000);
        const mins = Math.floor(totalSeconds / 60);
        const secs = totalSeconds % 60;

        const timeStr = String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
        if (timerDisplay) timerDisplay.textContent = timeStr;

        if (expiryDisplay) {
            if (mins >= 60) {
                const hours = Math.floor(mins / 60);
                const remMins = mins % 60;
                expiryDisplay.textContent = hours + 'h ' + remMins + 'm';
            } else {
                expiryDisplay.textContent = mins + 'm ' + secs + 's';
            }
        }
    }

    updateCountdown();
    const timerInterval = setInterval(updateCountdown, 1000);

    // Dropzone upload
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('proof_of_transfer');
    const filename = document.getElementById('filename');

    if (dropzone && fileInput) {
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                filename.textContent = '\u2713 ' + this.files[0].name;
                dropzone.style.borderColor = '#059669';
                dropzone.style.background = '#ecfdf5';
            } else {
                filename.textContent = '';
                dropzone.style.borderColor = '#d1d5db';
                dropzone.style.background = '#fafafa';
            }
        });

        dropzone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });

        dropzone.addEventListener('dragleave', function() {
            this.classList.remove('dragover');
        });

        dropzone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                fileInput.files = e.dataTransfer.files;
                filename.textContent = '\u2713 ' + e.dataTransfer.files[0].name;
                dropzone.style.borderColor = '#059669';
                dropzone.style.background = '#ecfdf5';
            }
        });
    }

    // QR Modal: close on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeQrModal();
    });
});

function showLoading() {
    document.getElementById('loadingOverlay').style.display = 'flex';
    document.getElementById('toyibPayBtn').style.pointerEvents = 'none';
    document.getElementById('toyibPayBtn').style.opacity = '0.6';
    // If the redirect doesn't happen within 10s, show a fallback
    setTimeout(function() {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay.style.display === 'flex') {
            overlay.querySelector('p').textContent = 'Taking longer than expected. Click the button again if not redirected.';
            document.getElementById('toyibPayBtn').style.pointerEvents = 'auto';
            document.getElementById('toyibPayBtn').style.opacity = '1';
        }
    }, 10000);
}

function checkStatus() {
    const btn = document.getElementById('checkStatusBtn');
    const result = document.getElementById('checkStatusResult');
    if (!btn || !result) return;

    btn.disabled = true;
    btn.textContent = 'Checking...';
    result.innerHTML = '';
    result.style.color = '#fff';

    fetch('{{ route("booking.check-status", $booking->booking_code) }}')
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.done && data.redirect) {
                result.innerHTML = '<strong>Payment confirmed!</strong> Redirecting...';
                window.location.href = data.redirect;
                return;
            }

            if (data.done && data.payment_status === 'expired') {
                result.innerHTML = '<strong>Booking expired.</strong> Please make a new booking.';
                location.reload();
                return;
            }

            if (data.payment_status === 'paid') {
                result.innerHTML = '<strong>Payment confirmed!</strong> Redirecting...';
                window.location.href = '{{ route("booking.success", $booking->booking_code) }}';
                return;
            }

            result.innerHTML = 'Status: <strong>Waiting for payment</strong> - ' +
                'Please complete the payment on ToyibPay page.';
            btn.disabled = false;
            btn.innerHTML =
                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg> ' +
                'Check Payment Status';
        })
        .catch(function(err) {
            result.innerHTML = 'Error checking status. Please try again.';
            btn.disabled = false;
            btn.textContent = 'Check Payment Status';
        });
}

function openQrModal(el) {
    var img = el.querySelector('img');
    if (!img) return;
    document.getElementById('qrModalImage').src = img.src;
    document.getElementById('qrModalOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeQrModal() {
    document.getElementById('qrModalOverlay').classList.remove('open');
    document.body.style.overflow = '';
}

function cancelBookingOnServer() {
    var http = new XMLHttpRequest();
    http.open('POST', '{{ route("booking.cancel-expired", $booking->booking_code) }}', true);
    http.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    http.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
    http.send('_token={{ csrf_token() }}');
}
</script>
@endsection
