@extends('layouts.user')
@section('title','Tukar kata laluan - DapurLink KUO')
@section('content')
<div class="user-page-title"><a href="{{ route('user.profile') }}">&larr;</a><div><p>KESELAMATAN</p><h1>Tukar kata laluan</h1><span>Gunakan kata laluan baharu untuk akaun DapurLink anda.</span></div></div>
<form class="user-form-card" method="POST" action="{{ route('user.profile.password.update', [], false) }}">
    @csrf
    @method('PATCH')
    <label>Kata laluan semasa<input class="@error('current_password') is-invalid @enderror" type="password" name="current_password" required autocomplete="current-password">@error('current_password')<span class="field-error">{{ $message }}</span>@enderror</label>
    <label>Kata laluan baharu<input class="@error('password') is-invalid @enderror" type="password" name="password" minlength="6" required autocomplete="new-password">@error('password')<span class="field-error">{{ $message }}</span>@enderror</label>
    <label>Sahkan kata laluan baharu<input type="password" name="password_confirmation" minlength="6" required autocomplete="new-password"></label>
    <button class="user-primary-button" type="submit">Kemas kini kata laluan</button>
</form>
@endsection
