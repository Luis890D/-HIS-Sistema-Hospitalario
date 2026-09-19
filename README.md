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

## 3. Puesta en Marcha Alternativa con Docker Manual

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

## 3. Endpoints de la API REST (`/api/v1`)

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

## 4. Validaciones y Reglas del Sistema
- **Sin traslapes de horario**: `AppointmentService::isSlotAvailable` previene que un médico sea agendado dos veces en horarios superpuestos.
- **Trazabilidad clínica**: Cada reprogramación y cancelación registra el motivo, la fecha del cambio y preserva el historial del expediente.
- **Integridad referencial en MySQL**: Llaves foráneas con restricciones para evitar registros huérfanos entre pacientes, doctores, especialidades y citas.
