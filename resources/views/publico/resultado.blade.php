@extends('layouts.publico')

@section('title', 'Ticket #' . $apartado->codigo_unico . ' - Deuditas')

@section('content')
@php
    $porcentaje = $apartado->porcentaje_pagado;
    $totalPagado = $apartado->total_pagado;
    $diasRestantes = (int) $apartado->dias_restantes;
    $estaVencido = $apartado->esta_vencido;
    $estaPagado = $apartado->esta_pagado;
    
    // Array de meses en español
    $meses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];
    
    $fecha = \Carbon\Carbon::parse($apartado->fecha_limite);
    $dia = $fecha->format('d');
    $mesNumero = (int) $fecha->format('m');
    $anio = $fecha->format('Y');
    $fechaEspañol = $dia . ' de ' . $meses[$mesNumero] . ' de ' . $anio;
@endphp

<div class="min-h-screen py-6 sm:py-12 px-3 sm:px-4 relative">
    <div class="max-w-4xl mx-auto">
        
        {{-- Header simplificado - solo botón volver --}}
        <div class="mb-4 sm:mb-8">
            <a href="{{ route('publico.index') }}" 
               class="group inline-flex items-center gap-2 text-gray-400 hover:text-white transition-all font-bold text-xs sm:text-sm uppercase tracking-widest">
                <div class="w-10 h-10 sm:w-8 sm:h-8 rounded-full bg-white/5 flex items-center justify-center group-hover:bg-blue-600 transition-colors">
                    <i class="fas fa-chevron-left text-sm sm:text-xs"></i>
                </div>
                <span class="text-sm sm:text-xs">Volver a buscar</span>
            </a>
        </div>

        {{-- Columnas --}}
        <div class="flex flex-col lg:grid lg:grid-cols-3 gap-4 sm:gap-8">
            
            {{-- COLUMNA IZQUIERDA --}}
            <div class="lg:col-span-1 space-y-4 sm:space-y-6">
                
                {{-- TIENDA --}}
                <div class="glass p-5 sm:p-6 rounded-2xl sm:rounded-[2rem] relative overflow-hidden">
                    <div class="absolute top-0 left-0 bg-blue-600/20 px-2 sm:px-3 py-1 rounded-br-xl sm:rounded-br-2xl text-[7px] sm:text-[8px] font-black uppercase text-blue-400">
                        <i class="fas fa-store mr-1"></i> TIENDA
                    </div>
                    
                    <div class="mt-5 sm:mt-6 flex flex-col items-center sm:block">
                        @if($apartado->tienda && $apartado->tienda->logo_url)
                            <img src="{{ $apartado->tienda->logo_url }}" 
                                 class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl sm:rounded-2xl mb-3 sm:mb-4 object-cover border border-white/10 mx-auto sm:mx-0"
                                 alt="Logo"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-blue-600 rounded-xl sm:rounded-2xl hidden items-center justify-center text-2xl sm:text-2xl font-black mb-3 sm:mb-4 mx-auto sm:mx-0">
                                {{ substr($apartado->tienda->nombre_tienda ?? 'T', 0, 1) }}
                            </div>
                        @else
                            <div class="w-16 h-16 sm:w-20 sm:h-20 bg-blue-600 rounded-xl sm:rounded-2xl flex items-center justify-center text-2xl sm:text-2xl font-black mb-3 sm:mb-4 mx-auto sm:mx-0">
                                {{ substr($apartado->tienda->nombre_tienda ?? $apartado->nombre_tienda ?? 'T', 0, 1) }}
                            </div>
                        @endif
                        
                        <h2 class="text-lg sm:text-xl font-black mb-1 text-center sm:text-left">{{ $apartado->tienda->nombre_tienda ?? $apartado->nombre_tienda ?? 'Tienda' }}</h2>
                        <p class="text-[10px] sm:text-xs text-gray-500 font-bold uppercase mb-3 sm:mb-4 text-center sm:text-left">Comercio Verificado</p>
                        
                        @if($apartado->tienda && $apartado->tienda->telefono_contacto)
                        <a href="tel:{{ $apartado->tienda->telefono_contacto }}" class="flex items-center justify-center sm:justify-start gap-2 sm:gap-3 text-gray-400 hover:text-blue-400 text-xs sm:text-sm py-2">
                            <i class="fas fa-phone-alt text-blue-500 w-4"></i> 
                            <span>{{ $apartado->tienda->telefono_contacto }}</span>
                        </a>
                        @endif
                    </div>
                </div>

                {{-- CLIENTE --}}
                <div class="glass p-5 sm:p-6 rounded-2xl sm:rounded-[2rem] relative overflow-hidden">
                    <div class="absolute top-0 left-0 bg-purple-600/20 px-2 sm:px-3 py-1 rounded-br-xl sm:rounded-br-2xl text-[7px] sm:text-[8px] font-black uppercase text-purple-400">
                        <i class="fas fa-user mr-1"></i> CLIENTE
                    </div>
                    
                    <div class="mt-5 sm:mt-6 space-y-2 sm:space-y-3">
                        <div class="flex items-center gap-2 sm:gap-3 text-sm sm:text-base">
                            <i class="fas fa-user-tag text-purple-500 w-4 sm:w-4"></i>
                            <span class="font-bold">{{ $apartado->nombre_cliente }}</span>
                        </div>
                        
                        @if($apartado->telefono_cliente)
                        <div class="flex items-center gap-2 sm:gap-3 text-gray-400 text-xs sm:text-sm">
                            <i class="fas fa-phone-alt text-purple-500 w-4 sm:w-4"></i>
                            <span>{{ $apartado->telefono_cliente }}</span>
                        </div>
                        @endif
                        
                        @if($apartado->email_cliente)
                        <div class="flex items-center gap-2 sm:gap-3 text-gray-400 text-xs sm:text-sm">
                            <i class="fas fa-envelope text-purple-500 w-4 sm:w-4"></i>
                            <span class="truncate">{{ $apartado->email_cliente }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- VENCIMIENTO con fecha en español manual --}}
                <div class="p-4 sm:p-6 rounded-2xl sm:rounded-[2rem] {{ $estaVencido ? 'bg-red-500/10 border border-red-500/20' : 'bg-blue-600' }} shadow-xl">
                    <div class="flex items-center gap-3 sm:gap-4">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-white/20 flex items-center justify-center text-base sm:text-xl text-white flex-shrink-0">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[8px] sm:text-[10px] uppercase font-black opacity-70 tracking-widest text-white">Vence el día</p>
                            <p class="text-base sm:text-lg font-black text-white truncate">
                                {{ $fechaEspañol }}
                            </p>
                            @if($estaVencido)
                                <p class="text-[8px] sm:text-[9px] font-black text-white/70 mt-1">VENCIDO HACE {{ abs($diasRestantes) }} DÍAS</p>
                            @elseif(!$estaPagado)
                                <p class="text-[8px] sm:text-[9px] font-black text-white/70 mt-1">{{ $diasRestantes }} DÍAS RESTANTES</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- BOTÓN DE PAGO --}}
                @if($apartado->saldo_pendiente > 0 && $apartado->tienda && $apartado->tienda->clabe && $apartado->tienda->banco && $apartado->tienda->titular)
                <button onclick="toggleModal('modalPago')" class="w-full mt-2 bg-white/5 hover:bg-blue-600 hover:text-white text-blue-400 border border-blue-500/20 font-black py-4 sm:py-4 px-4 rounded-xl sm:rounded-2xl transition-all flex items-center justify-center gap-2 text-sm sm:text-base min-h-[52px] sm:min-h-[56px]">
                    <i class="fas fa-university"></i>
                    <span>Ver datos para transferencia</span>
                </button>
                @endif
            </div>

            {{-- COLUMNA DERECHA --}}
            <div class="lg:col-span-2 space-y-4 sm:space-y-6 mt-4 lg:mt-0">
                <div class="glass rounded-2xl sm:rounded-[2.5rem] overflow-hidden">
                    <div class="p-5 sm:p-8 border-b border-white/5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 bg-white/[0.02]">
                        <div>
                            <span class="text-[8px] sm:text-[10px] font-black uppercase tracking-[0.2em] sm:tracking-[0.3em] text-blue-500">Folio de Seguimiento</span>
                            <h1 class="text-2xl sm:text-3xl font-black break-all">#{{ $apartado->codigo_unico }}</h1>
                        </div>
                        <span class="px-3 sm:px-5 py-1.5 sm:py-2 rounded-full text-[10px] sm:text-xs font-black uppercase whitespace-nowrap
                            @if($apartado->estado == 'activo') bg-yellow-500 text-black
                            @elseif($apartado->estado == 'pagado') bg-emerald-500 text-white
                            @else bg-red-500 text-white @endif">
                            {{ $apartado->estado }}
                        </span>
                    </div>

                    <div class="p-5 sm:p-8">
                        {{-- Progreso --}}
                        <div class="mb-6 sm:mb-10">
                            <div class="flex justify-between items-end mb-2 sm:mb-3">
                                <span class="text-xs sm:text-sm font-bold text-gray-400">Progreso de Pago</span>
                                <span class="text-xl sm:text-2xl font-black text-white">{{ $porcentaje }}%</span>
                            </div>
                            <div class="w-full bg-white/5 h-3 sm:h-4 rounded-full overflow-hidden p-0.5 sm:p-1 border border-white/5">
                                <div class="bg-gradient-to-r from-blue-600 to-indigo-400 h-full rounded-full transition-all duration-1000 progress-bar" 
                                     style="width: {{ $porcentaje }}%"></div>
                            </div>

                            {{-- HISTORIAL DE PAGOS CON DETALLE --}}
                            <div class="mt-4 sm:mt-6">
                                <button onclick="toggleAccordion('historial')" class="flex items-center gap-2 text-[9px] sm:text-[10px] font-black uppercase tracking-widest text-gray-500 hover:text-blue-400 py-2 w-full justify-between">
                                    <span>Ver historial de abonos</span>
                                    <i id="icon-historial" class="fas fa-chevron-down transition-transform duration-300"></i>
                                </button>
                                <div id="content-historial" class="hidden mt-3 sm:mt-4 space-y-2 animate-fadeIn">
                                    @forelse($apartado->pagos->sortByDesc('fecha_pago') as $pago)
                                    <div class="flex justify-between items-center bg-white/5 p-3 sm:p-3 rounded-xl border border-white/5">
                                        <div class="flex flex-col">
                                            <span class="text-gray-400 text-[10px] sm:text-xs">
                                                {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y H:i') }}
                                            </span>
                                            @if($pago->producto_id)
                                                <span class="text-blue-400 text-[8px] sm:text-[9px] mt-1 flex items-center gap-1">
                                                    <i class="fas fa-box"></i> Abono a "{{ $pago->producto->nombre_producto ?? 'Producto' }}"
                                                </span>
                                            @else
                                                <span class="text-gray-500 text-[8px] sm:text-[9px] mt-1 flex items-center gap-1">
                                                    <i class="fas fa-chart-line"></i> Abono general
                                                </span>
                                            @endif
                                        </div>
                                        <span class="font-bold text-emerald-400 text-[10px] sm:text-xs">+${{ number_format($pago->monto, 2) }}</span>
                                    </div>
                                    @empty
                                    <p class="text-[9px] sm:text-[10px] text-gray-600 italic">No hay abonos registrados.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        {{-- Totales --}}
                        <div class="grid grid-cols-2 gap-3 sm:gap-4 mb-6 sm:mb-8">
                            <div class="bg-white/5 p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-white/5">
                                <p class="text-[8px] sm:text-[10px] uppercase font-black text-gray-500 mb-1">Total</p>
                                <p class="text-lg sm:text-2xl font-black truncate">${{ number_format($apartado->total, 2) }}</p>
                            </div>
                            <div class="bg-blue-600/10 p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-blue-500/20">
                                <p class="text-[8px] sm:text-[10px] uppercase font-black text-blue-400 mb-1 text-right">Saldo Pendiente</p>
                                <p class="text-lg sm:text-2xl font-black text-right text-blue-400 truncate">${{ number_format($apartado->saldo_pendiente, 2) }}</p>
                            </div>
                        </div>

                        {{-- Productos CON SALDO PENDIENTE POR PRODUCTO --}}
                        <div class="space-y-3 sm:space-y-4">
                            <h3 class="text-xs sm:text-sm font-black uppercase tracking-widest text-gray-500 flex items-center gap-2">
                                <i class="fas fa-shopping-bag text-blue-500"></i> Artículos
                            </h3>
                            <div class="bg-white/[0.02] rounded-2xl sm:rounded-3xl border border-white/5 divide-y divide-white/5">
                                @foreach($apartado->productos as $producto)
                                @php
                                    $pagadoProducto = $producto->monto_pagado ?? 0;
                                    $saldoProducto = $producto->saldo_pendiente;
                                    $porcentajeProducto = $producto->porcentaje_pagado;
                                @endphp
                                <div class="p-3 sm:p-4">
                                    <div class="flex justify-between items-center gap-2 mb-2">
                                        <div class="flex items-center gap-2 sm:gap-4 min-w-0 flex-1">
                                            <div class="text-xs sm:text-sm font-bold text-blue-500 flex-shrink-0">{{ $producto->cantidad }}x</div>
                                            <p class="font-bold text-xs sm:text-sm truncate">{{ $producto->nombre_producto }}</p>
                                        </div>
                                        <span class="font-black text-xs sm:text-sm text-white whitespace-nowrap flex-shrink-0">${{ number_format($producto->subtotal, 2) }}</span>
                                    </div>
                                    
                                    {{-- Barra de progreso por producto --}}
                                    <div class="mt-2">
                                        <div class="flex justify-between text-[9px] text-gray-500 mb-1">
                                            <span>Pagado: ${{ number_format($pagadoProducto, 2) }}</span>
                                            <span class="text-red-400">Falta: ${{ number_format($saldoProducto, 2) }}</span>
                                            <span class="text-blue-400">{{ number_format($porcentajeProducto, 1) }}%</span>
                                        </div>
                                        <div class="w-full bg-white/10 rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-full rounded-full transition-all duration-500" 
                                                 style="width: {{ $porcentajeProducto }}%"></div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DE PAGO --}}
