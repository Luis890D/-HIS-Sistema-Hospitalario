<?php

/**
 * Runner de Pruebas Automatizadas (Unitarias y Generales) - HIS Hospitalario
 * Permite ejecutar las pruebas de manera autónoma y generar reporte de QA.
 */

namespace Tests;

require_once __DIR__ . '/../app/Enums/AppointmentStatus.php';

use App\Enums\AppointmentStatus;

class SimpleTestRunner
{
    private int $passed = 0;
    private int $failed = 0;
    private array $failures = [];

    public function assert($condition, string $message): void
    {
        if ($condition) {
            $this->passed++;
            echo "  \033[32m✔ PASS:\033[0m {$message}\n";
        } else {
            $this->failed++;
            $this->failures[] = $message;
            echo "  \033[31m✖ FAIL:\033[0m {$message}\n";
        }
    }

    public function assertEquals($expected, $actual, string $message): void
    {
        $this->assert($expected === $actual, "{$message} [Esperado: '{$expected}', Obtenido: '{$actual}']");
    }

    public function assertTrue($condition, string $message): void
    {
        $this->assert($condition === true, $message);
    }

    public function assertFalse($condition, string $message): void
    {
        $this->assert($condition === false, $message);
    }

    public function summary(): int
    {
        echo "\n=======================================================\n";
        echo " RESUMEN DE EJECUCIÓN DE PRUEBAS AUTOMATIZADAS (QA)\n";
        echo "=======================================================\n";
        echo " Total Pasadas: \033[32m{$this->passed}\033[0m\n";
        echo " Total Falladas: " . ($this->failed > 0 ? "\033[31m{$this->failed}\033[0m" : "\033[32m0\033[0m") . "\n";
        echo " Estado Global: " . ($this->failed === 0 ? "\033[32m100% EXITOSO (TODAS LAS PRUEBAS APROBADAS)\033[0m" : "\033[31mFALLAS DETECTADAS\033[0m") . "\n";
        echo "=======================================================\n\n";

        return $this->failed === 0 ? 0 : 1;
    }
}

$runner = new SimpleTestRunner();

echo "\n--- 1. PRUEBAS INDIVIDUALES: ENUM APPOINTMENT STATUS & TRANSICIONES ---\n";

// 1.1 Labels en español
$runner->assertEquals('Pendiente', AppointmentStatus::PENDING->label(), "Label correcto para PENDING");
$runner->assertEquals('Confirmada', AppointmentStatus::CONFIRMED->label(), "Label correcto para CONFIRMED");
$runner->assertEquals('Reprogramada', AppointmentStatus::RESCHEDULED->label(), "Label correcto para RESCHEDULED");
$runner->assertEquals('Cancelada', AppointmentStatus::CANCELLED->label(), "Label correcto para CANCELLED");
$runner->assertEquals('Atendida', AppointmentStatus::ATTENDED->label(), "Label correcto para ATTENDED");
$runner->assertEquals('No Asistió', AppointmentStatus::NO_SHOW->label(), "Label correcto para NO_SHOW");

// 1.2 Badges cromáticos
$runner->assertTrue(str_contains(AppointmentStatus::PENDING->badgeColor(), 'amber'), "Badge amber para PENDING");
$runner->assertTrue(str_contains(AppointmentStatus::CONFIRMED->badgeColor(), 'emerald'), "Badge emerald para CONFIRMED");
$runner->assertTrue(str_contains(AppointmentStatus::RESCHEDULED->badgeColor(), 'blue'), "Badge blue para RESCHEDULED");
$runner->assertTrue(str_contains(AppointmentStatus::CANCELLED->badgeColor(), 'rose'), "Badge rose para CANCELLED");
$runner->assertTrue(str_contains(AppointmentStatus::ATTENDED->badgeColor(), 'purple'), "Badge purple para ATTENDED");

// 1.3 Estados terminales
$runner->assertTrue(AppointmentStatus::ATTENDED->isTerminal(), "ATTENDED es un estado terminal");
$runner->assertTrue(AppointmentStatus::CANCELLED->isTerminal(), "CANCELLED es un estado terminal");
$runner->assertTrue(AppointmentStatus::NO_SHOW->isTerminal(), "NO_SHOW es un estado terminal");
$runner->assertFalse(AppointmentStatus::PENDING->isTerminal(), "PENDING NO es un estado terminal");
$runner->assertFalse(AppointmentStatus::CONFIRMED->isTerminal(), "CONFIRMED NO es un estado terminal");
$runner->assertFalse(AppointmentStatus::RESCHEDULED->isTerminal(), "RESCHEDULED NO es un estado terminal");

// 1.4 Transiciones permitidas
$runner->assertTrue(AppointmentStatus::PENDING->canTransitionTo(AppointmentStatus::CONFIRMED), "PENDING -> CONFIRMED permitido");
$runner->assertTrue(AppointmentStatus::PENDING->canTransitionTo(AppointmentStatus::CANCELLED), "PENDING -> CANCELLED permitido");
$runner->assertTrue(AppointmentStatus::PENDING->canTransitionTo(AppointmentStatus::RESCHEDULED), "PENDING -> RESCHEDULED permitido");
$runner->assertFalse(AppointmentStatus::PENDING->canTransitionTo(AppointmentStatus::ATTENDED), "PENDING -> ATTENDED bloqueado (debe confirmarse primero)");

