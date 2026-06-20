@extends('layouts.user')
@section('title','Aduan · DapurLink KUO')
@section('content')
<div class="user-page-title"><div><p>BANTUAN</p><h1>Aduan & maklum balas</h1><span>Kongsikan masalah berkaitan dapur atau tempahan.</span></div></div>
<div class="complaint-page-grid">
<form method="POST" action="{{ route('user.complaints.store') }}" class="user-form-card complaint-form">@csrf
    <h2>Hantar aduan baharu</h2><p>Admin akan membaca dan memberi maklum balas melalui portal ini.</p>
    <label>Tempahan berkaitan (pilihan)<select name="booking_id"><option value="">Aduan umum</option>@foreach($bookings as $booking)<option value="{{ $booking['id'] }}">{{ $booking['booking_date'] }} · {{ substr($booking['start_time'],0,5) }}</option>@endforeach</select></label>
    <label>Aduan anda<textarea name="complaint_text" rows="6" maxlength="1000" required placeholder="Terangkan masalah dengan jelas…">{{ old('complaint_text') }}</textarea></label>
    <button class="user-primary-button">Hantar aduan <span>→</span></button>
</form>
<section class="complaint-history"><div class="user-section-head"><div><p>SEJARAH</p><h2>Aduan anda</h2></div></div>
@forelse($complaints as $complaint)<article class="user-complaint-card"><div>@include('partials.status',['status'=>$complaint['status']])<time>{{ \Illuminate\Support\Carbon::parse($complaint['created_at'])->format('d M Y') }}</time></div><p>“{{ $complaint['complaint_text'] }}”</p>@if(!empty($complaint['admin_reply']))<aside><strong>Jawapan admin</strong><p>{{ $complaint['admin_reply'] }}</p></aside>@endif</article>@empty<div class="user-empty compact"><span>◇</span><p>Belum ada aduan dihantar.</p></div>@endforelse</section>
</div>
@endsection
