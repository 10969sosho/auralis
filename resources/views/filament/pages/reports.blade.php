<x-filament-panels::page>
<div class="report-dashboard">

    {{-- Header --}}
    <div class="rd-header">
        <div>
            <h1 class="rd-title">Schedule Reports & Analytics</h1>
            <p class="rd-subtitle">Realtime operational and revenue analytics</p>
        </div>
        <div class="rd-actions">
            <button wire:click="$refresh" class="rd-btn rd-btn-gray">
                <svg class="rd-icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23,4 23,10 17,10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                Refresh
            </button>
            <a href="{{ route('reports.csv', ['schedule_id' => $this->scheduleId, 'date_from' => $this->dateFrom, 'date_to' => $this->dateTo]) }}"
               target="_blank" class="rd-btn rd-btn-green">
                <svg class="rd-icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7,10 12,15 17,10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Export CSV
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="rd-card rd-filter-bar">
        <div class="rd-filter-grid">
            <select wire:model.live="scheduleId" class="rd-select">
                <option value="">All Schedules</option>
                @foreach($this->getScheduleList() as $s)
                    <option value="{{ $s['id'] }}">{{ $s['vessel']['name'] }} — {{ \Carbon\Carbon::parse($s['departure_time'])->format('d M Y') }}</option>
                @endforeach
            </select>
            <select wire:model.live="status" class="rd-select">
                <option value="">All Status</option>
                <option value="scheduled">Scheduled</option>
                <option value="departed">Departed</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <input type="date" wire:model.live="dateFrom" class="rd-input" placeholder="Date From">
            <input type="date" wire:model.live="dateTo" class="rd-input" placeholder="Date To">
        </div>
        <button wire:click="$set('scheduleId', ''); $set('status', ''); $set('dateFrom', ''); $set('dateTo', '')" class="rd-reset-btn">
            <svg class="rd-icon-xs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            Reset
        </button>
    </div>

    {{-- KPI Cards --}}
    <div class="rd-section" wire:poll.30s>
        @livewire(\App\Filament\Widgets\ReportsStatsOverviewWidget::class)
    </div>

    {{-- Charts --}}
    <div class="rd-charts-row">
        <div class="rd-chart-box" wire:poll.30s>
            @livewire(\App\Filament\Widgets\RevenueChartWidget::class)
        </div>
        <div class="rd-chart-box" wire:poll.30s>
            @livewire(\App\Filament\Widgets\BookingTrendChartWidget::class)
        </div>
    </div>

    {{-- Table --}}
    <div class="rd-section" wire:poll.30s>
        @livewire(\App\Filament\Widgets\ScheduleTableWidget::class)
    </div>

</div>

<style>
.report-dashboard {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* Header */
.rd-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    flex-wrap: wrap;
}
.rd-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #111827;
    letter-spacing: -0.02em;
}
.rd-subtitle {
    margin-top: 2px;
    font-size: 0.85rem;
    color: #6b7280;
}
.rd-actions {
    display: flex;
    gap: 8px;
    align-items: center;
}
.rd-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: background 0.15s, box-shadow 0.15s;
}
.rd-btn-gray {
    background: #f3f4f6;
    color: #374151;
}
.rd-btn-gray:hover {
    background: #e5e7eb;
}
.rd-btn-green {
    background: #059669;
    color: #fff;
}
.rd-btn-green:hover {
    background: #047857;
}
.rd-icon-sm { width: 14px; height: 14px; flex-shrink: 0; }
.rd-icon-xs { width: 12px; height: 12px; flex-shrink: 0; }

/* Cards */
.rd-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.04), 0 1px 3px rgba(0,0,0,0.06);
    border: 1px solid #e5e7eb;
}

/* Filter Bar */
.rd-filter-bar {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    flex-wrap: wrap;
}
.rd-filter-grid {
    display: flex;
    gap: 10px;
    flex: 1;
    flex-wrap: wrap;
}
.rd-select,
.rd-input {
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 0.82rem;
    color: #374151;
    background: #fff;
    min-width: 0;
    outline: none;
    transition: border-color 0.15s;
}
.rd-select:focus,
.rd-input:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 2px rgba(99,102,241,0.15);
}
.rd-select { flex: 1; min-width: 160px; max-width: 280px; }
.rd-input { flex: 1; min-width: 140px; max-width: 200px; }
.rd-reset-btn {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 8px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #fff;
    color: #6b7280;
    font-size: 0.78rem;
    font-weight: 500;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.15s;
}
.rd-reset-btn:hover {
    background: #f9fafb;
    color: #374151;
    border-color: #d1d5db;
}

/* Section */
.rd-section {
    /* widget sudah self-contained */
}

/* Charts Row */
.rd-charts-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}
.rd-chart-box {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.04);
    padding: 16px;
    min-height: 260px;
}

/* Override Filament chart canvas */
.rd-chart-box canvas {
    max-height: 280px !important;
}

/* Mobile */
@media (max-width: 767px) {
    .rd-header { flex-direction: column; }
    .rd-charts-row { grid-template-columns: 1fr; }
    .rd-filter-bar { flex-direction: column; align-items: stretch; }
    .rd-filter-grid { flex-direction: column; }
    .rd-select, .rd-input { max-width: 100%; }
}
</style>

</x-filament-panels::page>
