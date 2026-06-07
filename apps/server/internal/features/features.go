package features

import (
	"context"
	"errors"
	"strings"

	"github.com/jackc/pgx/v5"
	"github.com/jackc/pgx/v5/pgxpool"

	"finproppt/apps/server/internal/domain"
)

type Service struct {
	db *pgxpool.Pool
}

func NewService(db *pgxpool.Pool) *Service {
	return &Service{db: db}
}

func (s *Service) Tracking(receipt string) (TrackingResponse, error) {
	ctx := context.Background()
	pkg, err := s.packageByReceipt(ctx, receipt)
	if err != nil {
		return TrackingResponse{}, err
	}
	events, err := s.eventsByPackage(ctx, pkg.ID)
	if err != nil {
		return TrackingResponse{}, err
	}
	driverName, _ := s.driverName(ctx, pkg.CurrentDriverID)
	proof, _ := s.proofByPackage(ctx, pkg.ID)
	return trackingFrom(pkg, events, driverName, proof), nil
}

func (s *Service) ListPackages(filters PackageFilters) ([]PackageListItem, error) {
	ctx := context.Background()
	limitValue := 20
	query := `
		select id::text, receipt, destination, status, latest_location, current_driver_id::text, created_by::text, created_at::text, updated_at::text
		from public.packages
		where ($1 = '' or receipt ilike '%' || upper($1) || '%')
		  and ($2 = '' or status = $2)
		  and ($3 = '' or current_driver_id::text = $3)
		order by updated_at desc
		limit $4
	`
	rows, err := s.db.Query(ctx, query, filters.Query, filters.Status, filters.DriverID, limitValue)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	items := []PackageListItem{}
	for rows.Next() {
		pkg, err := scanPackage(rows)
		if err != nil {
			return nil, err
		}
		driverName, _ := s.driverName(ctx, pkg.CurrentDriverID)
		items = append(items, packageListItem(pkg, driverName))
	}
	return items, rows.Err()
}

func (s *Service) CreatePackage(req PackageRequest, actor domain.Profile) (PackageListItem, error) {
	if err := validatePackageRequest(req); err != nil {
		return PackageListItem{}, err
	}
	ctx := context.Background()
	tx, err := s.db.Begin(ctx)
	if err != nil {
		return PackageListItem{}, err
	}
	defer tx.Rollback(ctx)

	var pkg domain.Package
	err = tx.QueryRow(ctx, `
		insert into public.packages (receipt, destination, status, latest_location, created_by)
		values (upper($1), $2, $3, $4, $5)
		returning id::text, receipt, destination, status, latest_location, current_driver_id::text, created_by::text, created_at::text, updated_at::text
	`, req.Receipt, req.Destination, req.Status, req.LatestLocation, actor.ID).Scan(
		&pkg.ID, &pkg.Receipt, &pkg.Destination, &pkg.Status, &pkg.LatestLocation, &pkg.CurrentDriverID, &pkg.CreatedBy, &pkg.CreatedAt, &pkg.UpdatedAt,
	)
	if err != nil {
		return PackageListItem{}, err
	}
	if _, err := tx.Exec(ctx, `
		insert into public.package_events (package_id, status, location, note, created_by)
		values ($1, $2, $3, $4, $5)
	`, pkg.ID, req.Status, req.LatestLocation, req.Note, actor.ID); err != nil {
		return PackageListItem{}, err
	}
	if err := tx.Commit(ctx); err != nil {
		return PackageListItem{}, err
	}
	return packageListItem(pkg, nil), nil
}

