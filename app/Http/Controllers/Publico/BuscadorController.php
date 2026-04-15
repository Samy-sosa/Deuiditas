<?php
// app/Http/Controllers/Publico/BuscadorController.php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Apartado;
use App\Models\PagoApartado;
use Illuminate\Support\Facades\Cache;

class BuscadorController extends Controller
{
    /**
     * Mostrar la página de búsqueda pública
     */
    public function index()
    {
        return view('publico.index');
    }

    /**
     * Buscar un apartado por código único
     */
    public function buscar(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:20|min:3'
        ], [
            'codigo.required' => 'Ingresa un código para buscar',
            'codigo.min' => 'El código debe tener al menos 3 caracteres'
        ]);

        // Limpiar el código (eliminar espacios, mayúsculas)
        $codigo = strtoupper(trim($request->codigo));

        // Buscar en caché primero (más rápido)
        $apartado = Cache::remember('publico_apartado_' . $codigo, 300, function () use ($codigo) {
            return Apartado::where('codigo_unico', $codigo)
                ->select('id', 'codigo_unico', 'nombre_cliente', 'estado')
                ->first();
        });
        
        if (!$apartado) {
            return back()
                ->withInput()
                ->withErrors(['codigo' => '❌ El código ingresado no existe']);
        }

        // Verificar si el apartado está visible al público
        if ($apartado->estado === 'cancelado') {
            return back()
                ->withInput()
                ->withErrors(['codigo' => '❌ Este apartado fue cancelado']);
        }

        return redirect()->route('publico.mostrar', $apartado->codigo_unico);
    }

    /**
 * Mostrar detalles del apartado (vista pública)
 */
public function mostrar($codigo)
{
    // Limpiar código
    $codigo = strtoupper(trim($codigo));

    // SIN CACHÉ - Siempre datos frescos
    $apartado = Apartado::where('codigo_unico', $codigo)
        ->with(['pagos' => function($q) {
            $q->orderBy('created_at', 'desc');
        }, 'productos', 'tienda'])
        ->first();
    
    if (!$apartado) {
        return redirect()->route('publico.index')
            ->with('error', '❌ Apartado no encontrado');
    }

    // Verificar si está cancelado
    if ($apartado->estado === 'cancelado') {
        return redirect()->route('publico.index')
            ->with('error', '❌ Este apartado fue cancelado');
    }

    // Calcular estadísticas usando accessors del modelo
    $totalPagado = $apartado->total_pagado;
    $porcentaje = $apartado->porcentaje_pagado;
    $estaPagado = $apartado->esta_pagado;
    $estaVencido = $apartado->esta_vencido;
    
    return view('publico.resultado', compact(
        'apartado', 
        'totalPagado', 
        'porcentaje', 
        'estaPagado',
        'estaVencido'
    ));
}

    /**
     * Buscar por teléfono (opcional - para clientes frecuentes)
     */
    public function buscarPorTelefono(Request $request)
    {
        $request->validate([
            'telefono' => 'required|string|max:20|min:10'
        ]);

        $telefono = preg_replace('/[^0-9]/', '', $request->telefono);
        
        $apartados = Apartado::where('telefono_cliente', 'LIKE', '%' . $telefono . '%')
            ->with('productos')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        if ($apartados->isEmpty()) {
            return back()->with('error', 'No se encontraron apartados con ese teléfono');
        }

        return view('publico.telefono', compact('apartados', 'telefono'));
    }
}