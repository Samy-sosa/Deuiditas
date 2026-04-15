<?php
// app/Models/PagoApartado.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoApartado extends Model
{
    protected $table = 'pagos_apartado';
    
    public $timestamps = true;
    
    protected $fillable = [
        'apartado_id',
        'producto_id',
        'monto',
        'metodo_pago',
        'referencia',
        'fecha_pago'
    ];

    protected $casts = [
        'fecha_pago' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'monto' => 'decimal:2'
    ];

    // Relaciones
    public function apartado()
    {
        return $this->belongsTo(Apartado::class, 'apartado_id');
    }
    
    public function producto()
    {
        return $this->belongsTo(ApartadoProducto::class, 'producto_id');
    }
    
    // ============================================
    // MÉTODOS HELPER
    // ============================================
    
    public function esPagoGeneral()
    {
        return is_null($this->producto_id);
    }
    
    public function esPagoEspecifico()
    {
        return !is_null($this->producto_id);
    }
    
    public function getProductoNombreAttribute()
    {
        if ($this->producto) {
            return $this->producto->nombre_producto;
        }
        return 'Abono general';
    }
    
    /**
     * Generar mensaje para WhatsApp según el tipo de pago
     */
    public function generarMensajeWhatsApp()
    {
        $enlaceConsulta = 'https://deuditas.com.mx/apartado/' . $this->apartado->codigo_unico;
        
        $mensaje = "*PAGO REGISTRADO*\n";
        $mensaje .= "══════════════════\n\n";
        $mensaje .= "Apartado: {$this->apartado->codigo_unico}\n";
        $mensaje .= "Cliente: {$this->apartado->nombre_cliente}\n";
        $mensaje .= "Monto abonado: $" . number_format($this->monto, 2) . "\n";
        
        if ($this->producto) {
            $mensaje .= "Producto: {$this->producto->nombre_producto}\n";
            $mensaje .= "Saldo restante del producto: $" . number_format($this->producto->saldo_pendiente, 2) . "\n";
        }
        
        $mensaje .= "Método de pago: " . ucfirst($this->metodo_pago) . "\n";
        
        if ($this->apartado->saldo_pendiente == 0) {
            $mensaje .= "Estado: COMPLETAMENTE PAGADO\n\n";
        } else {
            $mensaje .= "Saldo total pendiente: $" . number_format($this->apartado->saldo_pendiente, 2) . "\n\n";
        }
        
        $mensaje .= "Consulta tu apartado en línea:\n";
        $mensaje .= "{$enlaceConsulta}\n\n";
        $mensaje .= "Fecha: " . now()->format('d/m/Y H:i') . "\n";
        $mensaje .= "Gracias por tu pago.";
        
        return $mensaje;
    }
}