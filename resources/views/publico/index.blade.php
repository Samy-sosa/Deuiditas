@extends('layouts.publico')

@section('title', 'Consulta tu Apartado')

@section('content')
<header class="w-full max-w-6xl mx-auto px-6 py-6 flex justify-between items-center" data-aos="fade-down">
    <div class="flex items-center gap-2">
        <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-lg flex items-center justify-center font-black text-sm shadow-lg shadow-blue-600/20">
            <span class="text-white">D</span>
        </div>
        <span class="text-xl font-black tracking-tighter italic bg-gradient-to-r from-blue-400 to-indigo-400 bg-clip-text text-transparent">Deuditas</span>
    </div>
    
</header>

<main class="flex-1 flex items-center justify-center px-6 py-12">
    <div class="w-full max-w-lg" data-aos="zoom-in" data-aos-duration="800">
        
        <div class="text-center mb-10">
            <div class="inline-block p-4 rounded-3xl bg-blue-600/10 border border-blue-500/20 mb-6 animate-pulse">
                <i class="fas fa-search text-3xl text-blue-500"></i>
            </div>
            <h1 class="text-4xl font-black mb-3 tracking-tight bg-gradient-to-r from-white to-gray-400 bg-clip-text text-transparent">Consulta tu apartado</h1>
            <p class="text-gray-500 font-medium">Ingresa el código que recibiste al apartar</p>
        </div>

        <div class="glass p-8 md:p-10 rounded-[2.5rem] shadow-2xl relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            
            @if(session('error'))
                <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-2xl mb-6 text-sm flex items-center gap-3" role="alert">
                    <i class="fas fa-circle-exclamation text-lg flex-shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-2xl mb-6 text-sm" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('publico.buscar') }}" method="POST" class="space-y-6" id="consultaForm">
                @csrf
                <div class="relative">
                    <label class="text-xs font-bold text-blue-500 uppercase tracking-widest ml-1 mb-2 block flex items-center gap-2">
                        <i class="fas fa-tag"></i>
                        Código de seguimiento
                    </label>
                    <div class="relative">
                        <span class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-500">
                            <i class="fas fa-hashtag"></i>
                        </span>
                        <input type="text" 
                               name="codigo" 
                               id="codigoInput"
                               required
                               autocomplete="off"
                               placeholder="Ej: MUN260323189"
                               value="{{ old('codigo') }}"
                               class="w-full bg-white/5 border border-white/10 rounded-2xl py-5 pl-12 pr-6 text-xl font-mono text-white placeholder:text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-600/50 focus:bg-white/10 transition-all text-center uppercase tracking-widest"
                               autofocus
                               aria-label="Código del apartado"
                               maxlength="20">
                        
                        <button type="button" 
                                onclick="limpiarInput()" 
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white transition-colors hidden"
                                id="clearButton"
                                aria-label="Limpiar código">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    </div>
                    
                    <div class="flex items-center gap-2 mt-2 text-[10px] text-gray-600">
                        <i class="fas fa-info-circle"></i>
                        <span>Ingresa el código completo (ej: MUN260323189)</span>
                    </div>
                </div>

                <button type="submit" 
                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-black py-5 rounded-2xl text-lg transition-all transform active:scale-95 shadow-xl shadow-blue-600/20 flex items-center justify-center gap-3 group"
                        id="submitBtn">
                    <span>Consultar apartado</span>
                    <i class="fas fa-arrow-right text-sm group-hover:translate-x-1 transition-transform"></i>
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-white/5">
                <div class="flex items-start gap-3 text-xs text-gray-500">
                    <i class="fas fa-lightbulb text-blue-500 mt-0.5"></i>
                    <div>
                        <p class="font-bold mb-1">¿Dónde encuentro mi código?</p>
                        <p class="text-[10px] leading-relaxed">El código está en el mensaje de WhatsApp que recibiste al apartar. También puedes pedirlo al vendedor.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-6 mt-16" data-aos="fade-up" data-aos-delay="200">
            <div class="text-center">
                <div class="text-2xl font-black text-white mb-1">+25</div>
                <div class="text-[9px] font-bold text-gray-600 uppercase tracking-widest">Consultas diarias</div>
            </div>
            <div class="text-center border-x border-white/5">
                <div class="text-2xl font-black text-white mb-1">+4</div>
                <div class="text-[9px] font-bold text-gray-600 uppercase tracking-widest">Tiendas afiliadas</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-black text-white mb-1">24/7</div>
                <div class="text-[9px] font-bold text-gray-600 uppercase tracking-widest">Disponible</div>
            </div>
        </div>
    </div>
