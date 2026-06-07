# Build Progress & Feature Map

This is the live build map for FINPROPPT TIKI Denpasar. It tracks status and connections only.

Source docs:

- WHAT / WHY -> `docs/PRD.md`
- Feature capabilities + phases -> `docs/FEATURES.md`
- Product-specific UI/UX direction -> `docs/UI_UX.md`
- Structure / boundaries -> `docs/ARCHITECTURE.md`
- API contracts -> `docs/API.md`
- Universal frontend guardrails -> `docs/FRONTEND.md`
- Backend implementation rules -> `docs/BACKEND.md`
- Database/RLS/storage rules -> `docs/DATABASE.md`
- Payment out-of-scope note -> `docs/PAYMENTS.md`
- Final verification -> `docs/QUALITY.md`

Status: `[ ]` todo, `[~]` in progress, `[x]` done, `[!]` blocked.

## Now Building

- `[x]` Go backend API scaffold connected to Supabase Auth and Supabase Postgres via env-driven config. (`apps/server`, `docs/API.md`)
- `[x]` Supabase schema migration and seed for tracking, packages, driver assignments, and delivery proofs. (`supabase/migrations`, `supabase/seed.sql`)

## Build Map

### Foundation And Stack Migration

Target stack and repo structure. Phase: P0. Spec: `docs/ARCHITECTURE.md`, `docs/DECISIONS.md`.

- `[x]` Add Laravel application shell in `apps/web` with Blade views and static assets. (ARCHITECTURE.md)
- `[x]` Migrate `apps/server` from template starter to Go API structure. (BACKEND.md)
- `[ ]` Add `apps/mobile` Expo app when mobile implementation begins. (ARCHITECTURE.md, FEATURES: Mobile Expo App)
- `[ ]` Update root scripts so verification includes Laravel, Go, Expo, docs, and any remaining workspace checks. (QUALITY.md)
- `[x]` Update `.env.example` for Laravel, Go API, Supabase, and Storage variables only. (BACKEND.md, DATABASE.md)
- `[x]` Keep database credentials server-only and public keys clearly marked. (ARCHITECTURE.md, DATABASE.md)
- `[ ]` Confirm no payment variables, payment routes, payment tables, or payment UI are introduced. (PAYMENTS.md)

**Connects to:** every feature depends on this stack shape and verification path.

### Public Tracking (`/`, `/tracking`)

Public customer receipt lookup. Phase: P0. Spec: `docs/UI_UX.md`, `docs/API.md`.

- `[x]` Public navbar with route-aware links for tracking and login. (UI_UX.md, FRONTEND.md)
- `[x]` Tracking-first hero with receipt input visible in first viewport. (UI_UX.md)
- `[x]` Tracking result with status, destination, latest location, updated time, timeline, and safe proof summary when delivered. (API.md, UI_UX.md)
- `[x]` Not-found, validation, loading, and error states represented in mock UI. (UI_UX.md, QUALITY.md)
- `[x]` Public footer with TIKI Denpasar help/contact/legal links, no provider/debug details. (UI_UX.md, QUALITY.md)
- `[ ]` Responsive render review at mobile and required desktop widths. (FRONTEND.md, QUALITY.md)

**Connects to:** `GET /api/v1/tracking/{receipt}`, `packages`, `package_events`, safe `delivery_proofs` summary.

### Authentication And Role Routing

Admin/driver entry and access control. Phase: P0. Spec: `docs/FEATURES.md`, `docs/API.md`, `docs/ARCHITECTURE.md`.

- `[x]` Use Supabase Auth as the final auth model. (ARCHITECTURE.md, DECISIONS.md)
- `[x]` Role-aware login UI in Laravel mock with admin/driver entry links. (FEATURES: Authentication And Roles)
- `[x]` `POST /api/v1/auth/login` in Go API through Supabase Auth password grant. (API.md, BACKEND.md)
- `[x]` Role authorization middleware for admin and driver endpoints. (BACKEND.md)
- `[!]` Seed demo admin and driver accounts safely: Auth users must be created in Supabase dashboard or with a valid service-role JWT before profile rows can be inserted. (DATABASE.md)
- `[ ]` Tests for login validation and role routing. (QUALITY.md)

**Connects to:** admin dashboard, driver dashboard, protected API endpoints.

### Admin Package Management (`/admin`, `/admin/packages`)

Create and update package/receipt status. Phase: P0. Spec: `docs/FEATURES.md`, `docs/API.md`, `docs/DATABASE.md`.

- `[x]` Admin shell with role-aware sidebar/top nav and active states. (UI_UX.md, FRONTEND.md)
- `[x]` Admin package list with mock receipt, status, driver, and date data. (UI_UX.md)
- `[x]` Package create/update form mock with receipt, destination, status, latest location, and note. (API.md)
- `[x]` `GET /api/v1/packages` paginated endpoint. (API.md)
- `[x]` `POST /api/v1/packages` create endpoint. (API.md)
- `[x]` `PATCH /api/v1/packages/{receipt}` update endpoint. (API.md)
- `[x]` Package event created for every status update. (DATABASE.md)
- `[ ]` Tests for uppercase receipt, status validation, and event creation. (BACKEND.md)

**Connects to:** customer tracking timeline, driver assignment, delivery proof.

### Driver Assignment (`/admin/assignments`)

