<?php
namespace App\Http\Controllers\Tienda;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Apartado;
use App\Models\Tienda;

class BuscarController extends Controller
{
    /**
     * Buscar apartados
     */
    public function buscar(Request $request)
    {
        $query = $request->get('q');
        $tiendaId = session('tienda_id');
        
        // Verificar sesión
        if (!$tiendaId) {
            return redirect()->route('login');
        }
        
        // Validar búsqueda
        if (!$query || strlen($query) < 2) {
            return redirect()->route('tienda.dashboard')
                ->with('error', 'Ingresa al menos 2 caracteres para buscar');
        }

        // Buscar apartados
        $resultados = Apartado::with('productos')
            ->where('tienda_id', $tiendaId)
            ->where(function($q) use ($query) {
                $q->where('codigo_unico', 'LIKE', '%' . $query . '%')
                  ->orWhere('nombre_cliente', 'LIKE', '%' . $query . '%')
                  ->orWhere('telefono_cliente', 'LIKE', '%' . $query . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends(['q' => $query]);

        return view('tienda.apartados.buscar', [
            'resultados' => $resultados,
            'termino' => $query
        ]);
    }

    /**
     * Buscar por código exacto (redirige al detalle)
     */
    public function buscarPorCodigo(Request $request)
    {
        $codigo = $request->get('codigo');
        $tiendaId = session('tienda_id');
        
        $apartado = Apartado::where('tienda_id', $tiendaId)
            ->where('codigo_unico', $codigo)
            ->first();
        
        if ($apartado) {
            return redirect()->route('tienda.apartados.mostrar', $apartado->id);
        }
        
        return redirect()->route('tienda.dashboard')
            ->with('error', 'Código no encontrado');
    }
}