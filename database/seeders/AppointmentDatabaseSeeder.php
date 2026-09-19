<?php

namespace Database\Seeders;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalSpecialty;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AppointmentDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Especialidades Médicas
        $cardio = MedicalSpecialty::firstOrCreate(
            ['code' => 'CARD-01'],
            [
                'name' => 'Cardiología',
                'description' => 'Diagnóstico y tratamiento de afecciones cardíacas y vasculares.',
                'is_active' => true,
            ]
        );

        $pediatria = MedicalSpecialty::firstOrCreate(
            ['code' => 'PED-01'],
            [
                'name' => 'Pediatría',
                'description' => 'Atención médica integral infantil y del adolescente.',
                'is_active' => true,
            ]
        );

        $trauma = MedicalSpecialty::firstOrCreate(
            ['code' => 'TRAUM-01'],
            [
                'name' => 'Traumatología y Ortopedia',
                'description' => 'Tratamiento de lesiones del sistema musculoesquelético.',
                'is_active' => true,
            ]
        );

        $medgen = MedicalSpecialty::firstOrCreate(
            ['code' => 'MEDGEN-01'],
            [
                'name' => 'Medicina General',
                'description' => 'Atención médica primaria y preventiva.',
                'is_active' => true,
            ]
        );

        // 2. Doctores
        $dr1 = Doctor::firstOrCreate(
            ['license_number' => 'COL-10294'],
            [
                'specialty_id' => $cardio->id,
                'first_name' => 'Carlos',
                'last_name' => 'Mendoza',
                'email' => 'carlos.mendoza@hospital.local',
                'phone' => '+502 5555-1234',
                'consulting_room' => 'Edificio A - Consultorio 204',
                'is_active' => true,
            ]
        );

        $dr2 = Doctor::firstOrCreate(
            ['license_number' => 'COL-11452'],
            [
                'specialty_id' => $pediatria->id,
                'first_name' => 'Ana',
                'last_name' => 'Gómez',
                'email' => 'ana.gomez@hospital.local',
                'phone' => '+502 5555-5678',
                'consulting_room' => 'Edificio B - Consultorio 101',
                'is_active' => true,
            ]
        );

        $dr3 = Doctor::firstOrCreate(
            ['license_number' => 'COL-12890'],
            [
                'specialty_id' => $trauma->id,
                'first_name' => 'Roberto',
                'last_name' => 'Castillo',
                'email' => 'roberto.castillo@hospital.local',
                'phone' => '+502 5555-9012',
                'consulting_room' => 'Edificio A - Consultorio 105',
                'is_active' => true,
            ]
        );

        $dr4 = Doctor::firstOrCreate(
            ['license_number' => 'COL-13500'],
            [
                'specialty_id' => $medgen->id,
                'first_name' => 'Lucía',
                'last_name' => 'Fernández',
                'email' => 'lucia.fernandez@hospital.local',
                'phone' => '+502 5555-3456',
                'consulting_room' => 'Edificio Central - Consultorio 12',
                'is_active' => true,
            ]
        );

        // 3. Pacientes
        $p1 = Patient::firstOrCreate(
            ['medical_record_number' => 'EXP-2026-0001'],
            [
                'identification_number' => '2548963210101',
                'first_name' => 'Mario',
                'last_name' => 'López',
                'birth_date' => '1988-04-12',
                'gender' => 'male',
                'blood_type' => 'O+',
                'phone' => '+502 4444-1111',
                'email' => 'mario.lopez@email.com',
                'emergency_contact_name' => 'Sonia de López',
                'emergency_contact_phone' => '+502 4444-2222',
                'allergies' => 'Penicilina',
            ]
        );

        $p2 = Patient::firstOrCreate(
            ['medical_record_number' => 'EXP-2026-0002'],
            [
                'identification_number' => '3012547890101',
                'first_name' => 'Elena',
                'last_name' => 'Morales',
                'birth_date' => '1995-11-20',
                'gender' => 'female',
                'blood_type' => 'A+',
                'phone' => '+502 4444-3333',
                'email' => 'elena.morales@email.com',
                'emergency_contact_name' => 'Roberto Morales',
                'emergency_contact_phone' => '+502 4444-4444',
                'allergies' => 'Ninguna conocida',
            ]
        );

        $p3 = Patient::firstOrCreate(
            ['medical_record_number' => 'EXP-2026-0003'],
            [
                'identification_number' => '1985472360101',
                'first_name' => 'Jorge',
                'last_name' => 'Herrera',
                'birth_date' => '1975-08-05',
                'gender' => 'male',
                'blood_type' => 'B+',
                'phone' => '+502 4444-5555',
                'email' => 'jorge.herrera@email.com',
                'emergency_contact_name' => 'Carla Herrera',
                'emergency_contact_phone' => '+502 4444-6666',
                'allergies' => 'Sulfamidas',
            ]
        );

        // 4. Citas Médicas Semilla (Agendada, Reprogramada y Cancelada)
        Appointment::firstOrCreate(
            ['appointment_code' => 'CITA-202609-001'],
            [
                'patient_id' => $p1->id,
                'doctor_id' => $dr1->id,
                'specialty_id' => $cardio->id,
                'appointment_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
                'start_time' => '09:00:00',
                'end_time' => '09:30:00',
                'status' => AppointmentStatus::CONFIRMED,
                'reason' => 'Chequeo rutinario por presión arterial elevada.',
                'clinical_notes' => 'Paciente refiere mareos esporádicos al despertar.',
            ]
        );

        Appointment::firstOrCreate(
            ['appointment_code' => 'CITA-202609-002'],
            [
                'patient_id' => $p2->id,
                'doctor_id' => $dr2->id,
                'specialty_id' => $pediatria->id,
                'appointment_date' => Carbon::now()->addDays(6)->format('Y-m-d'),
                'start_time' => '10:00:00',
                'end_time' => '10:30:00',
                'status' => AppointmentStatus::RESCHEDULED,
                'reason' => 'Control de crecimiento y vacunas de pediatría.',
                'clinical_notes' => 'Esquema de vacunación al día.',
                'reschedule_reason' => 'Solicitud de los padres por viaje fuera del departamento.',
                'rescheduled_at' => Carbon::now()->subHours(2),
            ]
        );

        Appointment::firstOrCreate(
            ['appointment_code' => 'CITA-202609-003'],
            [
                'patient_id' => $p3->id,
                'doctor_id' => $dr3->id,
                'specialty_id' => $trauma->id,
                'appointment_date' => Carbon::now()->addDays(2)->format('Y-m-d'),
                'start_time' => '11:00:00',
                'end_time' => '11:30:00',
                'status' => AppointmentStatus::CANCELLED,
                'reason' => 'Evaluación por dolor persistente en rodilla derecha.',
                'clinical_notes' => 'Radiografía previa sin hallazgo de fractura.',
                'cancellation_reason' => 'Paciente notificó que debió viajar por emergencia laboral.',
                'cancelled_at' => Carbon::now()->subHours(1),
            ]
        );
    }
}
