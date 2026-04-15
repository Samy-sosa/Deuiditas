<?php
// app/Http/Controllers/Tienda/RenovacionController.php

namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tienda;
use App\Models\Pago;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class RenovacionController extends Controller
{
    protected $accessToken;
    
    // Precios actualizados según la estrategia de ahorro
    const PRECIOS = [
        'mensual' => 199,
        'trimestral' => 497,
        'anual' => 1790
    ];
    
    const PRECIOS_REGULARES = [
        'mensual' => 199,
        'trimestral' => 597, // 199 * 3
        'anual' => 2388 // 199 * 12
    ];
    
    const DIAS_POR_PLAN = [
        'mensual' => 30,
        'trimestral' => 90,
        'anual' => 365
    ];
    
    const NOMBRES_PLAN = [
        'mensual' => 'Plan Mensual',
        'trimestral' => 'Plan Trimestral',
        'anual' => 'Plan Anual'
    ];
    
    const DESCUENTOS = [
        'mensual' => 0,
        'trimestral' => 100, // 597 - 497
        'anual' => 598 // 2388 - 1790
    ];
    
    const PORCENTAJES_AHORRO = [
        'mensual' => 0,
        'trimestral' => 16, // (100/597)*100
        'anual' => 25 // (598/2388)*100
    ];
    
    public function __construct()
    {
        $this->accessToken = env('MERCADO_PAGO_ACCESS_TOKEN');
        
        if (!$this->accessToken) {
            throw new \Exception('Error: Access Token de Mercado Pago no configurado');
        }
        
        MercadoPagoConfig::setAccessToken($this->accessToken);
    }

    public function index()
    {
        $tiendaId = session('tienda_a_renovar') ?? auth()->user()->tienda->id;
        $tienda = Tienda::findOrFail($tiendaId);
        
        $diasRestantes = $tienda->dias_restantes;
        
        // Planes con precios actualizados y características detalladas
        $planes = [
            (object)[
                'id' => 'mensual',
                'nombre' => self::NOMBRES_PLAN['mensual'],
                'dias' => self::DIAS_POR_PLAN['mensual'],
                'precio' => self::PRECIOS['mensual'],
                'precio_regular' => self::PRECIOS_REGULARES['mensual'],
                'popular' => false,
                'descuento' => null,
                'caracteristicas' => [
                    '30 días de servicio',
                    'Apartados ilimitados',
                    'Portal para clientes',
                    'Soporte por email'
                ]
            ],
            (object)[
                'id' => 'trimestral',
                'nombre' => self::NOMBRES_PLAN['trimestral'],
                'dias' => self::DIAS_POR_PLAN['trimestral'],
                'precio' => self::PRECIOS['trimestral'],
                'precio_regular' => self::PRECIOS_REGULARES['trimestral'],
                'popular' => true,
                'descuento' => self::DESCUENTOS['trimestral'],
                'porcentaje_ahorro' => self::PORCENTAJES_AHORRO['trimestral'],
                'caracteristicas' => [
                    '90 días de servicio',
                    'Todo lo del Plan Mensual',
                    'Soporte prioritario por WhatsApp',
                    'Reportes básicos de ventas',
                    'Respaldo semanal'
                ]
            ],
            (object)[
                'id' => 'anual',
                'nombre' => self::NOMBRES_PLAN['anual'],
                'dias' => self::DIAS_POR_PLAN['anual'],
                'precio' => self::PRECIOS['anual'],
                'precio_regular' => self::PRECIOS_REGULARES['anual'],
                'popular' => false,
                'descuento' => self::DESCUENTOS['anual'],
                'porcentaje_ahorro' => self::PORCENTAJES_AHORRO['anual'],
                'caracteristicas' => [
                    '365 días de servicio',
                    'Todo lo del Plan Trimestral',
                    '2 meses gratis vs. mensual',
                    'Reportes avanzados + gráficas',
                    'Exportación a Excel',
                    'Backups diarios automáticos'
                ]
            ]
        ];
        
        return view('tienda.renovar.index', compact('tienda', 'diasRestantes', 'planes'));
    }

    public function procesar(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:mensual,trimestral,anual'
        ]);

        $tiendaId = session('tienda_a_renovar') ?? auth()->user()->tienda->id;
        $tienda = Tienda::findOrFail($tiendaId);
        
        $precio = self::PRECIOS[$request->plan];
        $nombrePlan = self::NOMBRES_PLAN[$request->plan];
        $diasPlan = self::DIAS_POR_PLAN[$request->plan];
        $descuento = self::DESCUENTOS[$request->plan];

        try {
            if ($tienda->dias_restantes <= 0) {
                $nuevaFecha = Carbon::now()->addDays($diasPlan);
            } else {
                $nuevaFecha = Carbon::parse($tienda->fecha_expiracion)->addDays($diasPlan);
            }
            
            $externalReference = 'RENOV-' . $tienda->id . '-' . time() . '-' . uniqid();

            $pago = Pago::create([
                'tienda_id' => $tienda->id,
                'tipo' => 'renovacion',
                'monto' => $precio,
                'plan' => $request->plan,
                'metodo_pago' => 'mercadopago',
                'external_reference' => $externalReference,
                'status' => 'pending',
                'raw_data' => json_encode([
                    'plan_nombre' => $nombrePlan,
                    'dias' => $diasPlan,
                    'precio_regular' => self::PRECIOS_REGULARES[$request->plan],
                    'descuento' => $descuento,
                    'porcentaje_ahorro' => self::PORCENTAJES_AHORRO[$request->plan],
                    'nueva_fecha' => $nuevaFecha->toDateString(),
                    'dias_restantes_antes' => $tienda->dias_restantes
                ])
            ]);

            $client = new PreferenceClient();
            
            // Descripción detallada del plan para Mercado Pago
            $descripcion = "Renovación {$nombrePlan} por {$diasPlan} días";
            if ($descuento > 0) {
                $descripcion .= " (Ahorras $" . number_format($descuento, 0) . ")";
            }
            
            $preferenceData = [
                "items" => [
                    [
                        "title" => "Renovación " . $nombrePlan . " - " . $tienda->nombre_tienda,
                        "quantity" => 1,
                        "unit_price" => (float) $precio,
                        "currency_id" => "MXN",
                        "description" => $descripcion
                    ]
                ],
                "payer" => [
                    "email" => auth()->user()->email,
                    "name" => auth()->user()->nombre
                ],
                "back_urls" => [
                    "success" => route('tienda.renovar.exitoso'),
                    "failure" => route('tienda.renovar.fallido'),
                    "pending" => route('tienda.renovar.pendiente')
                ],
                "auto_return" => "approved",
                "external_reference" => $externalReference,
                "notification_url" => route('webhook.mercadopago'),
                "statement_descriptor" => "DEUDITAS RENOVACION"
            ];
            
            $preference = $client->create($preferenceData);
            
            // Actualizar raw_data con la información de la preferencia
            $rawData = json_decode($pago->raw_data, true);
            $rawData['preference_id'] = $preference->id;
            $rawData['init_point'] = $preference->init_point;
            $pago->raw_data = json_encode($rawData);
            $pago->save();
            
            session([
                'renovacion_tienda_id' => $tienda->id,
                'renovacion_plan' => $request->plan,
                'renovacion_dias' => $diasPlan,
                'renovacion_nueva_fecha' => $nuevaFecha->toDateString(),
                'renovacion_monto' => $precio,
                'renovacion_external_ref' => $externalReference,
                'renovacion_preference_id' => $preference->id,
                'renovacion_pago_id' => $pago->id
            ]);
            
            return redirect($preference->init_point);
            
        } catch (MPApiException $e) {
            if (isset($pago)) {
                $pago->status = 'rejected';
                $pago->raw_data = json_encode([
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $pago->save();
            }
            
            Log::error('Error Mercado Pago en renovación:', [
                'message' => $e->getMessage(),
                'tienda_id' => $tienda->id ?? null
            ]);
            
            return back()->with('error', 'Error al procesar el pago. Intenta nuevamente.');
        } catch (\Exception $e) {
            Log::error('Error general en renovación:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Error inesperado: ' . $e->getMessage());
        }
    }

    public function exitoso(Request $request)
    {
        Log::info('Renovación exitosa - callback recibido:', $request->all());
        
        $paymentId = $request->get('payment_id');
        $status = $request->get('status');
        $externalRef = $request->get('external_reference');
        
        if ($status == 'approved') {
            $pago = Pago::where('external_reference', $externalRef)->first();
            
            if ($pago && $pago->status != 'approved') {
                $pago->payment_id = $paymentId;
                $pago->status = 'approved';
                $pago->save();
                
                $this->procesarRenovacionExitosa($pago);
                
                Log::info('Renovación procesada exitosamente:', [
                    'pago_id' => $pago->id,
                    'tienda_id' => $pago->tienda_id
                ]);
            }
            
            session()->forget([
                'tienda_a_renovar',
                'renovacion_tienda_id',
                'renovacion_plan',
                'renovacion_dias',
                'renovacion_nueva_fecha',
                'renovacion_monto',
                'renovacion_external_ref',
                'renovacion_preference_id',
                'renovacion_pago_id'
            ]);
            
            return redirect()->route('tienda.dashboard')
                ->with('success', '🎉 ¡Suscripción renovada exitosamente! Ahora disfrutas de ' . self::NOMBRES_PLAN[$pago->plan] . ' hasta el ' . Carbon::parse($pago->tienda->fecha_expiracion)->format('d/m/Y'));
        }
        
        return redirect()->route('tienda.renovar')
            ->with('error', 'El pago no pudo ser procesado.');
    }

    public function fallido(Request $request)
    {
        Log::info('Renovación fallida:', $request->all());
        
        $externalRef = $request->get('external_reference');
        
        if ($externalRef) {
            Pago::where('external_reference', $externalRef)
                ->update(['status' => 'rejected']);
        }
        
        return redirect()->route('tienda.renovar')
            ->with('error', 'El pago fue rechazado. Intenta con otro método de pago.');
    }

    public function pendiente(Request $request)
    {
        Log::info('Renovación pendiente:', $request->all());
        
        $externalRef = $request->get('external_reference');
        
        if ($externalRef) {
            Pago::where('external_reference', $externalRef)
                ->update(['status' => 'pending']);
        }
        
        return redirect()->route('tienda.renovar')
            ->with('info', 'Tu pago está siendo procesado. Te notificaremos cuando se confirme.');
    }

    private function procesarRenovacionExitosa($pago)
    {
        DB::beginTransaction();
        
        try {
            $tienda = Tienda::findOrFail($pago->tienda_id);
            
            $diasPlan = self::DIAS_POR_PLAN[$pago->plan] ?? 30;
            
            if ($tienda->dias_restantes <= 0) {
                $nuevaFecha = Carbon::now()->addDays($diasPlan);
            } else {
                $nuevaFecha = Carbon::parse($tienda->fecha_expiracion)->addDays($diasPlan);
            }
            
            $tienda->fecha_expiracion = $nuevaFecha->toDateString();
            $tienda->estado = 'activa';
            $tienda->plan_tipo = $pago->plan;
            $tienda->save();
            
            DB::commit();
            
            Log::info('Renovación procesada en BD:', [
                'tienda_id' => $tienda->id,
                'nueva_expiracion' => $nuevaFecha->toDateString(),
                'plan' => $pago->plan
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error procesando renovación en BD:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
    
    /**
     * Método para obtener información de un plan específico
     */
    public static function getPlanInfo($plan)
    {
        if (!in_array($plan, ['mensual', 'trimestral', 'anual'])) {
            return null;
        }
        
        return [
            'id' => $plan,
            'nombre' => self::NOMBRES_PLAN[$plan],
            'precio' => self::PRECIOS[$plan],
            'precio_regular' => self::PRECIOS_REGULARES[$plan],
            'dias' => self::DIAS_POR_PLAN[$plan],
            'descuento' => self::DESCUENTOS[$plan],
            'porcentaje_ahorro' => self::PORCENTAJES_AHORRO[$plan]
        ];
    }
}