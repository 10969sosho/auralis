@extends('layouts.app')
@section('title', 'Deportation Ticket #' . $ticket->ticket_number)

@section('content')
<div style="max-width:500px;margin:0 auto;">
    <a href="{{ route('deportation.dashboard') }}" style="color:#64748b;text-decoration:none;font-size:14px;display:inline-flex;align-items:center;gap:4px;margin-bottom:16px;">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><polyline points="15 18 9 12 15 6"/></svg>
        Back
    </a>

    <div style="background:#fff;border-radius:16px;padding:24px;box-shadow:0 1px 3px rgba(0,0,0,0.08);border:1px solid #e5e7eb;text-align:center;">
        @php $qrData = json_encode(['ticket_id' => $ticket->id, 'ticket_number' => $ticket->ticket_number]); @endphp
        <div id="qr-container" style="width:200px;height:200px;margin:0 auto 16px;background:#f8fafc;border-radius:12px;display:flex;align-items:center;justify-content:center;border:2px solid #e5e7eb;">
            <canvas id="qrCanvas" width="190" height="190"></canvas>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            new QRCode(document.getElementById('qrCanvas'), {
                text: @json($qrData),
                width: 190,
                height: 190,
                colorDark: '#1e293b',
                colorLight: '#ffffff',
            });
        });
        </script>

        <h2 style="font-size:18px;font-weight:700;color:#1e293b;">{{ $ticket->passenger->full_name }}</h2>
        <p style="font-size:13px;color:#64748b;">{{ $ticket->ticket_number }}</p>

        <div style="display:inline-block;padding:4px 14px;border-radius:20px;font-size:13px;font-weight:700;
            @if($ticket->ticket_status === 'active') background:#dcfce7;color:#16a34a;
            @elseif($ticket->ticket_status === 'used') background:#f1f5f9;color:#64748b;
            @endif;margin-top:8px;">
            @if($ticket->ticket_status === 'active') OPEN TICKET &mdash; No Expiry
            @elseif($ticket->ticket_status === 'used') USED &mdash; {{ $ticket->boarded_at?->format('d M Y, H:i') }}
            @endif
        </div>

        <div style="margin-top:20px;text-align:left;border-top:1px solid #f1f5f9;padding-top:16px;display:grid;grid-template-columns:120px 1fr;gap:6px 8px;font-size:13px;">
            <span style="color:#64748b;">Route:</span>
            <span style="font-weight:600;">{{ $ticket->booking->schedule->route->origin_port }} &rarr; {{ $ticket->booking->schedule->route->destination_port }}</span>
            <span style="color:#64748b;">Vessel:</span>
            <span>{{ $ticket->booking->schedule->vessel->name }}</span>
            <span style="color:#64748b;">Class:</span>
            <span style="font-weight:600;text-transform:uppercase;">{{ $ticket->ticket_class }}</span>
            <span style="color:#64748b;">Gender:</span>
            <span style="text-transform:capitalize;">{{ $ticket->passenger->gender }}</span>
            <span style="color:#64748b;">Nationality:</span>
            <span>{{ $ticket->passenger->nationality }}</span>
            <span style="color:#64748b;">Passport:</span>
            <span>{{ $ticket->passenger->passport_number }}</span>
        </div>
    </div>
</div>
@endsection
