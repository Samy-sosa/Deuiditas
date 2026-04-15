@extends('layouts.tienda')

@section('title', 'Dashboard')

@section('content')
    {{-- MENSAJE DE SUSCRIPCIÓN --}}
    @if(session('dias_restantes'))
        @php
            $dias = (int)session('dias_restantes');
            
            if ($dias <= 7) {
                // Menos de 7 días o vencido (pero el middleware ya redirige si está vencido)
                $color = 'yellow';
                $icono = 'clock';
                $titulo = '¡Atención!';
                $mensaje = "⏰ Tu suscripción vence en {$dias} días. Renueva para no interrumpir tu servicio.";
                $mostrarBoton = true;
                $estilo = 'alerta';
            } else {
                // Suscripción activa con más de 7 días - estilo sutil
                $estilo = 'sutil';
            }
        @endphp
        
        @if($estilo == 'alerta')
            <div class="mb-6 p-4 bg-yellow-500/20 border-l-4 border-yellow-500 rounded-xl flex justify-between items-center glass">
                <div class="flex items-center text-white">
                    <i class="fas fa-clock mr-3 text-xl text-yellow-500"></i>
                    <div>
                        <span class="font-bold">¡Atención!</span>
                        <p class="text-xs text-gray-400">⏰ Tu suscripción vence en {{ $dias }} días. Renueva para no interrumpir tu servicio.</p>
                    </div>
                </div>
                
                <a href="{{ route('tienda.renovar') }}" 
                   class="ml-4 bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all shadow-lg whitespace-nowrap flex items-center gap-2">
                    <i class="fas fa-sync-alt"></i>
                    Renovar ahora
                </a>
            </div>
        @else
            {{-- Estilo sutil para suscripción activa --}}
            <div class="mb-4 flex justify-end">
                <div class="flex items-center gap-2 text-xs text-gray-500 bg-white/5 px-3 py-1.5 rounded-full">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span>Suscripción activa · {{ $dias }} días restantes</span>
                </div>
            </div>
        @endif
    @endif

    {{-- HEADER CON BOTONES DE ACCIÓN --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-4xl font-extrabold text-white tracking-tight">Centro de Control</h1>
            <p class="text-gray-500 text-sm font-medium mt-1 uppercase tracking-widest">Estado del sistema: <span class="text-emerald-500">Online</span></p>
        </div>
        
        {{-- BOTONES DE ACCIÓN --}}
        <div class="flex flex-wrap gap-2">
            {{-- Botón Buscar cliente --}}
            <a href="{{ route('tienda.clientes.buscar') }}" 
               class="bg-purple-600 hover:bg-purple-500 text-white px-5 py-3 rounded-xl text-sm font-bold transition-all shadow-lg shadow-purple-600/20 flex items-center gap-2">
                <i class="fas fa-users"></i>
                Buscar cliente
            </a>
            
            {{-- Botón Nuevo apartado --}}
            <a href="{{ route('tienda.apartados.crear') }}" 
               class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-3 rounded-xl text-sm font-bold transition-all shadow-lg shadow-blue-600/20 flex items-center gap-2">
                <i class="fas fa-plus-circle"></i>
                Nuevo apartado
            </a>
        </div>
    </div>

    {{-- BUSCADOR RÁPIDO --}}
    <div class="mb-8">
        <form action="{{ route('tienda.buscar') }}" method="GET" class="flex gap-2">
            <div class="relative flex-1">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" 
                       name="q" 
                       value="{{ request('q') }}"
                       placeholder="Buscar apartado por código o nombre del cliente..." 
                       class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-3 text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all">
            </div>
            <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-3 rounded-xl font-bold transition-all shadow-lg shadow-blue-600/20">
                Buscar
            </button>
        </form>
    </div>

    {{-- ESTADÍSTICAS --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
        <div class="glass p-6 rounded-[2rem] hover:border-blue-500/30 transition-all group">
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-500">Activos</span>
                <i class="fas fa-clock text-yellow-500/50"></i>
            </div>
            <p class="text-4xl font-black text-white">{{ $activos }}</p>
        </div>
        
        <div class="glass p-6 rounded-[2rem] hover:border-emerald-500/30 transition-all">
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-500">Pagados</span>
                <i class="fas fa-check-double text-emerald-500/50"></i>
            </div>
            <p class="text-4xl font-black text-white">{{ $pagados }}</p>
        </div>

        <div class="glass p-6 rounded-[2rem] hover:border-red-500/30 transition-all">
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-500">Vencidos</span>
                <i class="fas fa-bolt text-red-500/50"></i>
            </div>
            <p class="text-4xl font-black text-white">{{ $vencidos }}</p>
        </div>

        <div class="glass p-6 rounded-[2rem] bg-gradient-to-br from-blue-600/10 to-transparent border-blue-500/20">
            <div class="flex items-center justify-between mb-4 text-blue-400">
                <span class="text-[10px] font-black uppercase tracking-widest">Recaudado</span>
                <i class="fas fa-wallet"></i>
            </div>
            <p class="text-3xl font-black text-white">${{ number_format($totalAbonado, 2) }}</p>
        </div>
    </div>

    {{-- ACTIVIDAD RECIENTE --}}
    <div class="glass rounded-[2.5rem] p-8">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-extrabold text-white tracking-tighter">Actividad Reciente</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[10px] font-black uppercase tracking-widest text-gray-600 border-b border-white/5">
                        <th class="pb-4">Código</th>
                        <th class="pb-4">Cliente</th>
                        <th class="pb-4">Estado</th>
                        <th class="pb-4 text-right">Saldo</th>
                        <th class="pb-4 text-center" colspan="2">Acciones</th>
                     </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($recientes as $a)
                    <tr class="hover:bg-white/[0.01] transition-colors group">
                        <td class="py-5 font-mono text-blue-400 font-bold">#{{ $a->codigo_unico }}</td>
                        <td class="py-5 text-sm font-bold text-white uppercase">{{ $a->nombre_cliente }}</td>
                        <td class="py-5">
                            <span class="px-3 py-1 text-[9px] font-black uppercase rounded-lg 
                                @switch($a->estado)
                                    @case('activo') bg-yellow-500/10 text-yellow-500 @break
                                    @case('pagado') bg-emerald-500/10 text-emerald-500 @break
                                    @default bg-red-500/10 text-red-500
                                @endswitch">
                                {{ $a->estado }}
                            </span>
                        </td>
                        <td class="py-5 text-right font-black {{ $a->saldo_pendiente > 0 ? 'text-red-400' : 'text-emerald-400' }}">
                            ${{ number_format($a->saldo_pendiente, 2) }}
                        </td>
                        <td class="py-5 text-center">
                            @if($a->estado == 'activo')
                                <div class="flex items-center justify-center gap-2">
                                    {{-- Botón Agregar Productos --}}
                                    <a href="{{ route('tienda.apartados.agregar-producto', $a->id) }}" 
                                       class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white px-3 py-2 rounded-xl text-xs font-bold transition-all shadow-lg"
                                       title="Agregar productos a este apartado">
                                        <i class="fas fa-plus-circle"></i>
                                        <span class="hidden sm:inline">Productos</span>
                                    </a>
                                    
                                    {{-- Botón Registrar Abono --}}
                                    @if($a->saldo_pendiente > 0)
                                        <a href="{{ route('tienda.apartados.mostrar', $a->id) }}#pago" 
                                           class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-500 text-white px-3 py-2 rounded-xl text-xs font-bold transition-all shadow-lg"
                                           title="Registrar abono">
                                            <i class="fas fa-dollar-sign"></i>
                                            <span class="hidden sm:inline">Abono</span>
                                        </a>
                                    @else
                                        <span class="text-gray-600 text-xs">Pagado</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-gray-600 text-xs">{{ ucfirst($a->estado) }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-600 italic">No hay apartados recientes</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Botón para ver todos los apartados --}}
        <div class="mt-6 text-center">
            <a href="{{ route('tienda.apartados') }}" 
               class="inline-flex items-center gap-2 text-gray-500 hover:text-white transition-colors text-sm font-bold uppercase tracking-widest">
                Ver todos los apartados
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
@endsection