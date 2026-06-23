@extends('layouts.app')
@section('title', 'Harga Tiket')

@section('content')
<div class="price-page">
    <div class="price-page-header">
        <p class="guest-section-label">Daftar Harga</p>
        <h1 class="price-page-title">Harga Tiket</h1>
        <p class="price-page-sub">Informasi harga tiket untuk setiap rute pelayaran yang kami layani.</p>
    </div>

    <div class="price-layout">
        <div class="price-sidebar">
            <div class="price-sidebar-title">Kategori Provinsi</div>
            @forelse($ports as $port)
                <div class="price-sidebar-item {{ $loop->first ? 'active' : '' }}">{{ $port }}</div>
            @empty
                <div class="price-sidebar-item">—</div>
            @endforelse
        </div>
        <div class="price-content">
            <table class="price-table">
                <thead>
                    <tr>
                        <th>Rute</th>
                        <th>Kelas</th>
                        <th>Harga (MYR)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prices as $key => $schedule)
                        <tr>
                            <td>{{ $schedule->route->origin_port }} → {{ $schedule->route->destination_port }}</td>
                            <td>VIP</td>
                            <td class="price-amount">MYR {{ number_format($schedule->vip_price, 2) }}</td>
                        </tr>
                        <tr>
                            <td>{{ $schedule->route->origin_port }} → {{ $schedule->route->destination_port }}</td>
                            <td>Regular</td>
                            <td class="price-amount">MYR {{ number_format($schedule->regular_price, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align:center;color:#6C757D;padding:40px;">
                                Belum ada data harga tiket tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-center" style="margin-top:40px;">
        <a href="{{ route('schedules') }}" class="guest-hero-btn" style="display:inline-flex;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            Pesan Tiket Sekarang
        </a>
    </div>
</div>

<style>
.price-page {
    max-width: 1280px;
    margin: 0 auto;
}
.price-page-header {
    text-align: center;
    margin-bottom: 48px;
}
.price-page-title {
    font-size: 42px;
    font-weight: 700;
    color: #252B42;
    margin-bottom: 12px;
}
.price-page-sub {
    font-size: 16px;
    color: #6C757D;
    max-width: 600px;
    margin: 0 auto;
    line-height: 1.8;
}
@media (max-width: 768px) {
    .price-page-title { font-size: 28px; }
}
</style>
@endsection
