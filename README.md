# HIS - Sistema Hospitalario: Módulo de Control de Citas Médicas

Estructura de proyecto basada en **Laravel 12** con arquitectura **MVC**, respaldada por una **API RESTful** y un contenedor **Docker con MySQL 8.0**, diseñada específicamente para la gestión, agendamiento, reprogramación y cancelación de citas médicas hospitalarias.

---

## 1. Arquitectura del Módulo

El sistema implementa una separación de responsabilidades clara:

```
├── app/
│   ├── Enums/
│   │   └── AppointmentStatus.php         # Estados: Pending, Confirmed, Rescheduled, Cancelled, Attended, No Show
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Controller.php
│   │   │   ├── Api/V1/
│   │   │   │   └── AppointmentApiController.php    # Controlador API REST (JSON)
│   │   │   └── Web/
│   │   │       └── AppointmentWebController.php    # Controlador Web MVC (Blade)
│   │   ├── Requests/Appointments/
│   │   │   ├── StoreAppointmentRequest.php         # Validación de agendamiento
│   │   │   ├── RescheduleAppointmentRequest.php    # Validación de reprogramación
│   │   │   └── CancelAppointmentRequest.php        # Validación de cancelación
│   │   └── Resources/V1/
│   │       └── AppointmentResource.php             # Transformador estándar de respuesta REST
│   ├── Models/
│   │   ├── Appointment.php               # Cita Médica con relaciones y scopes
│   │   ├── Doctor.php                    # Médicos especialistas
│   │   ├── Patient.php                   # Expedientes de pacientes
│   │   └── MedicalSpecialty.php          # Especialidades del hospital
│   └── Services/
│       └── AppointmentService.php        # Lógica de negocio (disponibilidad y traslapes)
├── run/                                  # Scripts de levantamiento desatendido en 1 solo clic
│   ├── run.bat                           # Script Windows Batch (CMD / Doble clic)
│   ├── run.ps1                           # Script Windows PowerShell
│   ├── run.sh                            # Script Bash para Linux / macOS / WSL
│   ├── docker-run.bat                    # Script para levantar entorno con Docker
│   └── README.md                         # Instrucciones de la carpeta run/
├── doc/                                  # Documentación técnica formal del sistema
│   ├── matriz-requisitos-rqf-rqnf.md     # Matriz de trazabilidad y auditoría de RQF y RQNF
│   ├── qa-pruebas/                       # Manual de QA y suite de 54 pruebas
│   ├── integracion-develop/              # Reporte de integración de PRs en develop
│   ├── ui-ux-design/                     # Tokens y manual de diseño clínico
│   ├── fullcalendar-ui/                  # Integración de agenda interactiva
│   ├── validacion-conflictos-estados/    # Concurrencia y máquina de estados
│   ├── api-rest-citas/                   # Catálogo de endpoints REST v1
│   └── docker-mysql-schema/              # Esquema DDL y contenedores Docker
├── tests/                                # Suite de pruebas automatizadas
│   ├── run_tests.php                     # Runner CLI de pruebas autónomo
│   ├── Unit/                             # Pruebas unitarias de estados y traslapes
│   └── Feature/                          # Pruebas de integración de rutas API y Web
├── database/
│   ├── migrations/                       # Tablas: specialties, doctors, patients, appointments
│   └── seeders/                          # Datos iniciales para pruebas
├── docker/
│   ├── mysql/init.sql                    # Inicialización de la base de datos
│   ├── nginx/conf.d/app.conf             # Servidor web Nginx configurado para Laravel
│   └── php/Dockerfile                    # PHP 8.3 FPM + extensiones pdo_mysql + Composer
├── resources/views/
│   ├── layouts/app.blade.php             # Plantilla base hospitalaria con reloj y toasts
│   └── appointments/                     # Vistas Blade (calendar, index/dashboard, create, reschedule)
├── routes/
│   ├── api.php                           # Endpoints RESTful versión 1
│   └── web.php                           # Rutas Web MVC
├── docker-compose.yml                    # Orquestación de contenedores (app, webserver, db)
└── .env.example                          # Variables de entorno preconfiguradas
```

---

## 2. 🚀 Inicialización y Levantamiento Rápido en 1 Clic (Carpeta `run/`)

Para que cualquier usuario, docente o evaluador pueda poner en marcha el proyecto completo de manera inmediata y sin configuraciones manuales, se incluye la suite de automatización en la carpeta [`run/`](./run/README.md):

### Opciones de Ejecución:

