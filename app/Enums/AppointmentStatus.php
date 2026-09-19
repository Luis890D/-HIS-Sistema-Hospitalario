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
}
