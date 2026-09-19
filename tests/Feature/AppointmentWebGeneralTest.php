<?php

namespace Tests\Feature;

use Tests\TestCase;

class AppointmentWebGeneralTest extends TestCase
{
    /**
     * Prueba General: Redirección de la raíz '/' al panel de citas '/appointments'
     */
    public function test_root_redirects_to_appointments_index(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/appointments');
    }

    /**
     * Prueba General: Las rutas del módulo Web de citas están debidamente nombradas
     */
    public function test_web_routes_are_registered(): void
    {
        $this->assertTrue(\Route::has('appointments.index'));
        $this->assertTrue(\Route::has('appointments.calendar'));
        $this->assertTrue(\Route::has('appointments.create'));
        $this->assertTrue(\Route::has('appointments.store'));
        $this->assertTrue(\Route::has('appointments.show'));
        $this->assertTrue(\Route::has('appointments.reschedule.form'));
        $this->assertTrue(\Route::has('appointments.reschedule'));
        $this->assertTrue(\Route::has('appointments.cancel'));
    }

    /**
     * Prueba General: Validación en formulario web de creación (POST /appointments)
     * Campos vacíos deben redirigir con errores de sesión.
     */
    public function test_web_store_appointment_requires_fields_and_redirects(): void
    {
        $response = $this->post('/appointments', []);

        $response->assertSessionHasErrors([
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
     * Prueba General: Cancelación web requiere justificación
     */
    public function test_web_cancel_appointment_requires_reason(): void
    {
        $response = $this->put('/appointments/1/cancel', []);

        $response->assertSessionHasErrors(['cancellation_reason']);
    }
}
