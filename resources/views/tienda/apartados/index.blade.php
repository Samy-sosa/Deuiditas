@extends('layouts.tienda')

@section('title', 'Gestión de Apartados')

@push('styles')
<style>
    .progress-bar {
        transition: width 1s ease-in-out;
    }
    
    /* Botones fijos sin efecto hover de ocultar */
    .action-buttons {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }
    
    .action-btn {
        width: 36px;
        height: 36px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        font-size: 14px;
        text-decoration: none;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
    }
    
    .btn-add-product {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }
    
    .btn-add-product:hover {
        background: #10b981;
        color: white;
        border-color: #10b981;
    }
    
    .btn-payment {
        background: rgba(59, 130, 246, 0.15);
        color: #3b82f6;
        border: 1px solid rgba(59, 130, 246, 0.3);
    }
    
    .btn-payment:hover {
        background: #3b82f6;
        color: white;
        border-color: #3b82f6;
    }
    
    .btn-edit {
        background: rgba(139, 92, 246, 0.15);
        color: #8b5cf6;
        border: 1px solid rgba(139, 92, 246, 0.3);
    }
    
    .btn-edit:hover {
        background: #8b5cf6;
        color: white;
        border-color: #8b5cf6;
    }
    
    /* Botón deshabilitado */
    .btn-disabled {
        background: rgba(255, 255, 255, 0.05);
        color: #4b5563;
        border: 1px solid rgba(255, 255, 255, 0.1);
        cursor: not-allowed;
    }
    
    .btn-disabled:hover {
        transform: none;
        background: rgba(255, 255, 255, 0.05);
        color: #4b5563;
    }
    
    /* Tooltip en móvil */
    @media (max-width: 768px) {
        .action-btn {
            width: 40px;
            height: 40px;
        }
    }
</style>
@endpush

@section('content')
<div class="space-y-8">
    {{-- Notificaciones --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-500/20 border-l-4 border-emerald-500 rounded-xl flex items-center text-white animate-pulse">
            <i class="fas fa-check-circle mr-3 text-emerald-500"></i>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Header de la Sección --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h1 class="text-4xl font-extrabold text-white tracking-tight">Gestión de Apartados</h1>
            <p class="text-gray-500 text-sm font-medium mt-1 uppercase tracking-widest">Listado general de clientes y saldos</p>
        </div>
        
        <div class="flex items-center gap-4 w-full md:w-auto">
            <a href="{{ route('tienda.apartados.crear') }}" class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-3 rounded-2xl transition-all shadow-lg shadow-blue-600/20 flex items-center gap-2 text-sm font-bold whitespace-nowrap">
                <i class="fas fa-plus"></i>
                <span>NUEVO REGISTRO</span>
            </a>
        </div>
    </div>

    {{-- LISTADO COMPLETO --}}
    <div class="glass rounded-[2.5rem] p-8 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-separate border-spacing-y-2">
                <thead>
                    <tr class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-500">
                        <th class="px-4 pb-4">Código</th>
                        <th class="px-4 pb-4">Cliente / Contacto</th>
                        <th class="px-4 pb-4">Detalle Productos</th>
                        <th class="px-4 pb-4">Total Bruto</th>
                        <th class="px-4 pb-4">Saldo Pendiente</th>
                        <th class="px-4 pb-4 text-center">Estado</th>
                        <th class="px-4 pb-4 text-center">Acciones</th>
                     </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @foreach($apartados as $a)
                    <tr class="hover:bg-white/[0.02] transition-colors">
                        <td class="px-4 py-5 font-mono text-blue-400 font-bold">#{{ $a->codigo_unico }}</td>
                        <td class="px-4 py-5">
                            <p class="text-sm font-bold text-white uppercase">{{ $a->nombre_cliente }}</p>
                            <p class="text-[10px] text-gray-500 tracking-wider">{{ $a->telefono_cliente }}</p>
                        </td>
                        <td class="px-4 py-5">
                            @if($a->productos && $a->productos->count() > 0)
                                <div class="flex flex-col gap-1">
                                    @foreach($a->productos->take(2) as $producto)
                                        <span class="text-[11px] text-gray-300 font-medium truncate max-w-[200px]">
                                            <i class="fas fa-caret-right text-blue-500 mr-1"></i>
                                            {{ $producto->nombre_producto }}
                                        </span>
                                    @endforeach
                                    @if($a->productos->count() > 2)
                                        <span class="text-[9px] text-gray-500 font-black uppercase ml-4">+ {{ $a->productos->count() - 2 }} más</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-xs text-gray-600 italic">Sin productos</span>
                            @endif
                        </td>
                        <td class="px-4 py-5 text-sm font-bold text-gray-400">${{ number_format($a->total, 2) }}</td>
                        <td class="px-4 py-5 text-sm font-black">
                            <span class="{{ $a->saldo_pendiente > 0 ? 'text-red-400' : 'text-emerald-400' }}">
                                ${{ number_format($a->saldo_pendiente, 2) }}
                            </span>
                        </td>
                        <td class="px-4 py-5 text-center">
                            <span class="px-3 py-1 text-[9px] font-black uppercase rounded-full 
                                @switch($a->estado)
                                    @case('activo') bg-yellow-500/10 text-yellow-500 @break
                                    @case('pagado') bg-emerald-500/10 text-emerald-500 @break
                                    @default bg-red-500/10 text-red-500
                                @endswitch">
                                {{ $a->estado }}
                            </span>
                        </td>
                        <td class="px-4 py-5">
                            <div class="action-buttons">
                                {{-- Botón Agregar Productos (solo si está activo) --}}
                                @if($a->estado == 'activo')
                                    <a href="{{ route('tienda.apartados.agregar-producto', $a->id) }}" 
                                       class="action-btn btn-add-product" 
                                       title="Agregar productos">
                                        <i class="fas fa-plus-circle"></i>
                                    </a>
                                    
                                    {{-- Botón Registrar Abono (solo si tiene saldo pendiente) --}}
                                    @if($a->saldo_pendiente > 0)
                                        <a href="{{ route('tienda.apartados.mostrar', $a->id) }}#pago" 
                                           class="action-btn btn-payment" 
                                           title="Registrar abono">
                                            <i class="fas fa-dollar-sign"></i>
                                        </a>
                                    @endif
                                @endif
                                
                                {{-- Botón Editar (si no está pagado) --}}
                                @if($a->estado != 'pagado')
                                    <a href="{{ route('tienda.apartados.editar', $a->id) }}" 
                                       class="action-btn btn-edit" 
                                       title="Editar apartado">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif
                                
                                {{-- Si no hay botones disponibles, mostrar un placeholder vacío --}}
                                @if($a->estado != 'activo' && $a->estado == 'pagado')
                                    <span class="action-btn btn-disabled" title="Apartado pagado">
                                        <i class="fas fa-check-circle"></i>
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        {{-- Paginación personalizada --}}
        <div class="mt-8 pt-8 border-t border-white/5">
            {{ $apartados->links() }}
        </div>
    </div>
</div>
@endsection