package httpapi

import (
	"net/http"
	"net/http/httptest"
	"testing"

	"finproppt/apps/server/internal/auth"
	"finproppt/apps/server/internal/features"
	"finproppt/apps/server/internal/platform"
)

func TestHealth(t *testing.T) {
	supabase := platform.NewSupabaseClient(platform.Config{
		SupabaseURL:     "https://example.supabase.co",
		SupabaseAnonKey: "anon",
	})
	server := NewServer(auth.NewService(supabase, nil), features.NewService(nil))

	request := httptest.NewRequest(http.MethodGet, "/api/v1/health", nil)
	recorder := httptest.NewRecorder()

	server.Routes("http://127.0.0.1:8000").ServeHTTP(recorder, request)

	if recorder.Code != http.StatusOK {
		t.Fatalf("expected 200, got %d", recorder.Code)
	}
	if recorder.Body.String() != "{\"data\":{\"message\":\"backend ready\",\"status\":\"ok\"}}\n" {
		t.Fatalf("unexpected body: %s", recorder.Body.String())
	}
}
