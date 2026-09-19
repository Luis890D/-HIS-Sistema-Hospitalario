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
        $cardio = MedicalSpecialty::create([
            'name' => 'Cardiología',
            'code' => 'CARD-01',
            'description' => 'Diagnóstico y tratamiento de enfermedades del corazón y vasos sanguíneos.',
        ]);

        $pediatria = MedicalSpecialty::create([
            'name' => 'Pediatría',
            'code' => 'PED-01',
            'description' => 'Atención médica de bebés, niños y adolescentes.',
        ]);

        $trauma = MedicalSpecialty::create([
            'name' => 'Traumatología y Ortopedia',
            'code' => 'TRAUM-01',
            'description' => 'Tratamiento de lesiones del aparato locomotor.',
        ]);

        // 2. Doctores
        $dr1 = Doctor::create([
            'specialty_id' => $cardio->id,
            'license_number' => 'COL-10294',
            'first_name' => 'Carlos',
            'last_name' => 'Mendoza',
            'email' => 'carlos.mendoza@hospital.local',
            'phone' => '+502 5555-1234',
            'consulting_room' => 'Edificio A - Consultorio 204',
        ]);

        $dr2 = Doctor::create([
            'specialty_id' => $pediatria->id,
            'license_number' => 'COL-11452',
            'first_name' => 'Ana',
            'last_name' => 'Gómez',
            'email' => 'ana.gomez@hospital.local',
            'phone' => '+502 5555-5678',
            'consulting_room' => 'Edificio B - Consultorio 101',
        ]);

        // 3. Pacientes
        $p1 = Patient::create([
            'medical_record_number' => 'EXP-2026-0001',
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
        ]);

        $p2 = Patient::create([
            'medical_record_number' => 'EXP-2026-0002',
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
        ]);

        // 4. Cita médica de demostración
        Appointment::create([
            'appointment_code' => 'CITA-202609-001',
            'patient_id' => $p1->id,
            'doctor_id' => $dr1->id,
            'specialty_id' => $cardio->id,
            'appointment_date' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'start_time' => '09:00:00',
            'end_time' => '09:30:00',
            'status' => AppointmentStatus::PENDING,
            'reason' => 'Chequeo rutinario por presión arterial elevada.',
            'clinical_notes' => 'Paciente refiere mareos esporádicos al despertar.',
        ]);
    }
}
