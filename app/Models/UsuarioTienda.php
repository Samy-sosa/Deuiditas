<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class UsuarioTienda extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios_tienda';

    protected $fillable = [
        'tienda_id',
        'nombre',
        'email',
        'password',
        'rol',
        'activo'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relación con Tienda
    public function tienda()
    {
        return $this->belongsTo(Tienda::class, 'tienda_id');
    }

    public function esAdmin()
    {
        return $this->rol === 'admin_tienda';
    }

    public function esVendedor()
    {
        return $this->rol === 'vendedor';
    }
}