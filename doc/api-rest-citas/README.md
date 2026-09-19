# Documentación de API REST: Rama `feature/api-rest-citas`

**Módulo:** Control de Citas Médicas y Catálogos Clínicos  
**Versión API:** `v1` (`/api/v1/`)  
**Rama:** `feature/api-rest-citas`  
**Formato de Intercambio:** `application/json`

---

## 1. Resumen de Endpoints Disponibles

### Citas Médicas (`/api/v1/appointments`)
| Método | Endpoint | Acción | Descripción |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/appointments` | Listar | Listado paginado con filtros (`date`, `doctor_id`, `patient_id`, `status`). |
| `POST` | `/api/v1/appointments` | **Crear / Agendar** | Agenda una nueva cita médica comprobando disponibilidad de horario. |
| `GET` | `/api/v1/appointments/{id}` | Consultar | Detalle clínico completo de la cita médica. |
| `PUT / PATCH` | `/api/v1/appointments/{id}` | **Actualizar** | Modifica datos generales (notas, motivo o cambio de médico/horario). |
| `PUT / PATCH` | `/api/v1/appointments/{id}/reschedule`| **Reprogramar** | Reprograma fecha/hora con registro obligatorio del motivo. |
| `PUT / PATCH` | `/api/v1/appointments/{id}/cancel` | **Cancelar** | Cancela la cita con justificación obligatoria y libera el horario. |
| `PATCH` | `/api/v1/appointments/{id}/status` | **Cambiar Estado** | Transición de estado (`confirmed`, `attended`, `no_show`, etc.). |

### Lectura de Médicos (`/api/v1/doctors`)
| Método | Endpoint | Acción | Descripción |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/doctors` | Listar | Lista de médicos con especialidad, filtrable por `specialty_id`, `is_active`, `search`. |
| `GET` | `/api/v1/doctors/{id}` | Consultar | Detalle del médico y sus próximas citas agendadas. |

### Lectura de Pacientes (`/api/v1/patients`)
| Método | Endpoint | Acción | Descripción |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/patients` | Listar | Catálogo de pacientes con búsqueda por nombre, DPI o expediente. |
| `GET` | `/api/v1/patients/{id}` | Consultar | Expediente clínico del paciente y su historial de citas previas. |

### Lectura de Especialidades (`/api/v1/specialties`)
| Método | Endpoint | Acción | Descripción |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/specialties` | Listar | Catálogo de especialidades médicas activas. |

---

## 2. Detalle y Ejemplos de Peticiones y Respuestas

### 2.1. Listar Citas Médicas
**`GET /api/v1/appointments?status=confirmed&per_page=10`**

#### Parámetros Query:
- `date`: `YYYY-MM-DD` (filtra citas para un día en particular).
- `doctor_id`: Entero (filtra por médico).
- `patient_id`: Entero (filtra por paciente).
- `status`: `pending`, `confirmed`, `rescheduled`, `cancelled`, `attended`, `no_show`.
- `per_page`: Entero (por defecto 15).

#### Respuesta `200 OK`:
```json
{
  "data": [
    {
      "id": 1,
      "code": "CITA-202609-001",
      "status": {
        "value": "confirmed",
        "label": "Confirmada"
      },
      "date": "2026-09-25",
      "start_time": "09:00",
      "end_time": "09:30",
      "reason": "Chequeo rutinario por presión arterial elevada.",
      "clinical_notes": "Paciente refiere mareos esporádicos al despertar.",
      "patient": {
        "id": 1,
        "full_name": "Mario López",
        "medical_record_number": "EXP-2026-0001",
        "phone": "+502 4444-1111",
        "email": "mario.lopez@email.com"
      },
      "doctor": {
        "id": 1,
        "full_name": "Dr. Carlos Mendoza",
        "consulting_room": "Edificio A - Consultorio 204",
        "specialty": "Cardiología"
      },
      "specialty": {
        "id": 1,
        "name": "Cardiología"
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "total": 1
  }
}
```

---

### 2.2. Agendar Cita Médica
**`POST /api/v1/appointments`**

#### Payload de Solicitud:
```json
{
  "patient_id": 1,
  "doctor_id": 1,
  "specialty_id": 1,
  "appointment_date": "2026-10-05",
  "start_time": "14:00",
  "end_time": "14:30",
  "reason": "Control post-tratamiento de hipertensión",
  "clinical_notes": "Traer último electrocardiograma."
}
```

