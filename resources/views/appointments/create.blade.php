@extends('layouts.app')

@section('title', 'Agendar Nueva Cita Médica')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Encabezado de Página -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2.5">
                <span class="w-3 h-3 rounded-full bg-clinical-500 animate-pulse"></span>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight font-display">Agendar Nueva Cita Médica</h1>
            </div>
            <p class="text-xs text-slate-500 mt-1">Ingresa la información clínica requerida para programar la consulta en la agenda hospitalaria.</p>
        </div>
        <a href="{{ route('appointments.index') }}" class="px-3.5 py-2 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 shadow-sm transition">
            &larr; Volver al listado
        </a>
    </div>

    <div class="glass-card rounded-3xl shadow-sm border border-slate-200/90 p-6 sm:p-8">
        <form method="POST" action="{{ route('appointments.store') }}" class="space-y-6">
            @csrf

            <!-- Sección 1: Selección de Paciente -->
            <div class="space-y-2">
                <div class="flex items-center space-x-2 text-xs font-bold uppercase tracking-wider text-slate-700">
                    <span class="w-5 h-5 rounded-full bg-clinical-100 text-clinical-700 flex items-center justify-center text-[10px]">1</span>
                    <span>Paciente Hospitalario <span class="text-rose-500">*</span></span>
                </div>
                <select name="patient_id" required class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 rounded-xl bg-slate-50/70 focus:bg-white focus:ring-2 focus:ring-clinical-500 focus:outline-none transition">
                    <option value="">Selecciona un paciente del directorio...</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                            {{ $patient->full_name }} (Expediente: {{ $patient->medical_record_number }} • DNI: {{ $patient->identification_number }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Sección 2: Especialidad y Médico Asignado -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <div class="flex items-center space-x-2 text-xs font-bold uppercase tracking-wider text-slate-700">
                        <span class="w-5 h-5 rounded-full bg-clinical-100 text-clinical-700 flex items-center justify-center text-[10px]">2</span>
                        <span>Especialidad Médica <span class="text-rose-500">*</span></span>
                    </div>
                    <select id="specialty_select" name="specialty_id" required class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 rounded-xl bg-slate-50/70 focus:bg-white focus:ring-2 focus:ring-clinical-500 focus:outline-none transition">
                        <option value="">Selecciona la especialidad...</option>
                        @foreach($specialties as $specialty)
                            <option value="{{ $specialty->id }}" {{ old('specialty_id') == $specialty->id ? 'selected' : '' }}>
                                {{ $specialty->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2">
                    <div class="flex items-center space-x-2 text-xs font-bold uppercase tracking-wider text-slate-700">
                        <span class="w-5 h-5 rounded-full bg-clinical-100 text-clinical-700 flex items-center justify-center text-[10px]">3</span>
                        <span>Médico Especialista <span class="text-rose-500">*</span></span>
                    </div>
                    <select id="doctor_select" name="doctor_id" required class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 rounded-xl bg-slate-50/70 focus:bg-white focus:ring-2 focus:ring-clinical-500 focus:outline-none transition">
                        <option value="">Selecciona el médico asignado...</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" data-specialty="{{ $doctor->specialty_id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                {{ $doctor->full_name }} ({{ $doctor->specialty?->name }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Sección 3: Fecha, Horario y Duración Rápida -->
            <div class="p-5 rounded-2xl bg-slate-50/90 border border-slate-200/80 space-y-3.5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Horario de Consulta</span>
                    <span class="text-[11px] text-slate-400 font-medium">Validación automática de traslapes en servidor</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Fecha <span class="text-rose-500">*</span></label>
                        <input type="date" name="appointment_date" min="{{ date('Y-m-d') }}" value="{{ old('appointment_date', date('Y-m-d')) }}" required class="w-full px-3.5 py-2 text-xs font-medium border border-slate-200 rounded-xl bg-white focus:ring-2 focus:ring-clinical-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Hora Inicio <span class="text-rose-500">*</span></label>
                        <input type="time" id="create_start_time" name="start_time" onchange="calculateFormEndTime()" value="{{ old('start_time', '09:00') }}" required class="w-full px-3.5 py-2 text-xs font-medium border border-slate-200 rounded-xl bg-white focus:ring-2 focus:ring-clinical-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Hora Fin Estimada <span class="text-rose-500">*</span></label>
                        <input type="time" id="create_end_time" name="end_time" value="{{ old('end_time', '09:30') }}" required class="w-full px-3.5 py-2 text-xs font-medium border border-slate-200 rounded-xl bg-white focus:ring-2 focus:ring-clinical-500 focus:outline-none">
                    </div>
                </div>

                <!-- Duración Rápida -->
                <div class="flex items-center justify-between pt-1">
                    <span class="text-[11px] font-semibold text-slate-500">Ajustar duración estimada:</span>
                    <div class="flex items-center space-x-1.5">
                        <button type="button" onclick="setCreateDuration(15)" class="create-dur-btn px-2.5 py-1 rounded-md text-[11px] font-semibold bg-white border border-slate-200 hover:border-clinical-400 text-slate-600 transition">15 min</button>
                        <button type="button" onclick="setCreateDuration(30)" class="create-dur-btn active-create-dur px-2.5 py-1 rounded-md text-[11px] font-semibold bg-clinical-50 border border-clinical-300 text-clinical-700 transition">30 min</button>
                        <button type="button" onclick="setCreateDuration(45)" class="create-dur-btn px-2.5 py-1 rounded-md text-[11px] font-semibold bg-white border border-slate-200 hover:border-clinical-400 text-slate-600 transition">45 min</button>
                        <button type="button" onclick="setCreateDuration(60)" class="create-dur-btn px-2.5 py-1 rounded-md text-[11px] font-semibold bg-white border border-slate-200 hover:border-clinical-400 text-slate-600 transition">60 min</button>
                    </div>
                </div>
            </div>

            <!-- Sección 4: Motivo de Consulta -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Motivo Clínico de la Consulta <span class="text-rose-500">*</span>
                </label>
                <textarea name="reason" rows="3" required placeholder="Describa la sintomatología actual o control rutinario del paciente..." class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 rounded-xl bg-slate-50/70 focus:bg-white focus:ring-2 focus:ring-clinical-500 focus:outline-none transition leading-relaxed">{{ old('reason') }}</textarea>
            </div>

            <!-- Sección 5: Notas Clínicas Opcionales -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                    Observaciones / Notas de Triaje (Opcional)
                </label>
                <textarea name="clinical_notes" rows="2" placeholder="Instrucciones para recepción o antecedentes clínicos inmediatos..." class="w-full px-4 py-2.5 text-xs font-medium border border-slate-200 rounded-xl bg-slate-50/70 focus:bg-white focus:ring-2 focus:ring-clinical-500 focus:outline-none transition leading-relaxed">{{ old('clinical_notes') }}</textarea>
            </div>

            <!-- Botones de Acción -->
            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <a href="{{ route('appointments.index') }}" class="px-5 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                    Cancelar
                </a>
                <button type="submit" class="inline-flex items-center px-6 py-2.5 text-xs sm:text-sm font-semibold text-white bg-gradient-to-r from-clinical-600 to-clinical-700 hover:from-clinical-700 hover:to-clinical-800 rounded-xl shadow-md shadow-clinical-600/25 transition active:scale-95">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Confirmar y Agendar Cita
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentCreateDuration = 30;

    function setCreateDuration(mins) {
        currentCreateDuration = mins;
        document.querySelectorAll('.create-dur-btn').forEach(btn => {
            btn.classList.remove('active-create-dur', 'bg-clinical-50', 'border-clinical-300', 'text-clinical-700');
            btn.classList.add('bg-white', 'border-slate-200', 'text-slate-600');
        });
        event.target.classList.add('active-create-dur', 'bg-clinical-50', 'border-clinical-300', 'text-clinical-700');
        calculateFormEndTime();
    }

    function calculateFormEndTime() {
        const startVal = document.getElementById('create_start_time').value;
        if (!startVal) return;
        const [h, m] = startVal.split(':').map(Number);
        const d = new Date();
        d.setHours(h, m, 0, 0);

        const newD = new Date(d.getTime() + currentCreateDuration * 60000);
        const eh = String(newD.getHours()).padStart(2, '0');
        const em = String(newD.getMinutes()).padStart(2, '0');
        document.getElementById('create_end_time').value = `${eh}:${em}`;
    }

    // Filtrar médicos según especialidad
    document.getElementById('specialty_select').addEventListener('change', function() {
        const specId = this.value;
        const docSelect = document.getElementById('doctor_select');
        const opts = docSelect.querySelectorAll('option');

        let matched = false;
        opts.forEach(opt => {
            if (!opt.value) return;
            const ds = opt.getAttribute('data-specialty');
            if (!specId || ds === specId) {
                opt.hidden = false;
                if (!matched && specId) {
                    docSelect.value = opt.value;
                    matched = true;
                }
            } else {
                opt.hidden = true;
            }
        });
    });
</script>
@endsection
