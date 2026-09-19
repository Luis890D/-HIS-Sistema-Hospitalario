<?php

namespace Tests\Feature;

use Tests\TestCase;

class AppointmentApiGeneralTest extends TestCase
{
    /**
     * Prueba General: Validación de existencia y registro de rutas API v1
     */
    public function test_api_v1_routes_are_registered(): void
    {
        $this->assertTrue(\Route::has('api.v1.appointments.index'));
        $this->assertTrue(\Route::has('api.v1.appointments.store'));
        $this->assertTrue(\Route::has('api.v1.appointments.show'));
        $this->assertTrue(\Route::has('api.v1.appointments.reschedule'));
        $this->assertTrue(\Route::has('api.v1.appointments.cancel'));
        $this->assertTrue(\Route::has('api.v1.appointments.status'));
        $this->assertTrue(\Route::has('api.v1.calendar.events'));
        $this->assertTrue(\Route::has('api.v1.doctors.index'));
        $this->assertTrue(\Route::has('api.v1.patients.index'));
        $this->assertTrue(\Route::has('api.v1.specialties.index'));
    }

    /**
     * Prueba General: Validación de payloads en creación de cita (POST /api/v1/appointments)
     * Cuando se envían datos vacíos debe responder HTTP 422 con errores para cada campo obligatorio.
     */
    public function test_store_appointment_requires_mandatory_fields(): void
    {
        $response = $this->postJson('/api/v1/appointments', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'patient_id',
            'doctor_id',
            'specialty_id',
            'appointment_date',
            'start_time',
            'end_time',
            'reason',
        ]);
    }

    /**
     * Prueba General: Validación de hora fin posterior a hora inicio
     */
    public function test_store_appointment_validates_end_time_after_start_time(): void
    {
        $payload = [
            'patient_id'       => 1,
            'doctor_id'        => 1,
            'specialty_id'     => 1,
            'appointment_date' => '2026-10-15',
            'start_time'       => '10:00',
            'end_time'         => '09:00', // Inválido: fin anterior a inicio
            'reason'           => 'Consulta de prueba',
        ];

        $response = $this->postJson('/api/v1/appointments', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['end_time']);
    }

    /**
     * Prueba General: Validación de campos en cancelación de cita (PUT /api/v1/appointments/{id}/cancel)
     * El motivo de cancelación es mandatorio (cancellation_reason).
     */
    public function test_cancel_appointment_requires_cancellation_reason(): void
    {
        $response = $this->putJson('/api/v1/appointments/1/cancel', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['cancellation_reason']);
    }

    /**
     * Prueba General: Validación de campos en cambio de estado (PATCH /api/v1/appointments/{id}/status)
     * El campo status es obligatorio y debe ser un estado clínico válido.
     */
    public function test_change_status_requires_valid_status_enum(): void
    {
        $response = $this->patchJson('/api/v1/appointments/1/status', [
            'status' => 'invalid_status_xyz',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['status']);
    }
}
