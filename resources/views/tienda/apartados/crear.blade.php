@extends('layouts.tienda')

@section('title', 'Nuevo Registro')

@push('styles')
<style>
    .glass {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .input-glass {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: white;
        transition: all 0.3s ease;
    }
    .input-glass:focus {
        background: rgba(255, 255, 255, 0.05);
        border-color: #3b82f6;
        box-shadow: 0 0 15px rgba(59, 130, 246, 0.15);
        outline: none;
    }
    .input-glass:invalid {
        border-color: #ef4444;
    }

    .producto-item {
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    #successModal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(10px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
    #successModal.active { display: flex; }

    .error-border {
        border-color: #ef4444 !important;
    }
    .error-message {
        color: #ef4444;
        font-size: 10px;
        margin-top: 4px;
        font-weight: bold;
        text-transform: uppercase;
    }
    
    .producto-item:hover {
        background: rgba(255, 255, 255, 0.05);
        transition: all 0.2s ease;
    }
    
    .productos-scroll {
        max-height: 280px;
        overflow-y: auto;
        padding-right: 8px;
    }
    
    .productos-scroll::-webkit-scrollbar {
        width: 4px;
    }
    
    .productos-scroll::-webkit-scrollbar-track {
        background: rgba(255,255,255,0.05);
        border-radius: 10px;
    }
    
    .productos-scroll::-webkit-scrollbar-thumb {
        background: rgba(59,130,246,0.5);
        border-radius: 10px;
    }
    
    .required-star {
        color: #ef4444;
        font-size: 10px;
        margin-left: 2px;
    }
    
    .info-hint {
        font-size: 9px;
        color: #6b7280;
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    .field-icon {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
        font-size: 12px;
        pointer-events: none;
    }
    
    .input-with-icon {
        padding-left: 32px;
    }
    
    .whatsapp-hint {
        background: rgba(37, 211, 102, 0.1);
        border-left: 2px solid #25D366;
        padding: 4px 8px;
        border-radius: 6px;
        margin-top: 4px;
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <header class="mb-5 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight flex items-center gap-2">
                <i class="fas fa-file-invoice text-blue-500 text-2xl"></i>
                Nuevo Apartado
            </h1>
            <p class="text-gray-500 text-xs font-medium mt-1 flex items-center gap-2">
                <i class="fas fa-info-circle text-[10px]"></i>
                Los campos con <span class="text-red-500">*</span> son obligatorios
            </p>
        </div>
        <div class="bg-yellow-500/20 text-yellow-500 px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1">
            <i class="fas fa-bolt"></i>
            Registro exprés
        </div>
    </header>

    @if($errors->any())
        <div class="mb-4 p-4 bg-red-500/10 border border-red-500/20 rounded-2xl text-red-400 text-xs">
            <div class="flex items-start gap-2">
                <i class="fas fa-exclamation-triangle mt-0.5"></i>
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error) 
                        <li>{{ $error }}</li> 
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('tienda.apartados.guardar') }}" id="formApartado" class="space-y-4" onsubmit="return validarFormulario()" autocomplete="off">
        @csrf
        
        {{-- Fila 1: Datos del Cliente --}}
        <div class="glass rounded-2xl p-5">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-8 h-8 bg-blue-500/20 rounded-lg flex items-center justify-center text-blue-500">
                    <i class="fas fa-user-tag text-sm"></i>
                </div>
                <h2 class="text-md font-bold text-white uppercase tracking-tight">Información del Cliente</h2>
                <span class="text-[9px] text-gray-500 ml-auto">Datos de contacto</span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-[9px] font-black uppercase tracking-widest text-gray-500 block mb-1">
                        <i class="fas fa-user mr-1"></i> Nombre Completo <span class="required-star">*</span>
                    </label>
                    <div class="relative">
                        <i class="fas fa-user field-icon"></i>
                        <input type="text" 
                               name="nombre_cliente" 
                               id="nombre_cliente"
                               required 
                               class="w-full input-glass input-with-icon py-2 rounded-lg text-sm" 
                               value="{{ old('nombre_cliente') }}" 
                               placeholder="Ej: Juan Pérez"
                               autocomplete="off">
                    </div>
                    <div class="info-hint">
                        <i class="fas fa-info-circle text-[8px]"></i>
                        <span>Solo letras y espacios</span>
                    </div>
                    <div class="error-message text-[8px]" id="error-nombre"></div>
                </div>
                <div>
                    <label class="text-[9px] font-black uppercase tracking-widest text-gray-500 block mb-1">
                        <i class="fab fa-whatsapp mr-1"></i> Número de WhatsApp <span class="required-star">*</span>
                    </label>
                    <div class="relative">
                        <i class="fab fa-whatsapp field-icon text-green-500"></i>
                        <input type="tel" 
                               name="telefono_cliente" 
                               id="telefono_cliente"
                               required 
                               class="w-full input-glass input-with-icon py-2 rounded-lg text-sm" 
                               value="{{ old('telefono_cliente') }}" 
                               placeholder="9991234567"
                               autocomplete="off">
                    </div>
                    <div class="whatsapp-hint">
                        <i class="fab fa-whatsapp text-[10px] text-green-500 mr-1"></i>
                        <span class="text-[9px]">Número de celular con WhatsApp - Recibirá la notificación de su apartado</span>
                    </div>
                    <div class="error-message text-[8px]" id="error-telefono"></div>
                </div>
                <div>
                    <label class="text-[9px] font-black uppercase tracking-widest text-gray-500 block mb-1">
                        <i class="fas fa-envelope mr-1"></i> Correo Electrónico
                    </label>
                    <div class="relative">
                        <i class="fas fa-envelope field-icon"></i>
                        <input type="email" 
                               name="email_cliente" 
                               id="email_cliente"
                               class="w-full input-glass input-with-icon py-2 rounded-lg text-sm" 
                               value="{{ old('email_cliente') }}" 
                               placeholder="cliente@email.com"
                               autocomplete="off">
                    </div>
                    <div class="info-hint">
                        <i class="fas fa-info-circle text-[8px]"></i>
                        <span>Opcional - Para enviar comprobantes</span>
                    </div>
                    <div class="error-message text-[8px]" id="error-email"></div>
                </div>
            </div>
        </div>

        {{-- Fila 2: Productos --}}
        <div class="glass rounded-2xl p-5">
            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-purple-500/20 rounded-lg flex items-center justify-center text-purple-500">
                        <i class="fas fa-box-open text-sm"></i>
                    </div>
                    <h2 class="text-md font-bold text-white uppercase tracking-tight">Productos a Apartar</h2>
                    <span class="text-[9px] text-gray-500 flex items-center gap-1">
                        <i class="fas fa-arrows-alt-v"></i> Scroll para más
                    </span>
                </div>
                <button type="button" onclick="agregarProducto()" class="px-3 py-1.5 bg-emerald-600/20 hover:bg-emerald-600/30 border border-emerald-500/30 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-1">
                    <i class="fas fa-plus-circle text-emerald-400 text-xs"></i> Agregar Producto
                </button>
            </div>
            
            <div class="productos-scroll">
                <div id="productos-container" class="space-y-3">
                    <div class="producto-item bg-white/[0.01] p-3 rounded-xl border border-white/5">
                        <div class="grid grid-cols-12 gap-2 items-center">
                            <div class="col-span-5">
                                <div class="relative">
                                    <i class="fas fa-tag absolute left-2 top-1/2 -translate-y-1/2 text-gray-500 text-[10px]"></i>
                                    <input type="text" 
                                           name="productos[0][nombre]" 
                                           required 
                                           class="w-full input-glass pl-7 pr-2 py-2 rounded-lg text-xs producto-nombre" 
                                           placeholder="Nombre del producto *"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="col-span-3">
                                <div class="relative">
                                    <i class="fas fa-align-left absolute left-2 top-1/2 -translate-y-1/2 text-gray-500 text-[10px]"></i>
                                    <input type="text" 
                                           name="productos[0][descripcion]" 
                                           class="w-full input-glass pl-7 pr-2 py-2 rounded-lg text-xs" 
                                           placeholder="Detalles (color, talla...)"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="col-span-2">
                                <div class="relative">
                                    <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-500 text-[10px]">$</span>
                                    <input type="number" 
                                           step="0.01" 
                                           name="productos[0][precio]" 
                                           required 
                                           class="w-full input-glass pl-5 pr-2 py-2 rounded-lg text-xs precio-producto text-emerald-400 font-bold" 
                                           onchange="calcularTotal()" 
                                           onkeyup="calcularTotal()" 
                                           placeholder="Precio *"
                                           min="0.01"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="col-span-1">
                                <div class="relative">
                                    <i class="fas fa-hashtag absolute left-2 top-1/2 -translate-y-1/2 text-gray-500 text-[10px]"></i>
                                    <input type="number" 
                                           name="productos[0][cantidad]" 
                                           value="1" 
                                           min="1" 
                                           max="999"
                                           class="w-full input-glass pl-5 pr-2 py-2 rounded-lg text-xs cantidad-producto text-center" 
                                           onchange="calcularTotal()" 
                                           onkeyup="calcularTotal()"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="col-span-1 flex justify-center">
                                <button type="button" onclick="eliminarProducto(this)" class="text-red-500/40 hover:text-red-500 transition-colors text-xs">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-3 flex justify-end">
                <div class="bg-gradient-to-r from-blue-600/20 to-blue-500/10 px-5 py-2 rounded-xl">
                    <span class="text-[9px] text-gray-400 uppercase flex items-center gap-1">
                        <i class="fas fa-calculator"></i> Subtotal
                    </span>
                    <span id="total-productos" class="text-2xl font-black text-white ml-2">$0.00</span>
                </div>
            </div>
        </div>

        {{-- Fila 3: Condiciones Financieras + Notas + Botón --}}
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
            {{-- Columna izquierda: Condiciones Financieras --}}
            <div class="md:col-span-8">
                <div class="glass rounded-2xl p-5 h-full">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-8 h-8 bg-emerald-500/20 rounded-lg flex items-center justify-center text-emerald-500">
                            <i class="fas fa-hand-holding-usd text-sm"></i>
                        </div>
                        <h2 class="text-md font-bold text-white uppercase tracking-tight">Condiciones Financieras</h2>
                        <span class="text-[9px] text-gray-500 ml-auto">Define el pago inicial y plazo</span>
                    </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="text-[9px] font-black uppercase tracking-widest text-gray-500 block mb-1">
                                <i class="fas fa-money-bill-wave mr-1"></i> Abono Inicial <span class="required-star">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-500 text-xs">$</span>
                                <input type="number" 
                                       step="0.01" 
                                       name="apartado_inicial" 
                                       id="apartadoInicial" 
                                       required 
                                       class="w-full input-glass pl-6 pr-2 py-2 rounded-lg text-sm font-bold text-emerald-400" 
                                       onchange="calcularSaldo()" 
                                       onkeyup="calcularSaldo()" 
                                       value="0" 
                                       min="0"
                                       autocomplete="off">
                            </div>
                            <div class="info-hint">
                                <i class="fas fa-info-circle text-[8px]"></i>
                                <span>Mínimo $0, puede ser el total</span>
                            </div>
                            <div class="error-message text-[8px]" id="error-abono"></div>
                        </div>
                        <div>
                            <label class="text-[9px] font-black uppercase tracking-widest text-gray-500 block mb-1">
                                <i class="fas fa-calendar-alt mr-1"></i> Fecha Límite <span class="required-star">*</span>
                            </label>
                            <div class="relative">
                                <i class="fas fa-calendar-day absolute left-2 top-1/2 -translate-y-1/2 text-gray-500 text-xs"></i>
                                <input type="date" 
                                       name="fecha_limite" 
                                       id="fecha_limite"
                                       required 
                                       class="w-full input-glass pl-7 pr-2 py-2 rounded-lg text-xs" 
                                       value="{{ date('Y-m-d', strtotime('+60 days')) }}" 
                                       min="{{ date('Y-m-d') }}"
                                       autocomplete="off">
                            </div>
                            <div class="info-hint">
                                <i class="fas fa-info-circle text-[8px]"></i>
                                <span>📅 60 días de plazo (predeterminado) - Fecha máxima para pagar</span>
                            </div>
                        </div>
                        <div>
                            <label class="text-[9px] font-black uppercase tracking-widest text-gray-500 block mb-1">
                                <i class="fas fa-credit-card mr-1"></i> Método de Pago
                            </label>
                            <div class="relative">
                                <i class="fas fa-wallet absolute left-2 top-1/2 -translate-y-1/2 text-gray-500 text-xs"></i>
                                <select name="metodo_pago" class="w-full input-glass pl-7 pr-2 py-2 rounded-lg text-xs cursor-pointer appearance-none">
                                    <option value="efectivo">💵 Efectivo</option>
                                    <option value="tarjeta">💳 Tarjeta</option>
                                    <option value="transferencia">🏦 Transferencia</option>
                                </select>
                            </div>
                        </div>
                        <div class="bg-gradient-to-r from-red-500/15 to-red-500/5 rounded-xl p-2 text-center border border-red-500/20">
                            <p class="text-[8px] font-black text-red-500/80 uppercase tracking-widest flex items-center justify-center gap-1">
                                <i class="fas fa-coins"></i> Saldo Pendiente
                            </p>
                            <p id="saldoPendiente" class="text-2xl font-black text-red-500">$0.00</p>
                            <p class="text-[7px] text-gray-500 mt-1">Total a pagar después del abono</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Columna derecha: Notas + Botón --}}
            <div class="md:col-span-4">
                <div class="glass rounded-2xl p-5 h-full flex flex-col">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-gray-500/20 rounded-lg flex items-center justify-center text-gray-400">
                            <i class="fas fa-sticky-note text-sm"></i>
                        </div>
                        <h2 class="text-md font-bold text-white uppercase tracking-tight">Notas Internas</h2>
                        <span class="text-[9px] text-gray-500 ml-auto">Opcional</span>
                    </div>
                    <div class="relative">
                        <i class="fas fa-pen absolute left-3 top-3 text-gray-500 text-xs"></i>
                        <textarea name="notas" class="w-full input-glass pl-8 p-3 rounded-xl text-xs resize-none" placeholder="Ej: Cliente referido por... / Producto en exhibición..." maxlength="500" rows="3" autocomplete="off"></textarea>
                    </div>
                    <div class="info-hint mt-2">
                        <i class="fas fa-info-circle text-[8px]"></i>
                        <span>Información adicional para referencia interna</span>
                    </div>
                    
                    <button type="submit" class="mt-4 w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 text-white font-extrabold py-3 rounded-xl transition-all shadow-lg shadow-blue-600/20 uppercase tracking-widest text-xs flex items-center justify-center gap-2" id="btn-submit">
                        <i class="fas fa-save text-sm"></i>
                        Registrar Apartado
                    </button>
                    
                    <div class="mt-3 flex justify-center text-[8px] text-gray-600">
                        <i class="fas fa-shield-alt mr-1"></i>
                        <span>Datos seguros y encriptados</span>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- MODAL de Éxito --}}
