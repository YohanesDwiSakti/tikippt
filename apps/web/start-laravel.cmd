@echo off
cd /d "%~dp0"
"G:\XAMPP\php\php.exe" artisan serve --host=127.0.0.1 --port=8000 > laravel-dev.log 2> laravel-dev.err.log
