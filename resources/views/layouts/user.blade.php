<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#b91c1c">
    <title>@yield('title', 'DapurLink KUO')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('theme-red.css') }}?v={{ filemtime(public_path('theme-red.css')) }}">
</head>
<body class="user-body">
<div class="site-utility-bar">
    <div>
        <span>Waktu operasi: 8.00 pagi &ndash; 10.00 malam</span>
        <span>Dapur Siswa Madani KUO</span>
        <span class="utility-status"><i></i> Sistem dalam talian</span>
    </div>
</div>
<header class="user-header">
    <a href="{{ route('user.dashboard') }}" class="user-brand"><img class="site-logo user-logo" src="{{ asset('images/kuo-logo.png') }}" alt="Kolej Ungku Omar, Universiti Pendidikan Sultan Idris"></a>
    <nav class="user-desktop-nav">
        <a class="{{ request()->routeIs('user.dashboard') ? 'active' : '' }}" href="{{ route('user.dashboard') }}">Utama</a>
        <a class="{{ request()->routeIs('user.bookings.create') ? 'active' : '' }}" href="{{ route('user.bookings.create') }}">Buat Tempahan</a>
        <a class="{{ request()->routeIs('user.bookings') ? 'active' : '' }}" href="{{ route('user.bookings') }}">Tempahan Saya</a>
        <a class="{{ request()->routeIs('user.complaints') ? 'active' : '' }}" href="{{ route('user.complaints') }}">Aduan</a>
    </nav>
    <a class="user-account" href="{{ route('user.profile') }}"><span class="avatar small">{{ strtoupper(substr(session('profile.full_name', 'P'),0,1)) }}</span><span><strong>{{ session('profile.full_name', 'Pelajar') }}</strong><small>Pelajar</small></span></a>
</header>
<main class="user-main">
    @include('partials.alerts')
    @yield('content')
</main>
<nav class="user-bottom-nav">
    <a class="{{ request()->routeIs('user.dashboard') ? 'active' : '' }}" href="{{ route('user.dashboard') }}"><span>&#8962;</span>Utama</a>
    <a class="{{ request()->routeIs('user.bookings.create') ? 'active' : '' }}" href="{{ route('user.bookings.create') }}"><span>&plus;</span>Tempah</a>
    <a class="{{ request()->routeIs('user.bookings') ? 'active' : '' }}" href="{{ route('user.bookings') }}"><span>&#9635;</span>Tempahan</a>
    <a class="{{ request()->routeIs('user.complaints') ? 'active' : '' }}" href="{{ route('user.complaints') }}"><span>&#9671;</span>Aduan</a>
    <a class="{{ request()->routeIs('user.profile') ? 'active' : '' }}" href="{{ route('user.profile') }}"><span>&#9823;</span>Profil</a>
</nav>
</body>
</html>
