{{-- resources/views/tienda/configuracion/index.blade.php --}}
@extends('layouts.tienda')

@section('title', 'Configuración de la Tienda')

@section('content')
<div class="max-w-4xl mx-auto">
    <header class="mb-10">
        <h1 class="text-3xl font-black text-white tracking-tight">Configuración</h1>
        <p class="text-gray-400 mt-1">Personaliza la identidad y los datos operativos de tu negocio.</p>
    </header>

    @if(session('success'))
        <div class="mb-8 p-4 bg-green-500/10 border border-green-500/20 text-green-400 rounded-2xl flex items-center">
            <i class="fas fa-check-circle mr-3 text-xl"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <form action="{{ route('tienda.configuracion.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        
        {{-- Información de la Tienda --}}
        <section class="glass rounded-3xl p-8 border-white/5 shadow-2xl">
            <h2 class="text-sm font-black text-blue-500 uppercase tracking-widest mb-6 flex items-center">
                <i class="fas fa-info-circle mr-3"></i> Información de la Tienda
            </h2>
            
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-400 text-xs font-bold uppercase mb-2 ml-1">Nombre Comercial *</label>
                    <input type="text" name="nombre_tienda" required
                           class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                           value="{{ old('nombre_tienda', $tienda->nombre_tienda) }}">
                </div>
                
                <div>
                    <label class="block text-gray-400 text-xs font-bold uppercase mb-2 ml-1">Teléfono de Atención</label>
                    <input type="text" name="telefono_contacto"
                           class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                           value="{{ old('telefono_contacto', $tienda->telefono_contacto) }}">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-gray-400 text-xs font-bold uppercase mb-2 ml-1">Dirección Física</label>
                    <textarea name="direccion" rows="2"
                              class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">{{ old('direccion', $tienda->direccion) }}</textarea>
                </div>
            </div>
        </section>

        {{-- Datos Bancarios --}}
        <section class="glass rounded-3xl p-8 border-white/5 shadow-2xl">
            <h2 class="text-sm font-black text-emerald-500 uppercase tracking-widest mb-2 flex items-center">
                <i class="fas fa-credit-card mr-3"></i> Datos para Transferencias
            </h2>
            <p class="text-xs text-gray-500 mb-6">Completa estos datos para que tus clientes puedan pagar por transferencia bancaria.</p>
            
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-400 text-xs font-bold uppercase mb-2 ml-1">Institución Bancaria</label>
                    <input type="text" 
                           name="banco" 
                           class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           value="{{ old('banco', $tienda->banco) }}"
                           placeholder="Ej: BBVA, Santander, etc.">
                </div>
                
                <div>
                    <label class="block text-gray-400 text-xs font-bold uppercase mb-2 ml-1">CLABE Interbancaria <span class="text-blue-400">(18 dígitos)</span></label>
                    <input type="text" 
                           name="clabe" 
                           maxlength="18" 
                           class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 font-mono" 
                           value="{{ old('clabe', $tienda->clabe) }}"
                           placeholder="012345678901234567"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-gray-400 text-xs font-bold uppercase mb-2 ml-1">Nombre del Titular (como aparece en la cuenta)</label>
                    <input type="text" 
                           name="titular" 
                           class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           value="{{ old('titular', $tienda->titular) }}"
                           placeholder="Ej: Juan Pérez García">
                </div>
                
                <div>
                    <label class="block text-gray-400 text-xs font-bold uppercase mb-2 ml-1">Número de Cuenta (opcional)</label>
                    <input type="text" 
                           name="cuenta" 
                           class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           value="{{ old('cuenta', $tienda->cuenta) }}"
                           placeholder="Ej: 1234567890">
                </div>
                
                <div>
                    <label class="block text-gray-400 text-xs font-bold uppercase mb-2 ml-1">RFC (opcional)</label>
                    <input type="text" 
                           name="rfc" 
                           maxlength="13"
                           class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 uppercase" 
                           value="{{ old('rfc', $tienda->rfc) }}"
                           placeholder="XXXX000101XXX">
                </div>
            </div>
            
            @if($tienda->clabe && $tienda->banco && $tienda->titular)
            <div class="mt-4 p-3 bg-green-500/10 border border-green-500/20 rounded-xl">
                <p class="text-xs text-green-400 flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    Datos bancarios completos. Tus clientes podrán pagar por transferencia.
                </p>
            </div>
            @else
            <div class="mt-4 p-3 bg-yellow-500/10 border border-yellow-500/20 rounded-xl">
                <p class="text-xs text-yellow-400 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Completa al menos banco, CLABE y titular para activar el botón de pago por transferencia.
                </p>
            </div>
            @endif
        </section>

        {{-- Personalización de Ticket --}}
        <section class="glass rounded-3xl p-8 border-white/5 shadow-2xl">
            <h2 class="text-sm font-black text-purple-500 uppercase tracking-widest mb-6 flex items-center">
                <i class="fas fa-ticket-alt mr-3"></i> Personalización de Ticket
            </h2>
            
            <div class="space-y-6">
                <div>
                    <label class="block text-gray-400 text-xs font-bold uppercase mb-2 ml-1">Mensaje al pie del ticket</label>
                    <textarea name="ticket_mensaje" rows="2" 
                              class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-blue-500" 
                              placeholder="Ej: ¡Gracias por confiar en nosotros!">{{ old('ticket_mensaje', $tienda->ticket_mensaje) }}</textarea>
                </div>
                
                <label class="flex items-center p-4 bg-purple-500/5 border border-purple-500/20 rounded-2xl cursor-pointer hover:bg-purple-500/10 transition-all">
                    <input type="checkbox" name="ticket_mostrar_logo" value="1" {{ $tienda->ticket_mostrar_logo ? 'checked' : '' }}
                           class="w-5 h-5 rounded border-white/10 bg-white/5 text-purple-600 focus:ring-purple-500">
                    <span class="ml-4 text-sm font-semibold text-purple-200">Incluir logotipo en los tickets</span>
                </label>
            </div>
        </section>

        {{-- Identidad Visual --}}
        <section class="glass rounded-3xl p-8 border-white/5 shadow-2xl">
            <h2 class="text-sm font-black text-orange-500 uppercase tracking-widest mb-6 flex items-center">
                <i class="fas fa-image mr-3"></i> Identidad Visual
            </h2>
            
            <div class="flex flex-col md:flex-row items-center gap-8">
                <div class="relative">
                    <div class="w-32 h-32 bg-white/5 rounded-3xl border-2 border-dashed border-white/10 flex items-center justify-center overflow-hidden">
                        @if($tienda->logo_url)
                            <img src="{{ $tienda->logo_url }}" alt="Logo" class="w-full h-full object-contain p-2">
                        @else
                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-600"></i>
                        @endif
                    </div>
                </div>
                
                <div class="flex-1">
                    <label class="block text-gray-400 text-xs font-bold uppercase mb-3">Actualizar Logotipo</label>
                    <input type="file" name="logo" accept="image/*"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:bg-blue-600 file:text-white hover:file:bg-blue-500 file:cursor-pointer">
                    <p class="text-[10px] text-gray-500 mt-4">RECOMENDADO: PNG · 512x512px · MÁX 2MB</p>
                </div>
            </div>
        </section>
        
        {{-- Botones --}}
        <div class="sticky bottom-6 glass p-4 rounded-2xl border-white/10 flex justify-between shadow-2xl">
            <a href="{{ route('tienda.dashboard') }}" class="text-gray-400 hover:text-white px-6 py-2 text-sm font-bold">
                Cancelar
            </a>
            <button type="submit" class="px-10 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-black uppercase text-xs tracking-widest rounded-xl hover:from-blue-500 hover:to-indigo-500 shadow-lg active:scale-95 transition-all">
                <i class="fas fa-save mr-2"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>
@endsection