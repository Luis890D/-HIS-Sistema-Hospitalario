<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointments\CancelAppointmentRequest;
use App\Http\Requests\Appointments\RescheduleAppointmentRequest;
use App\Http\Requests\Appointments\StoreAppointmentRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\MedicalSpecialty;
use App\Models\Patient;
use App\Services\AppointmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentWebController extends Controller
{
    public function __construct(
        protected AppointmentService $appointmentService
    ) {}

    /**
     * Muestra el panel principal y listado de citas
     */
    public function index(Request $request): View
    {
        $query = Appointment::with(['doctor.specialty', 'patient', 'specialty'])
            ->latest('appointment_date');

        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->date);
        }

        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $appointments = $query->paginate(10)->withQueryString();
        $doctors = Doctor::where('is_active', true)->get();

        return view('appointments.index', compact('appointments', 'doctors'));
    }

    /**
     * Formulario para agendar una nueva cita
     */
    public function create(): View
    {
        $patients = Patient::orderBy('last_name')->get();
        $doctors = Doctor::with('specialty')->where('is_active', true)->get();
        $specialties = MedicalSpecialty::where('is_active', true)->get();

        return view('appointments.create', compact('patients', 'doctors', 'specialties'));
    }

    /**
     * Procesa el formulario de agendamiento
     */
    public function store(StoreAppointmentRequest $request): RedirectResponse
    {
        $appointment = $this->appointmentService->scheduleAppointment($request->validated());

        return redirect()
            ->route('appointments.show', $appointment)
            ->with('success', "Cita {$appointment->appointment_code} agendada correctamente.");
    }

    /**
     * Detalle de la cita médica
     */
    public function show(Appointment $appointment): View
    {
        $appointment->load(['doctor.specialty', 'patient', 'specialty', 'rescheduledFrom']);

        return view('appointments.show', compact('appointment'));
    }

    /**
     * Formulario para reprogramar cita
     */
    public function rescheduleForm(Appointment $appointment): View
    {
        $doctors = Doctor::where('is_active', true)->get();
        $specialties = MedicalSpecialty::where('is_active', true)->get();

        return view('appointments.reschedule', compact('appointment', 'doctors', 'specialties'));
    }

    /**
     * Procesa la reprogramación de la cita
     */
    public function reschedule(RescheduleAppointmentRequest $request, Appointment $appointment): RedirectResponse
    {
        $this->appointmentService->rescheduleAppointment($appointment, $request->validated());

        return redirect()
            ->route('appointments.show', $appointment)
            ->with('success', 'La cita médica ha sido reprogramada con éxito.');
    }

    /**
     * Procesa la cancelación de la cita
     */
    public function cancel(CancelAppointmentRequest $request, Appointment $appointment): RedirectResponse
    {
        $this->appointmentService->cancelAppointment($appointment, $request->validated('cancellation_reason'));

        return redirect()
            ->route('appointments.index')
            ->with('success', 'La cita médica ha sido cancelada.');
    }
}
