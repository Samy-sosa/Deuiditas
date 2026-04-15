<?php
// app/Http/Controllers/Tienda/ApartadoController.php

namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Apartado;
use App\Models\ApartadoProducto;
use App\Models\PagoApartado;
use App\Models\Tienda;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ApartadoController extends Controller
{
    /**
     * DASHBOARD - Vista principal con estadísticas
     */
    public function dashboard()
    {
        $tiendaId = session('tienda_id');
        
        // Verificar que la tienda existe
        $tienda = Cache::remember('tienda_' . $tiendaId, 3600, function () use ($tiendaId) {
            return Tienda::find($tiendaId);
        });
        
        if (!$tienda) {
            return redirect()->route('login')->with('error', 'Sesión de tienda no válida');
        }

        // Estadísticas en una sola consulta
        $estadisticas = Apartado::where('tienda_id', $tiendaId)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN estado = 'activo' THEN 1 ELSE 0 END) as activos,
                SUM(CASE WHEN estado = 'pagado' THEN 1 ELSE 0 END) as pagados,
                SUM(CASE WHEN estado = 'vencido' THEN 1 ELSE 0 END) as vencidos
            ")
            ->first();

        // Total abonado
        $totalAbonado = PagoApartado::whereHas('apartado', function($q) use ($tiendaId) {
                $q->where('tienda_id', $tiendaId);
            })
            ->sum('monto') ?? 0;

        // Apartados recientes
        $recientes = Apartado::with('productos')
            ->where('tienda_id', $tiendaId)
            ->latest()
            ->limit(5)
            ->get(['id', 'codigo_unico', 'nombre_cliente', 'total', 'saldo_pendiente', 'estado']);

        return view('tienda.dashboard', [
            'activos' => $estadisticas->activos ?? 0,
            'pagados' => $estadisticas->pagados ?? 0,
            'vencidos' => $estadisticas->vencidos ?? 0,
            'total' => $estadisticas->total ?? 0,
            'totalAbonado' => $totalAbonado,
            'recientes' => $recientes,
            'tienda' => $tienda
        ]);
    }

    /**
     * LISTADO de apartados
     */
    public function index()
    {
        $tiendaId = session('tienda_id');
        
        $apartados = Apartado::with('productos')
            ->where('tienda_id', $tiendaId)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('tienda.apartados.index', compact('apartados'));
    }

    /**
     * FORMULARIO de creación
     */
    public function crear()
    {
        return view('tienda.apartados.crear');
    }

    /**
     * GENERAR Y GUARDAR TICKET PARA IMPRESIÓN
     */
