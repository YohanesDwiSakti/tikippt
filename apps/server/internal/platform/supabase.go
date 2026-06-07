package platform

import (
	"bytes"
	"encoding/json"
	"fmt"
	"io"
	"net/http"
)

type SupabaseClient struct {
	baseURL    string
	anonKey    string
	httpClient *http.Client
}

func NewSupabaseClient(config Config) *SupabaseClient {
	return &SupabaseClient{
		baseURL:    config.SupabaseURL,
		anonKey:    config.SupabaseAnonKey,
		httpClient: http.DefaultClient,
	}
}

func (c *SupabaseClient) AuthLogin(email, password string) (AuthSession, error) {
	var session AuthSession
	body := map[string]string{"email": email, "password": password}
	err := c.request("POST", "/auth/v1/token?grant_type=password", c.anonKey, "", body, &session)
	return session, err
}

func (c *SupabaseClient) AuthUser(token string) (AuthUser, error) {
	var user AuthUser
	err := c.request("GET", "/auth/v1/user", c.anonKey, token, nil, &user)
	return user, err
}

func (c *SupabaseClient) request(method, path, key, bearer string, body any, out any) error {
	var payload io.Reader
	if body != nil {
		raw, err := json.Marshal(body)
		if err != nil {
			return err
		}
		payload = bytes.NewReader(raw)
	}

	req, err := http.NewRequest(method, c.baseURL+path, payload)
	if err != nil {
		return err
	}

	req.Header.Set("Content-Type", "application/json")
	req.Header.Set("Accept", "application/json")
	req.Header.Set("apikey", key)
	if bearer != "" {
		req.Header.Set("Authorization", "Bearer "+bearer)
	} else {
		req.Header.Set("Authorization", "Bearer "+key)
	}
	if method == "POST" || method == "PATCH" {
		req.Header.Set("Prefer", "return=representation")
	}

	resp, err := c.httpClient.Do(req)
	if err != nil {
		return err
	}
	defer resp.Body.Close()

	raw, _ := io.ReadAll(resp.Body)
	if resp.StatusCode < 200 || resp.StatusCode >= 300 {
		return fmt.Errorf("supabase %s %s failed: %s: %s", method, path, resp.Status, string(raw))
	}
	if out == nil || len(raw) == 0 {
		return nil
	}
	return json.Unmarshal(raw, out)
}

type AuthSession struct {
	AccessToken  string   `json:"access_token"`
	RefreshToken string   `json:"refresh_token"`
	User         AuthUser `json:"user"`
}

type AuthUser struct {
	ID    string `json:"id"`
	Email string `json:"email"`
}
