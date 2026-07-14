@extends('layouts.app')
@section('title', 'Deportation Booking History')

@section('content')
<div class="deportation-page">
    <h1 style="font-size:24px;font-weight:700;color:#1e293b;margin-bottom:6px;">Deportation Booking History</h1>
    <a href="{{ route('deportation.dashboard') }}" style="color:#64748b;text-decoration:none;font-size:14px;display:inline-flex;align-items:center;gap:4px;margin-bottom:20px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><polyline points="15 18 9 12 15 6"/></svg>
        Back to Dashboard
    </a>

    @if($bookings->isEmpty())
        <div style="background:#fff;border-radius:16px;padding:60px 20px;text-align:center;box-shadow:0 1px 2px rgba(0,0,0,0.05);border:1px solid #e5e7eb;">
            <p style="color:#64748b;">No deportation booking history.</p>
        </div>
    @else
        <div style="display:grid;gap:16px;">
            @foreach($bookings as $booking)
            <div style="background:#fff;border-radius:16px;padding:20px;box-shadow:0 1px 2px rgba(0,0,0,0.05);border:1px solid #e5e7eb;">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px;">
                    <div>
                        <p style="font-weight:700;font-family:monospace;">{{ $booking->booking_code }}</p>
                        <p style="font-size:13px;color:#64748b;">
                            {{ $booking->route_display }}
                            &bull; {{ $booking->vessel_display }}
                            &bull; {{ $booking->total_passengers }} pax
                        </p>
                        <p style="font-size:13px;color:#64748b;">Total: <strong>RM{{ number_format($booking->total_amount, 2) }}</strong></p>
                    </div>
                    <div style="text-align:right;">
                        @php
                            $statusColor = match($booking->payment_status) {
                                'paid', 'approved' => 'green',
                                'pending' => 'orange',
                                'awaiting_approval' => 'blue',
                                default => 'gray'
                            };
                            $statusLabel = match($booking->payment_status) {
                                'paid', 'approved' => 'Paid',
                                'pending' => 'Pending',
                                'awaiting_approval' => 'Approval',
                                'failed' => 'Failed',
                                'expired' => 'Expired',
                                'cancelled' => 'Cancelled',
                                default => $booking->payment_status
                            };
                        @endphp
                        <span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;background:{{ $statusColor === 'green' ? '#dcfce7' : ($statusColor === 'orange' ? '#fff7ed' : ($statusColor === 'blue' ? '#eff6ff' : '#f3f4f6')) }};color:{{ $statusColor === 'green' ? '#16a34a' : ($statusColor === 'orange' ? '#ea580c' : ($statusColor === 'blue' ? '#2563eb' : '#6b7280')) }};">
                            {{ $statusLabel }}
                        </span>
                        <br>
                        @if(in_array($booking->payment_status, ['paid', 'approved']))
                            <a href="{{ route('deportation.success', $booking->booking_code) }}" style="display:inline-block;margin-top:8px;color:#2563EB;text-decoration:none;font-weight:600;font-size:13px;">View Ticket</a>
                        @elseif($booking->payment_status === 'pending')
                            <a href="{{ route('deportation.payment', $booking->booking_code) }}" style="display:inline-block;margin-top:8px;color:#ea580c;text-decoration:none;font-weight:600;font-size:13px;">Pay</a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        {{ $bookings->links() }}
    @endif
</div>
@endsection
