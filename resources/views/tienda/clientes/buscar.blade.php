@extends('layouts.tienda')

@section('title', 'Buscar Cliente')

@section('content')
<div class="max-w-5xl mx-auto px-4">
    <div class="mb-6">
        <a href="{{ route('tienda.dashboard') }}" class="text-gray-400 hover:text-blue-500 transition flex items-center gap-2 group">
            <i class="fas fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
            <span class="font-medium">Volver al dashboard</span>
        </a>
    </div>

    <div class="glass rounded-3xl overflow-hidden border border-white/10 shadow-2xl">
        <div class="px-8 py-6 border-b border-white/5 bg-white/5">
            <h1 class="text-2xl font-black text-white">Buscar Cliente</h1>
            <p class="text-gray-400 text-sm mt-1">Ingresa el número de teléfono para buscar un cliente existente</p>
        </div>

        <div class="p-8">
            {{-- Formulario de búsqueda --}}
            <form method="GET" action="{{ route('tienda.clientes.buscar') }}" class="mb-8">
                <div class="flex gap-4">
                    <div class="flex-1">
                        <input type="tel" 
                               name="telefono" 
                               value="{{ request('telefono') }}"
                               placeholder="Ej: 9971510186"
                               class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                               required>
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-3 rounded-xl font-bold transition">
                        <i class="fas fa-search mr-2"></i> Buscar
                    </button>
                </div>
            </form>

            {{-- Resultados --}}
            @if(isset($no_encontrado) && $no_encontrado)
                <div class="text-center py-12">
                    <i class="fas fa-user-slash text-5xl text-gray-600 mb-4"></i>
                    <p class="text-gray-500">No se encontró ningún cliente con el teléfono <strong>{{ $telefono_buscado }}</strong></p>
                    <p class="text-gray-600 text-sm mt-2">Puedes crear un nuevo cliente desde el botón "Nuevo Registro"</p>
                    <a href="{{ route('tienda.apartados.crear') }}" class="inline-block mt-4 bg-emerald-600 hover:bg-emerald-500 text-white px-6 py-3 rounded-xl font-bold transition">
                        <i class="fas fa-plus-circle mr-2"></i> Crear nuevo cliente
                    </a>
                </div>
            @endif

            @if(isset($cliente))
                {{-- Datos del cliente --}}
                <div class="bg-gradient-to-r from-blue-600/20 to-purple-600/20 border border-blue-500/30 rounded-xl p-6 mb-6">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wider">Cliente</p>
                            <p class="text-2xl font-bold text-white">{{ $cliente->nombre }}</p>
                            <p class="text-sm text-gray-400">{{ $cliente->telefono }}</p>
                            @if($cliente->email)
                                <p class="text-sm text-gray-400">{{ $cliente->email }}</p>
                            @endif
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div>
                                <p class="text-xs text-gray-400">Total gastado</p>
                                <p class="text-xl font-bold text-white">${{ number_format($totalGastado, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Apartados</p>
                                <p class="text-xl font-bold text-white">{{ $apartados->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Apartado activo (si tiene) --}}
                @if($apartadoActivo)
                    <div class="bg-yellow-500/10 border border-yellow-500/30 rounded-xl p-4 mb-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-yellow-500 text-sm font-bold">
                                    <i class="fas fa-circle mr-2 text-[8px]"></i> TIENE UN APARTADO ACTIVO
                                </p>
                                <p class="text-white font-bold">#{{ $apartadoActivo->codigo_unico }}</p>
                                <p class="text-xs text-gray-400">Saldo pendiente: ${{ number_format($apartadoActivo->saldo_pendiente, 2) }}</p>
                            </div>
                            <a href="{{ route('tienda.apartados.mostrar', $apartadoActivo->id) }}" 
                               class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-xl text-sm transition">
                                Ver apartado
                            </a>
                        </div>
                    </div>
                @endif

                {{-- Botón para nuevo apartado --}}
                <div class="bg-emerald-500/10 border border-emerald-500/30 rounded-xl p-4 mb-8">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-emerald-500 text-sm font-bold">
                                <i class="fas fa-plus-circle mr-2"></i> CREAR NUEVO APARTADO
                            </p>
                            <p class="text-white text-sm mt-1">Los datos del cliente se precargarán automáticamente</p>
                        </div>
                        <form method="POST" action="{{ route('tienda.clientes.nuevo-apartado') }}">
                            @csrf
                            <input type="hidden" name="cliente_id" value="{{ $cliente->id }}">
                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white px-6 py-3 rounded-xl font-bold transition flex items-center gap-2">
                                <i class="fas fa-plus-circle"></i>
                                Nuevo apartado
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Historial de apartados --}}
                @if($apartados->count() > 0)
                    <div class="mt-6">
                        <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-4">Historial de apartados</h3>
                        <div class="space-y-3">
                            @foreach($apartados as $apartado)
                                <div class="bg-white/5 rounded-xl p-4 border border-white/10 hover:border-blue-500/30 transition">
                                    <div class="flex flex-col md:flex-row justify-between gap-4">
                                        <div>
                                            <div class="flex items-center gap-3 mb-2">
                                                <p class="text-white font-bold">
                                                    <span class="text-blue-500">#{{ $apartado->codigo_unico }}</span>
                                                </p>
                                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase
                                                    @if($apartado->estado == 'pagado') bg-green-500/20 text-green-500
                                                    @elseif($apartado->estado == 'activo') bg-yellow-500/20 text-yellow-500
                                                    @else bg-red-500/20 text-red-500 @endif">
                                                    {{ $apartado->estado }}
                                                </span>
                                            </div>
                                            <p class="text-xs text-gray-500">
                                                {{ $apartado->created_at->format('d/m/Y H:i') }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                Vencía: {{ \Carbon\Carbon::parse($apartado->fecha_limite)->format('d/m/Y') }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xl font-black text-white">${{ number_format($apartado->total, 2) }}</p>
                                            <p class="text-xs text-gray-500">
                                                Pagado: ${{ number_format($apartado->total_pagado, 2) }}
                                            </p>
                                            <a href="{{ route('tienda.apartados.mostrar', $apartado->id) }}" 
                                               class="text-blue-400 hover:text-blue-300 text-sm mt-2 inline-flex items-center gap-1">
                                                Ver detalles <i class="fas fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection