# Documentación Técnica: Rama `feature/ui-ux-design`

**Módulo:** Sistema de Diseño Clínico e Interfaz de Usuario UI/UX Hospitalaria (HIS)  
**Rama:** `feature/ui-ux-design`  
**Fecha de Implementación:** Septiembre 2026  
**Tecnologías:** Vanilla CSS, Tailwind CSS Extended Config, Google Fonts (Outfit & Inter), FullCalendar v6, Laravel 12 Blade, JavaScript (ES6+)  

---

## 1. Resumen Ejecutivo del Rediseño UI/UX

La rama `feature/ui-ux-design` eleva integralmente la experiencia visual y funcional del módulo de control de citas médicas a estándares modernos de **HealthTech y Sistemas Hospitalarios Contemporáneos**. 

Se reemplazaron interfaces genéricas por un **Sistema de Diseño Clínico Cohesivo**, priorizando la legibilidad médica, la retroalimentación inmediata al usuario, la mitigación de errores operativos de doble reserva y la compatibilidad con dispositivos de escritorio, tablets y comprobantes físicos impresos.

---

## 2. Tokens del Sistema de Diseño Clínico

### 2.1. Tipografía Dual (Google Fonts)
- **`Outfit` (Display & Encabezados):** Utilizada en títulos principales (`h1`, `h2`, `h3`), tarjetas de KPIs, códigos alfanuméricos de citas y números destacados. Proporciona modernidad tecnológica y alta legibilidad a distancia.
- **`Inter` (Cuerpo y Formularios):** Fuente base para textos descriptivos, etiquetas de formulario, tablas y modales, garantizando lectura descansada y profesional.

### 2.2. Paleta Cromática HealthTech
| Categoría | Color Token | Muestra Hex | Propósito en la UI |
| :--- | :--- | :---: | :--- |
| **Primario Clínico** | `clinical-600` | `#0369a1` | Botones de acción principal, enlaces activos, acentos de calendario. |
| **Turquesa Quirúrgico** | `teal-500` | `#14b8a6` | Gradientes de logotipo, iconos de especialidades e identificación médica. |
| **Azul Profundo** | `slate-900` | `#0f172a` | Encabezados de alto contraste y textos principales. |
| **Verde Confirmación** | `emerald-500`| `#10b981` | Estado "Confirmada", indicador de servidor online, toasts de éxito. |
| **Ámbar Precaución** | `amber-500` | `#f59e0b` | Estado "Pendiente", avisos de triaje y notas de auditoría. |
| **Azul Reprogramación**| `blue-500` | `#3b82f6` | Estado "Reprogramada", acciones de ajuste de horario. |
| **Púrpura Atención** | `purple-500` | `#8b5cf6` | Estado "Atendida" (consulta médica finalizada). |
| **Rojo Alerta** | `rose-500` | `#ef4444` | Estado "Cancelada", detección de colisión de citas (HTTP 422). |

### 2.3. Efectos Visuales y Glassmorphism
- **`glass-card`:** Tarjetas con fondo translúcido blanco (`rgba(255, 255, 255, 0.88)`), filtro de desenfoque de fondo (`backdrop-filter: blur(12px)`) y borde sutil (`slate-200/80`).
- **Modales en Frosted Glass:** Fondos oscuros con difuminado suave (`bg-slate-900/60 backdrop-blur-md`) que aíslan visualmente la ventana modal del contexto subyacente.

---

## 3. Catálogo de Componentes y Vistas Desarrolladas

### 3.1. Barra de Navegación y Cabecera Hospitalaria (`layouts/app.blade.php`)
- **Logotipo Hospitalario con Resplandor:** Icono tridimensional con gradiente y efecto hover.
- **Indicador de Servidor Operativo:** Baliza luminosa pulsante en verde esmeralda que certifica la disponibilidad de la API REST en tiempo real.
- **Reloj Digital Clínico:** Cronómetro interactivo que actualiza fecha en español y hora con segundos (`updateHospitalLiveClock()`).
- **Sistema Global de Toasts Notificadores (`window.showToast`):**
  - Contenedor flotante superior derecho (`#toast-container`).
  - Toasts contextuales (`success`, `error`, `warning`, `info`) con icono SVG dedicado y animación de entrada elástica.
  - Autocierre programado con transición de salida fluida.

### 3.2. Agenda Médica Interactiva (`appointments/calendar.blade.php`)
- **Barra de Herramientas y Filtro Rápido:**
  - Selector de médico especialista con sincronización inmediata del feed de eventos vía AJAX.
  - Botón "Refrescar" con animación de rotación (`animate-spin`) en el icono.
  - Leyenda de estados con micro-chips cromáticos y badges de colores estándar.
- **Modal de Creación con Selectores Inteligentes de Duración:**
  - Botones de duración rápida (**15 min**, **30 min**, **45 min**, **60 min**) que recalculan automáticamente el campo `modal_end_time` al seleccionar la hora de inicio.
  - Sincronización automática de especialidad y médico asignado.
  - Alerta de conflicto con animación de error si el servidor rechaza por doble reserva.
