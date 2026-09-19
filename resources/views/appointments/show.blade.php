@extends('layouts.app')

@section('title', 'Expediente de Cita ' . $appointment->appointment_code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Encabezado de Cita Médica -->
    <div class="sm:flex sm:items-center sm:justify-between no-print">
        <div>
            <div class="flex items-center space-x-3">
                <span class="w-3 h-3 rounded-full bg-clinical-500 animate-pulse"></span>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight font-display font-mono">{{ $appointment->appointment_code }}</h1>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border shadow-sm {{ $appointment->status->badgeColor() }}">
                    {{ $appointment->status->label() }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Ficha clínica, trazabilidad de estados y comprobante hospitalario de atención.</p>
        </div>

        <!-- Botones de Acción Superior -->
        <div class="mt-4 sm:mt-0 flex items-center space-x-2.5">
            <a href="{{ route('appointments.index') }}" class="px-3.5 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 shadow-sm transition">
                &larr; Volver
            </a>

            <!-- Botón de Impresión de Ticket de Cita -->
            <button type="button" onclick="window.print()" class="inline-flex items-center px-3.5 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 rounded-xl shadow-sm transition active:scale-95">
                <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Imprimir Comprobante
            </button>

            @if(!in_array($appointment->status->value, ['cancelled', 'attended']))
                <a href="{{ route('appointments.reschedule.form', $appointment) }}" class="inline-flex items-center px-3.5 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl shadow-md shadow-blue-600/20 transition active:scale-95">
                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Reprogramar
                </a>
                <button type="button" onclick="openCancelModal()" class="inline-flex items-center px-3.5 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-md shadow-rose-600/20 transition active:scale-95">
                    Cancelar Cita
                </button>
            @endif
        </div>
    </div>

    <!-- Stepper Visual de Ciclo de Vida Clínico -->
    <div class="glass-card p-5 rounded-3xl border border-slate-200/90 shadow-sm no-print">
        <h4 class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-4">Línea de Tiempo del Ciclo Clínico</h4>
        <div class="relative flex items-center justify-between">
            <!-- Barra conectora -->
            <div class="absolute left-6 right-6 top-1/2 -translate-y-1/2 h-0.5 bg-slate-200 -z-0"></div>

            <!-- Paso 1: Agendada / Pendiente -->
            <div class="relative z-10 flex flex-col items-center">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold {{ in_array($appointment->status->value, ['pending', 'confirmed', 'rescheduled', 'attended']) ? 'bg-clinical-600 text-white shadow-md shadow-clinical-600/30' : 'bg-slate-200 text-slate-500' }}">
                    1
                </div>
                <span class="text-[11px] font-semibold text-slate-800 mt-1.5">Agendada</span>
                <span class="text-[10px] text-slate-400 font-mono">{{ $appointment->created_at->format('d/m H:i') }}</span>
            </div>

            <!-- Paso 2: Confirmada / Reprogramada -->
            <div class="relative z-10 flex flex-col items-center">
                @php
                    $step2Active = in_array($appointment->status->value, ['confirmed', 'rescheduled', 'attended']);
                @endphp
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold {{ $step2Active ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'bg-slate-200 text-slate-500' }}">
                    2
                </div>
                <span class="text-[11px] font-semibold {{ $step2Active ? 'text-slate-800' : 'text-slate-400' }} mt-1.5">
                    {{ $appointment->status->value === 'rescheduled' ? 'Reprogramada' : 'Confirmada' }}
                </span>
                <span class="text-[10px] text-slate-400 font-mono">
                    {{ $appointment->rescheduled_at ? $appointment->rescheduled_at->format('d/m H:i') : ($appointment->status->value === 'confirmed' ? 'Validada' : 'En espera') }}
                </span>
            </div>

            <!-- Paso 3: Atendida o Terminal -->
            <div class="relative z-10 flex flex-col items-center">
                @php
                    $isAttended = $appointment->status->value === 'attended';
                    $isCancelled = $appointment->status->value === 'cancelled';
                @endphp
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold {{ $isAttended ? 'bg-purple-600 text-white shadow-md shadow-purple-600/30' : ($isCancelled ? 'bg-rose-600 text-white shadow-md shadow-rose-600/30' : 'bg-slate-200 text-slate-500') }}">
                    3
                </div>
                <span class="text-[11px] font-semibold {{ ($isAttended || $isCancelled) ? 'text-slate-800' : 'text-slate-400' }} mt-1.5">
                    {{ $isCancelled ? 'Cancelada' : ($isAttended ? 'Atendida' : 'Consulta Final') }}
                </span>
                <span class="text-[10px] text-slate-400 font-mono">
                    {{ $appointment->cancelled_at ? $appointment->cancelled_at->format('d/m H:i') : ($isAttended ? 'Finalizada' : 'Pendiente') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Bloque de Alerta si fue Cancelada -->
    @if($appointment->status->value === 'cancelled')
        <div class="bg-rose-50/90 border border-rose-200/90 rounded-3xl p-5 text-rose-900 shadow-sm">
            <h4 class="font-bold text-sm flex items-center mb-1">
                <svg class="w-4 h-4 mr-2 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Esta cita médica fue cancelada el {{ $appointment->cancelled_at?->format('d/m/Y H:i') }}
            </h4>
            <p class="text-xs text-rose-800 mt-1"><span class="font-bold">Motivo registrado:</span> {{ $appointment->cancellation_reason }}</p>
        </div>
    @endif

    <!-- Bloque Informativo si fue Reprogramada -->
    @if($appointment->status->value === 'rescheduled')
        <div class="bg-blue-50/90 border border-blue-200/90 rounded-3xl p-5 text-blue-900 shadow-sm">
            <h4 class="font-bold text-sm flex items-center mb-1">
                <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Cita reprogramada el {{ $appointment->rescheduled_at?->format('d/m/Y H:i') }}
            </h4>
            <p class="text-xs text-blue-800 mt-1"><span class="font-bold">Motivo del cambio:</span> {{ $appointment->reschedule_reason }}</p>
        </div>
    @endif

    <!-- Tarjetas de Información Clínica: Paciente y Médico -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <!-- Ficha del Paciente -->
        <div class="glass-card rounded-3xl p-6 border border-slate-200/90 shadow-sm space-y-4">
            <div class="flex items-center space-x-3 border-b border-slate-100 pb-3.5">
                <div class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center border border-teal-200/60">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-sm font-display">Información del Paciente</h3>
                    <p class="text-xs text-slate-500">Expediente Clínico Hospitalario</p>
                </div>
            </div>

            <div class="space-y-2.5 text-xs">
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-500">Nombre Completo:</span>
                    <span class="font-bold text-slate-900 text-right">{{ $appointment->patient?->full_name }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-500">No. Expediente:</span>
                    <span class="font-mono font-semibold text-clinical-700 text-right">{{ $appointment->patient?->medical_record_number }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-500">DNI / Documento:</span>
                    <span class="font-mono text-slate-800 text-right">{{ $appointment->patient?->identification_number }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-500">Teléfono:</span>
                    <span class="text-slate-800 text-right">{{ $appointment->patient?->phone }}</span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-slate-500">Alergias Conocidas:</span>
                    <span class="font-medium {{ $appointment->patient?->allergies ? 'text-rose-600 font-bold' : 'text-slate-400' }} text-right">
                        {{ $appointment->patient?->allergies ?: 'Ninguna registrada' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Ficha del Médico Especialista -->
        <div class="glass-card rounded-3xl p-6 border border-slate-200/90 shadow-sm space-y-4">
            <div class="flex items-center space-x-3 border-b border-slate-100 pb-3.5">
                <div class="w-10 h-10 rounded-2xl bg-clinical-50 text-clinical-600 flex items-center justify-center border border-clinical-200/60">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-sm font-display">Médico y Consultorio</h3>
                    <p class="text-xs text-slate-500">Especialista Asignado</p>
                </div>
            </div>

            <div class="space-y-2.5 text-xs">
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-500">Médico:</span>
                    <span class="font-bold text-slate-900 text-right">{{ $appointment->doctor?->full_name }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-500">Especialidad:</span>
                    <span class="font-semibold text-clinical-700 text-right">{{ $appointment->specialty?->name }}</span>
                </div>
                <div class="flex justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-500">No. Colegiado:</span>
                    <span class="font-mono text-slate-800 text-right">{{ $appointment->doctor?->license_number }}</span>
                </div>
                <div class="flex justify-between py-1">
                    <span class="text-slate-500">Consultorio / Ubicación:</span>
                    <span class="font-bold text-slate-900 text-right">{{ $appointment->doctor?->consulting_room ?: 'Consultorio Principal' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Comprobante / Voucher Físico Imprimible -->
    <div class="printable-voucher glass-card rounded-3xl p-6 sm:p-7 border border-slate-200/90 shadow-sm space-y-4">
        <div class="flex items-center justify-between border-b border-slate-200/80 pb-3.5">
            <div>
                <h3 class="font-extrabold text-slate-900 text-sm font-display uppercase tracking-wider">Comprobante de Cita Médica</h3>
                <p class="text-[11px] text-slate-500">Hospital General • Servicio de Admisión y Consulta Externa</p>
            </div>
            <!-- Código de Barras / QR Visual Simulado -->
            <div class="text-right">
                <div class="font-mono text-xs font-bold text-slate-700">{{ $appointment->appointment_code }}</div>
                <div class="text-[10px] tracking-widest text-slate-400 font-mono">||| | | || ||| || |||</div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Fecha y Horario:</span>
                <div class="text-sm font-extrabold text-slate-900 font-display">
                    {{ $appointment->appointment_date->format('d/m/Y') }} &bull; {{ substr($appointment->start_time, 0, 5) }} a {{ substr($appointment->end_time, 0, 5) }}
                </div>
            </div>
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Fecha de Emisión:</span>
                <div class="text-xs font-medium text-slate-800">{{ $appointment->created_at->format('d/m/Y H:i:s') }}</div>
            </div>
        </div>

        <div class="pt-2 text-xs space-y-3">
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Motivo de Consulta:</span>
                <p class="text-slate-800 bg-slate-50 p-3 rounded-2xl border border-slate-100 leading-relaxed">{{ $appointment->reason }}</p>
            </div>

            @if($appointment->clinical_notes)
                <div>
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Observaciones Clínicas / Trazabilidad:</span>
                    <p class="text-slate-700 bg-amber-50/60 p-3 rounded-2xl border border-amber-100 font-mono text-[11px] leading-relaxed">{{ $appointment->clinical_notes }}</p>
                </div>
            @endif

            <div class="p-3 rounded-xl bg-slate-50 text-[11px] text-slate-500 leading-relaxed border border-slate-200/50">
                <strong>Instrucciones para el paciente:</strong> Presentarse 15 minutos antes de la hora indicada en el consultorio asignado. Presentar documento de identidad y este comprobante.
            </div>
        </div>
    </div>
</div>

<!-- Modal para Cancelar Cita -->
<div id="cancelModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-md z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl space-y-4 border border-slate-100">
        <h3 class="text-lg font-bold text-slate-900 font-display">Cancelar Cita Médica</h3>
        <p class="text-xs text-slate-500 leading-relaxed">Por favor ingrese la justificación clínica o administrativa para cancelar la cita. Esta acción liberará el horario del médico.</p>
        <form method="POST" action="{{ route('appointments.cancel', $appointment) }}" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Motivo de Cancelación</label>
                <textarea name="cancellation_reason" required rows="3" class="w-full px-3.5 py-2 text-xs font-medium border border-slate-200 rounded-xl bg-slate-50/60 focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none transition" placeholder="Indique el motivo detallado de la cancelación..."></textarea>
            </div>
            <div class="flex justify-end space-x-2.5 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeCancelModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">Cerrar</button>
                <button type="submit" class="inline-flex items-center px-4 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-md shadow-rose-600/25 transition active:scale-95">Confirmar Cancelación</button>
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
