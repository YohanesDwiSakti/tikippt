# Payments

Payment features are explicitly **out of scope** for FINPROPPT TIKI Denpasar.

Do not add:

- Payment or invoice screens.
- Payment, invoice, checkout, refund, settlement, or payout tables.
- Midtrans or other payment gateway integration.
- Payment webhooks.
- Payment environment variables or client keys.
- Paid/unpaid dashboard counters.
- Merchant of record decisions or payment readiness modeling.
- Seller payout flows.

If the product later needs payments, update `docs/PRD.md`, `docs/FEATURES.md`, `docs/API.md`, `docs/DATABASE.md`, `docs/PROGRESS.md`, and append a new ADR in `docs/DECISIONS.md` before implementation starts.

## Current Product Flow

The active scope is:

- Customer checks receipt status.
- Admin creates/updates package status.
- Admin assigns packages to drivers.
- Driver sees assigned packages.
- Driver submits delivery proof with photo, time, and location.

## Payment Readiness Checklist

These stay intentionally unchecked because payments are not part of this product:

- [ ] Payment model chosen.
- [ ] Webhook retries tested.
- [ ] Payment and shipment states separated.
- [ ] Admin payment reconciliation defined.

## Sync Checklist

- [x] `docs/PRD.md` marks payments out of scope.
- [x] `docs/FEATURES.md` has no payment feature module.
- [x] `docs/API.md` has no payment endpoints.
- [x] `docs/DATABASE.md` has no payment tables.
- [x] `docs/PROGRESS.md` has no payment build tasks.
