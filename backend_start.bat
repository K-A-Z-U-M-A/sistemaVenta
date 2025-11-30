@echo off
cd /d "C:\Proyects\Sistema Abarrotes\sistemaAbarrotes"

REM 1. Matar procesos viejos silenciosamente
taskkill /F /IM php.exe >nul 2>&1

REM 2. Limpiar cache silenciosamente
call php artisan config:clear >nul 2>&1
call php artisan cache:clear >nul 2>&1

REM 2.5 Compilar Assets (para QR y cambios)
call npm run build >nul 2>&1

REM 3. Iniciar servidor (se quedara corriendo en segundo plano)
php artisan serve --host=0.0.0.0 --port=8000 >nul 2>&1
