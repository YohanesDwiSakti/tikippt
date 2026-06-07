# FINPROPPT Go API

Backend API for customer receipt tracking, admin package/status management, driver assignment, and delivery proof.

## Setup

1. Copy env:

   ```powershell
   Copy-Item .env.example .env
   ```

2. Fill Supabase values from Project Settings -> API:

   ```text
   SUPABASE_URL=
   SUPABASE_ANON_KEY=
   DATABASE_URL=
   ```

3. Apply the migration in `supabase/migrations/202606070001_add_tracking_core.sql`.

4. Create admin and driver users in Supabase Auth. After each Auth user exists, add matching rows in `public.profiles`:

   ```sql
   insert into public.profiles (id, name, email, role)
   values
     ('<admin-auth-user-id>', 'Admin Hub', 'admin@tiki.test', 'admin'),
     ('<driver-auth-user-id>', 'Made Driver', 'driver@tiki.test', 'driver');
   ```

5. Run seed data in `supabase/seed.sql`.

## Run

```powershell
go run ./cmd/api
```

Health check:

```powershell
Invoke-WebRequest http://127.0.0.1:5000/api/v1/health -UseBasicParsing
```

## Auth

`POST /api/v1/auth/login` uses Supabase Auth email/password and then checks `public.profiles.role`.

Protected admin endpoints require a bearer token from an admin profile. Protected driver endpoints require a bearer token from a driver profile.

Auth users must be created from the Supabase dashboard or with a valid service-role JWT. Supabase secret API keys are not used by this API for Admin Auth calls.
