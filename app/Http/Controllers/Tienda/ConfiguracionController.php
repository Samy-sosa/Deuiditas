<?php
// app/Http/Controllers/Tienda/ConfiguracionController.php

namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tienda;
use Illuminate\Support\Facades\Storage;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $tiendaId = session('tienda_id');
        $tienda = Tienda::findOrFail($tiendaId);
        
        return view('tienda.configuracion.index', compact('tienda'));
    }
    
    public function update(Request $request)
    {
        $tiendaId = session('tienda_id');
        $tienda = Tienda::findOrFail($tiendaId);
        
        $request->validate([
            'nombre_tienda' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'telefono_contacto' => 'nullable|string|max:20',
            'horario_atencion' => 'nullable|string|max:255',
            'direccion' => 'nullable|string',
            'sitio_web' => 'nullable|url|max:255',
            'facebook' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:20',
            
            // Datos bancarios
            'banco' => 'nullable|string|max:100',
            'clabe' => 'nullable|string|size:18',
            'cuenta' => 'nullable|string|max:20',
            'titular' => 'nullable|string|max:255',
            'rfc' => 'nullable|string|size:13',
            
            // Logo
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            // Ticket
            'ticket_mensaje' => 'nullable|string',
            'ticket_pie' => 'nullable|string',
            'ticket_mostrar_logo' => 'nullable|boolean'
        ]);
        
        // Actualizar datos básicos
        $tienda->nombre_tienda = $request->nombre_tienda;
        $tienda->descripcion = $request->descripcion;
        $tienda->telefono_contacto = $request->telefono_contacto;
        $tienda->horario_atencion = $request->horario_atencion;
        $tienda->direccion = $request->direccion;
        $tienda->sitio_web = $request->sitio_web;
        $tienda->facebook = $request->facebook;
        $tienda->instagram = $request->instagram;
        $tienda->whatsapp = $request->whatsapp;
        
        // ACTUALIZAR DATOS BANCARIOS
        $tienda->banco = $request->banco;
        $tienda->clabe = $request->clabe;
        $tienda->cuenta = $request->cuenta;
        $tienda->titular = $request->titular;
        $tienda->rfc = $request->rfc;
        
        // Subir logo
        if ($request->hasFile('logo')) {
            if ($tienda->logo_url && Storage::disk('public')->exists($tienda->logo_url)) {
                Storage::disk('public')->delete($tienda->logo_url);
            }
            
            $path = $request->file('logo')->store('logos', 'public');
            $tienda->logo_url = $path;
        }
        
        // Actualizar configuración del ticket
        $tienda->ticket_mensaje = $request->ticket_mensaje;
        $tienda->ticket_pie = $request->ticket_pie;
        $tienda->ticket_mostrar_logo = $request->has('ticket_mostrar_logo');
        
        $tienda->save();
        
        // REFRESCAR SESIÓN
        session([
            'nombre_tienda' => $tienda->nombre_tienda,
            'logo_tienda'   => $tienda->logo_url,
        ]);
        
        return redirect()->route('tienda.configuracion')
            ->with('success', 'Configuración actualizada correctamente');
    }
}