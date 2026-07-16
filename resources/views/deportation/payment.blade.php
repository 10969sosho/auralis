@extends('layouts.app')
@section('title', 'Deportation Payment')

@push('styles')
<style>
.deportation-payment-page svg {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
    display: inline-block;
    vertical-align: middle;
}
.deportation-payment-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    margin-bottom: 20px;
}
.deportation-payment-card h2,
.deportation-payment-card h3 {
    font-size: 15px;
    font-weight: 700;
    margin-bottom: 14px;
}
.deportation-payment-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    font-size: 14px;
}
.deportation-payment-grid .label {
    color: #64748b;
}
.deportation-payment-grid .value {
    font-weight: 600;
}
.deportation-payment-grid .total-label {
    border-top: 1px solid #e5e7eb;
    padding-top: 8px;
    font-weight: 700;
    color: #1e293b;
}
.deportation-payment-grid .total-value {
    border-top: 1px solid #e5e7eb;
    padding-top: 8px;
    font-weight: 700;
    color: #2563EB;
    font-size: 16px;
}
.deportation-expiry {
    margin-top: 12px;
    font-size: 13px;
    color: #EA580C;
    font-weight: 600;
}
.deportation-status-box {
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 20px;
    text-align: center;
}
.deportation-status-box.pending {
    background: #fff7ed;
    border: 1px solid #fed7aa;
}
.deportation-status-box.pending p:first-child {
    font-weight: 700;
    color: #ea580c;
}
.deportation-status-box.pending p:last-child {
    font-size: 13px;
    color: #9a3412;
    margin-top: 4px;
}
.deportation-status-box.waiting {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
}
.deportation-status-box.waiting p:first-child {
    font-weight: 700;
    color: #1e40af;
    font-size: 16px;
}
.deportation-status-box.waiting p:last-child {
    font-size: 13px;
    color: #3b82f6;
    margin-top: 4px;
}
.deportation-status-box.waiting svg {
    width: 48px;
    height: 48px;
    color: #2563eb;
    margin: 0 auto 12px;
    display: block;
}
.deportation-status-box.success {
    background: #dcfce7;
    border: 1px solid #bbf7d0;
}
.deportation-status-box.success p {
    font-weight: 700;
    color: #16a34a;
    font-size: 16px;
}
.deportation-btn {
    display: inline-block;
    margin-top: 12px;
    background: #2563EB;
    color: #fff;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: background 0.2s;
}
.deportation-btn:hover {
    background: #1d4ed8;
}
.deportation-btn.green {
    background: #059669;
}
.deportation-btn.green:hover {
    background: #047857;
}
.deportation-form-input {
    display: block;
    width: 100%;
    padding: 10px;
    border: 1.5px dashed #cbd5e1;
    border-radius: 8px;
    font-size: 13px;
}
.deportation-form-error {
    color: #dc2626;
    font-size: 12px;
}
.deportation-table {
    width: 100%;
    border-collapse: collapse;
}
.deportation-table th {
    background: #f8fafc;
    padding: 10px;
    text-align: left;
    font-size: 12px;
    text-transform: uppercase;
    color: #64748b;
}
.deportation-table td {
    padding: 10px;
    border-bottom: 1px solid #f1f5f9;
}
.deportation-table .passenger-name {
    font-weight: 600;
}
.deportation-table .class-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 20px;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 12px;
}
.deportation-table .class-badge.vip {
    background: #eff6ff;
    color: #2563EB;
}
.deportation-table .class-badge.regular {
    background: #f0fdf4;
    color: #059669;
}
.deportation-proof-preview {
    max-width: 320px;
    margin: 16px auto;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}
.deportation-proof-preview img {
    width: 100%;
    height: auto;
    display: block;
}
.deportation-proof-label {
    font-size: 0.8rem;
    color: #6b7280;
    margin: 12px 0 8px;
}
</style>
@endpush

@section('content')
<div class="deportation-payment-page" style="max-width:680px;margin:0 auto;">
    <h1 style="font-size:24px;font-weight:700;margin-bottom:6px;">Deportation Payment</h1>
    <p style="color:#64748b;margin-bottom:20px;">Booking Code: <strong>{{ $booking->booking_code }}</strong></p>

    {{-- Booking Summary --}}
    <div class="deportation-payment-card">
        <h2>Booking Summary</h2>
        <div class="deportation-payment-grid">
            <div class="label">Route</div>
            <div class="value">{{ $booking->route_display }}</div>
            <div class="label">Vessel</div>
            <div class="value">{{ $booking->vessel_display }}</div>
            <div class="label">Passengers</div>
            <div class="value">{{ $booking->total_passengers }} pax</div>
            <div class="label">Shelter Point</div>
            <div class="value">{{ $booking->user->shelter_point_name ?? '—' }} (+RM{{ number_format($booking->shelter_fee, 2) }})</div>
            <div class="total-label">Total</div>
            <div class="total-value">RM{{ number_format($booking->total_amount, 2) }}</div>
        </div>
        <p class="deportation-expiry">Please complete payment before: {{ $booking->expires_at->format('d M Y, H:i') }}</p>
    </div>

    @php $payment = $booking->payment; @endphp

    {{-- Payment Status --}}
    @if($payment && $payment->payment_status === 'pending')
        <div class="deportation-status-box pending">
            <p>Payment Pending</p>
            <p>Please upload your payment proof below.</p>
        </div>

        {{-- Manual Transfer Form --}}
        <div class="deportation-payment-card">
            <h3>Upload Transfer Proof</h3>
            <form action="{{ route('deportation.payment.process', $booking->booking_code) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="payment_method" value="manual_transfer">
                <div style="margin-bottom:12px;">
                    <label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;">Transfer Proof (JPG/PNG/WEBP, max 5MB) *</label>
                    <input type="file" name="proof_of_transfer" required accept="image/jpeg,image/png,image/webp" class="deportation-form-input">
                    @error('proof_of_transfer') <p class="deportation-form-error">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="deportation-btn green" style="width:100%;text-align:center;">
                    Submit Payment Proof
                </button>
            </form>
        </div>
    @elseif($payment && $payment->payment_status === 'awaiting_approval')
        <div class="deportation-status-box waiting">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <p>Awaiting Approval</p>
            <p>Your payment proof is being reviewed by admin. Please wait for confirmation.</p>
            @if($payment->proof_of_transfer)
            <p class="deportation-proof-label">Your uploaded proof of transfer:</p>
            <div class="deportation-proof-preview">
                <img src="{{ asset('storage/' . $payment->proof_of_transfer) }}" alt="Proof of Transfer">
            </div>
            @endif
        </div>
    @elseif($payment && in_array($payment->payment_status, ['paid', 'approved']))
        <div class="deportation-status-box success">
            <p>Payment Successful!</p>
            <a href="{{ route('deportation.success', $booking->booking_code) }}" class="deportation-btn">View Tickets</a>
        </div>
    @endif

    {{-- Passengers List --}}
    <div class="deportation-payment-card">
        <h3>Passenger List</h3>
        <div style="overflow-x:auto;">
            <table class="deportation-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Nationality</th>
                        <th>Class</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($booking->passengers as $p)
                    <tr>
                        <td class="passenger-name">{{ $p->full_name }}</td>
                        <td style="text-transform:capitalize;">{{ $p->gender }}</td>
                        <td>{{ $p->nationality }}</td>
                        <td>
                            <span class="class-badge {{ $p->ticket_class }}">
                                {{ $p->ticket_class }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
