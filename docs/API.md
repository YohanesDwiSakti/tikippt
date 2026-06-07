# API Contract

## Conventions

- Backend API is implemented in **Go** under `apps/server`.
- Base URL: `/api/v1`.
- Content-Type: `application/json`.
- Success responses use `{ "data": ... }`.
- Error responses use `{ "error": { "code": "...", "message": "..." } }`.
- Public endpoints are explicitly marked. Protected endpoints require a bearer token or a documented Laravel/session-to-API auth bridge once auth is implemented.
- Go validates params, query, and body at the boundary before business logic runs.
- Laravel web and Expo mobile consume this API over HTTP. They should not maintain independent business rules that can drift from this contract.

## Standard Status Codes

| Code | Meaning |
| --- | --- |
| 200 | OK |
| 201 | Created |
| 400 | Validation error |
| 401 | Unauthenticated |
| 403 | Unauthorized |
| 404 | Not found |
| 409 | Conflict |
| 500 | Server error |

## Common Status Values

Package statuses:

- `Terdaftar`
- `Diangkut Driver`
- `Dalam Perjalanan`
- `Sampai Tujuan`
- `Gagal Dikirim`
- `Cancel`

Assignment statuses:

- `Ditugaskan`
- `Diambil Driver`
- `Dalam Perjalanan`
- `Selesai`
- `Gagal`
- `Dibatalkan`

Proof status:

- `Terkirim`

## Public Endpoints

### `GET /api/v1/health`

Check that the API is running.

Response `200`:

```json
{ "data": { "status": "ok", "message": "backend ready" } }
```

Golden path compatibility: while the repository still carries the template TypeScript package checks, the equivalent contract name is `healthResponseSchema`. During Go migration, keep the health response shape compatible or update the docs check and generated contract artifacts together.

### `GET /api/v1/tracking/{receipt}`

Track a receipt.

Response `200`:

```json
{
  "data": {
    "receipt": "TKI-DEN-260607101500",
    "status": "Dalam Perjalanan",
    "destination": "Gianyar",
    "latest_location": "Hub Denpasar",
    "driver_name": "Made Driver",
    "updated_at": "2026-06-07T10:15:00+08:00",
    "delivery_proof": null,
    "timeline": [
      {
        "status": "Terdaftar",
        "location": "Hub Denpasar",
        "note": "Paket terdaftar",
        "created_at": "2026-06-07T09:30:00+08:00"
      }
    ]
  }
}
```

Delivered response includes proof summary:

```json
{
  "data": {
    "receipt": "TKI-DEN-260607101500",
    "status": "Sampai Tujuan",
    "destination": "Gianyar",
    "latest_location": "Alamat penerima, Gianyar",
    "driver_name": "Made Driver",
    "updated_at": "2026-06-07T13:20:00+08:00",
    "delivery_proof": {
      "photo_url": "https://...",
      "delivered_at": "2026-06-07T13:20:00+08:00",
      "delivered_location": "Alamat penerima, Gianyar"
    },
    "timeline": []
  }
}
```

Rules:

- `receipt` is required and normalized to uppercase.
- Return `404` with `RECEIPT_NOT_FOUND` when no package exists.
- Customer response never exposes internal admin notes beyond shipment-safe status information.

## Auth Endpoints

### `POST /api/v1/auth/login`

Admin or driver login.

Body:

```json
{ "email": "admin@tiki.test", "password": "admin123", "role": "admin" }
```

Response `200`:

```json
{
  "data": {
    "user": { "id": "uuid", "name": "Admin Hub", "email": "admin@tiki.test", "role": "admin" },
    "token": "session-or-api-token"
  }
}
```

## Package Endpoints

### `GET /api/v1/packages`

Protected admin endpoint. Returns paginated packages.

Query:

- `limit` optional, default `20`, max `100`.
- `cursor` optional.
- `q` optional receipt search.
- `status` optional.
- `driver_id` optional.

### `POST /api/v1/packages`

Protected admin endpoint. Create a package/receipt record.

Body:

```json
{
  "receipt": "TKI-DEN-260607101500",
  "destination": "Gianyar",
  "status": "Terdaftar",
  "latest_location": "Hub Denpasar",
  "note": "Paket masuk hub"
}
```

Rules:

- `receipt`, `destination`, `status`, and `latest_location` are required.
- `receipt` is stored uppercase.
- Creates an initial package event.

### `PATCH /api/v1/packages/{receipt}`

Protected admin endpoint. Update package status and location.

Body:

```json
{
  "status": "Dalam Perjalanan",
  "latest_location": "Keluar Hub Denpasar",
  "note": "Paket dibawa driver"
}
```

Rules:

- Status and latest location are required.
- Updates the package and creates a package event.

## Driver Assignment Endpoints

### `GET /api/v1/drivers`

Protected admin endpoint. List available drivers.

### `POST /api/v1/assignments`

Protected admin endpoint. Assign one or more packages to a driver.

Body:

```json
{
  "driver_id": "uuid",
  "receipts": ["TKI-DEN-260607101500", "TKI-DEN-260607101501"],
  "note": "Paket rute Gianyar"
}
```

Response `201`:

```json
{
  "data": {
    "driver_id": "uuid",
    "assigned_count": 2,
    "status": "Ditugaskan"
  }
}
```

Rules:

- `driver_id` and at least one receipt are required.
- Receipts are normalized to uppercase.
- Assignment updates package status to `Diangkut Driver` unless a package is already delivered or cancelled.
- Reassignment requires admin role.

### `GET /api/v1/assignments`

Protected admin endpoint. List assignments by driver, package, status, or date.

### `GET /api/v1/driver/packages`

Protected driver endpoint. Returns packages assigned to the current driver.

Response `200`:

```json
{
  "data": [
    {
      "receipt": "TKI-DEN-260607101500",
      "destination": "Gianyar",
      "status": "Diangkut Driver",
      "latest_location": "Hub Denpasar",
      "assignment_status": "Ditugaskan",
      "assigned_at": "2026-06-07T10:00:00+08:00",
      "admin_note": "Paket rute Gianyar"
    }
  ]
}
```

## Delivery Proof Endpoints

### `POST /api/v1/driver/packages/{receipt}/proof`

Protected driver endpoint. Submit proof that an assigned package arrived at the destination.

Body:

```json
{
  "photo_url": "https://...",
  "delivered_at": "2026-06-07T13:20:00+08:00",
  "delivered_location": "Alamat penerima, Gianyar",
  "latitude": -8.544,
  "longitude": 115.325,
  "note": "Diterima oleh penerima"
}
```

Rules:

- Driver must be assigned to the receipt.
- `photo_url`, `delivered_at`, and `delivered_location` are required.
- Latitude/longitude are optional in MVP, but allowed when Expo can capture them.
- Updates assignment to `Selesai`.
- Updates package status to `Sampai Tujuan`.
- Creates a package event.

### `GET /api/v1/packages/{receipt}/proof`

Protected admin endpoint. Review delivery proof for a package.

## Explicitly Not Provided

The API has no payment, invoice, checkout, Midtrans, refund, settlement, support, claim, pickup scheduling, branch, vehicle, or rate-calculator endpoints in this project scope.