#### Respuesta `201 Created`:
```json
{
  "id": 4,
  "code": "CITA-202609-AB123",
  "status": {
    "value": "pending",
    "label": "Pendiente"
  },
  "date": "2026-10-05",
  "start_time": "14:00",
  "end_time": "14:30",
  "reason": "Control post-tratamiento de hipertensión",
  "patient": { "id": 1, "full_name": "Mario López" },
  "doctor": { "id": 1, "full_name": "Dr. Carlos Mendoza" }
}
```

---

### 2.3. Actualizar Información de la Cita
**`PUT /api/v1/appointments/1`**

#### Payload de Solicitud:
```json
{
  "reason": "Control post-operatorio e inspección de suturas",
  "clinical_notes": "Se retiran puntos en consulta."
}
```

#### Respuesta `200 OK`:
```json
{
  "success": true,
  "message": "Cita médica actualizada correctamente.",
  "data": {
    "id": 1,
    "code": "CITA-202609-001",
    "reason": "Control post-operatorio e inspección de suturas"
  }
}
```

---

### 2.4. Reprogramar Cita Médica
**`PUT /api/v1/appointments/1/reschedule`**

#### Payload de Solicitud:
```json
{
  "appointment_date": "2026-10-10",
  "start_time": "11:00",
  "end_time": "11:30",
  "reschedule_reason": "Paciente solicitó cambio de día por motivos de viaje."
}
```

#### Respuesta `200 OK`:
```json
{
  "success": true,
  "message": "Cita médica reprogramada exitosamente.",
  "data": {
    "id": 1,
    "status": { "value": "rescheduled", "label": "Reprogramada" },
    "date": "2026-10-10",
    "start_time": "11:00",
    "reschedule_reason": "Paciente solicitó cambio de día por motivos de viaje.",
    "rescheduled_at": "2026-09-19T07:42:00.000000Z"
  }
}
```

---

### 2.5. Cancelar Cita Médica
**`PUT /api/v1/appointments/1/cancel`**

#### Payload de Solicitud:
```json
{
  "cancellation_reason": "Paciente cancela por imposibilidad de traslado al centro hospitalario."
}
```

#### Respuesta `200 OK`:
```json
{
  "success": true,
  "message": "Cita médica cancelada correctamente.",
  "data": {
    "id": 1,
    "status": { "value": "cancelled", "label": "Cancelada" },
    "cancellation_reason": "Paciente cancela por imposibilidad de traslado al centro hospitalario.",
    "cancelled_at": "2026-09-19T07:42:30.000000Z"
  }
}
```

---

### 2.6. Cambiar Estado de la Cita
**`PATCH /api/v1/appointments/1/status`**

#### Payload de Solicitud:
```json
{
  "status": "attended",
  "note": "Consulta finalizada con éxito y receta médica emitida."
}
```

#### Respuesta `200 OK`:
```json
{
  "success": true,
  "message": "Estado de la cita actualizado a 'Atendida'.",
  "data": {
    "id": 1,
    "status": {
      "value": "attended",
      "label": "Atendida"
    }
  }
}
```

---

### 2.7. Lectura de Médicos
**`GET /api/v1/doctors?search=Mendoza`**

#### Respuesta `200 OK`:
```json
{
  "data": [
    {
      "id": 1,
      "full_name": "Dr. Carlos Mendoza",
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
      "appointments_count": 1
    }
  ]
}
```

**`GET /api/v1/doctors/1`** devuelve el detalle del médico con el listado de sus `upcoming_appointments`.

---

### 2.8. Lectura de Pacientes
**`GET /api/v1/patients?search=Mario`**

#### Respuesta `200 OK`:
```json
{
  "data": [
    {
      "id": 1,
      "full_name": "Mario López",
      "medical_record_number": "EXP-2026-0001",
      "identification_number": "2548963210101",
      "birth_date": "1988-04-12",
      "gender": "male",
      "blood_type": "O+",
      "phone": "+502 4444-1111",
      "email": "mario.lopez@email.com",
      "allergies": "Penicilina",
      "appointments_count": 1
    }
  ]
}
```

**`GET /api/v1/patients/1`** devuelve el expediente con el historial `appointments_history`.
