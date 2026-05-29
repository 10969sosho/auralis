@extends('layouts.app')
@section('title', 'Counter Dashboard')

@section('content')
<div class="counter-page">
    <div class="counter-header">
        <div>
            <h1 class="counter-title">Ticket Counter</h1>
            <p class="counter-sub">Offline ticket sales & cash payment</p>
        </div>
        <form action="{{ route('counter.search') }}" method="GET" class="counter-search-form">
            <div class="counter-search-group">
                <input type="text" name="query" placeholder="Search booking code or passenger name..." class="form-input" style="min-width:260px;">
                <button type="submit" class="btn btn-primary btn-sm">Search</button>
            </div>
        </form>
    </div>

    <h2 class="text-lg font-semibold mb-4">Upcoming Schedules</h2>

    <div class="counter-grid">
        @forelse($schedules as $schedule)
            @php
                $vipBooked = (int)$schedule->vipBooked;
                $regularBooked = (int)$schedule->regularBooked;
                $vipLeft = $schedule->vessel->vip_capacity - $vipBooked;
                $regularLeft = $schedule->vessel->regular_capacity - $regularBooked;
                $totalLeft = $vipLeft + $regularLeft;
                $canBook = !$schedule->isH6Passed && $totalLeft > 0;
            @endphp
            <div class="counter-card {{ !$canBook ? 'counter-card-disabled' : '' }}">
                <div class="counter-card-top">
                    <div class="counter-card-vessel">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:18px;height:18px;"><path d="M2 21h20M6 18l2-6h8l2 6M9 12V7M15 12V7M12 7V3"/><path d="M5 7h14l-2 5H7L5 7Z"/></svg>
                        <span class="font-semibold">{{ $schedule->vessel->name }}</span>
                    </div>
                    <span class="counter-card-departure">{{ $schedule->departure_time->format('d M Y, H:i') }}</span>
                </div>
                <div class="counter-card-route">
                    <span>{{ $schedule->route->origin_port }}</span>
                    <span style="color:#2563EB;font-weight:700;">→</span>
                    <span>{{ $schedule->route->destination_port }}</span>
                </div>
                <div class="counter-card-seats">
                    <span>VIP: <strong>{{ max(0, $vipLeft) }}</strong> left</span>
                    <span>Regular: <strong>{{ max(0, $regularLeft) }}</strong> left</span>
                </div>
                <div class="counter-card-prices">
                    <span>VIP: MYR {{ number_format($schedule->vip_price, 2) }}</span>
                    <span>Regular: MYR {{ number_format($schedule->regular_price, 2) }}</span>
                </div>
                <div class="counter-card-action">
                    @if($canBook)
                        <a href="{{ route('counter.create', $schedule) }}" class="btn btn-primary btn-block btn-sm">Sell Ticket</a>
                    @elseif($schedule->isH6Passed)
                        <span class="counter-closed">Booking Closed</span>
                    @else
                        <span class="counter-closed">Fully Booked</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="empty-state" style="grid-column:1/-1;">
                <h3>No upcoming schedules</h3>
                <p>No ferry schedules are available at this time.</p>
            </div>
        @endforelse
    </div>
</div>

<style>
.counter-page { padding: 24px 0; }
.counter-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; }
.counter-title { font-size: 24px; font-weight: 700; }
.counter-sub { color: #6b7280; margin-top: 4px; }
.counter-search-group { display: flex; gap: 8px; }
.counter-grid { display: grid; gap: 16px; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); }
.counter-card { background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); padding: 20px; }
.counter-card-disabled { opacity: 0.55; }
.counter-card-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; }
.counter-card-vessel { display: flex; align-items: center; gap: 6px; color: #2563EB; }
.counter-card-departure { font-size: 0.8rem; color: #6b7280; }
.counter-card-route { display: flex; align-items: center; gap: 8px; font-size: 0.9rem; margin-bottom: 12px; }
.counter-card-seats { display: flex; gap: 16px; font-size: 0.8rem; color: #374151; margin-bottom: 8px; }
.counter-card-prices { display: flex; gap: 16px; font-size: 0.8rem; color: #6b7280; margin-bottom: 14px; }
.counter-card-action { margin-top: auto; }
.counter-closed { display: block; text-align: center; padding: 8px; font-size: 0.85rem; font-weight: 600; color: #DC2626; background: #FEE2E2; border-radius: 6px; }
</style>
@endsection
