# Backend

> Open this for `apps/server` work: Go routes, middleware, validation, services, auth guards, integrations, and server-side tests.

## Role

`apps/server` is the trusted Go backend boundary. It owns API routes, server-only secrets, Supabase service-role operations, package status transitions, driver assignment, and delivery proof submission. Payment, invoice, checkout, and Midtrans logic are not part of this project.

The API contract source of truth is `docs/API.md`. This file explains how backend code should be structured and verified.

## Server Structure

Use feature-based Go packages as the app grows:

```text
apps/server/
  cmd/api/
    main.go
  internal/
    auth/
      handler.go
      service.go
      service_test.go
    tracking/
    packages/
    assignments/
    proofs/
    drivers/
    platform/
      config.go
      errors.go
      http.go
      supabase.go
```

Keep files focused:

- `cmd/api/main.go` loads config and starts the HTTP server.
- `handler.go` owns HTTP parsing, validation, auth checks, and response envelopes.
- `service.go` owns business logic and persistence calls.
- `repository.go` is allowed when a feature needs database query isolation.
- `*_test.go` lives next to the code it verifies.
- Shared server helpers live under `internal/platform`.
- App-agnostic pure helpers may move to `packages/utils` only if they are used across multiple apps.

## Route Rules

- Base URL is `/api/v1`.
- Resource names are plural and lowercase.
- Validate params, query, and body at the route boundary.
- Return the envelopes defined in `docs/API.md`.
- Never return raw arrays at the top level.
- Keep error codes stable and `SCREAMING_SNAKE_CASE`.
- Paginate list endpoints. Do not return unbounded lists.
- Route handlers stay thin: validate, authorize, call service, shape response.

## Core Business Rules

- Receipt numbers are normalized to uppercase.
- Customer tracking is public by receipt, but only returns shipment-safe data.
- Admin creates packages, updates package status/location, and assigns packages to drivers.
- Every admin status update creates a `package_events` row.
- Assigning packages updates `packages.current_driver_id` and package status to `Diangkut Driver`, unless the package is delivered or cancelled.
- Drivers can only see and update packages assigned to them.
- Delivery proof requires photo URL, delivered time, and delivered location.
- Delivery proof submission updates assignment status to `Selesai`, package status to `Sampai Tujuan`, latest location to delivered location, and creates a package event.
- Payment, invoice, checkout, Midtrans, refund, settlement, and payout code must not be added.

## Auth And Secrets

- Plaintext passwords are prototype-only and not allowed for production implementation.
- Preferred production auth is Supabase Auth plus a role-bearing `profiles` table, unless a later ADR chooses Laravel session auth plus API tokens.
- Protected endpoints require an authenticated user and role authorization.
- Server-only secrets stay in Go environment variables and never use public prefixes.
- Supabase service-role key is only used from trusted server code.

## Error Handling

Use plain-language messages for clients and stable machine-readable codes.

Example:

```json
{
  "error": {
    "code": "RECEIPT_NOT_FOUND",
    "message": "No package was found for this receipt."
  }
}
```

Avoid leaking stack traces, SQL details, service-role errors, provider secrets, build IDs, or environment names.

## External Integrations

- Supabase/Postgres: see `docs/DATABASE.md`.
- Supabase Storage: delivery proof photos go into the `delivery-proofs` bucket.
- Notifications, GPS, barcode scanning, and route optimization are P2 unless rescheduled in `docs/FEATURES.md`.

Wrap external providers behind feature services so route handlers do not know provider details.

## Testing

Add focused Go tests for:

- Route contracts and response envelopes.
- Validation success/failure.
- Receipt normalization.
- Customer tracking not-found behavior.
- Admin package create/update and package event creation.
- Driver assignment validation and authorization.
- Driver task list scope: drivers only see their own assigned packages.
- Delivery proof required fields and synchronized status updates.

Before marking backend work done:

- `go test ./...` from `apps/server`.
- `go vet ./...` from `apps/server` when configured.
- Any repository-level verification that has been migrated to include Go.
- `pnpm docs:check` when docs changed.

## Sync Checklist

- [ ] Any new endpoint is documented in `docs/API.md`.
- [ ] API contract changes are reflected in Laravel and Expo clients.
- [ ] API tasks are reflected in `docs/PROGRESS.md`.
- [ ] Database changes are reflected in `docs/DATABASE.md`.
- [ ] Payment features remain out of scope in `docs/PAYMENTS.md`.
- [ ] Security-sensitive decisions are added to `docs/DECISIONS.md`.
