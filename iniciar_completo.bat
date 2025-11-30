@echo off
echo ========================================
echo   Sistema de Abarrotes - Modo Completo
echo   Iniciando servidor y compilador...
echo ========================================
echo.

REM Verificar si existe el directorio vendor
if not exist "vendor\" (
    echo [ERROR] No se encontro la carpeta vendor.
    echo Por favor ejecuta: composer install
    pause
    exit /b 1
)

REM Verificar si existe el directorio node_modules
if not exist "node_modules\" (
    echo [ERROR] No se encontro la carpeta node_modules.
    echo Por favor ejecuta: npm install
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
echo [3/3] Iniciando Vite dev server...
echo.
echo Se abriran dos ventanas:
echo   - Servidor Laravel (puerto 8000)
echo   - Vite Dev Server (puerto 5173)
echo.
echo Presiona Ctrl+C en ambas ventanas para detener
echo ========================================
echo.

REM Iniciar Vite en una nueva ventana
start "Vite Dev Server" cmd /k "npm run dev"

REM Esperar 3 segundos para que Vite inicie
timeout /t 3 /nobreak >nul

REM Iniciar el servidor de Laravel en la ventana actual
call php artisan serve

pause
