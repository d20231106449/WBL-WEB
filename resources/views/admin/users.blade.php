@extends('layouts.admin')
@section('title', 'Pengguna')
@section('page-title', 'Pengurusan Pengguna')
@section('content')
<div class="page-heading"><div><p class="eyebrow">AKSES SISTEM</p><h2>Pengguna berdaftar</h2><p>Semak akaun pelajar dan tetapkan akses pentadbir.</p></div><span class="count-pill">{{ count($profiles) }} akaun</span></div>
<section class="panel table-panel">
@if($error)<div class="empty-state">{{ $error }}</div>@elseif(empty($profiles))<div class="empty-state">Tiada pengguna dijumpai.</div>@else
<div class="responsive-table"><table><thead><tr><th>Nama</th><th>E-mel</th><th>Tarikh pendaftaran</th><th>Peranan</th><th>Tindakan</th></tr></thead><tbody>
@foreach($profiles as $profile)<tr><td data-label="Nama"><div class="person-cell"><span class="avatar small">{{ strtoupper(substr($profile['full_name'] ?? 'P',0,1)) }}</span><strong>{{ $profile['full_name'] ?? 'Tanpa nama' }}</strong></div></td><td data-label="E-mel">{{ $profile['email'] ?? '-' }}</td><td data-label="Tarikh pendaftaran">{{ isset($profile['created_at']) ? \Illuminate\Support\Carbon::parse($profile['created_at'])->translatedFormat('d M Y') : '-' }}</td><td data-label="Peranan"><span class="role-badge {{ ($profile['role'] ?? 'user') === 'admin' ? 'admin' : '' }}">{{ ($profile['role'] ?? 'user') === 'admin' ? 'Pentadbir' : 'Pelajar' }}</span></td><td data-label="Tindakan"><form method="POST" action="{{ route('admin.users.role', $profile['id'], false) }}">@csrf @method('PATCH')<input type="hidden" name="role" value="{{ ($profile['role'] ?? 'user') === 'admin' ? 'user' : 'admin' }}"><button class="action-button {{ ($profile['role'] ?? 'user') === 'admin' ? 'danger' : 'success' }}" {{ $profile['id'] === session('profile.id') ? 'disabled' : '' }}>{{ ($profile['role'] ?? 'user') === 'admin' ? 'Jadikan pelajar' : 'Jadikan pentadbir' }}</button></form></td></tr>@endforeach
</tbody></table></div>@endif
</section>
@endsection
