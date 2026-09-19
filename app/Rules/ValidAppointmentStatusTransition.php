<?php

namespace App\Rules;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidAppointmentStatusTransition implements ValidationRule
{
    public function __construct(
        protected Appointment $appointment
    ) {}

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $targetStatus = AppointmentStatus::tryFrom($value);

        if (!$targetStatus) {
            $fail("El estado '{$value}' no es un estado válido para una cita médica.");
            return;
        }

        if (!$this->appointment->status->canTransitionTo($targetStatus)) {
            $allowed = array_map(fn($st) => $st->label(), $this->appointment->status->allowedTransitions());
            $allowedList = !empty($allowed) ? implode(', ', $allowed) : 'Ninguno (estado terminal)';

            $fail("No se puede cambiar el estado de '{$this->appointment->status->label()}' a '{$targetStatus->label()}'. Estados permitidos: [{$allowedList}].");
        }
    }
}
