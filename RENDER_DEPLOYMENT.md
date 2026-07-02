# Deploy Laravel ke Render

Projek ini disediakan untuk Render menggunakan Docker kerana Laravel 13 memerlukan PHP 8.4.

## Cara Deploy

1. Push repo ke GitHub.
2. Buka Render Dashboard.
3. Pilih **New +** > **Blueprint**.
4. Connect repo `d20231106449/WBL-WEB`.
5. Render akan baca `render.yaml`.
6. Isi environment variables yang bertanda `sync: false`.
7. Klik **Apply** atau **Deploy**.

## Environment Variables Wajib

Set nilai ini dalam Render:

```env
APP_KEY=base64:APP_KEY_DARIPADA_ENV_LOCAL
APP_URL=https://nama-service.onrender.com
ASSET_URL=https://nama-service.onrender.com
SUPABASE_ANON_KEY=SUPABASE_ANON_KEY_SEBENAR
VITE_SUPABASE_URL=https://ihovylagnopqpimqxwsc.supabase.co
VITE_SUPABASE_ANON_KEY=SUPABASE_ANON_KEY_SEBENAR
SESSION_DRIVER=file
SESSION_SECURE_COOKIE=true
CACHE_STORE=array
QUEUE_CONNECTION=sync
```

Jika Render Docker build tidak memasukkan `VITE_*` ke bundle, nilai Supabase anon key yang sama turut dibaca melalui meta tag Laravel pada runtime. Jangan masukkan service role key.

Nilai lain sudah disediakan dalam `render.yaml`.

## Nota

Render free plan boleh tidur selepas tidak digunakan seketika, jadi first load mungkin perlahan. Data utama projek ini guna Supabase, jadi tiada database Render diperlukan.
