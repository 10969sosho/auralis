@extends('layouts.app')
@section('title', 'Deportation Payment')

@section('content')
<div class="payment-page" style="max-width:680px;margin:0 auto;">
    <h1 style="font-size:24px;font-weight:700;margin-bottom:6px;">Deportation Payment</h1>
    <p style="color:#64748b;margin-bottom:20px;">Booking Code: <strong>{{ $booking->booking_code }}</strong></p>

    {{-- Booking Summary --}}
    <div style="background:#fff;border-radius:16px;padding:20px;box-shadow:0 1px 2px rgba(0,0,0,0.05);border:1px solid #e5e7eb;margin-bottom:20px;">
        <h2 style="font-size:15px;font-weight:700;margin-bottom:14px;">Booking Summary</h2>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:14px;">
            <div style="color:#64748b;">Route</div>
            <div style="font-weight:600;">{{ $booking->route_display }}</div>
            <div style="color:#64748b;">Vessel</div>
            <div style="font-weight:600;">{{ $booking->vessel_display }}</div>
            <div style="color:#64748b;">Passengers</div>
            <div style="font-weight:600;">{{ $booking->total_passengers }} pax</div>
            <div style="color:#64748b;">Shelter Point</div>
            <div style="font-weight:600;">{{ $booking->user->shelter_point_name ?? '—' }} (+RM{{ number_format($booking->shelter_fee, 2) }})</div>
            <div style="border-top:1px solid #e5e7eb;padding-top:8px;font-weight:700;color:#1e293b;">Total</div>
            <div style="border-top:1px solid #e5e7eb;padding-top:8px;font-weight:700;color:#2563EB;font-size:16px;">RM{{ number_format($booking->total_amount, 2) }}</div>
        </div>

        <p style="margin-top:12px;font-size:13px;color:#EA580C;font-weight:600;">
            Please complete payment before: {{ $booking->expires_at->format('d M Y, H:i') }}
        </p>
    </div>

    @php $payment = $booking->payment; @endphp

    {{-- Payment Status --}}
    @if($payment && $payment->payment_status === 'pending')
        <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:12px;padding:16px;margin-bottom:20px;text-align:center;">
            <p style="font-weight:700;color:#ea580c;">Payment Pending</p>
            <p style="font-size:13px;color:#9a3412;margin-top:4px;">
                @if($payment->payment_method === 'toyibpay')
                    Please complete payment via ToyibPay. This page will update automatically after successful payment.
                @else
                    Please upload your payment proof below.
                @endif
            </p>
        </div>

        {{-- Manual Transfer Form --}}
        <div style="background:#fff;border-radius:16px;padding:20px;box-shadow:0 1px 2px rgba(0,0,0,0.05);border:1px solid #e5e7eb;margin-bottom:20px;">
            <h3 style="font-weight:700;margin-bottom:12px;">Upload Transfer Proof</h3>
            <form action="{{ route('deportation.payment.process', $booking->booking_code) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="payment_method" value="manual_transfer">
                <div style="margin-bottom:12px;">
                    <label style="display:block;margin-bottom:6px;font-weight:600;font-size:13px;">Transfer Proof (JPG/PNG/WEBP, max 5MB) *</label>
                    <input type="file" name="proof_of_transfer" required accept="image/jpeg,image/png,image/webp" style="display:block;width:100%;padding:10px;border:1.5px dashed #cbd5e1;border-radius:8px;font-size:13px;">
                    @error('proof_of_transfer') <p style="color:#dc2626;font-size:12px;">{{ $message }}</p> @enderror
                </div>
                <button type="submit" style="background:#059669;color:#fff;border:none;padding:12px 24px;border-radius:8px;font-weight:600;font-size:14px;cursor:pointer;width:100%;">
                    Submit Payment Proof
                </button>
            </form>
        </div>
    @elseif($payment && $payment->payment_status === 'awaiting_approval')
        <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;padding:20px;text-align:center;">
            <svg viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2" style="width:48px;height:48px;margin:0 auto 12px;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <p style="font-weight:700;color:#1e40af;font-size:16px;">Awaiting Approval</p>
            <p style="font-size:13px;color:#3b82f6;margin-top:4px;">Your payment proof is being reviewed by admin. Please wait for confirmation.</p>
        </div>
    @elseif($payment && in_array($payment->payment_status, ['paid', 'approved']))
        <div style="background:#dcfce7;border:1px solid #bbf7d0;border-radius:12px;padding:20px;text-align:center;">
            <p style="font-weight:700;color:#16a34a;font-size:16px;">Payment Successful!</p>
            <a href="{{ route('deportation.success', $booking->booking_code) }}" style="display:inline-block;margin-top:12px;background:#2563EB;color:#fff;padding:10px 20px;border-radius:8px;text-decoration:none;font-weight:600;">View Tickets</a>
        </div>
    @endif

    {{-- Passengers List --}}
    <div style="background:#fff;border-radius:16px;padding:20px;box-shadow:0 1px 2px rgba(0,0,0,0.05);border:1px solid #e5e7eb;">
        <h3 style="font-weight:700;margin-bottom:12px;">Passenger List</h3>
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:#f8fafc;font-size:12px;text-transform:uppercase;color:#64748b;">
                        <th style="padding:10px;text-align:left;">Name</th>
                        <th style="padding:10px;text-align:left;">Gender</th>
                        <th style="padding:10px;text-align:left;">Nationality</th>
                        <th style="padding:10px;text-align:left;">Class</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($booking->passengers as $p)
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:10px;font-weight:600;">{{ $p->full_name }}</td>
                        <td style="padding:10px;text-transform:capitalize;">{{ $p->gender }}</td>
                        <td style="padding:10px;">{{ $p->nationality }}</td>
                        <td style="padding:10px;text-transform:uppercase;font-size:12px;">
                            <span style="display:inline-block;padding:2px 8px;border-radius:20px;font-weight:600;background:{{ $p->ticket_class === 'vip' ? '#eff6ff' : '#f0fdf4' }};color:{{ $p->ticket_class === 'vip' ? '#2563EB' : '#059669' }};">
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
