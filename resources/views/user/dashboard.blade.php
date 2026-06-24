@extends('layouts.user')
@section('title', 'Utama · DapurLink KUO')
@section('content')
<section class="user-hero">
    <div><p>SELAMAT DATANG</p><h1>Hai, {{ explode(' ', session('profile.full_name', 'Pelajar'))[0] }} 👋</h1><span>Tempah Dapur Siswa Madani KUO dengan mudah.</span><a href="{{ route('user.bookings.create') }}">Buat tempahan <b>→</b></a></div>
    <div class="kitchen-mark"><img src="{{ asset('images/kuo-logo.png') }}" alt="Logo Kolej Ungku Omar"><i></i></div>
</section>
<section class="user-stat-grid">
    <article><span class="user-stat-icon blue">◷</span><div><small>WAKTU OPERASI</small><strong>8.00 pagi – 10.00 malam</strong></div></article>
    <article><span class="user-stat-icon purple">⌛</span><div><small>MAKSIMUM TEMPAHAN</small><strong>1 jam</strong></div></article>
    <article><span class="user-stat-icon green">✓</span><div><small>STATUS SISTEM</small><strong>Dalam talian</strong></div></article>
</section>
<div class="user-dashboard-grid">
<section class="user-section">
    <div class="user-section-head"><div><p>TEMPAHAN AKAN DATANG</p><h2>Jadual terdekat</h2></div><a href="{{ route('user.bookings') }}">Lihat semua →</a></div>
    @if($nextBooking)
    <article class="next-booking">
        <div class="next-date"><strong>{{ \Illuminate\Support\Carbon::parse($nextBooking['booking_date'])->format('d') }}</strong><span>{{ strtoupper(\Illuminate\Support\Carbon::parse($nextBooking['booking_date'])->translatedFormat('M')) }}</span></div>
        <div><strong>{{ $nextBooking['purpose'] ?: 'Penggunaan dapur' }}</strong><span>{{ substr($nextBooking['start_time'],0,5) }} – {{ substr($nextBooking['end_time'],0,5) }} · {{ $nextBooking['pax'] }} orang</span></div>
        @include('partials.status',['status'=>$nextBooking['status']])
    </article>
    @else<div class="user-empty"><span>▣</span><strong>Tiada tempahan akan datang</strong><p>Sila pilih waktu yang sesuai untuk menggunakan dapur.</p><a href="{{ route('user.bookings.create') }}">Tempah sekarang</a></div>@endif
</section>
<aside class="user-section">
    <div class="user-section-head"><div><p>STATUS</p><h2>Aktiviti anda</h2></div></div>
    <div class="activity-count"><span><b>{{ $pendingCount }}</b> Menunggu</span><span><b>{{ $approvedCount }}</b> Diluluskan</span></div>
    <a class="user-quick-link" href="{{ route('user.complaints') }}"><span>◇</span><div><strong>Aduan dan Maklum Balas</strong><small>Maklumkan kepada kami jika terdapat masalah</small></div><b>→</b></a>
</aside>
</div>
<section class="rules-card"><span>i</span><div><strong>Peringatan dapur</strong><p>Pastikan dapur dibersihkan selepas digunakan. Muat naik gambar sebagai bukti selesai penggunaan.</p></div></section>
@endsection
