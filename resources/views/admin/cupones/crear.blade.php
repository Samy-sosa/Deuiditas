@extends('layouts.admin')

@section('title', 'Crear Cupón')
@section('header', 'Nuevo Cupón de Descuento')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6 max-w-2xl mx-auto">
    <form action="{{ route('admin.cupones.guardar') }}" method="POST">
        @csrf
        
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-2">Código del cupón *</label>
            <input type="text" name="codigo" value="{{ old('codigo') }}" required
                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                   placeholder="Ej: BIENVENIDA50">
            <p class="text-xs text-gray-400 mt-1">Se guardará en mayúsculas automáticamente</p>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-2">Descripción</label>
            <textarea name="descripcion" rows="2" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                      placeholder="Descripción del cupón...">{{ old('descripcion') }}</textarea>
        </div>
        
        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 font-medium mb-2">Tipo de descuento *</label>
                <select name="tipo_descuento" required class="w-full px-4 py-2 border rounded-lg">
                    <option value="porcentaje" {{ old('tipo_descuento') == 'porcentaje' ? 'selected' : '' }}>Porcentaje (%)</option>
                    <option value="fijo" {{ old('tipo_descuento') == 'fijo' ? 'selected' : '' }}>Monto fijo ($)</option>
                </select>
            </div>
            
            <div>
                <label class="block text-gray-700 font-medium mb-2">Valor del descuento *</label>
                <input type="number" step="0.01" name="valor_descuento" value="{{ old('valor_descuento') }}" required
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                       placeholder="0.00">
            </div>
        </div>
        
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-2">Monto mínimo de compra (opcional)</label>
            <input type="number" step="0.01" name="monto_minimo" value="{{ old('monto_minimo') }}"
                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                   placeholder="Dejar vacío si no hay mínimo">
        </div>
        
        <div class="grid md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 font-medium mb-2">Fecha de inicio *</label>
                <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio', date('Y-m-d')) }}" required
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-gray-700 font-medium mb-2">Fecha de expiración *</label>
                <input type="date" name="fecha_expiracion" value="{{ old('fecha_expiracion', date('Y-m-d', strtotime('+30 days'))) }}" required
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
        
        <div class="mb-6">
            <label class="block text-gray-700 font-medium mb-2">Usos máximos (opcional)</label>
            <input type="number" name="usos_maximos" value="{{ old('usos_maximos') }}" min="1"
                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500"
                   placeholder="Dejar vacío para usos ilimitados">
        </div>
        
        <div class="flex gap-4">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-save mr-2"></i> Guardar Cupón
            </button>
            <a href="{{ route('admin.cupones') }}" class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200 transition">
                Cancelar
            </a>
        </div>
    </form>
</div>
@endsection