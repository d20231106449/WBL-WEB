@extends('layouts.admin')
@section('title', 'Pengguna')
@section('page-title', 'Pengurusan Pengguna')
@section('content')
<div class="page-heading"><div><p class="eyebrow">AKSES SISTEM</p><h2>Pengguna berdaftar</h2><p>Semak akaun pelajar dan tetapkan akses pentadbir.</p></div><span class="count-pill">{{ count($profiles) }} akaun</span></div>
<section class="panel table-panel">
@if($error)<div class="empty-state">{{ $error }}</div>@elseif(empty($profiles))<div class="empty-state">Tiada pengguna dijumpai.</div>@else
<div class="responsive-table"><table><thead><tr><th>Nama</th><th>Emel</th><th>Tarikh daftar</th><th>Peranan</th><th>Tindakan</th></tr></thead><tbody>
@foreach($profiles as $profile)<tr><td><div class="person-cell"><span class="avatar small">{{ strtoupper(substr($profile['full_name'] ?? 'P',0,1)) }}</span><strong>{{ $profile['full_name'] ?? 'Tanpa nama' }}</strong></div></td><td>{{ $profile['email'] ?? '—' }}</td><td>{{ isset($profile['created_at']) ? \Illuminate\Support\Carbon::parse($profile['created_at'])->format('d M Y') : '—' }}</td><td><span class="role-badge {{ ($profile['role'] ?? 'user') === 'admin' ? 'admin' : '' }}">{{ ($profile['role'] ?? 'user') === 'admin' ? 'Pentadbir' : 'Pelajar' }}</span></td><td><form method="POST" action="{{ route('admin.users.role', $profile['id']) }}">@csrf @method('PATCH')<input type="hidden" name="role" value="{{ ($profile['role'] ?? 'user') === 'admin' ? 'user' : 'admin' }}"><button class="text-button" {{ $profile['id'] === session('profile.id') ? 'disabled' : '' }}>{{ ($profile['role'] ?? 'user') === 'admin' ? 'Jadikan pelajar' : 'Jadikan admin' }}</button></form></td></tr>@endforeach
</tbody></table></div>@endif
</section>
@endsection
