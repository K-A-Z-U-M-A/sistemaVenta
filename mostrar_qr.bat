@echo off
setlocal enabledelayedexpansion

REM Detectar la IP local
set IP=
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr "IPv4"') do (
    set IP=%%a
)
set IP=%IP: =%
if "%IP%"=="" set IP=localhost

echo.
echo ========================================
echo   GENERADOR DE CODIGO QR - ACCESO
echo ========================================
echo.
echo IP detectada: %IP%
echo URL del sistema: http://%IP%:8000
echo.
echo Abriendo navegador...
echo.

REM Abrir el navegador con la página de QR
start "" "generar_qr.html?ip=%IP%"

echo.
echo Listo! El codigo QR se ha generado.
echo.
pause
