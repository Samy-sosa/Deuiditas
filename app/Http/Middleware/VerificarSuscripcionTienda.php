<?php
// app/Http/Middleware/VerificarSuscripcionTienda.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VerificarSuscripcionTienda
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $usuario = auth()->user();
        
        if (!isset($usuario->tienda)) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'No se encontró la tienda asociada a tu cuenta');
        }

        $tienda = $usuario->tienda;
        $diasRestantes = $tienda->dias_restantes;

        // ============================================
        // 🚨 CASO 1: Suscripción VENCIDA (0 días o menos)
        // ============================================
        if ($diasRestantes <= 0) {
            // Guardar datos necesarios para renovación
            session(['tienda_a_renovar' => $tienda->id]);
            
            // Redirigir directamente a la página de renovación
            return redirect()->route('tienda.renovar');
        }

        // ============================================
        // ⚠️ CASO 2: Suscripción POR VENCER (1-7 días)
        // ============================================
        if ($diasRestantes <= 7) {
            session([
                'dias_restantes' => $diasRestantes,
                'mostrar_boton_renovar' => true
            ]);
            
            session()->flash('alerta_suscripcion', [
                'tipo' => 'warning',
                'mensaje' => "⏰ Tu suscripción vence en {$diasRestantes} días.",
                'boton' => true,
                'boton_texto' => 'Renovar ahora',
                'boton_ruta' => route('tienda.renovar')
            ]);
        } 
        // ============================================
        // ✅ CASO 3: Suscripción ACTIVA (>7 días)
        // ============================================
        else {
            session(['dias_restantes' => $diasRestantes]);
        }

        return $next($request);
    }
}