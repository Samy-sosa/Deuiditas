<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\UsuarioTienda;
use App\Mail\PasswordResetMail; // ← IMPORTANTE: Agregar esta línea
use Illuminate\Support\Facades\Mail; // ← Y esta también



class ForgotPasswordController extends Controller
{
    /**
     * Mostrar formulario para solicitar restablecimiento
     */
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Enviar enlace de restablecimiento al email
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        // Verificar si el email existe en usuarios_tienda
        $usuario = UsuarioTienda::where('email', $request->email)->first();
        
        if (!$usuario) {
            return back()->withErrors(['email' => 'No encontramos una cuenta con ese correo electrónico.']);
        }

        // Generar token único
        $token = Str::random(64);

        // Eliminar tokens anteriores para este email
        DB::table('password_resets')->where('email', $request->email)->delete();

        // Guardar nuevo token
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        // Enviar correo con el enlace
        $this->enviarCorreoRestablecimiento($request->email, $token);

        return back()->with('success', '¡Hemos enviado el enlace de restablecimiento a tu correo!');
    }

    /**
     * Enviar correo de restablecimiento
     */
   
private function enviarCorreoRestablecimiento($email, $token)
{
    $enlace = url('/password/reset/' . $token . '?email=' . urlencode($email));
    
    // Usar el sistema de correo de Laravel, que ya lee tu archivo .env
    Mail::to($email)->send(new PasswordResetMail($enlace, $email));
}
}