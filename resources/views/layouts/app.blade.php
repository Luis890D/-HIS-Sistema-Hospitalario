<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50/70">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Control de Citas Médicas') - HIS Hospitalario</title>

    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (CDN con configuración avanzada de diseño clínico) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        clinical: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0284c7',
                            600: '#0369a1',
                            700: '#075985',
                            800: '#0c4a6e',
                            900: '#082f49',
                            950: '#041c2c',
                        },
                        teal: {
                            50: '#f0fdfa',
                            500: '#14b8a6',
                            600: '#0d9488',
                            700: '#0f766e',
                        },
                    },
                    boxShadow: {
                        'glow-sm': '0 0 15px rgba(2, 132, 199, 0.15)',
                        'glow-md': '0 0 25px rgba(2, 132, 199, 0.25)',
                    }
                }
            }
        }
    </script>

    <style>
        /* Tipografía de display para encabezados y números */
        h1, h2, h3, .font-display {
            font-family: 'Outfit', sans-serif;
        }

        /* Glassmorphism clínico */
        .glass-card {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }

        /* Barra de desplazamiento suave */
        ::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Micro-animaciones para Toasts */
        @keyframes toastSlideIn {
            from {
                opacity: 0;
                transform: translateY(-20px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        .toast-animate-in {
            animation: toastSlideIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Reglas para Comprobante Imprimible de Cita Médica */
        @media print {
            header, footer, nav, .no-print, button, #toast-container {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                color: #000000 !important;
                font-size: 12pt !important;
            }
            .print-only {
                display: block !important;
            }
            .printable-voucher {
                box-shadow: none !important;
                border: 1px dashed #94a3b8 !important;
                padding: 1.5rem !important;
                max-width: 100% !important;
                margin: 0 !important;
            }
        }
    </style>
</head>
<body class="h-full flex flex-col font-sans text-slate-800 antialiased selection:bg-clinical-500 selection:text-white">
    <!-- Header / Navbar Principal Hospitalario -->
    <header class="bg-white/95 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-30 shadow-sm transition-all duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                
                <!-- Identidad Visual y Estado del Sistema -->
                <div class="flex items-center space-x-3.5">
                    <a href="{{ route('appointments.index') }}" class="group flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-clinical-600 via-clinical-500 to-teal-400 flex items-center justify-center text-white shadow-md shadow-clinical-500/25 group-hover:scale-105 group-hover:shadow-clinical-500/40 transition-all duration-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="text-xl font-bold tracking-tight text-slate-900 font-display group-hover:text-clinical-700 transition">HIS Hospitalario</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-clinical-50 text-clinical-700 border border-clinical-200/60 uppercase tracking-wider">
                                    V1 Clinical Suite
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-400 font-medium">Gestión Integral de Citas Médicas</p>
                        </div>
                    </a>

                    <!-- Indicador de Servidor Activo (Desktop) -->
                    <div class="hidden lg:flex items-center space-x-1.5 pl-4 border-l border-slate-200 text-xs text-slate-500">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span class="text-[11px] font-medium text-slate-500">Servidor Operativo</span>
                    </div>
                </div>

                <!-- Reloj Clínico en Vivo & Enlaces de Navegación -->
                <div class="flex items-center space-x-4 sm:space-x-6">
                    <!-- Reloj Digital Clínico -->
                    <div class="hidden md:flex items-center space-x-2 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200/80 text-xs font-mono text-slate-600">
                        <svg class="w-3.5 h-3.5 text-clinical-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span id="hospitalLiveClock">--:--:--</span>
                    </div>

                    <!-- Enlaces Principales -->
                    <nav class="flex items-center space-x-1 sm:space-x-2">
                        <a href="{{ route('appointments.index') }}" 
                           class="inline-flex items-center px-3 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-150 {{ request()->routeIs('appointments.index') ? 'bg-clinical-50 text-clinical-700 shadow-sm border border-clinical-200/60' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                            <svg class="w-4 h-4 mr-1.5 text-slate-400 {{ request()->routeIs('appointments.index') ? 'text-clinical-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Dashboard
                        </a>

                        <a href="{{ route('appointments.calendar') }}" 
                           class="inline-flex items-center px-3 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all duration-150 {{ request()->routeIs('appointments.calendar') ? 'bg-clinical-50 text-clinical-700 shadow-sm border border-clinical-200/60' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/70' }}">
                            <svg class="w-4 h-4 mr-1.5 text-slate-400 {{ request()->routeIs('appointments.calendar') ? 'text-clinical-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Calendario
                        </a>

                        <a href="{{ route('appointments.create') }}" 
                           class="inline-flex items-center px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold text-white bg-gradient-to-r from-clinical-600 to-clinical-700 hover:from-clinical-700 hover:to-clinical-800 shadow-md shadow-clinical-600/25 hover:shadow-clinical-600/35 transition-all duration-200 active:scale-95">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Agendar Cita
                        </a>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenedor Global de Toasts Flotantes -->
    <div id="toast-container" class="fixed top-5 right-5 z-50 flex flex-col space-y-3 pointer-events-none max-w-sm w-full px-4 sm:px-0"></div>

    <!-- Contenido Principal -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Alertas de Sesión Clásicas (Flash Messages) Mejoradas con Micro-animación -->
        @if (session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50/90 border border-emerald-200/80 text-emerald-900 flex items-center justify-between shadow-sm animate-fade-in">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-800">Operación Exitosa</h4>
                        <p class="text-sm font-medium text-emerald-900">{{ session('success') }}</p>
                    </div>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 p-1 rounded-lg transition">&times;</button>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-rose-50/90 border border-rose-200/80 text-rose-900 shadow-sm animate-fade-in">
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center flex-shrink-0 mt-0.5 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-rose-800">Validación de Citas Médicas</h4>
                        <ul class="mt-1 list-disc list-inside text-xs text-rose-800 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-rose-400 hover:text-rose-600 p-1 rounded-lg transition">&times;</button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer Moderno -->
    <footer class="bg-white border-t border-slate-200/80 py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <div class="flex items-center space-x-2">
                    <span class="font-semibold text-slate-700">HIS Hospitalario &copy; {{ date('Y') }}</span>
                    <span>&bull;</span>
                    <span>Arquitectura MVC Laravel 12 & Docker MySQL 8.0</span>
                </div>
                <div class="flex items-center space-x-4 text-slate-400">
                    <span class="inline-flex items-center">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                        API REST v1 Conectada
                    </span>
                    <span>&bull;</span>
                    <span>Detección de Conflictos Activa</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Script Global: Reloj Clínico y Sistema de Toasts -->
    <script>
        // 1. Reloj Clínico en Vivo
        function updateHospitalLiveClock() {
            const el = document.getElementById('hospitalLiveClock');
            if (!el) return;
            const now = new Date();
            const days = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
            const dayName = days[now.getDay()];
            const timeStr = now.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            el.innerText = `${dayName} ${now.getDate()}/${now.getMonth() + 1} | ${timeStr}`;
        }
        setInterval(updateHospitalLiveClock, 1000);
        updateHospitalLiveClock();

        // 2. Sistema Global de Toasts Notificadores
        window.showToast = function(message, type = 'success', duration = 4000) {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `pointer-events-auto p-4 rounded-2xl shadow-xl border flex items-start space-x-3 toast-animate-in transition-all duration-300`;

            let iconSvg = '';
            let styleClasses = '';

            if (type === 'success') {
                styleClasses = 'bg-white border-emerald-200 text-slate-800 shadow-emerald-500/10';
                iconSvg = `<div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg></div>`;
            } else if (type === 'error') {
                styleClasses = 'bg-white border-rose-200 text-slate-800 shadow-rose-500/10';
                iconSvg = `<div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg></div>`;
            } else if (type === 'warning') {
                styleClasses = 'bg-white border-amber-200 text-slate-800 shadow-amber-500/10';
                iconSvg = `<div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg></div>`;
            } else {
                styleClasses = 'bg-white border-clinical-200 text-slate-800 shadow-clinical-500/10';
                iconSvg = `<div class="w-8 h-8 rounded-xl bg-clinical-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>`;
            }

            toast.className += ' ' + styleClasses;
            toast.innerHTML = `
                ${iconSvg}
                <div class="flex-1 pr-2">
                    <p class="text-xs font-semibold text-slate-900">${type.toUpperCase()}</p>
                    <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">${message}</p>
                </div>
                <button type="button" class="text-slate-400 hover:text-slate-600 p-1 text-base leading-none transition" onclick="this.parentElement.remove()">&times;</button>
            `;

            container.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-10px) scale(0.95)';
                setTimeout(() => toast.remove(), 300);
            }, duration);
        };
    </script>
</body>
</html>