private function generarYGuardarTicket($apartado, $tipo = 'nuevo_apartado', $productosEspecificos = null)
{
    try {
        $tienda = Cache::remember('tienda_' . session('tienda_id'), 3600, function () {
            return Tienda::find(session('tienda_id'));
        });
        
        $cantidadProductos = ($productosEspecificos && count($productosEspecificos) > 0) 
            ? count($productosEspecificos) 
            : count($apartado->productos);
        
        $anchoMm = 48; 
        // Aumentamos el alto para que quepan los puntos de relleno al final
        $altoMm = 85 + ($cantidadProductos * 22); 
        
        $anchoPts = $anchoMm * 2.83465;
        $altoPts = $altoMm * 2.83465;
        
        $customPaper = [0, 0, $anchoPts, $altoPts];
        
        $pdf = Pdf::loadView('tienda.ticket_apartado', [
            'apartado' => $apartado,
            'tienda' => $tienda,
            'tipo' => $tipo,
            'productos_especificos' => $productosEspecificos
        ]);
        
        $pdf->setPaper($customPaper, 'portrait');
        $pdf->setOptions([
            'defaultFont' => 'Courier-Bold', // Forzamos fuente negrita desde la base
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'dpi' => 110, // Aumentamos DPI para mayor nitidez
        ]);
        
        $pdfPath = 'tickets/ticket_' . $apartado->codigo_unico . '_' . time() . '.pdf';
        Storage::disk('public')->put($pdfPath, $pdf->output());
        
        return $pdfPath;
        
    } catch (\Exception $e) {
        \Log::error('Error al generar ticket: ' . $e->getMessage());
        return null;
    }
}
    /**
     * GENERAR mensaje para WhatsApp de nuevo apartado
     */
    private function generarMensajeWhatsAppNuevoApartado($apartado)
    {
        $enlaceConsulta = 'https://deuditas.com.mx/apartado/' . $apartado->codigo_unico;
        
        $mensaje = "*NUEVO APARTADO REGISTRADO*\n";
        $mensaje .= "════════════════════\n\n";
        $mensaje .= "Apartado: {$apartado->codigo_unico}\n";
        $mensaje .= "Cliente: {$apartado->nombre_cliente}\n";
        $mensaje .= "Total: $" . number_format($apartado->total, 2) . "\n";
        $mensaje .= "Abono inicial: $" . number_format($apartado->apartado_inicial, 2) . "\n";
        $mensaje .= "Saldo pendiente: $" . number_format($apartado->saldo_pendiente, 2) . "\n";
        $mensaje .= "Fecha límite: " . Carbon::parse($apartado->fecha_limite)->format('d/m/Y') . "\n\n";
        
        $mensaje .= "*PRODUCTOS SEPARADOS:*\n";
        foreach ($apartado->productos as $producto) {
            $mensaje .= "• {$producto->cantidad}x {$producto->nombre_producto} - $" . number_format($producto->subtotal, 2) . "\n";
        }
        
        $mensaje .= "\nConsulta tu apartado en línea:\n";
        $mensaje .= "{$enlaceConsulta}\n\n";
        $mensaje .= "El código {$apartado->codigo_unico} ya está precargado en el enlace\n\n";
        $mensaje .= "Fecha: " . now()->format('d/m/Y H:i') . "\n";
        $mensaje .= "Gracias por tu preferencia.";
        
        return $mensaje;
    }

    /**
     * GUARDAR nuevo apartado (con transacción)
     * ✅ IMPRIME TICKET Y MUESTRA MODAL DE WHATSAPP
     */
    public function guardar(Request $request)
    {
        $request->validate([
            'nombre_cliente' => 'required|string|max:255',
            'telefono_cliente' => 'required|string|max:20',
            'email_cliente' => 'nullable|email|max:255',
            'productos' => 'required|array|min:1',
            'productos.*.nombre' => 'required|string|max:255',
            'productos.*.precio' => 'required|numeric|min:0',
            'productos.*.cantidad' => 'required|integer|min:1',
            'apartado_inicial' => 'required|numeric|min:0',
            'fecha_limite' => 'required|date|after:today',
            'metodo_pago' => 'nullable|string',
            'notas' => 'nullable|string'
        ]);

        // Usar transacción para asegurar integridad
        return DB::transaction(function () use ($request) {
            $tiendaId = session('tienda_id');

            // ============================================
            // PASO 1: BUSCAR O CREAR CLIENTE (CON TIENDA_ID)
            // ============================================
            $cliente = Cliente::where('tienda_id', $tiendaId)
                              ->where('telefono', $request->telefono_cliente)
                              ->first();
            
            if (!$cliente) {
                // Cliente nuevo en esta tienda
                $cliente = Cliente::create([
                    'tienda_id' => $tiendaId,
                    'nombre' => $request->nombre_cliente,
                    'telefono' => $request->telefono_cliente,
                    'email' => $request->email_cliente,
                    'direccion' => null,
                    'notas' => null
                ]);
            } else {
                // Cliente existente: actualizar datos por si cambiaron
                $cliente->nombre = $request->nombre_cliente;
                $cliente->email = $request->email_cliente;
                $cliente->save();
            }

            // ============================================
            // PASO 2: CREAR APARTADO
            // ============================================
            // Obtener iniciales de tienda (cacheadas)
            $inicialesTienda = Cache::remember('tienda_iniciales_' . $tiendaId, 3600, function () use ($tiendaId) {
                return Tienda::where('id', $tiendaId)->value('iniciales');
            });

            // Calcular total
            $total = collect($request->productos)->sum(function($p) {
                return $p['precio'] * $p['cantidad'];
            });

            // Validar apartado inicial
            if ($request->apartado_inicial > $total) {
                return back()->withErrors(['apartado_inicial' => 'El apartado inicial no puede ser mayor al total'])->withInput();
            }

            // Generar código único
            $codigoUnico = $inicialesTienda . now()->format('ymd') . rand(100, 999);
            
            // Verificar si existe
            while (Apartado::where('codigo_unico', $codigoUnico)->exists()) {
                $codigoUnico = $inicialesTienda . now()->format('ymd') . rand(100, 999);
            }

            // Crear apartado con cliente_id
            $apartado = Apartado::create([
                'tienda_id' => $tiendaId,
                'cliente_id' => $cliente->id,
                'codigo_unico' => $codigoUnico,
                'nombre_cliente' => $request->nombre_cliente,
                'telefono_cliente' => $request->telefono_cliente,
                'email_cliente' => $request->email_cliente,
                'total' => $total,
                'apartado_inicial' => $request->apartado_inicial,
                'saldo_pendiente' => $total - $request->apartado_inicial,
                'fecha_limite' => $request->fecha_limite,
                'estado' => 'activo',
                'notas' => $request->notas
            ]);

            // Preparar datos de productos (incluyendo monto_pagado)
            $productosData = collect($request->productos)->map(function($p) use ($apartado) {
                return [
                    'apartado_id' => $apartado->id,
                    'nombre_producto' => $p['nombre'],
                    'descripcion' => $p['descripcion'] ?? null,
                    'precio_unitario' => $p['precio'],
                    'cantidad' => $p['cantidad'],
                    'subtotal' => $p['precio'] * $p['cantidad'],
                    'monto_pagado' => 0,
                    'estado' => 'pendiente',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            })->toArray();

            // Insertar productos
            ApartadoProducto::insert($productosData);

            // REGISTRAR PAGO INICIAL - Usando fecha_pago
            if ($request->apartado_inicial > 0) {
                $this->registrarPagoInicial($apartado, $request);
            }

            // ✅ GENERAR Y GUARDAR TICKET PARA IMPRESIÓN (NUEVO APARTADO)
            $pdfPath = $this->generarYGuardarTicket($apartado, 'nuevo_apartado', null);

            // ✅ PREPARAR DATOS PARA EL MODAL DE WHATSAPP
            $mensajeWhatsApp = $this->generarMensajeWhatsAppNuevoApartado($apartado);
            
            $modalData = [
                'codigo' => $codigoUnico,
                'cliente_nombre' => $request->nombre_cliente,
                'cliente_telefono' => $this->formatearTelefono($request->telefono_cliente),
                'total' => number_format($total, 2),
                'apartado_inicial' => number_format($request->apartado_inicial, 2),
                'fecha_limite' => Carbon::parse($request->fecha_limite)->format('d/m/Y'),
                'cantidad_productos' => count($request->productos),
                'saldo_pendiente' => number_format($total - $request->apartado_inicial, 2),
                'whatsapp_message' => $mensajeWhatsApp
            ];

            // ✅ REDIRIGIR A LA VISTA MOSTRAR CON AMBOS DATOS
            return redirect()->route('tienda.apartados.mostrar', $apartado->id)
                ->with('success', '¡Apartado creado correctamente!')
                ->with('imprimir_ticket', [
                    'url' => $pdfPath ? asset('storage/' . $pdfPath) : null,
                    'codigo' => $apartado->codigo_unico,
                    'tipo' => 'nuevo_apartado'
                ])
                ->with('modal_data', $modalData);
        });
    }

    /**
     * Registrar pago inicial distribuyéndolo entre productos
     * ❌ NO IMPRIME TICKET
     */
    private function registrarPagoInicial($apartado, $request)
    {
        $monto = $request->apartado_inicial;
        $productos = $apartado->productos;
        $montoRestante = $monto;
        
        // Distribuir el pago inicial entre los productos
        foreach ($productos as $producto) {
            if ($montoRestante <= 0) break;
            
            $subtotalProducto = $producto->subtotal;
            $montoAplicar = min($montoRestante, $subtotalProducto);
            
            if ($montoAplicar > 0) {
                // Registrar pago al producto
                $producto->monto_pagado += $montoAplicar;
                if ($producto->monto_pagado >= $producto->subtotal) {
                    $producto->estado = 'pagado';
                }
                $producto->save();
                
                // Registrar pago en la tabla pagos
                PagoApartado::create([
                    'apartado_id' => $apartado->id,
                    'producto_id' => $producto->id,
                    'monto' => $montoAplicar,
                    'metodo_pago' => $request->metodo_pago ?? 'efectivo',
                    'referencia' => 'Pago inicial',
                    'fecha_pago' => now()
                ]);
                
                $montoRestante -= $montoAplicar;
            }
        }
        
        // Actualizar saldo pendiente del apartado
        $apartado->saldo_pendiente = $apartado->total - $monto;
        $apartado->save();
    }

    /**
     * MOSTRAR detalles de un apartado
     */
    public function mostrar($id)
    {
        $apartado = Apartado::with(['pagos.producto', 'productos', 'cliente'])
            ->where('tienda_id', session('tienda_id'))
            ->findOrFail($id);
            
        return view('tienda.apartados.mostrar', compact('apartado'));
    }

    /**
     * EDITAR apartado
     */
    public function editar($id)
    {
        $apartado = Apartado::with('productos')
            ->where('tienda_id', session('tienda_id'))
            ->findOrFail($id);
            
        return view('tienda.apartados.editar', compact('apartado'));
    }

    /**
     * ACTUALIZAR apartado
     */
    public function actualizar(Request $request, $id)
    {
        $apartado = Apartado::where('tienda_id', session('tienda_id'))
            ->findOrFail($id);
        
        // Validar que no esté pagado (no se puede editar un apartado pagado)
        if ($apartado->estado === 'pagado') {
            return back()->with('error', 'No se puede editar un apartado que ya está pagado');
        }
        
        $request->validate([
            'nombre_cliente' => 'required|string|max:255',
            'telefono_cliente' => 'required|string|max:20',
            'email_cliente' => 'nullable|email|max:255',
            'fecha_limite' => 'required|date',
            'notas' => 'nullable|string'
        ]);

        // Validar que la fecha límite no sea anterior a hoy
        if (Carbon::parse($request->fecha_limite)->lt(Carbon::today())) {
            return back()->withErrors(['fecha_limite' => 'La fecha límite no puede ser anterior a hoy'])->withInput();
        }

        DB::transaction(function () use ($request, $apartado) {
            $apartado->update([
                'nombre_cliente' => $request->nombre_cliente,
                'telefono_cliente' => $request->telefono_cliente,
                'email_cliente' => $request->email_cliente,
                'fecha_limite' => $request->fecha_limite,
                'notas' => $request->notas,
                'estado' => $this->calcularEstado($apartado, $request->fecha_limite)
            ]);
            
            // Actualizar datos del cliente si es necesario
            if ($apartado->cliente_id) {
                $cliente = Cliente::find($apartado->cliente_id);
                if ($cliente) {
                    $cliente->nombre = $request->nombre_cliente;
                    $cliente->telefono = $request->telefono_cliente;
                    $cliente->email = $request->email_cliente;
                    $cliente->save();
                }
            }
        });
        
        // Limpiar caché
        Cache::forget('apartado_' . $id);
        
        return redirect()->route('tienda.apartados.mostrar', $id)
            ->with('success', 'Apartado actualizado correctamente');
    }

    /**
     * Calcular estado basado en fecha límite
     */
    private function calcularEstado($apartado, $nuevaFechaLimite)
    {
        // Si ya está pagado, no cambiar
        if ($apartado->estado === 'pagado') {
            return 'pagado';
        }
        
        // Si tiene saldo pendiente y la nueva fecha es menor a hoy
        if ($apartado->saldo_pendiente > 0 && Carbon::parse($nuevaFechaLimite)->lt(Carbon::today())) {
            return 'vencido';
        }
        
        // Si tiene saldo pendiente y la fecha es válida
        if ($apartado->saldo_pendiente > 0) {
            return 'activo';
        }
        
        return $apartado->estado;
    }

    /**
     * ELIMINAR apartado
     */
    public function eliminar($id)
    {
        $apartado = Apartado::where('tienda_id', session('tienda_id'))
            ->findOrFail($id);
            
        DB::transaction(function () use ($apartado) {
            $apartado->productos()->delete();
            $apartado->pagos()->delete();
            $apartado->delete();
        });
        
        Cache::forget('apartado_' . $id);
        
        return redirect()->route('tienda.apartados')
            ->with('success', 'Apartado eliminado correctamente');
    }

    /**
     * REGISTRAR PAGO GENERAL - Distribuye automáticamente entre productos
     * ❌ NO IMPRIME TICKET (solo modal de WhatsApp)
     */
    public function registrarPago(Request $request, $id)
    {
        $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia'
        ]);

        return DB::transaction(function () use ($request, $id) {
            $apartado = Apartado::with('productos')
                ->where('tienda_id', session('tienda_id'))
                ->lockForUpdate()
                ->findOrFail($id);

            if ($apartado->esta_vencido) {
                return back()->with('error', 'Este apartado ya venció');
            }

            if ($apartado->saldo_pendiente <= 0) {
                return back()->with('error', 'Este apartado ya está pagado');
            }

            if ($request->monto > $apartado->saldo_pendiente) {
                return back()->withErrors(['monto' => 'El monto no puede ser mayor al saldo pendiente: $' . number_format($apartado->saldo_pendiente, 2)]);
            }

            // Registrar pago general (se distribuye automáticamente)
            $resultado = $this->distribuirPagoGeneral($apartado, $request->monto, $request->metodo_pago);

            // Preparar datos para el modal de WhatsApp (sin impresión)
            session()->flash('ticket_data', [
                'apartado_id' => $apartado->id,
                'codigo' => $apartado->codigo_unico,
                'cliente' => $apartado->nombre_cliente,
                'cliente_telefono' => $apartado->telefono_cliente,
                'monto' => $request->monto,
                'nuevo_saldo' => $apartado->saldo_pendiente,
                'whatsapp_message' => $this->generarMensajePagoGeneral($apartado, $resultado),
                'es_abono' => true
            ]);

            Cache::forget('apartado_' . $id);

            return redirect()->route('tienda.apartados.mostrar', $id)
                ->with('success', $this->generarMensajeExitoGeneral($apartado, $resultado));
        });
    }

    /**
     * Distribuir un pago general entre los productos pendientes (con números enteros)
     */
    private function distribuirPagoGeneral($apartado, $monto, $metodoPago)
    {
        // Obtener productos con saldo pendiente
        $productosPendientes = $apartado->productos()
            ->where('estado', '!=', 'pagado')
            ->orderBy('id', 'asc')
            ->get()
            ->filter(function ($producto) {
                return $producto->saldo_pendiente > 0;
            });
        
        if ($productosPendientes->isEmpty()) {
            throw new \Exception("No hay productos pendientes en este apartado");
        }
        
        // Calcular saldo total pendiente
        $saldoTotalPendiente = $productosPendientes->sum('saldo_pendiente');
        
        $montoRestante = $monto;
        $pagosRegistrados = [];
        $productosRecibieronPago = [];
        
        // PRIMERA PASADA: Distribuir proporcionalmente (sin decimales)
        foreach ($productosPendientes as $producto) {
            if ($montoRestante <= 0) break;
            
            $saldoProducto = $producto->saldo_pendiente;
            
            // Calcular monto proporcional (redondeado al entero más cercano)
            $proporcion = $saldoProducto / $saldoTotalPendiente;
            $montoProporcional = (int) round($monto * $proporcion, 0);
            
            // Asegurar que no sea menor a 1 (si el producto tiene saldo y el pago es mayor a 0)
            if ($montoProporcional < 1 && $saldoProducto > 0 && $monto >= count($productosPendientes)) {
                $montoProporcional = 1;
            }
            
            // Ajustar si excede el saldo del producto
            $montoAplicar = min($montoProporcional, $saldoProducto);
            
            // Ajustar si excede el monto restante
            $montoAplicar = min($montoAplicar, $montoRestante);
            
            if ($montoAplicar > 0) {
                // Registrar pago al producto
                $producto->monto_pagado += $montoAplicar;
                $producto->save();
                
                // Registrar pago en la tabla pagos
                $pago = PagoApartado::create([
                    'apartado_id' => $apartado->id,
                    'producto_id' => $producto->id,
                    'monto' => $montoAplicar,
                    'metodo_pago' => $metodoPago,
                    'referencia' => "Abono general a {$producto->nombre_producto}",
                    'fecha_pago' => now()
                ]);
                
                $pagosRegistrados[] = $pago;
                
                $productosRecibieronPago[] = [
                    'nombre' => $producto->nombre_producto,
                    'monto' => $montoAplicar,
                    'saldo_restante' => $producto->saldo_pendiente,
                    'completado' => $producto->saldo_pendiente <= 0
                ];
                
                $montoRestante -= $montoAplicar;
            }
        }
        
        // SEGUNDA PASADA: Distribuir el sobrante (redondeo) entre los productos que aún tienen saldo
        if ($montoRestante > 0) {
            $productosAunPendientes = $productosPendientes->filter(function ($producto) {
                return $producto->saldo_pendiente > 0;
            });
            
            // Ordenar por saldo pendiente (mayor a menor) para aplicar el sobrante
            $productosAunPendientes = $productosAunPendientes->sortByDesc('saldo_pendiente');
            
            foreach ($productosAunPendientes as $producto) {
                if ($montoRestante <= 0) break;
                
                $saldoProducto = $producto->saldo_pendiente;
                $montoAplicar = min($montoRestante, $saldoProducto);
                
                if ($montoAplicar > 0) {
                    $producto->monto_pagado += $montoAplicar;
                    $producto->save();
                    
                    $pago = PagoApartado::create([
                        'apartado_id' => $apartado->id,
                        'producto_id' => $producto->id,
                        'monto' => $montoAplicar,
                        'metodo_pago' => $metodoPago,
                        'referencia' => "Abono general adicional a {$producto->nombre_producto}",
                        'fecha_pago' => now()
                    ]);
                    
                    $pagosRegistrados[] = $pago;
                    
                    $productosRecibieronPago[] = [
                        'nombre' => $producto->nombre_producto,
                        'monto' => $montoAplicar,
                        'saldo_restante' => $producto->saldo_pendiente,
                        'completado' => $producto->saldo_pendiente <= 0
                    ];
                    
                    $montoRestante -= $montoAplicar;
                }
            }
        }
        
        // Actualizar el saldo total del apartado
        $apartado->actualizarSaldoTotal();
        
        // Actualizar estado del apartado
        if ($apartado->saldo_pendiente == 0) {
            $apartado->estado = 'pagado';
            $apartado->save();
        }
        
        return [
            'pagos' => $pagosRegistrados,
            'productos' => $productosRecibieronPago,
            'monto_aplicado' => $monto - $montoRestante,
            'monto_excedente' => $montoRestante
        ];
    }

    /**
     * REGISTRAR PAGO A PRODUCTO ESPECÍFICO
     * ❌ NO IMPRIME TICKET (solo modal de WhatsApp)
     */
    public function registrarPagoProducto(Request $request, $id)
    {
        $request->validate([
            'producto_id' => 'required|exists:apartado_productos,id',
            'monto' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia'
        ]);

        return DB::transaction(function () use ($request, $id) {
            $apartado = Apartado::where('tienda_id', session('tienda_id'))
                ->lockForUpdate()
                ->findOrFail($id);
            
            if ($apartado->esta_vencido) {
                return back()->with('error', 'Este apartado ya venció');
            }
            
            $producto = ApartadoProducto::where('id', $request->producto_id)
                ->where('apartado_id', $apartado->id)
                ->lockForUpdate()
                ->firstOrFail();
            
            $saldoProducto = $producto->saldo_pendiente;
            
            if ($saldoProducto <= 0) {
                return back()->with('error', 'Este producto ya está pagado');
            }
            
            if ($request->monto > $saldoProducto) {
                return back()->withErrors(['monto' => "El monto no puede ser mayor al saldo pendiente del producto: $" . number_format($saldoProducto, 2)]);
            }
            
            if ($request->monto > $apartado->saldo_pendiente) {
                return back()->withErrors(['monto' => "El monto no puede ser mayor al saldo pendiente total: $" . number_format($apartado->saldo_pendiente, 2)]);
            }
            
            // Registrar pago al producto específico
            $producto->monto_pagado += $request->monto;
            if ($producto->saldo_pendiente <= 0) {
                $producto->estado = 'pagado';
            }
            $producto->save();
            
            // Registrar el pago
            PagoApartado::create([
                'apartado_id' => $apartado->id,
                'producto_id' => $producto->id,
                'monto' => $request->monto,
                'metodo_pago' => $request->metodo_pago,
                'referencia' => "Abono a {$producto->nombre_producto}",
                'fecha_pago' => now()
            ]);
            
            // Actualizar saldo total del apartado
            $apartado->actualizarSaldoTotal();
            
            // Generar mensaje de WhatsApp específico para este abono
            $mensajeWhatsApp = $this->generarMensajeWhatsAppAbonoProducto($apartado, $producto, $request->monto, $request->metodo_pago);
            
            // Guardar ticket data con el mensaje de WhatsApp (sin impresión)
            session()->flash('ticket_data', [
                'apartado_id' => $apartado->id,
                'codigo' => $apartado->codigo_unico,
                'cliente' => $apartado->nombre_cliente,
                'cliente_telefono' => $apartado->telefono_cliente,
                'monto' => $request->monto,
                'nuevo_saldo' => $apartado->saldo_pendiente,
                'whatsapp_message' => $mensajeWhatsApp,
                'producto' => [
                    'nombre' => $producto->nombre_producto,
                    'saldo_restante' => $producto->saldo_pendiente
                ],
                'es_abono' => true
            ]);
            
            // Generar mensaje de éxito
            $mensaje = "✓ Se registró abono de $" . number_format($request->monto, 2) . 
                       " a '{$producto->nombre_producto}'. " .
                       "Ahora falta $" . number_format($producto->saldo_pendiente, 2) . " de este producto.";
            
            if ($apartado->saldo_pendiente == 0) {
                $mensaje .= " ¡El apartado está completamente pagado!";
            }
            
            return redirect()->route('tienda.apartados.mostrar', $apartado->id)
                ->with('success', $mensaje);
        });
    }
