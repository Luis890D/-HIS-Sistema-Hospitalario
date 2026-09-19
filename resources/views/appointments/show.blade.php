@extends('layouts.app')

@section('title', 'Detalle de Cita Médica ' . $appointment->appointment_code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight font-mono">{{ $appointment->appointment_code }}</h1>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $appointment->status->badgeColor() }}">
                    {{ $appointment->status->label() }}
                </span>
            </div>
            <p class="text-sm text-slate-500 mt-1">Detalle y trazabilidad clínica de la cita médica agendada.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <a href="{{ route('appointments.index') }}" class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition">
                &larr; Volver
            </a>
            @if(!in_array($appointment->status->value, ['cancelled', 'attended']))
                <a href="{{ route('appointments.reschedule.form', $appointment) }}" class="px-4 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm transition">
                    Reprogramar
                </a>
                <button type="button" onclick="openCancelModal('{{ $appointment->id }}', '{{ $appointment->appointment_code }}')" class="px-4 py-2 text-sm font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-lg shadow-sm transition">
                    Cancelar
                </button>
            @endif
        </div>
    </div>

    <!-- Bloque de Cancelación (Si aplica) -->
    @if($appointment->status->value === 'cancelled')
        <div class="bg-rose-50 border border-rose-200 rounded-2xl p-5 text-rose-900">
            <h4 class="font-bold text-sm flex items-center mb-1">
                <svg class="w-4 h-4 mr-1.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Esta cita médica fue cancelada el {{ $appointment->cancelled_at?->format('d/m/Y H:i') }}
            </h4>
            <p class="text-xs text-rose-800"><span class="font-semibold">Motivo registrado:</span> {{ $appointment->cancellation_reason }}</p>
        </div>
    @endif

    <!-- Bloque de Reprogramación (Si aplica) -->
    @if($appointment->status->value === 'rescheduled')
        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 text-blue-900">
            <h4 class="font-bold text-sm flex items-center mb-1">
                <svg class="w-4 h-4 mr-1.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Cita reprogramada el {{ $appointment->rescheduled_at?->format('d/m/Y H:i') }}
            </h4>
            <p class="text-xs text-blue-800"><span class="font-semibold">Motivo del cambio:</span> {{ $appointment->reschedule_reason }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Tarjeta de Paciente -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center space-x-3 border-b border-slate-100 pb-3">
                <div class="w-9 h-9 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Información del Paciente</h3>
                    <p class="text-xs text-slate-500">Expediente Clínico Hospitalario</p>
                </div>
            </div>
            <div class="space-y-2 text-xs">
                <div><span class="text-slate-500">Nombre Completo:</span> <span class="font-semibold text-slate-900">{{ $appointment->patient?->full_name }}</span></div>
                <div><span class="text-slate-500">No. Expediente:</span> <span class="font-mono text-slate-800">{{ $appointment->patient?->medical_record_number }}</span></div>
                <div><span class="text-slate-500">DPI / Cédula:</span> <span class="font-mono text-slate-800">{{ $appointment->patient?->identification_number }}</span></div>
                <div><span class="text-slate-500">Teléfono:</span> <span class="text-slate-800">{{ $appointment->patient?->phone }}</span></div>
                <div><span class="text-slate-500">Alergias:</span> <span class="text-rose-600 font-medium">{{ $appointment->patient?->allergies ?: 'Ninguna' }}</span></div>
            </div>
        </div>

        <!-- Tarjeta de Médico y Consultorio -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center space-x-3 border-b border-slate-100 pb-3">
                <div class="w-9 h-9 rounded-lg bg-clinical-50 text-clinical-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Médico Asignado</h3>
                    <p class="text-xs text-slate-500">Especialista y Ubicación</p>
                </div>
            </div>
            <div class="space-y-2 text-xs">
                <div><span class="text-slate-500">Especialista:</span> <span class="font-semibold text-slate-900">{{ $appointment->doctor?->full_name }}</span></div>
                <div><span class="text-slate-500">Especialidad:</span> <span class="text-slate-800">{{ $appointment->specialty?->name }}</span></div>
                <div><span class="text-slate-500">No. Colegiado:</span> <span class="font-mono text-slate-800">{{ $appointment->doctor?->license_number }}</span></div>
                <div><span class="text-slate-500">Ubicación / Consultorio:</span> <span class="text-slate-800">{{ $appointment->doctor?->consulting_room ?: 'Por asignar' }}</span></div>
            </div>
        </div>
    </div>

    <!-- Horario y Motivo -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4">
        <h3 class="font-bold text-slate-900 text-sm border-b border-slate-100 pb-3">Detalle Clínico de la Consulta</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div>
                <span class="text-slate-500 block mb-1">Fecha y Horario:</span>
                <div class="text-sm font-semibold text-slate-900">
                    {{ $appointment->appointment_date->format('d/m/Y') }} &bull; {{ substr($appointment->start_time, 0, 5) }} a {{ substr($appointment->end_time, 0, 5) }}
                </div>
            </div>
            <div>
                <span class="text-slate-500 block mb-1">Fecha de Registro:</span>
                <div class="text-slate-800">{{ $appointment->created_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>
        <div class="pt-2 text-xs space-y-2">
            <div>
                <span class="text-slate-500 block font-semibold mb-1">Motivo de Consulta:</span>
                <p class="text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-100">{{ $appointment->reason }}</p>
            </div>
            @if($appointment->clinical_notes)
                <div>
                    <span class="text-slate-500 block font-semibold mb-1">Observaciones / Notas de Triaje:</span>
                    <p class="text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-100">{{ $appointment->clinical_notes }}</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal para Cancelar -->
<div id="cancelModal" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
        <h3 class="text-lg font-bold text-slate-900 mb-2">Cancelar Cita Médica</h3>
        <p class="text-sm text-slate-500 mb-4">Por favor ingresa la justificación para cancelar la cita.</p>
        <form method="POST" action="{{ route('appointments.cancel', $appointment) }}">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Motivo de Cancelación</label>
                <textarea name="cancellation_reason" required rows="3" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:outline-none" placeholder="Indique el motivo por el cual se cancela..."></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeCancelModal()" class="px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-lg transition">Cerrar</button>
                <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-lg shadow-sm transition">Confirmar Cancelación</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCancelModal() {
        document.getElementById('cancelModal').classList.remove('hidden');
    }
    function closeCancelModal() {
        document.getElementById('cancelModal').classList.add('hidden');
    }
</script>
@endsection
