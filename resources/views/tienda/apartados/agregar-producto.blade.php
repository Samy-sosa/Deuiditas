@extends('layouts.tienda')

@section('title', 'Agregar productos - #' . $apartado->codigo_unico)

@section('content')
<div class="max-w-4xl mx-auto px-4">
    <div class="mb-6">
        <a href="{{ route('tienda.apartados.mostrar', $apartado->id) }}" class="text-gray-400 hover:text-blue-500 transition flex items-center gap-2 group">
            <i class="fas fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
            <span class="font-medium">Volver al apartado</span>
        </a>
    </div>

    <div class="glass rounded-3xl overflow-hidden border border-white/10 shadow-2xl">
        {{-- Cabecera --}}
        <div class="px-8 py-6 border-b border-white/5 bg-white/5">
            <h1 class="text-2xl font-black text-white">
                Agregar productos a <span class="text-blue-500">#{{ $apartado->codigo_unico }}</span>
            </h1>
            <p class="text-gray-400 text-sm mt-1">Cliente: {{ $apartado->nombre_cliente }}</p>
        </div>

        <div class="p-8">
            {{-- Información actual del apartado --}}
            <div class="grid grid-cols-2 gap-4 mb-6 p-4 bg-white/5 rounded-xl border border-white/10">
                <div>
                    <p class="text-[10px] text-gray-500 uppercase font-black tracking-widest">Total actual</p>
                    <p class="text-xl font-black text-white">${{ number_format($apartado->total, 2) }}</p>
                </div>
                <div>
                    <p class="text-[10px] text-gray-500 uppercase font-black tracking-widest">Saldo pendiente</p>
                    <p class="text-xl font-black text-red-400">${{ number_format($apartado->saldo_pendiente, 2) }}</p>
                </div>
            </div>

            {{-- Formulario de productos --}}
            <form id="productosForm">
                @csrf
                
                {{-- Contenedor de productos --}}
                <div id="productosContainer" class="space-y-4 mb-6">
                    <div class="producto-item bg-white/5 p-4 rounded-xl border border-white/10">
                        <div class="flex justify-end mb-3">
                            <button type="button" onclick="this.closest('.producto-item').remove(); actualizarResumen();" 
                                    class="text-red-500/50 hover:text-red-500 text-xs flex items-center gap-1 transition">
                                <i class="fas fa-trash-alt"></i> Eliminar
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-gray-400 text-xs mb-1 font-bold uppercase tracking-wider">Producto *</label>
                                <input type="text" name="productos[0][nombre]" required
                                       class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition"
                                       placeholder="Ej: Vestido azul">
                            </div>
                            <div>
                                <label class="block text-gray-400 text-xs mb-1 font-bold uppercase tracking-wider">Cantidad *</label>
                                <input type="number" name="productos[0][cantidad]" value="1" min="1" max="9999" required
                                       class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition">
                            </div>
                            <div>
                                <label class="block text-gray-400 text-xs mb-1 font-bold uppercase tracking-wider">Precio unitario *</label>
                                <input type="number" step="0.01" name="productos[0][precio]" required
                                       min="0.01" max="999999.99"
                                       class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition"
                                       placeholder="0.00">
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="block text-gray-400 text-xs mb-1 font-bold uppercase tracking-wider">Descripción (opcional)</label>
                            <input type="text" name="productos[0][descripcion]"
                                   class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition"
                                   placeholder="Color, talla, detalles...">
                        </div>
                    </div>
                </div>

                {{-- Botón para agregar otro producto --}}
                <button type="button" onclick="agregarProductoField()" 
                        class="mb-6 text-blue-500 hover:text-blue-400 text-sm flex items-center gap-2 transition">
                    <i class="fas fa-plus-circle text-lg"></i> Agregar otro producto
                </button>

                {{-- Resumen de lo que se agregará --}}
                <div class="bg-blue-600/10 border border-blue-500/20 rounded-xl p-4 mb-6">
                    <p class="text-[10px] text-blue-400 font-black uppercase tracking-widest mb-2">RESUMEN DE NUEVOS PRODUCTOS</p>
                    <div id="resumenProductos" class="space-y-1 text-sm">
                        <p class="text-gray-400">Aún no hay productos agregados</p>
                    </div>
                    <div class="mt-3 pt-3 border-t border-blue-500/20 flex justify-between">
                        <span class="text-gray-300 font-bold">Total a agregar:</span>
                        <span id="totalNuevo" class="text-white font-black text-lg">$0.00</span>
                    </div>
                </div>

                {{-- Botones de acción --}}
                <div class="flex gap-4">
                    <button type="button" onclick="procesarAgregarProductos()" 
                            id="btnAgregar"
                            class="flex-1 bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 rounded-xl transition flex items-center justify-center gap-2">
                        <i class="fas fa-plus-circle"></i> Agregar productos
                    </button>
                    <a href="{{ route('tienda.apartados.mostrar', $apartado->id) }}" 
                       class="flex-1 text-center bg-gray-600/30 hover:bg-gray-600/50 text-white font-bold py-3 rounded-xl transition">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-black/90 backdrop-blur-md flex items-center justify-center z-[999] hidden">
    <div class="text-center">
        <div class="w-16 h-16 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
        <p class="text-white font-bold">Agregando productos...</p>
    </div>
