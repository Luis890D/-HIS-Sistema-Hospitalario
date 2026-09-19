# =====================================================================
#       HIS - SISTEMA DE CONTROL DE CITAS MÉDICAS HOSPITALARIAS
#              Script PowerShell de Inicialización y Ejecución
# =====================================================================

$ErrorActionPreference = "Stop"
$scriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$projectRoot = Split-Path -Parent $scriptDir
Set-Location $projectRoot

Write-Host "=====================================================================" -ForegroundColor Cyan
Write-Host "      HIS - SISTEMA DE CONTROL DE CITAS MÉDICAS HOSPITALARIAS        " -ForegroundColor White
Write-Host "             Inicializador Automatizado de Entorno                   " -ForegroundColor Cyan
Write-Host "=====================================================================" -ForegroundColor Cyan
Write-Host ""

# 1. Comprobación de PHP
try {
    $phpVersion = php -r "echo PHP_VERSION;"
    Write-Host "[OK] PHP detectado: v$phpVersion" -ForegroundColor Green
} catch {
    Write-Host "[ERROR] PHP no está instalado o no se encuentra en el PATH." -ForegroundColor Red
    Write-Host "Por favor asegúrese de tener PHP 8.2 o superior instalado." -ForegroundColor Yellow
    Exit 1
}

# 2. Comprobación de Composer
try {
    $composerVersion = composer --version
    Write-Host "[OK] Composer detectado en el sistema." -ForegroundColor Green
} catch {
    Write-Host "[ERROR] Composer no está instalado o no se encuentra en el PATH." -ForegroundColor Red
    Exit 1
}

# 3. Comprobación de extensión ZIP en PHP
$zipLoaded = php -r "echo extension_loaded('zip') ? 'YES' : 'NO';"
if ($zipLoaded -ne "YES") {
    Write-Host "[INFO] La extension ZIP de PHP no esta activa. Habilitando si es posible..." -ForegroundColor Yellow
    $iniPath = php -r "echo php_ini_loaded_file();"
    if (Test-Path $iniPath) {
        (Get-Content $iniPath) -replace ';extension=zip', 'extension=zip' | Set-Content $iniPath
        Write-Host "[OK] Extension ZIP habilitada en $iniPath" -ForegroundColor Green
    }
}

# 4. Creación de directorios requeridos
Write-Host "[INFO] Verificando estructura de carpetas de almacenamiento y caché..." -ForegroundColor Cyan
$dirs = @(
    "bootstrap/cache",
    "storage/framework/cache",
    "storage/framework/sessions",
    "storage/framework/views",
    "storage/logs",
    "database"
)
foreach ($d in $dirs) {
    if (-not (Test-Path $d)) {
        New-Item -ItemType Directory -Path $d -Force | Out-Null
    }
}
Write-Host "[OK] Estructura de almacenamiento lista." -ForegroundColor Green

# 5. Archivo .env
if (-not (Test-Path ".env")) {
    Write-Host "[INFO] Creando archivo .env a partir de .env.example..." -ForegroundColor Cyan
    Copy-Item ".env.example" ".env"
    Write-Host "[OK] Archivo .env generado." -ForegroundColor Green
} else {
    Write-Host "[OK] Archivo .env existente detectado." -ForegroundColor Green
}

# Base de datos SQLite local
if (-not (Test-Path "database/database.sqlite")) {
    New-Item -ItemType File -Path "database/database.sqlite" -Force | Out-Null
    Write-Host "[OK] Archivo database.sqlite inicializado." -ForegroundColor Green
}

# 6. Dependencias Composer
if (-not (Test-Path "vendor/autoload.php")) {
    Write-Host "[INFO] Instalando paquetes de dependencias con Composer..." -ForegroundColor Cyan
    composer install --no-interaction
    Write-Host "[OK] Dependencias instaladas con éxito." -ForegroundColor Green
} else {
    Write-Host "[OK] Dependencias de Composer ya instaladas en vendor/." -ForegroundColor Green
}

# 7. Clave criptográfica de la aplicación
$envContent = Get-Content ".env" -Raw
if ($envContent -notmatch "APP_KEY=base64:") {
    Write-Host "[INFO] Generando clave de cifrado de la aplicación..." -ForegroundColor Cyan
    php artisan key:generate --force
    Write-Host "[OK] Clave generada exitosamente." -ForegroundColor Green
} else {
    Write-Host "[OK] Clave de aplicación ya configurada." -ForegroundColor Green
}

# 8. Migraciones de Base de Datos
Write-Host "[INFO] Ejecutando migraciones de base de datos..." -ForegroundColor Cyan
php artisan migrate --force
Write-Host "[OK] Tablas de especialidades, doctores, pacientes y citas migradas." -ForegroundColor Green

# 9. Carga de Datos Semilla
Write-Host "[INFO] Cargando datos clínicos de prueba..." -ForegroundColor Cyan
php artisan db:seed --force
Write-Host "[OK] Datos clínicos iniciales cargados." -ForegroundColor Green

# 10. Control de Calidad (QA)
Write-Host ""
Write-Host "=====================================================================" -ForegroundColor Yellow
Write-Host "            EJECUTANDO SUITE DE CONTROL DE CALIDAD (QA)              " -ForegroundColor Yellow
Write-Host "=====================================================================" -ForegroundColor Yellow
php tests/run_tests.php
Write-Host ""

# 11. Despliegue del Servidor Web
Write-Host "=====================================================================" -ForegroundColor Green
Write-Host "                SISTEMA LISTO Y OPERATIVO EN LOCAL                   " -ForegroundColor Green
Write-Host "=====================================================================" -ForegroundColor Green
Write-Host "  URL Agenda FullCalendar: http://127.0.0.1:8000/appointments/calendar" -ForegroundColor White
Write-Host "  URL Dashboard Clínico:   http://127.0.0.1:8000/appointments" -ForegroundColor White
Write-Host "  URL API REST Events:     http://127.0.0.1:8000/api/v1/calendar/events" -ForegroundColor White
Write-Host "=====================================================================" -ForegroundColor Green
Write-Host ""
Write-Host "Abriendo agenda interactiva en su navegador predeterminado..." -ForegroundColor Cyan
Start-Process "http://127.0.0.1:8000/appointments/calendar"

Write-Host "Servidor web en ejecución. Presione Ctrl + C para detener." -ForegroundColor Yellow
Write-Host ""
php artisan serve --host=127.0.0.1 --port=8000
