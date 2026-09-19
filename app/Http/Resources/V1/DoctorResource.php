<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'full_name'       => $this->full_name,
            'first_name'      => $this->first_name,
            'last_name'       => $this->last_name,
            'license_number'  => $this->license_number,
            'email'           => $this->email,
            'phone'           => $this->phone,
            'consulting_room' => $this->consulting_room,
            'is_active'       => (bool) $this->is_active,
            'specialty'       => [
                'id'          => $this->specialty?->id,
                'name'        => $this->specialty?->name,
                'code'        => $this->specialty?->code,
            ],
            'appointments_count' => $this->whenCounted('appointments'),
            'created_at'      => $this->created_at?->toISOString(),
        ];
    }
}
