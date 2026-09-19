<?php

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Exceptions\AppointmentScheduleConflictException;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AppointmentService
{
    /**
     * Agendar una nueva cita médica
     * Valida disponibilidad de médico y paciente en concurrencia (evita doble reserva)
     */
    public function scheduleAppointment(array $data): Appointment
    {
        return DB::transaction(function () use ($data) {
            // Validar que el médico esté disponible (bloqueando registros coincidentes para evitar race conditions)
            $this->ensureDoctorIsAvailable(
                $data['doctor_id'],
                $data['appointment_date'],
                $data['start_time'],
                $data['end_time']
            );

            // Validar que el paciente no tenga otra cita al mismo tiempo
            $this->ensurePatientIsAvailable(
                $data['patient_id'],
                $data['appointment_date'],
                $data['start_time'],
                $data['end_time']
            );

            $data['appointment_code'] = $this->generateAppointmentCode();
            $data['status'] = AppointmentStatus::PENDING;

            return Appointment::create($data);
        });
    }

    /**
     * Reprogramar una cita médica existente
     * Valida transiciones de estado permitidas y disponibilidad de nuevo horario
     */
    public function rescheduleAppointment(Appointment $appointment, array $data): Appointment
    {
        $this->ensureValidTransition($appointment, AppointmentStatus::RESCHEDULED);

        $doctorId = $data['doctor_id'] ?? $appointment->doctor_id;
        $patientId = $appointment->patient_id;
        $newDate = $data['appointment_date'];
        $newStartTime = $data['start_time'];
        $newEndTime = $data['end_time'];

        return DB::transaction(function () use ($appointment, $data, $doctorId, $patientId, $newDate, $newStartTime, $newEndTime) {
            // Verificar disponibilidad excluyendo la cita actual
            $this->ensureDoctorIsAvailable($doctorId, $newDate, $newStartTime, $newEndTime, $appointment->id);
            $this->ensurePatientIsAvailable($patientId, $newDate, $newStartTime, $newEndTime, $appointment->id);

            $appointment->update([
                'doctor_id' => $doctorId,
                'specialty_id' => $data['specialty_id'] ?? $appointment->specialty_id,
                'appointment_date' => $newDate,
                'start_time' => $newStartTime,
                'end_time' => $newEndTime,
                'status' => AppointmentStatus::RESCHEDULED,
                'reschedule_reason' => $data['reschedule_reason'] ?? 'Reprogramación solicitada',
                'rescheduled_at' => now(),
            ]);

            return $appointment->fresh(['doctor.specialty', 'patient', 'specialty']);
        });
    }

    /**
     * Cancelar una cita médica
     * Valida transición de estado, registra motivo obligatorio y libera el horario
     */
    public function cancelAppointment(Appointment $appointment, string $reason): Appointment
    {
        $this->ensureValidTransition($appointment, AppointmentStatus::CANCELLED);

        return DB::transaction(function () use ($appointment, $reason) {
            $appointment->update([
                'status' => AppointmentStatus::CANCELLED,
                'cancellation_reason' => $reason,
                'cancelled_at' => now(),
            ]);

            return $appointment->fresh();
        });
    }

    /**
     * Actualizar datos generales de una cita médica
     * Si cambian fechas u horarios, valida que no haya conflicto de doble reserva
     */
    public function updateAppointment(Appointment $appointment, array $data): Appointment
    {
        $doctorId = $data['doctor_id'] ?? $appointment->doctor_id;
        $patientId = $appointment->patient_id;
        $date = $data['appointment_date'] ?? $appointment->appointment_date->format('Y-m-d');
        $startTime = $data['start_time'] ?? $appointment->start_time;
        $endTime = $data['end_time'] ?? $appointment->end_time;

        $hasScheduleChange = isset($data['appointment_date']) 
            || isset($data['start_time']) 
            || isset($data['end_time']) 
            || isset($data['doctor_id']);

        return DB::transaction(function () use ($appointment, $data, $hasScheduleChange, $doctorId, $patientId, $date, $startTime, $endTime) {
            if ($hasScheduleChange) {
                $this->ensureDoctorIsAvailable($doctorId, $date, $startTime, $endTime, $appointment->id);
                $this->ensurePatientIsAvailable($patientId, $date, $startTime, $endTime, $appointment->id);
            }

            $appointment->update($data);

            return $appointment->fresh(['doctor.specialty', 'patient', 'specialty']);
        });
    }

    /**
     * Cambiar el estado de una cita médica
     * Aplica la máquina de estados estricta (pendiente -> confirmada -> atendida / cancelada / no_show)
     */
    public function changeStatus(
        Appointment $appointment,
        string $status,
        ?string $note = null,
        ?string $cancellationReason = null
    ): Appointment {
        $newStatus = AppointmentStatus::from($status);

        // Validar si la transición entre el estado actual y el nuevo está permitida
        $this->ensureValidTransition($appointment, $newStatus);

        return DB::transaction(function () use ($appointment, $newStatus, $note, $cancellationReason) {
            $updateData = ['status' => $newStatus];

            if ($note) {
                $updateData['clinical_notes'] = ($appointment->clinical_notes ? $appointment->clinical_notes . "\n" : '') 
                    . "[Transición a {$newStatus->label()} - " . now()->format('d/m/Y H:i') . "]: " . $note;
            }

            if ($newStatus === AppointmentStatus::CANCELLED) {
                $updateData['cancellation_reason'] = $cancellationReason ?? 'Cancelada mediante cambio de estado';
                $updateData['cancelled_at'] = now();
            }

            $appointment->update($updateData);

            return $appointment->fresh(['doctor.specialty', 'patient', 'specialty']);
        });
    }

    /**
     * Valida si el médico tiene disponible el intervalo de tiempo especificado
     */
    public function isDoctorSlotAvailable(
        int $doctorId,
        string $date,
        string $startTime,
        string $endTime,
        ?int $excludeAppointmentId = null
    ): bool {
        return $this->getConflictingDoctorAppointment($doctorId, $date, $startTime, $endTime, $excludeAppointmentId) === null;
    }

    /**
     * Valida si el paciente tiene disponible el intervalo de tiempo especificado
     */
    public function isPatientSlotAvailable(
        int $patientId,
        string $date,
        string $startTime,
        string $endTime,
        ?int $excludeAppointmentId = null
    ): bool {
        return $this->getConflictingPatientAppointment($patientId, $date, $startTime, $endTime, $excludeAppointmentId) === null;
    }

    /**
     * Busca cita en conflicto para el médico aplicando traslape temporal
     */
    public function getConflictingDoctorAppointment(
        int $doctorId,
        string $date,
        string $startTime,
        string $endTime,
        ?int $excludeAppointmentId = null
    ): ?Appointment {
        $query = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->whereNotIn('status', [AppointmentStatus::CANCELLED])
            ->where(function ($q) use ($startTime, $endTime) {
                // Algoritmo de traslape: inicio_existente < fin_solicitado Y fin_existente > inicio_solicitado
                $q->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $startTime);
            });

        if ($excludeAppointmentId) {
            $query->where('id', '!=', $excludeAppointmentId);
        }

        // Usar lockForUpdate dentro de transacciones para evitar condiciones de carrera (doble reserva simultánea)
        if (DB::transactionLevel() > 0) {
            $query->lockForUpdate();
        }

        return $query->first();
    }

    /**
     * Busca cita en conflicto para el paciente aplicando traslape temporal
     */
    public function getConflictingPatientAppointment(
        int $patientId,
        string $date,
        string $startTime,
        string $endTime,
        ?int $excludeAppointmentId = null
    ): ?Appointment {
        $query = Appointment::where('patient_id', $patientId)
            ->whereDate('appointment_date', $date)
            ->whereNotIn('status', [AppointmentStatus::CANCELLED])
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $startTime);
            });

        if ($excludeAppointmentId) {
            $query->where('id', '!=', $excludeAppointmentId);
        }

        if (DB::transactionLevel() > 0) {
            $query->lockForUpdate();
        }

        return $query->first();
    }

    /**
     * Lanza excepción informativa de validación si el médico no está disponible
     */
    protected function ensureDoctorIsAvailable(
        int $doctorId,
        string $date,
        string $startTime,
        string $endTime,
        ?int $excludeAppointmentId = null
    ): void {
        $conflict = $this->getConflictingDoctorAppointment($doctorId, $date, $startTime, $endTime, $excludeAppointmentId);

        if ($conflict) {
            $conflictStart = substr($conflict->start_time, 0, 5);
            $conflictEnd = substr($conflict->end_time, 0, 5);

            throw new AppointmentScheduleConflictException(
                "Conflicto de horario: El médico ya tiene la cita '{$conflict->appointment_code}' agendada de {$conflictStart} a {$conflictEnd} en esa misma fecha.",
                'doctor_id'
            );
        }
    }

    /**
     * Lanza excepción si el paciente ya tiene otra cita en el mismo bloque de horario
     */
    protected function ensurePatientIsAvailable(
        int $patientId,
        string $date,
        string $startTime,
        string $endTime,
        ?int $excludeAppointmentId = null
    ): void {
        $conflict = $this->getConflictingPatientAppointment($patientId, $date, $startTime, $endTime, $excludeAppointmentId);

        if ($conflict) {
            $conflictStart = substr($conflict->start_time, 0, 5);
            $conflictEnd = substr($conflict->end_time, 0, 5);

            throw new AppointmentScheduleConflictException(
                "Conflicto de horario: El paciente ya tiene otra cita médica ({$conflict->appointment_code}) programada de {$conflictStart} a {$conflictEnd} en esa misma fecha.",
                'patient_id'
            );
        }
    }

    /**
     * Valida que la transición entre estados cumpla las reglas clínicas hospitalarias
     */
    protected function ensureValidTransition(Appointment $appointment, AppointmentStatus $targetStatus): void
    {
        if (!$appointment->status->canTransitionTo($targetStatus)) {
            $allowed = array_map(fn($st) => "'{$st->label()}'", $appointment->status->allowedTransitions());
            $allowedText = !empty($allowed) ? implode(', ', $allowed) : 'Ninguno (estado final)';

            throw ValidationException::withMessages([
                'status' => [
                    "Transición no permitida: No se puede cambiar el estado de '{$appointment->status->label()}' a '{$targetStatus->label()}'. Transiciones permitidas desde este estado: [{$allowedText}]."
                ],
            ]);
        }
    }

    /**
     * Genera un código legible único para la cita médica
     */
    protected function generateAppointmentCode(): string
    {
        $prefix = 'CITA-' . date('Ym') . '-';
        $random = strtoupper(substr(uniqid(), -5));
        return $prefix . $random;
    }
}
