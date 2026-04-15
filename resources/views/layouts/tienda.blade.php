<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('title') · {{ session('nombre_tienda') }}</title>
    
    {{-- ============================================ --}}
    {{-- PWA META TAGS --}}
    {{-- ============================================ --}}
    <meta name="theme-color" content="#2563eb">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ session('nombre_tienda') }}">
    <link rel="apple-touch-icon" href="/icons/icon-152x152.png">
    <link rel="manifest" href="/manifest.json">
    
    {{-- Eliminado @vite que causaba el error --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* ⚠️ NUEVO: Prevenir FOUC (Flash of Unstyled Content) */
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #08090d; 
            background-image: 
                radial-gradient(at 0% 0%, rgba(37, 99, 235, 0.15) 0px, transparent 50%), 
                radial-gradient(at 100% 100%, rgba(20, 184, 166, 0.08) 0px, transparent 50%);
            color: #e2e8f0; 
            min-height: 100vh; 
            margin: 0;
            opacity: 0;
            transition: opacity 0.2s ease-in-out;
        }
        body.loaded {
            opacity: 1;
        }

        .glass { 
            background: rgba(15, 17, 23, 0.6); 
            backdrop-filter: blur(12px); 
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.04); 
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }

        .sidebar-item { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); position: relative; }
        .sidebar-item.active { 
            background: linear-gradient(90deg, rgba(37, 99, 235, 0.1) 0%, rgba(37, 99, 235, 0) 100%); 
            color: #60a5fa; 
        }
        .sidebar-item.active::before {
            content: ''; position: absolute; left: 0; top: 15%; height: 70%; width: 4px;
            background: #3b82f6; border-radius: 0 4px 4px 0; box-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
        }

        /* Responsive Sidebar */
        #sidebar { 
            transition: transform 0.3s ease-in-out;
            transform: translateX(0);
        }
        @media (max-width: 1024px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0 !important; }
        }
        
        /* ⚠️ NUEVO: Asegurar margen en desktop */
        @media (min-width: 1025px) {
            .main-content { 
                margin-left: 288px;
                width: calc(100% - 288px);
            }
        }

        #searchModal {
            display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(16px); z-index: 9999;
        }
        #searchModal.active { display: flex; }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 10px; }
        
        /* Estilo para el logo con fallback */
        .logo-container {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(to bottom right, #3b82f6, #6366f1);
            box-shadow: 0 20px 25px -5px rgba(59, 130, 246, 0.2);
        }
        
        .logo-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .logo-fallback {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 900;
            font-style: italic;
            color: white;
        }

        /* PWA Install Button */
        .pwa-install-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 999;
            background: #2563eb;
            border-radius: 60px;
            padding: 12px 20px;
            box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.4);
            display: none;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }
        .pwa-install-btn:hover {
            transform: scale(1.05);
            background: #1d4ed8;
        }
        @media (max-width: 768px) {
            .pwa-install-btn { padding: 10px 16px; font-size: 12px; bottom: 15px; right: 15px; }
        }
    </style>
    @stack('styles')
