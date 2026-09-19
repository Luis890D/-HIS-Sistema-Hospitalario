#!/usr/bin/env bash
# =====================================================================
#       HIS - SISTEMA DE CONTROL DE CITAS MÉDICAS HOSPITALARIAS
#              Script Bash de Inicialización y Ejecución
# =====================================================================

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
cd "$PROJECT_ROOT"

echo "====================================================================="
echo "      HIS - SISTEMA DE CONTROL DE CITAS MÉDICAS HOSPITALARIAS        "
echo "             Inicializador Automatizado de Entorno                   "
echo "====================================================================="
echo ""

# 1. Comprobación de PHP y Composer
command -v php >/dev/null 2>&1 || { echo "[ERROR] PHP no está instalado."; exit 1; }
command -v composer >/dev/null 2>&1 || { echo "[ERROR] Composer no está instalado."; exit 1; }
echo "[OK] PHP y Composer detectados."

# 2. Creación de directorios requeridos
mkdir -p bootstrap/cache storage/framework/{cache,sessions,views} storage/logs database
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
echo "[OK] Directorios del sistema listos."

# 3. Archivo .env
if [ ! -f ".env" ]; then
    echo "[INFO] Generando archivo .env..."
    cp .env.example .env
fi

# Base de datos SQLite
touch database/database.sqlite
echo "[OK] Base de datos database.sqlite preparada."

# 4. Instalación de dependencias
if [ ! -f "vendor/autoload.php" ]; then
    echo "[INFO] Instalando dependencias de Composer..."
    composer install --no-interaction
fi
echo "[OK] Dependencias en vendor/ verificadas."

# 5. Application Key
if ! grep -q "APP_KEY=base64:" .env; then
    echo "[INFO] Generando clave de aplicación..."
    php artisan key:generate --force
fi

# 6. Migraciones y Seeders
echo "[INFO] Ejecutando migraciones y poblando datos clínicos..."
php artisan migrate --force
php artisan db:seed --force
echo "[OK] Base de datos relacional lista con datos de prueba."

# 7. Control de Calidad
echo ""
echo "====================================================================="
echo "            EJECUTANDO SUITE DE CONTROL DE CALIDAD (QA)              "
echo "====================================================================="
php tests/run_tests.php
echo ""

# 8. Servidor Web
echo "====================================================================="
echo "                SISTEMA LISTO Y OPERATIVO EN LOCAL                   "
echo "====================================================================="
echo "  URL Agenda FullCalendar: http://127.0.0.1:8000/appointments/calendar"
echo "  URL Dashboard Clínico:   http://127.0.0.1:8000/appointments"
echo "  URL API REST Events:     http://127.0.0.1:8000/api/v1/calendar/events"
echo "====================================================================="
echo ""
echo "Iniciando servidor web en http://127.0.0.1:8000 (Ctrl + C para salir)..."
php artisan serve --host=127.0.0.1 --port=8000
