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
    {{-- Rejected banner --}}
    @if($booking->payment_status === 'rejected')
    <div class="payment-waiting" style="border-left:4px solid #ef4444;">
        <h2 style="color:#dc2626;">Payment Rejected</h2>
        @if($booking->payment && $booking->payment->rejection_reason)
        <p style="color:#6b7280;"><strong>Reason:</strong> {{ $booking->payment->rejection_reason }}</p>
        @endif
        <p style="color:#9ca3af;" data-translate-en="Please re-upload a valid proof of transfer." data-translate-id="Sila muat naik bukti pembayaran yang sah.">Please re-upload a valid proof of transfer.</p>
    </div>
    @endif

    <div class="payment-expiry" id="paymentExpiry">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        <span data-translate-en="Booking expires in" data-translate-id="Pemesanan tamat dalam">Booking expires in <strong id="expiryDisplay">{{ $booking->expires_at->diffForHumans(null, true) }}</strong>.</span>
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
                    <span class="payment-info-label" data-translate-en="Route" data-translate-id="Laluan">Route</span>
                    <span class="payment-info-value">{{ $booking->schedule->route->origin_port }} → {{ $booking->schedule->route->destination_port }}</span>
                </div>
                <div class="payment-info-row">
                    <span class="payment-info-label" data-translate-en="Departure" data-translate-id="Berlepas">Departure</span>
                    <span class="payment-info-value">{{ $booking->schedule->departure_time->format('d M Y, H:i') }}</span>
                </div>
                <div class="payment-info-row">
                    <span class="payment-info-label" data-translate-en="Passengers" data-translate-id="Penumpang">Passengers</span>
                    <span class="payment-info-value">{{ $booking->total_passengers }}</span>
                </div>
                @if(($booking->discount_amount ?? 0) > 0)
                <div class="payment-info-row">
                    <span class="payment-info-label" data-translate-en="Discount" data-translate-id="Diskaun">Discount</span>
                    <span class="payment-info-value" style="color:#059669">-MYR {{ number_format($booking->discount_amount, 2) }}</span>
                </div>
                @endif
                <div class="payment-divider"></div>
                <div class="payment-info-row">
                    <span class="payment-info-label" data-translate-en="Total" data-translate-id="Jumlah">Total</span>
                    <span class="payment-info-value payment-total">MYR {{ number_format($booking->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="payment-card">
            <div class="payment-card-header">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                Manual Transfer
            </div>
            <div class="payment-card-body">
                {{-- QR Code Display with Timer --}}
                @php
                    $qrValue = App\Models\Setting::getValue('payment_qr_image');
                @endphp
                @if($qrValue)
                <div class="payment-qr-section" id="qrSection">
                    <h3 class="payment-qr-title" data-translate-en="Scan QR Code to Pay" data-translate-id="Imbas Kod QR untuk Membayar">Scan QR Code to Pay</h3>
                    <div class="payment-qr-timer" id="bookingTimer">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span data-translate-en="Booking expires in" data-translate-id="Pemesanan tamat dalam">Booking expires in <strong id="timerDisplay">--:--</strong></span>
                    </div>
                    <div class="payment-qr-image-wrap" id="qrImageWrap" onclick="openQrModal(this)" style="cursor:pointer;">
                        <img src="{{ asset('storage/' . $qrValue) }}" alt="Payment QR Code" class="payment-qr-image">
                        <span class="payment-qr-zoom-hint" data-translate-en="Click to enlarge" data-translate-id="Klik untuk besarkan">Click to enlarge</span>
                    </div>
                    <p class="payment-qr-hint" id="qrHint" data-translate-en="Scan the QR above using your e-wallet or mobile banking app." data-translate-id="Imbas QR di atas menggunakan e-wallet atau aplikasi perbankan mudah alih anda.">Scan the QR above using your e-wallet or mobile banking app.</p>
                    <div class="payment-qr-expired" id="qrExpired" style="display:none;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:32px;height:32px;color:#ef4444;margin:0 auto 10px;"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        <h4 style="color:#ef4444;margin:0 0 4px;" data-translate-en="Booking Cancelled" data-translate-id="Pemesanan Dibatalkan">Booking Cancelled</h4>
                        <p style="color:#6b7280;font-size:0.85rem;" data-translate-en="Payment time has expired. Please make a new booking." data-translate-id="Masa pembayaran telah tamat. Sila buat tempahan baru.">Payment time has expired. Please make a new booking.</p>
                        <a href="{{ route('schedules') }}" class="btn btn-primary" style="display:inline-flex;margin-top:16px;" data-translate-en="Book Again" data-translate-id="Tempah Semula">Book Again</a>
                    </div>
                </div>
                @else
                <div class="payment-qr-section">
                    <h3 class="payment-qr-title" data-translate-en="Transfer to Account" data-translate-id="Pindahan ke Akaun">Transfer to Account</h3>
                    <div class="payment-bank-info">
                        <p class="payment-bank-name">Bank Muamalat</p>
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
                        <h3 class="payment-upload-title" data-translate-en="Upload Proof of Transfer" data-translate-id="Muat Naik Bukti Pemindahan">Upload Proof of Transfer</h3>
                        <p class="payment-upload-desc" data-translate-en="Upload a screenshot or photo of your transfer (max 5MB, format: JPG/PNG/WebP)" data-translate-id="Muat naik tangkapan skrin atau foto pemindahan anda (maks 5MB, format: JPG/PNG/WebP)">Upload a screenshot or photo of your transfer (max 5MB, format: JPG/PNG/WebP)</p>
                        <div class="payment-upload-dropzone" id="dropzone">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="payment-upload-icon"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            <p class="payment-upload-text" data-translate-en="Click or drag &amp; drop your proof of transfer here" data-translate-id="Klik atau seret &amp; lepaskan bukti pemindahan anda di sini">Click or drag &amp; drop your proof of transfer here</p>
                            <span class="payment-upload-filename" id="filename"></span>
                            <input type="file" name="proof_of_transfer" id="proof_of_transfer" accept="image/*" required class="payment-upload-input">
                        </div>
                        @error('proof_of_transfer')
                            <span class="payment-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="payment-submit" id="submitBtn" data-translate-en="Upload Proof of Transfer &amp; Confirm" data-translate-id="Muat Naik Bukti Pemindahan &amp; Sahkan">
                        Upload Proof of Transfer & Confirm
                    </button>
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
                            <th data-translate-en="Name" data-translate-id="Nama">Name</th>
                            <th data-translate-en="Class" data-translate-id="Kelas">Class</th>
                            <th data-translate-en="Type" data-translate-id="Jenis">Type</th>
                            <th data-translate-en="Passport" data-translate-id="Pasport">Passport</th>
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

{{-- QR Modal --}}
<div id="qrModal" class="payment-qr-modal" onclick="closeQrModal()">
    <span class="payment-qr-modal-close">&times;</span>
    @php $qrValue = App\Models\Setting::getValue('payment_qr_image'); @endphp
    @if($qrValue)
    <img src="{{ asset('storage/' . $qrValue) }}" alt="QR Code">
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Server-time based countdown (timezone-safe)
    const expiresAt = {{ $booking->expires_at->timestamp }} * 1000;
    const serverNow = {{ now()->timestamp }} * 1000;
    const clientOffset = new Date().getTime() - serverNow;

    const timerDisplay = document.getElementById('timerDisplay');
    const expiryDisplay = document.getElementById('expiryDisplay');
    const qrImageWrap = document.getElementById('qrImageWrap');
    const bookingTimer = document.getElementById('bookingTimer');
    const qrExpired = document.getElementById('qrExpired');
    const qrHint = document.getElementById('qrHint');
    const paymentExpiry = document.getElementById('paymentExpiry');

    function updateCountdown() {
        const clientNow = new Date().getTime();
        const adjustedNow = clientNow - clientOffset;
        const diff = expiresAt - adjustedNow;

        if (diff <= 0) {
            if (!window.bookingCancelled) {
                window.bookingCancelled = true;
                cancelBookingOnServer();
            }
            if (qrImageWrap) qrImageWrap.style.display = 'none';
            if (bookingTimer) bookingTimer.style.display = 'none';
            if (paymentExpiry) paymentExpiry.style.display = 'none';
            if (qrExpired) qrExpired.style.display = 'block';
            clearInterval(timerInterval);
            return;
        }

        const mins = Math.floor(diff / 60000);
        const secs = Math.floor((diff % 60000) / 1000);
        const timeStr = String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
        if (timerDisplay) timerDisplay.textContent = timeStr;

        if (expiryDisplay) {
            if (mins >= 60) {
                const hours = Math.floor(mins / 60);
                expiryDisplay.textContent = hours + 'h ' + (mins % 60) + 'm';
            } else {
                expiryDisplay.textContent = timeStr + ' min';
            }
        }
    }

    updateCountdown();
    const timerInterval = setInterval(updateCountdown, 1000);

    // Drag & drop upload
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
                dropzone.style.background = '';
            }
        });

        dropzone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });

        dropzone.addEventListener('dragleave', function(e) {
            e.preventDefault();
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
});

function openQrModal(el) {
    var img = el.querySelector('img');
    if (!img) return;
    document.getElementById('qrModal').style.display = 'flex';
}

function closeQrModal() {
    document.getElementById('qrModal').style.display = 'none';
}

function cancelBookingOnServer() {
    fetch('{{ route("booking.cancel-expired", $booking->booking_code) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    });
}
</script>
@endsection
