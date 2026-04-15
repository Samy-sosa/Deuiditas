@extends('layouts.admin')

@section('title', 'Estadísticas del Cupón')
@section('header', 'Estadísticas: ' . $cupon->codigo)

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <p class="text-gray-500 text-sm">Total de usos</p>
        <p class="text-3xl font-bold">{{ $stats['total_usos'] }}</p>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <p class="text-gray-500 text-sm">Descuento total</p>
        <p class="text-3xl font-bold text-green-600">${{ number_format($stats['descuento_total'], 2) }}</p>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <p class="text-gray-500 text-sm">Monto original total</p>
        <p class="text-3xl font-bold">${{ number_format($stats['monto_total_original'], 2) }}</p>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-bold mb-4">Detalle de usos</h3>
    
    @if($cupon->usos->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left">Fecha</th>
                        <th class="px-4 py-2 text-left">Tienda</th>
                        <th class="px-4 py-2 text-left">Monto original</th>
                        <th class="px-4 py-2 text-left">Descuento</th>
                        <th class="px-4 py-2 text-left">Monto final</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cupon->usos as $uso)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $uso->fecha_uso->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-2">{{ $uso->tienda->nombre_tienda ?? 'N/A' }}</td>
                        <td class="px-4 py-2">${{ number_format($uso->monto_original, 2) }}</td>
                        <td class="px-4 py-2 text-green-600">${{ number_format($uso->descuento_aplicado, 2) }}</td>
                        <td class="px-4 py-2 font-bold">${{ number_format($uso->monto_final, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-center text-gray-400 py-4">Este cupón aún no ha sido usado</p>
    @endif
    
    <div class="mt-6">
        <a href="{{ route('admin.cupones') }}" class="text-blue-600 hover:underline">
            ← Volver a cupones
        </a>
    </div>
</div>
@endsection