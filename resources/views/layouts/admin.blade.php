<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#b91c1c">
    <title>@yield('title', 'Pentadbir') - DapurLink KUO</title>
    @include('partials.assets')
</head>
<body class="admin-body">
<div class="admin-shell">
    <aside class="sidebar" id="sidebar">
        <button class="sidebar-close" type="button" data-sidebar-close aria-label="Tutup menu">&times;</button>
        <a class="brand" href="{{ route('admin.dashboard') }}">
            <img class="site-logo admin-logo" src="{{ asset('images/kuo-logo.png') }}" alt="Kolej Ungku Omar, Universiti Pendidikan Sultan Idris">
        </a>

        <nav class="nav-list" aria-label="Navigasi utama">
            <p class="nav-label">RUANG KERJA</p>
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><span>&#8962;</span> Status</a>
            <a href="{{ route('admin.bookings') }}" class="nav-item {{ request()->routeIs('admin.bookings*') ? 'active' : '' }}"><span>&#9635;</span> Tempahan</a>
            <a href="{{ route('admin.complaints') }}" class="nav-item {{ request()->routeIs('admin.complaints*') ? 'active' : '' }}"><span>&#9671;</span> Aduan</a>
            <a href="{{ route('admin.checkouts') }}" class="nav-item {{ request()->routeIs('admin.checkouts') ? 'active' : '' }}"><span>&check;</span> Bukti penggunaan</a>
            <p class="nav-label">PENGURUSAN</p>
            <a href="{{ route('admin.users') }}" class="nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}"><span>&#9823;</span> Pengguna</a>
        </nav>

        <div class="sidebar-user">
            <span class="avatar">{{ strtoupper(substr(session('profile.full_name', 'A'), 0, 1)) }}</span>
            <span class="user-copy"><strong>{{ session('profile.full_name', 'Pentadbir') }}</strong><small>{{ session('profile.email') }}</small></span>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="icon-button" title="Log keluar">&nearr;</button></form>
        </div>
    </aside>
    <button class="sidebar-backdrop" type="button" data-sidebar-close aria-label="Tutup menu navigasi" tabindex="-1"></button>

    <main class="main-content">
        <header class="topbar">
            <button class="menu-button" type="button" data-sidebar-toggle aria-label="Buka menu" aria-controls="sidebar" aria-expanded="false">&#9776;</button>
            <div><p class="eyebrow">DAPUR SISWA MADANI KUO</p><h1>@yield('page-title', 'Papan pemuka')</h1></div>
            <div class="topbar-meta"><span class="live-dot"></span><span>Sistem dalam talian</span><time>{{ now()->translatedFormat('d M Y') }}</time></div>
        </header>

        <div class="page-content">
            @include('partials.alerts')
            @yield('content')
        </div>
    </main>
</div>
</body>
</html>
