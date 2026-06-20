<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'DapurLink KUO')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="user-body">
<header class="user-header">
    <a href="{{ route('user.dashboard') }}" class="user-brand"><img class="site-logo user-logo" src="{{ asset('images/kuo-logo.png') }}" alt="Kolej Ungku Omar, Universiti Pendidikan Sultan Idris"></a>
    <nav class="user-desktop-nav">
        <a class="{{ request()->routeIs('user.dashboard') ? 'active' : '' }}" href="{{ route('user.dashboard') }}">Utama</a>
        <a class="{{ request()->routeIs('user.bookings*') ? 'active' : '' }}" href="{{ route('user.bookings') }}">Tempahan Saya</a>
        <a class="{{ request()->routeIs('user.complaints') ? 'active' : '' }}" href="{{ route('user.complaints') }}">Aduan</a>
    </nav>
    <a class="user-account" href="{{ route('user.profile') }}"><span class="avatar small">{{ strtoupper(substr(session('profile.full_name', 'P'),0,1)) }}</span><span><strong>{{ session('profile.full_name', 'Pelajar') }}</strong><small>Pelajar</small></span></a>
</header>
<main class="user-main">
    @include('partials.alerts')
    @yield('content')
</main>
<nav class="user-bottom-nav">
    <a class="{{ request()->routeIs('user.dashboard') ? 'active' : '' }}" href="{{ route('user.dashboard') }}"><span>⌂</span>Utama</a>
    <a class="{{ request()->routeIs('user.bookings.create') ? 'active' : '' }}" href="{{ route('user.bookings.create') }}"><span>＋</span>Tempah</a>
    <a class="{{ request()->routeIs('user.bookings') ? 'active' : '' }}" href="{{ route('user.bookings') }}"><span>▣</span>Tempahan</a>
    <a class="{{ request()->routeIs('user.profile') ? 'active' : '' }}" href="{{ route('user.profile') }}"><span>♙</span>Profil</a>
</nav>
</body>
</html>
