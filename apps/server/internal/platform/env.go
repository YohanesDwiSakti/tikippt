package platform

import (
	"bufio"
	"errors"
	"os"
	"path/filepath"
	"strings"
)

type Config struct {
	Port              string
	SupabaseURL       string
	SupabaseAnonKey   string
	DatabaseURL       string
	CORSAllowedOrigin string
}

func LoadEnvFile(path string) error {
	file, err := os.Open(path)
	if err != nil {
		if errors.Is(err, os.ErrNotExist) {
			return nil
		}
		return err
	}
	defer file.Close()

	scanner := bufio.NewScanner(file)
	for scanner.Scan() {
		line := strings.TrimSpace(scanner.Text())
		if line == "" || strings.HasPrefix(line, "#") {
			continue
		}
		key, value, ok := strings.Cut(line, "=")
		if !ok {
			continue
		}
		key = strings.TrimSpace(key)
		value = strings.Trim(strings.TrimSpace(value), `"'`)
		if os.Getenv(key) == "" {
			_ = os.Setenv(key, value)
		}
	}

	return scanner.Err()
}

func LoadConfig() (Config, error) {
	_ = LoadEnvFile(filepath.Join(".", ".env"))
	_ = LoadEnvFile(filepath.Join("..", "..", ".env"))

	config := Config{
		Port:              getenv("PORT", "5000"),
		SupabaseURL:       strings.TrimRight(os.Getenv("SUPABASE_URL"), "/"),
		SupabaseAnonKey:   os.Getenv("SUPABASE_ANON_KEY"),
		DatabaseURL:       os.Getenv("DATABASE_URL"),
		CORSAllowedOrigin: getenv("CORS_ALLOWED_ORIGIN", "http://127.0.0.1:8000"),
	}

	if config.SupabaseURL == "" {
		return config, errors.New("SUPABASE_URL is required")
	}
	if config.SupabaseAnonKey == "" {
		return config, errors.New("SUPABASE_ANON_KEY is required")
	}
	if config.DatabaseURL == "" {
		return config, errors.New("DATABASE_URL is required")
	}

	return config, nil
}

func getenv(key string, fallback string) string {
	value := os.Getenv(key)
	if value == "" {
		return fallback
	}
	return value
}
