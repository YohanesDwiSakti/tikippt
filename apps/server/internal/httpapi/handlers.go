package httpapi

import (
	"errors"
	"net/http"
	"strings"

	"finproppt/apps/server/internal/auth"
	"finproppt/apps/server/internal/domain"
	"finproppt/apps/server/internal/features"
	"finproppt/apps/server/internal/platform"
)

type Server struct {
	auth     *auth.Service
	features *features.Service
}

func NewServer(authService *auth.Service, featureService *features.Service) *Server {
	return &Server{auth: authService, features: featureService}
}

func (s *Server) Routes(corsOrigin string) http.Handler {
	mux := http.NewServeMux()

	mux.HandleFunc("GET /api/v1/health", s.health)
	mux.HandleFunc("GET /api/v1/tracking/{receipt}", s.tracking)
	mux.HandleFunc("POST /api/v1/auth/login", s.login)

	admin := auth.Middleware(s.auth, domain.RoleAdmin)
	driver := auth.Middleware(s.auth, domain.RoleDriver)

	mux.Handle("GET /api/v1/packages", admin(http.HandlerFunc(s.listPackages)))
	mux.Handle("POST /api/v1/packages", admin(http.HandlerFunc(s.createPackage)))
	mux.Handle("PATCH /api/v1/packages/{receipt}", admin(http.HandlerFunc(s.updatePackage)))
	mux.Handle("GET /api/v1/drivers", admin(http.HandlerFunc(s.listDrivers)))
	mux.Handle("POST /api/v1/assignments", admin(http.HandlerFunc(s.assign)))
	mux.Handle("GET /api/v1/assignments", admin(http.HandlerFunc(s.listAssignments)))
	mux.Handle("GET /api/v1/packages/{receipt}/proof", admin(http.HandlerFunc(s.proof)))

	mux.Handle("GET /api/v1/driver/packages", driver(http.HandlerFunc(s.driverPackages)))
	mux.Handle("POST /api/v1/driver/packages/{receipt}/proof", driver(http.HandlerFunc(s.submitProof)))

	return platform.CORS(corsOrigin)(mux)
}

func (s *Server) health(w http.ResponseWriter, _ *http.Request) {
	platform.WriteData(w, http.StatusOK, map[string]string{"status": "ok", "message": "backend ready"})
}

func (s *Server) tracking(w http.ResponseWriter, r *http.Request) {
	data, err := s.features.Tracking(r.PathValue("receipt"))
	if err != nil {
		writeFeatureError(w, err)
		return
	}
	platform.WriteData(w, http.StatusOK, data)
}

func (s *Server) login(w http.ResponseWriter, r *http.Request) {
	var req auth.LoginRequest
	if err := platform.DecodeJSON(r, &req); err != nil {
		platform.WriteError(w, http.StatusBadRequest, "VALIDATION_ERROR", "Invalid login payload.")
		return
	}
	if strings.TrimSpace(req.Email) == "" || strings.TrimSpace(req.Password) == "" || strings.TrimSpace(req.Role) == "" {
		platform.WriteError(w, http.StatusBadRequest, "VALIDATION_ERROR", "Email, password, and role are required.")
		return
	}

	response, err := s.auth.Login(req.Email, req.Password, req.Role)
	if err != nil {
		if errors.Is(err, auth.ErrRoleMismatch) {
			platform.WriteError(w, http.StatusForbidden, "ROLE_MISMATCH", "This user does not have the requested role.")
			return
		}
		platform.WriteError(w, http.StatusUnauthorized, "LOGIN_FAILED", "Email, password, or role is invalid.")
		return
	}
	platform.WriteData(w, http.StatusOK, response)
}

func (s *Server) listPackages(w http.ResponseWriter, r *http.Request) {
	data, err := s.features.ListPackages(features.PackageFilters{
		Limit:    r.URL.Query().Get("limit"),
		Query:    r.URL.Query().Get("q"),
		Status:   r.URL.Query().Get("status"),
		DriverID: r.URL.Query().Get("driver_id"),
	})
	if err != nil {
		writeFeatureError(w, err)
		return
	}
	platform.WriteData(w, http.StatusOK, data)
}

func (s *Server) createPackage(w http.ResponseWriter, r *http.Request) {
	var req features.PackageRequest
	if err := platform.DecodeJSON(r, &req); err != nil {
		platform.WriteError(w, http.StatusBadRequest, "VALIDATION_ERROR", "Invalid package payload.")
		return
	}
	data, err := s.features.CreatePackage(req, auth.CurrentProfile(r))
	if err != nil {
		writeFeatureError(w, err)
		return
	}
	platform.WriteData(w, http.StatusCreated, data)
}

func (s *Server) updatePackage(w http.ResponseWriter, r *http.Request) {
	var req features.PackageUpdateRequest
	if err := platform.DecodeJSON(r, &req); err != nil {
		platform.WriteError(w, http.StatusBadRequest, "VALIDATION_ERROR", "Invalid package update payload.")
		return
	}
	data, err := s.features.UpdatePackage(r.PathValue("receipt"), req, auth.CurrentProfile(r))
	if err != nil {
		writeFeatureError(w, err)
		return
	}
	platform.WriteData(w, http.StatusOK, data)
}

func (s *Server) listDrivers(w http.ResponseWriter, _ *http.Request) {
	data, err := s.features.ListDrivers()
	if err != nil {
		writeFeatureError(w, err)
		return
	}
	platform.WriteData(w, http.StatusOK, data)
}

func (s *Server) assign(w http.ResponseWriter, r *http.Request) {
	var req features.AssignmentRequest
	if err := platform.DecodeJSON(r, &req); err != nil {
		platform.WriteError(w, http.StatusBadRequest, "VALIDATION_ERROR", "Invalid assignment payload.")
		return
	}
	data, err := s.features.Assign(req, auth.CurrentProfile(r))
	if err != nil {
		writeFeatureError(w, err)
		return
	}
	platform.WriteData(w, http.StatusCreated, data)
}

func (s *Server) listAssignments(w http.ResponseWriter, _ *http.Request) {
	data, err := s.features.ListAssignments()
	if err != nil {
		writeFeatureError(w, err)
		return
	}
	platform.WriteData(w, http.StatusOK, data)
}

func (s *Server) driverPackages(w http.ResponseWriter, r *http.Request) {
	data, err := s.features.DriverPackages(auth.CurrentProfile(r))
	if err != nil {
		writeFeatureError(w, err)
		return
	}
	platform.WriteData(w, http.StatusOK, data)
}

func (s *Server) submitProof(w http.ResponseWriter, r *http.Request) {
	var req features.ProofRequest
	if err := platform.DecodeJSON(r, &req); err != nil {
		platform.WriteError(w, http.StatusBadRequest, "VALIDATION_ERROR", "Invalid proof payload.")
		return
	}
	data, err := s.features.SubmitProof(r.PathValue("receipt"), req, auth.CurrentProfile(r))
	if err != nil {
		writeFeatureError(w, err)
		return
	}
	platform.WriteData(w, http.StatusCreated, data)
}

func (s *Server) proof(w http.ResponseWriter, r *http.Request) {
	data, err := s.features.Proof(r.PathValue("receipt"))
	if err != nil {
		writeFeatureError(w, err)
		return
	}
	platform.WriteData(w, http.StatusOK, data)
}

func writeFeatureError(w http.ResponseWriter, err error) {
	status, code, message := features.ErrorStatus(err)
	platform.WriteError(w, status, code, message)
}
