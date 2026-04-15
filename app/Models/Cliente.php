<?php
// app/Models/Cliente.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';
    
    protected $fillable = [
        'tienda_id',    // ← NUEVO: cada cliente pertenece a una tienda
        'nombre',
        'telefono',
        'email',
        'direccion',
        'notas'
    ];
    
    protected $casts = [
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
    
    public function apartados()
    {
        return $this->hasMany(Apartado::class, 'cliente_id');
    }
    
    public function apartadosActivos()
    {
        return $this->hasMany(Apartado::class, 'cliente_id')->where('estado', 'activo');
    }
    
    public function apartadosPagados()
    {
        return $this->hasMany(Apartado::class, 'cliente_id')->where('estado', 'pagado');
    }
    
    /**
     * ACCESSORS
     */
    public function getTotalGastadoAttribute()
    {
        return $this->apartados()->sum('total');
    }
    
    public function getTotalApartadosAttribute()
    {
        return $this->apartados()->count();
    }
    
    public function getUltimoApartadoAttribute()
    {
        return $this->apartados()->latest()->first();
    }
    
    /**
     * SCOPES
     */
    public function scopeDeTienda($query, $tiendaId)
    {
        return $query->where('tienda_id', $tiendaId);
    }
    
    public function scopeBuscarPorTelefono($query, $telefono, $tiendaId)
    {
        return $query->where('tienda_id', $tiendaId)
                     ->where('telefono', $telefono);
    }
    
    public function scopeBuscarPorNombre($query, $nombre, $tiendaId)
    {
        return $query->where('tienda_id', $tiendaId)
                     ->where('nombre', 'LIKE', "%{$nombre}%");
    }
    
    /**
     * MÉTODOS
     */
    public static function buscarOCrear($telefono, $nombre = null, $email = null, $tiendaId)
    {
        $cliente = self::where('tienda_id', $tiendaId)
                       ->where('telefono', $telefono)
                       ->first();
        
        if (!$cliente) {
            $cliente = self::create([
                'tienda_id' => $tiendaId,
                'nombre' => $nombre ?? 'Cliente sin nombre',
                'telefono' => $telefono,
                'email' => $email
            ]);
        } else {
            // Actualizar datos si es necesario
            if ($nombre && $cliente->nombre != $nombre) {
                $cliente->nombre = $nombre;
            }
            if ($email && $cliente->email != $email) {
                $cliente->email = $email;
            }
            if ($cliente->isDirty()) {
                $cliente->save();
            }
        }
        
        return $cliente;
    }
}