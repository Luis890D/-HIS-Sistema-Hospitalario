<?php

use App\Http\Controllers\Api\V1\AppointmentApiController;
use App\Http\Controllers\Api\V1\DoctorApiController;
use App\Http\Controllers\Api\V1\PatientApiController;
use App\Http\Controllers\Api\V1\SpecialtyApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - HIS Módulo de Citas Médicas (V1)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // 1. Endpoints de Citas Médicas (CRUD & Ciclo de Vida)
    Route::get('/appointments', [AppointmentApiController::class, 'index'])->name('api.v1.appointments.index');
    Route::post('/appointments', [AppointmentApiController::class, 'store'])->name('api.v1.appointments.store');
    Route::get('/appointments/{id}', [AppointmentApiController::class, 'show'])->name('api.v1.appointments.show');
    Route::match(['put', 'patch'], '/appointments/{id}', [AppointmentApiController::class, 'update'])->name('api.v1.appointments.update');
    Route::match(['put', 'patch'], '/appointments/{id}/reschedule', [AppointmentApiController::class, 'reschedule'])->name('api.v1.appointments.reschedule');
    Route::match(['put', 'patch'], '/appointments/{id}/cancel', [AppointmentApiController::class, 'cancel'])->name('api.v1.appointments.cancel');
    Route::patch('/appointments/{id}/status', [AppointmentApiController::class, 'changeStatus'])->name('api.v1.appointments.status');
    Route::get('/calendar/events', [AppointmentApiController::class, 'calendarEvents'])->name('api.v1.calendar.events');

    // 2. Endpoints de Lectura de Médicos
    Route::get('/doctors', [DoctorApiController::class, 'index'])->name('api.v1.doctors.index');
    Route::get('/doctors/{id}', [DoctorApiController::class, 'show'])->name('api.v1.doctors.show');

    // 3. Endpoints de Lectura de Pacientes
    Route::get('/patients', [PatientApiController::class, 'index'])->name('api.v1.patients.index');
    Route::get('/patients/{id}', [PatientApiController::class, 'show'])->name('api.v1.patients.show');

    // 4. Endpoints de Lectura de Especialidades Médicas
    Route::get('/specialties', [SpecialtyApiController::class, 'index'])->name('api.v1.specialties.index');
});