* **En Windows (CMD o Doble Clic directo):**
  ```cmd
  run\run.bat
  ```
  *(O simplemente haz doble clic sobre el archivo `run/run.bat` en el explorador de archivos)*.

* **En Windows (PowerShell):**
  ```powershell
  .\run\run.ps1
  ```

* **En Linux / macOS / WSL (Bash):**
  ```bash
  chmod +x run/run.sh
  ./run/run.sh
  ```

* **Con Docker (1 Solo Clic):**
  ```cmd
  run\docker-run.bat
  ```

### ⚡ ¿Qué realiza el script automáticamente?
1. **Comprobación de Entorno:** Detecta la presencia de PHP 8.2+ y Composer en el sistema.
2. **Habilitación de ZIP:** Verifica y activa la extensión `zip` en PHP si estaba deshabilitada para acelerar descargas.
3. **Carpetas del Sistema:** Crea las estructuras de caché (`bootstrap/cache`) y sesiones/vistas (`storage/framework/*`).
4. **Archivo de Configuración:** Genera el archivo `.env` a partir de `.env.example`.
5. **Instalación de Dependencias:** Ejecuta `composer install` descargando los 107 paquetes del framework.
6. **Seguridad Criptográfica:** Genera la clave de cifrado de sesiones con `php artisan key:generate`.
7. **Base de Datos y Migraciones:** Inicializa la base de datos relacional y corre las 4 migraciones DDL.
8. **Poblado de Datos Clínicos:** Carga automáticamente médicos especialistas, pacientes con expedientes y citas de prueba en varios estados.
9. **Control de Calidad (QA):** Ejecuta la batería de **54 pruebas automatizadas** (`php tests/run_tests.php`) validando que el sistema pase al 100%.
10. **Lanzamiento:** Inicia el servidor web en `http://127.0.0.1:8000` y **abre automáticamente la agenda interactiva en el navegador**.

---

## 3. 🧪 Dónde Encontrar las Pruebas, QA y Evidencias Técnicas

Para facilitar la revisión técnica, la auditoría y la calificación del proyecto, a continuación se detalla la **ubicación exacta** de todas las suites de pruebas, el control de calidad y las evidencias de ejecución:

| Elemento / Entregable | Ubicación en el Repositorio | Descripción y Contenido |
| :--- | :--- | :--- |
| 🏃 **Runner CLI de Pruebas** | [`tests/run_tests.php`](./tests/run_tests.php) | Script autónomo ejecutable con `php tests/run_tests.php` que corre las 54 pruebas con salida visual. |
| 🧩 **Pruebas Unitarias de Estados** | [`tests/Unit/AppointmentStatusUnitTest.php`](./tests/Unit/AppointmentStatusUnitTest.php) | **29 pruebas**: validación de etiquetas en español, badges cromáticos, estados terminales inmutables y matriz de transiciones. |
| ⏱️ **Pruebas Unitarias de Traslapes** | [`tests/Unit/TimeSlotOverlapAlgorithmUnitTest.php`](./tests/Unit/TimeSlotOverlapAlgorithmUnitTest.php) | **11 pruebas**: verificación matemática de los 8 casos frontera de colisión temporal y cálculo de duraciones rápidas (+15m, +30m, +45m, +60m). |
| 🌐 **Pruebas de Rutas API REST** | [`tests/Feature/AppointmentApiGeneralTest.php`](./tests/Feature/AppointmentApiGeneralTest.php) | **6 pruebas**: registro de rutas `/api/v1/appointments`, `/calendar/events`, catálogos y controladores. |
| 💻 **Pruebas de Rutas Web MVC** | [`tests/Feature/AppointmentWebGeneralTest.php`](./tests/Feature/AppointmentWebGeneralTest.php) | **8 pruebas**: registro de rutas web del calendario, reprogramación, cancelación y cálculo de KPIs. |
| ⚙️ **Configuración PHPUnit** | [`phpunit.xml`](./phpunit.xml) | Archivo formal de configuración de suites unitarias y de integración. |
| 📘 **Manual Completo de QA** | [`doc/qa-pruebas/README.md`](./doc/qa-pruebas/README.md) | Estrategia de QA, matriz de los 54 casos de prueba (QA-01 a QA-54), escaneo léxico (`php -l` en 30 archivos, 0 errores) y sign-off. |
| 📸 **Registro de Evidencias (RQNF-08)** | [`doc/evidencias/README.md`](./doc/evidencias/README.md) | Logs reales de comandos de terminal, salidas de migraciones, seeders y respuestas completas de API en vivo (HTTP 200, 201, 404, 409, 422). |
| 📊 **Matriz de Trazabilidad RQF/RQNF** | [`doc/matriz-requisitos-rqf-rqnf.md`](./doc/matriz-requisitos-rqf-rqnf.md) | Mapeo detallado de los 10 RQF y los 8 RQNF frente al código fuente y pruebas. |
| 🔀 **Reporte de Integración Git** | [`doc/integracion-develop/README.md`](./doc/integracion-develop/README.md) | Detalle de los 5 Pull Requests mergeados a `develop` sin conflictos. |