</main>

<footer class="py-8 text-center border-t border-white/5 mt-auto">
    <p class="text-[9px] text-gray-700 font-bold uppercase tracking-[0.3em]">
        © {{ date('Y') }} Deuditas · Sistema de apartados
    </p>
</footer>
@endsection

@push('scripts')
<script>
    (function() {
        const input = document.getElementById('codigoInput');
        const clearBtn = document.getElementById('clearButton');
        const form = document.getElementById('consultaForm');
        const submitBtn = document.getElementById('submitBtn');

        // ============================================
        // VALIDACIÓN Y FORMATEO EN TIEMPO REAL
        // ============================================
        function formatearCodigo() {
            let valor = input.value;
            // Eliminar caracteres no permitidos y convertir a mayúsculas
            valor = valor.toUpperCase().replace(/[^A-Z0-9]/g, '');
            
            // LIMITE AUMENTADO A 20 CARACTERES (suficiente para códigos largos)
            if (valor.length > 20) {
                valor = valor.slice(0, 20);
            }
            
            input.value = valor;
            
            // Mostrar/ocultar botón de limpiar
            if (valor.length > 0) {
                clearBtn.classList.remove('hidden');
            } else {
                clearBtn.classList.add('hidden');
            }
        }

        // ============================================
        // LIMPIAR INPUT
        // ============================================
        window.limpiarInput = function() {
            input.value = '';
            clearBtn.classList.add('hidden');
            input.focus();
        };

        // ============================================
        // VALIDAR ANTES DE ENVIAR
        // ============================================
        function validarFormulario(e) {
            const codigo = input.value.trim();
            
            if (codigo.length < 3) {
                e.preventDefault();
                mostrarError('El código debe tener al menos 3 caracteres');
                return false;
            }
            
            if (!/^[A-Z0-9]+$/.test(codigo)) {
                e.preventDefault();
                mostrarError('El código solo puede contener letras mayúsculas y números');
                return false;
            }
            
            // Deshabilitar botón para evitar doble envío
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <span>Consultando...</span>
                <i class="fas fa-spinner fa-spin"></i>
            `;
            
            return true;
        }

        // ============================================
        // MOSTRAR ERROR TEMPORAL
        // ============================================
        function mostrarError(mensaje) {
            let errorDiv = document.getElementById('error-temporal');
            if (!errorDiv) {
                errorDiv = document.createElement('div');
                errorDiv.id = 'error-temporal';
                errorDiv.className = 'bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-2xl mb-6 text-sm flex items-center gap-3';
                form.parentNode.insertBefore(errorDiv, form);
            }
            
            errorDiv.innerHTML = `
                <i class="fas fa-exclamation-circle text-lg flex-shrink-0"></i>
                <span>${mensaje}</span>
            `;
            
            setTimeout(() => {
                if (errorDiv) {
                    errorDiv.style.transition = 'opacity 0.3s';
                    errorDiv.style.opacity = '0';
                    setTimeout(() => {
                        if (errorDiv && errorDiv.parentNode) {
                            errorDiv.parentNode.removeChild(errorDiv);
                        }
                    }, 300);
                }
            }, 3000);
        }

        // ============================================
        // EVENT LISTENERS
        // ============================================
        input.addEventListener('input', formatearCodigo);
        
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                limpiarInput();
            }
        });
        
        form.addEventListener('submit', validarFormulario);
        
        input.focus();
    })();
</script>
@endpush

@push('styles')
<style>
    @keyframes subtle-pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }
    
    .animate-subtle-pulse {
        animation: subtle-pulse 2s ease-in-out infinite;
    }
    
    input:focus-visible {
        outline: 2px solid #3b82f6;
        outline-offset: 2px;
    }
    
    .glass, .input-glass {
        transition: all 0.2s ease-in-out;
    }
    
    #clearButton {
        transition: opacity 0.2s ease-in-out;
    }
    
    #clearButton:hover {
        transform: scale(1.1);
    }
    
    @media screen and (max-width: 768px) {
        input, select, textarea {
            font-size: 16px !important;
        }
    }
</style>
@endpush