@if($apartado->tienda && $apartado->tienda->clabe && $apartado->tienda->banco && $apartado->tienda->titular)
<div id="modalPago" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/95 backdrop-blur-md hidden opacity-0 transition-opacity duration-300">
    <div class="glass w-full sm:max-w-md rounded-t-3xl sm:rounded-[2.5rem] p-6 sm:p-8 shadow-2xl transform translate-y-full sm:translate-y-0 sm:scale-95 transition-all duration-300 border-t sm:border border-white/10"
         id="modalContent">
        <div class="flex justify-between items-center mb-4 sm:mb-6">
            <h3 class="text-lg sm:text-xl font-black flex items-center gap-2">
                <i class="fas fa-university text-blue-500"></i> Datos Bancarios
            </h3>
            <button onclick="toggleModal('modalPago')" class="text-gray-500 hover:text-white p-2">
                <i class="fas fa-times-circle text-xl sm:text-2xl"></i>
            </button>
        </div>

        <div class="space-y-3 sm:space-y-4 max-h-[70vh] overflow-y-auto pb-4">
            {{-- CLABE --}}
            <div class="p-4 sm:p-4 bg-blue-600/10 rounded-xl sm:rounded-2xl border border-blue-500/20">
                <p class="text-[8px] sm:text-[10px] font-black text-blue-500 uppercase mb-2 flex justify-between items-center">
                    <span>CLABE Interbancaria</span>
                    <button onclick="copiarTexto('{{ $apartado->tienda->clabe }}', 'CLABE copiada')" 
                            class="text-blue-400 hover:text-blue-300 text-[8px] sm:text-xs flex items-center gap-1 px-2 py-1 bg-blue-500/10 rounded-lg">
                        <i class="far fa-copy"></i> Copiar
                    </button>
                </p>
                <p class="text-base sm:text-lg font-mono text-white tracking-[0.1em] sm:tracking-[0.2em] text-center bg-black/20 py-2 sm:py-2 rounded-lg break-all px-2">
                    {{ wordwrap($apartado->tienda->clabe, 4, ' ', true) }}
                </p>
            </div>

            {{-- Banco y Titular --}}
            <div class="grid grid-cols-2 gap-2 sm:gap-3">
                <div class="p-3 sm:p-3 bg-white/5 rounded-xl border border-white/10">
                    <p class="text-[7px] sm:text-[8px] font-black text-gray-500 uppercase mb-1">Banco</p>
                    <p class="font-bold text-xs sm:text-sm truncate">{{ $apartado->tienda->banco }}</p>
                    <button onclick="copiarTexto('{{ $apartado->tienda->banco }}', 'Banco copiado')" 
                            class="text-[7px] sm:text-[8px] text-blue-400 hover:text-blue-300 mt-1 flex items-center gap-1">
                        <i class="far fa-copy"></i> Copiar
                    </button>
                </div>
                
                <div class="p-3 sm:p-3 bg-white/5 rounded-xl border border-white/10">
                    <p class="text-[7px] sm:text-[8px] font-black text-gray-500 uppercase mb-1">Titular</p>
                    <p class="font-bold text-xs sm:text-sm truncate">{{ $apartado->tienda->titular }}</p>
                    <button onclick="copiarTexto('{{ $apartado->tienda->titular }}', 'Titular copiado')" 
                            class="text-[7px] sm:text-[8px] text-blue-400 hover:text-blue-300 mt-1 flex items-center gap-1">
                        <i class="far fa-copy"></i> Copiar
                    </button>
                </div>
            </div>

            {{-- Instrucciones --}}
            <div class="bg-yellow-500/10 p-3 sm:p-4 rounded-xl border border-yellow-500/20">
                <p class="text-[9px] sm:text-[10px] text-yellow-500 font-bold leading-tight">
                    <i class="fas fa-info-circle mr-1"></i> 
                    Después de transferir, contacta al vendedor para confirmar tu pago.
                </p>
            </div>
        </div>
    </div>
