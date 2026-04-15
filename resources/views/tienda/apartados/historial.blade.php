@extends('layouts.tienda')

@section('title', 'Historial de ' . $cliente->nombre)

@section('content')
<div class="max-w-6xl mx-auto px-4">
    <div class="mb-6">
        <a href="{{ route('tienda.clientes.buscar') }}" class="text-gray-400 hover:text-blue-500 transition flex items-center gap-2 group">
            <i class="fas fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
            <span class="font-medium">Volver a buscar clientes</span>
        </a>
    </div>

    <div class="glass rounded-3xl overflow-hidden border border-white/10 shadow-2xl">
        <div class="px-8 py-6 border-b border-white/5 bg-white/5">
            <h1 class="text-2xl font-black text-white">Historial de {{ $cliente->nombre }}</h1>
            <div class="flex flex-wrap gap-4 mt-2">
                <p class="text-sm text-gray-400"><i class="fas fa-phone-alt mr-2"></i>{{ $cliente->telefono }}</p>
                @if($cliente->email)
                    <p class="text-sm text-gray-400"><i class="fas fa-envelope mr-2"></i>{{ $cliente->email }}</p>
                @endif
            </div>
        </div>

        <div class="p-8">
            {{-- Resumen de estadísticas --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white/5 rounded-xl p-4 text-center">
                    <p class="text-xs text-gray-400 uppercase">Total apartados</p>
                    <p class="text-2xl font-bold text-white">{{ $apartados->total() }}</p>
                </div>
                <div class="bg-white/5 rounded-xl p-4 text-center">
                    <p class="text-xs text-gray-400 uppercase">Total gastado</p>
                    <p class="text-2xl font-bold text-white">${{ number_format($totalGastado, 2) }}</p>
                </div>
                <div class="bg-white/5 rounded-xl p-4 text-center">
                    <p class="text-xs text-gray-400 uppercase">Total pagado</p>
                    <p class="text-2xl font-bold text-emerald-500">${{ number_format($totalPagado, 2) }}</p>
                </div>
                <div class="bg-white/5 rounded-xl p-4 text-center">
                    <p class="text-xs text-gray-400 uppercase">Saldo pendiente</p>
                    <p class="text-2xl font-bold text-red-500">${{ number_format($totalGastado - $totalPagado, 2) }}</p>
                </div>
            </div>

            {{-- Lista de apartados --}}
            <div class="space-y-4">
                @foreach($apartados as $apartado)
                    <div class="bg-white/5 rounded-2xl p-5 border border-white/10 hover:border-blue-500/30 transition">
                        <div class="flex flex-col md:flex-row justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2 flex-wrap">
                                    <p class="text-white font-bold text-lg">
                                        #{{ $apartado->codigo_unico }}
                                    </p>
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase
                                        @if($apartado->estado == 'pagado') bg-green-500/20 text-green-500
                                        @elseif($apartado->estado == 'activo') bg-yellow-500/20 text-yellow-500
                                        @else bg-red-500/20 text-red-500 @endif">
                                        {{ $apartado->estado }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500">
                                    <i class="fas fa-calendar-alt mr-1"></i> Creado: {{ $apartado->created_at->format('d/m/Y H:i') }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    <i class="fas fa-hourglass-end mr-1"></i> Vencía: {{ \Carbon\Carbon::parse($apartado->fecha_limite)->format('d/m/Y') }}
                                </p>
                                
                                {{-- Productos del apartado --}}
                                <div class="mt-3">
                                    <p class="text-[10px] text-gray-500 uppercase tracking-widest mb-2">Productos</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($apartado->productos as $producto)
                                            <span class="text-xs bg-white/5 px-2 py-1 rounded-full">
                                                {{ $producto->cantidad }}x {{ $producto->nombre_producto }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="text-right md:text-left">
                                <p class="text-2xl font-black text-white">${{ number_format($apartado->total, 2) }}</p>
                                <p class="text-xs text-emerald-500">Pagado: ${{ number_format($apartado->total_pagado, 2) }}</p>
                                @if($apartado->saldo_pendiente > 0)
                                    <p class="text-xs text-red-500">Falta: ${{ number_format($apartado->saldo_pendiente, 2) }}</p>
                                @endif
                                <a href="{{ route('tienda.apartados.mostrar', $apartado->id) }}" 
                                   class="text-blue-400 hover:text-blue-300 text-sm mt-2 inline-flex items-center gap-1">
                                    Ver detalles <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $apartados->links() }}
            </div>
        </div>
    </div>
</div>
@endsection