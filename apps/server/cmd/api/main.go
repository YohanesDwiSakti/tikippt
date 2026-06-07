package main

import (
	"context"
	"fmt"
	"log"
	"net/http"

	"finproppt/apps/server/internal/auth"
	"finproppt/apps/server/internal/features"
	"finproppt/apps/server/internal/httpapi"
	"finproppt/apps/server/internal/platform"
)

func main() {
	config, err := platform.LoadConfig()
	if err != nil {
		log.Fatal(err)
	}

	ctx := context.Background()
	db, err := platform.OpenDB(ctx, config.DatabaseURL)
	if err != nil {
		log.Fatal(err)
	}
	defer db.Close()

	if err := db.Ping(ctx); err != nil {
		log.Fatal(err)
	}

	supabase := platform.NewSupabaseClient(config)
	server := httpapi.NewServer(auth.NewService(supabase, db), features.NewService(db))

	addr := ":" + config.Port
	fmt.Printf("FINPROPPT API listening on http://127.0.0.1%s\n", addr)
	log.Fatal(http.ListenAndServe(addr, server.Routes(config.CORSAllowedOrigin)))
}
