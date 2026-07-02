<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#b91c1c">
    <title>Tetapkan kata laluan - DapurLink KUO</title>
    @include('partials.assets')
</head>
<body class="login-page">
<section class="login-visual">
    <div class="visual-orb orb-one"></div><div class="visual-orb orb-two"></div>
    <div class="login-brand"><img class="site-logo login-logo" src="{{ asset('images/kuo-logo.png') }}" alt="Kolej Ungku Omar, Universiti Pendidikan Sultan Idris"></div>
    <div class="visual-copy">
        <span class="visual-pill">KESELAMATAN AKAUN</span>
        <h1>Cipta kata laluan<br>baharu yang <em>selamat.</em></h1>
        <p>Gunakan sekurang-kurangnya lapan aksara dan jangan berkongsi kata laluan anda.</p>
    </div>
</section>
<main class="login-main">
    <div class="login-form-wrap" data-password-recovery>
        <p class="eyebrow">TETAPKAN SEMULA</p>
        <h2>Kata laluan baharu</h2>
        <p class="form-intro">Masukkan kata laluan baharu untuk akaun DapurLink anda.</p>
        @include('partials.alerts')
        <div class="alert error" role="alert" data-recovery-error hidden><span>!</span><p></p></div>
        <form method="POST" action="{{ route('password.update', [], false) }}" class="login-form" data-recovery-form>
            @csrf
            <input type="hidden" name="access_token" data-recovery-token>
            <label>Kata laluan baharu
                <span class="password-field"><input class="@error('password') is-invalid @enderror" id="new-password" type="password" name="password" minlength="8" required autocomplete="new-password"><button type="button" data-password-toggle="new-password" aria-label="Tunjukkan kata laluan" aria-pressed="false">Lihat</button></span>
                <span class="form-help">Gunakan sekurang-kurangnya lapan aksara.</span>
                @error('password')<span class="field-error">{{ $message }}</span>@enderror
            </label>
            <label>Sahkan kata laluan
                <span class="password-field"><input id="password-confirmation" type="password" name="password_confirmation" minlength="8" required autocomplete="new-password"><button type="button" data-password-toggle="password-confirmation" aria-label="Tunjukkan kata laluan" aria-pressed="false">Lihat</button></span>
            </label>
            <button class="primary-button" type="submit" data-recovery-submit disabled>Simpan kata laluan baharu <span>&rarr;</span></button>
        </form>
        <a class="auth-back-link" href="{{ route('password.request') }}">Minta pautan pemulihan baharu</a>
    </div>
</main>
</body>
</html>
