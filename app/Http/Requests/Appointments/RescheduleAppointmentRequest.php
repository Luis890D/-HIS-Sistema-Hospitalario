<?php

namespace App\Http\Requests\Appointments;

use Illuminate\Foundation\Http\FormRequest;

class RescheduleAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'doctor_id'          => ['nullable', 'integer', 'exists:doctors,id'],
            'specialty_id'       => ['nullable', 'integer', 'exists:medical_specialties,id'],
            'appointment_date'   => ['required', 'date', 'after_or_equal:today'],
            'start_time'         => ['required', 'date_format:H:i', 'before:end_time'],
            'end_time'           => ['required', 'date_format:H:i', 'after:start_time'],
            'reschedule_reason'  => ['required', 'string', 'min:5', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'appointment_date.required' => 'La nueva fecha para la cita es obligatoria.',
            'appointment_date.after_or_equal' => 'La nueva fecha no puede ser en el pasado.',
            'start_time.required'       => 'La nueva hora de inicio es requerida.',
            'end_time.required'         => 'La nueva hora de fin es requerida.',
            'reschedule_reason.required'=> 'Debe documentar el motivo de la reprogramación.',
        ];
    }
}
