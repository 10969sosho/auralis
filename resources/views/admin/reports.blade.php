@extends('layouts.app')
@section('title', 'Schedule Reports - Admin')

@section('content')
<div class="reports-page">
    <div class="reports-header">
        <div>
            <h1 class="reports-title">Schedule Reports</h1>
            <p class="reports-sub">Per-schedule analytics & occupancy metrics</p>
        </div>
        <form action="{{ route('admin.reports.csv') }}" method="GET" class="reports-export-form">
            <select name="schedule_id" class="form-input" style="width:auto;display:inline-block;">
                <option value="">All Schedules</option>
                @foreach($allSchedules as $s)
                    <option value="{{ $s->id }}" {{ request('schedule_id') == $s->id ? 'selected' : '' }}>
                        {{ $s->vessel->name }} - {{ $s->route->origin_port }} → {{ $s->route->destination_port }} ({{ $s->departure_time->format('d M Y') }})
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary">Export CSV</button>
        </form>
    </div>

    <div class="reports-table-wrap">
        <table class="reports-table">
            <thead>
                <tr>
                    <th>Vessel</th>
                    <th>Route</th>
                    <th>Departure</th>
                    <th>Registered</th>
                    <th>Payment Success</th>
                    <th>Boarded</th>
                    <th>Departed</th>
                    <th>Refund</th>
                    <th>Cancel</th>
                    <th>Remaining</th>
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
                        <td>{{ $m['total_registered_passengers'] }}</td>
                        <td>{{ $m['total_payment_success'] }}</td>
                        <td>{{ $m['total_boarded'] }}</td>
                        <td>{{ $m['total_departed'] ? 'Yes' : 'No' }}</td>
                        <td>{{ $m['total_refund'] }}</td>
                        <td>{{ $m['total_cancel'] }}</td>
                        <td>{{ $m['remaining_passengers'] }}</td>
                        <td>
                            <div class="occ-bar">
                                <div class="occ-fill" style="width: {{ $m['occupancy_percentage'] }}%"></div>
                            </div>
                            {{ $m['occupancy_percentage'] }}%
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="11" style="text-align:center;padding:40px;">No schedules found.</td></tr>
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
.reports-title { font-size: 24px; font-weight: 700; }
.reports-sub { color: #6b7280; margin-top: 4px; }
.reports-export-form { display: flex; gap: 8px; align-items: center; }
.reports-table-wrap { overflow-x: auto; background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
.reports-table { width: 100%; border-collapse: collapse; }
.reports-table th { background: #f9fafb; padding: 12px 16px; font-size: 13px; font-weight: 600; color: #374151; text-align: left; border-bottom: 2px solid #e5e7eb; white-space: nowrap; }
.reports-table td { padding: 12px 16px; font-size: 14px; border-bottom: 1px solid #f3f4f6; }
.reports-table tbody tr:hover { background: #f9fafb; }
.occ-bar { width: 80px; height: 8px; background: #e5e7eb; border-radius: 4px; overflow: hidden; display: inline-block; vertical-align: middle; margin-right: 6px; }
.occ-fill { height: 100%; background: #2563eb; border-radius: 4px; }
.reports-pagination { margin-top: 20px; }
</style>
@endsection
