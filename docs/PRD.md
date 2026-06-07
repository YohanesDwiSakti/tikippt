# PRD - FINPROPPT TIKI Denpasar Tracking And Driver Assignment

## Summary

FINPROPPT is a focused delivery tracking and driver assignment system for TIKI Denpasar. Customers check a receipt number and see the latest shipment status. Admins create or update receipt status and assign packages to drivers. Drivers see only the packages assigned to them and submit delivery proof when a package reaches its destination, including photo, delivery time, and delivery location.

## Target Users

- **Primary:** Customers who need to check receipt status quickly.
- **Secondary:** Admin hub staff who update receipt status and assign packages to drivers.
- **Secondary:** Drivers who need a clear task list and a way to submit proof that a package arrived at the correct destination.

## Goals

- Let customers enter a receipt number and immediately see package status, latest location, and status history.
- Let admins update receipt/package status and assign packages to a specific driver.
- Let drivers see assigned packages and submit delivery proof with photo, timestamp, and location.
- Keep Laravel web, Go API, Supabase, and Expo mobile contracts aligned around this narrow workflow.
- Keep the system production-safe: validated inputs, role authorization, migration-based schema, and no server secrets exposed to clients.

## Non-Goals

- No payment, invoice, checkout, Midtrans, refund, settlement, or payout features.
- No customer pickup scheduling in the first scope.
- No ongkir/rate calculator in the first scope.
- No support chat, claim workflow, branch management, or vehicle management in the first scope.
- No real barcode scanner in the first scope; manual receipt input is enough.
- No advanced AI route optimization.
- No full public clone of the old Vercel/Next prototype; the target stack is Laravel web, Go backend, Supabase, and Expo mobile.

## Product Principles

- **Tracking first:** every user action should improve receipt status clarity.
- **Driver accountability:** delivered packages need proof: photo, time, and location.
- **Role-appropriate access:** customers can only track by receipt; admins manage packages and assignments; drivers see their own assigned work.
- **No payment surface:** payment code, payment docs, and payment routes are not part of this project scope.
- **Simple operations before extras:** build status updates, assignments, and proof of delivery well before adding scanners, GPS automation, or reporting.
