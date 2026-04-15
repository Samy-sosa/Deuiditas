<?php
// app/Models/Pago.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pagos';

    protected $fillable = [
        'tienda_id',
        'tipo',
        'monto',
        'plan',
        'metodo_pago',
        'payment_id',
        'external_reference',
        'status',
        'raw_data'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'raw_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function tienda()
    {
        return $this->belongsTo(Tienda::class, 'tienda_id');
    }

    public function scopeRegistros($query)
    {
        return $query->where('tipo', 'registro');
    }

    public function scopeRenovaciones($query)
    {
        return $query->where('tipo', 'renovacion');
    }

    public function scopeAprobados($query)
    {
        return $query->where('status', 'approved');
    }

    public function getMontoFormateadoAttribute()
    {
        return '$' . number_format($this->monto, 2);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'approved' => 'green',
            'pending' => 'yellow',
            'rejected' => 'red',
            default => 'gray'
        };
    }
}