</div>

<script>
    let productoIndex = 1;
    
    // Función para actualizar el resumen en tiempo real
    function actualizarResumen() {
        const productos = document.querySelectorAll('.producto-item');
        const resumenContainer = document.getElementById('resumenProductos');
        let totalNuevo = 0;
        let productosHtml = '';
        
        productos.forEach((item, idx) => {
            const nombreInput = item.querySelector('input[name*="[nombre]"]');
            const cantidadInput = item.querySelector('input[name*="[cantidad]"]');
            const precioInput = item.querySelector('input[name*="[precio]"]');
            
            const nombre = nombreInput ? nombreInput.value : '';
            const cantidad = cantidadInput ? parseInt(cantidadInput.value) || 0 : 0;
            const precio = precioInput ? parseFloat(precioInput.value) || 0 : 0;
            const subtotal = cantidad * precio;
            
            if (nombre && cantidad > 0 && precio > 0) {
                totalNuevo += subtotal;
                productosHtml += `
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-gray-300">${cantidad}x ${nombre}</span>
                        <span class="text-white font-bold">$${subtotal.toFixed(2)}</span>
                    </div>
                `;
            } else if (nombre || cantidad || precio) {
                productosHtml += `
                    <div class="flex justify-between items-center text-xs text-yellow-500">
                        <span>⚠️ ${nombre || 'Producto'} - Datos incompletos</span>
                        <span>---</span>
                    </div>
                `;
            }
        });
        
        if (productosHtml) {
            resumenContainer.innerHTML = productosHtml;
        } else {
            resumenContainer.innerHTML = '<p class="text-gray-400">Aún no hay productos agregados</p>';
        }
        
        document.getElementById('totalNuevo').innerText = `$${totalNuevo.toFixed(2)}`;
    }
    
    // Escuchar cambios en los inputs para actualizar resumen
    document.addEventListener('input', function(e) {
        if (e.target.matches('input[name*="[nombre]"], input[name*="[cantidad]"], input[name*="[precio]"]')) {
            actualizarResumen();
        }
    });
    
    // Agregar nuevo campo de producto
    function agregarProductoField() {
        const container = document.getElementById('productosContainer');
        const newItem = document.createElement('div');
        newItem.className = 'producto-item bg-white/5 p-4 rounded-xl border border-white/10';
        newItem.innerHTML = `
            <div class="flex justify-end mb-3">
                <button type="button" onclick="this.closest('.producto-item').remove(); actualizarResumen();" 
                        class="text-red-500/50 hover:text-red-500 text-xs flex items-center gap-1 transition">
                    <i class="fas fa-trash-alt"></i> Eliminar
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-gray-400 text-xs mb-1 font-bold uppercase tracking-wider">Producto *</label>
                    <input type="text" name="productos[${productoIndex}][nombre]" required
                           class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition"
                           placeholder="Ej: Vestido azul">
                </div>
                <div>
                    <label class="block text-gray-400 text-xs mb-1 font-bold uppercase tracking-wider">Cantidad *</label>
                    <input type="number" name="productos[${productoIndex}][cantidad]" value="1" min="1" max="9999" required
                           class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition">
                </div>
                <div>
                    <label class="block text-gray-400 text-xs mb-1 font-bold uppercase tracking-wider">Precio unitario *</label>
                    <input type="number" step="0.01" name="productos[${productoIndex}][precio]" required
                           min="0.01" max="999999.99"
                           class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition"
                           placeholder="0.00">
                </div>
            </div>
            <div class="mt-3">
                <label class="block text-gray-400 text-xs mb-1 font-bold uppercase tracking-wider">Descripción (opcional)</label>
                <input type="text" name="productos[${productoIndex}][descripcion]"
                       class="w-full bg-black/20 border border-white/10 rounded-xl px-4 py-2.5 text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition"
                       placeholder="Color, talla, detalles...">
            </div>
        `;
        container.appendChild(newItem);
        
        // Agregar event listeners a los nuevos inputs
        const inputs = newItem.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('input', actualizarResumen);
        });
        
        productoIndex++;
        actualizarResumen();
    }
    
    // Procesar el envío del formulario
    function procesarAgregarProductos() {
        const form = document.getElementById('productosForm');
        const formData = new FormData(form);
        
        // Validar que haya al menos un producto con datos completos
        const productos = document.querySelectorAll('.producto-item');
        let productosValidos = 0;
        let errores = [];
        
        productos.forEach((item, index) => {
            const nombre = item.querySelector('input[name*="[nombre]"]')?.value;
            const cantidad = item.querySelector('input[name*="[cantidad]"]')?.value;
            const precio = item.querySelector('input[name*="[precio]"]')?.value;
            
            // Validar nombre
            if (!nombre || nombre.trim() === '') {
                errores.push(`Producto #${index + 1}: Nombre es requerido`);
                return;
            }
            
            // Validar cantidad
            if (!cantidad) {
                errores.push(`Producto #${index + 1}: Cantidad es requerida`);
                return;
            }
            
            const cantidadNum = parseInt(cantidad);
            if (isNaN(cantidadNum) || cantidadNum <= 0) {
                errores.push(`Producto #${index + 1}: Cantidad debe ser un número positivo`);
                return;
            }
            
            if (cantidadNum > 9999) {
                errores.push(`Producto #${index + 1}: Cantidad no puede ser mayor a 9,999`);
                return;
            }
            
            // Validar precio
            if (!precio) {
                errores.push(`Producto #${index + 1}: Precio es requerido`);
                return;
            }
            
            const precioNum = parseFloat(precio);
            if (isNaN(precioNum) || precioNum <= 0) {
                errores.push(`Producto #${index + 1}: Precio debe ser un número positivo`);
                return;
            }
            
            if (precioNum > 999999.99) {
                errores.push(`Producto #${index + 1}: Precio no puede ser mayor a 999,999.99`);
                return;
            }
            
            productosValidos++;
        });
        
        if (errores.length > 0) {
            alert('⚠️ Errores en el formulario:\n\n' + errores.join('\n'));
            return;
        }
        
        if (productosValidos === 0) {
            alert('⚠️ Agrega al menos un producto con nombre, cantidad y precio válidos');
            return;
        }
        
        const btnAgregar = document.getElementById('btnAgregar');
        const loadingOverlay = document.getElementById('loadingOverlay');
        const textoOriginal = btnAgregar.innerHTML;
        
        btnAgregar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Agregando...';
        btnAgregar.disabled = true;
        loadingOverlay.classList.remove('hidden');
        
        fetch('{{ route("tienda.apartados.agregar-producto.store", $apartado->id) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || 'Error en el servidor');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Guardar datos en sessionStorage para mostrar el modal en la página de mostrar
                sessionStorage.setItem('mostrar_modal_productos', JSON.stringify(data.modal_data));
                sessionStorage.setItem('imprimir_ticket_productos', JSON.stringify(data.imprimir_ticket));
                
                // Mostrar mensaje de éxito
                const successDiv = document.createElement('div');
                successDiv.className = 'fixed top-4 right-4 z-50 bg-green-500/90 backdrop-blur-md text-white px-6 py-3 rounded-xl shadow-2xl animate-fadeIn flex items-center gap-3';
                successDiv.innerHTML = `
                    <i class="fas fa-check-circle text-xl"></i>
                    <span>${data.message}</span>
                `;
                document.body.appendChild(successDiv);
                
                setTimeout(() => {
                    successDiv.remove();
                    window.location.href = data.redirect;
                }, 1500);
            } else {
                alert(data.message || 'Error al agregar productos');
                btnAgregar.innerHTML = textoOriginal;
                btnAgregar.disabled = false;
                loadingOverlay.classList.add('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            let errorMsg = error.message;
            
            // Manejar errores específicos de la base de datos
            if (errorMsg.includes('Out of range') || errorMsg.includes('integer')) {
                errorMsg = 'El valor ingresado es demasiado grande. Verifica los precios y cantidades.';
            } else if (errorMsg.includes('max')) {
                errorMsg = 'El precio o cantidad excede el límite permitido.';
            }
            
            alert('Error al agregar productos: ' + errorMsg);
            btnAgregar.innerHTML = textoOriginal;
            btnAgregar.disabled = false;
            loadingOverlay.classList.add('hidden');
        });
    }
    
    // Inicializar resumen al cargar la página
    document.addEventListener('DOMContentLoaded', () => {
        actualizarResumen();
    });
</script>

<style>
    .animate-fadeIn {
        animation: fadeIn 0.3s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection