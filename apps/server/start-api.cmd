@echo off
cd /d "%~dp0"
go run ./cmd/api > api-dev.log 2> api-dev.err.log
