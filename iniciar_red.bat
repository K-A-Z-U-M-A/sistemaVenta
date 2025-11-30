@echo off
echo ========================================
echo   Sistema de Abarrotes - MODO RED LOCAL
echo ========================================
echo.

REM Matar procesos anteriores para evitar conflictos
taskkill /F /IM php.exe >nul 2>&1
taskkill /F /IM node.exe >nul 2>&1

echo [INFO] Tu direccion IP es:
echo ----------------------------------------
ipconfig | findstr "IPv4"
echo ----------------------------------------
echo.
echo USA LA IP QUE APARECE ARRIBA (ej. 192.168.1.X)
echo.

REM Verificar PostgreSQL
sc query postgresql-x64-17 | find "RUNNING" >nul
if %errorlevel% neq 0 (
    echo [ADVERTENCIA] PostgreSQL no esta corriendo.
    echo Intentando iniciar...
    net start postgresql-x64-17
    if %errorlevel% neq 0 (
        echo [ERROR] No se pudo iniciar PostgreSQL.
        echo Por favor ejecuta este archivo como ADMINISTRADOR.
        pause
        exit /b
    )
)

echo [1/3] Limpiando cache...
call php artisan config:clear >nul
call php artisan cache:clear >nul

echo.
echo [2/3] Iniciando Vite...
start "Vite Server" /min cmd /c "npm run dev -- --host"

echo.
echo [3/3] Iniciando Servidor Laravel...
echo.
echo IMPORTANTE:
echo 1. Si aparece una alerta del Firewall de Windows, dale a "PERMITIR ACCESO".
echo 2. En tus otros dispositivos, entra a: http://[TU_IP]:8000
echo.
echo Servidor corriendo... (No cierres esta ventana)
echo.

php artisan serve --host=0.0.0.0 --port=8000

pause
