@extends('layouts.admin')

@section('title', 'Gestionar Tiendas')
@section('header', 'Gestión de Tiendas')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-xl font-bold text-gray-800">Listado de Tiendas</h2>
        <p class="text-sm text-gray-500">Administra todas las tiendas del sistema</p>
    </div>
    <a href="{{ route('admin.tiendas.crear') }}" 
       class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2.5 rounded-xl transition flex items-center gap-2 shadow-lg hover:shadow-xl">
        <i class="fas fa-plus-circle"></i>
        Nueva Tienda
    </a>
</div>

@if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6 flex items-center">
        <i class="fas fa-check-circle mr-2"></i>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6 flex items-center">
        <i class="fas fa-exclamation-circle mr-2"></i>
        {{ session('error') }}
    </div>
@endif

<!-- Filtros -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px]">
            <input type="text" 
                   name="buscar" 
                   placeholder="Buscar por nombre, email o iniciales..." 
                   value="{{ request('buscar') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
        </div>
        <div class="w-48">
            <select name="estado" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                <option value="">Todos los estados</option>
                <option value="activa" {{ request('estado') == 'activa' ? 'selected' : '' }}>Activas</option>
                <option value="suspendida" {{ request('estado') == 'suspendida' ? 'selected' : '' }}>Suspendidas</option>
                <option value="cancelada" {{ request('estado') == 'cancelada' ? 'selected' : '' }}>Canceladas</option>
            </select>
        </div>
        <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
            <i class="fas fa-search mr-2"></i>Filtrar
        </button>
        @if(request()->has('buscar') || request()->has('estado'))
        <a href="{{ route('admin.tiendas') }}" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
            <i class="fas fa-times mr-2"></i>Limpiar
        </a>
        @endif
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">ID</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Tienda</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Iniciales</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Email</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Teléfono</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Estado</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Renovación</th>
                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($tiendas as $tienda)
            <tr class="hover:bg-purple-50 transition">
                <td class="px-6 py-4 font-mono text-sm">#{{ $tienda->id }}</td>
                <td class="px-6 py-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center text-white font-bold text-xs">
                            {{ substr($tienda->nombre_tienda, 0, 2) }}
                        </div>
                        <span class="font-medium">{{ $tienda->nombre_tienda }}</span>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="font-mono bg-gray-100 px-2 py-1 rounded text-sm">{{ $tienda->iniciales }}</span>
                </td>
                <td class="px-6 py-4 text-sm">{{ $tienda->email }}</td>
                <td class="px-6 py-4 text-sm">{{ $tienda->telefono }}</td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                        @if($tienda->estado == 'activa') bg-green-100 text-green-700
                        @elseif($tienda->estado == 'suspendida') bg-yellow-100 text-yellow-700
                        @else bg-red-100 text-red-700
                        @endif">
                        <i class="fas fa-circle text-xs mr-1 
                            @if($tienda->estado == 'activa') text-green-500
                            @elseif($tienda->estado == 'suspendida') text-yellow-500
                            @else text-red-500
                            @endif"></i>
                        {{ ucfirst($tienda->estado) }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    @php
                        $dias = $tienda->dias_restantes ?? 0;
                        $color = $dias <= 0 ? 'red' : ($dias <= 7 ? 'yellow' : 'green');
                    @endphp
                    <span class="text-xs text-{{ $color }}-600 font-medium">
                        {{ $dias }} días
                    </span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.tiendas.ver', $tienda->id) }}" 
                           class="text-blue-600 hover:text-blue-800 p-2 hover:bg-blue-50 rounded-lg transition" 
                           title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </a>
                        
                        @if($tienda->estado == 'activa')
                        {{-- Usar ruta de suspender --}}
                        <form action="{{ route('admin.tiendas.suspender', $tienda->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="text-yellow-600 hover:text-yellow-800 p-2 hover:bg-yellow-50 rounded-lg transition"
                                    title="Suspender"
                                    onclick="return confirm('¿Suspender esta tienda?')">
                                <i class="fas fa-pause"></i>
                            </button>
                        </form>
                        @else
                        {{-- Usar ruta de activar --}}
                        <form action="{{ route('admin.tiendas.activar', $tienda->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="text-green-600 hover:text-green-800 p-2 hover:bg-green-50 rounded-lg transition"
                                    title="Activar"
                                    onclick="return confirm('¿Activar esta tienda?')">
                                <i class="fas fa-play"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="py-12 text-center text-gray-500">
                    <i class="fas fa-store fa-3x mb-3 opacity-30"></i>
                    <p>No hay tiendas registradas</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <!-- Paginación -->
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $tiendas->links() }}
    </div>
</div>
@endsection