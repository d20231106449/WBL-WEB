@extends('layouts.user')
@section('title','Pengesahan selesai penggunaan - DapurLink KUO')
@section('content')
<div class="user-page-title"><a href="{{ route('user.bookings') }}">&larr;</a><div><p>SELESAI MENGGUNAKAN DAPUR</p><h1>Sahkan selesai penggunaan</h1><span>Muat naik gambar dapur yang telah dibersihkan.</span></div></div>
<div class="checkout-grid">
<form method="POST" enctype="multipart/form-data" action="{{ route('user.checkout.store',$booking['id']) }}" class="user-form-card">@csrf
    <div class="checkout-summary"><span class="next-date"><strong>{{ \Illuminate\Support\Carbon::parse($booking['booking_date'])->format('d') }}</strong><small>{{ strtoupper(\Illuminate\Support\Carbon::parse($booking['booking_date'])->translatedFormat('M')) }}</small></span><div><strong>{{ $booking['purpose'] }}</strong><small>{{ substr($booking['start_time'],0,5) }} &ndash; {{ substr($booking['end_time'],0,5) }}</small></div></div>
    <label class="photo-upload @error('photo') is-invalid @enderror"><input type="file" name="photo" accept="image/jpeg,image/png,image/webp" required data-photo-input><span class="upload-icon">&#9638;</span><strong>Ambil atau pilih gambar</strong><small>JPG, PNG atau WebP &middot; maksimum 5 MB</small><img data-photo-preview alt="Pratonton gambar"></label>
    @error('photo')<span class="field-error">{{ $message }}</span>@enderror
    <label>Catatan (pilihan)<textarea class="@error('note') is-invalid @enderror" name="note" rows="3" placeholder="Contoh: Semua peralatan telah dicuci.">{{ old('note') }}</textarea>@error('note')<span class="field-error">{{ $message }}</span>@enderror</label>
    <button class="user-primary-button" type="submit">Sahkan selesai penggunaan <span>&check;</span></button>
</form>
<aside class="booking-help"><strong>Pastikan sebelum membuat pengesahan</strong><ul><li>Sinki dan permukaan kerja telah dibersihkan.</li><li>Peralatan telah dipulangkan ke tempat asal.</li><li>Sampah telah dibuang.</li><li>Gas dan suis elektrik telah ditutup.</li></ul></aside>
</div>
@endsection
