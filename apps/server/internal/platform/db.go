package platform

import (
	"context"

	"github.com/jackc/pgx/v5/pgxpool"
)

func OpenDB(ctx context.Context, databaseURL string) (*pgxpool.Pool, error) {
	return pgxpool.New(ctx, databaseURL)
}
