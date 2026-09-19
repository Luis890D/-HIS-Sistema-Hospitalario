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
