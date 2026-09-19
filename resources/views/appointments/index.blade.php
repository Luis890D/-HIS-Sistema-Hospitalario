@extends('layouts.app')

@section('title', 'Control de Citas Médicas')

@section('content')
<div class="space-y-6">
    <!-- Encabezado de página -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Control de Citas Médicas</h1>
            <p class="text-sm text-slate-500 mt-1">Gestión integral de agendamiento, reprogramación y cancelaciones del hospital.</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('appointments.create') }}" class="inline-flex items-center px-4 py-2.5 rounded-lg bg-clinical-600 hover:bg-clinical-700 text-white text-sm font-semibold shadow-sm transition">
                <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Agendar Nueva Cita
            </a>
        </div>
    </div>

    <!-- Filtros de búsqueda -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
        <form method="GET" action="{{ route('appointments.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Fecha</label>
                <input type="date" name="date" value="{{ request('date') }}" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-clinical-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Médico Especialista</label>
                <select name="doctor_id" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-clinical-500 focus:outline-none">
                    <option value="">Todos los médicos</option>
                    @foreach($doctors as $doc)
                        <option value="{{ $doc->id }}" {{ request('doctor_id') == $doc->id ? 'selected' : '' }}>
                            {{ $doc->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1">Estado</label>
                <select name="status" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-clinical-500 focus:outline-none">
                    <option value="">Todos los estados</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmada</option>
                    <option value="rescheduled" {{ request('status') == 'rescheduled' ? 'selected' : '' }}>Reprogramada</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="w-full px-4 py-2 text-sm font-semibold text-white bg-slate-800 rounded-lg hover:bg-slate-900 transition">
                    Filtrar
                </button>
                <a href="{{ route('appointments.index') }}" class="px-3 py-2 text-sm text-slate-600 bg-slate-100 rounded-lg hover:bg-slate-200 transition">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Tabla de Citas Médicas -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="py-3 px-4">Código</th>
                        <th class="py-3 px-4">Paciente</th>
                        <th class="py-3 px-4">Médico / Especialidad</th>
                        <th class="py-3 px-4">Fecha & Horario</th>
                        <th class="py-3 px-4">Estado</th>
                        <th class="py-3 px-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($appointments as $appointment)
                        <tr class="hover:bg-slate-50/75 transition">
                            <td class="py-3.5 px-4 font-mono font-medium text-clinical-600">
                                <a href="{{ route('appointments.show', $appointment) }}" class="hover:underline">
                                    {{ $appointment->appointment_code }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-medium text-slate-900">{{ $appointment->patient?->full_name }}</div>
                                <div class="text-xs text-slate-500 font-mono">{{ $appointment->patient?->medical_record_number }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-medium text-slate-900">{{ $appointment->doctor?->full_name }}</div>
                                <div class="text-xs text-slate-500">{{ $appointment->specialty?->name }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="text-slate-900">{{ $appointment->appointment_date->format('d/m/Y') }}</div>
                                <div class="text-xs text-slate-500 font-mono">{{ substr($appointment->start_time, 0, 5) }} - {{ substr($appointment->end_time, 0, 5) }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $appointment->status->badgeColor() }}">
                                    {{ $appointment->status->label() }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right space-x-2">
                                <a href="{{ route('appointments.show', $appointment) }}" class="text-xs font-medium text-slate-600 hover:text-slate-900 transition">
                                    Ver
                                </a>
                                @if(!in_array($appointment->status->value, ['cancelled', 'attended']))
                                    <a href="{{ route('appointments.reschedule.form', $appointment) }}" class="text-xs font-medium text-blue-600 hover:text-blue-800 transition">
                                        Reprogramar
                                    </a>
                                    <button type="button" onclick="openCancelModal('{{ $appointment->id }}', '{{ $appointment->appointment_code }}')" class="text-xs font-medium text-rose-600 hover:text-rose-800 transition">
                                        Cancelar
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                No se encontraron citas médicas registradas con los filtros actuales.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($appointments->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $appointments->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Simple para Cancelar Cita -->
<div id="cancelModal" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
        <h3 class="text-lg font-bold text-slate-900 mb-2">Cancelar Cita Médica</h3>
        <p class="text-sm text-slate-500 mb-4" id="cancelModalText">Por favor ingresa la justificación para cancelar la cita.</p>
        <form id="cancelForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Motivo de Cancelación</label>
                <textarea name="cancellation_reason" required rows="3" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-rose-500 focus:outline-none" placeholder="Ej: Paciente solicitó cancelación por viaje..."></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeCancelModal()" class="px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 rounded-lg transition">Cerrar</button>
                <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-lg shadow-sm transition">Confirmar Cancelación</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCancelModal(id, code) {
        const form = document.getElementById('cancelForm');
        form.action = `/appointments/${id}/cancel`;
        document.getElementById('cancelModalText').innerText = `¿Está seguro de cancelar la cita ${code}? Esta acción liberará el horario del médico.`;
        document.getElementById('cancelModal').classList.remove('hidden');
    }
    function closeCancelModal() {
        document.getElementById('cancelModal').classList.add('hidden');
    }
</script>
@endsection
