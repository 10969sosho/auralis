@extends('layouts.app')
@section('title', 'Deportation Tickets')

@section('content')
<div class="payment-page" style="max-width:800px;margin:0 auto;">
    <div style="text-align:center;padding:20px 0;">
        <svg viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2" style="width:64px;height:64px;margin:0 auto 12px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <h1 style="font-size:26px;font-weight:700;color:#059669;">Payment Successful!</h1>
        <p style="color:#64748b;margin-top:4px;">Booking Code: <strong>{{ $booking->booking_code }}</strong></p>
        <p style="color:#ea580c;font-weight:600;margin-top:8px;">OPEN TICKET — No expiry date, can be used anytime.</p>
    </div>

    {{-- Booking Info --}}
    <div style="background:#fff;border-radius:16px;padding:20px;box-shadow:0 1px 2px rgba(0,0,0,0.05);border:1px solid #e5e7eb;margin-bottom:20px;">
        <h2 style="font-size:16px;font-weight:700;margin-bottom:14px;">Booking Information</h2>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:14px;">
            <div style="color:#64748b;">Route</div>
            <div style="font-weight:600;">{{ $booking->route_display }}</div>
            <div style="color:#64748b;">Vessel</div>
            <div style="font-weight:600;">{{ $booking->vessel_display }}</div>
            <div style="color:#64748b;">Shelter Point</div>
            <div style="font-weight:600;">{{ $booking->user->shelter_point_name ?? '—' }}</div>
            <div style="color:#64748b;">Total Paid</div>
            <div style="font-weight:700;color:#2563EB;">RM{{ number_format($booking->total_amount, 2) }}</div>
        </div>
    </div>

    {{-- Tickets / QR Codes --}}
    <h2 style="font-size:18px;font-weight:700;margin-bottom:14px;">Deportation Tickets (Open)</h2>

    @foreach($booking->passengers as $passenger)
        @php $ticket = $passenger->ticket; @endphp
        <div style="background:#fff;border-radius:16px;padding:20px;box-shadow:0 1px 2px rgba(0,0,0,0.05);border:1px solid #e5e7eb;margin-bottom:16px;">
            <div style="display:flex;gap:20px;align-items:flex-start;flex-wrap:wrap;">
                <div style="flex-shrink:0;">
                    @if($ticket)
                        @php
                            $qrData = json_encode(['ticket_id' => $ticket->id, 'ticket_number' => $ticket->ticket_number]);
                        @endphp
                        <div id="qr-{{ $ticket->id }}" style="width:120px;height:120px;background:#f8fafc;border-radius:10px;display:flex;align-items:center;justify-content:center;border:2px solid #e5e7eb;">
                            <canvas id="qrCanvas-{{ $ticket->id }}" width="110" height="110"></canvas>
                        </div>
                        <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            new QRCode(document.getElementById('qrCanvas-{{ $ticket->id }}'), {
                                text: @json($qrData),
                                width: 110,
                                height: 110,
                                colorDark: '#1e293b',
                                colorLight: '#ffffff',
                            });
                        });
                        </script>
                        <p style="text-align:center;font-size:11px;color:#64748b;margin-top:6px;">{{ $ticket->ticket_number }}</p>
                    @else
                        <div style="width:120px;height:120px;background:#f1f5f9;border-radius:10px;display:flex;align-items:center;justify-content:center;border:2px dashed #cbd5e1;">
                            <span style="font-size:11px;color:#94a3b8;text-align:center;">No Ticket</span>
                        </div>
                    @endif
                </div>
                <div style="flex:1;min-width:200px;">
                    <h3 style="font-weight:700;font-size:16px;">{{ $passenger->full_name }}</h3>
                    <div style="display:grid;grid-template-columns:120px 1fr;gap:4px 8px;font-size:13px;margin-top:8px;">
                        <span style="color:#64748b;">Gender:</span>
                        <span style="text-transform:capitalize;">{{ $passenger->gender }}</span>
                        <span style="color:#64748b;">Nationality:</span>
                        <span>{{ $passenger->nationality }}</span>
                        <span style="color:#64748b;">Passport No:</span>
                        <span>{{ $passenger->passport_number }}</span>
                        <span style="color:#64748b;">Class:</span>
                        <span style="text-transform:uppercase;font-weight:600;color:{{ $passenger->ticket_class === 'vip' ? '#2563EB' : '#059669' }};">{{ $passenger->ticket_class }}</span>
                        <span style="color:#64748b;">Ticket Status:</span>
                        <span>
                            @if($ticket)
                                <span style="display:inline-block;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:600;
                                    @if($ticket->ticket_status === 'active') background:#dcfce7;color:#16a34a;
                                    @elseif($ticket->ticket_status === 'used') background:#f1f5f9;color:#64748b;
                                    @endif">
                                    @if($ticket->ticket_status === 'active') Active (Open)
                                    @elseif($ticket->ticket_status === 'used') Used / Boarded
                                    @else {{ $ticket->ticket_status }}
                                    @endif
                                </span>
                            @else
                                <span style="color:#94a3b8;">—</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <div style="display:flex;gap:12px;flex-wrap:wrap;">
        <a href="{{ route('deportation.dashboard') }}" style="display:inline-flex;align-items:center;gap:6px;background:#2563EB;color:#fff;padding:12px 24px;border-radius:10px;text-decoration:none;font-weight:600;font-size:14px;">
            Back to Dashboard
        </a>
        <a href="{{ route('deportation.history') }}" style="display:inline-flex;align-items:center;gap:6px;background:#f1f5f9;color:#1e293b;padding:12px 24px;border-radius:10px;text-decoration:none;font-weight:600;font-size:14px;border:1px solid #e5e7eb;">
            Booking History
        </a>
    </div>
</div>
@endsection
