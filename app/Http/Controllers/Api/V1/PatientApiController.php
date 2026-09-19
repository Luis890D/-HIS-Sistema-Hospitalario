<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\AppointmentResource;
use App\Http\Resources\V1\PatientResource;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PatientApiController extends Controller
{
    /**
     * GET /api/v1/patients
     * Listado de pacientes con filtros de búsqueda
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Patient::withCount('appointments')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('medical_record_number', 'like', "%{$search}%")
                  ->orWhere('identification_number', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        $patients = $query->paginate($request->get('per_page', 15));

        return PatientResource::collection($patients);
    }

    /**
     * GET /api/v1/patients/{id}
     * Detalle del expediente del paciente y su historial de citas
     */
    public function show(int $id): JsonResponse
    {
        $patient = Patient::withCount('appointments')->findOrFail($id);

        $appointmentsHistory = $patient->appointments()
            ->with(['doctor.specialty', 'specialty'])
            ->latest('appointment_date')
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => new PatientResource($patient),
            'appointments_history' => AppointmentResource::collection($appointmentsHistory),
        ]);
    }
}
