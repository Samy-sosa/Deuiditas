<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Cupon extends Model
{
    protected $table = 'cupones';

    protected $fillable = [
        'codigo',
        'descripcion',
        'tipo_descuento',
        'valor_descuento',
        'monto_minimo',
        'fecha_inicio',
        'fecha_expiracion',
        'usos_maximos',
        'usos_actuales',
        'activo'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_expiracion' => 'date',
        'valor_descuento' => 'decimal:2',
        'monto_minimo' => 'decimal:2',
        'activo' => 'boolean'
    ];

    /**
     * RELACIONES
     */
    public function usos()
    {
        return $this->hasMany(CuponUsado::class, 'cupon_id');
    }

    /**
     * MÉTODOS DE NEGOCIO
     */
    public function esValido($monto = null)
    {
        $hoy = Carbon::today();
        
        if (!$this->activo) return false;
        if ($hoy->lt($this->fecha_inicio) || $hoy->gt($this->fecha_expiracion)) return false;
        if ($this->usos_maximos && $this->usos_actuales >= $this->usos_maximos) return false;
        if ($monto && $this->monto_minimo && $monto < $this->monto_minimo) return false;
        
        return true;
    }

    public function aplicarDescuento($monto)
    {
        if ($this->tipo_descuento === 'porcentaje') {
            $descuento = ($monto * $this->valor_descuento) / 100;
        } else {
            $descuento = min($this->valor_descuento, $monto);
        }
        
        return [
            'descuento' => round($descuento, 2),
            'total' => round($monto - $descuento, 2)
        ];
    }

    public function registrarUso($tiendaId, $montoOriginal, $descuento, $montoFinal)
    {
        $this->increment('usos_actuales');
        
        return $this->usos()->create([
            'tienda_id' => $tiendaId,
            'fecha_uso' => now(),
            'monto_original' => $montoOriginal,
            'descuento_aplicado' => $descuento,
            'monto_final' => $montoFinal
        ]);
    }

    /**
     * SCOPES
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true)
                     ->where('fecha_inicio', '<=', now())
                     ->where('fecha_expiracion', '>=', now());
    }

    public function scopeBuscar($query, $termino)
    {
        return $query->where('codigo', 'LIKE', "%{$termino}%")
                     ->orWhere('descripcion', 'LIKE', "%{$termino}%");
    }
}