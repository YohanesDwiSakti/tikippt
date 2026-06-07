package features

import "finproppt/apps/server/internal/domain"

type TrackingResponse struct {
	Receipt        string                `json:"receipt"`
	Status         string                `json:"status"`
	Destination    string                `json:"destination"`
	LatestLocation string                `json:"latest_location"`
	DriverName     *string               `json:"driver_name"`
	UpdatedAt      string                `json:"updated_at"`
	DeliveryProof  *DeliveryProofSummary `json:"delivery_proof"`
	Timeline       []TimelineItem        `json:"timeline"`
}

type TimelineItem struct {
	Status    string  `json:"status"`
	Location  string  `json:"location"`
	Note      *string `json:"note"`
	CreatedAt string  `json:"created_at"`
}

type DeliveryProofSummary struct {
	PhotoURL          string `json:"photo_url"`
	DeliveredAt       string `json:"delivered_at"`
	DeliveredLocation string `json:"delivered_location"`
}

type PackageFilters struct {
	Limit    string
	Query    string
	Status   string
	DriverID string
}

type PackageRequest struct {
	Receipt        string  `json:"receipt"`
	Destination    string  `json:"destination"`
	Status         string  `json:"status"`
	LatestLocation string  `json:"latest_location"`
	Note           *string `json:"note"`
}

type PackageUpdateRequest struct {
	Status         string  `json:"status"`
	LatestLocation string  `json:"latest_location"`
	Note           *string `json:"note"`
}

type PackageListItem struct {
	ID             string  `json:"id"`
	Receipt        string  `json:"receipt"`
	Destination    string  `json:"destination"`
	Status         string  `json:"status"`
	LatestLocation string  `json:"latest_location"`
	DriverID       *string `json:"driver_id"`
	DriverName     *string `json:"driver_name"`
	CreatedAt      string  `json:"created_at"`
	UpdatedAt      string  `json:"updated_at"`
}

type AssignmentRequest struct {
	DriverID string   `json:"driver_id"`
	Receipts []string `json:"receipts"`
	Note     *string  `json:"note"`
}

type AssignmentResponse struct {
	DriverID      string `json:"driver_id"`
	AssignedCount int    `json:"assigned_count"`
	Status        string `json:"status"`
}

type AssignmentListItem struct {
	ID         string  `json:"id"`
	Receipt    string  `json:"receipt"`
	DriverName string  `json:"driver_name"`
	Status     string  `json:"status"`
	AssignedAt string  `json:"assigned_at"`
	Note       *string `json:"note"`
}

type DriverPackageItem struct {
	Receipt          string  `json:"receipt"`
	Destination      string  `json:"destination"`
	Status           string  `json:"status"`
	LatestLocation   string  `json:"latest_location"`
	AssignmentStatus string  `json:"assignment_status"`
	AssignedAt       string  `json:"assigned_at"`
	AdminNote        *string `json:"admin_note"`
}

type ProofRequest struct {
	PhotoURL          string   `json:"photo_url"`
	DeliveredAt       string   `json:"delivered_at"`
	DeliveredLocation string   `json:"delivered_location"`
	Latitude          *float64 `json:"latitude"`
	Longitude         *float64 `json:"longitude"`
	Note              *string  `json:"note"`
}

type ProofResponse struct {
	Receipt string               `json:"receipt"`
	Status  string               `json:"status"`
	Proof   domain.DeliveryProof `json:"proof"`
}
