@extends('layouts.user')
@section('title','Buat Tempahan · DapurLink KUO')
@section('content')
<div class="user-page-title"><a href="{{ route('user.dashboard') }}">←</a><div><p>TEMPAHAN BAHARU</p><h1>Pilih waktu anda</h1><span>Setiap tempahan adalah selama satu jam.</span></div></div>
<div class="booking-form-grid">
<form class="user-form-card" method="POST" action="{{ route('user.bookings.store') }}">@csrf
    <div class="form-section-title"><span>1</span><div><strong>Tarikh tempahan</strong><small>Pilih tarikh dalam 90 hari akan datang</small></div></div>
    <input type="date" name="booking_date" value="{{ old('booking_date', now()->addDay()->toDateString()) }}" min="{{ now()->toDateString() }}" max="{{ now()->addDays(90)->toDateString() }}" required>
    <div class="form-section-title"><span>2</span><div><strong>Waktu mula</strong><small>Dapur dibuka dari 8 pagi hingga 10 malam</small></div></div>
    <div class="time-slots">
        @foreach(['08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00','21:00'] as $time)
        <label><input type="radio" name="start_time" value="{{ $time }}" {{ old('start_time')===$time ? 'checked':'' }} required><span>{{ \Illuminate\Support\Carbon::createFromFormat('H:i',$time)->format('g:i A') }}</span></label>
        @endforeach
    </div>
    <div class="form-section-title"><span>3</span><div><strong>Maklumat penggunaan</strong><small>Bantu admin memahami tujuan anda</small></div></div>
    <div class="form-two"><label>Bilangan orang<input type="number" name="pax" value="{{ old('pax',1) }}" min="1" max="30" required></label><label>Tujuan<select name="purpose" required><option value="">Pilih tujuan</option>@foreach(['Memasak','Program kolej','Aktiviti persatuan','Lain-lain'] as $purpose)<option {{ old('purpose')===$purpose?'selected':'' }}>{{ $purpose }}</option>@endforeach</select></label></div>
    <button class="user-primary-button">Hantar untuk kelulusan <span>→</span></button>
</form>
<aside class="booking-help"><strong>Sebelum menempah</strong><ul><li>Satu tempahan aktif sahaja sehari.</li><li>Hadir tepat pada waktu yang dipilih.</li><li>Bersihkan semua peralatan selepas digunakan.</li><li>Checkout dengan gambar bukti kebersihan.</li></ul></aside>
</div>
@endsection
