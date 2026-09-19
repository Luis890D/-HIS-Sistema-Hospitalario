<?php

use App\Http\Controllers\Api\V1\AppointmentApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - HIS Módulo de Citas Médicas (V1)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Endpoints RESTful de Citas Médicas
    Route::get('/appointments', [AppointmentApiController::class, 'index'])->name('api.v1.appointments.index');
    Route::post('/appointments', [AppointmentApiController::class, 'store'])->name('api.v1.appointments.store');
    Route::get('/appointments/{id}', [AppointmentApiController::class, 'show'])->name('api.v1.appointments.show');
    Route::put('/appointments/{id}/reschedule', [AppointmentApiController::class, 'reschedule'])->name('api.v1.appointments.reschedule');
    Route::put('/appointments/{id}/cancel', [AppointmentApiController::class, 'cancel'])->name('api.v1.appointments.cancel');
});
