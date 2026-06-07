# Architecture

## Product Stack Override

This product intentionally uses the user-selected stack:

- **Web frontend/application:** Laravel in `apps/web`
- **Backend API:** Go in `apps/server`
- **Database, auth option, and storage:** Supabase PostgreSQL, Supabase Auth where adopted, and Supabase Storage
- **Mobile:** React Native Expo in `apps/mobile`
- **Shared contracts/docs:** `docs/API.md`, generated OpenAPI/JSON-schema artifacts when added, and small shared TypeScript packages only where they still help Expo or tooling

This supersedes the template default of Next.js plus Hono for this product. The default template rules still apply where they are stack-agnostic: clean boundaries, migration-based database changes, no exposed secrets, no cross-feature imports, focused files, and documentation-first work.

Payment, invoice, checkout, and Midtrans code are explicitly out of scope.

## Target Monorepo Layout

```text
apps/
  web/       -> Laravel web app for customer tracking, admin operations, and driver web surfaces
  server/    -> Go API service for tracking, package status, driver assignment, and delivery proof
  mobile/    -> Expo app for mobile tracking and driver proof flow
packages/
  types/     -> generated/shared contracts when useful for TypeScript clients
  utils/     -> pure app-agnostic helpers only when used by 3+ places
  config/    -> shared formatting/linting/tooling where applicable
supabase/
  migrations/ -> committed database migrations
  seed.sql    -> empty unless approved real reference data is needed
```

`apps/mobile` does not exist yet and should be created only when mobile implementation starts. `apps/web` and `apps/server` may need to be migrated from the current template starter to Laravel and Go.

## Application Boundaries

- `apps/web` owns Laravel routes, views/frontend assets, public tracking page, admin package/assignment pages, and driver task/proof pages.
- `apps/server` owns the trusted Go API, server-only secrets, Supabase service-role operations, status transitions, driver assignment, and delivery proof submission.
- `apps/mobile` owns Expo tracking and driver task/proof screens. It does not talk to Supabase with service-role credentials.
- Supabase is the database and file storage source of truth. Dashboard-only schema changes are not accepted as product state.

## Data Flow

```text
Customer tracking page
  -> Go API tracking endpoint
  -> Supabase packages + package_events + safe delivery_proofs summary

Admin Laravel pages
  -> trusted Laravel Supabase gateway or Go API package and assignment endpoints
  -> Supabase packages + driver_assignments + package_events

Driver Laravel/Expo pages
  -> trusted Laravel Supabase gateway or Go API driver endpoints
  -> Supabase driver_assignments + delivery_proofs + package_events + Storage
```

Laravel may read through the Go API for product data. If Laravel uses Supabase directly for auth/session helpers, it must still not import or expose the service-role key.

## API Contract Strategy

The durable contract source of truth is `docs/API.md`. During implementation, generate or maintain machine-readable contracts from the Go service when practical, such as OpenAPI, and use them for Laravel and Expo clients. Do not duplicate request/response shapes separately in each app.

Validation happens at the Go API boundary. Laravel and Expo also validate user input for user experience, but server validation is the source of truth.

## Feature-Based Architecture

Inside each app, group by product capability rather than generic file type.

```text
apps/server/
  internal/
    auth/
    tracking/
    packages/
    assignments/
    proofs/
    drivers/

apps/web/
  app/
    Http/
      Controllers/
        Admin/
        Driver/
        Tracking/
    Services/
      ApiClient/
  resources/
    views/
      tracking/
      admin/
      driver/

apps/mobile/
  src/
    features/
      tracking/
      driver-tasks/
      delivery-proof/
```

Feature internals stay private. Cross-feature sharing goes through app-level shared services, components, or documented package-level helpers.

## Import And Dependency Boundaries

- Apps do not import runtime code from sibling apps.
- `apps/web` and `apps/mobile` communicate with `apps/server` through documented HTTP APIs.
- Server-only secrets stay in Go API environment variables or trusted Laravel server config when required, never in browser bundles or Expo public config.
- Shared packages must stay app-agnostic. Do not put Laravel, Go, or Expo runtime logic in `packages/*`.

## Supabase Clients

- **Go API:** may use service-role credentials for trusted operations; this is the main backend data path.
- **Laravel web:** may use public anon credentials for user-scoped auth/client flows or call the Go API. Service-role usage in Laravel is allowed only for trusted server-side actions and must never reach rendered pages or frontend assets.
- **Expo mobile:** uses API calls and public anon credentials only if a future Supabase Auth client flow requires it.
- RLS should be enabled for user-facing tables. Service-role operations must still enforce role authorization in the Go service.

## Auth And Roles

Customer receipt tracking can be public by receipt. Admin and driver surfaces require authentication.

Preferred production auth:

- Supabase Auth plus a `profiles` table with `admin` and `driver` roles.

Alternative:

- Laravel session auth for web plus Go-issued/API-compatible tokens.

The final implementation must be recorded in `docs/DECISIONS.md` before coding auth deeply. Plaintext passwords are prototype-only and not allowed in production.

## Naming Conventions

| Thing | Convention | Example |
| --- | --- | --- |
| Files/folders | kebab-case where the stack permits; Laravel classes follow Laravel conventions | `tracking-service.go`, `PackageController.php` |
| Go packages | lowercase | `assignments` |
| PHP classes | PascalCase | `PackageController` |
| React Native components | PascalCase | `DriverTasksScreen` |
| Functions/vars | camelCase in TS/PHP, lowerCamel or idiomatic Go in Go | `submitProof`, `submitProof` |
| Database tables/columns | snake_case | `driver_assignments` |
| Constants | SCREAMING_SNAKE_CASE | `MAX_PHOTO_SIZE_MB` |

## Verification

Until scripts are migrated, root `pnpm` checks may only validate template tooling. Product implementation should add stack-specific commands:

- Laravel: composer install, PHP tests, static analysis/formatting where adopted.
- Go: `go test ./...`, `go vet ./...`, and build checks.
- Expo: package install, TypeScript check, lint, and platform smoke tests.
- Docs: `pnpm docs:check` after documentation changes.
