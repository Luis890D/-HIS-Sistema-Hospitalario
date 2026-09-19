-- ==========================================================
-- HIS - Sistema Hospitalario: Módulo de Control de Citas
-- Script de Inicialización para Contenedor Docker MySQL 8.0
-- ==========================================================

CREATE DATABASE IF NOT EXISTS his_hospital CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE his_hospital;

-- 1. Tabla de Especialidades Médicas
CREATE TABLE IF NOT EXISTS medical_specialties (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    code VARCHAR(20) NOT NULL UNIQUE,
    description TEXT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tabla de Doctores
CREATE TABLE IF NOT EXISTS doctors (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    specialty_id BIGINT UNSIGNED NOT NULL,
    license_number VARCHAR(50) NOT NULL UNIQUE COMMENT 'Número de colegiado médico',
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(30) NULL,
    consulting_room VARCHAR(50) NULL COMMENT 'Consultorio asignado',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_doctors_specialty FOREIGN KEY (specialty_id) 
        REFERENCES medical_specialties (id) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Tabla de Pacientes
CREATE TABLE IF NOT EXISTS patients (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    medical_record_number VARCHAR(50) NOT NULL UNIQUE COMMENT 'No. Expediente / Historia Clínica',
    identification_number VARCHAR(50) NOT NULL UNIQUE COMMENT 'DPI / DNI / Pasaporte',
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    birth_date DATE NOT NULL,
    gender ENUM('male', 'female', 'other') NOT NULL DEFAULT 'other',
    blood_type VARCHAR(10) NULL,
    phone VARCHAR(30) NOT NULL,
    email VARCHAR(150) NULL,
    emergency_contact_name VARCHAR(150) NULL,
    emergency_contact_phone VARCHAR(30) NULL,
    allergies TEXT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Tabla de Citas Médicas (Agendar, Reprogramar, Cancelar)
CREATE TABLE IF NOT EXISTS appointments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    appointment_code VARCHAR(30) NOT NULL UNIQUE COMMENT 'Código único visible de cita (ej. CITA-202609-001)',
    patient_id BIGINT UNSIGNED NOT NULL,
    doctor_id BIGINT UNSIGNED NOT NULL,
    specialty_id BIGINT UNSIGNED NOT NULL,
    appointment_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'pending' COMMENT 'pending, confirmed, rescheduled, cancelled, attended, no_show',
    reason TEXT NOT NULL COMMENT 'Motivo de consulta médica',
    clinical_notes TEXT NULL COMMENT 'Observaciones y notas clínicas de triaje',
    cancellation_reason TEXT NULL COMMENT 'Motivo obligatorio en caso de cancelación',
    cancelled_at TIMESTAMP NULL,
    rescheduled_from_id BIGINT UNSIGNED NULL COMMENT 'Cita previa si fue reprogramada',
    reschedule_reason TEXT NULL COMMENT 'Motivo obligatorio en caso de reprogramación',
    rescheduled_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Restricciones de llaves foráneas
    CONSTRAINT fk_appointments_patient FOREIGN KEY (patient_id) 
        REFERENCES patients (id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_appointments_doctor FOREIGN KEY (doctor_id) 
        REFERENCES doctors (id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_appointments_specialty FOREIGN KEY (specialty_id) 
        REFERENCES medical_specialties (id) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_appointments_rescheduled_from FOREIGN KEY (rescheduled_from_id) 
        REFERENCES appointments (id) ON UPDATE CASCADE ON DELETE SET NULL,
        
    -- Índices para optimización de consultas y traslapes de horarios
    INDEX idx_doctor_schedule (doctor_id, appointment_date, start_time),
    INDEX idx_patient_schedule (patient_id, appointment_date),
    INDEX idx_appointment_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================================
-- DATOS SEMILLA MÍNIMOS (SEEDS)
-- ==========================================================

-- Especialidades
INSERT INTO medical_specialties (id, name, code, description, is_active) VALUES
(1, 'Cardiología', 'CARD-01', 'Diagnóstico y tratamiento de afecciones cardíacas y vasculares.', 1),
(2, 'Pediatría', 'PED-01', 'Atención médica integral infantil y del adolescente.', 1),
(3, 'Traumatología y Ortopedia', 'TRAUM-01', 'Tratamiento de lesiones del sistema musculoesquelético.', 1),
(4, 'Medicina General', 'MEDGEN-01', 'Atención médica primaria y preventiva.', 1)
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Doctores
INSERT INTO doctors (id, specialty_id, license_number, first_name, last_name, email, phone, consulting_room, is_active) VALUES
(1, 1, 'COL-10294', 'Carlos', 'Mendoza', 'carlos.mendoza@hospital.local', '+502 5555-1234', 'Edificio A - Consultorio 204', 1),
(2, 2, 'COL-11452', 'Ana', 'Gómez', 'ana.gomez@hospital.local', '+502 5555-5678', 'Edificio B - Consultorio 101', 1),
(3, 3, 'COL-12890', 'Roberto', 'Castillo', 'roberto.castillo@hospital.local', '+502 5555-9012', 'Edificio A - Consultorio 105', 1),
(4, 4, 'COL-13500', 'Lucía', 'Fernández', 'lucia.fernandez@hospital.local', '+502 5555-3456', 'Edificio Central - Consultorio 12', 1)
ON DUPLICATE KEY UPDATE license_number=VALUES(license_number);

-- Pacientes
INSERT INTO patients (id, medical_record_number, identification_number, first_name, last_name, birth_date, gender, blood_type, phone, email, emergency_contact_name, emergency_contact_phone, allergies) VALUES
(1, 'EXP-2026-0001', '2548963210101', 'Mario', 'López', '1988-04-12', 'male', 'O+', '+502 4444-1111', 'mario.lopez@email.com', 'Sonia de López', '+502 4444-2222', 'Penicilina'),
(2, 'EXP-2026-0002', '3012547890101', 'Elena', 'Morales', '1995-11-20', 'female', 'A+', '+502 4444-3333', 'elena.morales@email.com', 'Roberto Morales', '+502 4444-4444', 'Ninguna conocida'),
(3, 'EXP-2026-0003', '1985472360101', 'Jorge', 'Herrera', '1975-08-05', 'male', 'B+', '+502 4444-5555', 'jorge.herrera@email.com', 'Carla Herrera', '+502 4444-6666', 'Sulfamidas')
ON DUPLICATE KEY UPDATE identification_number=VALUES(identification_number);

-- Citas de Demostración (Agendada, Reprogramada, Cancelada)
INSERT INTO appointments (id, appointment_code, patient_id, doctor_id, specialty_id, appointment_date, start_time, end_time, status, reason, clinical_notes, cancellation_reason, cancelled_at, rescheduled_from_id, reschedule_reason, rescheduled_at) VALUES
-- Cita 1: Confirmada / Pendiente
(1, 'CITA-202609-001', 1, 1, 1, '2026-09-25', '09:00:00', '09:30:00', 'confirmed', 'Chequeo rutinario por presión arterial elevada.', 'Paciente refiere mareos esporádicos al despertar.', NULL, NULL, NULL, NULL, NULL),

-- Cita 2: Reprogramada con trazabilidad
(2, 'CITA-202609-002', 2, 2, 2, '2026-09-26', '10:00:00', '10:30:00', 'rescheduled', 'Control de crecimiento y vacunas de pediatría.', 'Esquema de vacunación al día.', NULL, NULL, NULL, 'Solicitud de los padres por viaje fuera del departamento.', '2026-09-19 07:30:00'),

-- Cita 3: Cancelada con motivo obligatorio
(3, 'CITA-202609-003', 3, 3, 3, '2026-09-22', '11:00:00', '11:30:00', 'cancelled', 'Evaluación por dolor persistente en rodilla derecha.', 'Radiografía previa sin hallazgo de fractura.', 'Paciente notificó que debió viajar por emergencia laboral.', '2026-09-19 07:32:00', NULL, NULL, NULL)
ON DUPLICATE KEY UPDATE appointment_code=VALUES(appointment_code);
