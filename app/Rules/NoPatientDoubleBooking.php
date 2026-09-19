<?php

namespace App\Rules;

use App\Services\AppointmentService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NoPatientDoubleBooking implements ValidationRule
{
    public function __construct(
        protected string $date,
        protected string $startTime,
        protected string $endTime,
        protected ?int $excludeAppointmentId = null
    ) {}

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $appointmentService = app(AppointmentService::class);

        if (!$appointmentService->isPatientSlotAvailable((int) $value, $this->date, $this->startTime, $this->endTime, $this->excludeAppointmentId)) {
            $fail('El paciente seleccionado ya cuenta con otra cita médica agendada en ese mismo intervalo de horario.');
        }
    }
}
