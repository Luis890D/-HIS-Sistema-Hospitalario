@extends('layouts.app')

@section('title', 'Calendario Interactivo de Citas')

@section('content')
<!-- Dependencia de FullCalendar v6 CDN -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

<div class="space-y-6">
    <!-- Encabezado y Filtros Rápidos -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <div class="sm:flex sm:items-center sm:justify-between pb-4 border-b border-slate-100">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight flex items-center">
                    <span class="w-3 h-3 rounded-full bg-clinical-500 mr-2.5 animate-pulse"></span>
                    Agenda Médica Interactiva
                </h1>
                <p class="text-xs text-slate-500 mt-1">Haz clic en cualquier celda para agendar, arrastra citas para reprogramar o haz clic en un evento para ver su detalle clínico.</p>
            </div>
            <div class="mt-4 sm:mt-0 flex items-center space-x-3">
                <button type="button" onclick="openCreateModalFromButton()" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-clinical-600 hover:bg-clinical-700 rounded-xl shadow-sm transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Agendar Cita
                </button>
            </div>
        </div>

        <!-- Filtro por Médico y Leyenda de Estados -->
        <div class="pt-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center space-x-3">
                <label class="text-xs font-semibold text-slate-700 uppercase tracking-wider whitespace-nowrap">Filtrar por Médico:</label>
                <select id="doctorFilter" class="px-3 py-1.5 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-clinical-500 focus:outline-none">
                    <option value="">Todos los médicos especialistas</option>
                    @foreach($doctors as $doc)
                        <option value="{{ $doc->id }}">{{ $doc->full_name }} ({{ $doc->specialty?->name }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Leyenda de Estados -->
            <div class="flex flex-wrap items-center gap-3 text-xs">
                <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-amber-500 mr-1.5"></span> Pendiente</span>
                <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-emerald-500 mr-1.5"></span> Confirmada</span>
                <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-blue-500 mr-1.5"></span> Reprogramada</span>
                <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-purple-500 mr-1.5"></span> Atendida</span>
                <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-rose-500 mr-1.5"></span> Cancelada</span>
            </div>
        </div>
    </div>

    <!-- Contenedor del Calendario -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <div id="calendar" class="min-h-[700px]"></div>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: AGENDAR CITA (AL HACER CLIC EN HORARIO)                 -->
<!-- ============================================================== -->
<div id="createModal" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Agendar Cita Médica</h3>
                <p class="text-xs text-slate-500">Seleccionaste horario en el calendario interactivo</p>
            </div>
            <button type="button" onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>

        <!-- Alerta de Errores / Conflicto de Doble Reserva -->
        <div id="createErrorAlert" class="hidden p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium"></div>

        <form id="createForm" onsubmit="submitCreateAppointment(event)" class="space-y-4">
            <!-- Paciente -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Paciente *</label>
                <select id="modal_patient_id" required class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-clinical-500 focus:outline-none">
                    <option value="">Selecciona paciente...</option>
                    @foreach($patients as $p)
                        <option value="{{ $p->id }}">{{ $p->full_name }} (Exp: {{ $p->medical_record_number }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Especialidad y Médico -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Especialidad *</label>
                    <select id="modal_specialty_id" required class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-clinical-500 focus:outline-none">
                        <option value="">Especialidad...</option>
                        @foreach($specialties as $sp)
                            <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Médico *</label>
                    <select id="modal_doctor_id" required class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-clinical-500 focus:outline-none">
                        <option value="">Médico asignado...</option>
                        @foreach($doctors as $doc)
                            <option value="{{ $doc->id }}">{{ $doc->full_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Fecha y Horario -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Fecha *</label>
                    <input type="date" id="modal_appointment_date" required class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-clinical-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Hora Inicio *</label>
                    <input type="time" id="modal_start_time" required class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-clinical-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Hora Fin *</label>
                    <input type="time" id="modal_end_time" required class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-clinical-500 focus:outline-none">
                </div>
            </div>

            <!-- Motivo -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase mb-1">Motivo de Consulta *</label>
                <textarea id="modal_reason" required rows="2" placeholder="Motivo o sintomatología..." class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-clinical-500 focus:outline-none"></textarea>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded-lg transition">Cancelar</button>
                <button type="submit" id="btnSubmitCreate" class="px-5 py-2 text-xs font-semibold text-white bg-clinical-600 hover:bg-clinical-700 rounded-lg shadow-sm transition">
                    Confirmar y Agendar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: DETALLE DE LA CITA AL HACER CLIC EN EVENTO              -->
<!-- ============================================================== -->
<div id="detailModal" class="hidden fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
        <div class="flex justify-between items-start pb-3 border-b border-slate-100">
            <div>
                <div class="flex items-center space-x-2">
                    <span id="detailCode" class="font-mono font-bold text-lg text-slate-900"></span>
                    <span id="detailStatusBadge" class="px-2.5 py-0.5 rounded-full text-xs font-semibold border"></span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Detalle clínico de la cita médica</p>
            </div>
            <button type="button" onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
        </div>

        <div class="space-y-2.5 text-xs">
            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100 space-y-1">
                <div><span class="text-slate-500 font-semibold">Paciente:</span> <span id="detailPatient" class="text-slate-900 font-bold"></span></div>
                <div><span class="text-slate-500 font-semibold">Médico:</span> <span id="detailDoctor" class="text-slate-900"></span></div>
                <div><span class="text-slate-500 font-semibold">Especialidad:</span> <span id="detailSpecialty" class="text-slate-900"></span></div>
                <div><span class="text-slate-500 font-semibold">Horario:</span> <span id="detailSchedule" class="text-clinical-700 font-mono font-bold"></span></div>
            </div>
            <div>
                <span class="text-slate-500 font-semibold block mb-0.5">Motivo de consulta:</span>
                <p id="detailReason" class="text-slate-700 bg-slate-50 p-2.5 rounded-lg border border-slate-100"></p>
            </div>
            <div id="detailNotesContainer" class="hidden">
                <span class="text-slate-500 font-semibold block mb-0.5">Notas clínicas / Trazabilidad:</span>
                <p id="detailNotes" class="text-slate-700 bg-slate-50 p-2.5 rounded-lg border border-slate-100 font-mono text-[11px]"></p>
            </div>
        </div>

        <!-- Acciones Rápidas de Estados -->
        <div class="pt-3 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2">
            <a id="detailFullLink" href="#" class="text-xs text-clinical-600 hover:underline font-semibold flex items-center">
                Ver expediente completo &rarr;
            </a>
            <div class="flex items-center space-x-2" id="detailActionButtons">
                <!-- Se llenan dinámicamente según el estado actual -->
            </div>
        </div>
    </div>
</div>

<!-- Notificación Toast Flotante -->
<div id="toastNotification" class="hidden fixed bottom-5 right-5 z-50 px-4 py-3 rounded-xl shadow-xl text-xs font-semibold transition-all"></div>

<!-- Lógica de Integración con FullCalendar -->
<script>
    let calendar;
    let selectedAppointmentId = null;

    document.addEventListener('DOMContentLoaded', function() {
        const calendarEl = document.getElementById('calendar');

        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'timeGridWeek',
            locale: 'es',
            firstDay: 1, // Lunes
            slotMinTime: '07:00:00',
            slotMaxTime: '20:00:00',
            slotDuration: '00:15:00',
            slotLabelInterval: '00:30:00',
            allDaySlot: false,
            navLinks: true,
            selectable: true,
            selectMirror: true,
            editable: true,
            nowIndicator: true,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            buttonText: {
                today: 'Hoy',
                month: 'Mes',
                week: 'Semana',
                day: 'Día',
                list: 'Lista'
            },

            // 1. CARGA DE EVENTOS DESDE LA API REST
            events: function(fetchInfo, successCallback, failureCallback) {
                const doctorId = document.getElementById('doctorFilter').value;
                let url = `/api/v1/calendar/events?start=${fetchInfo.startStr}&end=${fetchInfo.endStr}`;
                if (doctorId) {
                    url += `&doctor_id=${doctorId}`;
                }

                fetch(url)
                    .then(res => res.json())
                    .then(data => successCallback(data))
                    .catch(err => {
                        console.error('Error cargando citas:', err);
                        failureCallback(err);
                    });
            },

            // 2. CREAR CITA AL HACER CLIC EN EL CALENDARIO (dateClick / select)
            select: function(info) {
                openCreateModalWithSlot(info.startStr, info.endStr);
            },

            // 3. MOSTRAR DETALLE AL HACER CLIC EN UN EVENTO
            eventClick: function(info) {
                openDetailModal(info.event);
            },

            // 4. REPROGRAMAR CON DRAG & DROP
            eventDrop: function(info) {
                handleEventReschedule(info);
            },

            // 5. REPROGRAMAR CON RESIZE (CAMBIO DE DURACIÓN)
            eventResize: function(info) {
                handleEventReschedule(info);
            }
        });

        calendar.render();

        // Refrescar eventos al cambiar filtro de médico
        document.getElementById('doctorFilter').addEventListener('change', function() {
            calendar.refetchEvents();
        });
    });

    // -------------------------------------------------------------
    // FUNCIONES PARA CREAR CITA
    // -------------------------------------------------------------
    function openCreateModalWithSlot(startStr, endStr) {
        document.getElementById('createErrorAlert').classList.add('hidden');
        document.getElementById('createForm').reset();

        const startDate = new Date(startStr);
        const yyyy = startDate.getFullYear();
        const mm = String(startDate.getMonth() + 1).padStart(2, '0');
        const dd = String(startDate.getDate()).padStart(2, '0');
        const hhStart = String(startDate.getHours()).padStart(2, '0');
        const minStart = String(startDate.getMinutes()).padStart(2, '0');

        document.getElementById('modal_appointment_date').value = `${yyyy}-${mm}-${dd}`;
        document.getElementById('modal_start_time').value = `${hhStart}:${minStart}`;

        if (endStr) {
            const endDate = new Date(endStr);
            const hhEnd = String(endDate.getHours()).padStart(2, '0');
            const minEnd = String(endDate.getMinutes()).padStart(2, '0');
            document.getElementById('modal_end_time').value = `${hhEnd}:${minEnd}`;
        } else {
            // Default 30 min
            const endD = new Date(startDate.getTime() + 30 * 60000);
            document.getElementById('modal_end_time').value = `${String(endD.getHours()).padStart(2, '0')}:${String(endD.getMinutes()).padStart(2, '0')}`;
        }

        // Si hay médico seleccionado en el filtro, preseleccionarlo
        const filterDoc = document.getElementById('doctorFilter').value;
        if (filterDoc) {
            document.getElementById('modal_doctor_id').value = filterDoc;
        }

        document.getElementById('createModal').classList.remove('hidden');
    }

    function openCreateModalFromButton() {
        const today = new Date().toISOString().split('T')[0];
        openCreateModalWithSlot(`${today}T09:00:00`, `${today}T09:30:00`);
    }

    function closeCreateModal() {
        document.getElementById('createModal').classList.add('hidden');
    }

    async function submitCreateAppointment(e) {
        e.preventDefault();
        const errorAlert = document.getElementById('createErrorAlert');
        errorAlert.classList.add('hidden');

        const payload = {
            patient_id: document.getElementById('modal_patient_id').value,
            doctor_id: document.getElementById('modal_doctor_id').value,
            specialty_id: document.getElementById('modal_specialty_id').value,
            appointment_date: document.getElementById('modal_appointment_date').value,
            start_time: document.getElementById('modal_start_time').value,
            end_time: document.getElementById('modal_end_time').value,
            reason: document.getElementById('modal_reason').value,
        };

        try {
            const res = await fetch('/api/v1/appointments', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();

            if (!res.ok) {
                // Mostrar error de validación o conflicto de doble reserva
                let errorMsg = data.message || 'Error al agendar la cita.';
                if (data.errors) {
                    errorMsg = Object.values(data.errors).flat().join('<br>');
                }
                errorAlert.innerHTML = errorMsg;
                errorAlert.classList.remove('hidden');
                return;
            }

            closeCreateModal();
            calendar.refetchEvents();
            showToast(`Cita médica ${data.code} agendada con éxito.`, 'success');
        } catch (err) {
            errorAlert.innerText = 'Error de conexión con el servidor.';
            errorAlert.classList.remove('hidden');
        }
    }

    // -------------------------------------------------------------
    // FUNCIONES PARA MOSTRAR DETALLE
    // -------------------------------------------------------------
    function openDetailModal(event) {
        selectedAppointmentId = event.id;
        const props = event.extendedProps;

        document.getElementById('detailCode').innerText = props.code;
        const badge = document.getElementById('detailStatusBadge');
        badge.innerText = props.status_label;
        badge.className = `px-2.5 py-0.5 rounded-full text-xs font-semibold border ${props.badge_color}`;

        document.getElementById('detailPatient').innerText = props.patient_name;
        document.getElementById('detailDoctor').innerText = props.doctor_name;
        document.getElementById('detailSpecialty').innerText = props.specialty_name;
        document.getElementById('detailSchedule').innerText = `${props.date} de ${props.start_time} a ${props.end_time}`;
        document.getElementById('detailReason').innerText = props.reason;

        const notesContainer = document.getElementById('detailNotesContainer');
        if (props.clinical_notes) {
            document.getElementById('detailNotes').innerText = props.clinical_notes;
            notesContainer.classList.remove('hidden');
        } else {
            notesContainer.classList.add('hidden');
        }

        document.getElementById('detailFullLink').href = `/appointments/${event.id}`;

        // Renderizar botones de acción según el estado
        const actionContainer = document.getElementById('detailActionButtons');
        actionContainer.innerHTML = '';

        if (props.status === 'pending') {
            actionContainer.innerHTML += `
                <button type="button" onclick="changeStatusFromCalendar(${event.id}, 'confirmed')" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold">Confirmar</button>
                <button type="button" onclick="cancelFromCalendar(${event.id})" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-semibold">Cancelar</button>
            `;
        } else if (props.status === 'confirmed' || props.status === 'rescheduled') {
            actionContainer.innerHTML += `
                <button type="button" onclick="changeStatusFromCalendar(${event.id}, 'attended')" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-semibold">Marcar Atendida</button>
                <button type="button" onclick="cancelFromCalendar(${event.id})" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-semibold">Cancelar</button>
            `;
        }

        document.getElementById('detailModal').classList.remove('hidden');
    }

    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
    }

    // -------------------------------------------------------------
    // REPROGRAMACIÓN CON DRAG & DROP Y VERIFICACIÓN DE CONFLICTOS
    // -------------------------------------------------------------
    async function handleEventReschedule(info) {
        const event = info.event;
        const newStart = event.start;
        const newEnd = event.end || new Date(newStart.getTime() + 30 * 60000);

        const yyyy = newStart.getFullYear();
        const mm = String(newStart.getMonth() + 1).padStart(2, '0');
        const dd = String(newStart.getDate()).padStart(2, '0');
        const newDate = `${yyyy}-${mm}-${dd}`;
        const newStartTime = `${String(newStart.getHours()).padStart(2, '0')}:${String(newStart.getMinutes()).padStart(2, '0')}`;
        const newEndTime = `${String(newEnd.getHours()).padStart(2, '0')}:${String(newEnd.getMinutes()).padStart(2, '0')}`;

        const reason = prompt(
            `Reprogramar cita ${event.extendedProps.code} al ${newDate} de ${newStartTime} a ${newEndTime}.\nIngresa el motivo de la reprogramación:`,
            'Reprogramación solicitada mediante arrastre en calendario'
        );

        if (!reason) {
            info.revert();
            return;
        }

        const payload = {
            appointment_date: newDate,
            start_time: newStartTime,
            end_time: newEndTime,
            reschedule_reason: reason
        };

        try {
            const res = await fetch(`/api/v1/appointments/${event.id}/reschedule`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();

            if (!res.ok) {
                // Revertir arrastre de inmediato si hay conflicto de horario
                info.revert();
                const errorMsg = data.errors ? Object.values(data.errors).flat().join('\n') : (data.message || 'Error al reprogramar.');
                alert(`⚠️ No se pudo reprogramar:\n\n${errorMsg}`);
                return;
            }

            calendar.refetchEvents();
            showToast(`Cita ${event.extendedProps.code} reprogramada exitosamente.`, 'success');
        } catch (err) {
            info.revert();
            alert('Error de conexión con el servidor al reprogramar.');
        }
    }

    // -------------------------------------------------------------
    // ACCIONES RÁPIDAS (CONFIRMAR, ATENDER, CANCELAR)
    // -------------------------------------------------------------
    async function changeStatusFromCalendar(id, status) {
        try {
            const res = await fetch(`/api/v1/appointments/${id}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: status })
            });

            const data = await res.json();
            if (!res.ok) {
                alert(data.message || 'Error al cambiar estado.');
                return;
            }

            closeDetailModal();
            calendar.refetchEvents();
            showToast(data.message, 'success');
        } catch (err) {
            alert('Error de conexión al actualizar estado.');
        }
    }

    async function cancelFromCalendar(id) {
        const reason = prompt('Por favor indique la justificación para cancelar la cita médica:');
        if (!reason) return;

        try {
            const res = await fetch(`/api/v1/appointments/${id}/cancel`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ cancellation_reason: reason })
            });

            const data = await res.json();
            if (!res.ok) {
                alert(data.message || 'Error al cancelar la cita.');
                return;
            }

            closeDetailModal();
            calendar.refetchEvents();
            showToast('Cita cancelada correctamente.', 'info');
        } catch (err) {
            alert('Error de conexión al cancelar.');
        }
    }

    function showToast(msg, type = 'success') {
        const toast = document.getElementById('toastNotification');
        toast.innerText = msg;
        toast.className = `fixed bottom-5 right-5 z-50 px-4 py-3 rounded-xl shadow-xl text-xs font-semibold text-white ${type === 'success' ? 'bg-emerald-600' : 'bg-slate-800'}`;
        toast.classList.remove('hidden');
        setTimeout(() => toast.classList.add('hidden'), 4000);
    }
</script>

<style>
    /* Ajustes sutiles a la estética de FullCalendar para alinearse al diseño hospitalario */
    .fc {
        --fc-border-color: #f1f5f9;
        --fc-today-bg-color: #f0fdf4;
        font-family: inherit;
    }
    .fc-header-toolbar {
        margin-bottom: 1.5rem !important;
    }
    .fc-button-primary {
        background-color: #0284c7 !important;
        border-color: #0284c7 !important;
        border-radius: 0.5rem !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        padding: 0.4rem 0.8rem !important;
    }
    .fc-button-primary:hover {
        background-color: #0369a1 !important;
        border-color: #0369a1 !important;
    }
    .fc-button-active {
        background-color: #075985 !important;
        border-color: #075985 !important;
    }
    .fc-event {
        border-radius: 0.4rem !important;
        padding: 2px 4px !important;
        font-size: 0.75rem !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        cursor: pointer;
    }
    .fc-col-header-cell-cushion {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #475569;
        padding: 8px 0 !important;
    }
</style>
@endsection
