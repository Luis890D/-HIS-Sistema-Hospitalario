<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'code'                => $this->appointment_code,
            'status'              => [
                'value' => $this->status->value,
                'label' => $this->status->label(),
            ],
            'date'                => $this->appointment_date->format('Y-m-d'),
            'start_time'          => substr($this->start_time, 0, 5),
            'end_time'            => substr($this->end_time, 0, 5),
            'reason'              => $this->reason,
            'clinical_notes'      => $this->clinical_notes,
            'cancellation_reason' => $this->cancellation_reason,
            'cancelled_at'        => $this->cancelled_at?->toISOString(),
            'reschedule_reason'   => $this->reschedule_reason,
            'rescheduled_at'      => $this->rescheduled_at?->toISOString(),
            'patient'             => [
                'id'                    => $this->patient?->id,
                'full_name'             => $this->patient?->full_name,
                'medical_record_number' => $this->patient?->medical_record_number,
                'phone'                 => $this->patient?->phone,
                'email'                 => $this->patient?->email,
            ],
            'doctor'              => [
                'id'              => $this->doctor?->id,
                'full_name'       => $this->doctor?->full_name,
                'consulting_room' => $this->doctor?->consulting_room,
                'specialty'       => $this->doctor?->specialty?->name,
            ],
            'specialty'           => [
                'id'   => $this->specialty?->id,
                'name' => $this->specialty?->name,
            ],
            'created_at'          => $this->created_at?->toISOString(),
            'updated_at'          => $this->updated_at?->toISOString(),
        ];
    }
}
