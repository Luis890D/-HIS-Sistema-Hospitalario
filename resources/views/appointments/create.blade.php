@extends('layouts.app')

@section('title', 'Agendar Cita Médica')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Agendar Cita Médica</h1>
            <p class="text-sm text-slate-500 mt-1">Registra una nueva cita médica asignando paciente, doctor y horario disponible.</p>
        </div>
        <a href="{{ route('appointments.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">
            &larr; Volver al listado
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
        <form method="POST" action="{{ route('appointments.store') }}" class="space-y-6">
            @csrf

            <!-- Selección de Paciente -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                    Paciente <span class="text-rose-500">*</span>
                </label>
                <select name="patient_id" required class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-clinical-500 focus:outline-none">
                    <option value="">Selecciona un paciente...</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                            {{ $patient->full_name }} (Expediente: {{ $patient->medical_record_number }} - DNI: {{ $patient->identification_number }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Especialidad y Médico -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Especialidad Médica <span class="text-rose-500">*</span>
                    </label>
                    <select name="specialty_id" required class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-clinical-500 focus:outline-none">
                        <option value="">Selecciona la especialidad...</option>
                        @foreach($specialties as $specialty)
                            <option value="{{ $specialty->id }}" {{ old('specialty_id') == $specialty->id ? 'selected' : '' }}>
                                {{ $specialty->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Médico Asignado <span class="text-rose-500">*</span>
                    </label>
                    <select name="doctor_id" required class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-clinical-500 focus:outline-none">
                        <option value="">Selecciona el médico...</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                {{ $doctor->full_name }} ({{ $doctor->specialty?->name }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Fecha y Horario -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Fecha de la Cita <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="appointment_date" min="{{ date('Y-m-d') }}" value="{{ old('appointment_date') }}" required class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-clinical-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Hora Inicio <span class="text-rose-500">*</span>
                    </label>
                    <input type="time" name="start_time" value="{{ old('start_time', '09:00') }}" required class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-clinical-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Hora Fin Estimada <span class="text-rose-500">*</span>
                    </label>
                    <input type="time" name="end_time" value="{{ old('end_time', '09:30') }}" required class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-clinical-500 focus:outline-none">
                </div>
            </div>

            <!-- Motivo de Consulta -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                    Motivo de la Cita Médica <span class="text-rose-500">*</span>
                </label>
                <textarea name="reason" rows="3" required placeholder="Describe la sintomatología o motivo de la visita..." class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-clinical-500 focus:outline-none">{{ old('reason') }}</textarea>
            </div>

            <!-- Notas Clínicas Adicionales -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                    Notas de Triaje / Observaciones Clínicas (Opcional)
                </label>
                <textarea name="clinical_notes" rows="2" placeholder="Antecedentes relevantes o indicaciones para recepción..." class="w-full px-4 py-2.5 text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-clinical-500 focus:outline-none">{{ old('clinical_notes') }}</textarea>
            </div>

            <!-- Botones -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <a href="{{ route('appointments.index') }}" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-xl transition">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white bg-clinical-600 hover:bg-clinical-700 rounded-xl shadow-md shadow-clinical-500/20 transition">
                    Agendar Cita Médica
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
