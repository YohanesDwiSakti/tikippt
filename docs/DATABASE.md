# Database

> Open this for Supabase/Postgres work: schema design, migrations, RLS policies, indexes, seed data, storage buckets, query performance, and data lifecycle decisions.

## Role

Supabase PostgreSQL is the source of truth for FINPROPPT tracking, package status, driver assignment, and delivery proof data. Supabase Storage owns uploaded delivery proof photos. Supabase Auth is the preferred production identity provider, with app roles stored in `profiles`, unless a later ADR chooses a different auth model.

All real schema changes must live in `supabase/migrations/`, with repeatable demo data in `supabase/seed.sql`.

The template still includes Supabase helper scripts. Use `pnpm db:diff -- -f add_finproppt_core_tables` or another descriptive migration name to generate reviewed migrations, `pnpm db:push` only when applying committed migrations to the linked remote, and keep `packages/types/src/database.types.ts` refreshed while TypeScript database types remain part of the workspace checks.

## Data Model Catalog

### `profiles`

Purpose: Application profile and role linked to an authenticated user.

| Column | Type | Required | Default | Notes |
| --- | --- | --- | --- | --- |
| `id` | `uuid` | yes | none | Primary key, references `auth.users.id` when Supabase Auth is used. |
| `name` | `text` | yes | none | User-facing name. |
| `email` | `text` | yes | none | Unique email. |
| `role` | `text` | yes | none | `admin` or `driver`. |
| `created_at` | `timestamptz` | yes | `now()` | Created timestamp. |
| `updated_at` | `timestamptz` | yes | `now()` | Updated timestamp. |

RLS: users can read/update their own profile; admins can read driver profiles; service role handles trusted admin operations.

### `packages`

Purpose: Receipt/package record and latest status shown to customers.

| Column | Type | Required | Default | Notes |
| --- | --- | --- | --- | --- |
| `id` | `uuid` | yes | `gen_random_uuid()` | Primary key. |
| `receipt` | `text` | yes | none | Unique uppercase receipt number. |
| `destination` | `text` | yes | none | Destination address/city. |
| `status` | `text` | yes | `Terdaftar` | Latest package status. |
| `latest_location` | `text` | yes | none | Latest known package location. |
| `current_driver_id` | `uuid` | no | none | Driver currently assigned. |
| `created_by` | `uuid` | no | none | Admin profile that created package. |
| `created_at` | `timestamptz` | yes | `now()` | Created timestamp. |
| `updated_at` | `timestamptz` | yes | `now()` | Updated timestamp. |

Indexes: unique `receipt`, status/updated order, current driver.

### `package_events`

Purpose: Timeline history for tracking responses and admin audit context.

| Column | Type | Required | Default | Notes |
| --- | --- | --- | --- | --- |
| `id` | `uuid` | yes | `gen_random_uuid()` | Primary key. |
| `package_id` | `uuid` | yes | none | References `packages.id`. |
| `status` | `text` | yes | none | Status at this event. |
| `location` | `text` | yes | none | Location at this event. |
| `note` | `text` | no | none | Short status note. |
| `created_by` | `uuid` | no | none | Admin/driver profile that created event. |
| `created_at` | `timestamptz` | yes | `now()` | Timeline order. |

Lifecycle: append-only except administrative correction migrations.

### `driver_assignments`

Purpose: Packages assigned by admin to drivers.

| Column | Type | Required | Default | Notes |
| --- | --- | --- | --- | --- |
| `id` | `uuid` | yes | `gen_random_uuid()` | Primary key. |
| `package_id` | `uuid` | yes | none | References `packages.id`. |
| `driver_id` | `uuid` | yes | none | References `profiles.id` with role `driver`. |
| `assigned_by` | `uuid` | yes | none | Admin profile. |
| `status` | `text` | yes | `Ditugaskan` | Assignment status. |
| `note` | `text` | no | none | Admin note for driver. |
| `assigned_at` | `timestamptz` | yes | `now()` | Assignment time. |
| `updated_at` | `timestamptz` | yes | `now()` | Updated timestamp. |

Notes: a package may have historical assignments, but only one active assignment should exist at a time.

### `delivery_proofs`

Purpose: Driver proof that package arrived at the destination.

| Column | Type | Required | Default | Notes |
| --- | --- | --- | --- | --- |
| `id` | `uuid` | yes | `gen_random_uuid()` | Primary key. |
| `package_id` | `uuid` | yes | none | References `packages.id`. |
| `assignment_id` | `uuid` | yes | none | References `driver_assignments.id`. |
| `driver_id` | `uuid` | yes | none | Driver profile. |
| `photo_url` | `text` | yes | none | Storage URL/path. |
| `delivered_at` | `timestamptz` | yes | none | Time package arrived. |
| `delivered_location` | `text` | yes | none | Human-readable destination/location proof. |
| `latitude` | `numeric` | no | none | Optional GPS latitude. |
| `longitude` | `numeric` | no | none | Optional GPS longitude. |
| `note` | `text` | no | none | Driver note. |
| `created_at` | `timestamptz` | yes | `now()` | Created timestamp. |

## Relationships

