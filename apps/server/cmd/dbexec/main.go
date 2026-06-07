package main

import (
	"context"
	"fmt"
	"log"
	"os"
	"path/filepath"

	"github.com/jackc/pgx/v5"

	"finproppt/apps/server/internal/platform"
)

func main() {
	if len(os.Args) < 2 {
		log.Fatal("usage: go run ./cmd/dbexec <sql-file> [<sql-file>...]")
	}

	if err := platform.LoadEnvFile(".env"); err != nil {
		log.Fatal(err)
	}
	if err := platform.LoadEnvFile(filepath.Join("..", "..", ".env")); err != nil {
		log.Fatal(err)
	}

	databaseURL := os.Getenv("DATABASE_URL")
	if databaseURL == "" {
		log.Fatal("DATABASE_URL is required")
	}

	ctx := context.Background()
	conn, err := pgx.Connect(ctx, databaseURL)
	if err != nil {
		log.Fatal(err)
	}
	defer conn.Close(ctx)

	for _, file := range os.Args[1:] {
		sql, err := os.ReadFile(file)
		if err != nil {
			log.Fatal(err)
		}
		if _, err := conn.Exec(ctx, string(sql)); err != nil {
			log.Fatalf("%s failed: %v", file, err)
		}
		fmt.Printf("applied %s\n", file)
	}
}
