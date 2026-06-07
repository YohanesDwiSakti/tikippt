# UI/UX Brief - FINPROPPT TIKI Denpasar

## Summary

FINPROPPT should feel like a focused logistics operations tool: customers get a fast receipt status page, admins get dense package and driver assignment controls, and drivers get a simple task list with proof submission. The interface should prioritize clarity, accountability, and speed over marketing decoration.

## Design Inputs

- **User direction:** Use Laravel for web, Go for backend, Supabase for database, and React Expo for mobile. No payment features. Focus on receipt tracking, admin status updates/driver assignment, and driver delivery proof with photo, time, and location.
- **Vertical playbook:** Logistics/operations. No dedicated vertical file was read because the user provided concrete workflow direction.
- **Closest vertical from REFERENCES.md:** Operational dashboard and delivery tracking patterns.
- **Reference products/sites:** TIKI/JNE-style tracking flows, courier task apps, shipment admin dashboards.
- **Starter design DNA to keep:** clean white surface, restrained open sections, sticky visible nav, clear footer/endcap, practical spacing.
- **Starter UI patterns not to copy:** Next.js-specific starter composition, generic SaaS hero, decorative stat strips, placeholder brand/copy.
- **Things the user explicitly likes:** The required Laravel + Go + Supabase + Expo stack and narrow operational workflow.
- **Things the user explicitly dislikes:** Payment features and using a stack outside the one specified.

## Product Personality

- **Direct:** the first action is always obvious: cek resi, update status, assign driver, or submit proof.
- **Accountable:** delivered packages show proof photo, time, and location.
- **Efficient:** admin and driver screens are optimized for scanning and repeated use.
- **Calm:** status labels, spacing, and tables carry hierarchy without heavy dark panels or noisy decoration.

## Layout Principles

- **Primary layout model:** top navigation for public tracking; sidebar or compact dashboard rail for admin; mobile bottom tabs for Expo tracking/driver tasks.
- **Page composition:** public landing starts with receipt tracking; admin uses tables/forms for package status and assignments; driver uses task cards and a proof form.
- **Surface budget:** cards are allowed for individual driver task cards, proof upload forms, and dashboard panels. Avoid wrapping every section in cards.
- **Open composition:** public tracking page stays open on a white background. Admin can use panels where data grouping matters, but avoid nested card shells.
- **Navigation placement:** route-based navigation, not same-page section jumps. Public links include home/tracking/login. Admin links include packages, assignments, proofs. Driver links include tasks and history.
- **Navigation surface:** sticky top nav with white background and border for public web. Admin can use a persistent sidebar with clear active state. Mobile uses bottom tabs.
- **First viewport:** public first screen should expose receipt input and status search without scrolling at desktop height.
- **Route connectivity:** admin and driver dashboards include a route back to public tracking/home; public/auth pages link into login.
- **Footer model:** public footer includes TIKI Denpasar help/contact/legal links; app/admin/driver routes use a compact endcap with support/legal/version-safe copy, not provider/debug details.
- **Desktop gutters:** admin pages use wide layouts with controlled gutters; public tracking uses readable max-width content.

## Visual System

- **App name for metadata:** FINPROPPT TIKI Denpasar
- **Browser title pattern:** Page name followed by `| FINPROPPT`, for example `Tracking | FINPROPPT`.
- **Icon/brand asset direction:** use a clean wordmark or approved TIKI asset if provided. Do not invent a fake logo mark.
- **Color direction:** white background, neutral gray surfaces, and a restrained TIKI-inspired accent in red/blue. Avoid cream, purple gradients, and generic dark SaaS panels.
- **Typography direction:** use a modern sans in Laravel assets. Keep weights to 400, 500/600, and 700.
- **Density and spacing:** customer tracking is medium density; admin and driver pages are denser and optimized for scanning.
- **Non-boxy hierarchy:** use section headings, table structure, status chips, inline metadata, and spacing before adding borders.
- **Rich text/scannability:** use short helper text for receipt input, status badges for package state, timestamps for tracking, and compact metadata for assignments.
- **Audience-appropriate data:** customers see receipt, status, destination, latest location, timeline, and safe delivery proof summary when delivered. Admins see package, driver, assignment, and proof data. Drivers see only assigned package tasks and proof submission state.
- **Radius and borders:** modest radius, subtle borders for data panels and inputs, no oversized rounded marketing cards.
- **Cards and surfaces:** forms, task cards, and proof panels can be card-like. Hero, tracking overview, and footer should remain open.
- **Imagery/product visuals:** use real logistics/shipping imagery only if supplied or sourced later. Proof photos are functional content, not decorative assets.
- **Icon style:** use familiar icons for search/tracking, package, driver, camera/photo, clock/time, map-pin/location, check, and cancel actions.
- **Motion:** minimal transform/opacity transitions; operational feedback should be immediate.

## Navigation Model

