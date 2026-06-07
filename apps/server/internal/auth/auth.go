package auth

import (
	"context"
	"net/http"
	"strings"

	"finproppt/apps/server/internal/domain"
	"finproppt/apps/server/internal/platform"
	"github.com/jackc/pgx/v5/pgxpool"
)

type contextKey string

const profileKey contextKey = "profile"

type Service struct {
	supabase *platform.SupabaseClient
	db       *pgxpool.Pool
}

func NewService(supabase *platform.SupabaseClient, db *pgxpool.Pool) *Service {
	return &Service{supabase: supabase, db: db}
}

func (s *Service) Login(email, password, role string) (LoginResponse, error) {
	session, err := s.supabase.AuthLogin(email, password)
	if err != nil {
		return LoginResponse{}, err
	}

	profile, err := s.ProfileByID(session.User.ID)
	if err != nil {
		return LoginResponse{}, err
	}
	if profile.Role != role {
		return LoginResponse{}, ErrRoleMismatch
	}

	return LoginResponse{
		User:         profile,
		Token:        session.AccessToken,
		RefreshToken: session.RefreshToken,
	}, nil
}

func (s *Service) ProfileByID(id string) (domain.Profile, error) {
	var profile domain.Profile
	err := s.db.QueryRow(context.Background(), `
		select id::text, name, email, role, created_at::text, updated_at::text
		from public.profiles
		where id = $1
	`, id).Scan(&profile.ID, &profile.Name, &profile.Email, &profile.Role, &profile.CreatedAt, &profile.UpdatedAt)
	if err != nil {
		return domain.Profile{}, err
	}
	return profile, nil
}

func (s *Service) Authenticate(token string) (domain.Profile, error) {
	user, err := s.supabase.AuthUser(token)
	if err != nil {
		return domain.Profile{}, err
	}
	return s.ProfileByID(user.ID)
}

func Middleware(service *Service, roles ...string) func(http.Handler) http.Handler {
	allowed := map[string]bool{}
	for _, role := range roles {
		allowed[role] = true
	}

	return func(next http.Handler) http.Handler {
		return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
			token := bearerToken(r.Header.Get("Authorization"))
			if token == "" {
				platform.WriteError(w, http.StatusUnauthorized, "UNAUTHENTICATED", "Login is required.")
				return
			}

			profile, err := service.Authenticate(token)
			if err != nil {
				platform.WriteError(w, http.StatusUnauthorized, "UNAUTHENTICATED", "The session token is invalid.")
				return
			}

			if len(allowed) > 0 && !allowed[profile.Role] {
				platform.WriteError(w, http.StatusForbidden, "UNAUTHORIZED", "This role cannot access this endpoint.")
				return
			}

			ctx := context.WithValue(r.Context(), profileKey, profile)
			next.ServeHTTP(w, r.WithContext(ctx))
		})
	}
}

func CurrentProfile(r *http.Request) domain.Profile {
	profile, _ := r.Context().Value(profileKey).(domain.Profile)
	return profile
}

func bearerToken(header string) string {
	if header == "" {
		return ""
	}
	prefix := "Bearer "
	if !strings.HasPrefix(header, prefix) {
		return ""
	}
	return strings.TrimSpace(strings.TrimPrefix(header, prefix))
}

type LoginResponse struct {
	User         domain.Profile `json:"user"`
	Token        string         `json:"token"`
	RefreshToken string         `json:"refresh_token"`
}

type LoginRequest struct {
	Email    string `json:"email"`
	Password string `json:"password"`
	Role     string `json:"role"`
}

type sentinelError string

func (e sentinelError) Error() string { return string(e) }

const (
	ErrRoleMismatch    sentinelError = "role mismatch"
	ErrProfileNotFound sentinelError = "profile not found"
)
