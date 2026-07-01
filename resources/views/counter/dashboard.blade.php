@extends('layouts.app')
@section('title', 'Counter Dashboard')

@section('content')
<div style="padding:24px 0;">
    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Ticket Counter</h1>
            <p class="text-sm text-gray-500 mt-1">Offline ticket sales & cash payment</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <form action="{{ route('counter.search') }}" method="GET" style="display:flex;gap:8px;">
                <input type="text" name="query" placeholder="Search booking code..." class="form-input" style="width:220px;">
                <button type="submit" class="btn btn-primary btn-sm">Search</button>
            </form>
        </div>
    </div>

    <h2 class="text-lg font-semibold text-gray-800 mb-4">Upcoming Schedules</h2>

    <div style="display:flex;flex-direction:column;gap:12px;">
        @forelse($schedules as $schedule)
            @php
                $vipBooked = (int)$schedule->vipBooked;
                $regularBooked = (int)$schedule->regularBooked;
                $vipLeft = $schedule->vessel->vip_capacity - $vipBooked;
                $regularLeft = $schedule->vessel->regular_capacity - $regularBooked;
                $totalLeft = $vipLeft + $regularLeft;
                $canBook = !$schedule->isH6Passed && $totalLeft > 0;
            @endphp
            <div class="card" style="display:flex;flex-direction:row;align-items:center;gap:24px;padding:16px 24px;opacity:{{ $canBook ? '1' : '0.55' }};">
                <div style="flex:1;display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div style="width:40px;height:40px;border-radius:8px;background:#EFF6FF;display:flex;align-items:center;justify-content:center;color:#2563EB;flex-shrink:0;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;"><path d="M2 21h20M6 18l2-6h8l2 6M9 12V7M15 12V7M12 7V3"/><path d="M5 7h14l-2 5H7L5 7Z"/></svg>
                        </div>
                        <div>
                            <div style="font-weight:600;font-size:0.95rem;">{{ $schedule->vessel->name }}</div>
                            <div style="font-size:0.8rem;color:#6b7280;">{{ $schedule->route->origin_port }} → {{ $schedule->route->destination_port }}</div>
                        </div>
                    </div>
                    <div style="display:flex;gap:16px;font-size:0.8rem;color:#374151;">
                        <span>Departure: <strong>{{ $schedule->departure_time->format('d M Y, H:i') }}</strong></span>
                        <span>VIP: <strong>{{ max(0, $vipLeft) }}</strong> left</span>
                        <span>Regular: <strong>{{ max(0, $regularLeft) }}</strong> left</span>
                        <span>VIP: RM {{ number_format($schedule->vip_price, 2) }} · Reg: RM {{ number_format($schedule->regular_price, 2) }}</span>
                    </div>
                </div>
                <div style="flex-shrink:0;">
                    @if($canBook)
                        <a href="{{ route('counter.create', $schedule) }}" class="btn btn-primary btn-sm" style="white-space:nowrap;">Sell Ticket</a>
                    @elseif($schedule->isH6Passed)
                        <span style="display:inline-block;padding:6px 14px;font-size:0.8rem;font-weight:600;color:#DC2626;background:#FEE2E2;border-radius:6px;white-space:nowrap;">Booking Closed</span>
                    @else
                        <span style="display:inline-block;padding:6px 14px;font-size:0.8rem;font-weight:600;color:#DC2626;background:#FEE2E2;border-radius:6px;white-space:nowrap;">Fully Booked</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-12" style="grid-column:1/-1;">
                <p class="text-gray-500">No upcoming schedules available.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection