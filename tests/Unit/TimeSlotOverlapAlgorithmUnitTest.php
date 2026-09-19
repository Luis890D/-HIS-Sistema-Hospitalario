<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class TimeSlotOverlapAlgorithmUnitTest extends TestCase
{
    /**
     * Evalúa si dos intervalos de horario [startA, endA] y [startB, endB] entran en conflicto.
     * Teorema: Conflicto <=> (startA < endB) AND (endA > startB)
     */
    private function hasOverlap(string $startA, string $endA, string $startB, string $endB): bool
    {
        return ($startA < $endB) && ($endA > $startB);
    }

    /**
     * Prueba Individual: Conflicto cuando los horarios son exactamente idénticos
     */
    public function test_exact_same_slot_causes_overlap(): void
    {
        // Dos citas de 09:00 a 09:30
        $this->assertTrue($this->hasOverlap('09:00:00', '09:30:00', '09:00:00', '09:30:00'));
    }

    /**
     * Prueba Individual: Conflicto por traslape superior (inicia antes de que termine la previa)
     */
    public function test_slot_overlapping_end_boundary_causes_conflict(): void
    {
        // Cita A: 10:00 a 10:30, Solicitud B: 10:15 a 10:45
        $this->assertTrue($this->hasOverlap('10:00:00', '10:30:00', '10:15:00', '10:45:00'));
    }

    /**
     * Prueba Individual: Conflicto por traslape inferior (inicia antes y termina dentro)
     */
    public function test_slot_overlapping_start_boundary_causes_conflict(): void
    {
        // Cita A: 10:00 a 10:30, Solicitud B: 09:45 a 10:15
        $this->assertTrue($this->hasOverlap('10:00:00', '10:30:00', '09:45:00', '10:15:00'));
    }

    /**
     * Prueba Individual: Conflicto cuando un intervalo engloba completamente al otro
     */
    public function test_enclosing_slot_causes_conflict(): void
    {
        // Cita corta: 11:00 a 11:30, Solicitud larga: 10:30 a 12:00
        $this->assertTrue($this->hasOverlap('11:00:00', '11:30:00', '10:30:00', '12:00:00'));
    }

    /**
     * Prueba Individual: Citas consecutivas adyacentes NO deben causar conflicto
     * Si una cita finaliza a las 09:30, la siguiente puede empezar exactamente a las 09:30.
     */
    public function test_adjacent_consecutive_slots_do_not_conflict(): void
    {
        // Cita A termina a las 09:30, Cita B inicia a las 09:30
        $this->assertFalse($this->hasOverlap('09:00:00', '09:30:00', '09:30:00', '10:00:00'));

        // Cita B termina a las 09:00, Cita A inicia a las 09:00
        $this->assertFalse($this->hasOverlap('09:00:00', '09:30:00', '08:30:00', '09:00:00'));
    }

    /**
     * Prueba Individual: Intervalos temporales distantes no tienen conflicto
     */
    public function test_distant_time_slots_do_not_conflict(): void
    {
        // Cita A de mañana (08:00 a 08:30) vs Cita B de tarde (16:00 a 16:30)
        $this->assertFalse($this->hasOverlap('08:00:00', '08:30:00', '16:00:00', '16:30:00'));
    }

    /**
     * Prueba Individual: Cálculo de duraciones clínicas estándar (15m, 30m, 45m, 60m)
     */
    public function test_standard_clinical_duration_calculations(): void
    {
        $start = strtotime('2026-09-25 09:00:00');

        $durations = [15, 30, 45, 60];
        $expectedEnds = ['09:15', '09:30', '09:45', '10:00'];

        foreach ($durations as $idx => $mins) {
            $end = $start + ($mins * 60);
            $this->assertEquals($expectedEnds[$idx], date('H:i', $end));
        }
    }
}
