@extends('layouts.user')
@section('title','Checkout · DapurLink KUO')
@section('content')
<div class="user-page-title"><a href="{{ route('user.bookings') }}">←</a><div><p>SELESAI MENGGUNAKAN DAPUR</p><h1>Checkout tempahan</h1><span>Muat naik gambar dapur yang telah dibersihkan.</span></div></div>
<div class="checkout-grid">
<form method="POST" enctype="multipart/form-data" action="{{ route('user.checkout.store',$booking['id']) }}" class="user-form-card">@csrf
    <div class="checkout-summary"><span class="next-date"><strong>{{ \Illuminate\Support\Carbon::parse($booking['booking_date'])->format('d') }}</strong><small>{{ strtoupper(\Illuminate\Support\Carbon::parse($booking['booking_date'])->format('M')) }}</small></span><div><strong>{{ $booking['purpose'] }}</strong><small>{{ substr($booking['start_time'],0,5) }} – {{ substr($booking['end_time'],0,5) }}</small></div></div>
    <label class="photo-upload"><input type="file" name="photo" accept="image/jpeg,image/png,image/webp" required data-photo-input><span class="upload-icon">▧</span><strong>Ambil atau pilih gambar</strong><small>JPG, PNG atau WebP · maksimum 5 MB</small><img data-photo-preview alt="Pratonton gambar"></label>
    <label>Catatan (pilihan)<textarea name="note" rows="3" placeholder="Contoh: Semua peralatan telah dicuci.">{{ old('note') }}</textarea></label>
    <button class="user-primary-button">Sahkan checkout <span>✓</span></button>
</form>
<aside class="booking-help"><strong>Pastikan sebelum checkout</strong><ul><li>Sinki dan permukaan kerja bersih.</li><li>Peralatan dipulangkan ke tempat asal.</li><li>Sampah telah dibuang.</li><li>Gas dan suis elektrik ditutup.</li></ul></aside>
</div>
@endsection
