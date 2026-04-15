<?php
// app/Http/Controllers/Tienda/ClienteController.php

namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Apartado;

class ClienteController extends Controller
{
    /**
     * Buscar cliente por teléfono
     */
    public function buscar(Request $request)
    {
        $telefono = $request->get('telefono');
        
        if (!$telefono) {
            return view('tienda.clientes.buscar');
        }
        
        $tiendaId = session('tienda_id');
        
        // Buscar cliente por teléfono EN ESTA TIENDA
        $cliente = Cliente::where('tienda_id', $tiendaId)
                          ->where('telefono', $telefono)
                          ->first();
        
        if (!$cliente) {
            return view('tienda.clientes.buscar', [
                'telefono_buscado' => $telefono,
                'no_encontrado' => true
            ]);
        }
        
        // Obtener todos los apartados de este cliente (solo de esta tienda)
        $apartados = Apartado::where('cliente_id', $cliente->id)
            ->where('tienda_id', $tiendaId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $apartadoActivo = $apartados->where('estado', 'activo')->first();
        
        // Calcular totales
        $totalGastado = $apartados->sum('total');
        $totalPagado = $apartados->sum('total_pagado');
        
        return view('tienda.clientes.buscar', [
            'cliente' => $cliente,
            'apartados' => $apartados,
            'apartadoActivo' => $apartadoActivo,
            'totalGastado' => $totalGastado,
            'totalPagado' => $totalPagado
        ]);
    }
    
    /**
     * Redirigir a creación de nuevo apartado con datos precargados
     */
    public function nuevoApartado(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id'
        ]);
        
        $cliente = Cliente::findOrFail($request->cliente_id);
        
        // Verificar que el cliente pertenece a esta tienda
        $tiendaId = session('tienda_id');
        if ($cliente->tienda_id != $tiendaId) {
            return redirect()->route('tienda.clientes.buscar')
                ->with('error', 'Cliente no pertenece a tu tienda');
        }
        
        return redirect()->route('tienda.apartados.crear')
            ->withInput([
                'nombre_cliente' => $cliente->nombre,
                'telefono_cliente' => $cliente->telefono,
                'email_cliente' => $cliente->email
            ]);
    }
    
    /**
     * Ver historial completo del cliente
     */
    public function historial($clienteId)
    {
        $tiendaId = session('tienda_id');
        
        $cliente = Cliente::where('id', $clienteId)
                          ->where('tienda_id', $tiendaId)
                          ->firstOrFail();
        
        $apartados = Apartado::where('cliente_id', $clienteId)
            ->where('tienda_id', $tiendaId)
            ->with('productos', 'pagos')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        $totalGastado = $apartados->sum('total');
        $totalPagado = $apartados->sum('total_pagado');
        
        return view('tienda.clientes.historial', compact(
            'cliente', 
            'apartados', 
            'totalGastado',
            'totalPagado'
        ));
    }
}