</head>
<body class="overflow-x-hidden">

    <header class="lg:hidden fixed top-0 left-0 right-0 z-[60] glass border-b border-white/5 p-4 flex justify-between items-center">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center italic font-black text-white">
                {{ substr(session('nombre_tienda') ?? 'E', 0, 1) }}
            </div>
            <span class="font-bold text-xs uppercase tracking-widest text-white">{{ session('nombre_tienda') }}</span>
        </div>
        <button onclick="toggleSidebar()" class="text-white p-2">
            <i class="fas fa-bars text-xl"></i>
        </button>
    </header>

    <aside id="sidebar" class="fixed left-0 top-0 h-full w-72 glass z-[70] flex flex-col border-r border-white/5 lg:translate-x-0">
        <div class="p-8">
            <button onclick="toggleSidebar()" class="lg:hidden absolute top-6 right-6 text-gray-500">
                <i class="fas fa-times"></i>
            </button>

            {{-- LOGO CORREGIDO - Usando el accessor del modelo a través de la sesión --}}
            <div class="flex items-center space-x-4 mb-12">
                <div class="logo-container">
                    @php
                        $tiendaId = session('tienda_id');
                        $tienda = \App\Models\Tienda::find($tiendaId);
                    @endphp
                    
                    @if($tienda && $tienda->logo_url)
                        {{-- Usar el accessor directamente --}}
                        <img src="{{ Str::startsWith($tienda->logo_url, 'http') || Str::startsWith($tienda->logo_url, '/storage') ? $tienda->logo_url : asset('storage/' . $tienda->logo_url) }}">
                        <div class="logo-fallback" style="display: none;">
                            {{ substr($tienda->nombre_tienda ?? session('nombre_tienda') ?? 'E', 0, 1) }}
                        </div>
                    @else
                        <div class="logo-fallback">
                            {{ substr(session('nombre_tienda') ?? 'E', 0, 1) }}
                        </div>
                    @endif
                </div>
                <div class="overflow-hidden">
                    <h2 class="font-bold text-white text-sm truncate leading-tight">{{ session('nombre_tienda') }}</h2>
                    <span class="text-[10px] font-black uppercase text-blue-500 tracking-[0.15em]">Admin Core</span>
                </div>
            </div>
            
            <nav class="space-y-1.5">
                <a href="{{ route('tienda.dashboard') }}" class="sidebar-item group flex items-center space-x-3 px-4 py-3.5 rounded-xl text-sm font-semibold {{ request()->routeIs('tienda.dashboard') ? 'active text-white' : 'text-gray-400 hover:text-gray-200 hover:bg-white/5' }}">
                    <i class="fas fa-th-large w-5 text-center group-hover:scale-110 transition-transform"></i>
                    <span>Dashboard</span>
                </a>
                
                <a href="{{ route('tienda.apartados') }}" class="sidebar-item group flex items-center space-x-3 px-4 py-3.5 rounded-xl text-sm font-semibold {{ request()->routeIs('tienda.apartados*') ? 'active text-white' : 'text-gray-400 hover:text-gray-200 hover:bg-white/5' }}">
                    <i class="fas fa-layer-group w-5 text-center group-hover:scale-110 transition-transform"></i>
                    <span>Apartados</span>
                </a>

                <a href="{{ route('tienda.apartados.crear') }}" class="sidebar-item group flex items-center space-x-3 px-4 py-3.5 rounded-xl text-sm font-semibold {{ request()->routeIs('tienda.apartados.crear') ? 'active text-white' : 'text-gray-400 hover:text-gray-200 hover:bg-white/5' }}">
                    <i class="fas fa-plus-circle w-5 text-center group-hover:scale-110 transition-transform"></i>
                    <span>Nuevo Registro</span>
                </a>

                <a href="{{ route('tienda.configuracion') }}" class="sidebar-item group flex items-center space-x-3 px-4 py-3.5 rounded-xl text-sm font-semibold {{ request()->routeIs('tienda.configuracion') ? 'active text-white' : 'text-gray-400 hover:text-gray-200 hover:bg-white/5' }}">
                    <i class="fas fa-cog w-5 text-center group-hover:rotate-90 transition-transform duration-500"></i>
                    <span>Configuración</span>
                </a>
            </nav>
        </div>

        <div class="mt-auto p-6 border-t border-white/5">
            <div class="flex items-center justify-between gap-3 p-3 rounded-2xl bg-white/[0.02] border border-white/5">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-8 h-8 shrink-0 rounded-lg bg-blue-500/20 flex items-center justify-center text-blue-400 text-xs font-bold uppercase">
                        {{ substr(session('nombre') ?? 'A', 0, 1) }}
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-[9px] text-gray-500 font-bold uppercase truncate">Usuario</p>
                        <p class="text-[11px] text-white font-medium truncate leading-tight">{{ session('nombre') }}</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-red-400 transition-colors">
                        <i class="fas fa-power-off text-sm"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <main class="main-content min-h-screen transition-all pt-20 lg:pt-0">
        <div class="p-6 lg:p-10 container mx-auto">
            @yield('content')
        </div>
    </main>

    <div id="searchModal" class="items-start justify-center pt-24 px-4">
        <div class="w-full max-w-2xl">
            <div class="glass rounded-[2rem] shadow-2xl p-6 lg:p-8 border border-white/10">
                <div class="flex items-center gap-4 mb-6">
                    <i class="fas fa-search text-blue-500 text-xl lg:text-2xl"></i>
                    <input type="text" id="searchInput" placeholder="BUSCAR..." 
                        class="w-full bg-transparent border-none text-xl lg:text-2xl font-black text-white placeholder-gray-700 focus:ring-0 uppercase tracking-tighter">
                    <button onclick="cerrarBuscador()" class="text-gray-500 hover:text-white">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
                <div id="searchResults" class="space-y-2 max-h-[50vh] overflow-y-auto pr-2"></div>
            </div>
        </div>
    </div>

    {{-- Botón de instalación PWA --}}
    <div id="pwaInstallBtn" class="pwa-install-btn">
        <i class="fas fa-download"></i>
        <span>Instalar App</span>
    </div>

    <script>
        // ⚠️ NUEVO: Mostrar body cuando todo esté listo (evitar FOUC)
        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.add('loaded');
        });
        
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
        }

        function abrirBuscador() { 
            document.getElementById('searchModal').classList.add('active'); 
            setTimeout(() => document.getElementById('searchInput').focus(), 100); 
        }

        function cerrarBuscador() { 
            document.getElementById('searchModal').classList.remove('active'); 
            document.getElementById('searchInput').value = ''; 
            document.getElementById('searchResults').innerHTML = ''; 
        }

        // Cerrar sidebar al hacer click fuera en móvil
        document.addEventListener('click', (e) => {
            const sidebar = document.getElementById('sidebar');
            if (window.innerWidth < 1024 && sidebar.classList.contains('open') && !sidebar.contains(e.target) && !e.target.closest('header')) {
                toggleSidebar();
            }
        });
        
        // Actualizar la sesión silenciosamente para mantener datos frescos (opcional)
        setInterval(() => {
            fetch('/tienda/refresh-session', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                .catch(() => {});
        }, 300000); // Cada 5 minutos

        // ============================================
        // PWA INSTALL PROMPT
        // ============================================
        let deferredPrompt;
        const installBtn = document.getElementById('pwaInstallBtn');

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            installBtn.style.display = 'flex';
        });

        installBtn.addEventListener('click', () => {
            if (!deferredPrompt) return;
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    console.log('Usuario instaló la app');
                } else {
                    console.log('Usuario canceló la instalación');
                }
                deferredPrompt = null;
                installBtn.style.display = 'none';
            });
        });

        // Detectar si ya está instalada como PWA
        if (window.matchMedia('(display-mode: standalone)').matches) {
            installBtn.style.display = 'none';
        }
    </script>
    
    {{-- ============================================ --}}
    {{-- PWA SERVICE WORKER REGISTRATION --}}
    {{-- ============================================ --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('Service Worker registrado exitosamente:', registration.scope);
                    })
                    .catch(error => {
                        console.log('Error al registrar Service Worker:', error);
                    });
            });
        }
    </script>
    
    {{-- LIBRERÍAS PARA IMPRESIÓN TÉRMICA (QZ TRAY) --}}
 <script src="https://cdnjs.cloudflare.com/ajax/libs/rsvp/4.8.4/rsvp.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/qz-tray@2.2.5/qz-tray.js"></script>
<script>
    // Configuración para QZ Tray 2.2.x
    async function imprimirDirecto(urlPdf) {
        try {
            // En la 2.2.x, es mejor asegurar la conexión así:
            if (!qz.websocket.isActive()) {
                await qz.websocket.connect();
            }

            // Configuración para tu térmica de 58mm
            const config = qz.configs.create("POS58 Printer", { 
                rasterize: true, 
                units: 'mm',
                density: 203,
                margins: 0 // Importante en térmicas pequeñas para no desperdiciar papel
            });

            const data = [{
                type: 'pixel',
                format: 'pdf',
                flavor: 'file',
                data: urlPdf
            }];

            await qz.print(config, data);
            
            // Opcional: desconectar para liberar el puerto
            // await qz.websocket.disconnect(); 

        } catch (e) {
            console.error("Error de comunicación con QZ Tray:", e);
        }
    }
</script>
    
    @stack('scripts')
</body>
</html>