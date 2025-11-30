@echo off
setlocal enabledelayedexpansion
echo ========================================
echo   Sistema de Abarrotes
echo ========================================
echo.

REM Detener servidores anteriores
echo [1/4] Deteniendo servidores anteriores...
taskkill /F /IM php.exe >nul 2>&1
timeout /t 1 >nul

REM Compilar Assets
echo [2/4] Compilando assets...
call npm run build >nul 2>&1

REM Limpiar Cache
echo [3/4] Limpiando cache...
call php artisan config:clear >nul
call php artisan cache:clear >nul
call php artisan view:clear >nul

REM Firewall
echo [4/4] Configurando firewall (Permitir todo)...
netsh advfirewall firewall delete rule name="Laravel 8000" >nul 2>&1
netsh advfirewall firewall add rule name="Laravel 8000" dir=in action=allow protocol=TCP localport=8000 profile=any >nul 2>&1

echo.
echo ========================================
echo   SERVIDOR INICIADO
echo ========================================
echo.
echo   Nombre del servidor: KAZUMA
echo.
echo   [DESDE ESTA PC]
echo   http://localhost:8000
echo.
echo   [DESDE CELULARES]
echo   http://KAZUMA:8000
echo.
echo ========================================
echo.

REM Ejecutar servidor
php artisan serve --host=0.0.0.0 --port=8000

pause
