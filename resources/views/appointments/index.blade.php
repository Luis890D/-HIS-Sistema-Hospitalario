@extends('layouts.app')

@section('title', 'Control y Monitoreo de Citas Médicas')

@section('content')
<div class="space-y-6">
    <!-- Encabezado de Página -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center space-x-2.5">
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight font-display">Dashboard & Control de Citas Médicas</h1>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-clinical-50 text-clinical-700 border border-clinical-200">
                    Dashboard Clínico
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Gestión integral de agendamiento, monitoreo de estados, reprogramaciones y cancelaciones del hospital.</p>
        </div>
        <div class="mt-4 sm:mt-0 flex items-center space-x-3">
            <a href="{{ route('appointments.calendar') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs sm:text-sm font-semibold shadow-sm transition active:scale-95">
                <svg class="w-4 h-4 mr-2 text-clinical-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Ver en Calendario
            </a>
            <a href="{{ route('appointments.create') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl bg-gradient-to-r from-clinical-600 to-clinical-700 hover:from-clinical-700 hover:to-clinical-800 text-white text-xs sm:text-sm font-semibold shadow-md shadow-clinical-600/25 transition active:scale-95">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Agendar Nueva Cita
            </a>
        </div>
    </div>

    <!-- Widgets de KPIs / Métricas Clínicas -->
    @if(isset($stats))
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
        <!-- Total Citas -->
        <div class="glass-card p-4 rounded-2xl border border-slate-200/90 shadow-sm flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Citas</p>
                <p class="text-xl font-extrabold text-slate-900 font-display">{{ $stats['total'] }}</p>
            </div>
        </div>

        <!-- Citas Hoy -->
        <div class="glass-card p-4 rounded-2xl border border-clinical-200/80 shadow-sm bg-gradient-to-br from-clinical-50/40 to-white flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-clinical-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-clinical-600">Citas Hoy</p>
                <p class="text-xl font-extrabold text-clinical-900 font-display">{{ $stats['today'] }}</p>
            </div>
        </div>

        <!-- Confirmadas -->
        <div class="glass-card p-4 rounded-2xl border border-emerald-200/80 shadow-sm bg-gradient-to-br from-emerald-50/40 to-white flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Confirmadas</p>
                <p class="text-xl font-extrabold text-emerald-950 font-display">{{ $stats['confirmed'] }}</p>
            </div>
        </div>

        <!-- Pendientes -->
        <div class="glass-card p-4 rounded-2xl border border-amber-200/80 shadow-sm bg-gradient-to-br from-amber-50/40 to-white flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-amber-700">Pendientes</p>
                <p class="text-xl font-extrabold text-amber-950 font-display">{{ $stats['pending'] }}</p>
            </div>
        </div>

        <!-- Atendidas -->
        <div class="glass-card p-4 rounded-2xl border border-purple-200/80 shadow-sm bg-gradient-to-br from-purple-50/40 to-white flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-purple-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-purple-700">Atendidas</p>
                <p class="text-xl font-extrabold text-purple-950 font-display">{{ $stats['attended'] }}</p>
            </div>
        </div>

        <!-- Canceladas -->
        <div class="glass-card p-4 rounded-2xl border border-rose-200/80 shadow-sm bg-gradient-to-br from-rose-50/40 to-white flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-rose-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-rose-700">Canceladas</p>
                <p class="text-xl font-extrabold text-rose-950 font-display">{{ $stats['cancelled'] }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Filtros de Búsqueda -->
    <div class="glass-card rounded-2xl shadow-sm border border-slate-200/90 p-5">
        <form method="GET" action="{{ route('appointments.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Filtrar Fecha</label>
                <input type="date" name="date" value="{{ request('date') }}" class="w-full px-3.5 py-2 text-xs font-medium border border-slate-200 rounded-xl bg-slate-50/70 focus:bg-white focus:ring-2 focus:ring-clinical-500 focus:outline-none transition">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Médico Especialista</label>
                <select name="doctor_id" class="w-full px-3.5 py-2 text-xs font-medium border border-slate-200 rounded-xl bg-slate-50/70 focus:bg-white focus:ring-2 focus:ring-clinical-500 focus:outline-none transition">
                    <option value="">Todos los médicos</option>
                    @foreach($doctors as $doc)
                        <option value="{{ $doc->id }}" {{ request('doctor_id') == $doc->id ? 'selected' : '' }}>
                            {{ $doc->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Estado de la Cita</label>
                <select name="status" class="w-full px-3.5 py-2 text-xs font-medium border border-slate-200 rounded-xl bg-slate-50/70 focus:bg-white focus:ring-2 focus:ring-clinical-500 focus:outline-none transition">
                    <option value="">Todos los estados</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmada</option>
                    <option value="rescheduled" {{ request('status') == 'rescheduled' ? 'selected' : '' }}>Reprogramada</option>
                    <option value="attended" {{ request('status') == 'attended' ? 'selected' : '' }}>Atendida</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center px-4 py-2 text-xs font-bold text-white bg-slate-800 hover:bg-slate-900 rounded-xl transition shadow-sm active:scale-95">
                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Aplicar Filtro
                </button>
                <a href="{{ route('appointments.index') }}" class="px-3 py-2 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition" title="Limpiar filtros">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Tabla Estilizada de Citas Médicas -->
    <div class="glass-card rounded-3xl shadow-sm border border-slate-200/90 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-5">Código Cita</th>
                        <th class="py-3.5 px-5">Paciente</th>
                        <th class="py-3.5 px-5">Médico / Especialidad</th>
                        <th class="py-3.5 px-5">Fecha & Horario</th>
                        <th class="py-3.5 px-5">Estado Clínico</th>
                        <th class="py-3.5 px-5 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($appointments as $appointment)
                        <tr class="hover:bg-slate-50/80 transition-colors duration-150">
                            <!-- Código de Cita con Insignia -->
                            <td class="py-4 px-5 font-mono font-bold text-clinical-600 whitespace-nowrap">
                                <a href="{{ route('appointments.show', $appointment) }}" class="inline-flex items-center hover:underline group">
                                    <span class="w-2 h-2 rounded-full bg-clinical-500 mr-2 group-hover:scale-125 transition-transform"></span>
                                    {{ $appointment->appointment_code }}
                                </a>
                            </td>

                            <!-- Datos del Paciente con Avatar por Iniciales -->
                            <td class="py-4 px-5">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-clinical-100 text-clinical-700 font-bold flex items-center justify-center flex-shrink-0 text-xs border border-clinical-200/60">
                                        {{ substr($appointment->patient?->first_name ?? 'P', 0, 1) }}{{ substr($appointment->patient?->last_name ?? 'A', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $appointment->patient?->full_name }}</div>
                                        <div class="text-[11px] text-slate-400 font-mono">Exp: {{ $appointment->patient?->medical_record_number }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Médico y Especialidad -->
                            <td class="py-4 px-5">
                                <div class="font-semibold text-slate-900">{{ $appointment->doctor?->full_name }}</div>
                                <div class="inline-flex items-center text-[10px] font-semibold text-clinical-700 bg-clinical-50 px-2 py-0.5 rounded-md border border-clinical-200/50 mt-0.5">
                                    {{ $appointment->specialty?->name }}
                                </div>
                            </td>

                            <!-- Fecha y Rango de Horario -->
                            <td class="py-4 px-5 whitespace-nowrap">
                                <div class="font-medium text-slate-900 flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $appointment->appointment_date->format('d/m/Y') }}
                                </div>
                                <div class="text-[11px] text-slate-500 font-mono pl-5">
                                    {{ substr($appointment->start_time, 0, 5) }} - {{ substr($appointment->end_time, 0, 5) }}
                                </div>
                            </td>

                            <!-- Estado con Badge Cromático -->
                            <td class="py-4 px-5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold border shadow-sm {{ $appointment->status->badgeColor() }}">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-current"></span>
                                    {{ $appointment->status->label() }}
                                </span>
                            </td>

                            <!-- Acciones Rápidas -->
                            <td class="py-4 px-5 text-right whitespace-nowrap space-x-1.5">
                                <a href="{{ route('appointments.show', $appointment) }}" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold text-slate-700 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-lg transition" title="Consultar detalle">
                                    Ver
                                </a>

                                @if(!in_array($appointment->status->value, ['cancelled', 'attended']))
                                    <a href="{{ route('appointments.reschedule.form', $appointment) }}" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition" title="Reprogramar cita">
                                        Reprogramar
                                    </a>
                                    <button type="button" onclick="openCancelModal('{{ $appointment->id }}', '{{ $appointment->appointment_code }}')" class="inline-flex items-center px-2.5 py-1 text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg transition" title="Cancelar cita">
                                        Cancelar
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="text-sm font-medium text-slate-500">No se encontraron citas médicas registradas con los filtros actuales.</p>
                                    <a href="{{ route('appointments.create') }}" class="text-xs text-clinical-600 font-semibold hover:underline">
                                        Agendar una nueva cita ahora &rarr;
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($appointments->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $appointments->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal de Cancelación con Glassmorphism -->
<div id="cancelModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-md z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-7 shadow-2xl space-y-4 border border-slate-100">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-900 font-display">Cancelar Cita Médica</h3>
                <p class="text-xs text-slate-500">Se liberará el horario del médico en la agenda.</p>
            </div>
        </div>

        <p class="text-xs text-slate-600 leading-relaxed bg-slate-50 p-3 rounded-xl border border-slate-200/60" id="cancelModalText"></p>

        <form id="cancelForm" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Motivo Clínico de Cancelación <span class="text-rose-500">*</span>
                </label>
                <textarea name="cancellation_reason" required rows="3" class="w-full px-3.5 py-2 text-xs font-medium border border-slate-200 rounded-xl bg-slate-50/60 focus:bg-white focus:ring-2 focus:ring-rose-500 focus:outline-none transition" placeholder="Ej: Solicitud expresa del paciente por viaje imprevisto..."></textarea>
            </div>
            <div class="flex justify-end space-x-2.5 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeCancelModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                    Cerrar
                </button>
                <button type="submit" class="inline-flex items-center px-4 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-md shadow-rose-600/25 transition active:scale-95">
                    Confirmar Cancelación
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCancelModal(id, code) {
        const form = document.getElementById('cancelForm');
        form.action = `/appointments/${id}/cancel`;
        document.getElementById('cancelModalText').innerHTML = `¿Está seguro de cancelar la cita médica <strong class="text-slate-900 font-mono">${code}</strong>? Esta acción pasará la cita al estado terminal inmutable <strong>Cancelada</strong>.`;
        document.getElementById('cancelModal').classList.remove('hidden');
    }
    function closeCancelModal() {
        document.getElementById('cancelModal').classList.add('hidden');
    }
</script>
@endsection
