<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (session('tipo') !== 'super_admin') {
            return redirect('/login')->with('error', 'Acceso no autorizado');
        }
        return $next($request);
    }
}