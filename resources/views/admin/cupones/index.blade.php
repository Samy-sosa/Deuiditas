@extends('layouts.admin')

@section('title', 'Gestión de Cupones')
@section('header', 'Cupones de Descuento')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('admin.cupones.crear') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i> Nuevo Cupón
            </a>
        </div>
        
        <form method="GET" class="flex gap-2">
            <input type="text" name="buscar" value="{{ request('buscar') }}" 
                   placeholder="Buscar por código..." 
                   class="border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="bg-gray-100 px-4 py-2 rounded-lg hover:bg-gray-200">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
    
    <div class="flex gap-2 mb-4">
        <a href="{{ route('admin.cupones') }}" class="px-3 py-1 rounded-full text-sm {{ !request('estado') ? 'bg-blue-600 text-white' : 'bg-gray-100' }}">
            Todos
        </a>
        <a href="?estado=activo" class="px-3 py-1 rounded-full text-sm {{ request('estado') == 'activo' ? 'bg-green-600 text-white' : 'bg-gray-100' }}">
            Activos
        </a>
        <a href="?estado=inactivo" class="px-3 py-1 rounded-full text-sm {{ request('estado') == 'inactivo' ? 'bg-red-600 text-white' : 'bg-gray-100' }}">
            Inactivos
        </a>
        <a href="?estado=expirado" class="px-3 py-1 rounded-full text-sm {{ request('estado') == 'expirado' ? 'bg-yellow-600 text-white' : 'bg-gray-100' }}">
            Expirados
        </a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 text-left">
                    <th class="px-4 py-3">Código</th>
                    <th class="px-4 py-3">Descripción</th>
                    <th class="px-4 py-3">Descuento</th>
                    <th class="px-4 py-3">Vigencia</th>
                    <th class="px-4 py-3">Usos</th>
                    <th class="px-4 py-3">Estado</th>
                    <th class="px-4 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cupones as $cupon)
                <tr class="border-t hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono font-bold">{{ $cupon->codigo }}</td>
                    <td class="px-4 py-3">{{ $cupon->descripcion ?: '-' }}</td>
                    <td class="px-4 py-3">
                        @if($cupon->tipo_descuento == 'porcentaje')
                            {{ $cupon->valor_descuento }}%
                        @else
                            ${{ number_format($cupon->valor_descuento, 2) }}
                        @endif
                        @if($cupon->monto_minimo)
                            <br><small class="text-gray-500">Mín: ${{ number_format($cupon->monto_minimo, 2) }}</small>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        {{ \Carbon\Carbon::parse($cupon->fecha_inicio)->format('d/m/Y') }}<br>
                        <span class="text-sm text-gray-500">hasta {{ \Carbon\Carbon::parse($cupon->fecha_expiracion)->format('d/m/Y') }}</span>
                    </td>
                    <td class="px-4 py-3">
                        {{ $cupon->usos_actuales }}/{{ $cupon->usos_maximos ?? '∞' }}
                    </td>
                    <td class="px-4 py-3">
                        @php
                            $hoy = now();
                            $activo = $cupon->activo && 
                                     $hoy >= $cupon->fecha_inicio && 
                                     $hoy <= $cupon->fecha_expiracion &&
                                     (!$cupon->usos_maximos || $cupon->usos_actuales < $cupon->usos_maximos);
                        @endphp
                        <span class="px-2 py-1 text-xs rounded-full {{ $activo ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.cupones.editar', $cupon->id) }}" 
                               class="text-blue-600 hover:text-blue-800" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('admin.cupones.estadisticas', $cupon->id) }}" 
                               class="text-green-600 hover:text-green-800" title="Estadísticas">
                                <i class="fas fa-chart-bar"></i>
                            </a>
                            <form action="{{ route('admin.cupones.toggle', $cupon->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-{{ $cupon->activo ? 'yellow' : 'green' }}-600 hover:text-{{ $cupon->activo ? 'yellow' : 'green' }}-800" 
                                        title="{{ $cupon->activo ? 'Desactivar' : 'Activar' }}">
                                    <i class="fas fa-{{ $cupon->activo ? 'ban' : 'check' }}"></i>
                                </button>
                            </form>
                            @if($cupon->usos->count() == 0)
                            <form action="{{ route('admin.cupones.eliminar', $cupon->id) }}" method="POST" 
                                  onsubmit="return confirm('¿Eliminar este cupón?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-8 text-gray-400">
                        <i class="fas fa-ticket-alt text-4xl mb-2"></i>
                        <p>No hay cupones registrados</p>
                        <a href="{{ route('admin.cupones.crear') }}" class="text-blue-600 hover:underline mt-2 inline-block">
                            Crear primer cupón
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $cupones->links() }}
    </div>
</div>
@endsection