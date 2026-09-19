@extends('layouts.app')

@section('title', 'Reprogramar Cita Médica')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Reprogramar Cita Médica</h1>
            <p class="text-sm text-slate-500 mt-1">Modificar la fecha y horario para la cita <span class="font-mono font-semibold text-clinical-600">{{ $appointment->appointment_code }}</span>.</p>
        </div>
        <a href="{{ route('appointments.show', $appointment) }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">
            &larr; Volver a la cita
        </a>
    </div>

    <!-- Resumen de cita actual -->
    <div class="bg-amber-50/70 border border-amber-200 rounded-2xl p-5 text-sm text-amber-900">
        <h3 class="font-bold mb-2 flex items-center">
            <svg class="w-4 h-4 mr-1.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Horario Actual de la Cita
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
            <div><span class="font-semibold">Paciente:</span> {{ $appointment->patient?->full_name }}</div>
            <div><span class="font-semibold">Médico:</span> {{ $appointment->doctor?->full_name }}</div>
            <div><span class="font-semibold">Fecha y Hora:</span> {{ $appointment->appointment_date->format('d/m/Y') }} a las {{ substr($appointment->start_time, 0, 5) }}</div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
        <form method="POST" action="{{ route('appointments.reschedule', $appointment) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Cambio de Médico (Opcional) -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                    Médico Especialista
                </label>
                <select name="doctor_id" class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-clinical-500 focus:outline-none">
                    @foreach($doctors as $doc)
                        <option value="{{ $doc->id }}" {{ old('doctor_id', $appointment->doctor_id) == $doc->id ? 'selected' : '' }}>
                            {{ $doc->full_name }} ({{ $doc->specialty?->name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Nueva Fecha y Horario -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Nueva Fecha <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="appointment_date" min="{{ date('Y-m-d') }}" value="{{ old('appointment_date', $appointment->appointment_date->format('Y-m-d')) }}" required class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-clinical-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Nueva Hora Inicio <span class="text-rose-500">*</span>
                    </label>
                    <input type="time" name="start_time" value="{{ old('start_time', substr($appointment->start_time, 0, 5)) }}" required class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-clinical-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Nueva Hora Fin <span class="text-rose-500">*</span>
                    </label>
                    <input type="time" name="end_time" value="{{ old('end_time', substr($appointment->end_time, 0, 5)) }}" required class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-clinical-500 focus:outline-none">
                </div>
            </div>

            <!-- Justificación obligatoria -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                    Motivo de la Reprogramación <span class="text-rose-500">*</span>
                </label>
                <textarea name="reschedule_reason" rows="3" required placeholder="Explique la razón por la que se modifica la fecha de la cita..." class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-clinical-500 focus:outline-none">{{ old('reschedule_reason', $appointment->reschedule_reason) }}</textarea>
            </div>

            <!-- Botones -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <a href="{{ route('appointments.show', $appointment) }}" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-xl transition">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-md shadow-blue-500/20 transition">
                    Guardar Reprogramación
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
