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
├── database/
│   ├── migrations/                       # Tablas: specialties, doctors, patients, appointments
│   └── seeders/                          # Datos iniciales para pruebas
├── docker/
│   ├── mysql/init.sql                    # Inicialización de la base de datos
│   ├── nginx/conf.d/app.conf             # Servidor web Nginx configurado para Laravel
│   └── php/Dockerfile                    # PHP 8.3 FPM + extensiones pdo_mysql + Composer
├── resources/views/
│   ├── layouts/app.blade.php             # Plantilla base hospitalaria
│   └── appointments/                     # Vistas Blade (index, create, reschedule, show)
├── routes/
│   ├── api.php                           # Endpoints RESTful versión 1
│   └── web.php                           # Rutas Web MVC
├── docker-compose.yml                    # Orquestación de contenedores (app, webserver, db)
└── .env.example                          # Variables de entorno preconfiguradas para Docker
```

---

## 2. Puesta en Marcha con Docker

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
   - Panel Web MVC: [http://localhost:8080/appointments](http://localhost:8080/appointments)
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
