@extends('layouts.admin')
@section('title', 'Bukti Checkout')
@section('page-title', 'Bukti Checkout')
@section('content')
<div class="page-heading"><div><p class="eyebrow">SELEPAS PENGGUNAAN</p><h2>Rekod kebersihan dapur</h2><p>Gambar yang dihantar pelajar selepas selesai menggunakan dapur.</p></div></div>
<div class="evidence-grid">
@forelse($checkouts as $checkout)
    @php($booking = $bookingsById->get($checkout['booking_id'] ?? ''))
    <article class="evidence-card"><a href="{{ $checkout['photo_url'] }}" target="_blank" rel="noopener"><img src="{{ $checkout['photo_url'] }}" alt="Bukti checkout dapur" loading="lazy"><span>Lihat penuh ↗</span></a><div><div class="evidence-date"><strong>{{ isset($checkout['created_at']) ? \Illuminate\Support\Carbon::parse($checkout['created_at'])->format('d M Y, g:i A') : '—' }}</strong>@include('partials.status', ['status'=>'completed'])</div><p>{{ $checkout['note'] ?: 'Tiada catatan tambahan.' }}</p><small>Tempahan #{{ substr($checkout['booking_id'] ?? '', 0, 8) }} · {{ $booking['booking_date'] ?? '' }}</small></div></article>
@empty<div class="panel empty-state"><span>▧</span><strong>Belum ada bukti checkout</strong><p>Gambar pelajar akan muncul di sini.</p></div>@endforelse
</div>
@if($error)<div class="panel empty-state">{{ $error }}</div>@endif
@endsection
