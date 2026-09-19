<?php

namespace Tests\Unit;

use App\Enums\AppointmentStatus;
use PHPUnit\Framework\TestCase;

class AppointmentConflictAndStatusTest extends TestCase
{
    /**
     * Prueba: Transiciones válidas desde el estado PENDIENTE
     */
    public function test_pending_appointment_allowed_transitions(): void
    {
        $status = AppointmentStatus::PENDING;

        $this->assertTrue($status->canTransitionTo(AppointmentStatus::CONFIRMED));
        $this->assertTrue($status->canTransitionTo(AppointmentStatus::CANCELLED));
        $this->assertTrue($status->canTransitionTo(AppointmentStatus::RESCHEDULED));

        // No se puede pasar de pendiente directamente a atendida sin ser confirmada
        $this->assertFalse($status->canTransitionTo(AppointmentStatus::ATTENDED));
        $this->assertFalse($status->canTransitionTo(AppointmentStatus::NO_SHOW));
    }

    /**
     * Prueba: Transiciones válidas desde el estado CONFIRMADA
     */
    public function test_confirmed_appointment_allowed_transitions(): void
    {
        $status = AppointmentStatus::CONFIRMED;

        $this->assertTrue($status->canTransitionTo(AppointmentStatus::ATTENDED));
        $this->assertTrue($status->canTransitionTo(AppointmentStatus::CANCELLED));
        $this->assertTrue($status->canTransitionTo(AppointmentStatus::RESCHEDULED));
        $this->assertTrue($status->canTransitionTo(AppointmentStatus::NO_SHOW));

        // No puede regresar a pendiente
        $this->assertFalse($status->canTransitionTo(AppointmentStatus::PENDING));
    }

    /**
     * Prueba: Estados terminales no permiten transicionar a ningún otro estado
     */
    public function test_terminal_states_cannot_transition(): void
    {
        // Cancelada es terminal
        $cancelled = AppointmentStatus::CANCELLED;
        $this->assertTrue($cancelled->isTerminal());
        $this->assertFalse($cancelled->canTransitionTo(AppointmentStatus::CONFIRMED));
        $this->assertFalse($cancelled->canTransitionTo(AppointmentStatus::ATTENDED));

        // Atendida es terminal
        $attended = AppointmentStatus::ATTENDED;
        $this->assertTrue($attended->isTerminal());
        $this->assertFalse($attended->canTransitionTo(AppointmentStatus::CANCELLED));
        $this->assertFalse($attended->canTransitionTo(AppointmentStatus::PENDING));

        // No Asistió es terminal
        $noShow = AppointmentStatus::NO_SHOW;
        $this->assertTrue($noShow->isTerminal());
        $this->assertFalse($noShow->canTransitionTo(AppointmentStatus::CONFIRMED));
    }

    /**
     * Prueba: Algoritmo de detección de traslapes en intervalos de horario
     * Dos citas [A_start, A_end] y [B_start, B_end] se traslapan si:
     * A_start < B_end AND A_end > B_start
     */
    public function test_time_slot_overlap_detection_logic(): void
    {
        // Intervalo base: 09:00 a 09:30
        $baseStart = '09:00:00';
        $baseEnd   = '09:30:00';

        // Caso 1: Mismo horario exacto (09:00 a 09:30) -> TRASLAPE
        $this->assertTrue($this->checkOverlap($baseStart, $baseEnd, '09:00:00', '09:30:00'));

        // Caso 2: Empieza durante el intervalo (09:15 a 09:45) -> TRASLAPE
        $this->assertTrue($this->checkOverlap($baseStart, $baseEnd, '09:15:00', '09:45:00'));

        // Caso 3: Empieza antes y termina adentro (08:45 a 09:15) -> TRASLAPE
        $this->assertTrue($this->checkOverlap($baseStart, $baseEnd, '08:45:00', '09:15:00'));

        // Caso 4: Contiene completamente el intervalo (08:30 a 10:00) -> TRASLAPE
        $this->assertTrue($this->checkOverlap($baseStart, $baseEnd, '08:30:00', '10:00:00'));

        // Caso 5: Justo termina cuando empieza la otra (08:30 a 09:00) -> NO TRASLAPE (Válido)
        $this->assertFalse($this->checkOverlap($baseStart, $baseEnd, '08:30:00', '09:00:00'));

        // Caso 6: Justo empieza cuando termina la otra (09:30 a 10:00) -> NO TRASLAPE (Válido)
        $this->assertFalse($this->checkOverlap($baseStart, $baseEnd, '09:30:00', '10:00:00'));

        // Caso 7: Totalmente antes (08:00 a 08:30) -> NO TRASLAPE (Válido)
        $this->assertFalse($this->checkOverlap($baseStart, $baseEnd, '08:00:00', '08:30:00'));

        // Caso 8: Totalmente después (10:00 a 10:30) -> NO TRASLAPE (Válido)
        $this->assertFalse($this->checkOverlap($baseStart, $baseEnd, '10:00:00', '10:30:00'));
    }

    private function checkOverlap(string $startA, string $endA, string $startB, string $endB): bool
    {
        return ($startA < $endB) && ($endA > $startB);
    }
}
