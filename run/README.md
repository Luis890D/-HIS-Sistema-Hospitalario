# Scripts de Ejecución y Levantamiento Automatizado (`run/`)

Esta carpeta contiene los scripts para inicializar, configurar y levantar el proyecto **HIS - Sistema Hospitalario** de forma completamente desatendida y reproducible.

---

## 🛠️ Opciones de Ejecución

### 1. Windows (CMD o Doble Clic)
- **Archivo:** [`run.bat`](./run.bat)
- **Instrucciones:** Haz doble clic sobre `run.bat` o ejecútalo en la consola de Windows (`cmd.exe`):
  ```cmd
  run\run.bat
  ```

### 2. Windows (PowerShell)
- **Archivo:** [`run.ps1`](./run.ps1)
- **Instrucciones:** Abre PowerShell y ejecuta:
  ```powershell
  .\run\run.ps1
  ```

### 3. Linux / macOS / WSL (Bash)
- **Archivo:** [`run.sh`](./run.sh)
- **Instrucciones:**
  ```bash
  chmod +x run/run.sh
  ./run/run.sh
  ```

### 4. Docker Compose (1 Solo Clic)
- **Archivo:** [`docker-run.bat`](./docker-run.bat)
- **Instrucciones:**
  ```cmd
  run\docker-run.bat
  ```

---

## ⚡ ¿Qué hace el script automáticamente?

1. **Comprobación de Entorno:** Valida la existencia de PHP 8.2+ y Composer en el sistema.
2. **Habilitación de ZIP:** Verifica y activa la extensión `zip` en `php.ini` si estaba comentada, acelerando descargas.
3. **Estructura de Almacenamiento:** Crea carpetas de caché (`bootstrap/cache`) y sesiones/vistas (`storage/framework/*`).
4. **Configuración `.env`:** Si no existe el archivo `.env`, lo crea automáticamente a partir de `.env.example`.
5. **Instalación de Dependencias:** Ejecuta `composer install` para descargar los 107 paquetes del framework y utilidades.
6. **Seguridad (App Key):** Genera la clave criptográfica de sesión (`php artisan key:generate`).
7. **Base de Datos y Migraciones:** Inicializa la base de datos y corre las 4 migraciones DDL relacionales.
8. **Datos Semilla (Seeds):** Carga médicos especialistas, pacientes con expedientes y citas clínicas de prueba.
9. **Control de Calidad (QA):** Ejecuta la batería de **54 pruebas automatizadas** (`php tests/run_tests.php`) certificando que nada esté roto (100% éxito).
10. **Lanzamiento:** Arranca el servidor web en `http://127.0.0.1:8000` y abre automáticamente la agenda en el navegador.
