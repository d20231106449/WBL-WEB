@extends('layouts.user')
@section('title','Aduan · DapurLink KUO')
@section('content')
<div class="user-page-title"><div><p>BANTUAN</p><h1>Aduan dan maklum balas</h1><span>Maklumkan masalah berkaitan dapur atau tempahan.</span></div></div>
<div class="complaint-page-grid">
<form method="POST" action="{{ route('user.complaints.store') }}" class="user-form-card complaint-form">@csrf
    <h2>Hantar aduan baharu</h2><p>Pentadbir akan membaca dan memberikan maklum balas melalui portal ini.</p>
    <label>Tempahan berkaitan (pilihan)<select class="@error('booking_id') is-invalid @enderror" name="booking_id"><option value="">Aduan umum</option>@foreach($bookings as $booking)<option value="{{ $booking['id'] }}" {{ old('booking_id') === $booking['id'] ? 'selected' : '' }}>{{ $booking['booking_date'] }} · {{ substr($booking['start_time'],0,5) }}</option>@endforeach</select>@error('booking_id')<span class="field-error">{{ $message }}</span>@enderror</label>
    <label>Aduan anda<textarea class="@error('complaint_text') is-invalid @enderror" name="complaint_text" rows="6" maxlength="1000" required placeholder="Terangkan masalah dengan jelas…">{{ old('complaint_text') }}</textarea>@error('complaint_text')<span class="field-error">{{ $message }}</span>@enderror</label>
    <button class="user-primary-button" type="submit">Hantar aduan <span>→</span></button>
</form>
<section class="complaint-history"><div class="user-section-head"><div><p>SEJARAH</p><h2>Aduan anda</h2></div></div>
@forelse($complaints as $complaint)<article class="user-complaint-card"><div>@include('partials.status',['status'=>$complaint['status']])<time>{{ \Illuminate\Support\Carbon::parse($complaint['created_at'])->translatedFormat('d M Y') }}</time></div><p>“{{ $complaint['complaint_text'] }}”</p>@if(!empty($complaint['admin_reply']))<aside><strong>Jawapan pentadbir</strong><p>{{ $complaint['admin_reply'] }}</p></aside>@endif</article>@empty<div class="user-empty compact"><span>◇</span><p>Belum ada aduan dihantar.</p></div>@endforelse</section>
</div>
@endsection