<div id="successModal">
    <div class="glass max-w-sm w-full mx-4 rounded-2xl p-8 border border-white/10 shadow-2xl text-center">
        <div class="w-16 h-16 bg-emerald-500/20 rounded-2xl flex items-center justify-center mx-auto mb-5">
            <i class="fas fa-check-circle text-3xl text-emerald-500"></i>
        </div>
        <h3 class="text-xl font-black text-white mb-1">¡Registro Exitoso!</h3>
        <p class="text-gray-500 text-[10px] font-bold uppercase mb-5 flex items-center justify-center gap-1" id="modalCantidadProductos"></p>
        
        <div class="bg-gradient-to-r from-blue-600/20 to-blue-500/10 p-4 rounded-2xl border border-blue-600/20 mb-5">
            <span class="text-[9px] font-black text-blue-500 uppercase flex items-center justify-center gap-1">
                <i class="fas fa-qrcode"></i> Código Único
            </span>
            <p class="text-2xl font-black text-white tracking-tighter mt-1" id="modalCodigo"></p>
        </div>
        
        <div class="grid gap-2">
            <a href="#" id="whatsappLink" target="_blank" class="bg-[#25D366] hover:bg-[#20b859] text-white py-3 rounded-xl font-black text-[10px] uppercase tracking-widest transition flex items-center justify-center gap-2">
                <i class="fab fa-whatsapp text-lg"></i> Enviar comprobante por WhatsApp
            </a>
            <button onclick="cerrarModal()" class="w-full text-gray-600 hover:text-gray-400 text-[9px] font-black uppercase tracking-widest mt-1 transition py-2 flex items-center justify-center gap-1">
                <i class="fas fa-times"></i> Cerrar
            </button>
        </div>
    </div>
