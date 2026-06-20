<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log Masuk Admin · DapurLink KUO</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="login-page">
<section class="login-visual">
    <div class="visual-orb orb-one"></div><div class="visual-orb orb-two"></div>
    <div class="login-brand"><img class="site-logo login-logo" src="{{ asset('images/kuo-logo.png') }}" alt="Kolej Ungku Omar, Universiti Pendidikan Sultan Idris"></div>
    <div class="visual-copy">
        <span class="visual-pill">Portal Dapur Siswa</span>
        <h1>Tempah dan urus dapur<br>dengan lebih <em>teratur.</em></h1>
        <p>Satu portal untuk pelajar membuat tempahan dan admin mengurus penggunaan dapur.</p>
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
        @include('partials.alerts')
        <form method="POST" action="{{ route('login.store') }}" class="login-form">
            @csrf
            <label>Alamat emel<input type="email" name="email" value="{{ old('email') }}" placeholder="admin@kuo.edu.my" required autofocus autocomplete="email"></label>
            <label>Kata laluan
                <span class="password-field"><input id="password" type="password" name="password" placeholder="Masukkan kata laluan" required autocomplete="current-password"><button type="button" data-password-toggle="password" aria-label="Tunjukkan kata laluan">◉</button></span>
            </label>
            <button class="primary-button" type="submit">Log Masuk <span>→</span></button>
        </form>
        <p class="security-note"><span>◆</span> Anda akan dibawa ke portal pelajar atau admin mengikut peranan akaun.</p>
    </div>
</main>
</body>
</html>
