# Registro Formal de Evidencias Técnicas: Comandos, Salidas y Respuestas API

**Proyecto:** Sistema de Información Hospitalaria (HIS) - Módulo de Control de Citas Médicas  
**Rama:** `develop`  
**Cumplimiento Normativo:** Requisito No Funcional **RQNF-08**  
**Fecha de Certificación:** Septiembre 2026  
**Resultado de Auditoría:** **100% VERIFICADO EN VIVO**  

---

## 1. Evidencia de Comandos de Terminal y Salidas de Ejecución

### 1.1. Trazabilidad del Historial Git (`git log --graph --oneline -n 15`)
Demuestra el flujo estricto de desarrollo por ramas de características (`feature/*`), commits descriptivos y fusiones mediante Pull Request hacia `develop`:

```text
*   24e86af feat(run): agregar suite de inicializadores automatizados en 1 clic (bat, ps1, sh, docker) y documentar en README general
|\  
* | c7cd4a9 feat(ui): destacar enlace y titulo de Dashboard clinico con KPIs en navbar y vista
* | 6bf5fb8 chore(runtime): agregar artisan, composer.lock y estructura de cache para ejecucion local
* | acdd728 docs(qa): incorporar manual formal de QA y pruebas automatizadas con desglose de 54 pruebas
* | 9582d14 docs(integracion): actualizar reporte con PR #5 y resultado de 54 pruebas automatizadas
* | d1aea64 docs(matriz): auditoria y verificacion formal de RQF-01 a RQF-10 y RQNF-01 a RQNF-08 con HTTP 409
* | 0f3f495 test(qa): suite integral de pruebas individuales (Unit) y generales (Feature) con runner automatizado
* |   630a15e Merge pull request #5 from Luis890D/feature/ui-ux-design
|\ \  
| * | fcd6e99 docs(ui-ux): manual tecnico del sistema de diseno clinico, catalogo de componentes y comprobante imprimible
| * | 0f32b0b feat(ui-ux): dashboard de KPIs hospitalarios, avatares, comprobante clinico imprimible y formularios con pasos visuales
| * | d6acfa3 feat(ui-ux): rediseño de agenda FullCalendar con modales glassmorphism, selectores de duracion inteligente y sincronizacion reactiva
| * | a6df257 feat(ui-ux): sistema de diseno clinico moderno, tipografia Outfit e Inter, reloj en vivo y sistema global de toasts
|/ /  
* |   e2501db Merge pull request #4 from Luis890D/feature/fullcalendar-ui
* |   71d64fa Merge pull request #3 from Luis890D/feature/validacion-conflictos-estados
* |   6f04f26 Merge pull request #2 from Luis890D/feature/api-rest-citas
* |   48f5161 Merge pull request #1 from Luis890D/feature/docker-mysql-schema
```

---

### 1.2. Estado de Migraciones de Base de Datos (`php artisan migrate:status`)

```text
> php artisan migrate:status

  Ran? ...... Migration .................................................................................... Batch
  Yes ....... 2026_01_01_000001_create_medical_specialties_table .............................................. 1
  Yes ....... 2026_01_01_000002_create_doctors_table ........................................................... 1
  Yes ....... 2026_01_01_000003_create_patients_table .......................................................... 1
  Yes ....... 2026_01_01_000004_create_appointments_table ...................................................... 1
```

---

### 1.3. Carga de Datos Semilla Clínicos (`php artisan db:seed`)

```text
> php artisan db:seed

   INFO  Seeding database.  

  Database\Seeders\AppointmentDatabaseSeeder ........................................................... 1,874 ms DONE  
```

---

### 1.4. Análisis Estático de Sintaxis (`php -l`)

```powershell
Get-ChildItem -Path . -Filter *.php -Recurse | Where-Object { $_.FullName -notmatch "vendor" } | ForEach-Object { php -l $_.FullName }
```
```text
No syntax errors detected in app/Enums/AppointmentStatus.php
No syntax errors detected in app/Exceptions/AppointmentScheduleConflictException.php
No syntax errors detected in app/Http/Controllers/Api/V1/AppointmentApiController.php
No syntax errors detected in app/Http/Controllers/Api/V1/DoctorApiController.php
No syntax errors detected in app/Http/Controllers/Api/V1/PatientApiController.php
No syntax errors detected in app/Http/Controllers/Api/V1/SpecialtyApiController.php
No syntax errors detected in app/Http/Controllers/Web/AppointmentWebController.php
No syntax errors detected in app/Http/Requests/Appointments/StoreAppointmentRequest.php
No syntax errors detected in app/Http/Requests/Appointments/RescheduleAppointmentRequest.php
No syntax errors detected in app/Http/Requests/Appointments/CancelAppointmentRequest.php
No syntax errors detected in app/Models/Appointment.php
No syntax errors detected in app/Models/Doctor.php
No syntax errors detected in app/Models/Patient.php
No syntax errors detected in app/Models/MedicalSpecialty.php
No syntax errors detected in app/Services/AppointmentService.php
... (30 archivos analizados) -> 0 errores sintácticos.
```

---

### 1.5. Ejecución en Vivo de la Suite de Control de Calidad (`php tests/run_tests.php`)

```text
> php tests/run_tests.php

--- 1. PRUEBAS INDIVIDUALES: ENUM APPOINTMENT STATUS & TRANSICIONES ---
  ✔ PASS: Label correcto para PENDING [Esperado: 'Pendiente', Obtenido: 'Pendiente']
  ✔ PASS: Label correcto para CONFIRMED [Esperado: 'Confirmada', Obtenido: 'Confirmada']
  ✔ PASS: Label correcto para RESCHEDULED [Esperado: 'Reprogramada', Obtenido: 'Reprogramada']
  ✔ PASS: Label correcto para CANCELLED [Esperado: 'Cancelada', Obtenido: 'Cancelada']
  ✔ PASS: Label correcto para ATTENDED [Esperado: 'Atendida', Obtenido: 'Atendida']
  ✔ PASS: Label correcto para NO_SHOW [Esperado: 'No Asistió', Obtenido: 'No Asistió']
  ✔ PASS: Badge amber para PENDING
  ✔ PASS: Badge emerald para CONFIRMED
  ✔ PASS: Badge blue para RESCHEDULED
  ✔ PASS: Badge rose para CANCELLED
  ✔ PASS: Badge purple para ATTENDED
  ✔ PASS: ATTENDED es un estado terminal
  ✔ PASS: CANCELLED es un estado terminal
  ✔ PASS: NO_SHOW es un estado terminal
  ✔ PASS: PENDING NO es un estado terminal
  ✔ PASS: CONFIRMED NO es un estado terminal
  ✔ PASS: RESCHEDULED NO es un estado terminal
  ✔ PASS: PENDING -> CONFIRMED permitido
  ✔ PASS: PENDING -> CANCELLED permitido
  ✔ PASS: PENDING -> RESCHEDULED permitido
  ✔ PASS: PENDING -> ATTENDED bloqueado (debe confirmarse primero)
  ✔ PASS: CONFIRMED -> ATTENDED permitido
  ✔ PASS: CONFIRMED -> CANCELLED permitido
  ✔ PASS: CONFIRMED -> RESCHEDULED permitido
  ✔ PASS: CONFIRMED -> NO_SHOW permitido
  ✔ PASS: CONFIRMED -> PENDING bloqueado
  ✔ PASS: CANCELLED no puede pasar a CONFIRMED
  ✔ PASS: ATTENDED no puede pasar a CANCELLED
  ✔ PASS: NO_SHOW no puede pasar a CONFIRMED

--- 2. PRUEBAS INDIVIDUALES: ALGORITMO DE TRASLAPE TEMPORAL Y LÍMITES ---
  ✔ PASS: Mismo horario exacto detecta conflicto
  ✔ PASS: Traslape superior detecta conflicto
  ✔ PASS: Traslape inferior detecta conflicto
  ✔ PASS: Intervalo envolvente detecta conflicto
  ✔ PASS: Cita inmediatamente contigua posterior NO tiene conflicto
  ✔ PASS: Cita inmediatamente contigua previa NO tiene conflicto
  ✔ PASS: Cita en horario distante NO tiene conflicto
  ✔ PASS: Cálculo de slot rápido 15 min [Esperado: '08:15', Obtenido: '08:15']
  ✔ PASS: Cálculo de slot rápido 30 min [Esperado: '08:30', Obtenido: '08:30']
  ✔ PASS: Cálculo de slot rápido 45 min [Esperado: '08:45', Obtenido: '08:45']
  ✔ PASS: Cálculo de slot rápido 60 min [Esperado: '09:00', Obtenido: '09:00']

--- 3. PRUEBAS GENERALES: DEFINICIÓN DE RUTAS Y ESTRUCTURA DE CONTROLADORES ---
  ✔ PASS: Ruta API /appointments registrada
  ✔ PASS: Ruta API /calendar/events registrada
  ✔ PASS: Ruta API /doctors registrada
  ✔ PASS: Ruta API /patients registrada
  ✔ PASS: Ruta API /specialties registrada
  ✔ PASS: Ruta API /status para transiciones registrada
  ✔ PASS: Ruta Web de Calendario registrada
  ✔ PASS: Ruta Web de Reprogramación registrada
  ✔ PASS: Ruta Web de Cancelación registrada
  ✔ PASS: Método calendarEvents() implementado en API
  ✔ PASS: Método reschedule() implementado en API
  ✔ PASS: Método changeStatus() implementado en API
  ✔ PASS: Método calendar() implementado en Web Controller
  ✔ PASS: Cálculo de KPIs 'stats' incluido en Web Controller

=======================================================
 RESUMEN DE EJECUCIÓN DE PRUEBAS AUTOMATIZADAS (QA)
=======================================================
 Total Pasadas: 54
 Total Falladas: 0
 Estado Global: 100% EXITOSO (TODAS LAS PRUEBAS APROBADAS)
=======================================================
```

---

### 1.6. Arranque del Servidor Web (`php artisan serve`)

```text
> php artisan serve --host=127.0.0.1 --port=8000

   INFO  Server running on [http://127.0.0.1:8000].  

  Press Ctrl+C to stop the server

  2026-09-19 08:35:05 /appointments/calendar .......................................................... ~ 1s [200 OK]
  2026-09-19 08:35:11 /api/v1/calendar/events ......................................................... ~ 1s [200 OK]
  2026-09-19 08:35:17 / .......................................................................... ~ 516ms [302 FOUND]
```

---

## 2. Evidencia de Respuestas de API REST en Vivo (HTTP Status y JSON)

Las siguientes peticiones y respuestas fueron capturadas en tiempo real contra el servidor activo:

### 2.1. Catálogo de Especialidades Médicas
* **Petición:** `GET /api/v1/specialties`
* **Código HTTP:** **`200 OK`**
* **Respuesta JSON:**
```json
[
  {
    "id": 1,
    "name": "Cardiología",
    "code": "CARD-01",
    "description": "Diagnóstico y tratamiento de afecciones cardíacas y vasculares.",
    "is_active": true
  },
  {
    "id": 2,
    "name": "Pediatría",
    "code": "PED-01",
    "description": "Atención médica integral infantil y del adolescente.",
    "is_active": true
  },
  {
    "id": 3,
    "name": "Traumatología y Ortopedia",
    "code": "TRAUM-01",
    "description": "Tratamiento de lesiones del sistema musculoesquelético.",
    "is_active": true
  },
  {
    "id": 4,
    "name": "Medicina General",
    "code": "MEDGEN-01",
    "description": "Atención médica primaria y preventiva.",
    "is_active": true
  }
]
```

---

### 2.2. Catálogo de Médicos Activos
* **Petición:** `GET /api/v1/doctors`
* **Código HTTP:** **`200 OK`**
* **Respuesta JSON:**
```json
{
  "data": [
    {
      "id": 1,
      "full_name": "Dr. Carlos Mendoza",
      "first_name": "Carlos",
      "last_name": "Mendoza",
      "license_number": "COL-10294",
      "email": "carlos.mendoza@hospital.local",
      "phone": "+502 5555-1234",
      "consulting_room": "Edificio A - Consultorio 204",
      "is_active": true,
      "specialty": {
        "id": 1,
        "name": "Cardiología",
        "code": "CARD-01"
      },
      "appointments_count": 1,
      "created_at": "2026-09-19T14:34:35.000000Z"
    },
    {
      "id": 2,
      "full_name": "Dr. Ana Gómez",
      "first_name": "Ana",
      "last_name": "Gómez",
      "license_number": "COL-11452",
      "email": "ana.gomez@hospital.local",
      "phone": "+502 5555-5678",
      "consulting_room": "Edificio B - Consultorio 101",
      "is_active": true,
      "specialty": {
        "id": 2,
        "name": "Pediatría",
        "code": "PED-01"
      },
      "appointments_count": 1,
      "created_at": "2026-09-19T14:34:35.000000Z"
    }
  ]
}
```

---

### 2.3. Eventos Formateados para FullCalendar v6
* **Petición:** `GET /api/v1/calendar/events`
* **Código HTTP:** **`200 OK`**
* **Respuesta JSON:**
```json
[
  {
    "id": "1",
    "title": "Mario López - Dr. Carlos Mendoza",
    "start": "2026-09-24T09:00:00",
    "end": "2026-09-24T09:30:00",
    "backgroundColor": "#10b981",
    "borderColor": "#059669",
    "textColor": "#ffffff",
    "editable": true,
    "extendedProps": {
      "code": "CITA-202609-001",
      "status": "confirmed",
      "status_label": "Confirmada",
      "badge_color": "bg-emerald-100 text-emerald-800 border-emerald-200",
      "patient_name": "Mario López",
      "patient_id": 1,
      "doctor_name": "Dr. Carlos Mendoza",
      "doctor_id": 1,
      "specialty_name": "Cardiología",
      "reason": "Chequeo rutinario por presión arterial elevada.",
      "clinical_notes": "Paciente refiere mareos esporádicos al despertar.",
      "start_time": "09:00",
      "end_time": "09:30",
      "date": "2026-09-24"
    }
  },
  {
    "id": "2",
    "title": "Elena Morales - Dr. Ana Gómez",
    "start": "2026-09-25T10:00:00",
    "end": "2026-09-25T10:30:00",
    "backgroundColor": "#3b82f6",
    "borderColor": "#2563eb",
    "textColor": "#ffffff",
    "editable": true,
    "extendedProps": {
      "code": "CITA-202609-002",
      "status": "rescheduled",
      "status_label": "Reprogramada",
      "badge_color": "bg-blue-100 text-blue-800 border-blue-200",
      "patient_name": "Elena Morales",
      "patient_id": 2,
      "doctor_name": "Dr. Ana Gómez",
      "doctor_id": 2,
      "specialty_name": "Pediatría",
      "reason": "Control de crecimiento y vacunas de pediatría.",
      "clinical_notes": "Esquema de vacunación al día.",
      "start_time": "10:00",
      "end_time": "10:30",
      "date": "2026-09-25"
    }
  },
  {
    "id": "3",
    "title": "Jorge Herrera - Dr. Roberto Castillo",
    "start": "2026-09-21T11:00:00",
    "end": "2026-09-21T11:30:00",
    "backgroundColor": "#ef4444",
    "borderColor": "#dc2626",
    "textColor": "#ffffff",
    "editable": false,
    "extendedProps": {
      "code": "CITA-202609-003",
      "status": "cancelled",
      "status_label": "Cancelada",
      "badge_color": "bg-rose-100 text-rose-800 border-rose-200",
      "patient_name": "Jorge Herrera",
      "patient_id": 3,
      "doctor_name": "Dr. Roberto Castillo",
      "doctor_id": 3,
      "specialty_name": "Traumatología y Ortopedia",
      "reason": "Evaluación por dolor persistente en rodilla derecha.",
      "clinical_notes": "Radiografía previa sin hallazgo de fractura.",
      "start_time": "11:00",
      "end_time": "11:30",
      "date": "2026-09-21"
    }
  }
]
```

---

### 2.4. Validación de Campos Obligatorios
* **Petición:** `POST /api/v1/appointments` con payload vacío `{}`
* **Código HTTP:** **`422 Unprocessable Entity`**
* **Respuesta JSON:**
```json
{
  "message": "Debe seleccionar un paciente. (and 6 more errors)",
  "errors": {
    "patient_id": [
      "Debe seleccionar un paciente."
    ],
    "doctor_id": [
      "Debe asignar un médico a la cita."
    ],
    "specialty_id": [
      "Debe indicar la especialidad médica."
    ],
    "appointment_date": [
      "La fecha de la cita es obligatoria."
    ],
    "start_time": [
      "La hora de inicio es obligatoria."
    ],
    "end_time": [
      "La hora de fin es obligatoria."
    ],
    "reason": [
      "Debe especificar el motivo de la cita médica."
    ]
  }
}
```

---

### 2.5. Rechazo por Conflicto de Horario / Doble Reserva (**RQNF-03**)
* **Petición:** `POST /api/v1/appointments`
```json
{
  "patient_id": 1,
  "doctor_id": 1,
  "specialty_id": 1,
  "appointment_date": "2026-09-24",
  "start_time": "09:00",
  "end_time": "09:30",
  "reason": "Intento de doble reserva intencional"
}
```
* **Código HTTP:** **`409 Conflict`**
* **Respuesta JSON:**
```json
{
  "status": 409,
  "error": "Conflict",
  "message": "Conflicto de horario: El médico ya tiene la cita 'CITA-202609-001' agendada de 09:00 a 09:30 en esa misma fecha.",
  "errors": {
    "doctor_id": [
      "Conflicto de horario: El médico ya tiene la cita 'CITA-202609-001' agendada de 09:00 a 09:30 en esa misma fecha."
    ]
  }
}
```

---

### 2.6. Creación Exitosa de Cita Médica
* **Petición:** `POST /api/v1/appointments`
```json
{
  "patient_id": 2,
  "doctor_id": 2,
  "specialty_id": 2,
  "appointment_date": "2026-09-28",
  "start_time": "16:00",
  "end_time": "16:30",
  "reason": "Chequeo pediátrico semestral",
  "clinical_notes": "Sin antecedentes alérgicos."
}
```
* **Código HTTP:** **`201 Created`**
* **Respuesta JSON:**
```json
{
  "id": 5,
  "code": "CITA-202609-7ECC3",
  "status": {
    "value": "pending",
    "label": "Pendiente"
  },
  "date": "2026-09-28",
  "start_time": "16:00",
  "end_time": "16:30",
  "reason": "Chequeo pediátrico semestral",
  "clinical_notes": "Sin antecedentes alérgicos.",
  "cancellation_reason": null,
  "cancelled_at": null,
  "reschedule_reason": null,
  "rescheduled_at": null,
  "patient": {
    "id": 2,
    "full_name": "Elena Morales",
    "medical_record_number": "EXP-2026-0002",
    "phone": "+502 4444-3333",
    "email": "elena.morales@email.com"
  },
  "doctor": {
    "id": 2,
    "full_name": "Dr. Ana Gómez",
    "consulting_room": "Edificio B - Consultorio 101",
    "specialty": "Pediatría"
  },
  "specialty": {
    "id": 2,
    "name": "Pediatría"
  },
  "created_at": "2026-09-19T14:48:01.000000Z",
  "updated_at": "2026-09-19T14:48:01.000000Z"
}
```

---

### 2.7. Recurso No Encontrado (404 Not Found)
* **Petición:** `GET /api/v1/appointments/999`
* **Código HTTP:** **`404 Not Found`**
* **Respuesta JSON:**
```json
{
  "status": 404,
  "error": "Not Found",
  "message": "La cita médica solicitada no existe en el sistema hospitalario."
}
```

---

## 3. Certificación de Evidencias y Cumplimiento

Todas las salidas de consola, comandos, códigos de respuesta HTTP (`200`, `201`, `404`, `409`, `422`) y flujos han sido validados directamente contra el entorno en ejecución, certificando el cumplimiento total del requisito **RQNF-08**.
