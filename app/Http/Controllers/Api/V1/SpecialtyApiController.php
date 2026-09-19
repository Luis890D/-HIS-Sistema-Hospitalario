<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\SpecialtyResource;
use App\Models\MedicalSpecialty;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SpecialtyApiController extends Controller
{
    /**
     * GET /api/v1/specialties
     * Listado de especialidades médicas activas
     */
    public function index(): AnonymousResourceCollection
    {
        $specialties = MedicalSpecialty::where('is_active', true)
            ->orderBy('name')
            ->get();

        return SpecialtyResource::collection($specialties);
    }
}
