<?php

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AppointmentService
{
    /**
     * Agendar una nueva cita médica
     * Valida disponibilidad de médico y genera código único
     */
    public function scheduleAppointment(array $data): Appointment
    {
        $this->ensureDoctorIsAvailable(
            $data['doctor_id'],
            $data['appointment_date'],
            $data['start_time'],
            $data['end_time']
        );

        return DB::transaction(function () use ($data) {
            $data['appointment_code'] = $this->generateAppointmentCode();
            $data['status'] = AppointmentStatus::PENDING;

            return Appointment::create($data);
        });
    }

    /**
     * Reprogramar una cita médica existente
     * Actualiza la fecha/hora, registra el motivo y cambia el estado a RESCHEDULED o crea trazabilidad
     */
    public function rescheduleAppointment(Appointment $appointment, array $data): Appointment
    {
        if (in_array($appointment->status, [AppointmentStatus::CANCELLED, AppointmentStatus::ATTENDED])) {
            throw ValidationException::withMessages([
                'status' => ["No se puede reprogramar una cita con estado: {$appointment->status->label()}"],
            ]);
        }

        $doctorId = $data['doctor_id'] ?? $appointment->doctor_id;
        $newDate = $data['appointment_date'];
        $newStartTime = $data['start_time'];
        $newEndTime = $data['end_time'];

        $this->ensureDoctorIsAvailable($doctorId, $newDate, $newStartTime, $newEndTime, $appointment->id);

        return DB::transaction(function () use ($appointment, $data, $newDate, $newStartTime, $newEndTime, $doctorId) {
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

            return $appointment->fresh(['doctor', 'patient', 'specialty']);
        });
    }

    /**
     * Cancelar una cita médica
     * Registra el motivo y libera el cupo
     */
    public function cancelAppointment(Appointment $appointment, string $reason): Appointment
    {
        if ($appointment->status === AppointmentStatus::CANCELLED) {
            throw ValidationException::withMessages([
                'status' => ['La cita médica ya se encuentra cancelada.'],
            ]);
        }

        if ($appointment->status === AppointmentStatus::ATTENDED) {
            throw ValidationException::withMessages([
                'status' => ['No es posible cancelar una cita médica que ya ha sido atendida.'],
            ]);
        }

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
     * Si cambia de fecha u horario, valida que no colisione con otra cita
     */
    public function updateAppointment(Appointment $appointment, array $data): Appointment
    {
        $doctorId = $data['doctor_id'] ?? $appointment->doctor_id;
        $date = $data['appointment_date'] ?? $appointment->appointment_date->format('Y-m-d');
        $startTime = $data['start_time'] ?? $appointment->start_time;
        $endTime = $data['end_time'] ?? $appointment->end_time;

        if (isset($data['appointment_date']) || isset($data['start_time']) || isset($data['end_time']) || isset($data['doctor_id'])) {
            $this->ensureDoctorIsAvailable($doctorId, $date, $startTime, $endTime, $appointment->id);
        }

        $appointment->update($data);

        return $appointment->fresh(['doctor.specialty', 'patient', 'specialty']);
    }

    /**
     * Cambiar el estado de una cita médica (confirmed, attended, no_show, cancelled, pending)
     */
    public function changeStatus(Appointment $appointment, string $status, ?string $note = null, ?string $cancellationReason = null): Appointment
    {
        $newStatus = AppointmentStatus::from($status);

        return DB::transaction(function () use ($appointment, $newStatus, $note, $cancellationReason) {
            $updateData = ['status' => $newStatus];

            if ($note) {
                $updateData['clinical_notes'] = ($appointment->clinical_notes ? $appointment->clinical_notes . "\n" : '') . "[Estado a {$newStatus->label()}]: " . $note;
            }

            if ($newStatus === AppointmentStatus::CANCELLED) {
                $updateData['cancellation_reason'] = $cancellationReason ?? 'Cancelada mediante cambio de estado API';
                $updateData['cancelled_at'] = now();
            }

            $appointment->update($updateData);

            return $appointment->fresh(['doctor.specialty', 'patient', 'specialty']);
        });
    }

    /**
     * Comprueba si el médico tiene disponible el intervalo de tiempo especificado
     */
    public function isSlotAvailable(
        int $doctorId,
        string $date,
        string $startTime,
        string $endTime,
        ?int $excludeAppointmentId = null
    ): bool {
        $query = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->whereNotIn('status', [AppointmentStatus::CANCELLED])
            ->where(function ($q) use ($startTime, $endTime) {
                // Hay traslape si la cita existente empieza antes de que termine la nueva y termina después de que empiece la nueva
                $q->where('start_time', '<', $endTime)
                  ->where('end_time', '>', $startTime);
            });

        if ($excludeAppointmentId) {
            $query->where('id', '!=', $excludeAppointmentId);
        }

        return !$query->exists();
    }

    /**
     * Lanza excepción si el médico no se encuentra disponible
     */
    protected function ensureDoctorIsAvailable(
        int $doctorId,
        string $date,
        string $startTime,
        string $endTime,
        ?int $excludeAppointmentId = null
    ): void {
        if (!$this->isSlotAvailable($doctorId, $date, $startTime, $endTime, $excludeAppointmentId)) {
            throw ValidationException::withMessages([
                'start_time' => ['El médico seleccionado ya tiene una cita agendada en ese intervalo de horario.'],
            ]);
        }
    }

    /**
     * Genera un código legible único para el paciente
     */
    protected function generateAppointmentCode(): string
    {
        $prefix = 'CITA-' . date('Ym') . '-';
        $random = strtoupper(substr(uniqid(), -5));
        return $prefix . $random;
    }
}
