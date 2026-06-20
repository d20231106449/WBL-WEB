# DapurLink KUO Admin Setup

The Laravel website uses the same Supabase project as the Flutter app. Normal
profiles open the student portal; profiles with `role = 'admin'` open the admin
dashboard.

## One-time Supabase setup

1. Open the Supabase SQL Editor for project `ihovylagnopqpimqxwsc`.
2. Run `database/supabase_admin_policies.sql`.
3. Promote an existing registered profile:

```sql
update public.profiles
set role = 'admin'
where email = 'your-admin-email@example.com';
```

Every user signs in with the same email and password stored in Supabase Auth.

## Run locally

```powershell
php artisan serve
pnpm run dev
```

Then open `/login`. Supabase settings live in `.env` as `SUPABASE_URL` and
`SUPABASE_ANON_KEY`.
