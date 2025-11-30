@echo off
echo ========================================
echo   Sistema de Abarrotes - MODO OPTIMIZADO
echo ========================================
echo.

REM Verificar si se ejecuta como administrador (opcional pero recomendado para servicios)
net session >nul 2>&1
if %errorLevel% == 0 (
    echo [INFO] Ejecutando con privilegios de Administrador.
) else (
    echo [INFO] Ejecutando sin privilegios de Administrador.
)

echo.
echo [1/4] Optimizando Autoload de Composer...
call composer dump-autoload -o >nul

echo.
echo [2/4] Compilando Assets para Produccion (Vite)...
echo Esto puede tardar unos segundos...
call npm run build

echo.
echo [3/4] Cacheando configuracion, rutas y vistas...
call php artisan optimize
call php artisan view:cache
call php artisan event:cache

echo.
echo [4/4] Iniciando Servidor Laravel...
echo.
echo ---------------------------------------------------------
echo  IMPORTANTE PARA SEGURIDAD:
echo  Asegurate de que tu archivo .env tenga:
echo    APP_ENV=production
echo    APP_DEBUG=false
echo ---------------------------------------------------------
echo.
echo Servidor corriendo en http://0.0.0.0:8000
echo (Puedes cerrar esta ventana con Ctrl+C)
echo.

php artisan serve --host=0.0.0.0 --port=8000

pause
