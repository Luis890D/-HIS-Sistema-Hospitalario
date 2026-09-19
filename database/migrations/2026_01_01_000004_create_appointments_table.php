<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->string('appointment_code', 30)->unique();
            
            // Relaciones principales
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('specialty_id')->constrained('medical_specialties')->cascadeOnUpdate()->restrictOnDelete();
            
            // Fechas y horarios
            $table->date('appointment_date');
            $table->time('start_time');
            $table->time('end_time');
            
            // Estado del ciclo de vida
            $table->string('status', 30)->default('pending'); // pending, confirmed, rescheduled, cancelled, attended, no_show
            
            // Justificaciones y notas
            $table->text('reason'); // Motivo de la cita
            $table->text('clinical_notes')->nullable(); // Notas previas o de triaje
            
            // Trazabilidad de Cancelación
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            
            // Trazabilidad de Reprogramación
            $table->foreignId('rescheduled_from_id')->nullable()->constrained('appointments')->nullOnDelete();
            $table->text('reschedule_reason')->nullable();
            $table->timestamp('rescheduled_at')->nullable();
            
            $table->timestamps();

            // Índices para optimizar consultas de disponibilidad y reportes
            $table->index(['doctor_id', 'appointment_date', 'start_time'], 'idx_doctor_schedule');
            $table->index(['patient_id', 'appointment_date'], 'idx_patient_schedule');
            $table->index('status', 'idx_appointment_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
