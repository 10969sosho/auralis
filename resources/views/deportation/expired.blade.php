@extends('layouts.app')
@section('title', 'Tempahan Luput')

@section('content')
<div style="max-width:500px;margin:0 auto;text-align:center;padding:40px 0;">
    <svg viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2" style="width:64px;height:64px;margin:0 auto 16px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <h1 style="font-size:24px;font-weight:700;color:#DC2626;">Tempahan Telah Luput</h1>
    <p style="color:#64748b;margin-top:8px;">Masa pembayaran untuk tempahan <strong>{{ $booking->booking_code }}</strong> telah tamat.</p>
    <p style="color:#94a3b8;margin-top:4px;">Sila buat tempahan baru.</p>
    <a href="{{ route('deportation.dashboard') }}" style="display:inline-block;margin-top:20px;background:#2563EB;color:#fff;padding:12px 24px;border-radius:10px;text-decoration:none;font-weight:600;">
        Kembali ke Dashboard
    </a>
</div>
@endsection
