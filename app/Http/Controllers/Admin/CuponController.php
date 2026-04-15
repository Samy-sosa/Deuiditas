<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cupon;
use App\Models\CuponUsado;
use Carbon\Carbon;

class CuponController extends Controller
{
    /**
     * Listado de cupones
     */
    public function index(Request $request)
    {
        $query = Cupon::query();
        
        if ($request->has('buscar')) {
            $query->where('codigo', 'LIKE', "%{$request->buscar}%")
                  ->orWhere('descripcion', 'LIKE', "%{$request->buscar}%");
        }
        
        if ($request->has('estado')) {
            if ($request->estado === 'activo') {
                $query->activos();
            } elseif ($request->estado === 'inactivo') {
                $query->where('activo', false);
            } elseif ($request->estado === 'expirado') {
                $query->where('fecha_expiracion', '<', now());
            }
        }
        
        $cupones = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('admin.cupones.index', compact('cupones'));
    }

    /**
     * Formulario para crear cupón
     */
    public function crear()
    {
        return view('admin.cupones.crear');
    }

    /**
     * Guardar nuevo cupón
     */
    public function guardar(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:50|unique:cupones',
            'descripcion' => 'nullable|string',
            'tipo_descuento' => 'required|in:porcentaje,fijo',
            'valor_descuento' => 'required|numeric|min:0',
            'monto_minimo' => 'nullable|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'fecha_expiracion' => 'required|date|after:fecha_inicio',
            'usos_maximos' => 'nullable|integer|min:1'
        ]);

        Cupon::create([
            'codigo' => strtoupper($request->codigo),
            'descripcion' => $request->descripcion,
            'tipo_descuento' => $request->tipo_descuento,
            'valor_descuento' => $request->valor_descuento,
            'monto_minimo' => $request->monto_minimo,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_expiracion' => $request->fecha_expiracion,
            'usos_maximos' => $request->usos_maximos,
            'usos_actuales' => 0,
            'activo' => true
        ]);

        return redirect()->route('admin.cupones')
            ->with('success', 'Cupón creado correctamente');
    }

    /**
     * Formulario para editar cupón
     */
    public function editar($id)
    {
        $cupon = Cupon::findOrFail($id);
        return view('admin.cupones.editar', compact('cupon'));
    }

    /**
     * Actualizar cupón
     */
    public function actualizar(Request $request, $id)
    {
        $cupon = Cupon::findOrFail($id);
        
        $request->validate([
            'codigo' => 'required|string|max:50|unique:cupones,codigo,' . $id,
            'descripcion' => 'nullable|string',
            'tipo_descuento' => 'required|in:porcentaje,fijo',
            'valor_descuento' => 'required|numeric|min:0',
            'monto_minimo' => 'nullable|numeric|min:0',
            'fecha_inicio' => 'required|date',
            'fecha_expiracion' => 'required|date|after:fecha_inicio',
            'usos_maximos' => 'nullable|integer|min:1',
            'activo' => 'boolean'
        ]);

        $cupon->update([
            'codigo' => strtoupper($request->codigo),
            'descripcion' => $request->descripcion,
            'tipo_descuento' => $request->tipo_descuento,
            'valor_descuento' => $request->valor_descuento,
            'monto_minimo' => $request->monto_minimo,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_expiracion' => $request->fecha_expiracion,
            'usos_maximos' => $request->usos_maximos,
            'activo' => $request->has('activo')
        ]);

        return redirect()->route('admin.cupones')
            ->with('success', 'Cupón actualizado correctamente');
    }

    /**
     * Eliminar cupón (solo si no tiene usos)
     */
    public function eliminar($id)
    {
        $cupon = Cupon::withCount('usos')->findOrFail($id);
        
        if ($cupon->usos_count > 0) {
            return redirect()->route('admin.cupones')
                ->with('error', 'No se puede eliminar un cupón que ya ha sido usado');
        }
        
        $cupon->delete();
        
        return redirect()->route('admin.cupones')
            ->with('success', 'Cupón eliminado correctamente');
    }

    /**
     * Cambiar estado (activar/desactivar)
     */
    public function toggleEstado($id)
    {
        $cupon = Cupon::findOrFail($id);
        $cupon->activo = !$cupon->activo;
        $cupon->save();
        
        $mensaje = $cupon->activo ? 'Cupón activado' : 'Cupón desactivado';
        
        return redirect()->route('admin.cupones')
            ->with('success', $mensaje);
    }

    /**
     * Ver estadísticas de un cupón
     */
    public function estadisticas($id)
    {
        $cupon = Cupon::with(['usos.tienda'])->findOrFail($id);
        
        $stats = [
            'total_usos' => $cupon->usos->count(),
            'descuento_total' => $cupon->usos->sum('descuento_aplicado'),
            'monto_total_original' => $cupon->usos->sum('monto_original'),
            'monto_total_final' => $cupon->usos->sum('monto_final'),
            'usos_por_fecha' => $cupon->usos->groupBy(function($item) {
                return $item->fecha_uso->format('Y-m-d');
            })->map->count()
        ];
        
        return view('admin.cupones.estadisticas', compact('cupon', 'stats'));
    }
}