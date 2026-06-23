@extends('layouts.app')
@section('title', 'Booking Expired')

@section('content')
<div class="expired-page">
    <div class="expired-box">
        <div class="expired-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        </div>
        <h1 class="expired-title">Booking Expired</h1>
        <p class="expired-text">Your booking <strong>#{{ $booking->booking_code }}</strong> has expired because payment was not completed in time.</p>
        <a href="{{ route('schedules') }}" class="expired-btn">Search Again</a>
    </div>
</div>
@endsection
