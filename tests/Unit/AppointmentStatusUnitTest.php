<?php

namespace Tests\Unit;

use App\Enums\AppointmentStatus;
use PHPUnit\Framework\TestCase;

class AppointmentStatusUnitTest extends TestCase
{
    /**
     * Prueba Unitaria Individual: Verificación de etiquetas legibles en español (label)
     */
    public function test_each_status_has_correct_spanish_label(): void
    {
        $this->assertEquals('Pendiente', AppointmentStatus::PENDING->label());
        $this->assertEquals('Confirmada', AppointmentStatus::CONFIRMED->label());
        $this->assertEquals('Reprogramada', AppointmentStatus::RESCHEDULED->label());
        $this->assertEquals('Cancelada', AppointmentStatus::CANCELLED->label());
        $this->assertEquals('Atendida', AppointmentStatus::ATTENDED->label());
        $this->assertEquals('No Asistió', AppointmentStatus::NO_SHOW->label());
    }

    /**
     * Prueba Unitaria Individual: Verificación de clases CSS cromáticas para badges
     */
    public function test_each_status_has_defined_badge_color_class(): void
    {
        $this->assertStringContainsString('amber', AppointmentStatus::PENDING->badgeColor());
        $this->assertStringContainsString('emerald', AppointmentStatus::CONFIRMED->badgeColor());
        $this->assertStringContainsString('blue', AppointmentStatus::RESCHEDULED->badgeColor());
        $this->assertStringContainsString('rose', AppointmentStatus::CANCELLED->badgeColor());
        $this->assertStringContainsString('purple', AppointmentStatus::ATTENDED->badgeColor());
        $this->assertStringContainsString('gray', AppointmentStatus::NO_SHOW->badgeColor());
    }

    /**
     * Prueba Unitaria Individual: Estados terminales inmutables del ciclo clínico
     */
    public function test_terminal_states_are_properly_identified(): void
    {
        // Estados terminales
        $this->assertTrue(AppointmentStatus::ATTENDED->isTerminal());
        $this->assertTrue(AppointmentStatus::CANCELLED->isTerminal());
        $this->assertTrue(AppointmentStatus::NO_SHOW->isTerminal());

        // Estados activos (no terminales)
        $this->assertFalse(AppointmentStatus::PENDING->isTerminal());
        $this->assertFalse(AppointmentStatus::CONFIRMED->isTerminal());
        $this->assertFalse(AppointmentStatus::RESCHEDULED->isTerminal());
    }

    /**
     * Prueba Unitaria Individual: Permanencia en el mismo estado siempre es válida
     */
    public function test_transition_to_self_is_always_permitted(): void
    {
        foreach (AppointmentStatus::cases() as $status) {
            $this->assertTrue($status->canTransitionTo($status));
        }
    }

    /**
     * Prueba Unitaria Individual: Transiciones válidas e inválidas para PENDING
     */
    public function test_pending_status_transition_rules(): void
    {
        $pending = AppointmentStatus::PENDING;

        // Válidas
        $this->assertTrue($pending->canTransitionTo(AppointmentStatus::CONFIRMED));
        $this->assertTrue($pending->canTransitionTo(AppointmentStatus::CANCELLED));
        $this->assertTrue($pending->canTransitionTo(AppointmentStatus::RESCHEDULED));

        // Inválidas directas
        $this->assertFalse($pending->canTransitionTo(AppointmentStatus::ATTENDED));
        $this->assertFalse($pending->canTransitionTo(AppointmentStatus::NO_SHOW));
    }

    /**
     * Prueba Unitaria Individual: Transiciones válidas e inválidas para CONFIRMED
     */
    public function test_confirmed_status_transition_rules(): void
    {
        $confirmed = AppointmentStatus::CONFIRMED;

        // Válidas
        $this->assertTrue($confirmed->canTransitionTo(AppointmentStatus::ATTENDED));
        $this->assertTrue($confirmed->canTransitionTo(AppointmentStatus::CANCELLED));
        $this->assertTrue($confirmed->canTransitionTo(AppointmentStatus::RESCHEDULED));
        $this->assertTrue($confirmed->canTransitionTo(AppointmentStatus::NO_SHOW));

        // Inválidas
        $this->assertFalse($confirmed->canTransitionTo(AppointmentStatus::PENDING));
    }

    /**
     * Prueba Unitaria Individual: Estados terminales no pueden transicionar a ningún otro estado
     */
    public function test_terminal_states_cannot_transition_to_any_other_state(): void
    {
        $terminals = [
            AppointmentStatus::CANCELLED,
            AppointmentStatus::ATTENDED,
            AppointmentStatus::NO_SHOW,
        ];

        foreach ($terminals as $terminal) {
            $this->assertEmpty($terminal->allowedTransitions());

            foreach (AppointmentStatus::cases() as $target) {
                if ($target === $terminal) {
                    continue;
                }
                $this->assertFalse(
                    $terminal->canTransitionTo($target),
                    "Estado terminal {$terminal->value} no debe poder transicionar a {$target->value}"
                );
            }
        }
    }
}
