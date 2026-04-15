@extends('layouts.tienda')

@section('title', 'Apartado #' . $apartado->codigo_unico)

@section('content')
<div class="max-w-5xl mx-auto px-4">
    {{-- Navegación superior --}}
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('tienda.apartados') }}" class="text-gray-400 hover:text-blue-500 transition-colors flex items-center gap-2 group">
            <i class="fas fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
            <span class="font-medium">Volver a apartados</span>
        </a>
        
        <div class="flex gap-2">
            @if($apartado->estado !== 'pagado')
            <a href="{{ route('tienda.apartados.agregar-producto', $apartado->id) }}" 
               class="text-emerald-500 hover:text-emerald-400 px-4 py-2 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 border border-emerald-500/30 rounded-xl hover:bg-emerald-500/10">
                <i class="fas fa-plus-circle"></i> Agregar productos
            </a>
            @endif
            
            <a href="{{ route('tienda.apartados.editar', $apartado->id) }}" 
               class="text-blue-500 hover:text-blue-400 px-4 py-2 text-xs font-black uppercase tracking-widest transition-all flex items-center gap-2 border border-blue-500/30 rounded-xl hover:bg-blue-500/10">
                <i class="fas fa-edit"></i> Editar
            </a>
        </div>
    </div>

    <div class="glass rounded-3xl overflow-hidden border border-white/10 shadow-2xl">
        {{-- Cabecera del Apartado --}}
        <div class="px-8 py-6 border-b border-white/5 bg-white/5">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-black text-white tracking-tight">
                        Apartado <span class="text-blue-500">#{{ $apartado->codigo_unico }}</span>
                    </h1>
                    <p class="text-gray-400 text-sm mt-1 flex items-center">
                        <i class="fas fa-store mr-2 text-blue-500/50"></i>
                        Sucursal Activa
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border
                        @switch($apartado->estado)
                            @case('activo') bg-yellow-500/20 text-yellow-500 border-yellow-500/30 @break
                            @case('pagado') bg-green-500/20 text-green-500 border-green-500/30 @break
                            @default bg-red-500/20 text-red-500 border-red-500/30
                        @endswitch">
                        {{ $apartado->estado }}
                    </span>
                </div>
            </div>
        </div>

        <div class="p-8">
            {{-- Grid de Información Rápida (Cliente + Vencimiento) --}}
            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white/5 p-5 rounded-2xl border border-white/5">
                    <h3 class="text-[10px] font-black text-blue-500 uppercase tracking-widest mb-4 flex items-center">
                        <i class="fas fa-user-circle mr-2 text-sm"></i> Datos del Cliente
                    </h3>
                    <div class="space-y-2">
                        <p class="text-white text-lg font-bold">{{ $apartado->nombre_cliente }}</p>
                        <p class="text-gray-400 text-sm flex items-center"><i class="fas fa-phone-alt mr-2 text-blue-500/50"></i>{{ $apartado->telefono_cliente }}</p>
                        @if($apartado->email_cliente)
                            <p class="text-gray-400 text-sm flex items-center"><i class="fas fa-envelope mr-2 text-blue-500/50"></i>{{ $apartado->email_cliente }}</p>
                        @endif
                    </div>
                </div>

                <div class="bg-white/5 p-5 rounded-2xl border border-white/5">
                    <h3 class="text-[10px] font-black text-purple-500 uppercase tracking-widest mb-4 flex items-center">
                        <i class="fas fa-calendar-alt mr-2 text-sm"></i> Vencimiento
                    </h3>
                    <p class="text-3xl font-black {{ $apartado->esta_vencido ? 'text-red-500' : 'text-white' }}">
                        {{ \Carbon\Carbon::parse($apartado->fecha_limite)->format('d/m/Y') }}
                    </p>
                    @if($apartado->esta_vencido)
                        <p class="text-xs text-red-400 mt-2 font-black animate-pulse uppercase tracking-tighter">⚠️ EL PLAZO HA VENCIDO</p>
                    @endif
                </div>
            </div>

            {{-- Resumen Financiero (Compacto) --}}
            <div class="glass rounded-2xl p-5 mb-6 border-blue-500/20">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                    <div>
                        <p class="text-[9px] text-gray-500 uppercase font-black tracking-widest">Total Apartado</p>
                        <p class="text-2xl font-black text-white">${{ number_format($apartado->total, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-[9px] text-gray-500 uppercase font-black tracking-widest">Abonado Total</p>
                        <p class="text-2xl font-black text-emerald-500">${{ number_format($apartado->total_pagado, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-[9px] text-gray-500 uppercase font-black tracking-widest">Saldo Restante</p>
                        <p class="text-2xl font-black text-red-500">${{ number_format($apartado->saldo_pendiente, 2) }}</p>
                    </div>
                </div>
                <div class="mt-3">
                    <div class="flex justify-between text-[9px] font-black uppercase mb-2">
                        <span class="text-gray-500">Progreso</span>
                        <span class="text-blue-500">{{ number_format($apartado->porcentaje_pagado, 1) }}%</span>
                    </div>
                    <div class="w-full bg-white/10 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-600 to-indigo-500 h-full rounded-full transition-all" 
                             style="width: {{ $apartado->porcentaje_pagado }}%"></div>
                    </div>
                </div>
            </div>

            {{-- FORMULARIO DE ABONO GENERAL - UBICADO ARRIBA --}}
            @if($apartado->saldo_pendiente > 0 && !$apartado->esta_vencido)
            <div class="bg-gradient-to-r from-blue-600/10 to-blue-500/5 border border-blue-500/30 rounded-2xl p-5 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-black text-white uppercase tracking-widest flex items-center gap-2">
                        <i class="fas fa-cash-register text-blue-500 text-lg"></i> Abono General Rápido
                    </h3>
                    <span class="text-[9px] text-gray-500">El pago se distribuye automáticamente</span>
                </div>
                <form action="{{ route('tienda.apartados.pago', $apartado->id) }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    @csrf
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">$</span>
                        <input type="number" step="0.01" name="monto" required
                               class="w-full bg-black/20 border border-white/10 rounded-xl pl-7 pr-3 py-3 text-white font-bold focus:outline-none focus:ring-2 focus:ring-blue-500/50 text-sm"
                               placeholder="0.00" max="{{ $apartado->saldo_pendiente }}" min="0.01"
                               autocomplete="off">
                    </div>
                    <div>
                        <select name="metodo_pago" required class="w-full bg-black/20 border border-white/10 rounded-xl px-3 py-3 text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 cursor-pointer">
                            <option value="efectivo">💵 Efectivo</option>
                            <option value="tarjeta">💳 Tarjeta</option>
                            <option value="transferencia">🏦 Transferencia</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-black py-3 rounded-xl transition-all shadow-lg shadow-blue-600/20 uppercase tracking-widest text-xs flex items-center justify-center gap-2">
                        <i class="fas fa-check-circle"></i> Aplicar Abono
                    </button>
                </form>
            </div>
            @endif

            {{-- Listado de Productos --}}
            <div class="mb-6">
                <h3 class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3 flex items-center gap-2">
                    <i class="fas fa-boxes"></i> Productos en este apartado
                </h3>
                <div class="grid gap-2 max-h-[400px] overflow-y-auto pr-2">
                    @foreach($apartado->productos as $producto)
                    @php
                        $pagadoProducto = $producto->monto_pagado ?? 0;
                        $saldoProducto = $producto->saldo_pendiente;
                        $porcentajeProducto = $producto->porcentaje_pagado;
                        $estaPagadoProducto = $saldoProducto <= 0;
                    @endphp
                    <div class="p-3 bg-white/5 rounded-xl border {{ $estaPagadoProducto ? 'border-green-500/30' : 'border-transparent hover:border-white/10' }} transition-all group">
                        <div class="flex justify-between items-start">
                            <div class="flex items-start gap-3 flex-1">
                                <div class="w-8 h-8 {{ $estaPagadoProducto ? 'bg-green-500/20' : 'bg-blue-500/10' }} rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas {{ $estaPagadoProducto ? 'fa-check-circle' : 'fa-tag' }} text-xs"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-white font-bold text-sm {{ $estaPagadoProducto ? 'line-through text-gray-400' : '' }}">{{ $producto->nombre_producto }}</p>
                                    <p class="text-[10px] text-gray-500">Cant: {{ $producto->cantidad }} × ${{ number_format($producto->precio_unitario, 2) }}</p>
                                    @if($producto->descripcion)
                                        <p class="text-[9px] text-gray-600">{{ $producto->descripcion }}</p>
                                    @endif
                                </div>
                            </div>
                            <p class="font-black text-white text-base">{{ number_format($producto->subtotal, 2) }}</p>
                        </div>
                        
                        <div class="mt-2 flex justify-between text-[9px]">
                            <span class="text-emerald-400">Pagado: ${{ number_format($pagadoProducto, 2) }}</span>
                            <span class="text-red-500">Falta: ${{ number_format($saldoProducto, 2) }}</span>
                            <span class="text-blue-400">{{ number_format($porcentajeProducto, 1) }}%</span>
                        </div>
                        
                        <div class="mt-1">
                            <div class="w-full bg-white/10 rounded-full h-1 overflow-hidden">
                                <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-full rounded-full" 
                                     style="width: {{ $porcentajeProducto }}%"></div>
                            </div>
                        </div>
                        
                        {{-- Formulario para abonar a este producto específico (compacto) --}}
                        @if(!$estaPagadoProducto && $apartado->saldo_pendiente > 0 && !$apartado->esta_vencido)
                        <div class="mt-2 pt-2 border-t border-white/10">
                            <form action="{{ route('tienda.apartados.pago-producto', $apartado->id) }}" method="POST" class="flex flex-wrap gap-2 items-end">
                                @csrf
                                <input type="hidden" name="producto_id" value="{{ $producto->id }}">
                                
                                <div class="flex-1 min-w-[120px]">
                                    <div class="relative">
                                        <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-500 text-[10px]">$</span>
                                        <input type="number" 
                                               name="monto" 
                                               step="0.01" 
                                               max="{{ $saldoProducto }}"
                                               min="0.01"
                                               required
                                               class="w-full bg-black/20 border border-white/10 rounded-lg pl-5 pr-2 py-1.5 text-white text-xs"
                                               placeholder="Monto">
                                    </div>
                                </div>
                                
                                <div class="w-28">
                                    <select name="metodo_pago" class="w-full bg-black/20 border border-white/10 rounded-lg px-2 py-1.5 text-white text-xs">
                                        <option value="efectivo">Efectivo</option>
                                        <option value="tarjeta">Tarjeta</option>
                                        <option value="transferencia">Transferencia</option>
                                    </select>
                                </div>
                                
                                <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white px-3 py-1.5 rounded-lg text-[10px] font-bold transition-all">
                                    <i class="fas fa-money-bill-wave mr-1"></i> Abonar
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Historial de Pagos (colapsado por defecto) --}}
            <div class="border border-white/5 rounded-2xl overflow-hidden bg-black/20">
                <details class="group">
                    <summary class="px-5 py-3 border-b border-white/5 flex justify-between items-center cursor-pointer hover:bg-white/5 transition">
                        <h3 class="text-[10px] font-black text-gray-500 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-history"></i> Historial de Transacciones
                            <span class="text-[8px] text-gray-600">({{ $apartado->pagos->count() }} registros)</span>
                        </h3>
                        <i class="fas fa-chevron-down text-gray-500 text-xs group-open:rotate-180 transition-transform"></i>
                    </summary>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-[9px] uppercase font-black text-gray-600 border-b border-white/5">
                                <tr>
                                    <th class="px-4 py-2">Fecha</th>
                                    <th class="px-4 py-2">Monto</th>
                                    <th class="px-4 py-2">Producto</th>
                                    <th class="px-4 py-2 text-right">Método</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @forelse($apartado->pagos->sortByDesc('fecha_pago') as $pago)
                                <tr class="text-gray-300 text-xs hover:bg-white/5">
                                    <td class="px-4 py-2">{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-2 font-black text-white">${{ number_format($pago->monto, 2) }}</td>
                                    <td class="px-4 py-2">
                                        @if($pago->producto_id)
                                            <span class="text-blue-400 text-[10px]">{{ $pago->producto->nombre_producto ?? 'Producto' }}</span>
                                        @else
                                            <span class="text-gray-500 text-[10px]">Abono general</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-right">
                                        <span class="px-2 py-0.5 bg-blue-500/10 text-blue-400 rounded text-[9px] uppercase">
                                            {{ $pago->metodo_pago }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-gray-600 text-xs">No hay movimientos registrados</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </details>
            </div>
        </div>
    </div>

    {{-- Acciones --}}
    <div class="mt-6 flex justify-end gap-4">
        <a href="{{ route('tienda.apartados.editar', $apartado->id) }}" class="text-gray-500 hover:text-white px-3 py-1.5 text-[10px] font-black uppercase tracking-widest transition">
            <i class="fas fa-edit mr-1"></i> Editar
        </a>
        <form action="{{ route('tienda.apartados.eliminar', $apartado->id) }}" method="POST" onsubmit="return confirm('¿Confirmas la eliminación definitiva?');">
            @csrf @method('DELETE')
            <button type="submit" class="text-red-500/30 hover:text-red-500 text-[10px] font-black uppercase tracking-widest transition">
                <i class="fas fa-trash-alt mr-1"></i> Eliminar
            </button>
        </form>
    </div>
</div>

{{-- MODALES (WhatsApp y Abono) --}}
<div class="fixed inset-0 bg-black/90 backdrop-blur-md flex items-center justify-center z-[200] hidden opacity-0 transition-all duration-300" id="whatsappModal">
    <div class="glass rounded-[2.5rem] max-w-sm w-full mx-4 p-8 border-white/10 shadow-2xl transform scale-95 transition-all duration-300" id="whatsappModalContent">
        <div class="text-center mb-6">
            <div class="w-20 h-20 bg-emerald-500/20 rounded-full flex items-center justify-center mx-auto mb-5">
                <i class="fas fa-check text-4xl text-emerald-500"></i>
            </div>
            <h3 class="text-2xl font-black text-white tracking-tight" id="modalTitle">¡APARTADO ACTUALIZADO!</h3>
            <p class="text-gray-400 text-xs mt-2" id="modalSubtitle">Ticket impreso automáticamente</p>
        </div>
        <div class="bg-white/5 rounded-2xl p-4 mb-6 space-y-2" id="modalInfoContainer"></div>
        <div class="grid gap-3">
            <a href="#" id="whatsappLinkNuevo" target="_blank" class="w-full bg-[#25D366] text-white font-black py-4 rounded-2xl transition flex items-center justify-center gap-3 text-[10px]">
                <i class="fab fa-whatsapp text-xl"></i> Notificar al Cliente
            </a>
            <button onclick="cerrarModalWhatsApp()" class="w-full text-gray-500 hover:text-white text-[9px] uppercase tracking-widest mt-2">Cerrar</button>
        </div>
    </div>
</div>

<div class="fixed inset-0 bg-black/90 backdrop-blur-md flex items-center justify-center z-[200] hidden opacity-0 transition-all duration-300" id="ticketModal">
    <div class="glass rounded-[2.5rem] max-w-sm w-full mx-4 p-8 border-white/10 shadow-2xl transform scale-95 transition-all duration-300" id="modalContent">
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-emerald-500/20 rounded-full flex items-center justify-center mx-auto mb-5">
                <i class="fas fa-check text-4xl text-emerald-500"></i>
            </div>
            <h3 class="text-2xl font-black text-white tracking-tight">¡PAGO PROCESADO!</h3>
            <p class="text-gray-400 text-sm mt-2">Comprobante digital listo</p>
        </div>
        <div class="grid gap-3">
            <a href="#" id="whatsappLink" target="_blank" class="w-full bg-[#25D366] text-white font-black py-4 rounded-2xl transition flex items-center justify-center gap-3 text-[10px]">
                <i class="fab fa-whatsapp text-xl"></i> Enviar a Cliente
            </a>
            <button onclick="cerrarModalAbono()" class="w-full text-gray-500 hover:text-white text-[9px] uppercase tracking-widest mt-2">Cerrar</button>
        </div>
    </div>
</div>

<script>
    // Funciones de impresión y modales (mantener igual)
    function imprimirTicket(url) {
        if (!url) return;
        const printWindow = window.open(url, '_blank');
        if (printWindow) {
            printWindow.onload = function() {
                setTimeout(() => printWindow.print(), 500);
            };
        }
    }

    function mostrarModalWhatsApp(data) {
        const modal = document.getElementById('whatsappModal');
        const whatsappLink = document.getElementById('whatsappLinkNuevo');
        const modalTitle = document.getElementById('modalTitle');
        const modalSubtitle = document.getElementById('modalSubtitle');
        const modalInfoContainer = document.getElementById('modalInfoContainer');
        
        function generarEnlaceWhatsApp(telefono, mensaje) {
            const telefonoLimpio = telefono.replace(/\D/g, '');
            let telefonoCompleto = telefonoLimpio.length === 10 ? '52' + telefonoLimpio : telefonoLimpio;
            return `https://wa.me/${telefonoCompleto}?text=${encodeURIComponent(mensaje)}`;
        }
        
        if (data.tipo === 'productos_agregados') {
            modalTitle.textContent = '¡PRODUCTOS AGREGADOS!';
            modalSubtitle.textContent = 'Ticket actualizado impreso automáticamente';
            let productosHtml = '';
            if (data.productos?.length) {
                productosHtml = '<div class="mt-2 space-y-1 border-t border-white/10 pt-2">';
                data.productos.forEach(p => {
                    productosHtml += `<div class="flex justify-between text-xs"><span>${p.cantidad}x ${p.nombre}</span><span>$${p.subtotal}</span></div>`;
                });
                productosHtml += '</div>';
            }
            modalInfoContainer.innerHTML = `
                <div class="flex justify-between text-sm"><span class="text-gray-500">Código:</span><span class="text-white font-bold">${data.codigo}</span></div>
                <div class="flex justify-between text-sm"><span class="text-gray-500">Cliente:</span><span class="text-white">${data.cliente_nombre}</span></div>
                <div class="flex justify-between text-sm"><span class="text-gray-500">Total agregado:</span><span class="text-emerald-400 font-bold">$${data.total_agregado}</span></div>
                <div class="flex justify-between text-sm"><span class="text-gray-500">Nuevo saldo:</span><span class="text-red-400 font-bold">$${data.nuevo_saldo}</span></div>
                ${productosHtml}
                <div class="flex justify-between text-sm pt-2 border-t"><span>Productos:</span><span class="text-blue-400">${data.cantidad_productos} ARTÍCULOS</span></div>
            `;
        } else {
            modalTitle.textContent = '¡APARTADO CREADO!';
            modalSubtitle.textContent = 'Ticket impreso automáticamente';
            modalInfoContainer.innerHTML = `
                <div class="flex justify-between text-sm"><span class="text-gray-500">Código:</span><span class="text-white font-bold">${data.codigo}</span></div>
                <div class="flex justify-between text-sm"><span class="text-gray-500">Cliente:</span><span class="text-white">${data.cliente_nombre}</span></div>
                <div class="flex justify-between text-sm"><span class="text-gray-500">Total:</span><span class="text-white font-bold">$${data.total}</span></div>
                <div class="flex justify-between text-sm"><span class="text-gray-500">Abono:</span><span class="text-emerald-400 font-bold">$${data.apartado_inicial}</span></div>
                <div class="flex justify-between text-sm"><span class="text-gray-500">Saldo:</span><span class="text-red-400 font-bold">$${data.saldo_pendiente}</span></div>
                <div class="flex justify-between text-sm"><span class="text-gray-500">Vence:</span><span class="text-white">${data.fecha_limite}</span></div>
                <div class="flex justify-between text-sm pt-2 border-t"><span>Productos:</span><span class="text-blue-400">${data.cantidad_productos} ARTÍCULOS</span></div>
            `;
        }
        
        if (data.cliente_telefono && data.whatsapp_message) {
            whatsappLink.href = generarEnlaceWhatsApp(data.cliente_telefono, data.whatsapp_message);
            whatsappLink.classList.remove('hidden');
        }
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('opacity-100'), 10);
    }

    function cerrarModalWhatsApp() {
        const modal = document.getElementById('whatsappModal');
        modal.classList.remove('opacity-100');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    function mostrarTicket(data) {
        const modal = document.getElementById('ticketModal');
        const whatsappLink = document.getElementById('whatsappLink');
        function generarEnlaceWhatsApp(telefono, mensaje) {
            const telefonoLimpio = telefono.replace(/\D/g, '');
            let telefonoCompleto = telefonoLimpio.length === 10 ? '52' + telefonoLimpio : telefonoLimpio;
            return `https://wa.me/${telefonoCompleto}?text=${encodeURIComponent(mensaje)}`;
        }
        if (data.cliente_telefono && data.whatsapp_message) {
            whatsappLink.href = generarEnlaceWhatsApp(data.cliente_telefono, data.whatsapp_message);
            whatsappLink.classList.remove('hidden');
        }
        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.add('opacity-100'), 10);
    }

    function cerrarModalAbono() {
        const modal = document.getElementById('ticketModal');
        modal.classList.remove('opacity-100');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    // Inicialización
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('imprimir_ticket') && session('imprimir_ticket.url'))
            imprimirTicket('{{ session('imprimir_ticket.url') }}');
            @php session()->forget('imprimir_ticket'); @endphp
        @endif
        @if(session('modal_data'))
            setTimeout(() => mostrarModalWhatsApp(@json(session('modal_data'))), 1000);
            @php session()->forget('modal_data'); @endphp
        @endif
        @if(session('ticket_data'))
            mostrarTicket(@json(session('ticket_data')));
            @php session()->forget('ticket_data'); @endphp
        @endif
        
        const productosAgregados = sessionStorage.getItem('mostrar_modal_productos');
        const ticketProductos = sessionStorage.getItem('imprimir_ticket_productos');
        if (productosAgregados) {
            setTimeout(() => mostrarModalWhatsApp(JSON.parse(productosAgregados)), 1000);
            sessionStorage.removeItem('mostrar_modal_productos');
        }
        if (ticketProductos) {
            const ticketData = JSON.parse(ticketProductos);
            if (ticketData?.url) setTimeout(() => imprimirTicket(ticketData.url), 1500);
            sessionStorage.removeItem('imprimir_ticket_productos');
        }
    });
</script>
@endsection