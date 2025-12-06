@echo off
cd /d "C:\Proyects\Sistema Abarrotes\sistemaAbarrotes"

REM 1. Matar procesos viejos silenciosamente
taskkill /F /IM php.exe >nul 2>&1

REM 2. Optimizar cache silenciosamente (CRITICO PARA VELOCIDAD)
call php artisan config:cache >nul 2>&1
call php artisan route:cache >nul 2>&1
call php artisan view:cache >nul 2>&1
call php artisan event:cache >nul 2>&1

REM 2.5 Compilar Assets
call npm run build >nul 2>&1

REM 3. Iniciar servidor optimizado
php -d memory_limit=512M artisan serve --host=0.0.0.0 --port=8000 >nul 2>&1
