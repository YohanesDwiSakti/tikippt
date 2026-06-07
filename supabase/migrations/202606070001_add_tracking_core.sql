create extension if not exists "pgcrypto";

create table if not exists public.profiles (
  id uuid primary key references auth.users(id) on delete cascade,
  name text not null,
  email text not null unique,
  role text not null check (role in ('admin', 'driver')),
  created_at timestamptz not null default now(),
  updated_at timestamptz not null default now()
);

create table if not exists public.packages (
  id uuid primary key default gen_random_uuid(),
  receipt text not null unique,
  destination text not null,
  status text not null default 'Terdaftar' check (status in ('Terdaftar', 'Diangkut Driver', 'Dalam Perjalanan', 'Sampai Tujuan', 'Gagal Dikirim', 'Cancel')),
  latest_location text not null,
  current_driver_id uuid references public.profiles(id) on delete set null,
  created_by uuid references public.profiles(id) on delete set null,
  created_at timestamptz not null default now(),
  updated_at timestamptz not null default now()
);

create table if not exists public.package_events (
  id uuid primary key default gen_random_uuid(),
  package_id uuid not null references public.packages(id) on delete cascade,
  status text not null check (status in ('Terdaftar', 'Diangkut Driver', 'Dalam Perjalanan', 'Sampai Tujuan', 'Gagal Dikirim', 'Cancel')),
  location text not null,
  note text,
  created_by uuid references public.profiles(id) on delete set null,
  created_at timestamptz not null default now()
);

create table if not exists public.driver_assignments (
  id uuid primary key default gen_random_uuid(),
  package_id uuid not null references public.packages(id) on delete restrict,
  driver_id uuid not null references public.profiles(id) on delete restrict,
  assigned_by uuid not null references public.profiles(id) on delete restrict,
  status text not null default 'Ditugaskan' check (status in ('Ditugaskan', 'Diambil Driver', 'Dalam Perjalanan', 'Selesai', 'Gagal', 'Dibatalkan')),
  note text,
  assigned_at timestamptz not null default now(),
  updated_at timestamptz not null default now()
);

create table if not exists public.delivery_proofs (
  id uuid primary key default gen_random_uuid(),
  package_id uuid not null references public.packages(id) on delete restrict,
  assignment_id uuid not null references public.driver_assignments(id) on delete restrict,
  driver_id uuid not null references public.profiles(id) on delete restrict,
  photo_url text not null,
  delivered_at timestamptz not null,
  delivered_location text not null,
  latitude numeric,
  longitude numeric,
  note text,
  created_at timestamptz not null default now()
);

create index if not exists packages_status_updated_idx on public.packages(status, updated_at desc);
create index if not exists packages_current_driver_idx on public.packages(current_driver_id);
create index if not exists package_events_package_created_idx on public.package_events(package_id, created_at asc);
create index if not exists driver_assignments_driver_status_idx on public.driver_assignments(driver_id, status, assigned_at desc);
create index if not exists driver_assignments_package_active_idx on public.driver_assignments(package_id, status);
create index if not exists delivery_proofs_package_idx on public.delivery_proofs(package_id);
create index if not exists delivery_proofs_driver_created_idx on public.delivery_proofs(driver_id, created_at desc);

alter table public.profiles enable row level security;
alter table public.packages enable row level security;
alter table public.package_events enable row level security;
alter table public.driver_assignments enable row level security;
alter table public.delivery_proofs enable row level security;

create policy "profiles own read"
  on public.profiles for select
  using (auth.uid() = id);

create policy "drivers read own assignments"
  on public.driver_assignments for select
  using (auth.uid() = driver_id);

create policy "drivers read own proofs"
  on public.delivery_proofs for select
  using (auth.uid() = driver_id);

insert into storage.buckets (id, name, public)
values ('delivery-proofs', 'delivery-proofs', false)
on conflict (id) do nothing;