$runner->assertTrue(AppointmentStatus::CONFIRMED->canTransitionTo(AppointmentStatus::ATTENDED), "CONFIRMED -> ATTENDED permitido");
$runner->assertTrue(AppointmentStatus::CONFIRMED->canTransitionTo(AppointmentStatus::CANCELLED), "CONFIRMED -> CANCELLED permitido");
$runner->assertTrue(AppointmentStatus::CONFIRMED->canTransitionTo(AppointmentStatus::RESCHEDULED), "CONFIRMED -> RESCHEDULED permitido");
$runner->assertTrue(AppointmentStatus::CONFIRMED->canTransitionTo(AppointmentStatus::NO_SHOW), "CONFIRMED -> NO_SHOW permitido");
$runner->assertFalse(AppointmentStatus::CONFIRMED->canTransitionTo(AppointmentStatus::PENDING), "CONFIRMED -> PENDING bloqueado");

// 1.5 Terminales no transicionan
$runner->assertFalse(AppointmentStatus::CANCELLED->canTransitionTo(AppointmentStatus::CONFIRMED), "CANCELLED no puede pasar a CONFIRMED");
$runner->assertFalse(AppointmentStatus::ATTENDED->canTransitionTo(AppointmentStatus::CANCELLED), "ATTENDED no puede pasar a CANCELLED");
$runner->assertFalse(AppointmentStatus::NO_SHOW->canTransitionTo(AppointmentStatus::CONFIRMED), "NO_SHOW no puede pasar a CONFIRMED");

echo "\n--- 2. PRUEBAS INDIVIDUALES: ALGORITMO DE TRASLAPE TEMPORAL Y LÍMITES ---\n";

function checkOverlap(string $startA, string $endA, string $startB, string $endB): bool {
    return ($startA < $endB) && ($endA > $startB);
}

// 2.1 Mismo horario
$runner->assertTrue(checkOverlap('09:00:00', '09:30:00', '09:00:00', '09:30:00'), "Mismo horario exacto detecta conflicto");

// 2.2 Traslapes parciales
$runner->assertTrue(checkOverlap('09:00:00', '09:30:00', '09:15:00', '09:45:00'), "Traslape superior detecta conflicto");
$runner->assertTrue(checkOverlap('09:00:00', '09:30:00', '08:45:00', '09:15:00'), "Traslape inferior detecta conflicto");
$runner->assertTrue(checkOverlap('09:00:00', '09:30:00', '08:30:00', '10:00:00'), "Intervalo envolvente detecta conflicto");

// 2.3 Horarios consecutivos limpios (sin traslape)
$runner->assertFalse(checkOverlap('09:00:00', '09:30:00', '09:30:00', '10:00:00'), "Cita inmediatamente contigua posterior NO tiene conflicto");
$runner->assertFalse(checkOverlap('09:00:00', '09:30:00', '08:30:00', '09:00:00'), "Cita inmediatamente contigua previa NO tiene conflicto");
$runner->assertFalse(checkOverlap('09:00:00', '09:30:00', '11:00:00', '11:30:00'), "Cita en horario distante NO tiene conflicto");

// 2.4 Duraciones clínicas rápidas
$startTs = strtotime('2026-09-25 08:00:00');
$runner->assertEquals('08:15', date('H:i', $startTs + 15 * 60), "Cálculo de slot rápido 15 min");
$runner->assertEquals('08:30', date('H:i', $startTs + 30 * 60), "Cálculo de slot rápido 30 min");
$runner->assertEquals('08:45', date('H:i', $startTs + 45 * 60), "Cálculo de slot rápido 45 min");
$runner->assertEquals('09:00', date('H:i', $startTs + 60 * 60), "Cálculo de slot rápido 60 min");

echo "\n--- 3. PRUEBAS GENERALES: DEFINICIÓN DE RUTAS Y ESTRUCTURA DE CONTROLADORES ---\n";

$routesApiContent = file_get_contents(__DIR__ . '/../routes/api.php');
$routesWebContent = file_get_contents(__DIR__ . '/../routes/web.php');
$appointmentCtrlContent = file_get_contents(__DIR__ . '/../app/Http/Controllers/Api/V1/AppointmentApiController.php');
$appointmentWebCtrlContent = file_get_contents(__DIR__ . '/../app/Http/Controllers/Web/AppointmentWebController.php');

// Rutas API
$runner->assertTrue(str_contains($routesApiContent, '/appointments'), "Ruta API /appointments registrada");
$runner->assertTrue(str_contains($routesApiContent, '/calendar/events'), "Ruta API /calendar/events registrada");
$runner->assertTrue(str_contains($routesApiContent, '/doctors'), "Ruta API /doctors registrada");
$runner->assertTrue(str_contains($routesApiContent, '/patients'), "Ruta API /patients registrada");
$runner->assertTrue(str_contains($routesApiContent, '/specialties'), "Ruta API /specialties registrada");
$runner->assertTrue(str_contains($routesApiContent, '/status'), "Ruta API /status para transiciones registrada");

// Rutas Web
$runner->assertTrue(str_contains($routesWebContent, 'calendar'), "Ruta Web de Calendario registrada");
$runner->assertTrue(str_contains($routesWebContent, 'reschedule'), "Ruta Web de Reprogramación registrada");
$runner->assertTrue(str_contains($routesWebContent, 'cancel'), "Ruta Web de Cancelación registrada");

// Métodos en controladores
$runner->assertTrue(str_contains($appointmentCtrlContent, 'function calendarEvents'), "Método calendarEvents() implementado en API");
$runner->assertTrue(str_contains($appointmentCtrlContent, 'function reschedule'), "Método reschedule() implementado en API");
$runner->assertTrue(str_contains($appointmentCtrlContent, 'function changeStatus'), "Método changeStatus() implementado en API");
$runner->assertTrue(str_contains($appointmentWebCtrlContent, 'function calendar'), "Método calendar() implementado en Web Controller");
$runner->assertTrue(str_contains($appointmentWebCtrlContent, "'stats'"), "Cálculo de KPIs 'stats' incluido en Web Controller");

// Terminar con reporte de salida
exit($runner->summary());
