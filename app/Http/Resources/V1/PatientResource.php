<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                      => $this->id,
            'full_name'               => $this->full_name,
            'first_name'              => $this->first_name,
            'last_name'               => $this->last_name,
            'medical_record_number'   => $this->medical_record_number,
            'identification_number'   => $this->identification_number,
            'birth_date'              => $this->birth_date?->format('Y-m-d'),
            'gender'                  => $this->gender,
            'blood_type'              => $this->blood_type,
            'phone'                   => $this->phone,
            'email'                   => $this->email,
            'emergency_contact'       => [
                'name'  => $this->emergency_contact_name,
                'phone' => $this->emergency_contact_phone,
            ],
            'allergies'               => $this->allergies,
            'appointments_count'      => $this->whenCounted('appointments'),
            'created_at'              => $this->created_at?->toISOString(),
        ];
    }
}