func (s *Service) UpdatePackage(receipt string, req PackageUpdateRequest, actor domain.Profile) (PackageListItem, error) {
	if strings.TrimSpace(req.Status) == "" || strings.TrimSpace(req.LatestLocation) == "" {
		return PackageListItem{}, ErrValidation
	}
	if !domain.PackageStatuses[req.Status] {
		return PackageListItem{}, ErrInvalidStatus
	}
	ctx := context.Background()
	tx, err := s.db.Begin(ctx)
	if err != nil {
		return PackageListItem{}, err
	}
	defer tx.Rollback(ctx)

	pkg, err := packageByReceiptTx(ctx, tx, receipt)
	if err != nil {
		return PackageListItem{}, err
	}

	err = tx.QueryRow(ctx, `
		update public.packages
		set status = $2, latest_location = $3, updated_at = now()
		where id = $1
		returning id::text, receipt, destination, status, latest_location, current_driver_id::text, created_by::text, created_at::text, updated_at::text
	`, pkg.ID, req.Status, req.LatestLocation).Scan(
		&pkg.ID, &pkg.Receipt, &pkg.Destination, &pkg.Status, &pkg.LatestLocation, &pkg.CurrentDriverID, &pkg.CreatedBy, &pkg.CreatedAt, &pkg.UpdatedAt,
	)
	if err != nil {
		return PackageListItem{}, err
	}
	if _, err := tx.Exec(ctx, `
		insert into public.package_events (package_id, status, location, note, created_by)
		values ($1, $2, $3, $4, $5)
	`, pkg.ID, req.Status, req.LatestLocation, req.Note, actor.ID); err != nil {
		return PackageListItem{}, err
	}
	if err := tx.Commit(ctx); err != nil {
		return PackageListItem{}, err
	}
	return packageListItem(pkg, nil), nil
}

func (s *Service) ListDrivers() ([]domain.Profile, error) {
	ctx := context.Background()
	rows, err := s.db.Query(ctx, `
		select id::text, name, email, role, created_at::text, updated_at::text
		from public.profiles
		where role = 'driver'
		order by name asc
	`)
	if err != nil {
		return nil, err
	}
	defer rows.Close()
	drivers := []domain.Profile{}
	for rows.Next() {
		var driver domain.Profile
		if err := rows.Scan(&driver.ID, &driver.Name, &driver.Email, &driver.Role, &driver.CreatedAt, &driver.UpdatedAt); err != nil {
			return nil, err
		}
		drivers = append(drivers, driver)
	}
	return drivers, rows.Err()
}

func (s *Service) Assign(req AssignmentRequest, actor domain.Profile) (AssignmentResponse, error) {
	if strings.TrimSpace(req.DriverID) == "" || len(req.Receipts) == 0 {
		return AssignmentResponse{}, ErrValidation
	}
	ctx := context.Background()
	tx, err := s.db.Begin(ctx)
	if err != nil {
		return AssignmentResponse{}, err
	}
	defer tx.Rollback(ctx)

	if ok, err := driverExists(ctx, tx, req.DriverID); err != nil || !ok {
		return AssignmentResponse{}, ErrDriverNotFound
	}

	assigned := 0
	for _, receipt := range req.Receipts {
		pkg, err := packageByReceiptTx(ctx, tx, receipt)
		if err != nil {
			return AssignmentResponse{}, err
		}
		if pkg.Status == "Sampai Tujuan" || pkg.Status == "Cancel" {
			return AssignmentResponse{}, ErrPackageLocked
		}
		if _, err := tx.Exec(ctx, `
			insert into public.driver_assignments (package_id, driver_id, assigned_by, status, note)
			values ($1, $2, $3, 'Ditugaskan', $4)
		`, pkg.ID, req.DriverID, actor.ID, req.Note); err != nil {
			return AssignmentResponse{}, err
		}
		if _, err := tx.Exec(ctx, `
			update public.packages
			set current_driver_id = $2, status = 'Diangkut Driver', updated_at = now()
			where id = $1
		`, pkg.ID, req.DriverID); err != nil {
			return AssignmentResponse{}, err
		}
		if _, err := tx.Exec(ctx, `
			insert into public.package_events (package_id, status, location, note, created_by)
			values ($1, 'Diangkut Driver', $2, $3, $4)
		`, pkg.ID, pkg.LatestLocation, req.Note, actor.ID); err != nil {
			return AssignmentResponse{}, err
		}
		assigned++
	}

	if err := tx.Commit(ctx); err != nil {
		return AssignmentResponse{}, err
	}
	return AssignmentResponse{DriverID: req.DriverID, AssignedCount: assigned, Status: "Ditugaskan"}, nil
}

