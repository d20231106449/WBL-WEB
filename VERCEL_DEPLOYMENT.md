# Deploy Laravel ke Vercel

Projek ini Laravel/PHP, jadi Vercel perlu guna community PHP runtime melalui `vercel.json`.
Jika build Vercel gagal dengan error `composer: command not found` atau exit code `127`, pastikan `installCommand` dalam `vercel.json` hanya menjalankan `npm ci`. Composer dependencies akan diurus oleh runtime PHP.

## Cara Deploy

1. Push perubahan ini ke GitHub.
2. Buka Vercel Dashboard.
3. Pilih **New Project**.
4. Import repo `d20231106449/WBL-WEB`.
5. Pada project settings, biarkan root directory sebagai root repo.
6. Tambah environment variables di bawah.
7. Klik **Deploy**.

## Environment Variables

Set nilai ini dalam Vercel Project Settings > Environment Variables:

```env
APP_NAME="DapurLink KUO"
APP_ENV=production
APP_KEY=base64:GENERATE_APP_KEY_DAN_LETAK_DI_SINI
APP_DEBUG=false
APP_URL=https://nama-project.vercel.app
APP_TIMEZONE=Asia/Kuala_Lumpur
APP_LOCALE=ms
APP_FALLBACK_LOCALE=ms

SUPABASE_URL=https://ihovylagnopqpimqxwsc.supabase.co
SUPABASE_ANON_KEY=LETAK_SUPABASE_ANON_KEY_SEBENAR

LOG_CHANNEL=stderr
LOG_STACK=stderr
SESSION_DRIVER=cookie
CACHE_STORE=array
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local
```

Untuk jana `APP_KEY`:

```bash
php artisan key:generate --show
```

Selepas Vercel beri domain production, kemas kini `APP_URL` kepada URL sebenar.

## Nota Penting

Vercel serverless tidak sesuai untuk simpan fail upload atau database SQLite secara kekal. Projek ini sepatutnya guna Supabase untuk data utama. Jika nanti ada upload fail yang perlu disimpan kekal, gunakan Supabase Storage atau S3-compatible storage.
