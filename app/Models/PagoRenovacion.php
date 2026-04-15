<?php
// app/Models/PagoRenovacion.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoRenovacion extends Model
{
    protected $table = 'pagos_renovacion';

    protected $fillable = [
        'tienda_id',
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

    /**
     * RELACIONES
     */
    public function tienda()
    {
        return $this->belongsTo(Tienda::class, 'tienda_id');
    }

    /**
     * SCOPES
     */
    public function scopeAprobados($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePendientes($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRechazados($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeDeTienda($query, $tiendaId)
    {
        return $query->where('tienda_id', $tiendaId);
    }

    /**
     * ACCESSORS
     */
    public function getMontoFormateadoAttribute()
    {
        return '$' . number_format($this->monto, 2);
    }

    public function getPlanNombreAttribute()
    {
        return match($this->plan) {
            'mensual' => 'Plan Mensual',
            'trimestral' => 'Plan Trimestral',
            'anual' => 'Plan Anual',
            default => $this->plan
        };
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

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'approved' => '<span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium"><i class="fas fa-circle text-xs mr-1 text-green-500"></i>Aprobado</span>',
            'pending' => '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium"><i class="fas fa-circle text-xs mr-1 text-yellow-500"></i>Pendiente</span>',
            'rejected' => '<span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium"><i class="fas fa-circle text-xs mr-1 text-red-500"></i>Rechazado</span>',
            default => '<span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">Desconocido</span>'
        };
    }
}