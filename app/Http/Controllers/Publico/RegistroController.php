<?php
namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tienda;
use App\Models\UsuarioTienda;
use App\Models\Cupon;
use App\Models\Pago;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;

class RegistroController extends Controller
{
    private function limpiarTexto($texto)
    {
        $texto = preg_replace('/[áàäâª]/u', 'a', $texto);
        $texto = preg_replace('/[éèëê]/u', 'e', $texto);
        $texto = preg_replace('/[íìïî]/u', 'i', $texto);
        $texto = preg_replace('/[óòöô]/u', 'o', $texto);
        $texto = preg_replace('/[úùüû]/u', 'u', $texto);
        $texto = preg_replace('/[ñ]/u', 'n', $texto);
        $texto = preg_replace('/[^a-zA-Z0-9]/', '', $texto);
        return $texto;
    }

    public function procesar(Request $request)
    {
        Log::info('Intentando procesar registro:', $request->all());
        
        $request->validate([
            'nombre_tienda' => 'required|string|max:100|unique:tiendas,nombre_tienda',
            'email'         => 'required|email|unique:tiendas,email|unique:usuarios_tienda,email',
            'password'      => 'required|min:8|confirmed',
            'plan'          => 'required|in:mensual,trimestral,anual', // ← AGREGADO: trimestral
            'cupon'         => 'nullable|string|max:50'
        ], [
            'nombre_tienda.unique' => 'El nombre de la tienda ya está en uso.',
            'email.unique'         => 'Este correo ya está registrado.',
            'password.confirmed'   => 'Las contraseñas no coinciden.',
            'password.min'         => 'La contraseña debe tener al menos 8 caracteres.'
        ]);

        // ============================================
        // NUEVA ESTRUCTURA DE PLANES CON TRIMESTRAL
        // ============================================
        $planes = [
            'mensual' => [
                'dias' => 30, 
                'precio' => 199, 
                'nombre' => 'Plan Mensual',
                'descripcion' => 'Facturación mensual'
            ],
            'trimestral' => [ // ← NUEVO PLAN
                'dias' => 90, 
                'precio' => 497, // Estrategia de ahorro: 3 meses = $497 (ahorro de $100)
                'nombre' => 'Plan Trimestral',
                'descripcion' => 'Facturación trimestral (ahorra $100)'
            ],
            'anual' => [
                'dias' => 365, 
                'precio' => 1790, // Estrategia de ahorro: 12 meses = $1,790 (ahorro de $598)
                'nombre' => 'Plan Anual',
                'descripcion' => 'Facturación anual (ahorra $598)'
            ]
        ];

        if (!isset($planes[$request->plan])) {
            return response()->json(['success' => false, 'message' => 'Plan no válido'], 422);
        }

        $planSeleccionado = $planes[$request->plan];
        $diasPlan = $planSeleccionado['dias'];
        $precioBase = $planSeleccionado['precio'];

        DB::beginTransaction();
        try {
            // Generar iniciales únicas
            $nombre = $request->nombre_tienda;
            $nombreLimpio = $this->limpiarTexto($nombre);
            $baseIniciales = strtoupper(substr($nombreLimpio, 0, 3));

            if (strlen($baseIniciales) < 3) {
                $baseIniciales = str_pad($baseIniciales, 3, 'X');
            }

            $iniciales = $baseIniciales;
            $contador = 1;

            while (Tienda::where('iniciales', $iniciales)->exists()) {
                $iniciales = $baseIniciales . $contador;
                $contador++;
            }

            // Calcular total con cupón
            $total = $precioBase;
            $descuentoAplicado = 0;
            $cuponAplicado = null;
            
            if ($request->cupon) {
                $cupon = Cupon::where('codigo', strtoupper($request->cupon))
                    ->where('activo', true)
                    ->where('fecha_inicio', '<=', Carbon::now())
                    ->where('fecha_expiracion', '>=', Carbon::now())
                    ->first();

                if ($cupon) {
                    $cuponAplicado = $cupon->codigo;
                    
                    if ($cupon->tipo_descuento === 'porcentaje') {
                        $descuento = ($precioBase * $cupon->valor_descuento) / 100;
                    } else {
                        $descuento = min($cupon->valor_descuento, $precioBase);
                    }
                    
                    $descuentoAplicado = $descuento;
                    $total = $precioBase - $descuento;
                    
                    // Incrementar usos del cupón
                    $cupon->increment('usos_actuales');
                    
                    Log::info('Cupón aplicado:', [
                        'codigo' => $cupon->codigo, 
                        'descuento' => $descuento,
                        'total_final' => $total
                    ]);
                }
            }

            // Crear tienda (estado pendiente_pago)
            $tienda = Tienda::create([
                'nombre_tienda'    => $request->nombre_tienda,
                'iniciales'        => $iniciales,
                'email'            => $request->email,
                'plan_tipo'        => $request->plan,
                'estado'           => 'pendiente_pago',
                'fecha_inicio'     => null,
                'fecha_expiracion' => null,
                'mp_preference_id' => null,
                'mp_payment_id'    => null,
                'mp_status'        => null
            ]);

            // Crear usuario administrador
            UsuarioTienda::create([
                'tienda_id' => $tienda->id,
                'nombre'    => 'Administrador',
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'rol'       => 'admin_tienda',
                'activo'    => true
            ]);

            DB::commit();

            // ============================================
            // REGISTRAR EL PAGO EN LA TABLA `pagos`
            // ============================================
            $externalReference = 'REG-' . $tienda->id . '-' . time();

            $pago = Pago::create([
                'tienda_id' => $tienda->id,
                'tipo' => 'registro',
                'monto' => $total,
                'plan' => $request->plan,
                'metodo_pago' => 'mercadopago',
                'external_reference' => $externalReference,
                'status' => 'pending',
                'payment_id' => null,
                'raw_data' => json_encode([
                    'plan_nombre' => $planSeleccionado['nombre'],
                    'plan_descripcion' => $planSeleccionado['descripcion'],
                    'dias' => $diasPlan,
                    'cupon' => $cuponAplicado,
                    'precio_base' => $precioBase,
                    'descuento' => $descuentoAplicado,
                    'total' => $total
                ])
            ]);

            // ============================================
            // INTEGRACIÓN CON MERCADO PAGO
            // ============================================
            try {
                // Configurar Mercado Pago
                MercadoPagoConfig::setAccessToken(env('MERCADO_PAGO_ACCESS_TOKEN'));
                
                // Crear cliente de preferencia
                $client = new PreferenceClient();
                
                // Crear descripción detallada del plan
                $descripcionItems = [];
                
                // Agregar descripción del plan base
                $descripcionItems[] = $planSeleccionado['descripcion'];
                
                // Si aplicó cupón, agregarlo a la descripción
                if ($descuentoAplicado > 0) {
                    $descripcionItems[] = "Cupón aplicado: $" . number_format($descuentoAplicado, 2);
                }
                
                $item = [
                    "title" => $planSeleccionado['nombre'] . " - " . $request->nombre_tienda,
                    "quantity" => 1,
                    "currency_id" => "MXN",
                    "unit_price" => (float) $total,
                    "description" => implode(" | ", $descripcionItems)
                ];
                
                $backUrls = [
                    "success" => route('pago.exitoso'),
                    "failure" => route('pago.fallido'),
                    "pending" => route('pago.pendiente')
                ];
                
                // Usar la external reference que guardamos
                $preference = $client->create([
                    "items" => [$item],
                    "back_urls" => $backUrls,
                    "auto_return" => "approved",
                    "external_reference" => $externalReference,
                    "notification_url" => route('webhook.mercadopago'),
                    "statement_descriptor" => "DEUDITAS"
                ]);
                
                // Actualizar el pago con la preferencia
                $rawData = json_decode($pago->raw_data, true);
                $rawData['preference_id'] = $preference->id;
                $rawData['init_point'] = $preference->init_point;
                $pago->raw_data = json_encode($rawData);
                $pago->save();
                
                // Guardar preference_id en tienda
                $tienda->mp_preference_id = $preference->id;
                $tienda->save();
                
                Log::info('Preferencia MP creada:', [
                    'preference_id' => $preference->id,
                    'external_ref' => $externalReference,
                    'total' => $total
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Redirigiendo a Mercado Pago...',
                    'redirect_url' => $preference->init_point
                ]);
                
            } catch (MPApiException $e) {
                // Si falla MP, marcar el pago como rechazado
                $pago->status = 'rejected';
                $pago->raw_data = json_encode([
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $pago->save();
                
                Log::error('Error Mercado Pago:', [
                    'message' => $e->getMessage(),
                    'response' => $e->getApiResponse() ?? 'No response',
                    'pago_id' => $pago->id
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Error al conectar con Mercado Pago. Intenta más tarde.'
                ], 500);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error crítico en registro:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Hubo un problema interno: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function validarCupon(Request $request)
    {
        $codigo = $request->get('codigo');
        $plan = $request->get('plan');
        
        // ============================================
        // ACTUALIZADO: Incluir plan trimestral
        // ============================================
        $planes = [
            'mensual' => 199,
            'trimestral' => 497, // ← NUEVO
            'anual' => 1790
        ];
        
        if (!isset($planes[$plan])) {
            return response()->json([
                'valido' => false,
                'mensaje' => 'Plan no válido'
            ]);
        }
        
        $monto = $planes[$plan];
        
        $cupon = Cupon::where('codigo', strtoupper($codigo))
            ->where('activo', true)
            ->where('fecha_inicio', '<=', Carbon::now())
            ->where('fecha_expiracion', '>=', Carbon::now())
            ->first();
        
        if (!$cupon) {
            return response()->json([
                'valido' => false,
                'mensaje' => 'Cupón no válido o expirado'
            ]);
        }
        
        if ($cupon->usos_maximos && $cupon->usos_actuales >= $cupon->usos_maximos) {
            return response()->json([
                'valido' => false,
                'mensaje' => 'Este cupón ya no tiene usos disponibles'
            ]);
        }
        
        if ($cupon->monto_minimo && $monto < $cupon->monto_minimo) {
            return response()->json([
                'valido' => false,
                'mensaje' => 'Este cupón aplica para compras mayores a $' . number_format($cupon->monto_minimo, 2)
            ]);
        }
        
        if ($cupon->tipo_descuento === 'porcentaje') {
            $descuento = ($monto * $cupon->valor_descuento) / 100;
            $textoDescuento = $cupon->valor_descuento . '%';
        } else {
            $descuento = min($cupon->valor_descuento, $monto);
            $textoDescuento = '$' . number_format($cupon->valor_descuento, 2);
        }
        
        $total = $monto - $descuento;
        
        // Calcular ahorro respecto al plan mensual (para mostrarlo en la UI)
        $ahorroInfo = null;
        if ($plan === 'trimestral') {
            $costoMensualSinDescuento = 199 * 3; // $597
            $ahorroTotal = $costoMensualSinDescuento - $total;
            $ahorroInfo = "Ahorras $" . number_format($ahorroTotal, 2) . " vs. el plan mensual";
        } elseif ($plan === 'anual') {
            $costoMensualSinDescuento = 199 * 12; // $2,388
            $ahorroTotal = $costoMensualSinDescuento - $total;
            $ahorroInfo = "Ahorras $" . number_format($ahorroTotal, 2) . " vs. el plan mensual";
        }
        
        return response()->json([
            'valido' => true,
            'descuento' => round($descuento, 2),
            'total' => round($total, 2),
            'mensaje' => "¡Cupón válido! Descuento: $textoDescuento",
            'cupon_id' => $cupon->id,
            'ahorro_info' => $ahorroInfo // ← NUEVO: Información de ahorro para mostrar al cliente
        ]);
    }

    public function pagoExitoso(Request $request)
    {
        Log::info('Pago exitoso recibido:', $request->all());
        
        $paymentId = $request->get('payment_id');
        $externalRef = $request->get('external_reference'); // REG-123-123456
        $status = $request->get('status', 'approved');
        
        // Buscar el pago por external_reference
        $pago = Pago::where('external_reference', $externalRef)->first();
        
        if (!$pago) {
            Log::error('Pago no encontrado:', ['external_ref' => $externalRef]);
            return redirect()->route('landing')->with('error', 'Referencia de pago no válida');
        }
        
        // Actualizar el pago
        $pago->payment_id = $paymentId;
        $pago->status = $status;
        $pago->save();
        
        // Buscar la tienda
        $tienda = Tienda::find($pago->tienda_id);
        
        if (!$tienda) {
            return redirect()->route('landing')->with('error', 'Tienda no encontrada');
        }
        
        // ============================================
        // ACTUALIZADO: Incluir plan trimestral
        // ============================================
        $dias = [
            'mensual' => 30,
            'trimestral' => 90, // ← NUEVO
            'anual' => 365
        ];
        
        // Activar la tienda
        $tienda->mp_payment_id = $paymentId;
        $tienda->mp_status = $status;
        $tienda->estado = 'activa';
        $tienda->fecha_inicio = Carbon::now();
        $tienda->fecha_expiracion = Carbon::now()->addDays($dias[$tienda->plan_tipo]);
        $tienda->save();
        
        Log::info('Tienda activada por pago exitoso:', [
            'tienda_id' => $tienda->id,
            'pago_id' => $pago->id,
            'plan' => $tienda->plan_tipo,
            'dias' => $dias[$tienda->plan_tipo],
            'expiracion' => $tienda->fecha_expiracion
        ]);
        
        return redirect()->route('login')->with('success', '¡Pago exitoso! Ya puedes iniciar sesión en ' . $tienda->nombre_tienda);
    }

    public function pagoFallido(Request $request)
    {
        Log::info('Pago fallido:', $request->all());
        
        $externalRef = $request->get('external_reference');
        
        if ($externalRef) {
            Pago::where('external_reference', $externalRef)
                ->update(['status' => 'rejected']);
        }
        
        return redirect()->route('landing')->with('error', 'El pago no pudo completarse. Intenta de nuevo.');
    }

    public function pagoPendiente(Request $request)
    {
        Log::info('Pago pendiente:', $request->all());
        
        $externalRef = $request->get('external_reference');
        
        if ($externalRef) {
            Pago::where('external_reference', $externalRef)
                ->update(['status' => 'pending']);
        }
        
        return redirect()->route('landing')->with('info', 'Tu pago está siendo procesado. Te notificaremos cuando se confirme.');
    }

    public function webhook(Request $request)
    {
        Log::info('Webhook recibido:', $request->all());
        
        $data = $request->all();
        
        if (isset($data['type']) && $data['type'] === 'payment') {
            $paymentId = $data['data']['id'];
            
            try {
                MercadoPagoConfig::setAccessToken(env('MERCADO_PAGO_ACCESS_TOKEN'));
                
                $client = new \MercadoPago\Client\Payment\PaymentClient();
                $payment = $client->get($paymentId);
                
                if ($payment && isset($payment->external_reference)) {
                    // Buscar el pago por external_reference
                    $pago = Pago::where('external_reference', $payment->external_reference)->first();
                    
                    if ($pago) {
                        // Actualizar el pago
                        $pago->payment_id = $paymentId;
                        $pago->status = $payment->status;
                        
                        // Combinar datos existentes con nuevos
                        $rawData = json_decode($pago->raw_data, true) ?? [];
                        $rawData['webhook_data'] = json_decode(json_encode($payment), true);
                        $pago->raw_data = json_encode($rawData);
                        $pago->save();
                        
                        // Si es aprobado y es de tipo registro, activar tienda
                        if ($payment->status === 'approved' && $pago->tipo === 'registro') {
                            $tienda = Tienda::find($pago->tienda_id);
                            
                            if ($tienda) {
                                // ============================================
                                // ACTUALIZADO: Incluir plan trimestral
                                // ============================================
                                $dias = [
                                    'mensual' => 30,
                                    'trimestral' => 90, // ← NUEVO
                                    'anual' => 365
                                ];
                                
                                $tienda->mp_payment_id = $paymentId;
                                $tienda->mp_status = 'approved';
                                $tienda->estado = 'activa';
                                $tienda->fecha_inicio = Carbon::now();
                                $tienda->fecha_expiracion = Carbon::now()->addDays($dias[$tienda->plan_tipo]);
                                $tienda->save();
                                
                                Log::info('Tienda activada por webhook:', [
                                    'tienda_id' => $tienda->id,
                                    'pago_id' => $pago->id
                                ]);
                            }
                        }
                        
                        // Si es aprobado y es de tipo renovacion, extender suscripción
                        if ($payment->status === 'approved' && $pago->tipo === 'renovacion') {
                            $tienda = Tienda::find($pago->tienda_id);
                            
                            if ($tienda) {
                                $diasPlan = match($pago->plan) {
                                    'mensual' => 30,
                                    'trimestral' => 90, // ← NUEVO
                                    'anual' => 365,
                                    default => 30
                                };
                                
                                if ($tienda->dias_restantes <= 0) {
                                    $nuevaFecha = Carbon::now()->addDays($diasPlan);
                                } else {
                                    $nuevaFecha = Carbon::parse($tienda->fecha_expiracion)->addDays($diasPlan);
                                }
                                
                                $tienda->fecha_expiracion = $nuevaFecha;
                                $tienda->estado = 'activa';
                                $tienda->save();
                                
                                Log::info('Suscripción renovada por webhook:', [
                                    'tienda_id' => $tienda->id,
                                    'nueva_fecha' => $nuevaFecha
                                ]);
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error en webhook:', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        
        return response()->json(['status' => 'ok']);
    }
}