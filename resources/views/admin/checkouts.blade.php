@extends('layouts.admin')
@section('title', 'Bukti Penggunaan')
@section('page-title', 'Bukti Selesai Penggunaan')
@section('content')
<div class="page-heading"><div><p class="eyebrow">SELEPAS PENGGUNAAN</p><h2>Rekod kebersihan dapur</h2><p>Gambar yang dihantar pelajar selepas selesai menggunakan dapur.</p></div></div>
<div class="evidence-grid">
@forelse($checkouts as $checkout)
    @php($booking = $bookingsById->get($checkout['booking_id'] ?? ''))
    <article class="evidence-card"><a href="{{ $checkout['photo_url'] }}" target="_blank" rel="noopener"><img src="{{ $checkout['photo_url'] }}" alt="Bukti selesai penggunaan dapur" loading="lazy"><span>Lihat saiz penuh ↗</span></a><div><div class="evidence-date"><strong>{{ isset($checkout['created_at']) ? \Illuminate\Support\Carbon::parse($checkout['created_at'])->translatedFormat('d M Y, g:i A') : '—' }}</strong>@include('partials.status', ['status'=>'completed'])</div><p>{{ $checkout['note'] ?: 'Tiada catatan tambahan.' }}</p><small>Tempahan #{{ substr($checkout['booking_id'] ?? '', 0, 8) }} · {{ $booking['booking_date'] ?? '' }}</small></div></article>
@empty<div class="panel empty-state"><span>▧</span><strong>Belum ada bukti penggunaan</strong><p>Gambar pelajar akan dipaparkan di sini.</p></div>@endforelse
</div>
@if($error)<div class="panel empty-state">{{ $error }}</div>@endif
@endsection
