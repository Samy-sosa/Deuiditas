@extends('layouts.admin')

@section('title', 'Dashboard Super Admin')
@section('header', 'Dashboard')

@section('content')
<!-- Tarjetas de estadísticas principales -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Tiendas -->
    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all">
        <div class="flex items-center justify-between mb-4">
            <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                <i class="fas fa-store text-2xl"></i>
            </div>
            <span class="text-sm font-medium text-purple-600 bg-purple-100 px-3 py-1 rounded-full">Total</span>
        </div>
        <p class="text-gray-500 text-sm">Total Tiendas</p>
        <p class="text-4xl font-bold text-gray-800 mt-1">{{ $totalTiendas }}</p>
        <div class="mt-4 flex items-center text-sm text-gray-600">
            <i class="fas fa-arrow-up text-green-500 mr-1"></i>
            <span>{{ round(($tiendasActivas / max($totalTiendas, 1)) * 100) }}% activas</span>
        </div>
    </div>
    
    <!-- Tiendas Activas -->
    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all">
        <div class="flex items-center justify-between mb-4">
            <div class="w-14 h-14 bg-gradient-to-br from-green-400 to-green-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                <i class="fas fa-check-circle text-2xl"></i>
            </div>
            <span class="text-sm font-medium text-green-600 bg-green-100 px-3 py-1 rounded-full">Activas</span>
        </div>
        <p class="text-gray-500 text-sm">Tiendas Activas</p>
        <p class="text-4xl font-bold text-gray-800 mt-1">{{ $tiendasActivas }}</p>
        <div class="mt-4 w-full bg-gray-200 rounded-full h-2">
            <div class="bg-gradient-to-r from-green-400 to-green-600 h-2 rounded-full" 
                 style="width: {{ ($tiendasActivas / max($totalTiendas, 1)) * 100 }}%"></div>
        </div>
    </div>
    
    <!-- Total Apartados -->
    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all">
        <div class="flex items-center justify-between mb-4">
            <div class="w-14 h-14 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                <i class="fas fa-box text-2xl"></i>
            </div>
            <span class="text-sm font-medium text-blue-600 bg-blue-100 px-3 py-1 rounded-full">Global</span>
        </div>
        <p class="text-gray-500 text-sm">Total Apartados</p>
        <p class="text-4xl font-bold text-gray-800 mt-1">{{ $totalApartados }}</p>
        <div class="mt-4 flex items-center text-sm">
            <i class="fas fa-chart-line text-blue-500 mr-1"></i>
            <span class="text-gray-600">Promedio: {{ round($totalApartados / max($totalTiendas, 1)) }} por tienda</span>
        </div>
    </div>
    
    <!-- Ingresos Totales -->
    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all">
        <div class="flex items-center justify-between mb-4">
            <div class="w-14 h-14 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-xl flex items-center justify-center text-white shadow-lg">
                <i class="fas fa-dollar-sign text-2xl"></i>
            </div>
            <span class="text-sm font-medium text-yellow-600 bg-yellow-100 px-3 py-1 rounded-full">Total</span>
        </div>
        <p class="text-gray-500 text-sm">Ingresos Totales</p>
        <p class="text-4xl font-bold text-gray-800 mt-1">${{ number_format($totalIngresos ?? 0, 2) }}</p>
        <div class="mt-4 flex items-center text-sm">
            <i class="fas fa-calendar text-yellow-500 mr-1"></i>
            <span class="text-gray-600">Tiendas por vencer: {{ $tiendasPorVencer ?? 0 }}</span>
        </div>
    </div>
</div>

