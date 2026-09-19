@extends('layouts.app')

@section('title', 'Reprogramar Cita ' . $appointment->appointment_code)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Encabezado de Página -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2.5">
                <span class="w-3 h-3 rounded-full bg-blue-500 animate-pulse"></span>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight font-display">Reprogramar Cita Médica</h1>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Modificar fecha y horario para la cita <span class="font-mono font-bold text-clinical-600">{{ $appointment->appointment_code }}</span>.
            </p>
        </div>
        <a href="{{ route('appointments.show', $appointment) }}" class="px-3.5 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 shadow-sm transition">
            &larr; Volver a la cita
        </a>
    </div>

    <!-- Comparativa Visual: Horario Actual Registrado -->
    <div class="glass-card rounded-3xl p-5 border border-amber-200/80 bg-amber-50/40 shadow-sm">
        <div class="flex items-center space-x-2 text-xs font-bold uppercase tracking-wider text-amber-900 mb-3">
            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Horario Actual de la Cita Médica</span>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
            <div class="p-3 bg-white/80 rounded-2xl border border-amber-100">
                <span class="text-[11px] text-slate-400 block font-bold uppercase">Paciente</span>
                <span class="font-bold text-slate-900 text-xs">{{ $appointment->patient?->full_name }}</span>
            </div>
            <div class="p-3 bg-white/80 rounded-2xl border border-amber-100">
                <span class="text-[11px] text-slate-400 block font-bold uppercase">Médico</span>
                <span class="font-bold text-slate-900 text-xs">{{ $appointment->doctor?->full_name }}</span>
            </div>
            <div class="p-3 bg-white/80 rounded-2xl border border-amber-100">
                <span class="text-[11px] text-slate-400 block font-bold uppercase">Fecha y Hora</span>
                <span class="font-mono font-extrabold text-amber-800 text-xs">
                    {{ $appointment->appointment_date->format('d/m/Y') }} • {{ substr($appointment->start_time, 0, 5) }} - {{ substr($appointment->end_time, 0, 5) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Formulario de Reprogramación -->
    <div class="glass-card rounded-3xl shadow-sm border border-slate-200/90 p-6 sm:p-8">
        <form method="POST" action="{{ route('appointments.reschedule', $appointment) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Asignación de Médico Especialista -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Médico Especialista Asignado <span class="text-rose-500">*</span>
                </label>
                <select name="doctor_id" class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 rounded-xl bg-slate-50/70 focus:bg-white focus:ring-2 focus:ring-clinical-500 focus:outline-none transition">
                    @foreach($doctors as $doc)
                        <option value="{{ $doc->id }}" {{ old('doctor_id', $appointment->doctor_id) == $doc->id ? 'selected' : '' }}>
                            {{ $doc->full_name }} ({{ $doc->specialty?->name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Nuevo Horario y Duración -->
            <div class="p-5 rounded-2xl bg-slate-50/90 border border-slate-200/80 space-y-3.5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Nuevo Horario Propuesto</span>
                    <span class="text-[11px] text-slate-400 font-medium">Validación con bloqueo pesimista en servidor</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Nueva Fecha <span class="text-rose-500">*</span></label>
                        <input type="date" name="appointment_date" min="{{ date('Y-m-d') }}" value="{{ old('appointment_date', $appointment->appointment_date->format('Y-m-d')) }}" required class="w-full px-3.5 py-2 text-xs font-medium border border-slate-200 rounded-xl bg-white focus:ring-2 focus:ring-clinical-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Nueva Hora Inicio <span class="text-rose-500">*</span></label>
                        <input type="time" id="resched_start_time" name="start_time" onchange="calcReschedEnd()" value="{{ old('start_time', substr($appointment->start_time, 0, 5)) }}" required class="w-full px-3.5 py-2 text-xs font-medium border border-slate-200 rounded-xl bg-white focus:ring-2 focus:ring-clinical-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Nueva Hora Fin <span class="text-rose-500">*</span></label>
                        <input type="time" id="resched_end_time" name="end_time" value="{{ old('end_time', substr($appointment->end_time, 0, 5)) }}" required class="w-full px-3.5 py-2 text-xs font-medium border border-slate-200 rounded-xl bg-white focus:ring-2 focus:ring-clinical-500 focus:outline-none">
                    </div>
                </div>

                <!-- Selectores Rápidos de Duración -->
                <div class="flex items-center justify-between pt-1">
                    <span class="text-[11px] font-semibold text-slate-500">Duración estimada:</span>
                    <div class="flex items-center space-x-1.5">
                        <button type="button" onclick="setReschedDuration(15)" class="resched-dur-btn px-2.5 py-1 rounded-md text-[11px] font-semibold bg-white border border-slate-200 hover:border-clinical-400 text-slate-600 transition">15 min</button>
                        <button type="button" onclick="setReschedDuration(30)" class="resched-dur-btn active-resched-dur px-2.5 py-1 rounded-md text-[11px] font-semibold bg-clinical-50 border border-clinical-300 text-clinical-700 transition">30 min</button>
                        <button type="button" onclick="setReschedDuration(45)" class="resched-dur-btn px-2.5 py-1 rounded-md text-[11px] font-semibold bg-white border border-slate-200 hover:border-clinical-400 text-slate-600 transition">45 min</button>
                        <button type="button" onclick="setReschedDuration(60)" class="resched-dur-btn px-2.5 py-1 rounded-md text-[11px] font-semibold bg-white border border-slate-200 hover:border-clinical-400 text-slate-600 transition">60 min</button>
                    </div>
                </div>
            </div>

            <!-- Motivo Obligatorio de Reprogramación -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Motivo Clínico o Administrativo del Cambio <span class="text-rose-500">*</span>
                </label>
                <textarea name="reschedule_reason" rows="3" required placeholder="Explique la justificación de la modificación de fecha u horario..." class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 rounded-xl bg-slate-50/70 focus:bg-white focus:ring-2 focus:ring-clinical-500 focus:outline-none transition leading-relaxed">{{ old('reschedule_reason', $appointment->reschedule_reason) }}</textarea>
            </div>

            <!-- Botones -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <a href="{{ route('appointments.show', $appointment) }}" class="px-5 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                    Cancelar
                </a>
                <button type="submit" class="inline-flex items-center px-6 py-2.5 text-xs sm:text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-xl shadow-md shadow-blue-600/25 transition active:scale-95">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Confirmar y Reprogramar Cita
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentReschedDuration = 30;

    function setReschedDuration(mins) {
        currentReschedDuration = mins;
        document.querySelectorAll('.resched-dur-btn').forEach(btn => {
            btn.classList.remove('active-resched-dur', 'bg-clinical-50', 'border-clinical-300', 'text-clinical-700');
            btn.classList.add('bg-white', 'border-slate-200', 'text-slate-600');
        });
        event.target.classList.add('active-resched-dur', 'bg-clinical-50', 'border-clinical-300', 'text-clinical-700');
        calcReschedEnd();
    }

    function calcReschedEnd() {
        const startVal = document.getElementById('resched_start_time').value;
        if (!startVal) return;
        const [h, m] = startVal.split(':').map(Number);
        const d = new Date();
        d.setHours(h, m, 0, 0);

        const newD = new Date(d.getTime() + currentReschedDuration * 60000);
        const eh = String(newD.getHours()).padStart(2, '0');
        const em = String(newD.getMinutes()).padStart(2, '0');
        document.getElementById('resched_end_time').value = `${eh}:${em}`;
    }
</script>
@endsection
