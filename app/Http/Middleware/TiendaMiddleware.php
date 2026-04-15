<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TiendaMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (session('tipo') !== 'tienda') {
            return redirect('/login')->with('error', 'Acceso no autorizado');
        }
        return $next($request);
    }
}