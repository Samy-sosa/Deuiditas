{{-- resources/views/tienda/renovar/index.blade.php --}}
@extends('layouts.tienda')

@section('title', 'Renovar Suscripción - Deuditas')

@section('content')
<div class="max-w-6xl mx-auto px-4">
    {{-- Header con información de la suscripción actual --}}
    <header class="mb-12 text-center" data-aos="fade-down">
        <div class="inline-block p-4 rounded-3xl bg-blue-600/10 border border-blue-500/20 mb-6">
            <i class="fas fa-sync-alt text-3xl text-blue-500 animate-spin-slow"></i>
        </div>
        
        <h1 class="text-4xl font-extrabold text-white mb-3">
            Renovar tu <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-600">Suscripción</span>
        </h1>
        
        {{-- Tarjeta con información de la suscripción actual --}}
        <div class="max-w-md mx-auto mt-6 p-4 bg-white/5 rounded-2xl border border-white/10">
            <div class="flex items-center justify-between">
                <div class="text-left">
                    <p class="text-xs text-gray-400 uppercase tracking-wider">Plan actual</p>
                    <p class="text-lg font-bold text-white">
                        @switch($tienda->plan_tipo)
                            @case('mensual')
                                Plan Mensual
                                @break
                            @case('trimestral')
                                Plan Trimestral
                                @break
                            @case('anual')
                                Plan Anual
                                @break
                            @default
                                {{ ucfirst($tienda->plan_tipo) }}
                        @endswitch
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-400 uppercase tracking-wider">Vence el</p>
                    <p class="text-lg font-bold text-white">{{ \Carbon\Carbon::parse($tienda->fecha_expiracion)->format('d/m/Y') }}</p>
                </div>
            </div>
            
            {{-- Barra de progreso de días restantes --}}
            @php
                $diasRestantes = $tienda->dias_restantes;
                $porcentaje = min(100, max(0, ($diasRestantes / 365) * 100));
                $colorBarra = $diasRestantes > 30 ? 'bg-green-500' : ($diasRestantes > 7 ? 'bg-yellow-500' : 'bg-red-500');
            @endphp
            
            <div class="mt-4">
                <div class="flex justify-between text-xs text-gray-400 mb-1">
                    <span>Días restantes: <span class="font-bold text-white">{{ $diasRestantes }}</span></span>
                    <span>{{ $porcentaje }}%</span>
                </div>
                <div class="w-full h-2 bg-white/10 rounded-full overflow-hidden">
                    <div class="h-full {{ $colorBarra }} rounded-full" style="width: {{ $porcentaje }}%"></div>
                </div>
            </div>
        </div>
    </header>

    {{-- Cards de Planes con precios actualizados --}}
    <div class="grid md:grid-cols-3 gap-8 mb-12">
        @foreach($planes as $plan)
        <div class="glass rounded-[2.5rem] p-8 hover:border-blue-500/30 hover:scale-105 transition-all duration-300 group relative {{ $plan->popular ? 'border-2 border-yellow-500/30 transform scale-105 z-10' : '' }}" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
            @if($plan->popular)
            <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-gradient-to-r from-yellow-500 to-yellow-600 text-black text-[10px] font-black py-1 px-4 rounded-full uppercase tracking-widest whitespace-nowrap shadow-lg">
                🏆 Más popular
            </div>
            @endif
            
            <div class="text-center mb-6">
                <h3 class="text-xl font-black text-white mb-2">{{ $plan->nombre }}</h3>
                
                {{-- Precio principal --}}
                <div class="text-5xl font-black text-blue-500">${{ number_format($plan->precio, 0) }}</div>
                
                {{-- Comparación de ahorro --}}
                @if($plan->precio_regular)
                <div class="flex items-center justify-center gap-2 mt-2">
                    <span class="text-sm text-gray-400 line-through">${{ number_format($plan->precio_regular, 0) }}</span>
                    <span class="text-xs bg-green-500/20 text-green-500 px-2 py-1 rounded-full font-bold">
                        Ahorras ${{ number_format($plan->precio_regular - $plan->precio, 0) }}
                    </span>
                </div>
                @endif
                
                {{-- Precio por día --}}
                <div class="text-xs text-gray-400 mt-2">
                    <i class="fas fa-tag mr-1"></i>
                    ${{ number_format($plan->precio / $plan->dias, 1) }} por día
                </div>
            </div>
            
            {{-- Características del plan --}}
            <div class="space-y-4 mb-8">
                <div class="flex items-center text-gray-300">
                    <i class="fas fa-calendar-check text-green-500 mr-3 text-lg"></i>
                    <span class="font-bold">{{ $plan->dias }} días de servicio</span>
                </div>
                
                @foreach($plan->caracteristicas as $caracteristica)
                <div class="flex items-center text-gray-300">
                    <i class="fas fa-check-circle text-green-500 mr-3 text-lg"></i>
                    <span class="text-sm">{{ $caracteristica }}</span>
                </div>
                @endforeach
            </div>
            
            {{-- Formulario de renovación --}}
            <form action="{{ route('tienda.renovar.procesar') }}" method="POST">
                @csrf
                <input type="hidden" name="plan" value="{{ $plan->id }}">
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-black py-4 rounded-2xl transition-all shadow-lg hover:shadow-blue-600/30">
                    @if($plan->id === $tienda->plan_tipo)
                        <i class="fas fa-sync-alt mr-2"></i>Renovar {{ $plan->nombre }}
                    @else
                        <i class="fas fa-arrow-up mr-2"></i>Cambiar a {{ $plan->nombre }}
                    @endif
                </button>
            </form>
            
            {{-- Nueva fecha de vencimiento --}}
            <div class="mt-4 p-3 bg-white/5 rounded-xl text-center">
                <p class="text-xs text-gray-400">
                    <i class="fas fa-calendar-alt mr-1 text-blue-400"></i>
                    Nueva fecha de vencimiento:
                </p>
                <p class="text-sm font-bold text-white">
                    {{ \Carbon\Carbon::parse($tienda->fecha_expiracion)->addDays($plan->dias)->format('d \d\e F \d\e Y') }}
                </p>
            </div>
        </div>
        @endforeach
    </div>
    
    {{-- Información adicional --}}
    <div class="max-w-3xl mx-auto mt-12 p-6 bg-white/5 rounded-3xl border border-white/10">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
            <div>
                <div class="w-12 h-12 bg-blue-600/20 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-shield-alt text-blue-500 text-xl"></i>
                </div>
                <h4 class="font-bold text-white mb-1">Pago Seguro</h4>
                <p class="text-xs text-gray-400">Procesado por Mercado Pago</p>
            </div>
            <div>
                <div class="w-12 h-12 bg-blue-600/20 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-clock text-blue-500 text-xl"></i>
                </div>
                <h4 class="font-bold text-white mb-1">Activación Inmediata</h4>
                <p class="text-xs text-gray-400">Tu suscripción se renueva al instante</p>
            </div>
            <div>
                <div class="w-12 h-12 bg-blue-600/20 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-headset text-blue-500 text-xl"></i>
                </div>
                <h4 class="font-bold text-white mb-1">Soporte 24/7</h4>
                <p class="text-xs text-gray-400">Ayuda inmediata si la necesitas</p>
            </div>
        </div>
    </div>
    
    {{-- Botón para volver al dashboard --}}
    <div class="text-center mt-8">
        <a href="{{ route('tienda.dashboard') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-white transition-colors text-sm bg-white/5 hover:bg-white/10 px-6 py-3 rounded-full">
            <i class="fas fa-arrow-left"></i>
            Volver al dashboard
        </a>
    </div>
</div>

<style>
    @keyframes spin-slow {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .animate-spin-slow {
        animation: spin-slow 3s linear infinite;
    }
</style>

@push('scripts')
<script>
    // Animación suave al hacer hover en las cards
    document.querySelectorAll('.glass').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
            if(this.classList.contains('z-10')) {
                this.style.transform = 'scale(1.08)';
            }
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = '';
        });
    });
</script>
@endpush
@endsection