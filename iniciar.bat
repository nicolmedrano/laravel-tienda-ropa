@echo off
title Bella Girls - Tienda de Ropa
echo =================================================================
echo   INICIANDO BELLA GIRLS - SISTEMA DE TIENDA DE ROPA
echo =================================================================
echo.

:: Liberar puerto 8000 si estuviera ocupado por otro proceso
for /f "tokens=5" %%a in ('netstat -aon ^| findstr :8000 ^| findstr LISTENING') do (
    echo [INFO] Cerrando proceso previo en puerto 8000 (PID: %%a)...
    taskkill /F /PID %%a >nul 2>&1
)

:: Abrir enlace en el navegador
timeout /t 1 /nobreak >nul
start "" "http://127.0.0.1:8000/login"

:: Iniciar el servidor de desarrollo
php artisan serve

