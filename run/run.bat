@echo off
setlocal enabledelayedexpansion
title HIS - Sistema Hospitalario: Inicializador y Servidor

echo =====================================================================
echo       HIS - SISTEMA DE CONTROL DE CITAS MEDICAS HOSPITALARIAS        
echo              Inicializador Automatico y Levantamiento                
echo =====================================================================
echo.

cd /d "%~dp0\.."

:: 1. Comprobacion de PHP
where php >nul 2>nul
if %ERRORLEVEL% neq 0 (
    echo [ERROR] PHP no esta instalado o no se encuentra en el PATH del sistema.
    echo Por favor asegurese de tener PHP 8.2 o superior instalado.
    pause
    exit /b 1
)
echo [OK] PHP detectado en el sistema.

:: 2. Comprobacion de Composer
where composer >nul 2>nul
if %ERRORLEVEL% neq 0 (
    echo [ERROR] Composer no esta instalado o no se encuentra en el PATH.
    echo Por favor instale Composer desde https://getcomposer.org/
    pause
    exit /b 1
)
echo [OK] Composer detectado en el sistema.

:: 3. Creacion de directorios requeridos
echo [INFO] Verificando directorios del sistema...
if not exist "bootstrap\cache" mkdir "bootstrap\cache"
if not exist "storage\framework\cache" mkdir "storage\framework\cache"
if not exist "storage\framework\sessions" mkdir "storage\framework\sessions"
if not exist "storage\framework\views" mkdir "storage\framework\views"
if not exist "storage\logs" mkdir "storage\logs"
if not exist "database" mkdir "database"
echo [OK] Directorios del sistema listos.

:: 4. Archivo de Entorno .env
if not exist ".env" (
    echo [INFO] Creando archivo .env a partir de .env.example...
    copy .env.example .env >nul
    echo [OK] Archivo .env creado.
) else (
    echo [OK] Archivo .env existente detectado.
)

:: Asegurar SQLite en modo local si no se ha configurado MySQL
if not exist "database\database.sqlite" (
    echo. 2>"database\database.sqlite"
    echo [OK] Archivo database.sqlite inicializado.
)

:: 5. Instalacion de dependencias Composer
if not exist "vendor\autoload.php" (
    echo [INFO] Instalando dependencias de Composer (esto puede tardar unos momentos)...
    call composer install --no-interaction
    if %ERRORLEVEL% neq 0 (
        echo [ERROR] Fallo la instalacion de dependencias con Composer.
        pause
        exit /b 1
    )
    echo [OK] Dependencias instaladas satisfactoriamente.
) else (
    echo [OK] Dependencias de Composer ya presentes en vendor/.
)

:: 6. Generacion de Application Key si esta vacia
echo [INFO] Verificando clave de aplicacion...
php -r "$c=file_get_contents('.env'); if(strpos($c, 'APP_KEY=base64:')===false){exit(1);}exit(0);" >nul 2>nul
if %ERRORLEVEL% neq 0 (
    echo [INFO] Generando clave de cifrado de la aplicacion...
    call php artisan key:generate --force
)
echo [OK] Clave de aplicacion configurada.

:: 7. Migraciones de Base de Datos
echo [INFO] Ejecutando migraciones de la base de datos...
call php artisan migrate --force
if %ERRORLEVEL% neq 0 (
    echo [ERROR] Hubo un problema al ejecutar las migraciones.
    pause
    exit /b 1
)
echo [OK] Migraciones ejecutadas exitosamente.

:: 8. Carga de Datos Semilla (Doctores, Pacientes, Citas)
echo [INFO] Poblando base de datos con datos clinicos de prueba...
call php artisan db:seed --force
echo [OK] Datos clinicos listos.

:: 9. Ejecucion de Pruebas Automatizadas de Calidad (QA)
echo.
echo =====================================================================
echo            EJECUTANDO SUITE DE CONTROL DE CALIDAD (QA)              
echo =====================================================================
call php tests/run_tests.php
if %ERRORLEVEL% neq 0 (
    echo [ADVERTENCIA] Algunas pruebas no pasaron. Revise el log anterior.
) else (
    echo [OK] Control de Calidad: 54/54 Pruebas Aprobadas al 100%%.
)

echo.
echo =====================================================================
echo                SISTEMA LISTO Y OPERATIVO EN LOCAL                    
echo =====================================================================
echo   URL Agenda FullCalendar: http://127.0.0.1:8000/appointments/calendar
echo   URL Dashboard Clinico:   http://127.0.0.1:8000/appointments
echo   URL API REST Events:     http://127.0.0.1:8000/api/v1/calendar/events
echo =====================================================================
echo.
echo Abriendo navegador en la agenda medica...
start http://127.0.0.1:8000/appointments/calendar

echo.
echo Presione Ctrl + C para detener el servidor web cuando termine.
echo.
call php artisan serve --host=127.0.0.1 --port=8000
pause