/**
 * Agregar productos a un apartado existente
 * ✅ IMPRIME TICKET Y MUESTRA MODAL DE WHATSAPP
 */
public function agregarProducto(Request $request, $id)
{
    try {
        $request->validate([
            'productos' => 'required|array|min:1',
            'productos.*.nombre' => 'required|string|max:255',
            'productos.*.precio' => 'required|numeric|min:0|max:999999.99',
            'productos.*.cantidad' => 'required|integer|min:1|max:9999'
        ]);

        return DB::transaction(function () use ($request, $id) {
            $apartado = Apartado::where('tienda_id', session('tienda_id'))
                ->where('estado', 'activo')
                ->lockForUpdate()
                ->findOrFail($id);

            $nuevosProductos = [];
            $totalNuevo = 0;
            $productosAgregados = [];

            foreach ($request->productos as $producto) {
                // Asegurar conversión correcta de tipos
                $precio = floatval($producto['precio']);
                $cantidad = intval($producto['cantidad']);
                $subtotal = $precio * $cantidad;
                
                // Validar que no exceda límites de la base de datos
                if ($subtotal > 9999999.99) {
                    throw new \Exception("El subtotal del producto '{$producto['nombre']}' excede el límite permitido");
                }
                
                $totalNuevo += $subtotal;
                
                $nuevosProductos[] = [
                    'apartado_id' => $apartado->id,
                    'nombre_producto' => $producto['nombre'],
                    'descripcion' => $producto['descripcion'] ?? null,
                    'precio_unitario' => $precio,
                    'cantidad' => $cantidad,
                    'subtotal' => $subtotal,
                    'monto_pagado' => 0,
                    'estado' => 'pendiente',
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                
                // ✅ GUARDAR COMO NÚMEROS, NO COMO STRINGS CON FORMATO
                $productosAgregados[] = [
                    'nombre' => $producto['nombre'],
                    'cantidad' => $cantidad,
                    'precio' => $precio,
                    'subtotal' => $subtotal  // Guardar como número, no con number_format()
                ];
            }
            
            // Validar que el total nuevo no exceda límites
            if ($totalNuevo > 9999999.99) {
                throw new \Exception("El total de productos agregados excede el límite permitido");
            }

            // Insertar nuevos productos
            ApartadoProducto::insert($nuevosProductos);

            // Actualizar totales del apartado
            $apartado->total += $totalNuevo;
            $apartado->saldo_pendiente += $totalNuevo;
            $apartado->save();

            // ✅ GENERAR Y GUARDAR TICKET PARA IMPRESIÓN (PASANDO LOS PRODUCTOS ESPECÍFICOS)
            $pdfPath = $this->generarYGuardarTicket($apartado, 'productos_agregados', $productosAgregados);

            // ✅ GENERAR MENSAJE PARA WHATSAPP
            $mensajeWhatsApp = $this->generarMensajeProductosAgregados($apartado, $productosAgregados, $totalNuevo);

            // ✅ PREPARAR DATOS PARA EL MODAL DE WHATSAPP
            $modalData = [
                'tipo' => 'productos_agregados',
                'codigo' => $apartado->codigo_unico,
                'cliente_nombre' => $apartado->nombre_cliente,
                'cliente_telefono' => $this->formatearTelefono($apartado->telefono_cliente),
                'total_agregado' => number_format($totalNuevo, 2),
                'nuevo_total' => number_format($apartado->total, 2),
                'nuevo_saldo' => number_format($apartado->saldo_pendiente, 2),
                'cantidad_productos' => count($request->productos),
                'productos' => $productosAgregados,
                'whatsapp_message' => $mensajeWhatsApp
            ];

            // Retornar JSON para AJAX
            return response()->json([
                'success' => true,
                'redirect' => route('tienda.apartados.mostrar', $apartado->id),
                'message' => 'Se agregaron ' . count($request->productos) . ' productos al apartado',
                'imprimir_ticket' => [
                    'url' => $pdfPath ? asset('storage/' . $pdfPath) : null,
                    'codigo' => $apartado->codigo_unico,
                    'tipo' => 'productos_agregados'
                ],
                'modal_data' => $modalData
            ]);
        });
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error de validación',
            'errors' => $e->errors()
        ], 422);
        
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'success' => false,
            'message' => 'El apartado no existe o ya no está activo'
        ], 404);
        
    } catch (\Exception $e) {
        \Log::error('Error en agregarProducto: ' . $e->getMessage());
        
        // Mensaje más amigable para errores de límites
        $errorMessage = $e->getMessage();
        if (strpos($errorMessage, 'Out of range') !== false || 
            strpos($errorMessage, 'integer') !== false ||
            strpos($errorMessage, 'excede') !== false) {
            $errorMessage = 'Uno o más valores son demasiado grandes. Verifica que los precios no excedan 999,999.99 y las cantidades no excedan 9,999.';
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Error al agregar productos: ' . $errorMessage
        ], 500);
    }
}
    /**
     * GENERAR mensaje de éxito para mostrar en la vista
     */
    private function generarMensajeExitoGeneral($apartado, $resultado)
    {
        $mensaje = "✓ Pago registrado: $" . number_format($resultado['monto_aplicado'], 2) . " distribuido en:\n";
        
        foreach ($resultado['productos'] as $producto) {
            $mensaje .= "• {$producto['nombre']}: $" . number_format($producto['monto'], 2);
            
            if ($producto['saldo_restante'] > 0) {
                $mensaje .= " (falta $" . number_format($producto['saldo_restante'], 2) . ")\n";
            } else {
                $mensaje .= " (COMPLETADO)\n";
            }
        }
        
        if ($resultado['monto_excedente'] > 0) {
            $mensaje .= "⚠️ Excedente: $" . number_format($resultado['monto_excedente'], 2) . " (no se pudo aplicar)";
        }
        
        $mensaje .= "\nSaldo total restante: $" . number_format($apartado->saldo_pendiente, 2);
        
        return $mensaje;
    }

    /**
     * GENERAR mensaje para WhatsApp en pagos generales
     */
    private function generarMensajePagoGeneral($apartado, $resultado)
    {
        $enlaceConsulta = 'https://deuditas.com.mx/apartado/' . $apartado->codigo_unico;
        
        $mensaje = "*PAGO REGISTRADO*\n";
        $mensaje .= "══════════════════\n\n";
        $mensaje .= "Apartado: {$apartado->codigo_unico}\n";
        $mensaje .= "Cliente: {$apartado->nombre_cliente}\n";
        $mensaje .= "Monto abonado: $" . number_format($resultado['monto_aplicado'], 2) . "\n\n";
        
        $mensaje .= "*DISTRIBUCIÓN DEL PAGO:*\n";
        
        foreach ($resultado['productos'] as $producto) {
            $mensaje .= "• {$producto['nombre']}: $" . number_format($producto['monto'], 2);
            
            if ($producto['saldo_restante'] > 0) {
                $mensaje .= " (falta $" . number_format($producto['saldo_restante'], 2) . ")\n";
            } else {
                $mensaje .= " ✓ COMPLETADO\n";
            }
        }
        
        if ($resultado['monto_excedente'] > 0) {
            $mensaje .= "\n⚠️ EXCEDENTE: $" . number_format($resultado['monto_excedente'], 2) . "\n";
            $mensaje .= "El excedente no fue aplicado. Contacta al vendedor.\n";
        }
        
        $mensaje .= "\nSaldo total pendiente: $" . number_format($apartado->saldo_pendiente, 2) . "\n";
        $mensaje .= "Método de pago: " . ucfirst($resultado['pagos'][0]->metodo_pago ?? 'efectivo') . "\n\n";
        $mensaje .= "Consulta tu apartado en línea:\n";
        $mensaje .= "{$enlaceConsulta}\n\n";
        $mensaje .= "Fecha: " . now()->format('d/m/Y H:i') . "\n";
        $mensaje .= "Gracias por tu pago.";
        
        return $mensaje;
    }

    /**
     * GENERAR mensaje para WhatsApp del pago inicial (legacy)
     */
    private function generarMensajeWhatsApp($apartado, $pago, $nuevoSaldo)
    {
        $enlaceConsulta = 'https://deuditas.com.mx/apartado/' . $apartado->codigo_unico;
        
        $mensaje = "*PAGO REGISTRADO*\n";
        $mensaje .= "══════════════════\n\n";
        $mensaje .= "Apartado: {$apartado->codigo_unico}\n";
        $mensaje .= "Cliente: {$apartado->nombre_cliente}\n";
        $mensaje .= "Monto abonado: $" . number_format($pago->monto, 2) . "\n";
        $mensaje .= "Metodo de pago: " . ucfirst($pago->metodo_pago) . "\n";
        
        if ($nuevoSaldo == 0) {
            $mensaje .= "Estado: COMPLETAMENTE PAGADO\n\n";
        } else {
            $mensaje .= "Saldo pendiente: $" . number_format($nuevoSaldo, 2) . "\n\n";
        }
        
        $mensaje .= "Consulta tu apartado en línea:\n";
        $mensaje .= "{$enlaceConsulta}\n\n";
        $mensaje .= "El codigo {$apartado->codigo_unico} ya esta precargado en el enlace\n\n";
        $mensaje .= "Fecha: " . now()->format('d/m/Y H:i') . "\n";
        $mensaje .= "Gracias por tu pago.";
        
        return $mensaje;
    }

    /**
     * GENERAR mensaje para WhatsApp en abono a producto específico
     */
    private function generarMensajeWhatsAppAbonoProducto($apartado, $producto, $monto, $metodoPago)
    {
        $enlaceConsulta = 'https://deuditas.com.mx/apartado/' . $apartado->codigo_unico;
        
        $mensaje = "*PAGO REGISTRADO*\n";
        $mensaje .= "══════════════════\n\n";
        $mensaje .= "Apartado: {$apartado->codigo_unico}\n";
        $mensaje .= "Cliente: {$apartado->nombre_cliente}\n";
        $mensaje .= "Monto abonado: $" . number_format($monto, 2) . "\n";
        $mensaje .= "Producto: {$producto->nombre_producto}\n";
        $mensaje .= "Método de pago: " . ucfirst($metodoPago) . "\n";
        
        if ($producto->saldo_pendiente <= 0) {
            $mensaje .= "✓ Este producto está COMPLETAMENTE PAGADO\n\n";
        } else {
            $mensaje .= "Saldo restante del producto: $" . number_format($producto->saldo_pendiente, 2) . "\n\n";
        }
        
        $mensaje .= "Saldo total pendiente: $" . number_format($apartado->saldo_pendiente, 2) . "\n\n";
        $mensaje .= "Consulta tu apartado en línea:\n";
        $mensaje .= "{$enlaceConsulta}\n\n";
        $mensaje .= "Fecha: " . now()->format('d/m/Y H:i') . "\n";
        $mensaje .= "Gracias por tu pago.";
        
        return $mensaje;
    }

    /**
 * GENERAR mensaje para WhatsApp cuando se agregan productos a un apartado existente
 */
private function generarMensajeProductosAgregados($apartado, $productos, $totalNuevo)
{
    $enlaceConsulta = 'https://deuditas.com.mx/apartado/' . $apartado->codigo_unico;
    
    $mensaje = "*PRODUCTOS AGREGADOS A TU APARTADO*\n";
    $mensaje .= "══════════════════\n\n";
    $mensaje .= "Apartado: {$apartado->codigo_unico}\n";
    $mensaje .= "Cliente: {$apartado->nombre_cliente}\n\n";
    
    $mensaje .= "*NUEVOS PRODUCTOS:*\n";
    foreach ($productos as $producto) {
        // $producto['subtotal'] ahora es un número, no un string
        $mensaje .= "• {$producto['cantidad']}x {$producto['nombre']} - $" . number_format($producto['subtotal'], 2) . "\n";
    }
    
    $mensaje .= "\nTotal agregado: $" . number_format($totalNuevo, 2) . "\n";
    $mensaje .= "Nuevo total del apartado: $" . number_format($apartado->total, 2) . "\n";
    $mensaje .= "Saldo pendiente actualizado: $" . number_format($apartado->saldo_pendiente, 2) . "\n\n";
    
    $mensaje .= "Consulta tu apartado en línea:\n";
    $mensaje .= "{$enlaceConsulta}\n\n";
    $mensaje .= "Fecha: " . now()->format('d/m/Y H:i') . "\n";
    $mensaje .= "Gracias por tu preferencia.";
    
    return $mensaje;
}
    /**
     * Mostrar formulario para agregar productos a un apartado existente
     */
    public function formularioAgregarProducto($id)
    {
        $apartado = Apartado::where('tienda_id', session('tienda_id'))
            ->where('estado', 'activo')
            ->findOrFail($id);
        
        return view('tienda.apartados.agregar-producto', compact('apartado'));
    }

    /**
     * BUSCAR apartados
     */
    public function buscar(Request $request)
    {
        $query = $request->get('q');
        $tiendaId = session('tienda_id');
        
        // Validar que haya término de búsqueda
        if (!$query || strlen($query) < 2) {
            return redirect()->route('tienda.dashboard')
                ->with('error', 'Ingresa al menos 2 caracteres para buscar');
        }

        $resultados = Apartado::where('tienda_id', $tiendaId)
            ->where(function($q) use ($query) {
                $q->where('codigo_unico', 'LIKE', '%' . $query . '%')
                  ->orWhere('nombre_cliente', 'LIKE', '%' . $query . '%')
                  ->orWhere('telefono_cliente', 'LIKE', '%' . $query . '%');
            })
            ->with('productos')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends(['q' => $query]);

        if ($resultados->isEmpty()) {
            return view('tienda.apartados.buscar', [
                'resultados' => $resultados,
                'termino' => $query,
                'mensaje' => 'No se encontraron resultados para "' . $query . '"'
            ]);
        }

        return view('tienda.apartados.buscar', [
            'resultados' => $resultados,
            'termino' => $query
        ]);
    }

    /**
     * FORMATEAR teléfono para WhatsApp
     */
    private function formatearTelefono($telefono)
    {
        $telefono = preg_replace('/[^0-9]/', '', $telefono);
        
        if (strlen($telefono) == 10) {
            $telefono = '52' . $telefono;
        }
        
        return $telefono;
    }

    /**
     * Registrar actividad (placeholder)
     */
    private function registrarActividad($apartado, $tipo, $datos)
    {
        // Puedes implementar log de actividades aquí si lo deseas
        // Por ahora es un placeholder
        \Log::info('Actividad registrada', [
            'apartado_id' => $apartado->id,
            'tipo' => $tipo,
            'datos' => $datos
        ]);
    }
}