<?php
// app/Models/Tienda.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Tienda extends Model
{
    protected $table = 'tiendas';
    
    protected $fillable = [
        'super_admin_id',
        'nombre_tienda',
        'iniciales',
        'email',
        'descripcion',
        'telefono_contacto',
        'horario_atencion',
        'telefono',
        'direccion',
        'sitio_web',
        'facebook',
        'instagram',
        'whatsapp',
        'banco',
        'clabe',
        'cuenta',
        'titular',
        'rfc',
        'ticket_mensaje',
        'ticket_pie',
        'ticket_mostrar_logo',
        'logo_url',
        'fecha_registro',
        'fecha_renovacion',
        'estado',
        'plan_tipo',
        'fecha_inicio',
        'fecha_expiracion',
        'mp_preference_id',
        'mp_payment_id',
        'mp_status'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_expiracion' => 'date',
        'fecha_registro' => 'datetime',
        'fecha_renovacion' => 'date',
        'ticket_mostrar_logo' => 'boolean'
    ];

    /**
     * RELACIONES
     */
    public function superAdmin()
    {
        return $this->belongsTo(SuperAdmin::class);
    }

    public function usuarios()
    {
        return $this->hasMany(UsuarioTienda::class, 'tienda_id');
    }

    public function apartados()
    {
        return $this->hasMany(Apartado::class, 'tienda_id');
    }

    /**
     * ACCESSORS - Atributos calculados
     */
    public function getTotalApartadosAttribute()
    {
        return $this->apartados()->count();
    }

    public function getTotalUsuariosAttribute()
    {
        return $this->usuarios()->count();
    }

    public function getTotalIngresosAttribute()
    {
        return $this->apartados()
            ->join('pagos_apartado', 'apartados.id', '=', 'pagos_apartado.apartado_id')
            ->sum('pagos_apartado.monto') ?? 0;
    }

    public function getDiasRestantesAttribute()
    {
        if (!$this->fecha_expiracion) return 0;
        
        $dias = Carbon::now()->diffInDays($this->fecha_expiracion, false);
        return ceil($dias); // Redondea hacia arriba
    }

    public function getEstadoColorAttribute()
    {
        return match($this->estado) {
            'activa' => 'green',
            'suspendida' => 'yellow',
            'cancelada' => 'red',
            'pendiente_pago' => 'orange',
            default => 'gray'
        };
    }

    public function getEstadoBadgeAttribute()
    {
        return match($this->estado) {
            'activa' => '<span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium"><i class="fas fa-circle text-xs mr-1 text-green-500"></i>Activa</span>',
            'suspendida' => '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium"><i class="fas fa-circle text-xs mr-1 text-yellow-500"></i>Suspendida</span>',
            'cancelada' => '<span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium"><i class="fas fa-circle text-xs mr-1 text-red-500"></i>Cancelada</span>',
            'pendiente_pago' => '<span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-medium"><i class="fas fa-circle text-xs mr-1 text-orange-500"></i>Pendiente</span>',
            default => '<span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">Desconocido</span>'
        };
    }

    public function getLogoUrlAttribute($value)
    {
        return $value ? asset('storage/' . $value) : null;
    }

    public function getWhatsappLinkAttribute()
    {
        if (!$this->whatsapp) return null;
        
        $telefono = preg_replace('/[^0-9]/', '', $this->whatsapp);
        if (strlen($telefono) == 10) {
            $telefono = '52' . $telefono;
        }
        return 'https://wa.me/' . $telefono;
    }

    /**
     * SCOPES - Consultas reutilizables
     */
    public function scopeActivas($query)
    {
        return $query->where('estado', 'activa');
    }

    public function scopeSuspendidas($query)
    {
        return $query->where('estado', 'suspendida');
    }

    public function scopeCanceladas($query)
    {
        return $query->where('estado', 'cancelada');
    }

    public function scopePendientesPago($query)
    {
        return $query->where('estado', 'pendiente_pago');
    }

    public function scopePorVencer($query, $dias = 7)
    {
        return $query->activas()
                     ->where('fecha_expiracion', '<=', Carbon::now()->addDays($dias))
                     ->where('fecha_expiracion', '>', Carbon::now());
    }

    public function scopeVencidas($query)
    {
        return $query->where('fecha_expiracion', '<', Carbon::now())
                     ->where('estado', 'activa');
    }

    public function scopeConPlan($query, $plan)
    {
        return $query->where('plan_tipo', $plan);
    }

    public function scopeBuscar($query, $termino)
    {
        return $query->where(function($q) use ($termino) {
            $q->where('nombre_tienda', 'LIKE', "%{$termino}%")
              ->orWhere('email', 'LIKE', "%{$termino}%")
              ->orWhere('iniciales', 'LIKE', "%{$termino}%")
              ->orWhere('telefono', 'LIKE', "%{$termino}%")
              ->orWhere('rfc', 'LIKE', "%{$termino}%");
        });
    }

    /**
     * MÉTODOS DE SUSCRIPCIÓN
     */
    public function suscripcionActiva()
    {
        return $this->estado === 'activa' && 
               $this->fecha_expiracion && 
               Carbon::now()->lessThanOrEqualTo($this->fecha_expiracion);
    }

    public function suscripcionExpirada()
    {
        return $this->fecha_expiracion && Carbon::now()->greaterThan($this->fecha_expiracion);
    }

    public function activarSuscripcion($plan)
    {
        $dias = $this->getDiasPorPlan($plan);
        
        $this->plan_tipo = $plan;
        $this->fecha_inicio = Carbon::now();
        $this->fecha_expiracion = Carbon::now()->addDays($dias);
        $this->estado = 'activa';
        $this->save();
        
        return $this;
    }

    public function renovarSuscripcion()
    {
        $dias = $this->getDiasPorPlan($this->plan_tipo);
        
        if ($this->suscripcionExpirada()) {
            // Si ya expiró, la nueva suscripción empieza desde hoy
            $this->fecha_inicio = Carbon::now();
            $this->fecha_expiracion = Carbon::now()->addDays($dias);
        } else {
            // Si aún está activa, se extiende desde la fecha de expiración actual
            $this->fecha_expiracion = Carbon::parse($this->fecha_expiracion)->addDays($dias);
        }
        
        $this->estado = 'activa';
        $this->save();
        
        return $this;
    }

    public function suspender()
    {
        $this->estado = 'suspendida';
        $this->save();
        
        return $this;
    }

    public function cancelar()
    {
        $this->estado = 'cancelada';
        $this->save();
        
        return $this;
    }

    /**
     * MÉTODO PRINCIPAL PARA OBTENER DÍAS POR PLAN
     * Este método es el corazón de la lógica de suscripciones
     */
    private function getDiasPorPlan($plan)
    {
        return match($plan) {
            'mensual' => 30,
            'trimestral' => 90,  // ← YA EXISTE (bien)
            'anual' => 365,
            default => 30
        };
    }

    /**
     * MÉTODO DE UTILIDAD - Validar si un plan es válido
     */
    public static function planValido($plan)
    {
        return in_array($plan, ['mensual', 'trimestral', 'anual']);
    }

    /**
     * MÉTODO DE UTILIDAD - Obtener todos los planes disponibles
     */
    public static function getPlanesDisponibles()
    {
        return [
            'mensual' => [
                'nombre' => 'Plan Mensual',
                'dias' => 30,
                'precio' => 199,
                'descripcion' => 'Facturación mensual'
            ],
            'trimestral' => [
                'nombre' => 'Plan Trimestral',
                'dias' => 90,
                'precio' => 497,
                'descripcion' => 'Facturación trimestral (ahorra $100)'
            ],
            'anual' => [
                'nombre' => 'Plan Anual',
                'dias' => 365,
                'precio' => 1790,
                'descripcion' => 'Facturación anual (ahorra $598)'
            ]
        ];
    }

    /**
     * MÉTODOS DE ESTADÍSTICAS
     */
    public function getEstadisticas()
    {
        return [
            'total_apartados' => $this->apartados()->count(),
            'activos' => $this->apartados()->where('estado', 'activo')->count(),
            'pagados' => $this->apartados()->where('estado', 'pagado')->count(),
            'vencidos' => $this->apartados()->where('estado', 'vencido')->count(),
            'ingresos' => $this->apartados()
                ->join('pagos_apartado', 'apartados.id', '=', 'pagos_apartado.apartado_id')
                ->sum('pagos_apartado.monto') ?? 0,
            'ultimos_7_dias' => $this->apartados()
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->count()
        ];
    }

    /**
     * BOOT - Eventos del modelo
     */
    protected static function boot()
    {
        parent::boot();
        
        // Al crear, establecer fecha_registro
        static::creating(function ($tienda) {
            if (empty($tienda->fecha_registro)) {
                $tienda->fecha_registro = Carbon::now();
            }
        });
        
        // Al actualizar, limpiar caché relacionado
        static::saved(function ($tienda) {
            \Illuminate\Support\Facades\Cache::forget('tienda_' . $tienda->id);
            \Illuminate\Support\Facades\Cache::forget('tienda_iniciales_' . $tienda->id);
        });
        
        // Evento para verificar expiración antes de guardar
        static::saving(function ($tienda) {
            // Si la fecha de expiración ya pasó y está activa, suspender automáticamente
            if ($tienda->estado === 'activa' && $tienda->fecha_expiracion && Carbon::now()->greaterThan($tienda->fecha_expiracion)) {
                $tienda->estado = 'suspendida';
            }
        });
    }
}