<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuponUsado extends Model
{
    protected $table = 'cupones_usados';

    protected $fillable = [
        'cupon_id',
        'tienda_id',
        'fecha_uso',
        'monto_original',
        'descuento_aplicado',
        'monto_final'
    ];

    protected $casts = [
        'fecha_uso' => 'datetime',
        'monto_original' => 'decimal:2',
        'descuento_aplicado' => 'decimal:2',
        'monto_final' => 'decimal:2'
    ];

    /**
     * RELACIONES (funcionan gracias a las foreign keys)
     */
    public function cupon()
    {
        return $this->belongsTo(Cupon::class, 'cupon_id');
    }

    public function tienda()
    {
        return $this->belongsTo(Tienda::class, 'tienda_id');
    }
}