- `/` - public tracking-focused landing.
- `/tracking` - public receipt tracking.
- `/login` - admin/driver sign in.
- `/admin` - admin operations overview.
- `/admin/packages` - create/update package receipt and status.
- `/admin/assignments` - assign packages to drivers.
- `/admin/proofs` - review delivery proof photo/time/location.
- `/driver` - driver assigned package task list.
- `/driver/proof/{receipt}` - submit delivery proof.
- Expo mobile: `Lacak`, `Tugas Driver`, and `Bukti` flows.

## Page UX Map

| Route / Area | User goal | Primary action | Layout notes | States needed |
| --- | --- | --- | --- | --- |
| `/` | Check a package quickly | Enter receipt | Open tracking-first hero | Empty, validation, loading |
| `/tracking` | See shipment status | Search receipt | Timeline with latest status and safe proof summary | Empty, not found, loading, result |
| `/login` | Access admin/driver tools | Sign in | Focused form, role-aware redirect | Validation, loading, error |
| `/admin` | See operational snapshot | Review packages needing action | Dense summary and recent package/assignment lists | Empty data as zero, loading skeleton |
| `/admin/packages` | Create or update receipt status | Save package status | Table plus compact create/update form | Validation, success, conflict |
| `/admin/assignments` | Give packages to a driver | Assign selected receipts | Driver selector plus package selection table | Empty, validation, assigned |
| `/admin/proofs` | Verify delivered package proof | Review proof | Table/detail with photo, time, location | Empty, loading, missing proof |
| `/driver` | Know which packages to carry | Open assigned package | Mobile-friendly task cards | Empty, loading, assigned |
| `/driver/proof/{receipt}` | Confirm package arrived | Upload photo and location/time | Proof form optimized for mobile | Photo required, submitting, success |
| Expo `Lacak` | Track on mobile | Search receipt | Single-purpose tab | Alert validation, loading, result |
| Expo driver tasks | Work assigned packages | Submit proof | Task cards and proof capture | Empty, permission, upload, success |

## Components And Patterns

- **Buttons and CTAs:** primary CTAs use text labels; operational row actions may use icon + label; destructive/cancel actions are visibly distinct.
- **Navigation active states:** active route uses accent text/background and `aria-current="page"` on web.
- **Route links:** public, auth, admin, and driver contexts always provide a way back to home or role dashboard.
- **Click affordance:** rows and cards that are clickable need visible action labels or chevrons.
- **Footer/endcap:** public footer has help/contact/legal; app endcaps are compact and role-aware.
- **Cards/lists/tables:** tables for admin data; cards for mobile/driver tasks; forms use panels only when it clarifies grouping.
- **Metrics and stats:** customers do not see internal counters. Admins see operational package/assignment/proof counts only.
- **Forms:** labels are always visible; required fields are clear; validation appears near fields.
- **Empty states:** explain what data is missing and the next useful action.
- **Error states:** plain language with retry or next step where possible.
- **Loading states:** skeletons match table, card, or form result shape.

## Copy Tone

- **Voice:** direct, helpful, operational.
- **Words to use:** resi, status, driver, paket, bukti, foto, waktu, lokasi, sampai tujuan.
- **Words to avoid:** payment, invoice, checkout, Midtrans, vague marketing claims, provider/debug names, internal stack labels.
- **Example headline style:** `Cek status resi TIKI Denpasar`
- **Example button style:** `Cek Resi`, `Update Status`, `Assign Driver`, `Kirim Bukti`.

## Responsive Rules

- **Mobile:** prioritize one task per screen, large tap targets, bottom-tab pattern for Expo, and compact driver task cards.
- **Tablet:** admin tables may become stacked rows with key metadata visible.
- **Desktop:** admin uses dense tables and summary rows; public pages keep receipt search visible above the fold.

## Accessibility Notes

- **Keyboard:** all web forms, nav links, and table actions are keyboard reachable.
- **Focus states:** visible accent focus ring on every interactive element.
- **Contrast:** status chips and action buttons must pass contrast checks.
- **Motion sensitivity:** avoid non-essential animation; respect reduced motion.

## Explicit UI Non-Goals

- No payment, invoice, checkout, Midtrans, or paid/unpaid UI.
- No generic SaaS landing page detached from tracking.
- No heavy dark hero panel or purple/indigo gradient theme.
- No customer-facing internal admin metrics.
- No provider/stack/debug badges in the public UI.
- No fake logo assets pretending to be official TIKI branding.

## Sync Checklist

- [x] This brief matches `docs/PRD.md` goals and non-goals.
- [x] This brief covers all relevant `docs/FEATURES.md` modules.
- [x] This brief follows `docs/DESIGN_DNA.md` and stack-agnostic `docs/FRONTEND.md` rules.
- [x] Route/page intent here is reflected in `docs/PROGRESS.md`.
- [x] API/data needs implied by UX are reflected in `docs/API.md` and `docs/DATABASE.md`.
