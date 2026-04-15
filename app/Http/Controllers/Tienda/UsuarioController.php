<?php
namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = DB::table('usuarios_tienda')
            ->where('tienda_id', session('tienda_id'))
            ->orderBy('id', 'desc')
            ->get();

        return view('tienda.usuarios.index', compact('usuarios'));
    }

    public function crear()
    {
        return view('tienda.usuarios.crear');
    }

    public function guardar(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email|unique:usuarios_tienda,email',
            'password' => 'required|min:6',
            'rol' => 'required|in:admin_tienda,vendedor'
        ]);

        DB::table('usuarios_tienda')->insert([
            'tienda_id' => session('tienda_id'),
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol' => $request->rol,
            'activo' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('tienda.usuarios')
            ->with('success', 'Usuario creado exitosamente');
    }

    public function eliminar($id)
    {
        // No permitir eliminar al admin principal
        $usuario = DB::table('usuarios_tienda')->where('id', $id)->first();
        
        if ($usuario->rol === 'admin_tienda') {
            return back()->with('error', 'No puedes eliminar al administrador principal');
        }

        DB::table('usuarios_tienda')->where('id', $id)->delete();

        return back()->with('success', 'Usuario eliminado');
    }
}