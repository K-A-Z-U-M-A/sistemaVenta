@echo off
setlocal
echo ========================================
echo   Sistema de Abarrotes - MODO PRODUCCION
echo ========================================
echo.

REM 1. Compilar Assets
echo [1/3] Generando archivos de estilo...
call npm run build >nul 2>&1

REM 2. Limpiar Cache
echo [2/3] Limpiando sistema...
call php artisan config:clear >nul
call php artisan cache:clear >nul
call php artisan view:clear >nul

REM 3. Firewall
echo [INFO] Configurando Firewall...
netsh advfirewall firewall add rule name="Laravel 8000" dir=in action=allow protocol=TCP localport=8000 >nul 2>&1

echo.
echo ========================================
echo   TU DIRECCION IP ES:
echo ========================================
ipconfig | findstr "IPv4"
echo ========================================
echo.
echo EN TU COMPUTADORA (SERVIDOR):
echo   http://localhost:8000
echo.
echo EN TU CELULAR ESCRIBE:
echo   http://[NUMERO_DE_ARRIBA]:8000
echo.
echo Ejemplo: Si arriba dice 192.168.1.15, escribe http://192.168.1.15:8000
echo.

REM Ejecutar servidor
php artisan serve --host=0.0.0.0 --port=8000

pause
