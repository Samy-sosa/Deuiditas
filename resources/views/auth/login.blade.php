<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Deuditas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #050507;
            background-image: 
                radial-gradient(at 0% 0%, rgba(37, 99, 235, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(79, 70, 229, 0.1) 0px, transparent 50%);
        }
        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus {
            -webkit-text-fill-color: white;
            -webkit-box-shadow: 0 0 0px 1000px rgba(0,0,0,0) inset;
            transition: background-color 5000s ease-in-out 0s;
        }
        /* Estilo para el botón de visor de contraseña */
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
            transition: color 0.2s;
            background: transparent;
            border: none;
            cursor: pointer;
            z-index: 10;
        }
        .password-toggle:hover {
            color: #60a5fa;
        }
    </style>
</head>
<body class="text-gray-200">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-[2rem] bg-gradient-to-tr from-blue-600 to-indigo-500 shadow-2xl shadow-blue-500/20 mb-6 group transition-transform hover:scale-110">
                    <span class="text-4xl font-black text-white italic">D</span>
                </div>
                <h2 class="text-3xl font-extrabold text-white tracking-tight">
                    Bienvenido a <span class="text-blue-500">Deuditas</span>
                </h2>
                <p class="mt-2 text-sm text-gray-500 font-medium">
                    Gestiona tus apartados con estilo profesional
                </p>
            </div>
            
            <div class="glass p-10 rounded-[3rem] shadow-2xl">
                
                @if(session('error') || $errors->any())
                <div class="mb-6 p-4 rounded-2xl bg-red-500/10 border border-red-500/20 flex items-center gap-3 text-red-400 text-sm">
                    <i class="fas fa-circle-exclamation text-lg"></i>
                    <span>{{ session('error') ?? $errors->first() }}</span>
                </div>
                @endif

                <form class="space-y-6" action="{{ route('login') }}" method="POST">
                    @csrf
                    
                    <div class="space-y-2">
                        <label for="email" class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 ml-2">
                            Correo Electrónico
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-600 group-focus-within:text-blue-500 transition-colors">
                                <i class="fas fa-envelope text-sm"></i>
                            </div>
                            <input id="email" name="email" type="email" autocomplete="email" required 
                                class="block w-full pl-11 pr-4 py-4 bg-white/5 border border-white/5 rounded-2xl text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all sm:text-sm"
                                placeholder="tu@correo.com"
                                value="{{ old('email') }}">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="password" class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500 ml-2">
                            Contraseña
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-600 group-focus-within:text-blue-500 transition-colors">
                                <i class="fas fa-lock text-sm"></i>
                            </div>
                            <input id="password" name="password" type="password" required 
                                class="block w-full pl-11 pr-12 py-4 bg-white/5 border border-white/5 rounded-2xl text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all sm:text-sm"
                                placeholder="••••••••">
                            <button type="button" 
                                    onclick="togglePasswordVisibility()"
                                    class="password-toggle"
                                    aria-label="Mostrar/ocultar contraseña">
                                <i id="passwordIcon" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Enlace "¿Olvidaste tu contraseña?" centrado --}}
                    <div class="text-center mt-2">
                        <a href="{{ route('password.request') }}" class="text-sm text-blue-500 hover:text-blue-400 transition-colors font-medium">
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>

                    <div class="pt-2">
                        <button type="submit" 
                            class="w-full flex justify-center py-4 px-4 bg-blue-600 hover:bg-blue-500 text-white text-sm font-black uppercase tracking-widest rounded-2xl transition-all shadow-lg shadow-blue-600/20 active:scale-[0.98]">
                            Entrar al Sistema
                        </button>
                    </div>
                </form>

                <div class="mt-10">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-white/5"></div>
                        </div>
                        <div class="relative flex justify-center text-[10px] font-black uppercase tracking-widest">
                            <span class="px-4 bg-[#050507] text-gray-600">
                                ¿Eres cliente?
                            </span>
                        </div>
                    </div>

                    <div class="mt-8 text-center">
                        <a href="{{ route('publico.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-400 hover:text-white transition-colors">
                            <i class="fas fa-search text-xs"></i>
                            Consultar mi apartado
                        </a>
                    </div>
                </div>
            </div>
            
            <p class="mt-8 text-center text-[10px] font-bold text-gray-600 uppercase tracking-[0.3em]">
                &copy; {{ date('Y') }} Deuditas System
            </p>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>