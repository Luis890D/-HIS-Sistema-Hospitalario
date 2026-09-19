@echo off
title HIS - Sistema Hospitalario: Despliegue con Docker

echo =====================================================================
echo       HIS - SISTEMA DE CONTROL DE CITAS MEDICAS HOSPITALARIAS        
echo              Levantamiento Automatizado con Docker                   
echo =====================================================================
echo.

cd /d "%~dp0\.."

where docker >nul 2>nul
if %ERRORLEVEL% neq 0 (
    echo [ERROR] Docker no esta instalado o no se encuentra en el PATH.
    echo Asegurese de tener Docker Desktop iniciado.
    pause
    exit /b 1
)

if not exist ".env" (
    copy .env.example .env >nul
)

echo [INFO] Levantando contenedores Docker (MySQL 8.0, PHP 8.3 FPM, Nginx)...
docker compose up -d --build

echo [INFO] Instalando paquetes de Composer dentro del contenedor...
docker compose exec -T app composer install --no-interaction

echo [INFO] Generando clave de aplicacion...
docker compose exec -T app php artisan key:generate --force

echo [INFO] Ejecutando migraciones y seeders en MySQL...
docker compose exec -T app php artisan migrate --seed --force

echo.
echo =====================================================================
echo             CONTENEDORES DOCKER LEVANTADOS Y LISTOS                  
echo =====================================================================
echo   URL Agenda Web Nginx: http://localhost:8080/appointments/calendar
echo   URL API REST:         http://localhost:8080/api/v1/calendar/events
echo =====================================================================
echo.
start http://localhost:8080/appointments/calendar
pause