### Cómo Ejecutar la Suite de Pruebas:
```powershell
php tests/run_tests.php
```
> **Resultado Actual:** **54 pruebas PASADAS, 0 FALLIDAS (100% de éxito)**.

---

## 4. Puesta en Marcha Alternativa con Docker Manual

### Requisitos
- [Docker Desktop](https://www.docker.com/) instalado y en ejecución.

### Pasos de Instalación

1. **Configurar el archivo de entorno**:
   ```bash
   cp .env.example .env
   ```

2. **Levantar los contenedores**:
   ```bash
   docker compose up -d --build
   ```

   Los servicios levantados serán:
   - **`his_mysql_db`**: MySQL 8.0 en el puerto `3306` (Base de datos: `his_hospital`).
   - **`his_laravel_app`**: PHP 8.3 FPM con extensiones `pdo_mysql`, `bcmath`, `gd`, `zip`.
   - **`his_nginx_webserver`**: Servidor Web Nginx accesible en `http://localhost:8080`.

3. **Instalar dependencias de Composer (dentro del contenedor)**:
   ```bash
   docker compose exec app composer install
   ```

4. **Generar la llave de la aplicación**:
   ```bash
   docker compose exec app php artisan key:generate
   ```

5. **Ejecutar migraciones y datos de prueba**:
   ```bash
   docker compose exec app php artisan migrate --seed --seeder=AppointmentDatabaseSeeder
   ```

6. **Acceder a la aplicación**:
   - Agenda FullCalendar: [http://localhost:8080/appointments/calendar](http://localhost:8080/appointments/calendar)
   - Dashboard Clínico: [http://localhost:8080/appointments](http://localhost:8080/appointments)
   - API REST: [http://localhost:8080/api/v1/appointments](http://localhost:8080/api/v1/appointments)

---

## 5. Endpoints de la API REST (`/api/v1`)

| Método | Endpoint | Acción | Descripción |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/appointments` | Listar | Obtener listado con filtros (`date`, `doctor_id`, `patient_id`, `status`). |
| `POST` | `/api/v1/appointments` | **Agendar** | Crear y reservar nueva cita médica validando horarios sin traslapes. |
| `GET` | `/api/v1/appointments/{id}` | Consultar | Ver detalle clínico completo de la cita médica. |
| `PUT` | `/api/v1/appointments/{id}/reschedule` | **Reprogramar** | Cambiar fecha/hora y registrar justificación de cambio. |
| `PUT` | `/api/v1/appointments/{id}/cancel` | **Cancelar** | Cancelar cita con motivo obligatorio y liberar horario. |

### Ejemplo: Agendar Cita Médica (`POST /api/v1/appointments`)
```json
{
  "patient_id": 1,
  "doctor_id": 1,
  "specialty_id": 1,
  "appointment_date": "2026-10-15",
  "start_time": "10:00",
  "end_time": "10:30",
  "reason": "Evaluación cardiológica por arritmia recurrente",
  "clinical_notes": "Paciente hipertenso bajo tratamiento."
}
```

### Ejemplo: Reprogramar Cita (`PUT /api/v1/appointments/1/reschedule`)
```json
{
  "appointment_date": "2026-10-18",
  "start_time": "11:00",
  "end_time": "11:30",
  "reschedule_reason": "Paciente presentó conflicto laboral en el horario previo."
}
```

### Ejemplo: Cancelar Cita (`PUT /api/v1/appointments/1/cancel`)
```json
{
  "cancellation_reason": "Paciente solicitó cancelar por viaje imprevisto."
}
```

---

## 6. Validaciones y Reglas del Sistema
- **Sin traslapes de horario**: `AppointmentService::isSlotAvailable` previene que un médico sea agendado dos veces en horarios superpuestos.
- **Trazabilidad clínica**: Cada reprogramación y cancelación registra el motivo, la fecha del cambio y preserva el historial del expediente.
- **Integridad referencial en MySQL**: Llaves foráneas con restricciones para evitar registros huérfanos entre pacientes, doctores, especialidades y citas.
