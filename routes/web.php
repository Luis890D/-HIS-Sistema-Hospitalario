<?php

use App\Http\Controllers\Web\AppointmentWebController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - HIS Sistema Hospitalario
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('appointments.index');
});

// Módulo MVC de Citas Médicas
Route::prefix('appointments')->name('appointments.')->group(function () {
    Route::get('/', [AppointmentWebController::class, 'index'])->name('index');
    Route::get('/create', [AppointmentWebController::class, 'create'])->name('create');
    Route::post('/', [AppointmentWebController::class, 'store'])->name('store');
    Route::get('/{appointment}', [AppointmentWebController::class, 'show'])->name('show');
    Route::get('/{appointment}/reschedule', [AppointmentWebController::class, 'rescheduleForm'])->name('reschedule.form');
    Route::put('/{appointment}/reschedule', [AppointmentWebController::class, 'reschedule'])->name('reschedule');
    Route::put('/{appointment}/cancel', [AppointmentWebController::class, 'cancel'])->name('cancel');
});
