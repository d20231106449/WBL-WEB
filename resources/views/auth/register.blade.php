<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#b91c1c">
    <title>Daftar Akaun · DapurLink KUO</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('theme-red.css') }}?v={{ filemtime(public_path('theme-red.css')) }}">
</head>
<body class="login-page">
<section class="login-visual">
    <div class="visual-orb orb-one"></div><div class="visual-orb orb-two"></div>
    <div class="login-brand"><img class="site-logo login-logo" src="{{ asset('images/kuo-logo.png') }}" alt="Kolej Ungku Omar, Universiti Pendidikan Sultan Idris"></div>
    <div class="visual-copy">
        <span class="visual-pill">PENDAFTARAN PELAJAR</span>
        <h1>Sertai DapurLink<br>dan urus tempahan dengan <em>mudah.</em></h1>
        <p>Akaun baharu didaftarkan sebagai pelajar. Akses pentadbir hanya boleh diberikan oleh pentadbir sedia ada.</p>
    </div>
</section>
<main class="login-main">
    <div class="login-form-wrap">
        <p class="eyebrow">AKAUN BAHARU</p>
        <h2>Daftar sebagai pelajar</h2>
        <p class="form-intro">Isi maklumat berikut untuk mencipta akaun DapurLink KUO.</p>
        @include('partials.alerts')
        <form method="POST" action="{{ route('register.store') }}" class="login-form">
            @csrf
            <label>Nama penuh<input class="@error('full_name') is-invalid @enderror" type="text" name="full_name" value="{{ old('full_name') }}" maxlength="120" placeholder="Nama seperti dalam rekod pelajar" required autofocus autocomplete="name">@error('full_name')<span class="field-error">{{ $message }}</span>@enderror</label>
            <label>Alamat e-mel<input class="@error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" placeholder="nama@contoh.com" required autocomplete="email">@error('email')<span class="field-error">{{ $message }}</span>@enderror</label>
            <label>Kata laluan
                <span class="password-field"><input class="@error('password') is-invalid @enderror" id="register-password" type="password" name="password" minlength="8" required autocomplete="new-password"><button type="button" data-password-toggle="register-password" aria-label="Tunjukkan kata laluan" aria-pressed="false">◉</button></span>
                <span class="form-help">Gunakan sekurang-kurangnya lapan aksara.</span>
                @error('password')<span class="field-error">{{ $message }}</span>@enderror
            </label>
            <label>Sahkan kata laluan
                <span class="password-field"><input id="register-password-confirmation" type="password" name="password_confirmation" minlength="8" required autocomplete="new-password"><button type="button" data-password-toggle="register-password-confirmation" aria-label="Tunjukkan kata laluan" aria-pressed="false">◉</button></span>
            </label>
            <button class="primary-button" type="submit">Daftar akaun <span>→</span></button>
        </form>
        <a class="auth-back-link" href="{{ route('login') }}">← Kembali ke halaman log masuk</a>
    </div>
</main>
</body>
</html>
