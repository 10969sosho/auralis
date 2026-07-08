<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name')) - Auralis8</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/app.css">
    <style>
    .pw-show svg:last-child { display: none; }
    .pw-show.pw-visible svg:first-child { display: none; }
    .pw-show.pw-visible svg:last-child { display: block; }
    </style>
    @stack('styles')
</head>
<body>
    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:0;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error" style="margin-bottom:0;">{{ session('error') }}</div>
    @endif

    @yield('content')

    @stack('scripts')
</body>
</html>