func (s *Service) ListAssignments() ([]AssignmentListItem, error) {
	ctx := context.Background()
	rows, err := s.db.Query(ctx, `
		select da.id::text, p.receipt, pr.name, da.status, da.assigned_at::text, da.note
		from public.driver_assignments da
		join public.packages p on p.id = da.package_id
		join public.profiles pr on pr.id = da.driver_id
		order by da.assigned_at desc
		limit 100
	`)
	if err != nil {
		return nil, err
	}
	defer rows.Close()
	items := []AssignmentListItem{}
	for rows.Next() {
		var item AssignmentListItem
		if err := rows.Scan(&item.ID, &item.Receipt, &item.DriverName, &item.Status, &item.AssignedAt, &item.Note); err != nil {
			return nil, err
		}
		items = append(items, item)
	}
	return items, rows.Err()
}

func (s *Service) DriverPackages(driver domain.Profile) ([]DriverPackageItem, error) {
	ctx := context.Background()
	rows, err := s.db.Query(ctx, `
		select p.receipt, p.destination, p.status, p.latest_location, da.status, da.assigned_at::text, da.note
		from public.driver_assignments da
		join public.packages p on p.id = da.package_id
		where da.driver_id = $1
		order by da.assigned_at desc
	`, driver.ID)
	if err != nil {
		return nil, err
	}
	defer rows.Close()
	items := []DriverPackageItem{}
	for rows.Next() {
		var item DriverPackageItem
		if err := rows.Scan(&item.Receipt, &item.Destination, &item.Status, &item.LatestLocation, &item.AssignmentStatus, &item.AssignedAt, &item.AdminNote); err != nil {
			return nil, err
		}
		items = append(items, item)
	}
	return items, rows.Err()
}

func (s *Service) SubmitProof(receipt string, req ProofRequest, driver domain.Profile) (ProofResponse, error) {
	if strings.TrimSpace(req.PhotoURL) == "" || strings.TrimSpace(req.DeliveredAt) == "" || strings.TrimSpace(req.DeliveredLocation) == "" {
		return ProofResponse{}, ErrValidation
	}
	ctx := context.Background()
	tx, err := s.db.Begin(ctx)
	if err != nil {
		return ProofResponse{}, err
	}
	defer tx.Rollback(ctx)

	pkg, err := packageByReceiptTx(ctx, tx, receipt)
	if err != nil {
		return ProofResponse{}, err
	}
	assignment, err := activeAssignment(ctx, tx, pkg.ID, driver.ID)
	if err != nil {
		return ProofResponse{}, err
	}

	var proof domain.DeliveryProof
	err = tx.QueryRow(ctx, `
		insert into public.delivery_proofs (package_id, assignment_id, driver_id, photo_url, delivered_at, delivered_location, latitude, longitude, note)
		values ($1, $2, $3, $4, $5, $6, $7, $8, $9)
		returning id::text, package_id::text, assignment_id::text, driver_id::text, photo_url, delivered_at::text, delivered_location, latitude, longitude, note, created_at::text
	`, pkg.ID, assignment.ID, driver.ID, req.PhotoURL, req.DeliveredAt, req.DeliveredLocation, req.Latitude, req.Longitude, req.Note).Scan(
		&proof.ID, &proof.PackageID, &proof.AssignmentID, &proof.DriverID, &proof.PhotoURL, &proof.DeliveredAt, &proof.DeliveredLocation, &proof.Latitude, &proof.Longitude, &proof.Note, &proof.CreatedAt,
	)
	if err != nil {
		return ProofResponse{}, err
	}
	if _, err := tx.Exec(ctx, `update public.driver_assignments set status = 'Selesai', updated_at = now() where id = $1`, assignment.ID); err != nil {
		return ProofResponse{}, err
	}
	if _, err := tx.Exec(ctx, `update public.packages set status = 'Sampai Tujuan', latest_location = $2, updated_at = now() where id = $1`, pkg.ID, req.DeliveredLocation); err != nil {
		return ProofResponse{}, err
	}
	if _, err := tx.Exec(ctx, `
		insert into public.package_events (package_id, status, location, note, created_by)
		values ($1, 'Sampai Tujuan', $2, $3, $4)
	`, pkg.ID, req.DeliveredLocation, req.Note, driver.ID); err != nil {
		return ProofResponse{}, err
	}
	if err := tx.Commit(ctx); err != nil {
		return ProofResponse{}, err
	}
	return ProofResponse{Receipt: pkg.Receipt, Status: "Sampai Tujuan", Proof: proof}, nil
}

