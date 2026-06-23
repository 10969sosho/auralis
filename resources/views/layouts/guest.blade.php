@extends('layouts.app')

@section('page_class', 'guest-page')

{{-- Override the main section to support full-width content for landing page --}}
@section('main_content')
    @if(session('success'))
        <div class="alert alert-success guest-container" style="margin-bottom:0;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error guest-container" style="margin-bottom:0;">{{ session('error') }}</div>
    @endif
    @hasSection('full_width')
        @yield('content')
    @else
        <div class="guest-container guest-main-padded">
            @yield('content')
        </div>
    @endif
@endsection
