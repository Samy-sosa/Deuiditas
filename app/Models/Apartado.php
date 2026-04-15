<?php
// app/Models/Apartado.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Apartado extends Model
{
    protected $table = 'apartados';
    
    protected $fillable = [
        'tienda_id',
        'usuario_id',
        'cliente_id',           // ← NUEVO: relación con clientes
        'codigo_unico',
        'nombre_cliente',
        'telefono_cliente',
        'email_cliente',
        'total',
        'apartado_inicial',
        'saldo_pendiente',
        'fecha_limite',
        'estado',
        'notas'
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'apartado_inicial' => 'decimal:2',
        'saldo_pendiente' => 'decimal:2',
        'fecha_limite' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * RELACIONES
     */
    public function tienda()
    {
        return $this->belongsTo(Tienda::class, 'tienda_id');
    }

    public function usuario()
    {
        return $this->belongsTo(UsuarioTienda::class, 'usuario_id');
    }

    // ← NUEVA RELACIÓN CON CLIENTE
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function pagos()
    {
        return $this->hasMany(PagoApartado::class, 'apartado_id');
    }

    public function productos()
    {
        return $this->hasMany(ApartadoProducto::class, 'apartado_id');
    }

    /**
     * ACCESSORS (Atributos calculados)
     */
    public function getPorcentajePagadoAttribute()
    {
        if ($this->total <= 0) return 0;
        
        $pagado = $this->total - $this->saldo_pendiente;
        return round(($pagado / $this->total) * 100, 1);
    }

    public function getTotalPagadoAttribute()
    {
        return $this->total - $this->saldo_pendiente;
    }

    public function getEstaPagadoAttribute()
    {
        return $this->saldo_pendiente <= 0;
    }

    public function getEstaVencidoAttribute()
    {
        return now() > $this->fecha_limite && $this->estado === 'activo';
    }

    public function getDiasRestantesAttribute()
    {
        if ($this->fecha_limite) {
            return now()->diffInDays($this->fecha_limite, false);
        }
        return 0;
    }

    public function getEstadoColorAttribute()
    {
        return match($this->estado) {
            'activo' => 'yellow',
            'pagado' => 'green',
            'vencido' => 'red',
            'cancelado' => 'gray',
            default => 'gray'
        };
    }

    /**
     * SCOPES (Consultas reutilizables)
     */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopePagados($query)
    {
        return $query->where('estado', 'pagado');
    }

    public function scopeVencidos($query)
    {
        return $query->where('estado', 'vencido');
    }

    public function scopePorVencer($query, $dias = 7)
    {
        return $query->activos()
                     ->where('fecha_limite', '<=', now()->addDays($dias))
                     ->where('fecha_limite', '>', now());
    }

    public function scopeConSaldo($query)
    {
        return $query->where('saldo_pendiente', '>', 0);
    }

    public function scopeDeTienda($query, $tiendaId)
    {
        return $query->where('tienda_id', $tiendaId);
    }

    public function scopeBuscar($query, $termino)
    {
        return $query->where(function($q) use ($termino) {
            $q->where('codigo_unico', 'LIKE', "%{$termino}%")
              ->orWhere('nombre_cliente', 'LIKE', "%{$termino}%")
              ->orWhere('telefono_cliente', 'LIKE', "%{$termino}%");
        });
    }

    // ← NUEVOS SCOPES PARA CLIENTES
    public function scopeDeCliente($query, $clienteId)
    {
        return $query->where('cliente_id', $clienteId);
    }

    public function scopeActivosDeCliente($query, $clienteId)
    {
        return $query->where('cliente_id', $clienteId)->where('estado', 'activo');
    }

    /**
     * ============================================
     * MÉTODOS PARA GESTIÓN DE PAGOS
     * ============================================
     */
    
    /**
     * Registrar un pago GENERAL (se distribuye automáticamente entre productos)
     */
    public function registrarPagoGeneral($monto, $metodoPago, $referencia = null)
    {
        return DB::transaction(function () use ($monto, $metodoPago, $referencia) {
            // Obtener productos con saldo pendiente
            $productosPendientes = $this->productos()
                ->where('estado', '!=', 'pagado')
                ->get()
                ->filter(function ($producto) {
                    return $producto->saldo_pendiente > 0;
                });
            
            if ($productosPendientes->isEmpty()) {
                throw new \Exception("No hay productos pendientes en este apartado");
            }
            
            $montoRestante = $monto;
            $pagosRegistrados = [];
            $productosRecibieronPago = [];
            
            // Distribuir el pago entre productos pendientes
            foreach ($productosPendientes as $producto) {
                if ($montoRestante <= 0) break;
                
                $saldoProducto = $producto->saldo_pendiente;
                $montoAplicar = min($montoRestante, $saldoProducto);
                
                // Registrar pago al producto
                $pago = $producto->registrarPago($montoAplicar, $metodoPago, $referencia);
                $pagosRegistrados[] = $pago;
                
                $productosRecibieronPago[] = [
                    'nombre' => $producto->nombre_producto,
                    'monto' => $montoAplicar,
                    'saldo_restante' => $producto->saldo_pendiente,
                    'completado' => $producto->saldo_pendiente <= 0
                ];
                
                $montoRestante -= $montoAplicar;
            }
            
            // Actualizar el saldo total del apartado
            $this->actualizarSaldoTotal();
            
            // Actualizar estado del apartado
            if ($this->saldo_pendiente == 0) {
                $this->estado = 'pagado';
                $this->save();
            }
            
            return [
                'pagos' => $pagosRegistrados,
                'productos' => $productosRecibieronPago,
                'monto_aplicado' => $monto - $montoRestante,
                'monto_excedente' => $montoRestante
            ];
        });
    }
    
    /**
     * Actualizar el saldo total del apartado sumando saldos de productos
     */
    public function actualizarSaldoTotal()
    {
        $saldoTotal = $this->productos()->sum(DB::raw('subtotal - monto_pagado'));
        $totalPagado = $this->productos()->sum('monto_pagado');
        
        $this->saldo_pendiente = max(0, $saldoTotal);
        $this->save();
        
        return $this;
    }
    
    /**
     * Obtener el resumen de pagos por producto
     */
    public function getResumenPagosPorProducto()
    {
        return $this->productos->map(function ($producto) {
            return [
                'producto' => $producto->nombre_producto,
                'total' => $producto->subtotal,
                'pagado' => $producto->monto_pagado,
                'saldo' => $producto->saldo_pendiente,
                'porcentaje' => $producto->porcentaje_pagado,
                'esta_pagado' => $producto->esta_pagado
            ];
        });
    }

    /**
     * MÉTODOS EXISTENTES
     */
    public function generarCodigoUnico($iniciales)
    {
        do {
            $codigo = $iniciales . rand(10000, 999999);
        } while (self::where('codigo_unico', $codigo)->exists());
        
        return $codigo;
    }

    /**
     * Boot del modelo
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($apartado) {
            if (empty($apartado->uuid)) {
                $apartado->uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }
}