@extends('layouts.user')
@section('title','Profil · DapurLink KUO')
@section('content')
<div class="profile-wrap">
<section class="profile-hero"><span class="profile-avatar">{{ strtoupper(substr(session('profile.full_name','P'),0,1)) }}</span><h1>{{ session('profile.full_name','Pelajar') }}</h1><p>{{ session('profile.email') }}</p><span class="role-badge">Pelajar KUO</span></section>
<section class="profile-card"><h2>Maklumat akaun</h2><div><span>Nama penuh</span><strong>{{ session('profile.full_name','—') }}</strong></div><div><span>Alamat emel</span><strong>{{ session('profile.email','—') }}</strong></div><div><span>Peranan</span><strong>{{ session('profile.role','user') === 'admin' ? 'Pentadbir' : 'Pelajar' }}</strong></div></section>
<section class="profile-card"><h2>Akses pantas</h2><a href="{{ route('user.complaints') }}">◇ Aduan & Maklum Balas <b>→</b></a>@if(session('profile.role')==='admin')<a href="{{ route('admin.dashboard') }}">▦ Portal Pentadbir <b>→</b></a>@endif</section>
<form method="POST" action="{{ route('logout') }}">@csrf<button class="logout-button">Log keluar</button></form>
</div>
@endsection
