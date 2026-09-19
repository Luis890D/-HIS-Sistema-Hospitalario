<?php

namespace App\Enums;

enum AppointmentStatus: string
{
    case PENDING = 'pending';           // Cita pendiente de confirmación
    case CONFIRMED = 'confirmed';       // Cita confirmada
    case RESCHEDULED = 'rescheduled';   // Cita reprogramada a una nueva fecha
    case CANCELLED = 'cancelled';       // Cita cancelada
    case ATTENDED = 'attended';         // Paciente atendido
    case NO_SHOW = 'no_show';           // Paciente no se presentó

    /**
     * Retorna una etiqueta legible en español para la interfaz
     */
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pendiente',
            self::CONFIRMED => 'Confirmada',
            self::RESCHEDULED => 'Reprogramada',
            self::CANCELLED => 'Cancelada',
            self::ATTENDED => 'Atendida',
            self::NO_SHOW => 'No Asistió',
        };
    }

    /**
     * Clase CSS de color de badge para la UI
     */
    public function badgeColor(): string
    {
        return match($this) {
            self::PENDING => 'bg-amber-100 text-amber-800 border-amber-200',
            self::CONFIRMED => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            self::RESCHEDULED => 'bg-blue-100 text-blue-800 border-blue-200',
            self::CANCELLED => 'bg-rose-100 text-rose-800 border-rose-200',
            self::ATTENDED => 'bg-purple-100 text-purple-800 border-purple-200',
            self::NO_SHOW => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }

    /**
     * Define los estados válidos hacia los que se puede transicionar desde el estado actual
     * @return AppointmentStatus[]
     */
    public function allowedTransitions(): array
    {
        return match($this) {
            self::PENDING => [
                self::CONFIRMED,
                self::CANCELLED,
                self::RESCHEDULED,
            ],
            self::CONFIRMED => [
                self::ATTENDED,
                self::CANCELLED,
                self::RESCHEDULED,
                self::NO_SHOW,
            ],
            self::RESCHEDULED => [
                self::CONFIRMED,
                self::ATTENDED,
                self::CANCELLED,
                self::NO_SHOW,
                self::RESCHEDULED,
            ],
            self::ATTENDED => [],   // Estado terminal
            self::CANCELLED => [],  // Estado terminal
            self::NO_SHOW => [],    // Estado terminal
        };
    }

    /**
     * Valida si la transición hacia el estado destino está permitida
     */
    public function canTransitionTo(self $target): bool
    {
        if ($this === $target) {
            return true; // Mantener el mismo estado se considera válido
        }

        return in_array($target, $this->allowedTransitions(), true);
    }

    /**
     * Indica si el estado es final/terminal en el ciclo clínico
     */
    public function isTerminal(): bool
    {
        return empty($this->allowedTransitions());
    }
}

