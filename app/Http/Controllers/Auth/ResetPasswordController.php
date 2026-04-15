<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\UsuarioTienda;

class ResetPasswordController extends Controller
{
    /**
     * Mostrar formulario para restablecer contraseña
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Procesar el restablecimiento de contraseña
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        // Verificar si el token existe y no ha expirado (60 minutos)
        $reset = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$reset) {
            return back()->withErrors(['email' => 'El enlace de restablecimiento no es válido.']);
        }

        // Verificar expiración (60 minutos)
        $expiracion = Carbon::parse($reset->created_at)->addMinutes(60);
        if (Carbon::now()->greaterThan($expiracion)) {
            DB::table('password_resets')->where('email', $request->email)->delete();
            return back()->withErrors(['email' => 'El enlace ha expirado. Solicita uno nuevo.']);
        }

        // Actualizar contraseña
        $usuario = UsuarioTienda::where('email', $request->email)->first();
        
        if (!$usuario) {
            return back()->withErrors(['email' => 'No encontramos el usuario.']);
        }

        $usuario->password = Hash::make($request->password);
        $usuario->save();

        // Eliminar el token usado
        DB::table('password_resets')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', '¡Tu contraseña ha sido restablecida!');
    }
}