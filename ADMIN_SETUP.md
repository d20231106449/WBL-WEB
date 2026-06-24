# Persediaan Pentadbir DapurLink KUO

Laman Laravel menggunakan projek Supabase yang sama dengan aplikasi Flutter.
Profil biasa akan membuka portal pelajar, manakala profil dengan nilai
`role = 'admin'` akan membuka papan pemuka pentadbir.

## Persediaan awal Supabase

1. Buka Penyunting SQL Supabase bagi projek `ihovylagnopqpimqxwsc`.
2. Jalankan fail `database/supabase_admin_policies.sql`.
3. Jadikan profil berdaftar sebagai pentadbir:

```sql
update public.profiles
set role = 'admin'
where email = 'pentadbir@example.com';
```

Setiap pengguna perlu log masuk menggunakan e-mel dan kata laluan yang disimpan
dalam Supabase Auth.

## Pendaftaran pengguna baharu

Jalankan semula `database/supabase_admin_policies.sql` selepas mengemas kini
projek ini. Fail tersebut memasang pencetus pangkalan data yang mencipta profil
pelajar secara automatik apabila pengguna mendaftar melalui halaman
`/register`.

Pendaftaran awam hanya mencipta akaun pelajar. Hak pentadbir perlu diberikan
oleh pentadbir sedia ada melalui halaman pengurusan pengguna. Pada halaman log
masuk, pengguna perlu memilih Pelajar atau Pentadbir mengikut peranan akaun
mereka.

## Pemulihan kata laluan

Tambahkan alamat berikut pada bahagian **Supabase > Authentication > URL
Configuration > Redirect URLs**:

```text
http://127.0.0.1:8000/reset-password
```

Halaman log masuk kemudiannya boleh menghantar e-mel pemulihan melalui Supabase
Auth. Pautan pemulihan akan membawa pengguna kembali ke laman untuk menetapkan
kata laluan baharu.

## Menjalankan laman secara setempat

```powershell
C:\laragon\bin\php\php-8.4.12-nts-Win32-vs17-x64\php.exe artisan serve
C:\laragon\bin\nodejs\node-v22\npm.cmd run dev
```

Kemudian, buka `/login`. Tetapan Supabase disimpan dalam fail `.env` melalui
`SUPABASE_URL` dan `SUPABASE_ANON_KEY`. Salin kunci awam daripada bahagian
**Supabase > Project Settings > API Keys**. Kunci contoh yang bermula dengan
`your-` tidak boleh digunakan.

Projek ini memerlukan PHP 8.4.1 atau versi yang lebih baharu. Pemasangan PHP 8.2
daripada XAMPP pada komputer ini tidak serasi dengan kebergantungan Composer
yang telah dipasang.
