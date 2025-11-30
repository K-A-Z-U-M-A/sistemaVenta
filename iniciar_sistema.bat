@echo off
echo ========================================
echo   Sistema de Abarrotes
echo   Iniciando servidor...
echo ========================================
echo.

REM Verificar si existe el directorio vendor
if not exist "vendor\" (
    echo [ERROR] No se encontro la carpeta vendor.
    echo Por favor ejecuta: composer install
    pause
    exit /b 1
)

REM Verificar si existe el archivo .env
if not exist ".env" (
    echo [ERROR] No se encontro el archivo .env
    echo Por favor copia .env.example a .env y configuralo
    pause
    exit /b 1
)

echo [1/3] Limpiando cache de Laravel...
call php artisan config:clear
call php artisan cache:clear
call php artisan route:clear
call php artisan view:clear

echo.
echo [2/3] Iniciando servidor Laravel en http://localhost:8000
echo.
echo Presiona Ctrl+C para detener el servidor
echo ========================================
echo.

REM Iniciar el servidor de Laravel
call php artisan serve

pause
