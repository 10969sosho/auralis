@extends('layouts.app')
@section('title', 'Schedule Reports - Admin')

@section('content')
<div class="reports-page">
    <div class="reports-header">
        <div>
            <h1 class="reports-title">Schedule Reports</h1>
            <p class="reports-sub">Per-schedule analytics, revenue & occupancy metrics</p>
        </div>
        <div class="reports-header-actions">
            <button class="btn btn-outline btn-sm" onclick="document.getElementById('filterPanel').classList.toggle('open')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                Filters
            </button>
            <form action="{{ route('reports.csv') }}" method="GET" class="reports-export-form" style="display:inline-flex;">
                <select name="schedule_id" class="form-input" style="width:auto;display:inline-block;">
                    <option value="">All Schedules</option>
                    @foreach($allSchedules as $s)
                        <option value="{{ $s->id }}" {{ request('schedule_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->vessel->name }} - {{ $s->departure_time->format('d M Y') }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary btn-sm">Export CSV</button>
            </form>
        </div>
    </div>

    <div class="filter-panel" id="filterPanel" style="display:none;margin-bottom:20px;">
        <form action="{{ route('reports.index') }}" method="GET" class="card" style="padding:16px;">
            <div class="grid sm:grid-cols-4 gap-4">
                <div class="form-group">
                    <label class="form-label">Schedule</label>
                    <select name="schedule_id" class="form-input">
                        <option value="">All</option>
                        @foreach($allSchedules as $s)
                            <option value="{{ $s->id }}" {{ request('schedule_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->vessel->name }} ({{ $s->departure_time->format('d M Y') }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-input">
                        <option value="">All</option>
                        <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                        <option value="departed" {{ request('status') === 'departed' ? 'selected' : '' }}>Departed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-input" value="{{ request('date_from') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" class="form-input" value="{{ request('date_to') }}">
                </div>
            </div>
            <div class="flex gap-3 mt-4">
                <button type="submit" class="btn btn-primary btn-sm">Apply</button>
                <a href="{{ route('reports.index') }}" class="btn btn-outline btn-sm">Reset</a>
            </div>
        </form>
    </div>

    @if($summaryMetrics)
    <div class="summary-cards">
        <div class="summary-card">
            <span class="summary-num">{{ $summaryMetrics['total_bookings'] }}</span>
            <span class="summary-label">Total Bookings</span>
        </div>
        <div class="summary-card summary-card-revenue">
            <span class="summary-num">MYR {{ number_format($summaryMetrics['total_revenue'], 2) }}</span>
            <span class="summary-label">Total Revenue</span>
        </div>
        <div class="summary-card">
            <span class="summary-num">{{ $summaryMetrics['total_passengers'] }}</span>
            <span class="summary-label">Passengers Registered</span>
        </div>
        <div class="summary-card summary-card-paid">
            <span class="summary-num">{{ $summaryMetrics['total_paid'] }}</span>
            <span class="summary-label">Paid Passengers</span>
        </div>
        <div class="summary-card">
            <span class="summary-num">{{ $summaryMetrics['total_boardings'] }}</span>
            <span class="summary-label">Boarded</span>
        </div>
        <div class="summary-card summary-card-pending">
            <span class="summary-num">{{ $summaryMetrics['total_pending'] }}</span>
            <span class="summary-label">Pending Payment</span>
        </div>
        <div class="summary-card summary-card-refund">
            <span class="summary-num">{{ $summaryMetrics['total_refunds'] }}</span>
            <span class="summary-label">Refunds</span>
        </div>
        <div class="summary-card summary-card-cancel">
            <span class="summary-num">{{ $summaryMetrics['total_cancelled'] }}</span>
            <span class="summary-label">Cancelled</span>
        </div>
    </div>
    @endif

    <div class="reports-table-wrap">
        <table class="reports-table">
            <thead>
                <tr>
                    <th>Vessel</th>
                    <th>Route</th>
                    <th>Departure</th>
                    <th>Status</th>
                    <th>Registered</th>
                    <th>Paid</th>
                    <th>Pending</th>
                    <th>Boarded</th>
                    <th>Refund</th>
                    <th>Cancel</th>
                    <th>Revenue</th>
                    <th>Occupancy</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schedules as $schedule)
                    @php $m = app(\App\Http\Controllers\AdminReportController::class)->getMetrics($schedule); @endphp
                    <tr>
                        <td>{{ $m['schedule']['vessel'] }}</td>
                        <td>{{ $m['schedule']['route'] }}</td>
                        <td>{{ $m['schedule']['departure'] }}</td>
                        <td><span class="status-badge status-{{ $m['schedule']['status'] }}">{{ ucfirst($m['schedule']['status']) }}</span></td>
                        <td>{{ $m['total_registered_passengers'] }}</td>
                        <td>{{ $m['total_payment_success'] }}</td>
                        <td>{{ $m['total_pending'] }}</td>
                        <td>{{ $m['total_boarded'] }}</td>
                        <td>{{ $m['total_refund'] }}</td>
                        <td>{{ $m['total_cancel'] }}</td>
                        <td>MYR {{ number_format($m['total_revenue'], 0) }}</td>
                        <td>
                            <div class="occ-bar">
                                <div class="occ-fill" style="width: {{ $m['occupancy_percentage'] }}%"></div>
                            </div>
                            {{ $m['occupancy_percentage'] }}%
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="12" style="text-align:center;padding:40px;">No schedules found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="reports-pagination">
        {{ $schedules->links() }}
    </div>
</div>

<style>
.reports-page { padding: 24px 0; }
.reports-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
.reports-header-actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
.reports-title { font-size: 24px; font-weight: 700; }
.reports-sub { color: #6b7280; margin-top: 4px; }
.reports-export-form { display: flex; gap: 8px; align-items: center; }
.reports-table-wrap { overflow-x: auto; background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
.reports-table { width: 100%; border-collapse: collapse; min-width: 1100px; }
.reports-table th { background: #f9fafb; padding: 12px 14px; font-size: 0.75rem; font-weight: 600; color: #374151; text-align: left; border-bottom: 2px solid #e5e7eb; white-space: nowrap; text-transform: uppercase; letter-spacing: 0.5px; }
.reports-table td { padding: 12px 14px; font-size: 0.85rem; border-bottom: 1px solid #f3f4f6; }
.reports-table tbody tr:hover { background: #f9fafb; }
.occ-bar { width: 80px; height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden; display: inline-block; vertical-align: middle; margin-right: 6px; }
.occ-fill { height: 100%; background: #2563eb; border-radius: 4px; }
.reports-pagination { margin-top: 20px; }

.summary-cards { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 12px; margin-bottom: 24px; }
.summary-card { background: #fff; border-radius: 10px; padding: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); text-align: center; }
.summary-num { display: block; font-size: 1.5rem; font-weight: 700; color: #374151; }
.summary-label { display: block; font-size: 0.7rem; color: #6b7280; margin-top: 4px; text-transform: uppercase; letter-spacing: 0.5px; }
.summary-card-revenue .summary-num { color: #059669; font-size: 1.2rem; }
.summary-card-paid .summary-num { color: #2563EB; }
.summary-card-pending .summary-num { color: #D97706; }
.summary-card-refund .summary-num { color: #DC2626; }
.summary-card-cancel .summary-num { color: #6B7280; }

.status-badge { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 0.7rem; font-weight: 600; }
.status-scheduled { background: #DBEAFE; color: #1E40AF; }
.status-departed { background: #D1FAE5; color: #065F46; }
.status-cancelled { background: #FEE2E2; color: #991B1B; }
.status-completed { background: #E0E7FF; color: #3730A3; }

.filter-panel.open { display: block !important; }
</style>
@endsection
