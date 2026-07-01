<meta name="supabase-url" content="{{ config('services.supabase.url') }}">
<meta name="supabase-anon-key" content="{{ config('services.supabase.key') }}">

@if(file_exists(public_path('build/manifest.json')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endif

@php($themePath = public_path('theme-red.css'))
<link rel="stylesheet" href="{{ asset('theme-red.css') }}@if(file_exists($themePath))?v={{ filemtime($themePath) }}@endif">
