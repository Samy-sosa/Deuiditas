<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>@yield('title', 'OCELLEAD - Sistema de Apartados para Tiendas')</title>
    
    {{-- ============================================ --}}
    {{-- PWA META TAGS --}}
    {{-- ============================================ --}}
    <meta name="theme-color" content="#2563eb">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Ocellead">
    <link rel="apple-touch-icon" href="/icons/icon-152x152.png">
    <link rel="manifest" href="/manifest.json">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        html { scroll-behavior: smooth; }
    </style>
    
    @stack('styles')
</head>
<body class="bg-white">
    <!-- Navbar fija -->
    <nav class="bg-white/80 backdrop-blur-md shadow-sm border-b border-gray-100 fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Logo -->
                <a href="{{ route('landing') }}" class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                    OCELLEAD
                </a>
                
                <!-- Menú -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#caracteristicas" class="text-gray-600 hover:text-gray-900 transition">Características</a>
                    <a href="#planes" class="text-gray-600 hover:text-gray-900 transition">Planes</a>
                    <a href="#faq" class="text-gray-600 hover:text-gray-900 transition">FAQ</a>
                    <a href="{{ route('publico.index') }}" class="text-gray-600 hover:text-gray-900 transition">Buscar apartado</a>
                    <a href="#registro" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-5 py-2 rounded-lg hover:from-blue-700 hover:to-purple-700 transition shadow-md">
                        Crear tienda
                    </a>
                </div>
                
                <!-- Menú móvil (hamburguesa) -->
                <button class="md:hidden text-gray-600">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Contenido principal (padding-top para compensar navbar fija) -->
    <main class="pt-16">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">OCELLEAD</h3>
                    <p class="text-gray-400 text-sm">Soluciones tecnológicas para tu negocio</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Producto</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="#caracteristicas" class="hover:text-white transition">Características</a></li>
                        <li><a href="#planes" class="hover:text-white transition">Planes</a></li>
                        <li><a href="#faq" class="hover:text-white transition">FAQ</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Legal</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="#" class="hover:text-white transition">Términos</a></li>
                        <li><a href="#" class="hover:text-white transition">Privacidad</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Contacto</h4>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li>hola@ocellead.com</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400 text-sm">
                © {{ date('Y') }} OCELLEAD. Todos los derechos reservados.
            </div>
        </div>
    </footer>

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
        
        // Detectar si está instalada como PWA
        if (window.matchMedia('(display-mode: standalone)').matches) {
            console.log('App instalada en el dispositivo');
        }
    </script>
    
    @stack('scripts')
</body>
</html>