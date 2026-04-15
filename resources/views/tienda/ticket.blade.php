@extends('layouts.tienda')

@section('title', 'Apartado #' . $apartado->codigo_unico)

@section('content')
<div class="max-w-5xl mx-auto px-2 sm:px-0">
    <div class="mb-6">
        <a href="{{ route('tienda.apartados') }}" class="text-gray-400 hover:text-blue-500 transition-colors flex items-center gap-2 text-sm font-bold">
            <i class="fas fa-arrow-left"></i>
            <span>VOLVER A LISTADO</span>
        </a>
    </div>

    <div class="glass rounded-3xl overflow-hidden border border-white/10 shadow-2xl">
        <div class="px-6 py-6 border-b border-white/5 bg-white/5">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-black text-white tracking-tighter">
                        Apartado <span class="text-blue-500">#{{ $apartado->codigo_unico }}</span>
                    </h1>
                    <p class="text-gray-400 text-xs mt-1 font-bold uppercase tracking-widest">
                        <i class="fas fa-store mr-1 text-blue-500"></i> {{ $apartado->nombre_tienda }}
                    </p>
                </div>
                <span class="px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest
                    @if($apartado->estado == 'activo') bg-yellow-500/10 text-yellow-500 border border-yellow-500/20
                    @elseif($apartado->estado == 'pagado') bg-emerald-500/10 text-emerald-500 border border-emerald-500/20
                    @else bg-red-500/10 text-red-500 border border-red-500/20
                    @endif">
                    ● {{ $apartado->estado }}
                </span>
            </div>
        </div>

        <div class="p-6 md:p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                <div class="bg-white/[0.03] p-5 rounded-2xl border border-white/5">
                    <h3 class="text-[10px] font-black text-blue-500 uppercase tracking-widest mb-3">Cliente</h3>
                    <div class="space-y-1">
                        <p class="text-white font-bold text-lg uppercase leading-tight">{{ $apartado->nombre_cliente }}</p>
                        <p class="text-gray-400 text-sm flex items-center gap-2">
                            <i class="fas fa-phone-alt text-xs text-blue-500/50"></i> {{ $apartado->telefono_cliente }}
                        </p>
                    </div>
                </div>

                <div class="bg-white/[0.03] p-5 rounded-2xl border border-white/5">
                    <h3 class="text-[10px] font-black text-purple-500 uppercase tracking-widest mb-3">Vencimiento</h3>
                    <p class="text-2xl font-black {{ now() > $apartado->fecha_limite ? 'text-red-500' : 'text-white' }}">
                        {{ \Carbon\Carbon::parse($apartado->fecha_limite)->format('d/m/Y') }}
                    </p>
                    @if(now() > $apartado->fecha_limite && $apartado->estado != 'pagado')
                        <p class="text-[10px] text-red-500 font-black mt-1 uppercase animate-pulse">⚠️ TIEMPO AGOTADO</p>
                    @endif
                </div>
            </div>

            <div class="mb-8">
                <h3 class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-4">Artículos en Apartado</h3>
                <div class="space-y-2">
                    @foreach($apartado->productos as $producto)
                    <div class="flex justify-between items-center p-4 bg-white/[0.02] rounded-2xl border border-white/5">
                        <div class="pr-4">
                            <p class="text-white font-bold text-sm uppercase">{{ $producto->nombre_producto }}</p>
                            <p class="text-[10px] text-gray-500 font-bold uppercase">{{ $producto->cantidad }} unidad(es) × ${{ number_format($producto->precio_unitario, 2) }}</p>
                        </div>
                        <p class="font-black text-white text-sm">${{ number_format($producto->subtotal, 2) }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="glass rounded-2xl p-6 mb-8 border-blue-500/20">
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-center">
                    <div class="col-span-1">
                        <p class="text-[9px] text-gray-500 uppercase font-black mb-1">Total</p>
                        <p class="text-lg font-black text-white">${{ number_format($apartado->total, 2) }}</p>
                    </div>
                    <div class="col-span-1">
                        <p class="text-[9px] text-gray-500 uppercase font-black mb-1">Abonado</p>
                        <p class="text-lg font-black text-emerald-500">${{ number_format($apartado->total - $apartado->saldo_pendiente, 2) }}</p>
                    </div>
                    <div class="col-span-2 md:col-span-1 border-t md:border-t-0 md:border-l border-white/5 pt-4 md:pt-0">
                        <p class="text-[9px] text-gray-500 uppercase font-black mb-1">Pendiente</p>
                        <p class="text-xl font-black text-red-500">${{ number_format($apartado->saldo_pendiente, 2) }}</p>
                    </div>
                </div>
            </div>

            @if($apartado->saldo_pendiente > 0 && $apartado->estado != 'vencido')
            <div class="bg-blue-600/5 border border-blue-500/20 rounded-[2rem] p-6 mb-8">
                <h3 class="text-xs font-black text-white uppercase tracking-widest mb-4">Registrar Nuevo Abono</h3>
                <form action="{{ route('tienda.apartados.pago', $apartado->id) }}" method="POST">
                    @csrf
                    <div class="flex flex-col md:flex-row gap-3">
                        <div class="flex-1">
                            <input type="number" step="0.01" name="monto" required
                                   class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 outline-none transition-all font-bold"
                                   placeholder="MONTO $0.00" max="{{ $apartado->saldo_pendiente }}">
                        </div>
                        <div class="flex-1">
                            <select name="metodo_pago" required class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-blue-500 outline-none font-bold">
                                <option value="efectivo" class="bg-gray-900 uppercase">💵 Efectivo</option>
                                <option value="tarjeta" class="bg-gray-900 uppercase">💳 Tarjeta</option>
                                <option value="transferencia" class="bg-gray-900 uppercase">🏦 Transferencia</option>
                            </select>
                        </div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-black px-8 py-3 rounded-xl transition-all shadow-lg shadow-blue-600/30 uppercase text-xs tracking-widest">
                            Cobrar
                        </button>
                    </div>
                </form>
            </div>
            @endif

            <div class="border border-white/5 rounded-2xl overflow-hidden">
                <div class="bg-white/5 px-6 py-4 border-b border-white/5">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Historial de Pagos</h3>
                </div>
                <div class="overflow-x-auto overflow-y-hidden">
                    <table class="w-full text-sm text-left">
                        <tbody class="divide-y divide-white/5">
                            @forelse($apartado->pagos as $pago)
                            <tr class="text-gray-300 hover:bg-white/5 transition-colors">
                                <td class="px-6 py-4 text-xs">{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 font-black text-white">${{ number_format($pago->monto, 2) }}</td>
                                <td class="px-6 py-4 text-[9px] font-black uppercase tracking-widest text-blue-400">{{ $pago->metodo_pago }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-gray-500 italic text-xs uppercase tracking-widest">No hay abonos registrados</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 flex flex-wrap justify-center md:justify-end gap-6 pb-12">
        <a href="{{ route('tienda.apartados.editar', $apartado->id) }}" class="text-gray-500 hover:text-white text-[10px] font-black uppercase tracking-widest transition-colors">
            <i class="fas fa-edit mr-1"></i> Editar Apartado
        </a>
        <form action="{{ route('tienda.apartados.eliminar', $apartado->id) }}" method="POST" onsubmit="return confirm('¿Eliminar definitivamente?');">
            @csrf @method('DELETE')
            <button type="submit" class="text-red-900 hover:text-red-500 text-[10px] font-black uppercase tracking-widest transition-colors">
                <i class="fas fa-trash mr-1"></i> Eliminar Registro
            </button>
        </form>
    </div>
</div>

<div class="fixed inset-0 bg-black/90 backdrop-blur-md flex items-center justify-center z-[200] hidden opacity-0 transition-opacity duration-300" id="ticketModal">
    <div class="glass rounded-[3rem] max-w-sm w-full mx-4 p-8 border-white/10 shadow-2xl transform scale-95 transition-transform duration-300" id="modalContent">
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-emerald-500/20 rounded-full flex items-center justify-center mx-auto mb-6 border border-emerald-500/30">
                <i class="fas fa-check text-4xl text-emerald-500"></i>
            </div>
            <h3 class="text-2xl font-black text-white tracking-tighter">¡PAGO REGISTRADO!</h3>
            <p class="text-gray-400 text-sm mt-2 font-medium">El comprobante está listo para ser enviado.</p>
        </div>
        
        <div class="space-y-3">
            <a href="#" id="pdfLink" target="_blank" class="w-full bg-white text-black font-black py-4 rounded-2xl hover:bg-gray-200 transition flex items-center justify-center gap-3 uppercase text-xs tracking-widest">
                <i class="fas fa-file-pdf text-lg"></i> Abrir Ticket PDF
            </a>
            <a href="#" id="whatsappLink" target="_blank" class="w-full bg-[#25D366] text-white font-black py-4 rounded-2xl hover:opacity-90 transition flex items-center justify-center gap-3 uppercase text-xs tracking-widest shadow-lg shadow-green-500/20">
                <i class="fab fa-whatsapp text-2xl"></i> Enviar WhatsApp
            </a>
            <button onclick="cerrarModal()" class="w-full text-gray-500 hover:text-white font-black py-4 transition text-[10px] uppercase tracking-[0.2em]">Finalizar</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function mostrarTicket(data) {
        const modal = document.getElementById('ticketModal');
        const content = document.getElementById('modalContent');
        
        // Configurar Enlaces
        document.getElementById('pdfLink').href = data.pdf_url;
        
        let telefono = data.cliente_telefono ? data.cliente_telefono.replace(/\D/g, '') : '';
        if (telefono) {
            // Asegurar código de país para México si tiene 10 dígitos
            if (telefono.length === 10) telefono = '52' + telefono;
            
            const mensaje = encodeURIComponent(data.whatsapp_message);
            document.getElementById('whatsappLink').href = `https://wa.me/${telefono}?text=${mensaje}`;
            document.getElementById('whatsappLink').classList.remove('hidden');
        } else {
            document.getElementById('whatsappLink').classList.add('hidden');
        }

        // Mostrar con animación
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95');
        }, 10);
    }

    function cerrarModal() {
        const modal = document.getElementById('ticketModal');
        modal.classList.add('opacity-0');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    // DISPARADOR DEL MODAL DESDE LA SESIÓN DE LARAVEL
    @if(session('ticket_data'))
        document.addEventListener('DOMContentLoaded', () => {
            mostrarTicket(@json(session('ticket_data')));
        });
    @endif
</script>
@endpush