# Features - FINPROPPT TIKI Denpasar

> Scope is bounded by `docs/PRD.md`. Nothing here may contradict the PRD Non-Goals.

## Build Phases

- **P0** - Core tracking loop: auth, customer tracking, admin receipt/status management, driver assignment, driver task list, delivery proof, Supabase persistence.
- **P1** - Operational completeness: better filters, status history, assignment notes, delivery proof review, mobile driver flow.
- **P2** - Depth and polish: scanner support, optional GPS capture, reporting, audit logs, notifications.

## 1. Authentication And Roles

Role-aware access for admin hub and driver users. Customer tracking can stay public by receipt.

- Sign in admin and driver users with validated credentials. `P0`
- Redirect admin and driver users to the correct dashboard. `P0`
- Seed demo admin and driver accounts for testing. `P0`
- Hash passwords or use Supabase Auth before production. `P0`
- Enforce endpoint authorization by role. `P0`
- Add password reset and profile management. `P2`

## 2. Customer Receipt Tracking

Customer-facing tracking by receipt number.

- Track receipt by receipt number and show status, latest location, assigned/delivery information when available, and updated time. `P0`
- Show status timeline/history for the receipt. `P0`
- Show clear validation and not-found states. `P0`
- Keep customer view limited to shipment status, not admin/driver internal controls. `P0`

## 3. Admin Package Management

Admin tools to create/update receipt records and keep package status current.

- Create package/receipt records with receipt number, destination, status, and latest location. `P0`
- Update package status and latest location by receipt. `P0`
- Normalize receipt numbers to uppercase. `P0`
- List and filter packages by status, driver, receipt, and date. `P1`
- Write a status history event for every status update. `P0`
- Add audit logs for admin status changes. `P2`

## 4. Driver Assignment

Admin assigns packages to drivers so drivers know what to carry.

- Create driver profiles/users. `P0`
- Assign one or more packages to a driver. `P0`
- Store assignment status, assigned time, and optional admin note. `P0`
- Let admin reassign a package before delivery when needed. `P1`
- Let admin review which packages each driver is carrying. `P0`

## 5. Driver Task Workflow

Driver-facing workflow for assigned packages and delivery proof.

- Show each driver only their assigned package list. `P0`
- Show package receipt, destination, latest status, admin note, and assignment time. `P0`
- Let driver mark package as picked up/in transit when appropriate. `P1`
- Let driver submit delivered status with proof photo, delivered time, and delivery location/address. `P0`
- Update package status to delivered after valid proof submission. `P0`
- Let admin review proof photo, time, and location for delivered packages. `P1`
- Add mobile Expo driver task and proof flow. `P1`

## 6. Mobile Expo App

Mobile surface for tracking and driver work.

- Provide a tracking tab that calls the Go API. `P1`
- Provide a driver login/task list. `P1`
- Provide delivery proof capture/upload flow. `P1`
- Show validation alerts and success/error results. `P1`

## 7. Platform Foundation

Cross-cutting architecture, contracts, data, and quality work.

- Use Laravel for the web frontend/application surface. `P0`
- Use Go for backend API services. `P0`
- Use Supabase PostgreSQL as the source of truth. `P0`
- Use Supabase Storage for delivery proof photos. `P0`
- Keep API contracts documented in `docs/API.md` and implemented consistently in Go, Laravel, and Expo clients. `P0`
- Keep schema changes migration-based and documented in `docs/DATABASE.md`. `P0`
- Add automated tests for API validation and status/assignment/proof transitions. `P1`

## Explicitly Out Of Scope

- Payment, invoice, checkout, Midtrans, refund, settlement, and payout features.
- Customer pickup scheduling.
- Ongkir/rate calculator.
- Support chat and claims.
- Branch and vehicle management.
- Real barcode scanner for MVP.
- Advanced AI route optimization.
- Rebuilding the old Vercel/Next prototype as the target production web app.
