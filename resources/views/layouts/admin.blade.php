<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') · DapurLink KUO</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-body">
<div class="admin-shell">
    <aside class="sidebar" id="sidebar">
        <a class="brand" href="{{ route('admin.dashboard') }}">
            <img class="site-logo admin-logo" src="{{ asset('images/kuo-logo.png') }}" alt="Kolej Ungku Omar, Universiti Pendidikan Sultan Idris">
        </a>

        <nav class="nav-list" aria-label="Navigasi utama">
            <p class="nav-label">RUANG KERJA</p>
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><span>⌂</span> Ringkasan</a>
            <a href="{{ route('admin.bookings') }}" class="nav-item {{ request()->routeIs('admin.bookings*') ? 'active' : '' }}"><span>▣</span> Tempahan</a>
            <a href="{{ route('admin.complaints') }}" class="nav-item {{ request()->routeIs('admin.complaints*') ? 'active' : '' }}"><span>◇</span> Aduan</a>
            <a href="{{ route('admin.checkouts') }}" class="nav-item {{ request()->routeIs('admin.checkouts') ? 'active' : '' }}"><span>✓</span> Bukti Checkout</a>
            <p class="nav-label">PENGURUSAN</p>
            <a href="{{ route('admin.users') }}" class="nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}"><span>♙</span> Pengguna</a>
        </nav>

        <div class="sidebar-user">
            <span class="avatar">{{ strtoupper(substr(session('profile.full_name', 'A'), 0, 1)) }}</span>
            <span class="user-copy"><strong>{{ session('profile.full_name', 'Admin') }}</strong><small>{{ session('profile.email') }}</small></span>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="icon-button" title="Log keluar">↗</button></form>
        </div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <button class="menu-button" type="button" data-sidebar-toggle aria-label="Buka menu">☰</button>
            <div><p class="eyebrow">DAPUR SISWA MADANI KUO</p><h1>@yield('page-title', 'Dashboard')</h1></div>
            <div class="topbar-meta"><span class="live-dot"></span><span>Sistem Online</span><time>{{ now()->format('d M Y') }}</time></div>
        </header>

        <div class="page-content">
            @include('partials.alerts')
            @yield('content')
        </div>
    </main>
</div>
</body>
</html>
