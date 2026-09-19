<?php

namespace App\Http\Requests\Appointments;

use App\Enums\AppointmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ChangeAppointmentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(AppointmentStatus::class)],
            'note'   => ['nullable', 'string', 'max:500'],
            'cancellation_reason' => ['required_if:status,cancelled', 'nullable', 'string', 'min:5', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'El nuevo estado de la cita es obligatorio.',
            'status.enum'     => 'El estado proporcionado no es válido.',
            'cancellation_reason.required_if' => 'Debe registrar el motivo si el estado a aplicar es cancelado.',
        ];
    }
}
