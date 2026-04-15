@extends('layouts.tienda')

@section('title', 'Editar Apartado #' . $apartado->codigo_unico)

@section('content')
<div class="max-w-4xl mx-auto px-4">
    {{-- Navegación superior --}}
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('tienda.apartados.mostrar', $apartado->id) }}" class="text-gray-400 hover:text-blue-500 transition-colors flex items-center gap-2 group">
            <i class="fas fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
            <span class="font-medium">Volver al apartado</span>
        </a>
    </div>

    <div class="glass rounded-3xl overflow-hidden border border-white/10 shadow-2xl">
        {{-- Cabecera --}}
        <div class="px-8 py-6 border-b border-white/5 bg-white/5">
            <h1 class="text-3xl font-black text-white tracking-tight">
                Editar <span class="text-blue-500">#{{ $apartado->codigo_unico }}</span>
            </h1>
            <p class="text-gray-400 text-sm mt-1">Modifica los datos del cliente y fecha límite</p>
        </div>

        <div class="p-8">
            <form action="{{ route('tienda.apartados.actualizar', $apartado->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Datos del Cliente --}}
                <div class="bg-white/5 p-6 rounded-2xl border border-white/5 mb-6">
                    <h3 class="text-[10px] font-black text-blue-500 uppercase tracking-widest mb-4 flex items-center">
                        <i class="fas fa-user-circle mr-2 text-sm"></i> Datos del Cliente
                    </h3>
                    
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-400 text-sm mb-2">Nombre completo *</label>
                            <input type="text" name="nombre_cliente" 
                                   value="{{ old('nombre_cliente', $apartado->nombre_cliente) }}"
                                   class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 @error('nombre_cliente') border-red-500 @enderror"
                                   required>
                            @error('nombre_cliente')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-gray-400 text-sm mb-2">Teléfono *</label>
                            <input type="text" name="telefono_cliente" 
                                   value="{{ old('telefono_cliente', $apartado->telefono_cliente) }}"
                                   class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 @error('telefono_cliente') border-red-500 @enderror"
                                   required>
                            @error('telefono_cliente')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-gray-400 text-sm mb-2">Email (opcional)</label>
                            <input type="email" name="email_cliente" 
                                   value="{{ old('email_cliente', $apartado->email_cliente) }}"
                                   class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 @error('email_cliente') border-red-500 @enderror">
                            @error('email_cliente')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Fecha Límite --}}
                <div class="bg-white/5 p-6 rounded-2xl border border-white/5 mb-6">
                    <h3 class="text-[10px] font-black text-purple-500 uppercase tracking-widest mb-4 flex items-center">
                        <i class="fas fa-calendar-alt mr-2 text-sm"></i> Fecha de Vencimiento
                    </h3>
                    
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-400 text-sm mb-2">Fecha límite *</label>
                            <input type="date" name="fecha_limite" 
                                   value="{{ old('fecha_limite', \Carbon\Carbon::parse($apartado->fecha_limite)->format('Y-m-d')) }}"
                                   min="{{ now()->format('Y-m-d') }}"
                                   class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 @error('fecha_limite') border-red-500 @enderror"
                                   required>
                            @error('fecha_limite')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm mb-2">Días restantes</p>
                            <p class="text-white font-bold text-xl">{{ $apartado->dias_restantes }} días</p>
                        </div>
                    </div>
                </div>

                {{-- Notas --}}
                <div class="bg-white/5 p-6 rounded-2xl border border-white/5 mb-6">
                    <h3 class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-4 flex items-center">
                        <i class="fas fa-sticky-note mr-2 text-sm"></i> Notas (opcional)
                    </h3>
                    
                    <textarea name="notas" rows="3" 
                              class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50">{{ old('notas', $apartado->notas) }}</textarea>
                </div>

                {{-- Información de Productos (Solo lectura) --}}
                <div class="bg-white/5 p-6 rounded-2xl border border-white/5 mb-8">
                    <h3 class="text-[10px] font-black text-green-500 uppercase tracking-widest mb-4 flex items-center">
                        <i class="fas fa-box mr-2 text-sm"></i> Productos (no editables)
                    </h3>
                    
                    <div class="space-y-2">
                        @foreach($apartado->productos as $producto)
                        <div class="flex justify-between items-center p-3 bg-black/20 rounded-xl">
                            <div>
                                <span class="text-white font-bold">{{ $producto->nombre_producto }}</span>
                                <span class="text-gray-500 text-xs ml-2">x{{ $producto->cantidad }}</span>
                            </div>
                            <span class="text-white font-black">${{ number_format($producto->subtotal, 2) }}</span>
                        </div>
                        @endforeach
                        <div class="flex justify-between items-center p-3 bg-blue-500/10 rounded-xl mt-2">
                            <span class="text-blue-400 font-bold">TOTAL</span>
                            <span class="text-white font-black text-xl">${{ number_format($apartado->total, 2) }}</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-3 italic">Para modificar productos, crea un nuevo apartado</p>
                </div>

                {{-- Botones de acción --}}
                <div class="flex justify-end gap-4">
                    <a href="{{ route('tienda.apartados.mostrar', $apartado->id) }}" 
                       class="px-6 py-3 bg-gray-600/20 hover:bg-gray-600/30 text-white rounded-xl transition-all font-bold">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl transition-all font-bold flex items-center gap-2">
                        <i class="fas fa-save"></i> Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection