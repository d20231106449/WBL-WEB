@extends('layouts.admin')
@section('title', 'Aduan')
@section('page-title', 'Aduan dan maklum balas')
@section('content')
<div class="page-heading"><div><p class="eyebrow">SUARA PELAJAR</p><h2>Pusat aduan</h2><p>Balas isu yang dihantar melalui aplikasi DapurLink.</p></div></div>
<div class="filter-tabs"><a class="{{ $status === '' ? 'active' : '' }}" href="{{ route('admin.complaints') }}">Semua</a><a class="{{ $status === 'open' ? 'active' : '' }}" href="{{ route('admin.complaints', ['status'=>'open']) }}">Terbuka</a><a class="{{ $status === 'resolved' ? 'active' : '' }}" href="{{ route('admin.complaints', ['status'=>'resolved']) }}">Selesai</a></div>
<div class="complaint-grid">
@forelse($complaints as $complaint)
    @php($person = $profilesById->get($complaint['user_id'] ?? ''))
    <article class="complaint-card">
        <div class="complaint-top"><div class="person-cell"><span class="avatar small">{{ strtoupper(substr($person['full_name'] ?? 'P',0,1)) }}</span><div><strong>{{ $person['full_name'] ?? 'Pelajar' }}</strong><small>{{ isset($complaint['created_at']) ? \Illuminate\Support\Carbon::parse($complaint['created_at'])->diffForHumans() : '' }}</small></div></div>@include('partials.status', ['status'=>$complaint['status'] ?? 'open'])</div>
        <p class="complaint-text">&ldquo;{{ $complaint['complaint_text'] }}&rdquo;</p>
        @if(!empty($complaint['admin_reply']))<div class="admin-reply"><span>Jawapan pentadbir</span><p>{{ $complaint['admin_reply'] }}</p></div>@endif
        @if(($complaint['status'] ?? '') === 'open')<button class="action-button full" data-modal-open="complaint-{{ $complaint['id'] }}">Balas aduan</button>
        <dialog class="action-modal" id="complaint-{{ $complaint['id'] }}"><form method="POST" action="{{ route('admin.complaints.resolve', $complaint['id'], false) }}">@csrf @method('PATCH')<div class="modal-head"><div><p class="eyebrow">MAKLUM BALAS</p><h3>Jawab aduan pelajar</h3></div><button type="button" data-modal-close>&times;</button></div><div class="modal-summary"><p>{{ $complaint['complaint_text'] }}</p></div><label>Jawapan pentadbir<textarea name="admin_reply" rows="5" required placeholder="Tulis jawapan yang jelas dan membantu..."></textarea></label><div class="modal-actions"><button type="button" class="button ghost" data-modal-close>Batal</button><button class="button primary">Hantar dan selesaikan</button></div></form></dialog>@endif
    </article>
@empty<div class="panel empty-state"><span>&#9671;</span><strong>Tiada aduan</strong><p>Semua tenang buat masa ini.</p></div>@endforelse
</div>
@if($error)<div class="panel empty-state">{{ $error }}</div>@endif
@endsection
