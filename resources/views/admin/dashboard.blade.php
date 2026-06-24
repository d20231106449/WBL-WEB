@extends('layouts.admin')
@section('title', 'Status')
@section('page-title', 'Status Hari Ini')
@section('content')
<section class="hero-panel">
    <div><span class="hero-kicker">SELAMAT DATANG, {{ strtoupper(explode(' ', session('profile.full_name', 'PENTADBIR'))[0]) }}</span><h2>Dapur dalam kawalan.<br><em>Keadaan hari ini baik.</em></h2><p>Semak permohonan baharu dan perkara yang memerlukan perhatian anda.</p></div>
    <div class="hero-mark"><img src="{{ asset('images/kuo-logo.png') }}" alt="Logo Kolej Ungku Omar"><i></i></div>
</section>

<section class="stats-grid">
    <article class="stat-card blue"><span class="stat-icon">◷</span><div><small>MENUNGGU KELULUSAN</small><strong>{{ $stats['pending'] }}</strong><a href="{{ route('admin.bookings', ['status' => 'pending']) }}">Semak sekarang →</a></div></article>
    <article class="stat-card cyan"><span class="stat-icon">▣</span><div><small>TEMPAHAN HARI INI</small><strong>{{ $stats['today'] }}</strong><span>Untuk {{ now()->format('d M Y') }}</span></div></article>
    <article class="stat-card orange"><span class="stat-icon">◇</span><div><small>ADUAN TERBUKA</small><strong>{{ $stats['openComplaints'] }}</strong><a href="{{ route('admin.complaints', ['status' => 'open']) }}">Beri maklum balas →</a></div></article>
    <article class="stat-card green"><span class="stat-icon">♙</span><div><small>PELAJAR BERDAFTAR</small><strong>{{ $stats['users'] }}</strong><a href="{{ route('admin.users') }}">Lihat pengguna →</a></div></article>
</section>

<div class="dashboard-grid">
    <section class="panel">
        <div class="panel-header"><div><p class="eyebrow">PERLU TINDAKAN</p><h3>Tempahan terkini</h3></div><a href="{{ route('admin.bookings') }}">Lihat semua →</a></div>
        @if($bookingError)<div class="empty-state">{{ $bookingError }}</div>
        @elseif(collect($bookings)->where('status', 'pending')->isEmpty())<div class="empty-state"><span>✓</span><strong>Semua sudah selesai</strong><p>Tiada tempahan menunggu kelulusan.</p></div>
        @else
            <div class="booking-list">
            @foreach(collect($bookings)->where('status', 'pending')->take(5) as $booking)
                @php($person = $profilesById->get($booking['user_id'] ?? ''))
                <article class="booking-row">
                    <div class="date-block"><strong>{{ \Illuminate\Support\Carbon::parse($booking['booking_date'])->format('d') }}</strong><span>{{ strtoupper(\Illuminate\Support\Carbon::parse($booking['booking_date'])->translatedFormat('M')) }}</span></div>
                    <div class="booking-info"><strong>{{ $person['full_name'] ?? $booking['user_name'] ?? 'Pelajar' }}</strong><span>{{ substr($booking['start_time'] ?? '', 0, 5) }} – {{ substr($booking['end_time'] ?? '', 0, 5) }} · {{ $booking['pax'] ?? 1 }} orang</span></div>
                    @include('partials.status', ['status' => $booking['status']])
                    <a class="round-link" href="{{ route('admin.bookings', ['status' => 'pending']) }}">→</a>
                </article>
            @endforeach
            </div>
        @endif
    </section>
    <aside class="panel quick-panel">
        <div class="panel-header"><div><p class="eyebrow">PINTASAN</p><h3>Tindakan pantas</h3></div></div>
        <a href="{{ route('admin.bookings', ['status' => 'pending']) }}"><span class="quick-icon blue">✓</span><div><strong>Lulus tempahan</strong><small>Semak permohonan pelajar</small></div><b>→</b></a>
        <a href="{{ route('admin.complaints', ['status' => 'open']) }}"><span class="quick-icon orange">◇</span><div><strong>Jawab aduan</strong><small>Urus aduan yang belum diselesaikan</small></div><b>→</b></a>
        <a href="{{ route('admin.checkouts') }}"><span class="quick-icon green">▧</span><div><strong>Semak bukti</strong><small>Lihat gambar selepas penggunaan</small></div><b>→</b></a>
    </aside>
</div>
@endsection
