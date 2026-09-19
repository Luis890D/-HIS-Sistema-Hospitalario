<?php

namespace App\Http\Requests\Appointments;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id'       => ['required', 'integer', 'exists:patients,id'],
            'doctor_id'        => ['required', 'integer', 'exists:doctors,id'],
            'specialty_id'     => ['required', 'integer', 'exists:medical_specialties,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time'       => ['required', 'date_format:H:i', 'before:end_time'],
            'end_time'         => ['required', 'date_format:H:i', 'after:start_time'],
            'reason'           => ['required', 'string', 'max:500'],
            'clinical_notes'   => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.required'       => 'Debe seleccionar un paciente.',
            'patient_id.exists'         => 'El paciente seleccionado no existe.',
            'doctor_id.required'        => 'Debe asignar un médico a la cita.',
            'doctor_id.exists'          => 'El médico seleccionado no es válido.',
            'specialty_id.required'     => 'Debe indicar la especialidad médica.',
            'appointment_date.required' => 'La fecha de la cita es obligatoria.',
            'appointment_date.after_or_equal' => 'La cita no puede agendarse en fechas pasadas.',
            'start_time.required'       => 'La hora de inicio es obligatoria.',
            'end_time.required'         => 'La hora de fin es obligatoria.',
            'end_time.after'            => 'La hora de finalización debe ser posterior a la hora de inicio.',
            'reason.required'           => 'Debe especificar el motivo de la cita médica.',
        ];
    }
}
