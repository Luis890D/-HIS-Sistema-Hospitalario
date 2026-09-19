<?php

namespace App\Models;

use App\Enums\AppointmentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;

class Appointment extends Model
{
    protected $table = 'appointments';

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'specialty_id',
        'appointment_code',          // Código único de cita (ej: CITA-202609-001)
        'appointment_date',          // Fecha de la cita (YYYY-MM-DD)
        'start_time',                // Hora inicio (HH:MM:SS)
        'end_time',                  // Hora fin estimada (HH:MM:SS)
        'status',                    // Enum AppointmentStatus
        'reason',                    // Motivo de consulta
        'clinical_notes',            // Notas médicas
        'cancellation_reason',       // Motivo si es cancelada
        'cancelled_at',              // Timestamp de cancelación
        'rescheduled_from_id',       // Cita previa si proviene de reprogramación
        'reschedule_reason',         // Motivo del cambio de fecha
        'rescheduled_at',            // Timestamp de reprogramación
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'status' => AppointmentStatus::class,
        'cancelled_at' => 'datetime',
        'rescheduled_at' => 'datetime',
    ];

    // Relaciones
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(MedicalSpecialty::class, 'specialty_id');
    }

    public function rescheduledFrom(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, 'rescheduled_from_id');
    }

    public function rescheduledTo(): HasOne
    {
        return $this->hasOne(Appointment::class, 'rescheduled_from_id');
    }

    // Scopes de conveniencia
    public function scopeForDoctor(Builder $query, int $doctorId): Builder
    {
        return $query->where('doctor_id', $doctorId);
    }

    public function scopeForDate(Builder $query, string $date): Builder
    {
        return $query->whereDate('appointment_date', $date);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [AppointmentStatus::PENDING, AppointmentStatus::CONFIRMED]);
    }
}
