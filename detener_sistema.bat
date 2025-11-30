@echo off
echo Deteniendo Sistema de Abarrotes...
taskkill /F /IM php.exe >nul 2>&1
taskkill /F /IM node.exe >nul 2>&1
echo.
echo Sistema detenido correctamente.
pause