func (s *Service) Proof(receipt string) (ProofResponse, error) {
	ctx := context.Background()
	pkg, err := s.packageByReceipt(ctx, receipt)
	if err != nil {
		return ProofResponse{}, err
	}
	proof, err := s.proofByPackage(ctx, pkg.ID)
	if err != nil {
		return ProofResponse{}, err
	}
	return ProofResponse{Receipt: pkg.Receipt, Status: pkg.Status, Proof: proof}, nil
}

func (s *Service) packageByReceipt(ctx context.Context, receipt string) (domain.Package, error) {
	rows, err := s.db.Query(ctx, `
		select id::text, receipt, destination, status, latest_location, current_driver_id::text, created_by::text, created_at::text, updated_at::text
		from public.packages
		where receipt = upper($1)
		limit 1
	`, receipt)
	if err != nil {
		return domain.Package{}, err
	}
	defer rows.Close()
	if !rows.Next() {
		return domain.Package{}, ErrPackageNotFound
	}
	return scanPackage(rows)
}

func (s *Service) eventsByPackage(ctx context.Context, packageID string) ([]domain.PackageEvent, error) {
	rows, err := s.db.Query(ctx, `
		select id::text, package_id::text, status, location, note, created_by::text, created_at::text
		from public.package_events
		where package_id = $1
		order by created_at asc
	`, packageID)
	if err != nil {
		return nil, err
	}
	defer rows.Close()
	events := []domain.PackageEvent{}
	for rows.Next() {
		var event domain.PackageEvent
		if err := rows.Scan(&event.ID, &event.PackageID, &event.Status, &event.Location, &event.Note, &event.CreatedBy, &event.CreatedAt); err != nil {
			return nil, err
		}
		events = append(events, event)
	}
	return events, rows.Err()
}

func (s *Service) proofByPackage(ctx context.Context, packageID string) (domain.DeliveryProof, error) {
	var proof domain.DeliveryProof
	err := s.db.QueryRow(ctx, `
		select id::text, package_id::text, assignment_id::text, driver_id::text, photo_url, delivered_at::text, delivered_location, latitude, longitude, note, created_at::text
		from public.delivery_proofs
		where package_id = $1
		order by created_at desc
		limit 1
	`, packageID).Scan(&proof.ID, &proof.PackageID, &proof.AssignmentID, &proof.DriverID, &proof.PhotoURL, &proof.DeliveredAt, &proof.DeliveredLocation, &proof.Latitude, &proof.Longitude, &proof.Note, &proof.CreatedAt)
	if errors.Is(err, pgx.ErrNoRows) {
		return domain.DeliveryProof{}, ErrProofNotFound
	}
	return proof, err
}

func (s *Service) driverName(ctx context.Context, driverID *string) (*string, error) {
	if driverID == nil || *driverID == "" {
		return nil, nil
	}
	var name string
	err := s.db.QueryRow(ctx, `select name from public.profiles where id = $1`, *driverID).Scan(&name)
	return &name, err
}

func packageByReceiptTx(ctx context.Context, tx pgx.Tx, receipt string) (domain.Package, error) {
	rows, err := tx.Query(ctx, `
		select id::text, receipt, destination, status, latest_location, current_driver_id::text, created_by::text, created_at::text, updated_at::text
		from public.packages
		where receipt = upper($1)
		limit 1
	`, receipt)
	if err != nil {
		return domain.Package{}, err
	}
	defer rows.Close()
	if !rows.Next() {
		return domain.Package{}, ErrPackageNotFound
	}
	return scanPackage(rows)
}

func driverExists(ctx context.Context, tx pgx.Tx, driverID string) (bool, error) {
	var exists bool
	err := tx.QueryRow(ctx, `select exists(select 1 from public.profiles where id = $1 and role = 'driver')`, driverID).Scan(&exists)
	return exists, err
}

func activeAssignment(ctx context.Context, tx pgx.Tx, packageID, driverID string) (domain.DriverAssignment, error) {
	var assignment domain.DriverAssignment
	err := tx.QueryRow(ctx, `
		select id::text, package_id::text, driver_id::text, assigned_by::text, status, note, assigned_at::text, updated_at::text
		from public.driver_assignments
		where package_id = $1 and driver_id = $2 and status <> 'Dibatalkan'
		order by assigned_at desc
		limit 1
	`, packageID, driverID).Scan(&assignment.ID, &assignment.PackageID, &assignment.DriverID, &assignment.AssignedBy, &assignment.Status, &assignment.Note, &assignment.AssignedAt, &assignment.UpdatedAt)
	if errors.Is(err, pgx.ErrNoRows) {
		return domain.DriverAssignment{}, ErrAssignmentNotFound
	}
	return assignment, err
}

