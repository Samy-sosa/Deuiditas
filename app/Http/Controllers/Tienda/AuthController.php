<?php
namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UsuarioTienda;
use App\Models\SuperAdmin; // ← IMPORTAR SUPER ADMIN

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // ============================================
        // 1. Intentar login como SUPER ADMIN
        // ============================================
        if (Auth::guard('superadmin')->attempt($credentials)) {
            $admin = Auth::guard('superadmin')->user();
            
            session([
                'user_id' => $admin->id,
                'nombre' => $admin->nombre,
                'email' => $admin->email,
                'rol' => 'superadmin'
            ]);
            
            return redirect()->intended('admin/dashboard');
        }

        // ============================================
        // 2. Intentar login como USUARIO DE TIENDA
        // ============================================
        if (Auth::attempt($credentials)) {
            $usuario = Auth::user();
            
            // Verificar si el usuario está activo
            if (!$usuario->activo) {
                Auth::logout();
                return back()->with('error', 'Tu cuenta está desactivada');
            }

            // Cargar la relación de la tienda
            $usuario->load('tienda');

            // Guardar datos en sesión
            session([
                'tienda_id' => $usuario->tienda_id,
                'nombre_tienda' => $usuario->tienda ? $usuario->tienda->nombre_tienda : 'Tienda',
                'nombre' => $usuario->nombre,
                'rol' => $usuario->rol,
                'email' => $usuario->email
            ]);

            return redirect()->intended('tienda/dashboard');
        }

        // ============================================
        // 3. Si ninguna autenticación funcionó
        // ============================================
        return back()->with('error', 'Credenciales incorrectas');
    }

    public function logout(Request $request)
    {
        // Cerrar sesión en ambos guards
        Auth::logout();
        Auth::guard('superadmin')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}