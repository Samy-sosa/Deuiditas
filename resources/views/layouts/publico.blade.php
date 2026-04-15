<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('title', 'Deuditas - Consulta tu Apartado')</title>
    
    {{-- ============================================ --}}
    {{-- PWA META TAGS --}}
    {{-- ============================================ --}}
    <meta name="theme-color" content="#2563eb">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Deuditas">
    <link rel="apple-touch-icon" href="/icons/icon-152x152.png">
    <link rel="manifest" href="/manifest.json">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #050507;
        }

        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .neon-glow {
            box-shadow: 0 0 20px rgba(37, 99, 235, 0.2);
        }

        /* Animaci¨®n de fondo */
        .bg-animate {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: -1;
            background: radial-gradient(circle at 50% 50%, #0a0a12 0%, #050507 100%);
        }
    </style>
    
    @stack('styles')
</head>
<body class="min-h-screen text-white flex flex-col">
    <div class="bg-animate"></div>
    <div class="fixed top-[-10%] right-[-10%] w-96 h-96 bg-blue-600/10 blur-[100px] rounded-full pointer-events-none"></div>
    
    @yield('content')

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>AOS.init();</script>
    
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
        
        // Detectar si est¨¢ instalada como PWA
        if (window.matchMedia('(display-mode: standalone)').matches) {
            console.log('App instalada en el dispositivo');
        }
    </script>
    
    @stack('scripts')
</body>
</html>