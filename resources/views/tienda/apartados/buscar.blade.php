@extends('layouts.tienda')

@section('title', 'Resultados de búsqueda')

@section('content')
<div class="max-w-7xl mx-auto px-4">
    
    {{-- Header --}}
    <div class="mb-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <div>
                <h1 class="text-4xl font-extrabold text-white tracking-tight">Resultados de búsqueda</h1>
                <p class="text-gray-500 text-sm mt-1">
                    @if(isset($resultados) && $resultados->total() > 0)
                        Se encontraron <span class="text-blue-500 font-bold">{{ $resultados->total() }}</span> coincidencias para "{{ $termino }}"
                    @else
                        No se encontraron resultados para "{{ $termino }}"
                    @endif
                </p>
            </div>
            
            {{-- Buscador --}}
            <div class="w-full md:w-auto">
                <form action="{{ route('tienda.apartados.buscar') }}" method="GET" class="flex gap-2">
                    <div class="relative flex-1 md:w-80">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" 
                               name="q" 
                               value="{{ $termino ?? request('q') }}"
                               placeholder="Buscar por código o cliente..." 
                               class="w-full bg-white/5 border border-white/10 rounded-xl pl-10 pr-4 py-3 text-white placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all">
                    </div>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-3 rounded-xl font-bold transition-all">
                        Buscar
                    </button>
                </form>
            </div>
        </div>

        {{-- Botón volver --}}
        <a href="{{ route('tienda.dashboard') }}" class="inline-flex items-center gap-2 text-gray-400 hover:text-white transition-colors text-sm">
            <i class="fas fa-arrow-left"></i>
            Volver al dashboard
        </a>
    </div>

    {{-- Resultados --}}
    @if(isset($resultados) && $resultados->count() > 0)
        <div class="glass rounded-[2.5rem] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-white/[0.02] border-b border-white/5">
                        <tr class="text-[10px] font-black uppercase tracking-widest text-gray-500">
                            <th class="px-6 py-4">Código</th>
                            <th class="px-6 py-4">Cliente</th>
                            <th class="px-6 py-4">Teléfono</th>
                            <th class="px-6 py-4">Productos</th>
                            <th class="px-6 py-4">Saldo</th>
                            <th class="px-6 py-4">Estado</th>
                            <th class="px-6 py-4 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @foreach($resultados as $apartado)
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-mono text-blue-400 font-bold">#{{ $apartado->codigo_unico }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-white">{{ $apartado->nombre_cliente }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-400">{{ $apartado->telefono_cliente }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @if($apartado->productos && $apartado->productos->count() > 0)
                                    <span class="text-sm text-gray-300">{{ $apartado->productos->first()->nombre_producto }}</span>
                                    @if($apartado->productos->count() > 1)
                                        <span class="text-xs text-gray-600 ml-1">+{{ $apartado->productos->count() - 1 }}</span>
                                    @endif
                                @else
                                    <span class="text-sm text-gray-600">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold {{ $apartado->saldo_pendiente > 0 ? 'text-red-400' : 'text-emerald-400' }}">
                                    ${{ number_format($apartado->saldo_pendiente, 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 text-[9px] font-black uppercase rounded-full
                                    @if($apartado->estado == 'activo') bg-yellow-500/10 text-yellow-500
                                    @elseif($apartado->estado == 'pagado') bg-emerald-500/10 text-emerald-500
                                    @else bg-red-500/10 text-red-500 @endif">
                                    {{ $apartado->estado }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('tienda.apartados.mostrar', $apartado->id) }}" 
                                   class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-xl text-xs font-bold transition-all">
                                    Ver detalles
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{-- Paginación --}}
            @if($resultados->hasPages())
            <div class="px-6 py-4 border-t border-white/5">
                {{ $resultados->links() }}
            </div>
            @endif
        </div>
    @elseif(isset($termino))
        {{-- Sin resultados --}}
        <div class="glass rounded-[2.5rem] p-12 text-center">
            <div class="w-24 h-24 mx-auto mb-6 bg-white/5 rounded-full flex items-center justify-center">
                <i class="fas fa-search text-4xl text-gray-600"></i>
            </div>
            <h2 class="text-2xl font-black text-white mb-2">No hay resultados</h2>
            <p class="text-gray-500 mb-6">No se encontraron apartados que coincidan con "{{ $termino }}"</p>
            <a href="{{ route('tienda.apartados.crear') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white px-6 py-3 rounded-xl font-bold transition-all">
                <i class="fas fa-plus"></i>
                Crear nuevo apartado
            </a>
        </div>
    @endif
</div>
@endsection