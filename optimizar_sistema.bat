@echo off
echo ========================================
echo   OPTIMIZACION DEL SISTEMA ABARROTES
echo ========================================
echo.

echo [1/6] Limpiando cache anterior...
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo.
echo [2/6] Optimizando configuracion...
php artisan config:cache

echo.
echo [3/6] Optimizando rutas...
php artisan route:cache

echo.
echo [4/6] Optimizando vistas...
php artisan view:cache

echo.
echo [5/6] Optimizando autoload de Composer...
composer dump-autoload -o

echo.
echo [6/6] Ejecutando optimizacion general...
php artisan optimize

echo.
echo ========================================
echo   OPTIMIZACION COMPLETADA
echo ========================================
echo.
echo El sistema ahora deberia cargar mas rapido.
echo Presiona cualquier tecla para cerrar...
pause > nul
