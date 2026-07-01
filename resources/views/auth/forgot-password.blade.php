<!DOCTYPE html>
<html lang="ms">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#b91c1c">
    <title>Lupa Kata Laluan · DapurLink KUO</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('theme-red.css') }}?v={{ filemtime(public_path('theme-red.css')) }}">
</head>

<body class="login-page">
    <section class="login-visual">
        <div class="visual-orb orb-one"></div>
        <div class="visual-orb orb-two"></div>
        <div class="login-brand"><img class="site-logo login-logo" src="{{ asset('images/kuo-logo.png') }}"
                alt="Kolej Ungku Omar, Universiti Pendidikan Sultan Idris"></div>
        <div class="visual-copy">
            <span class="visual-pill">PEMULIHAN AKAUN</span>
            <h1>Kembali ke akaun anda<br>dengan lebih <em>mudah.</em></h1>
            <p>Kami akan menghantar pautan selamat ke alamat e-mel yang didaftarkan.</p>
        </div>
    </section>
    <main class="login-main">
        <div class="login-form-wrap">
            <p class="eyebrow">BANTUAN LOG MASUK</p>
            <h2>Lupa kata laluan?</h2>
            <p class="form-intro">Masukkan e-mel akaun DapurLink anda untuk menerima pautan pemulihan.</p>
            @include('partials.alerts')
            <form method="POST" action="{{ route('password.email') }}" class="login-form">
                @csrf
                <label>Alamat e-mel<input class="@error('email') is-invalid @enderror" type="email" name="email"
                        value="{{ old('email') }}" placeholder="nama@contoh.com" required autofocus
                        autocomplete="email">
                    @error('email')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </label>
                <button class="primary-button" type="submit">Hantar pautan pemulihan <span>→</span></button>
            </form>
            <a class="auth-back-link" href="{{ route('login') }}">← Kembali ke log masuk</a>
        </div>
    </main>
</body>

</html>