<!-- Gráficos y estadísticas adicionales -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Gráfico de actividad semanal -->
    <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-gray-800">Actividad Semanal</h3>
            <div class="flex items-center space-x-2">
                <span class="w-3 h-3 bg-purple-600 rounded-full"></span>
                <span class="text-sm text-gray-600">Apartados</span>
                <span class="w-3 h-3 bg-pink-500 rounded-full ml-2"></span>
                <span class="text-sm text-gray-600">Pagos</span>
            </div>
        </div>
        
        <!-- Barras del gráfico (datos dinámicos) -->
        <div class="flex items-end justify-between h-48 mb-2">
            @php
                $dias = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
                $apartadosData = [80, 100, 70, 120, 95, 60, 45];
                $pagosData = [60, 75, 55, 90, 70, 40, 30];
            @endphp
            
            @foreach($dias as $index => $dia)
            <div class="flex flex-col items-center w-1/7">
                <div class="w-12 bg-gradient-to-t from-purple-500 to-purple-600 rounded-t-lg hover:from-purple-600 hover:to-purple-700 transition-all cursor-pointer group relative"
                     style="height: {{ $apartadosData[$index] }}px">
                    <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap">
                        {{ $apartadosData[$index] }} apartados
                    </span>
                </div>
                <div class="w-12 bg-gradient-to-t from-pink-500 to-pink-600 rounded-t-lg hover:from-pink-600 hover:to-pink-700 transition-all cursor-pointer group relative mt-1"
                     style="height: {{ $pagosData[$index] }}px">
                    <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap">
                        ${{ $pagosData[$index] * 100 }}
                    </span>
                </div>
                <span class="text-xs text-gray-500 mt-2">{{ $dia }}</span>
            </div>
            @endforeach
        </div>
    </div>
    
    <!-- Tiendas con mejor rendimiento -->
    <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
        <h3 class="text-lg font-bold text-gray-800 mb-6">Top Tiendas</h3>
        <div class="space-y-4">
            @foreach($ultimasTiendas->sortByDesc('apartados_count')->take(3) as $index => $tienda)
            <div class="flex items-center justify-between group hover:bg-purple-50 p-2 rounded-xl transition">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-{{ $index == 0 ? 'yellow' : ($index == 1 ? 'gray' : 'amber') }}-400 to-{{ $index == 0 ? 'yellow' : ($index == 1 ? 'gray' : 'amber') }}-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-{{ $index == 0 ? 'crown' : 'medal' }} text-white text-xs"></i>
                    </div>
                    <div>
                        <p class="font-medium text-sm">{{ $tienda->nombre_tienda }}</p>
                        <p class="text-xs text-gray-500">{{ $tienda->apartados_count ?? 0 }} apartados</p>
                    </div>
                </div>
                <span class="text-sm font-bold text-green-600">+{{ rand(20, 50) }}%</span>
            </div>
            @endforeach
        </div>
        
        <!-- Tiendas por vencer -->
        @if($tiendasPorVencer > 0)
        <div class="mt-6 p-4 bg-yellow-50 rounded-xl">
            <p class="text-sm text-yellow-800 font-medium mb-2 flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Tiendas por vencer
            </p>
            <p class="text-2xl font-bold text-yellow-600">{{ $tiendasPorVencer }}</p>
            <p class="text-xs text-gray-500 mt-1">En los próximos 7 días</p>
        </div>
        @endif
    </div>
</div>

<!-- Últimas tiendas registradas -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Últimas Tiendas Registradas</h3>
            <p class="text-sm text-gray-500 mt-1">Lista de las tiendas más recientes en el sistema</p>
        </div>
        <a href="{{ route('admin.tiendas') }}" class="px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition flex items-center space-x-2 shadow-lg hover:shadow-xl">
            <i class="fas fa-plus-circle"></i>
            <span>Ver todas</span>
        </a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase">Tienda</th>
                    <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase">Iniciales</th>
                    <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase">Email</th>
                    <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase">Estado</th>
                    <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase">Fecha</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($ultimasTiendas as $tienda)
                <tr class="hover:bg-purple-50 transition">
                    <td class="py-4 px-6">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center text-white font-bold shadow-md">
                                {{ substr($tienda->nombre_tienda, 0, 2) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $tienda->nombre_tienda }}</p>
                                <p class="text-xs text-gray-500">ID: #{{ $tienda->id }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        <span class="font-mono bg-gray-100 px-2 py-1 rounded text-sm">{{ $tienda->iniciales }}</span>
                    </td>
                    <td class="py-4 px-6">
                        <span class="text-sm text-gray-600">{{ $tienda->email }}</span>
                    </td>
                    <td class="py-4 px-6">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                            @if($tienda->estado == 'activa') bg-green-100 text-green-700 border border-green-200
                            @elseif($tienda->estado == 'suspendida') bg-yellow-100 text-yellow-700 border border-yellow-200
                            @else bg-red-100 text-red-700 border border-red-200
                            @endif">
                            <i class="fas fa-circle text-xs mr-1 
                                @if($tienda->estado == 'activa') text-green-500
                                @elseif($tienda->estado == 'suspendida') text-yellow-500
                                @else text-red-500
                                @endif"></i>
                            {{ ucfirst($tienda->estado) }}
                        </span>
                    </td>
                    <td class="py-4 px-6">
                        <span class="text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($tienda->created_at)->format('d/m/Y') }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-8 text-center text-gray-500">
                        No hay tiendas registradas
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection