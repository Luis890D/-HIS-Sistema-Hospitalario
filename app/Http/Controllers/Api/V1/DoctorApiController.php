<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\AppointmentResource;
use App\Http\Resources\V1\DoctorResource;
use App\Models\Doctor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DoctorApiController extends Controller
{
    /**
     * GET /api/v1/doctors
     * Listado de médicos facultados en el hospital
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Doctor::with('specialty')->withCount('appointments');

        if ($request->filled('specialty_id')) {
            $query->where('specialty_id', $request->specialty_id);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('license_number', 'like', "%{$search}%");
            });
        }

        $doctors = $query->paginate($request->get('per_page', 15));

        return DoctorResource::collection($doctors);
    }

    /**
     * GET /api/v1/doctors/{id}
     * Detalle del médico y sus próximas citas
     */
    public function show(int $id): JsonResponse
    {
        $doctor = Doctor::with('specialty')
            ->withCount('appointments')
            ->findOrFail($id);

        $upcomingAppointments = $doctor->appointments()
            ->with(['patient', 'specialty'])
            ->whereDate('appointment_date', '>=', now())
            ->orderBy('appointment_date')
            ->orderBy('start_time')
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => new DoctorResource($doctor),
            'upcoming_appointments' => AppointmentResource::collection($upcomingAppointments),
        ]);
    }
}