Admin assigns packages to drivers. Phase: P0. Spec: `docs/FEATURES.md`, `docs/API.md`.

- `[x]` Driver list/selector represented with mock driver profiles. (DATABASE.md)
- `[x]` Package selection table for assignable packages. (UI_UX.md)
- `[x]` Assignment form supports one or more receipts and optional admin note in mock UI. (API.md)
- `[x]` `GET /api/v1/drivers` endpoint. (API.md)
- `[x]` `POST /api/v1/assignments` endpoint. (API.md)
- `[x]` `GET /api/v1/assignments` admin review endpoint. (API.md)
- `[x]` Assignment updates package current driver and status consistently. (BACKEND.md)
- `[ ]` Tests for assigned count, unknown receipt, delivered/cancelled guard, and role authorization. (BACKEND.md)

**Connects to:** driver task list, package status, tracking.

### Driver Task List (`/driver`)

Driver sees assigned packages only. Phase: P0. Spec: `docs/FEATURES.md`, `docs/API.md`.

- `[x]` Driver dashboard with assigned package cards/list. (UI_UX.md)
- `[x]` Each task shows receipt, destination, latest status, assignment status, and admin note. (API.md)
- `[x]` Empty state pattern included for unassigned package lists. (UI_UX.md)
- `[x]` `GET /api/v1/driver/packages` endpoint scoped to current driver. (API.md)
- `[ ]` Tests prove drivers cannot see other drivers' packages. (BACKEND.md)

**Connects to:** driver proof submission, admin assignment.

### Delivery Proof (`/driver/proof/{receipt}`, `/admin/proofs`)

Driver submits and admin reviews proof of delivery. Phase: P0/P1. Spec: `docs/FEATURES.md`, `docs/API.md`, `docs/DATABASE.md`.

- `[x]` Driver proof form with required photo, delivered time, delivered location, and optional note/GPS. (UI_UX.md)
- `[ ]` Supabase Storage bucket and upload flow for delivery proof photos. (DATABASE.md)
- `[x]` `POST /api/v1/driver/packages/{receipt}/proof` endpoint. (API.md)
- `[x]` Proof submission updates assignment status to `Selesai`, package status to `Sampai Tujuan`, latest location, and package event. (BACKEND.md)
- `[x]` Admin proof review page with photo, time, location, driver, and receipt. (UI_UX.md)
- `[x]` `GET /api/v1/packages/{receipt}/proof` endpoint for admin proof review. (API.md)
- `[ ]` Tests for required proof fields and assigned-driver authorization. (BACKEND.md)

**Connects to:** customer delivered tracking summary, admin package review, storage.

### Mobile Expo App (`apps/mobile`)

Mobile tracking and driver workflow. Phase: P1. Spec: `docs/FEATURES.md`, `docs/UI_UX.md`.

- `[ ]` Create Expo app in `apps/mobile`. (ARCHITECTURE.md)
- `[ ]` Lacak tab with receipt input, validation alert, loading, and result. (UI_UX.md)
- `[ ]` Driver login/task list flow. (FEATURES: Mobile Expo App)
- `[ ]` Delivery proof capture/upload flow. (UI_UX.md)
- `[ ]` API client config for Go backend base URL. (API.md)

**Connects to:** tracking, driver packages, proof API.

## Shared Building Blocks

- `[ ]` Laravel API client wrapper with consistent envelopes and error handling. (API.md)
- `[x]` Go response envelope helpers. (BACKEND.md)
- `[ ]` Shared status labels/constants documented and aligned across apps. (API.md, DATABASE.md)
- `[x]` Supabase migrations for all P0/P1 tables, indexes, RLS, and storage bucket. (DATABASE.md)
- `[x]` Demo seed data for sample packages and package events. (DATABASE.md)
- `[ ]` Loading, empty, error, disabled, focus, hover, and active states for every interactive surface. (QUALITY.md)
- `[x]` Documentation checks after each spec change. (QUALITY.md)
- `[ ]` Payment exclusion check: no payment tables, routes, docs tasks, env vars, UI, or dependencies. (PAYMENTS.md)

## Verification Gates

- `[x]` `pnpm docs:check` passes after docs initialization. (QUALITY.md)
- `[x]` Go tests pass after backend migration. (BACKEND.md)
- `[ ]` Laravel tests/static checks pass after web migration. (QUALITY.md)
- `[ ]` Expo type/lint/smoke checks pass after mobile creation. (QUALITY.md)
- `[ ]` Render review for public/admin/driver web routes at mobile, 1366x768, 1440x900, 1920x1080. (FRONTEND.md, UI_UX.md)
- `[~]` API smoke tests cover health and public tracking against Supabase; protected admin/driver smoke waits for Auth users. (API.md)

## Done Log

- 2026-06-07 - Product docs rescoped to customer receipt tracking, admin status/driver assignment, and driver delivery proof. Payment and unrelated prototype modules removed from active scope.
- 2026-06-07 - Go backend API added with Supabase Auth login, role middleware, package tracking, admin package/status endpoints, driver assignment, driver package list, and delivery proof endpoints.
- 2026-06-07 - Laravel frontend mock added for public tracking, login, admin package/status, admin assignment, admin proof review, driver task list, and driver proof submission.
- 2026-06-07 - Product docs initialized from old FINPROPPT/TIKI Denpasar decomposition with target stack Laravel web, Go API, Supabase, and Expo mobile.
