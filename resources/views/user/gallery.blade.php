@extends('layouts.user')
@section('title','Galeri Dapur - DapurLink KUO')
@section('content')
<div class="user-page-title between gallery-title">
    <div><p>GALERI DAPUR</p><h1>Ruang Dapur Siswa Madani KUO</h1><span>Lihat kemudahan dapur sebelum membuat tempahan.</span></div>
    <a class="compact-primary" href="{{ route('user.bookings.create') }}">Tempah dapur <span>&rarr;</span></a>
</div>

<section class="gallery-slideshow" data-gallery-slideshow aria-label="Slideshow galeri dapur">
    <div class="gallery-slides">
        @foreach($photos as $index => $photo)
            <article class="gallery-slide {{ $index === 0 ? 'active' : '' }}" data-gallery-slide>
                <img src="{{ asset('images/kitchen/'.$photo['file']) }}" alt="{{ $photo['title'] }}">
                <div>
                    <p>KEMUDAHAN PELAJAR</p>
                    <h2>{{ $photo['title'] }}</h2>
                    <span>{{ $photo['caption'] }}</span>
                </div>
            </article>
        @endforeach
    </div>
    <button class="gallery-control prev" type="button" data-gallery-prev aria-label="Gambar sebelum">&lsaquo;</button>
    <button class="gallery-control next" type="button" data-gallery-next aria-label="Gambar seterusnya">&rsaquo;</button>
    <div class="gallery-dots" aria-label="Pilih gambar">
        @foreach($photos as $index => $photo)
            <button class="{{ $index === 0 ? 'active' : '' }}" type="button" data-gallery-dot="{{ $index }}" aria-label="Paparkan {{ $photo['title'] }}"></button>
        @endforeach
    </div>
</section>

<section class="gallery-grid">
    @foreach($photos as $index => $photo)
        <article class="gallery-card {{ $index === 0 ? 'featured' : '' }}">
            <a href="{{ asset('images/kitchen/'.$photo['file']) }}" target="_blank" rel="noopener">
                <img src="{{ asset('images/kitchen/'.$photo['file']) }}" alt="{{ $photo['title'] }}">
                <span>Lihat gambar</span>
            </a>
            <div>
                <small>{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</small>
                <h2>{{ $photo['title'] }}</h2>
                <p>{{ $photo['caption'] }}</p>
            </div>
        </article>
    @endforeach
</section>

<section class="rules-card gallery-note"><span>i</span><div><strong>Nota penggunaan</strong><p>Gambar ini diambil daripada galeri aplikasi DapurLink. Sila gunakan kemudahan dengan berhemah dan laporkan sebarang kerosakan melalui Aduan.</p></div></section>
@endsection
