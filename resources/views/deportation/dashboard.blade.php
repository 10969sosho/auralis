@extends('layouts.app')
@section('title', 'Dashboard Deportasi')

@section('content')
<div class="deportation-page">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;margin-bottom:20px;">
        <div>
            <h1 style="font-size:26px;font-weight:700;color:#1e293b;">Dashboard Deportasi</h1>
            <p style="color:#64748b;margin-top:2px;">Selamat datang, {{ $user->name }}</p>
        </div>
        <a href="{{ route('deportation.booking') }}" style="display:inline-flex;align-items:center;gap:8px;background:#2563EB;color:#fff;padding:12px 20px;border-radius:10px;text-decoration:none;font-weight:600;font-size:14px;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:18px;height:18px;"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            Beli Tiket Kapal
        </a>
    </div>

    {{-- Shelter Point Info --}}
    <div style="background:linear-gradient(135deg,#1e3a5f,#2563EB);border-radius:16px;padding:24px;color:#fff;margin-bottom:24px;display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
        <div style="background:rgba(255,255,255,0.15);border-radius:50%;width:56px;height:56px;display:flex;align-items:center;justify-content:center;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:28px;height:28px;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
        </div>
        <div>
            <p style="font-size:12px;opacity:0.8;text-transform:uppercase;letter-spacing:0.5px;">Titik Penampungan</p>
            <p style="font-size:20px;font-weight:700;">{{ $user->shelter_point_name ?? '—' }}</p>
            <p style="font-size:14px;opacity:0.85;">Tambang bas: <strong>RM{{ number_format($user->shelter_fee, 2) }}</strong></p>
        </div>
    </div>

    {{-- Recent Bookings --}}
    <div style="background:#fff;border-radius:16px;box-shadow:0 1px 3px rgba(0,0,0,0.08);border:1px solid #e5e7eb;overflow:hidden;">
        <div style="padding:18px 20px;border-bottom:1px solid #f1f5f9;">
            <h2 style="font-size:16px;font-weight:700;">Tempahan Terkini</h2>
        </div>

        @if($bookings->isEmpty())
            <div style="padding:40px 20px;text-align:center;color:#94a3b8;">
                <p>Tiada tempahan lagi.</p>
                <a href="{{ route('deportation.booking') }}" style="display:inline-block;margin-top:8px;color:#2563EB;font-weight:600;text-decoration:none;">Buat tempahan pertama anda</a>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8fafc;font-size:12px;text-transform:uppercase;color:#64748b;letter-spacing:0.3px;">
                            <th style="padding:12px 16px;text-align:left;font-weight:600;">Kod Tempahan</th>
                            <th style="padding:12px 16px;text-align:left;font-weight:600;">Laluan</th>
                            <th style="padding:12px 16px;text-align:left;font-weight:600;">Jadual</th>
                            <th style="padding:12px 16px;text-align:left;font-weight:600;">Penumpang</th>
                            <th style="padding:12px 16px;text-align:left;font-weight:600;">Jumlah</th>
                            <th style="padding:12px 16px;text-align:left;font-weight:600;">Status</th>
                            <th style="padding:12px 16px;text-align:right;font-weight:600;">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:12px 16px;font-weight:600;font-family:monospace;">{{ $booking->booking_code }}</td>
                            <td style="padding:12px 16px;font-size:14px;">{{ $booking->schedule->route->origin_port }} → {{ $booking->schedule->route->destination_port }}</td>
                            <td style="padding:12px 16px;font-size:13px;">{{ $booking->schedule->departure_time->format('d M Y, H:i') }}</td>
                            <td style="padding:12px 16px;">{{ $booking->total_passengers }}</td>
                            <td style="padding:12px 16px;font-weight:600;">RM{{ number_format($booking->total_amount, 2) }}</td>
                            <td style="padding:12px 16px;">
                                @php
                                    $statusColor = match($booking->payment_status) {
                                        'paid', 'approved' => 'green',
                                        'pending' => 'orange',
                                        'awaiting_approval' => 'blue',
                                        'failed', 'expired', 'cancelled' => 'red',
                                        default => 'gray'
                                    };
                                    $statusLabel = match($booking->payment_status) {
                                        'paid', 'approved' => 'Berbayar',
                                        'pending' => 'Menunggu Bayaran',
                                        'awaiting_approval' => 'Menunggu Kelulusan',
                                        'failed' => 'Gagal',
                                        'expired' => 'Luput',
                                        default => $booking->payment_status
                                    };
                                @endphp
                                <span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:12px;font-weight:600;background:{{ $statusColor === 'green' ? '#dcfce7' : ($statusColor === 'orange' ? '#fff7ed' : ($statusColor === 'blue' ? '#eff6ff' : ($statusColor === 'red' ? '#fef2f2' : '#f3f4f6'))) }};color:{{ $statusColor === 'green' ? '#16a34a' : ($statusColor === 'orange' ? '#ea580c' : ($statusColor === 'blue' ? '#2563eb' : ($statusColor === 'red' ? '#dc2626' : '#6b7280'))) }};">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td style="padding:12px 16px;text-align:right;">
                                @if(in_array($booking->payment_status, ['paid', 'approved']))
                                    <a href="{{ route('deportation.success', $booking->booking_code) }}" style="color:#2563EB;text-decoration:none;font-weight:600;font-size:13px;">Lihat Tiket</a>
                                @elseif($booking->payment_status === 'pending')
                                    <a href="{{ route('deportation.payment', $booking->booking_code) }}" style="color:#ea580c;text-decoration:none;font-weight:600;font-size:13px;">Bayar</a>
                                @else
                                    <span style="font-size:13px;color:#94a3b8;">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
