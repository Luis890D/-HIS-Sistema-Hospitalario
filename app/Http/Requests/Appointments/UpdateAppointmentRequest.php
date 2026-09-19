<?php

namespace App\Http\Requests\Appointments;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'doctor_id'        => ['sometimes', 'integer', 'exists:doctors,id'],
            'specialty_id'     => ['sometimes', 'integer', 'exists:medical_specialties,id'],
            'appointment_date' => ['sometimes', 'date'],
            'start_time'       => ['sometimes', 'date_format:H:i', 'before:end_time'],
            'end_time'         => ['sometimes', 'date_format:H:i', 'after:start_time'],
            'reason'           => ['sometimes', 'string', 'max:500'],
            'clinical_notes'   => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'doctor_id.exists'        => 'El médico seleccionado no existe.',
            'specialty_id.exists'     => 'La especialidad médica no es válida.',
            'start_time.before'       => 'La hora de inicio debe ser anterior a la hora de finalización.',
            'end_time.after'          => 'La hora de fin debe ser posterior a la hora de inicio.',
        ];
    }
}