</div>
@endif

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Auto-recarga
@if($apartado->estado === 'activo')
setTimeout(() => location.reload(), 30000);
@endif

// Acordeón
function toggleAccordion(id) {
    const content = document.getElementById(`content-${id}`);
    const icon = document.getElementById(`icon-${id}`);
    content.classList.toggle('hidden');
    icon.classList.toggle('rotate-180');
}

// Modal mejorado para móvil
function toggleModal(id) {
    const modal = document.getElementById(id);
    const content = document.getElementById('modalContent');
    const body = document.body;
    
    if(modal.classList.contains('hidden')) {
        modal.classList.remove('hidden');
        body.style.overflow = 'hidden';
        
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            if (window.innerWidth < 640) {
                content.classList.remove('translate-y-full');
            } else {
                content.classList.remove('scale-95');
            }
        }, 10);
    } else {
        modal.classList.add('opacity-0');
        if (window.innerWidth < 640) {
            content.classList.add('translate-y-full');
        } else {
            content.classList.add('scale-95');
        }
        setTimeout(() => {
            modal.classList.add('hidden');
            body.style.overflow = 'auto';
        }, 300);
    }
}

// Copiar texto con feedback táctil
function copiarTexto(texto, mensaje = 'Copiado') {
    navigator.clipboard.writeText(texto).then(() => {
        if (window.navigator && window.navigator.vibrate) {
            window.navigator.vibrate(50);
        }
        
        Swal.fire({
            toast: true,
            position: window.innerWidth < 640 ? 'bottom' : 'top-end',
            icon: 'success',
            title: mensaje,
            showConfirmButton: false,
            timer: 1500,
            background: '#050507',
            color: '#fff',
            iconColor: '#10b981'
        });
    });
}

// Cerrar modal con botón de retroceso en Android
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('modalPago');
        if (!modal.classList.contains('hidden')) {
            toggleModal('modalPago');
        }
    }
});
</script>

<style>
/* Animaciones */
.animate-fadeIn {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}

.progress-bar {
    transition: width 1s ease-in-out;
}

/* Prevenir zoom en inputs iOS */
@media screen and (max-width: 768px) {
    input, select, textarea {
        font-size: 16px !important;
    }
}

/* Mejora de scroll en móvil */
#modalContent {
    max-height: 90vh;
}

#modalContent .overflow-y-auto {
    -webkit-overflow-scrolling: touch;
}

/* Botones táctiles */
button, a {
    min-height: 44px;
    min-width: 44px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

/* Ajuste para botones pequeños */
.text-blue-400 button {
    min-height: 32px;
    min-width: 32px;
}

/* Print styles */
@media print {
    body { background: white !important; color: black !important; }
    .glass { border: 1px solid #ddd !important; background: white !important; }
    #modalPago, button, .no-print { display: none !important; }
    .text-white { color: black !important; }
}
</style>
@endsection