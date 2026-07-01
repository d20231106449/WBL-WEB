@extends('layouts.user')
@section('title','Profil - DapurLink KUO')
@section('content')
@php
    $fullName = session('profile.full_name','Pelajar');
    $email = session('profile.email','-');
    $phone = session('profile.phone_number') ?: 'Tidak tersedia';
    $matric = session('profile.matric_no', session('profile.matric_number')) ?: 'Tidak tersedia';
@endphp
<div class="profile-wrap">
<section class="profile-hero"><span class="profile-avatar">{{ strtoupper(substr($fullName,0,1)) }}</span><h1>{{ $fullName }}</h1><p>{{ $email }}</p><span class="role-badge">Pelajar KUO</span></section>
<section class="profile-card">
    <h2>Maklumat akaun</h2>
    <div><span>Nama penuh</span><strong>{{ $fullName }}</strong></div>
    <div><span>Alamat e-mel</span><strong>{{ $email }}</strong></div>
    <div><span>Nombor telefon</span><strong>{{ $phone }}</strong></div>
    <div><span>Nombor matrik</span><strong>{{ $matric }}</strong></div>
    <div><span>Peranan</span><strong>{{ session('profile.role','user') === 'admin' ? 'Pentadbir' : 'Pelajar' }}</strong></div>
</section>
<section class="profile-card">
    <h2>Akses pantas</h2>
    <a href="{{ route('user.profile.edit') }}">Kemas kini profil <b>&rarr;</b></a>
    <a href="{{ route('user.profile.password') }}">Tukar kata laluan <b>&rarr;</b></a>
    <a href="{{ route('user.bookings') }}">Tempahan saya <b>&rarr;</b></a>
    <a href="{{ route('user.complaints') }}">Aduan dan maklum balas <b>&rarr;</b></a>
    <a href="{{ route('user.about') }}">Tentang laman sesawang <b>&rarr;</b></a>
    @if(session('profile.role')==='admin')<a href="{{ route('admin.dashboard') }}">Portal Pentadbir <b>&rarr;</b></a>@endif
</section>
<form method="POST" action="{{ route('logout') }}">@csrf<button class="logout-button">Log keluar</button></form>
</div>
@endsection
