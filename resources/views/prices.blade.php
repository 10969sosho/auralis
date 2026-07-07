@extends('layouts.app')
@section('title', 'Daftar Harga')

@section('content')
<div class="price-page">
    <div class="price-page-header">
        <p class="guest-section-label" data-translate-en="Price List" data-translate-id="Daftar Harga">Price List</p>
        <h1 class="price-page-title" data-translate-en="Ticket Prices" data-translate-id="Harga Tiket">Ticket Prices</h1>
        <p class="price-page-sub" data-translate-en="Ticket price information for every shipping route we serve." data-translate-id="Informasi harga tiket untuk setiap rute pelayaran yang kami layani.">Ticket price information for every shipping route we serve.</p>
    </div>

    <div class="price-layout">
        <div class="price-sidebar">
            <div class="price-sidebar-title" data-translate-en="Province Category" data-translate-id="Kategori Provinsi">Province Category</div>
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
                        <th data-translate-en="Route" data-translate-id="Rute">Route</th>
                        <th data-translate-en="Class" data-translate-id="Kelas">Class</th>
                        <th data-translate-en="Price (RM)" data-translate-id="Harga (RM)">Price (RM)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($prices as $key => $schedule)
                        <tr>
                            <td>{{ $schedule->route->origin_port }} → {{ $schedule->route->destination_port }}</td>
                            <td>VIP</td>
                            <td class="price-amount">RM {{ number_format($schedule->vip_price, 2) }}</td>
                        </tr>
                        <tr>
                            <td>{{ $schedule->route->origin_port }} → {{ $schedule->route->destination_port }}</td>
                            <td>Regular</td>
                            <td class="price-amount">RM {{ number_format($schedule->regular_price, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align:center;color:#6C757D;padding:40px;" data-translate-en="No price data available yet." data-translate-id="Belum ada data harga yang tersedia.">
                                No price data available yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-center" style="margin-top:40px;">
        <a href="{{ route('schedules') }}" class="guest-hero-btn" style="display:inline-flex;" data-translate-en="Book Ticket Now" data-translate-id="Pesan Tiket Sekarang">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            Book Ticket Now
        </a>
    </div>
</div>

<style>
.price-page {
    max-width: 1100px;
    margin: 0 auto;
}
.price-page-header {
    text-align: center;
    margin-bottom: 48px;
    background: linear-gradient(135deg, rgba(11,125,218,0.85), rgba(78,162,255,0.75)), url('{{ asset("images/ferry-story.jpeg") }}') center/cover no-repeat;
    border-radius: 16px;
    padding: 60px 48px;
    min-height: 250px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}
.price-page-header .guest-section-label { color: rgba(255,255,255,0.8); }
.price-page-title {
    font-size: 42px;
    font-weight: 700;
    color: #fff;
    margin-bottom: 12px;
}
.price-page-sub {
    font-size: 16px;
    color: rgba(255,255,255,0.8);
    max-width: 600px;
    margin: 0 auto;
    line-height: 1.8;
}
@media (max-width: 768px) {
    .price-page-title { font-size: 28px; }
    .price-page-header { padding: 40px 24px; }
}
</style>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebarItems = document.querySelectorAll('.price-sidebar-item');
    const rows = document.querySelectorAll('.price-table tbody tr');

    const rowGroups = [];
    for (let i = 0; i < rows.length; i += 2) {
        rowGroups.push([rows[i], rows[i + 1]]);
    }

    sidebarItems.forEach(function(item) {
        item.addEventListener('click', function() {
            sidebarItems.forEach(el => el.classList.remove('active'));
            this.classList.add('active');

            const province = this.textContent.trim().toLowerCase();

            rowGroups.forEach(function(group) {
                const firstRow = group[0];
                if (!firstRow) return;
                const routeCell = firstRow.cells[0];
                const routeText = routeCell ? routeCell.textContent.trim().toLowerCase() : '';

                if (province === '' || routeText.includes(province)) {
                    group.forEach(r => { if (r) r.style.display = ''; });
                } else {
                    group.forEach(r => { if (r) r.style.display = 'none'; });
                }
            });
        });
    });
});
</script>
@endpush
@endsection