</div>

<script>
    let productoIndex = 1;
    
    // Establecer fecha límite a 60 días desde hoy
    function setDefaultFecha() {
        const fechaInput = document.getElementById('fecha_limite');
        if (fechaInput && !fechaInput.value) {
            const fecha = new Date();
            fecha.setDate(fecha.getDate() + 60);
            const year = fecha.getFullYear();
            const month = String(fecha.getMonth() + 1).padStart(2, '0');
            const day = String(fecha.getDate()).padStart(2, '0');
            fechaInput.value = `${year}-${month}-${day}`;
        }
    }
    
    function agregarProducto() {
        const container = document.getElementById('productos-container');
        const div = document.createElement('div');
        div.className = 'producto-item bg-white/[0.01] p-3 rounded-xl border border-white/5';
        div.innerHTML = `
            <div class="grid grid-cols-12 gap-2 items-center">
                <div class="col-span-5">
                    <div class="relative">
                        <i class="fas fa-tag absolute left-2 top-1/2 -translate-y-1/2 text-gray-500 text-[10px]"></i>
                        <input type="text" name="productos[${productoIndex}][nombre]" required class="w-full input-glass pl-7 pr-2 py-2 rounded-lg text-xs producto-nombre" placeholder="Nombre del producto *" autocomplete="off">
                    </div>
                </div>
                <div class="col-span-3">
                    <div class="relative">
                        <i class="fas fa-align-left absolute left-2 top-1/2 -translate-y-1/2 text-gray-500 text-[10px]"></i>
                        <input type="text" name="productos[${productoIndex}][descripcion]" class="w-full input-glass pl-7 pr-2 py-2 rounded-lg text-xs" placeholder="Detalles (color, talla...)" autocomplete="off">
                    </div>
                </div>
                <div class="col-span-2">
                    <div class="relative">
                        <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-500 text-[10px]">$</span>
                        <input type="number" step="0.01" name="productos[${productoIndex}][precio]" required class="w-full input-glass pl-5 pr-2 py-2 rounded-lg text-xs precio-producto text-emerald-400 font-bold" onchange="calcularTotal()" onkeyup="calcularTotal()" placeholder="Precio *" min="0.01" autocomplete="off">
                    </div>
                </div>
                <div class="col-span-1">
                    <div class="relative">
                        <i class="fas fa-hashtag absolute left-2 top-1/2 -translate-y-1/2 text-gray-500 text-[10px]"></i>
                        <input type="number" name="productos[${productoIndex}][cantidad]" value="1" min="1" max="999" class="w-full input-glass pl-5 pr-2 py-2 rounded-lg text-xs cantidad-producto text-center" onchange="calcularTotal()" onkeyup="calcularTotal()" autocomplete="off">
                    </div>
                </div>
                <div class="col-span-1 flex justify-center">
                    <button type="button" onclick="eliminarProducto(this)" class="text-red-500/40 hover:text-red-500 transition-colors text-xs">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
        `;
        container.appendChild(div);
        productoIndex++;
        div.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
    
    function eliminarProducto(btn) {
        const items = document.querySelectorAll('.producto-item');
        if (items.length > 1) {
            btn.closest('.producto-item').remove();
            calcularTotal();
        }
    }
    
    function calcularTotal() {
        let total = 0;
        document.querySelectorAll('.producto-item').forEach(item => {
            const precio = parseFloat(item.querySelector('.precio-producto').value) || 0;
            const cantidad = parseInt(item.querySelector('.cantidad-producto').value) || 1;
            total += precio * cantidad;
        });
        document.getElementById('total-productos').textContent = '$' + total.toFixed(2);
        calcularSaldo();
    }
    
    function calcularSaldo() {
        const total = parseFloat(document.getElementById('total-productos').textContent.replace('$', '')) || 0;
        const inicial = parseFloat(document.getElementById('apartadoInicial').value) || 0;
        const saldo = Math.max(0, total - inicial);
        document.getElementById('saldoPendiente').textContent = '$' + saldo.toFixed(2);
        
        const inputInicial = document.getElementById('apartadoInicial');
        if (inicial > total) {
            inputInicial.classList.add('error-border');
            document.getElementById('error-abono').textContent = '⚠️ El abono no puede ser mayor al total';
        } else {
            inputInicial.classList.remove('error-border');
            document.getElementById('error-abono').textContent = '';
        }
    }

    function validarFormulario() {
        let valido = true;
        let errores = [];
        
        const nombre = document.getElementById('nombre_cliente');
        if (!nombre.value.trim()) {
            errores.push('❌ Nombre del cliente requerido');
            valido = false;
        }
        
        const telefono = document.getElementById('telefono_cliente');
        if (!telefono.value.trim()) {
            errores.push('❌ Número de WhatsApp requerido');
            valido = false;
        }
        
        const productos = document.querySelectorAll('.producto-item');
        if (productos.length === 0) {
            errores.push('❌ Agrega al menos un producto');
            valido = false;
        }
        
        productos.forEach((item, index) => {
            const nombreProd = item.querySelector('.producto-nombre').value;
            const precio = item.querySelector('.precio-producto').value;
            if (!nombreProd || !precio || parseFloat(precio) <= 0) {
                errores.push(`❌ Producto #${index + 1}: nombre y precio válido requeridos`);
                valido = false;
            }
        });
        
        const total = parseFloat(document.getElementById('total-productos').textContent.replace('$', '')) || 0;
        const inicial = parseFloat(document.getElementById('apartadoInicial').value) || 0;
        
        if (inicial > total) {
            errores.push('❌ El abono inicial no puede ser mayor al total de productos');
            valido = false;
        }
        
        if (errores.length > 0) {
            alert('⚠️ Por favor corrige los siguientes errores:\n\n' + errores.join('\n'));
        }
        
        return valido;
    }

    function mostrarModal(data) {
        document.getElementById('modalCodigo').textContent = data.codigo;
        document.getElementById('modalCantidadProductos').innerHTML = `<i class="fas fa-boxes"></i> ${data.cantidad_productos} PRODUCTO${data.cantidad_productos !== 1 ? 'S' : ''}`;
        
        const mensaje = `🎉 *APARTADO REGISTRADO* 🎉\n\n👤 *Cliente:* ${data.cliente_nombre}\n🔖 *Código:* ${data.codigo}\n💰 *Total:* $${data.total}\n💵 *Apartado:* $${data.apartado_inicial}\n📊 *Saldo pendiente:* $${data.saldo_pendiente}\n📅 *Vence:* ${data.fecha_limite}\n\n🔗 *Consulta en línea:*\nhttps://deuditas.com.mx/apartado/${data.codigo}\n\n✅ Gracias por tu preferencia`;
        
        const msg = encodeURIComponent(mensaje);
        document.getElementById('whatsappLink').href = `https://wa.me/${data.cliente_telefono}?text=${msg}`;
        
        document.getElementById('successModal').classList.add('active');
    }

    function cerrarModal() { 
        document.getElementById('successModal').classList.remove('active'); 
    }

    document.addEventListener('DOMContentLoaded', () => {
        setDefaultFecha();
        calcularTotal();
        document.getElementById('nombre_cliente').focus();
    });

    @if(session('modal_data'))
        document.addEventListener('DOMContentLoaded', () => mostrarModal(@json(session('modal_data'))));
    @endif
</script>
@endsection