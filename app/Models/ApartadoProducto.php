<?php
// app/Models/ApartadoProducto.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ApartadoProducto extends Model
{
    protected $table = 'apartado_productos';

    protected $fillable = [
        'apartado_id',
        'nombre_producto',
        'descripcion',
        'precio_unitario',
        'cantidad',
        'subtotal',
        'monto_pagado',  // ← NUEVO
        'estado'
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'monto_pagado' => 'decimal:2',  // ← NUEVO
        'cantidad' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * RELACIONES
     */
    public function apartado()
    {
        return $this->belongsTo(Apartado::class);
    }
    
    public function pagos()
    {
        return $this->hasMany(PagoApartado::class, 'producto_id');
    }

    /**
     * ACCESSORS
     */
    public function getPrecioFormateadoAttribute()
    {
        return '$' . number_format($this->precio_unitario, 2);
    }

    public function getSubtotalFormateadoAttribute()
    {
        return '$' . number_format($this->subtotal, 2);
    }
    
    // ============================================
    // NUEVOS ACCESSORS
    // ============================================
    
    /**
     * Saldo pendiente de este producto
     */
    public function getSaldoPendienteAttribute()
    {
        return max(0, $this->subtotal - ($this->monto_pagado ?? 0));
    }
    
    /**
     * Porcentaje pagado
     */
    public function getPorcentajePagadoAttribute()
    {
        if ($this->subtotal <= 0) return 0;
        return round(($this->monto_pagado / $this->subtotal) * 100, 2);
    }
    
    /**
     * ¿Está completamente pagado?
     */
    public function getEstaPagadoAttribute()
    {
        return $this->saldo_pendiente <= 0;
    }

    /**
     * SCOPES
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopePagados($query)
    {
        return $query->where('estado', 'pagado');
    }

    /**
     * MÉTODOS DE NEGOCIO
     */
    public function calcularSubtotal()
    {
        return $this->precio_unitario * $this->cantidad;
    }
    
    // ============================================
    // NUEVOS MÉTODOS PARA GESTIÓN DE PAGOS
    // ============================================
    
    /**
     * Registrar un pago a este producto específico
     */
    public function registrarPago($monto, $metodoPago, $referencia = null)
    {
        return DB::transaction(function () use ($monto, $metodoPago, $referencia) {
            // Validar que no exceda el saldo
            if ($monto > $this->saldo_pendiente) {
                throw new \Exception("El monto excede el saldo pendiente del producto: $" . number_format($this->saldo_pendiente, 2));
            }
            
            // Actualizar monto pagado del producto
            $this->monto_pagado += $monto;
            
            // Si quedó saldo 0, marcar como pagado
            if ($this->saldo_pendiente <= 0) {
                $this->estado = 'pagado';
            }
            
            $this->save();
            
            // Crear registro de pago
            $pago = PagoApartado::create([
                'apartado_id' => $this->apartado_id,
                'producto_id' => $this->id,
                'monto' => $monto,
                'metodo_pago' => $metodoPago,
                'referencia' => $referencia ?? "Abono a {$this->nombre_producto}",
                'fecha_pago' => now()
            ]);
            
            return $pago;
        });
    }
    
    /**
     * Agregar este producto a un apartado existente
     */
    public static function agregarA(Apartado $apartado, $nombre, $precio, $cantidad, $descripcion = null)
    {
        $subtotal = $precio * $cantidad;
        
        return DB::transaction(function () use ($apartado, $nombre, $precio, $cantidad, $descripcion, $subtotal) {
            $producto = self::create([
                'apartado_id' => $apartado->id,
                'nombre_producto' => $nombre,
                'descripcion' => $descripcion,
                'precio_unitario' => $precio,
                'cantidad' => $cantidad,
                'subtotal' => $subtotal,
                'monto_pagado' => 0,
                'estado' => 'pendiente'
            ]);
            
            // Actualizar totales del apartado
            $apartado->total += $subtotal;
            $apartado->saldo_pendiente += $subtotal;
            $apartado->save();
            
            return $producto;
        });
    }
}