| From table | Column | To table | Cardinality | Delete behavior | Notes |
| --- | --- | --- | --- | --- | --- |
| `profiles` | `id` | `auth.users` | one-to-one | cascade | When Supabase Auth is used. |
| `packages` | `current_driver_id` | `profiles.id` | many-to-one | set null | Current assigned driver. |
| `package_events` | `package_id` | `packages.id` | many-to-one | cascade | Timeline belongs to package. |
| `driver_assignments` | `package_id` | `packages.id` | many-to-one | restrict | Assignment history. |
| `driver_assignments` | `driver_id` | `profiles.id` | many-to-one | restrict | Driver task list. |
| `driver_assignments` | `assigned_by` | `profiles.id` | many-to-one | restrict | Admin assignment actor. |
| `delivery_proofs` | `package_id` | `packages.id` | one-to-one or many-to-one | restrict | Usually one final proof per package. |
| `delivery_proofs` | `assignment_id` | `driver_assignments.id` | one-to-one | restrict | Proof belongs to assignment. |

## Enums And Status Values

| Name | Values | Used by | Notes |
| --- | --- | --- | --- |
| `user_role` | `admin`, `driver` | `profiles.role` | Customer tracking is public by receipt. |
| `package_status` | `Terdaftar`, `Diangkut Driver`, `Dalam Perjalanan`, `Sampai Tujuan`, `Gagal Dikirim`, `Cancel` | `packages.status`, `package_events.status` | Keep API/UI aligned. |
| `assignment_status` | `Ditugaskan`, `Diambil Driver`, `Dalam Perjalanan`, `Selesai`, `Gagal`, `Dibatalkan` | `driver_assignments.status` | Driver workflow. |

## Indexes

| Table | Index | Columns | Reason |
| --- | --- | --- | --- |
| `profiles` | `profiles_email_key` | `email` | Login/profile lookup. |
| `packages` | `packages_receipt_key` | `receipt` | Tracking lookup. |
| `packages` | `packages_status_updated_idx` | `status`, `updated_at` | Admin filters. |
| `packages` | `packages_current_driver_idx` | `current_driver_id` | Driver assignment review. |
| `package_events` | `package_events_package_created_idx` | `package_id`, `created_at` | Tracking timeline. |
| `driver_assignments` | `driver_assignments_driver_status_idx` | `driver_id`, `status`, `assigned_at` | Driver task list. |
| `driver_assignments` | `driver_assignments_package_active_idx` | `package_id`, `status` | Active assignment lookup. |
| `delivery_proofs` | `delivery_proofs_package_idx` | `package_id` | Proof review by receipt. |
| `delivery_proofs` | `delivery_proofs_driver_created_idx` | `driver_id`, `created_at` | Driver delivery history. |

## RLS Policy Matrix

| Table | Select | Insert | Update | Delete | Notes |
| --- | --- | --- | --- | --- | --- |
| `profiles` | Own profile; admin reads drivers | Service/admin | Own limited fields; admin role changes | Admin only | Enforce role changes server-side. |
| `packages` | Public by receipt via API; admin full; assigned driver limited | Admin service | Admin service; assigned driver only through proof/status endpoints | Admin only | Direct anon table access should be restricted. |
| `package_events` | Public by receipt via API; admin/assigned driver limited | Service only | No normal update | Admin only | Append-only timeline. |
| `driver_assignments` | Admin full; driver own assignments | Admin service | Admin service; driver own status through driver endpoints | Admin only | Drivers cannot see other drivers' work. |
| `delivery_proofs` | Admin full; driver own proofs; public tracking gets safe summary only | Assigned driver service | Admin correction only | Admin only | Photo access should be controlled. |

## Storage Buckets

| Bucket | Purpose | Path pattern | Public? | RLS/policy summary |
| --- | --- | --- | --- | --- |
| `delivery-proofs` | Driver delivery proof photos | `delivery-proofs/{receipt}/{uuid}.{ext}` | no by default | Assigned driver uploads through API; admin can review; customer receives safe signed/public view only when intended. |

## Seed And Reference Data

| Name | Where defined | Purpose | Notes |
| --- | --- | --- | --- |
| Demo admin Auth user | Supabase dashboard/Auth API | Admin login/testing | Create Auth user first, then insert matching `profiles` row with role `admin`. |
| Demo driver Auth user | Supabase dashboard/Auth API | Driver flow testing | Create Auth user first, then insert matching `profiles` row with role `driver`. |
| Sample packages | `supabase/seed.sql` | Tracking smoke tests | Keep deterministic and small. |
| Sample package events | `supabase/seed.sql` | Tracking timeline smoke tests | Does not require Auth users. |

## Schema Principles

- Model real product concepts, not screens.
- Prefer clear table names: plural, lowercase, `snake_case`.
- Use `uuid` primary keys.
- Add `created_at` and `updated_at` where records are user or business objects.
- Use foreign keys for relationships where practical.
- Add indexes for frequent filters, joins, and ordering.
- Avoid JSON blobs for data that must be queried, filtered, joined, or authorized.
- Do not add payment tables or payment provider fields.

## Migration Checklist

- [ ] Tables, columns, constraints, and indexes are included.
- [ ] RLS is enabled where needed.
- [ ] Policies are included.
- [ ] Seed/reference data is intentional and repeatable.
- [ ] Generated database types or schema artifacts are refreshed when the workflow exists.
- [ ] This Data Model Catalog is updated.
- [ ] Roll-forward strategy is clear if production data exists.

## Sync Checklist

- [ ] Schema changes match `docs/FEATURES.md` and `docs/API.md`.
- [ ] Data Model Catalog is updated for tables, columns, relationships, RLS, indexes, storage buckets, and lifecycle rules.
- [ ] API contracts match persisted data shape.
- [ ] RLS policies protect every user-facing table.
- [ ] Storage buckets and policies are documented when used.
- [ ] Performance-sensitive queries have indexes.
- [ ] Data tasks are reflected in `docs/PROGRESS.md`.
- [ ] Major data model choices are appended to `docs/DECISIONS.md`.