func scanPackage(rows pgx.Rows) (domain.Package, error) {
	var pkg domain.Package
	err := rows.Scan(&pkg.ID, &pkg.Receipt, &pkg.Destination, &pkg.Status, &pkg.LatestLocation, &pkg.CurrentDriverID, &pkg.CreatedBy, &pkg.CreatedAt, &pkg.UpdatedAt)
	return pkg, err
}

func validatePackageRequest(req PackageRequest) error {
	if strings.TrimSpace(req.Receipt) == "" || strings.TrimSpace(req.Destination) == "" || strings.TrimSpace(req.Status) == "" || strings.TrimSpace(req.LatestLocation) == "" {
		return ErrValidation
	}
	if !domain.PackageStatuses[req.Status] {
		return ErrInvalidStatus
	}
	return nil
}

func trackingFrom(pkg domain.Package, events []domain.PackageEvent, driverName *string, proof domain.DeliveryProof) TrackingResponse {
	timeline := make([]TimelineItem, 0, len(events))
	for _, event := range events {
		timeline = append(timeline, TimelineItem{Status: event.Status, Location: event.Location, Note: event.Note, CreatedAt: event.CreatedAt})
	}
	var proofSummary *DeliveryProofSummary
	if proof.ID != "" {
		proofSummary = &DeliveryProofSummary{PhotoURL: proof.PhotoURL, DeliveredAt: proof.DeliveredAt, DeliveredLocation: proof.DeliveredLocation}
	}
	return TrackingResponse{Receipt: pkg.Receipt, Status: pkg.Status, Destination: pkg.Destination, LatestLocation: pkg.LatestLocation, DriverName: driverName, UpdatedAt: pkg.UpdatedAt, DeliveryProof: proofSummary, Timeline: timeline}
}

func packageListItem(pkg domain.Package, driverName *string) PackageListItem {
	return PackageListItem{ID: pkg.ID, Receipt: pkg.Receipt, Destination: pkg.Destination, Status: pkg.Status, LatestLocation: pkg.LatestLocation, DriverID: pkg.CurrentDriverID, DriverName: driverName, CreatedAt: pkg.CreatedAt, UpdatedAt: pkg.UpdatedAt}
}

type sentinelError string

func (e sentinelError) Error() string { return string(e) }

const (
	ErrValidation         sentinelError = "validation error"
	ErrInvalidStatus      sentinelError = "invalid status"
	ErrPackageNotFound    sentinelError = "package not found"
	ErrDriverNotFound     sentinelError = "driver not found"
	ErrAssignmentNotFound sentinelError = "assignment not found"
	ErrPackageLocked      sentinelError = "package locked"
	ErrProofNotFound      sentinelError = "proof not found"
)

func ErrorStatus(err error) (int, string, string) {
	switch {
	case errors.Is(err, ErrValidation):
		return 400, "VALIDATION_ERROR", "Please check the required fields."
	case errors.Is(err, ErrInvalidStatus):
		return 400, "INVALID_STATUS", "The requested status is not allowed."
	case errors.Is(err, ErrPackageNotFound):
		return 404, "RECEIPT_NOT_FOUND", "No package was found for this receipt."
	case errors.Is(err, ErrDriverNotFound):
		return 404, "DRIVER_NOT_FOUND", "No driver was found for this id."
	case errors.Is(err, ErrAssignmentNotFound):
		return 403, "ASSIGNMENT_NOT_FOUND", "This package is not assigned to the current driver."
	case errors.Is(err, ErrPackageLocked):
		return 409, "PACKAGE_LOCKED", "Delivered or cancelled packages cannot be reassigned."
	case errors.Is(err, ErrProofNotFound):
		return 404, "PROOF_NOT_FOUND", "No delivery proof was found for this receipt."
	default:
		return 500, "SERVER_ERROR", "The server could not complete the request."
	}
}
