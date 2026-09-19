<?php

namespace App\Http\Requests\Appointments;

use Illuminate\Foundation\Http\FormRequest;

class CancelAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cancellation_reason' => ['required', 'string', 'min:5', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'cancellation_reason.required' => 'Debe registrar la justificación o motivo de la cancelación de la cita.',
            'cancellation_reason.min'      => 'El motivo de cancelación debe contener al menos 5 caracteres.',
        ];
    }
}
