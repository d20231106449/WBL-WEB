@extends('layouts.user')
@section('title','Tempahan Saya · DapurLink KUO')
@section('content')
<div class="user-page-title between"><div><p>REKOD ANDA</p><h1>Tempahan saya</h1><span>Semak status dan urus penggunaan dapur.</span></div><a class="compact-primary" href="{{ route('user.bookings.create') }}">＋ Tempahan baharu</a></div>
<div class="mobile-booking-list">
@forelse($bookings as $booking)
<article class="my-booking-card">
    <div class="my-booking-top"><div class="next-date"><strong>{{ \Illuminate\Support\Carbon::parse($booking['booking_date'])->format('d') }}</strong><span>{{ strtoupper(\Illuminate\Support\Carbon::parse($booking['booking_date'])->translatedFormat('M')) }}</span></div><div><strong>{{ $booking['purpose'] ?: 'Penggunaan dapur' }}</strong><span>Dibuat {{ \Illuminate\Support\Carbon::parse($booking['created_at'])->diffForHumans() }}</span></div>@include('partials.status',['status'=>$booking['status']])</div>
    <div class="booking-detail-grid"><span><small>WAKTU</small><strong>{{ substr($booking['start_time'],0,5) }} – {{ substr($booking['end_time'],0,5) }}</strong></span><span><small>BILANGAN PENGGUNA</small><strong>{{ $booking['pax'] }} orang</strong></span><span><small>ID TEMPAHAN</small><strong>#{{ strtoupper(substr($booking['id'],0,8)) }}</strong></span></div>
    @if(!empty($booking['admin_note']))<div class="booking-note"><strong>Catatan pentadbir</strong><p>{{ $booking['admin_note'] }}</p></div>@endif
    @if(in_array($booking['status'], ['pending', 'approved'], true))<form method="POST" action="{{ route('user.bookings.cancel',$booking['id']) }}" onsubmit="return confirm('Batalkan tempahan ini?')">@csrf @method('PATCH')<button class="cancel-link">Batalkan tempahan</button></form>@endif
    @if($booking['status']==='approved')<a class="checkout-link" href="{{ route('user.checkout',$booking['id']) }}">Sahkan selesai penggunaan <span>→</span></a>@endif
</article>
@empty<div class="user-section user-empty"><span>▣</span><strong>Belum ada tempahan</strong><p>Tempahan pertama anda akan muncul di sini.</p><a href="{{ route('user.bookings.create') }}">Buat tempahan</a></div>@endforelse
</div>
@if($error)<div class="user-section user-empty">{{ $error }}</div>@endif
@endsection
