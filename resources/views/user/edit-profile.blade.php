@extends('layouts.user')
@section('title','Edit Profil - DapurLink KUO')
@section('content')
<div class="user-page-title"><a href="{{ route('user.profile') }}">&larr;</a><div><p>PROFIL</p><h1>Edit profil</h1><span>Kemas kini maklumat akaun pelajar anda.</span></div></div>
<form class="user-form-card" method="POST" action="{{ route('user.profile.update') }}">
    @csrf
    @method('PATCH')
    <label>Nama penuh<input class="@error('full_name') is-invalid @enderror" type="text" name="full_name" value="{{ old('full_name', session('profile.full_name')) }}" maxlength="120" required>@error('full_name')<span class="field-error">{{ $message }}</span>@enderror</label>
    <label>Alamat e-mel<input class="@error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email', session('profile.email')) }}" required>@error('email')<span class="field-error">{{ $message }}</span>@enderror</label>
    <label>Nombor telefon<input class="@error('phone_number') is-invalid @enderror" type="text" name="phone_number" value="{{ old('phone_number', session('profile.phone_number')) }}" maxlength="30">@error('phone_number')<span class="field-error">{{ $message }}</span>@enderror</label>
    <label>Nombor matrik<input class="@error('matric_no') is-invalid @enderror" type="text" name="matric_no" value="{{ old('matric_no', session('profile.matric_no', session('profile.matric_number'))) }}" maxlength="50">@error('matric_no')<span class="field-error">{{ $message }}</span>@enderror</label>
    <button class="user-primary-button" type="submit">Kemas kini profil</button>
</form>
@endsection