- **Modal de Detalle Clínico:**
  - Código alfanumérico en fuente monoespaciada con badge de estado.
  - Horario formateado, expediente del paciente, consultorio del médico y notas de triaje.
  - Botones de transición de estado directo (**Confirmar**, **Marcar Atendida**, **Cancelar**).

### 3.3. Panel de Métricas y Listado de Citas (`appointments/index.blade.php`)
- **Dashboard Superior de KPIs:**
  - 6 widgets analíticos: *Total Citas*, *Citas Hoy*, *Confirmadas*, *Pendientes*, *Atendidas*, *Canceladas*.
  - Indicadores numéricos en fuente display `Outfit` con fondo de color suave.
- **Tabla Hospitalaria con Avatares:**
  - Avatares circulares generados automáticamente a partir de las iniciales del paciente (ej: "ML").
  - Identificación clara del expediente clínico (`Exp: EXP-XXXX`).
  - Badge de especialidad médica en cada fila.
  - Menú de acciones rápidas para Ver, Reprogramar y Cancelar.

### 3.4. Ficha Clínica y Comprobante Imprimible (`appointments/show.blade.php`)
- **Stepper Visual de Ciclo de Vida:**
  - Barra de progreso cronológica de 3 etapas: `1. Agendada` $\rightarrow$ `2. Confirmada / Reprogramada` $\rightarrow$ `3. Atendida / Cancelada`.
  - Marcas de tiempo exactas del registro, reprogramación o cancelación.
- **Comprobante Físico de Atención (`@media print`):**
  - Vista optimizada para impresoras térmicas y de oficina mediante el botón **"Imprimir Comprobante"**.
  - Oculta automáticamente elementos de navegación, menús y botones (`.no-print`).
  - Incluye encabezado formal de admisión, simulación de código de barras para escaneo de recepción e instrucciones para el paciente (ej: presentarse 15 minutos antes).

### 3.5. Formularios Estructurados por Pasos (`create.blade.php` & `reschedule.blade.php`)
- **Agrupación en Pasos Numéricos:**
  - `1. Paciente Hospitalario` $\rightarrow$ `2. Especialidad` $\rightarrow$ `3. Médico Especialista` $\rightarrow$ `4. Horario & Duración Rápida` $\rightarrow$ `5. Motivo y Triaje`.
- **Comparativa de Horario Previo vs Propuesto:** En reprogramación, destaca en tarjeta ámbar el horario actual frente a los nuevos campos a modificar.

---

## 4. Archivos Modificados en la Rama

```
app/Http/Controllers/Web/
└── AppointmentWebController.php       # [MODIFICADO] Cálculo de métricas KPI (total, hoy, estados)
resources/views/
├── layouts/
│   └── app.blade.php                  # [MODIFICADO] Sistema de diseño, fuentes Outfit/Inter, reloj en vivo, Toasts
└── appointments/
    ├── calendar.blade.php             # [MODIFICADO] Agenda FullCalendar estilizada, modales glassmorphism, duración rápida
    ├── index.blade.php                # [MODIFICADO] Dashboard KPIs, avatares de iniciales y tabla clínica
    ├── show.blade.php                 # [MODIFICADO] Stepper de ciclo de vida y comprobante físico imprimible
    ├── create.blade.php               # [MODIFICADO] Formulario por pasos lógicos y cálculo dinámico de hora fin
    └── reschedule.blade.php           # [MODIFICADO] Comparativa visual horario actual vs nuevo y botones de duración
doc/
├── README.md                          # [MODIFICADO] Registro de la rama en el índice central
└── ui-ux-design/
    └── README.md                      # [NUEVO] Documentación exhaustiva del sistema de diseño UI/UX
```

---

## 5. Instrucciones de Verificación Manual de la UI

1. **Dashboard y Métricas:**
   - Navega a `http://localhost:8080/appointments`.
   - Verifica los 6 contadores de KPIs en la parte superior.
   - Observa los avatares generados por iniciales en la columna "Paciente".
2. **Agenda Interactiva y Modales:**
   - Navega a `http://localhost:8080/appointments/calendar`.
   - Haz clic en cualquier celda de horario: se abrirá el modal de agendamiento en glassmorphism con la hora precargada.
   - Haz clic en los botones de duración (+15m, +30m, +45m, +60m) y comprueba cómo el campo "Hora Fin" se recalcula al instante.
   - Presiona el botón "Refrescar" y observa la animación del icono y la notificación Toast en la esquina superior derecha.
3. **Ficha Clínica e Impresión:**
   - Abre el detalle de cualquier cita (`/appointments/{id}`).
   - Verifica el stepper de 3 pasos que indica el estado actual del ciclo de vida.
   - Haz clic en **"Imprimir Comprobante"**: se abrirá el diálogo de impresión con el ticket formateado en blanco y negro sin navbars ni botones.
