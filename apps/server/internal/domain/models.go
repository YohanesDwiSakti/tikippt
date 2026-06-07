package domain

type Profile struct {
	ID        string `json:"id"`
	Name      string `json:"name"`
	Email     string `json:"email"`
	Role      string `json:"role"`
	CreatedAt string `json:"created_at,omitempty"`
	UpdatedAt string `json:"updated_at,omitempty"`
}

type Package struct {
	ID              string  `json:"id"`
	Receipt         string  `json:"receipt"`
	Destination     string  `json:"destination"`
	Status          string  `json:"status"`
	LatestLocation  string  `json:"latest_location"`
	CurrentDriverID *string `json:"current_driver_id"`
	CreatedBy       *string `json:"created_by"`
	CreatedAt       string  `json:"created_at"`
	UpdatedAt       string  `json:"updated_at"`
}

type PackageEvent struct {
	ID        string  `json:"id"`
	PackageID string  `json:"package_id"`
	Status    string  `json:"status"`
	Location  string  `json:"location"`
	Note      *string `json:"note"`
	CreatedBy *string `json:"created_by"`
	CreatedAt string  `json:"created_at"`
}

type DriverAssignment struct {
	ID         string  `json:"id"`
	PackageID  string  `json:"package_id"`
	DriverID   string  `json:"driver_id"`
	AssignedBy string  `json:"assigned_by"`
	Status     string  `json:"status"`
	Note       *string `json:"note"`
	AssignedAt string  `json:"assigned_at"`
	UpdatedAt  string  `json:"updated_at"`
}

type DeliveryProof struct {
	ID                string   `json:"id"`
	PackageID         string   `json:"package_id"`
	AssignmentID      string   `json:"assignment_id"`
	DriverID          string   `json:"driver_id"`
	PhotoURL          string   `json:"photo_url"`
	DeliveredAt       string   `json:"delivered_at"`
	DeliveredLocation string   `json:"delivered_location"`
	Latitude          *float64 `json:"latitude"`
	Longitude         *float64 `json:"longitude"`
	Note              *string  `json:"note"`
	CreatedAt         string   `json:"created_at"`
}

const (
	RoleAdmin  = "admin"
	RoleDriver = "driver"
)

var PackageStatuses = map[string]bool{
	"Terdaftar":        true,
	"Diangkut Driver":  true,
	"Dalam Perjalanan": true,
	"Sampai Tujuan":    true,
	"Gagal Dikirim":    true,
	"Cancel":           true,
}

var AssignmentStatuses = map[string]bool{
	"Ditugaskan":       true,
	"Diambil Driver":   true,
	"Dalam Perjalanan": true,
	"Selesai":          true,
	"Gagal":            true,
	"Dibatalkan":       true,
}
