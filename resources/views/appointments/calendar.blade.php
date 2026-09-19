@extends('layouts.app')

@section('title', 'Agenda Médica Interactiva')

@section('content')
<!-- Dependencia de FullCalendar v6 CDN -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

<div class="space-y-6">
    <!-- Encabezado Clínico y Filtros Rápidos -->
    <div class="glass-card rounded-3xl shadow-sm border border-slate-200/90 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-5 border-b border-slate-100">
            <div>
                <div class="flex items-center space-x-3">
                    <span class="relative flex h-3.5 w-3.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-clinical-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-clinical-500"></span>
                    </span>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight font-display">Agenda Médica Interactiva</h1>
                    <span class="hidden sm:inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                        FullCalendar v6 Live
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                    Visualización en tiempo real: haz clic en cualquier celda para agendar, arrastra citas para reprogramar o haz clic en un evento para consultar su detalle clínico.
                </p>
            </div>

            <!-- Botones de Acción Superior -->
            <div class="flex items-center space-x-3 flex-shrink-0">
                <button type="button" onclick="refreshCalendarData()" class="inline-flex items-center px-3 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition active:scale-95" title="Refrescar eventos">
                    <svg id="refreshIcon" class="w-3.5 h-3.5 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Refrescar
                </button>

                <button type="button" onclick="openCreateModalFromButton()" class="inline-flex items-center px-4 py-2 text-xs sm:text-sm font-semibold text-white bg-gradient-to-r from-clinical-600 to-clinical-700 hover:from-clinical-700 hover:to-clinical-800 rounded-xl shadow-md shadow-clinical-600/25 transition active:scale-95">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Agendar Cita
                </button>
            </div>
        </div>

        <!-- Barra de Filtros y Leyenda Cromática de Estados -->
        <div class="pt-4 flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <!-- Filtro por Médico -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3">
                <label for="doctorFilter" class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1 text-clinical-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Filtrar Médico:
                </label>
                <div class="relative w-full sm:w-72">
                    <select id="doctorFilter" class="w-full pl-3 pr-8 py-2 text-xs font-medium border border-slate-200 rounded-xl bg-slate-50/70 focus:bg-white focus:ring-2 focus:ring-clinical-500 focus:outline-none transition shadow-sm">
                        <option value="">Todos los médicos especialistas</option>
                        @foreach($doctors as $doc)
                            <option value="{{ $doc->id }}">{{ $doc->full_name }} ({{ $doc->specialty?->name }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Leyenda de Estados con Chips Estilizados -->
            <div class="flex flex-wrap items-center gap-2 text-xs">
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-amber-50 border border-amber-200/80 text-amber-800 font-medium">
                    <span class="w-2 h-2 rounded-full bg-amber-500 mr-1.5 animate-pulse"></span> Pendiente
                </span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-200/80 text-emerald-800 font-medium">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5"></span> Confirmada
                </span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-blue-50 border border-blue-200/80 text-blue-800 font-medium">
                    <span class="w-2 h-2 rounded-full bg-blue-500 mr-1.5"></span> Reprogramada
                </span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-purple-50 border border-purple-200/80 text-purple-800 font-medium">
                    <span class="w-2 h-2 rounded-full bg-purple-500 mr-1.5"></span> Atendida
                </span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-rose-50 border border-rose-200/80 text-rose-800 font-medium">
                    <span class="w-2 h-2 rounded-full bg-rose-500 mr-1.5"></span> Cancelada
                </span>
            </div>
        </div>
    </div>

    <!-- Contenedor Principal del Calendario -->
    <div class="glass-card rounded-3xl shadow-sm border border-slate-200/90 p-4 sm:p-6 overflow-hidden">
        <div id="calendar" class="min-h-[720px]"></div>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: AGENDAR CITA AL CLIC CON GLASSMORPHISM Y SMART DURATION -->
<!-- ============================================================== -->
<div id="createModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-md z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-7 shadow-2xl space-y-5 max-h-[92vh] overflow-y-auto border border-slate-100">
        <div class="flex justify-between items-center pb-3 border-b border-slate-100">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-clinical-50 text-clinical-600 flex items-center justify-center border border-clinical-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900 font-display">Agendar Cita Médica</h3>
                    <p class="text-xs text-slate-500">Horario preseleccionado desde el calendario interactivo</p>
                </div>
            </div>
            <button type="button" onclick="closeCreateModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-600 flex items-center justify-center text-lg transition">&times;</button>
        </div>

        <!-- Alerta Dinámica de Conflicto de Doble Reserva (Servidor HTTP 422) -->
        <div id="createErrorAlert" class="hidden p-3.5 rounded-2xl bg-rose-50 border border-rose-200/90 text-rose-900 text-xs font-medium leading-relaxed"></div>

        <form id="createForm" onsubmit="submitCreateAppointment(event)" class="space-y-4">
            <!-- Selección de Paciente -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Paciente <span class="text-rose-500">*</span></label>
                <select id="modal_patient_id" required class="w-full px-3.5 py-2.5 text-xs font-medium border border-slate-200 rounded-xl bg-slate-50/60 focus:bg-white focus:ring-2 focus:ring-clinical-500 focus:outline-none transition">
                    <option value="">Selecciona un paciente...</option>
                    @foreach($patients as $p)
                        <option value="{{ $p->id }}">{{ $p->full_name }} (Expediente: {{ $p->medical_record_number }} - DNI: {{ $p->identification_number }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Especialidad y Médico -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Especialidad <span class="text-rose-500">*</span></label>
                    <select id="modal_specialty_id" required class="w-full px-3.5 py-2.5 text-xs font-medium border border-slate-200 rounded-xl bg-slate-50/60 focus:bg-white focus:ring-2 focus:ring-clinical-500 focus:outline-none transition">
                        <option value="">Selecciona especialidad...</option>
                        @foreach($specialties as $sp)
                            <option value="{{ $sp->id }}">{{ $sp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Médico Asignado <span class="text-rose-500">*</span></label>
                    <select id="modal_doctor_id" required class="w-full px-3.5 py-2.5 text-xs font-medium border border-slate-200 rounded-xl bg-slate-50/60 focus:bg-white focus:ring-2 focus:ring-clinical-500 focus:outline-none transition">
                        <option value="">Médico especialista...</option>
                        @foreach($doctors as $doc)
                            <option value="{{ $doc->id }}" data-specialty="{{ $doc->specialty_id }}">{{ $doc->full_name }} ({{ $doc->specialty?->name }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Fecha y Horario con Selectores de Duración Rápida -->
            <div class="p-4 rounded-2xl bg-slate-50/90 border border-slate-200/80 space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Fecha *</label>
                        <input type="date" id="modal_appointment_date" required class="w-full px-3 py-2 text-xs font-medium border border-slate-200 rounded-lg bg-white focus:ring-2 focus:ring-clinical-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Hora Inicio *</label>
                        <input type="time" id="modal_start_time" onchange="autoCalculateEndTime()" required class="w-full px-3 py-2 text-xs font-medium border border-slate-200 rounded-lg bg-white focus:ring-2 focus:ring-clinical-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Hora Fin *</label>
                        <input type="time" id="modal_end_time" required class="w-full px-3 py-2 text-xs font-medium border border-slate-200 rounded-lg bg-white focus:ring-2 focus:ring-clinical-500 focus:outline-none">
                    </div>
                </div>

                <!-- Botones de Duración Rápida -->
                <div class="flex items-center justify-between pt-1">
                    <span class="text-[11px] font-semibold text-slate-500">Duración estimada:</span>
                    <div class="flex items-center space-x-1.5">
                        <button type="button" onclick="setSlotDuration(15)" class="duration-pill px-2.5 py-1 rounded-md text-[11px] font-semibold bg-white border border-slate-200 hover:border-clinical-400 hover:text-clinical-600 transition">15m</button>
                        <button type="button" onclick="setSlotDuration(30)" class="duration-pill active-pill px-2.5 py-1 rounded-md text-[11px] font-semibold bg-clinical-50 border border-clinical-300 text-clinical-700 transition">30m</button>
                        <button type="button" onclick="setSlotDuration(45)" class="duration-pill px-2.5 py-1 rounded-md text-[11px] font-semibold bg-white border border-slate-200 hover:border-clinical-400 hover:text-clinical-600 transition">45m</button>
                        <button type="button" onclick="setSlotDuration(60)" class="duration-pill px-2.5 py-1 rounded-md text-[11px] font-semibold bg-white border border-slate-200 hover:border-clinical-400 hover:text-clinical-600 transition">60m</button>
                    </div>
                </div>
            </div>

            <!-- Motivo de Consulta -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Motivo de Consulta <span class="text-rose-500">*</span></label>
                <textarea id="modal_reason" required rows="2" placeholder="Describa el motivo médico, síntoma o control rutinario del paciente..." class="w-full px-3.5 py-2 text-xs font-medium border border-slate-200 rounded-xl bg-slate-50/60 focus:bg-white focus:ring-2 focus:ring-clinical-500 focus:outline-none transition"></textarea>
            </div>

            <!-- Botones de Acción -->
            <div class="flex justify-end space-x-2.5 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                    Cancelar
                </button>
                <button type="submit" id="btnSubmitCreate" class="inline-flex items-center px-5 py-2 text-xs font-semibold text-white bg-gradient-to-r from-clinical-600 to-clinical-700 hover:from-clinical-700 hover:to-clinical-800 rounded-xl shadow-md shadow-clinical-600/25 transition active:scale-95">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Confirmar y Agendar Cita
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: DETALLE CLÍNICO Y ACCIONES RÁPIDAS DE ESTADO            -->
<!-- ============================================================== -->
<div id="detailModal" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-md z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-7 shadow-2xl space-y-5 border border-slate-100">
        <div class="flex justify-between items-start pb-3 border-b border-slate-100">
            <div>
                <div class="flex items-center space-x-2.5">
                    <span id="detailCode" class="font-mono font-extrabold text-lg text-slate-900 tracking-tight"></span>
                    <span id="detailStatusBadge" class="px-2.5 py-0.5 rounded-full text-xs font-semibold border shadow-sm"></span>
                </div>
                <p class="text-xs text-slate-500 mt-1">Ficha clínica rápida de la cita agendada</p>
            </div>
            <button type="button" onclick="closeDetailModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-600 flex items-center justify-center text-lg transition">&times;</button>
        </div>

        <!-- Tarjetas de Información Clínica -->
        <div class="space-y-3 text-xs">
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-semibold">Paciente:</span>
                    <span id="detailPatient" class="text-slate-900 font-bold text-sm"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-semibold">Médico Especialista:</span>
                    <span id="detailDoctor" class="text-slate-900 font-semibold"></span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500 font-semibold">Especialidad:</span>
                    <span id="detailSpecialty" class="text-clinical-700 font-semibold"></span>
                </div>
                <div class="flex items-center justify-between pt-1 border-t border-slate-200/60">
                    <span class="text-slate-500 font-semibold">Horario Asignado:</span>
                    <span id="detailSchedule" class="text-slate-900 font-mono font-bold"></span>
                </div>
            </div>

            <div>
                <span class="text-slate-500 font-bold uppercase tracking-wider block mb-1">Motivo de Consulta:</span>
                <p id="detailReason" class="text-slate-700 bg-slate-50/70 p-3 rounded-xl border border-slate-100 leading-relaxed"></p>
            </div>

            <div id="detailNotesContainer" class="hidden">
                <span class="text-slate-500 font-bold uppercase tracking-wider block mb-1">Trazabilidad Clínica / Notas:</span>
                <p id="detailNotes" class="text-slate-700 bg-amber-50/60 p-3 rounded-xl border border-amber-100 font-mono text-[11px] leading-relaxed"></p>
            </div>
        </div>

        <!-- Barra Inferior de Acciones Rápidas -->
        <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <a id="detailFullLink" href="#" class="inline-flex items-center text-xs text-clinical-600 hover:text-clinical-800 font-bold hover:underline transition">
                <span>Ver expediente completo</span>
                <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
            <div class="flex items-center space-x-2" id="detailActionButtons">
                <!-- Botones inyectados dinámicamente según el estado -->
            </div>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- SCRIPT: INTEGRACIÓN AVANZADA FULLCALENDAR V6                   -->
<!-- ============================================================== -->
<script>
    let calendar;
    let selectedAppointmentId = null;
    let selectedDurationMinutes = 30;

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

            // 1. CARGA DE EVENTOS DESDE LA API REST HOSPITALARIA
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
                        window.showToast('Error de conexión al cargar la agenda médica.', 'error');
                        failureCallback(err);
                    });
            },

            // 2. CREAR CITA AL HACER CLIC / SELECCIÓN DE FRANJA
            select: function(info) {
                openCreateModalWithSlot(info.startStr, info.endStr);
            },

            // 3. MOSTRAR DETALLE AL HACER CLIC EN CITA
            eventClick: function(info) {
                openDetailModal(info.event);
            },

            // 4. REPROGRAMAR CON DRAG & DROP
            eventDrop: function(info) {
                handleEventReschedule(info);
            },

            // 5. REPROGRAMAR CON RESIZE (DURACIÓN)
            eventResize: function(info) {
                handleEventReschedule(info);
            }
        });

        calendar.render();

        // Escuchador para filtrar por médico
        document.getElementById('doctorFilter').addEventListener('change', function() {
            calendar.refetchEvents();
        });

        // Autoselección de médico si cambia la especialidad en el modal
        document.getElementById('modal_specialty_id').addEventListener('change', function() {
            const specialtyId = this.value;
            const doctorSelect = document.getElementById('modal_doctor_id');
            const options = doctorSelect.querySelectorAll('option');

            let matchedFirst = false;
            options.forEach(opt => {
                if (!opt.value) return;
                const docSpec = opt.getAttribute('data-specialty');
                if (!specialtyId || docSpec === specialtyId) {
                    opt.hidden = false;
                    if (!matchedFirst && specialtyId) {
                        doctorSelect.value = opt.value;
                        matchedFirst = true;
                    }
                } else {
                    opt.hidden = true;
                }
            });
        });
    });

    // -------------------------------------------------------------
    // FUNCIONES DE CONTROL DE CALENDARIO Y DURACIÓN
    // -------------------------------------------------------------
    function refreshCalendarData() {
        const icon = document.getElementById('refreshIcon');
        icon.classList.add('animate-spin');
        calendar.refetchEvents();
        setTimeout(() => {
            icon.classList.remove('animate-spin');
            window.showToast('Agenda médica sincronizada en tiempo real.', 'info');
        }, 500);
    }

    function setSlotDuration(minutes) {
        selectedDurationMinutes = minutes;
        document.querySelectorAll('.duration-pill').forEach(btn => {
            btn.classList.remove('active-pill', 'bg-clinical-50', 'border-clinical-300', 'text-clinical-700');
            btn.classList.add('bg-white', 'border-slate-200', 'text-slate-600');
        });
        event.target.classList.add('active-pill', 'bg-clinical-50', 'border-clinical-300', 'text-clinical-700');
        autoCalculateEndTime();
    }

    function autoCalculateEndTime() {
        const startTime = document.getElementById('modal_start_time').value;
        if (!startTime) return;

        const [hours, minutes] = startTime.split(':').map(Number);
        const dateObj = new Date();
        dateObj.setHours(hours, minutes, 0, 0);

        const newDateObj = new Date(dateObj.getTime() + selectedDurationMinutes * 60000);
        const endH = String(newDateObj.getHours()).padStart(2, '0');
        const endM = String(newDateObj.getMinutes()).padStart(2, '0');
        document.getElementById('modal_end_time').value = `${endH}:${endM}`;
    }

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
            autoCalculateEndTime();
        }

        // Si hay médico filtrado, preseleccionarlo
        const filterDoc = document.getElementById('doctorFilter').value;
        if (filterDoc) {
            document.getElementById('modal_doctor_id').value = filterDoc;
            const selectedOpt = document.querySelector(`#modal_doctor_id option[value="${filterDoc}"]`);
            if (selectedOpt) {
                document.getElementById('modal_specialty_id').value = selectedOpt.getAttribute('data-specialty');
            }
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
                let errorMsg = data.message || 'Error al agendar la cita médica.';
                if (data.errors) {
                    errorMsg = Object.values(data.errors).flat().join('<br>');
                }
                errorAlert.innerHTML = `
                    <div class="flex items-start space-x-2">
                        <svg class="w-4 h-4 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>${errorMsg}</span>
                    </div>`;
                errorAlert.classList.remove('hidden');
                return;
            }

            closeCreateModal();
            calendar.refetchEvents();
            window.showToast(`Cita médica ${data.code || ''} agendada exitosamente.`, 'success');
        } catch (err) {
            errorAlert.innerHTML = 'Error de comunicación con el servidor.';
            errorAlert.classList.remove('hidden');
        }
    }

    // -------------------------------------------------------------
    // MODAL DETALLE CLÍNICO
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
        document.getElementById('detailSchedule').innerText = `${props.date} • ${props.start_time} - ${props.end_time}`;
        document.getElementById('detailReason').innerText = props.reason;

        const notesContainer = document.getElementById('detailNotesContainer');
        if (props.clinical_notes) {
            document.getElementById('detailNotes').innerText = props.clinical_notes;
            notesContainer.classList.remove('hidden');
        } else {
            notesContainer.classList.add('hidden');
        }

        document.getElementById('detailFullLink').href = `/appointments/${event.id}`;

        // Renderizado de botones de acción rápida
        const actionContainer = document.getElementById('detailActionButtons');
        actionContainer.innerHTML = '';

        if (props.status === 'pending') {
            actionContainer.innerHTML += `
                <button type="button" onclick="changeStatusFromCalendar(${event.id}, 'confirmed')" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-sm transition">Confirmar</button>
                <button type="button" onclick="cancelFromCalendar(${event.id})" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-semibold shadow-sm transition">Cancelar</button>
            `;
        } else if (props.status === 'confirmed' || props.status === 'rescheduled') {
            actionContainer.innerHTML += `
                <button type="button" onclick="changeStatusFromCalendar(${event.id}, 'attended')" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-semibold shadow-sm transition">Marcar Atendida</button>
                <button type="button" onclick="cancelFromCalendar(${event.id})" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-semibold shadow-sm transition">Cancelar</button>
            `;
        }

        document.getElementById('detailModal').classList.remove('hidden');
    }

    function closeDetailModal() {
        document.getElementById('detailModal').classList.add('hidden');
    }

    // -------------------------------------------------------------
    // REPROGRAMACIÓN CON DRAG & DROP (CON REVERSIÓN INMEDIATA)
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
                info.revert();
                const errorMsg = data.errors ? Object.values(data.errors).flat().join('\n') : (data.message || 'Error al reprogramar.');
                window.showToast(errorMsg, 'error', 6000);
                return;
            }

            calendar.refetchEvents();
            window.showToast(`Cita ${event.extendedProps.code} reprogramada con éxito.`, 'success');
        } catch (err) {
            info.revert();
            window.showToast('Error de comunicación con el servidor al reprogramar.', 'error');
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
                window.showToast(data.message || 'Error al cambiar estado.', 'error');
                return;
            }

            closeDetailModal();
            calendar.refetchEvents();
            window.showToast(data.message, 'success');
        } catch (err) {
            window.showToast('Error de comunicación con el servidor.', 'error');
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
                window.showToast(data.message || 'Error al cancelar la cita.', 'error');
                return;
            }

            closeDetailModal();
            calendar.refetchEvents();
            window.showToast('Cita cancelada y cupo liberado en el servidor.', 'warning');
        } catch (err) {
            window.showToast('Error de comunicación con el servidor.', 'error');
        }
    }
</script>

<style>
    /* Estilos Premium de FullCalendar adaptados al Sistema Hospitalario */
    .fc {
        --fc-border-color: #f1f5f9;
        --fc-today-bg-color: #f0f9ff;
        font-family: inherit;
    }
    .fc .fc-toolbar-title {
        font-family: 'Outfit', sans-serif !important;
        font-weight: 700 !important;
        font-size: 1.25rem !important;
        color: #0f172a !important;
    }
    .fc-header-toolbar {
        margin-bottom: 1.5rem !important;
    }
    .fc-button-primary {
        background-color: #0284c7 !important;
        border-color: #0284c7 !important;
        border-radius: 0.75rem !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        padding: 0.45rem 0.9rem !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
        transition: all 0.15s ease-in-out !important;
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
        border-radius: 0.6rem !important;
        padding: 3px 6px !important;
        font-size: 0.75rem !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
        cursor: pointer;
        transition: transform 0.15s ease;
    }
    .fc-event:hover {
        transform: scale(1.015);
    }
    .fc-col-header-cell-cushion {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #475569;
        padding: 10px 0 !important;
    }
    .fc-timegrid-slot {
        height: 28px !important;
    }
</style>
@endsection
