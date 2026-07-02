@extends('layouts.user')
@section('title','Buat tempahan - DapurLink KUO')
@section('content')
<div class="user-page-title"><a href="{{ route('user.dashboard') }}">&larr;</a><div><p>TEMPAHAN BAHARU</p><h1>Pilih waktu anda</h1><span>Setiap tempahan berlangsung selama satu jam.</span></div></div>
<div class="booking-form-grid">
<form class="user-form-card" method="POST" action="{{ route('user.bookings.store', [], false) }}" data-booking-form data-server-now="{{ now()->toIso8601String() }}" data-booked-slots-url="{{ route('user.bookings.slots', [], false) }}">@csrf
    <div class="form-section-title"><span>1</span><div><strong>Tarikh tempahan</strong><small>Pilih tarikh dalam 90 hari akan datang</small></div></div>
    <label>Tarikh pilihan<input class="@error('booking_date') is-invalid @enderror" type="date" name="booking_date" value="{{ old('booking_date', now()->toDateString()) }}" min="{{ now()->toDateString() }}" max="{{ now()->addDays(90)->toDateString() }}" data-booking-date required>@error('booking_date')<span class="field-error">{{ $message }}</span>@enderror</label>
    <div class="form-section-title"><span>2</span><div><strong>Waktu mula</strong><small>Dapur dibuka dari 8 pagi hingga 10 malam. Waktu yang telah berlalu dinyahaktifkan.</small></div></div>
    <div class="time-slots">
        @foreach(['08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00','21:00'] as $time)
        <label><input type="radio" name="start_time" value="{{ $time }}" data-booking-time {{ old('start_time')===$time ? 'checked':'' }} required><span data-slot-label>{{ \Illuminate\Support\Carbon::createFromFormat('H:i',$time)->format('g:i A') }}</span></label>
        @endforeach
    </div>
    @error('start_time')<span class="field-error">{{ $message }}</span>@enderror
    <div class="form-section-title"><span>3</span><div><strong>Maklumat penggunaan</strong><small>Bantu pentadbir memahami tujuan tempahan anda.</small></div></div>
    <div class="form-two"><label>Bilangan pengguna<input class="@error('pax') is-invalid @enderror" type="number" name="pax" value="{{ old('pax',1) }}" min="1" max="12" required><small>Maksimum 12 orang sahaja untuk setiap tempahan.</small>@error('pax')<span class="field-error">{{ $message }}</span>@enderror</label><label>Tujuan<select class="@error('purpose') is-invalid @enderror" name="purpose" required><option value="">Pilih tujuan</option>@foreach(['Memasak','Program kolej','Aktiviti persatuan','Lain-lain'] as $purpose)<option {{ old('purpose')===$purpose?'selected':'' }}>{{ $purpose }}</option>@endforeach</select>@error('purpose')<span class="field-error">{{ $message }}</span>@enderror</label></div>
    <button class="user-primary-button" type="submit">Hantar kepada pentadbir untuk kelulusan <span>&rarr;</span></button>
</form>
<aside class="booking-help"><strong>Sebelum menempah</strong><ul><li>Hanya satu tempahan aktif dibenarkan dalam sehari.</li><li>Hadir tepat pada waktu yang dipilih.</li><li>Bersihkan semua peralatan selepas digunakan.</li><li>Sahkan selesai penggunaan dengan gambar bukti kebersihan.</li></ul></aside>
</div>
@endsection
