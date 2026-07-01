<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#b91c1c">
    <title>Log Masuk - DapurLink KUO</title>
    @include('partials.assets')
</head>
<body class="login-page">
<section class="login-visual">
    <div class="visual-orb orb-one"></div><div class="visual-orb orb-two"></div>
    <div class="login-brand"><img class="site-logo login-logo" src="{{ asset('images/kuo-logo.png') }}" alt="Kolej Ungku Omar, Universiti Pendidikan Sultan Idris"></div>
    <div class="visual-copy">
        <span class="visual-pill">Portal Dapur Siswa</span>
        <h1>Tempah dan urus dapur<br>dengan lebih <em>teratur.</em></h1>
        <p>Satu portal untuk pelajar membuat tempahan dan pentadbir mengurus penggunaan dapur.</p>
    </div>
    <div class="visual-card">
        <span class="mini-icon">✓</span><div><strong>Pangkalan data yang sama</strong><small>Disambungkan terus dengan aplikasi pelajar.</small></div><span class="pulse-ring"></span>
    </div>
</section>
<main class="login-main">
    <div class="login-form-wrap">
        <p class="eyebrow">SELAMAT DATANG KEMBALI</p>
        <h2>Log masuk DapurLink</h2>
        <p class="form-intro">Gunakan akaun yang sama seperti aplikasi DapurLink KUO.</p>
        @if(session('password_reset_success'))
            <div class="alert success" role="status"><span aria-hidden="true">✓</span><p>{{ session('password_reset_success') }}</p><button type="button" data-dismiss aria-label="Tutup mesej">&times;</button></div>
        @endif
        @if($errors->any())
            <div class="alert error" role="alert"><span aria-hidden="true">!</span><p>{{ $errors->first() }}</p><button type="button" data-dismiss aria-label="Tutup mesej">&times;</button></div>
        @endif
        <div class="alert error" role="alert" data-login-error hidden><span aria-hidden="true">!</span><p></p></div>
        <form class="login-form" data-login-form data-session-url="{{ route('auth.client-session', [], false) }}" novalidate>
            <fieldset class="account-type-field">
                <legend>Log masuk sebagai</legend>
                <div class="account-type-options">
                    <label><input type="radio" name="account_type" value="user" {{ old('account_type', 'user') === 'user' ? 'checked' : '' }} required><span>Pelajar</span></label>
                    <label><input type="radio" name="account_type" value="admin" {{ old('account_type') === 'admin' ? 'checked' : '' }} required><span>Pentadbir</span></label>
                </div>
            </fieldset>
            <label>Alamat e-mel<input class="@error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" placeholder="nama@contoh.com" required autofocus autocomplete="email" data-login-email><span class="field-error" data-login-email-error></span></label>
            <label>Kata laluan
                <span class="password-field"><input class="@error('password') is-invalid @enderror" id="password" type="password" name="password" placeholder="Masukkan kata laluan" required autocomplete="current-password" data-login-password><button type="button" data-password-toggle="password" aria-label="Tunjukkan kata laluan" aria-pressed="false">◉</button></span>
                <span class="field-error" data-login-password-error></span>
            </label>
            <a class="forgot-password-link" href="{{ route('password.request') }}">Lupa kata laluan?</a>
            <button class="primary-button" type="submit" data-login-submit><span data-button-label>Log Masuk</span> <span>→</span></button>
        </form>
        <div class="auth-register-prompt"><span>Belum mempunyai akaun?</span><a href="{{ route('register') }}">Daftar akaun baharu</a></div>
        <p class="security-note"><span>◆</span> Anda akan dibawa ke portal pelajar atau pentadbir mengikut peranan akaun.</p>
    </div>
</main>
</body>
</html>
