@echo off
echo ========================================
echo   Deteniendo Servidores PHP...
echo ========================================
echo.

REM Detener todos los procesos de PHP
taskkill /F /IM php.exe >nul 2>&1

if %ERRORLEVEL% EQU 0 (
    echo [OK] Servidores detenidos correctamente.
) else (
    echo [INFO] No habia servidores corriendo.
)

echo.
echo Listo para iniciar nuevamente.
timeout /t 2 >nul
