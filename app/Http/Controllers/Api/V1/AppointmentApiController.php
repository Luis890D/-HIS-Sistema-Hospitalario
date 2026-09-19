<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointments\CancelAppointmentRequest;
use App\Http\Requests\Appointments\ChangeAppointmentStatusRequest;
use App\Http\Requests\Appointments\RescheduleAppointmentRequest;
use App\Http\Requests\Appointments\StoreAppointmentRequest;
use App\Http\Requests\Appointments\UpdateAppointmentRequest;
use App\Http\Resources\V1\AppointmentResource;
use App\Models\Appointment;
use App\Services\AppointmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AppointmentApiController extends Controller
{
    public function __construct(
        protected AppointmentService $appointmentService
    ) {}

    /**
     * GET /api/v1/appointments
     * Lista citas médicas con filtros opcionales (fecha, médico, paciente, estado)
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Appointment::with(['doctor.specialty', 'patient', 'specialty'])
            ->latest('appointment_date');

        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $appointments = $query->paginate($request->get('per_page', 15));

        return AppointmentResource::collection($appointments);
    }

    /**
     * GET /api/v1/calendar/events
     * Feed de eventos formateado directamente para FullCalendar v6
     */
    public function calendarEvents(Request $request): JsonResponse
    {
        $query = Appointment::with(['doctor', 'patient', 'specialty']);

        if ($request->filled('start')) {
            $query->whereDate('appointment_date', '>=', substr($request->start, 0, 10));
        }

        if ($request->filled('end')) {
            $query->whereDate('appointment_date', '<=', substr($request->end, 0, 10));
        }

        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $appointments = $query->get();

        $events = $appointments->map(function ($app) {
            $colors = match($app->status) {
                \App\Enums\AppointmentStatus::PENDING => ['bg' => '#f59e0b', 'border' => '#d97706'],     // amber
                \App\Enums\AppointmentStatus::CONFIRMED => ['bg' => '#10b981', 'border' => '#059669'],   // emerald
                \App\Enums\AppointmentStatus::RESCHEDULED => ['bg' => '#3b82f6', 'border' => '#2563eb'], // blue
                \App\Enums\AppointmentStatus::ATTENDED => ['bg' => '#8b5cf6', 'border' => '#7c3aed'],    // purple
                \App\Enums\AppointmentStatus::CANCELLED => ['bg' => '#ef4444', 'border' => '#dc2626'],   // red
                \App\Enums\AppointmentStatus::NO_SHOW => ['bg' => '#6b7280', 'border' => '#4b5563'],     // gray
            };

            $startTimeFormatted = substr($app->start_time, 0, 5);
            $endTimeFormatted = substr($app->end_time, 0, 5);
            $dateStr = $app->appointment_date->format('Y-m-d');

            return [
                'id'              => (string) $app->id,
                'title'           => "{$app->patient?->full_name} - {$app->doctor?->full_name}",
                'start'           => "{$dateStr}T{$startTimeFormatted}:00",
                'end'             => "{$dateStr}T{$endTimeFormatted}:00",
                'backgroundColor' => $colors['bg'],
                'borderColor'     => $colors['border'],
                'textColor'       => '#ffffff',
                'editable'        => !in_array($app->status, [\App\Enums\AppointmentStatus::ATTENDED, \App\Enums\AppointmentStatus::CANCELLED]),
                'extendedProps'   => [
                    'code'            => $app->appointment_code,
                    'status'          => $app->status->value,
                    'status_label'    => $app->status->label(),
                    'badge_color'     => $app->status->badgeColor(),
                    'patient_name'    => $app->patient?->full_name,
                    'patient_id'      => $app->patient_id,
                    'doctor_name'     => $app->doctor?->full_name,
                    'doctor_id'       => $app->doctor_id,
                    'specialty_name'  => $app->specialty?->name,
                    'reason'          => $app->reason,
                    'clinical_notes'  => $app->clinical_notes,
                    'start_time'      => $startTimeFormatted,
                    'end_time'        => $endTimeFormatted,
                    'date'            => $dateStr,
                ],
            ];
        });

        return response()->json($events);
    }

    /**
     * POST /api/v1/appointments
     * Agendar una nueva cita médica
     */
    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        $appointment = $this->appointmentService->scheduleAppointment($request->validated());

        return (new AppointmentResource($appointment->load(['doctor.specialty', 'patient', 'specialty'])))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * GET /api/v1/appointments/{id}
     * Obtener el detalle de una cita
     */
    public function show(int $id): JsonResponse
    {
        $appointment = Appointment::with(['doctor.specialty', 'patient', 'specialty', 'rescheduledFrom'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => new AppointmentResource($appointment),
        ]);
    }

    /**
     * PUT/PATCH /api/v1/appointments/{id}
     * Actualizar información general de una cita médica
     */
    public function update(UpdateAppointmentRequest $request, int $id): JsonResponse
    {
        $appointment = Appointment::findOrFail($id);
        $updated = $this->appointmentService->updateAppointment($appointment, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cita médica actualizada correctamente.',
            'data'    => new AppointmentResource($updated),
        ]);
    }

    /**
     * PUT/PATCH /api/v1/appointments/{id}/reschedule
     * Reprogramar una cita médica existente (con motivo y nueva fecha)
     */
    public function reschedule(RescheduleAppointmentRequest $request, int $id): JsonResponse
    {
        $appointment = Appointment::findOrFail($id);
        $updatedAppointment = $this->appointmentService->rescheduleAppointment($appointment, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cita médica reprogramada exitosamente.',
            'data'    => new AppointmentResource($updatedAppointment),
        ]);
    }

    /**
     * PUT/PATCH /api/v1/appointments/{id}/cancel
     * Cancelar una cita médica con justificación
     */
    public function cancel(CancelAppointmentRequest $request, int $id): JsonResponse
    {
        $appointment = Appointment::findOrFail($id);
        $cancelledAppointment = $this->appointmentService->cancelAppointment(
            $appointment,
            $request->validated('cancellation_reason')
        );

        return response()->json([
            'success' => true,
            'message' => 'Cita médica cancelada correctamente.',
            'data'    => new AppointmentResource($cancelledAppointment),
        ]);
    }

    /**
     * PATCH /api/v1/appointments/{id}/status
     * Cambiar de estado una cita médica (confirmed, attended, no_show, cancelled, pending)
     */
    public function changeStatus(ChangeAppointmentStatusRequest $request, int $id): JsonResponse
    {
        $appointment = Appointment::findOrFail($id);
        $updated = $this->appointmentService->changeStatus(
            $appointment,
            $request->validated('status'),
            $request->validated('note'),
            $request->validated('cancellation_reason')
        );

        return response()->json([
            'success' => true,
            'message' => "Estado de la cita actualizado a '{$updated->status->label()}'.",
            'data'    => new AppointmentResource($updated),
        ]);
    }
}
