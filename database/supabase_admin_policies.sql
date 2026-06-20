-- Run this once in the Supabase SQL Editor for the same project used by WBL.
-- It gives authenticated profiles with role='admin' access to the admin website.

create or replace function public.is_admin()
returns boolean
language sql
security definer
set search_path = public
stable
as $$
  select exists (
    select 1 from public.profiles
    where id = auth.uid() and role = 'admin'
  );
$$;

grant execute on function public.is_admin() to authenticated;

-- Student access: each signed-in user can only work with their own records.
-- Remove older broad policies from the original Flutter setup before replacing them.
drop policy if exists "Users can view their own profile" on public.profiles;
drop policy if exists "Users can update their own profile" on public.profiles;
drop policy if exists "Users can view their own bookings" on public.bookings;
drop policy if exists "Users can create bookings" on public.bookings;
drop policy if exists "Users can update their own bookings" on public.bookings;
drop policy if exists "Users can delete their own bookings" on public.bookings;

drop policy if exists "Users view own profile" on public.profiles;
create policy "Users view own profile" on public.profiles
for select to authenticated using (id = auth.uid());

drop policy if exists "Users update own profile" on public.profiles;
create policy "Users update own profile" on public.profiles
for update to authenticated using (id = auth.uid()) with check (id = auth.uid() and role = 'user');

drop policy if exists "Users view own bookings" on public.bookings;
create policy "Users view own bookings" on public.bookings
for select to authenticated using (user_id::text = auth.uid()::text);

drop policy if exists "Users create own bookings" on public.bookings;
create policy "Users create own bookings" on public.bookings
for insert to authenticated with check (user_id::text = auth.uid()::text and status = 'pending');

drop policy if exists "Users update own bookings" on public.bookings;
create policy "Users update own bookings" on public.bookings
for update to authenticated using (user_id::text = auth.uid()::text)
with check (user_id::text = auth.uid()::text and status in ('pending', 'cancelled', 'completed'));

drop policy if exists "Users view own complaints" on public.complaints;
create policy "Users view own complaints" on public.complaints
for select to authenticated using (user_id::text = auth.uid()::text);

drop policy if exists "Users create own complaints" on public.complaints;
create policy "Users create own complaints" on public.complaints
for insert to authenticated with check (
  user_id::text = auth.uid()::text and status = 'open' and admin_reply is null
);

drop policy if exists "Users view own checkouts" on public.checkouts;
create policy "Users view own checkouts" on public.checkouts
for select to authenticated using (
  exists (select 1 from public.bookings b where b.id = checkouts.booking_id and b.user_id::text = auth.uid()::text)
);

drop policy if exists "Users create own checkouts" on public.checkouts;
create policy "Users create own checkouts" on public.checkouts
for insert to authenticated with check (
  exists (select 1 from public.bookings b where b.id = checkouts.booking_id and b.user_id::text = auth.uid()::text)
);

-- Checkout photo storage. The Flutter app already uses this bucket.
drop policy if exists "Authenticated users upload checkout photos" on storage.objects;
create policy "Authenticated users upload checkout photos" on storage.objects
for insert to authenticated with check (bucket_id = 'checkout_photos');

alter table public.bookings enable row level security;
alter table public.profiles enable row level security;
alter table public.complaints enable row level security;
alter table public.checkouts enable row level security;

drop policy if exists "Admins manage all bookings" on public.bookings;
create policy "Admins manage all bookings" on public.bookings
for all to authenticated using (public.is_admin()) with check (public.is_admin());

drop policy if exists "Admins view all profiles" on public.profiles;
create policy "Admins view all profiles" on public.profiles
for select to authenticated using (public.is_admin());

drop policy if exists "Admins update profiles" on public.profiles;
create policy "Admins update profiles" on public.profiles
for update to authenticated using (public.is_admin()) with check (public.is_admin());

drop policy if exists "Admins manage complaints" on public.complaints;
create policy "Admins manage complaints" on public.complaints
for all to authenticated using (public.is_admin()) with check (public.is_admin());

drop policy if exists "Admins view checkouts" on public.checkouts;
create policy "Admins view checkouts" on public.checkouts
for select to authenticated using (public.is_admin());

-- Promote the first admin manually (replace the email):
-- update public.profiles set role = 'admin' where email = 'admin@kuo.edu.my';
