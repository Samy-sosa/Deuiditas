<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('title', 'Panel de Administraci車n')</title>
    
    {{-- ============================================ --}}
    {{-- PWA META TAGS --}}
    {{-- ============================================ --}}
    <meta name="theme-color" content="#4f46e5">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Ocellated Admin">
    <link rel="apple-touch-icon" href="/icons/icon-152x152.png">
    <link rel="manifest" href="/manifest.json">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        
        /* PWA Install Button - Admin version */
        .pwa-install-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 999;
            background: #4f46e5;
            border-radius: 60px;
            padding: 12px 20px;
            box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.4);
            display: none;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            color: white;
        }
        .pwa-install-btn:hover {
            transform: scale(1.05);
            background: #4338ca;
        }
        @media (max-width: 768px) {
            .pwa-install-btn { padding: 10px 16px; font-size: 12px; bottom: 15px; right: 15px; }
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-indigo-900 text-white">
            <div class="p-6">
                <h2 class="text-2xl font-bold">Ocellated</h2>
                <p class="text-sm text-indigo-300 mt-1">Super Admin</p>
            </div>
            <nav class="mt-6">
                <a href="{{ route('admin.dashboard') }}" class="block py-3 px-6 hover:bg-indigo-800 transition {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-800' : '' }}">
                    <i class="fas fa-home mr-3"></i> Dashboard
                </a>
                <a href="{{ route('admin.tiendas') }}" class="block py-3 px-6 hover:bg-indigo-800 transition {{ request()->routeIs('admin.tiendas*') ? 'bg-indigo-800' : '' }}">
                    <i class="fas fa-store mr-3"></i> Tiendas
                </a>
                <a href="{{ route('admin.cupones') }}" class="block py-3 px-6 hover:bg-indigo-800 transition {{ request()->routeIs('admin.cupones*') ? 'bg-indigo-800' : '' }}">
                    <i class="fas fa-ticket-alt mr-3"></i> Cupones
                </a>
            </nav>
        </div>
        
        <!-- Contenido -->
        <div class="flex-1 overflow-y-auto">
            <!-- Top bar -->
            <div class="bg-white shadow-sm p-4 flex justify-between items-center sticky top-0 z-10">
                <h1 class="text-2xl font-bold text-gray-800">@yield('header', 'Administraci車n')</h1>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="text-red-600 hover:text-red-800">
                        <i class="fas fa-sign-out-alt mr-2"></i> Cerrar Sesi車n
                    </button>
                </form>
            </div>
            
            <!-- Main content -->
            <div class="p-6">
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                        {{ session('error') }}
                    </div>
                @endif
                
                @yield('content')
            </div>
        </div>
    </div>

    {{-- Bot車n de instalaci車n PWA --}}
    <div id="pwaInstallBtn" class="pwa-install-btn">
        <i class="fas fa-download"></i>
        <span>Instalar App</span>
    </div>

    {{-- ============================================ --}}
    {{-- PWA SERVICE WORKER REGISTRATION --}}
    {{-- ============================================ --}}
    <script>
        // PWA Install Prompt
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
                    console.log('Usuario instal車 la app');
                } else {
                    console.log('Usuario cancel車 la instalaci車n');
                }
                deferredPrompt = null;
                installBtn.style.display = 'none';
            });
        });

        // Detectar si ya est芍 instalada como PWA
        if (window.matchMedia('(display-mode: standalone)').matches) {
            installBtn.style.display = 'none';
        }
        
        // Service Worker Registration
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
    
    @stack('scripts')
</body>
</html>