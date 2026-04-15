<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Models\Tienda;
use App\Models\Apartado;
use App\Models\UsuarioTienda;

class SuperAdminController extends Controller
{
    /**
     * Dashboard con estadísticas globales
     */
    public function dashboard()
    {
        // Cachear estadísticas por 5 minutos
        $stats = Cache::remember('admin_dashboard_stats', 300, function () {
            return [
                'totalTiendas' => Tienda::count(),
                'tiendasActivas' => Tienda::where('estado', 'activa')->count(),
                'totalApartados' => Apartado::count(),
                'totalIngresos' => DB::table('pagos_apartado')->sum('monto') ?? 0,
                'tiendasPorVencer' => Tienda::porVencer(7)->count(),
            ];
        });

        $ultimasTiendas = Cache::remember('admin_ultimas_tiendas', 300, function () {
            return Tienda::withCount('apartados')
                ->latest()
                ->limit(5)
                ->get();
        });

        return view('admin.dashboard', array_merge($stats, [
            'ultimasTiendas' => $ultimasTiendas
        ]));
    }

    /**
     * Listado de tiendas con filtros
     */
    public function listarTiendas(Request $request)
    {
        $query = Tienda::withCount('apartados', 'usuarios');

        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('buscar')) {
            $query->where(function($q) use ($request) {
                $q->where('nombre_tienda', 'LIKE', "%{$request->buscar}%")
                  ->orWhere('email', 'LIKE', "%{$request->buscar}%")
                  ->orWhere('iniciales', 'LIKE', "%{$request->buscar}%");
            });
        }

        $tiendas = $query->orderBy('id', 'desc')->paginate(15);

        return view('admin.tiendas.index', compact('tiendas'));
    }

    /**
     * Mostrar detalles de una tienda
     */
    public function verTienda($id)
    {
        $tienda = Tienda::with(['usuarios', 'apartados' => function($q) {
            $q->latest()->limit(10);
        }])->findOrFail($id);

        $stats = [
            'totalApartados' => $tienda->apartados()->count(),
            'activos' => $tienda->apartados()->where('estado', 'activo')->count(),
            'pagados' => $tienda->apartados()->where('estado', 'pagado')->count(),
            'ingresos' => DB::table('pagos_apartado')
                ->join('apartados', 'pagos_apartado.apartado_id', '=', 'apartados.id')
                ->where('apartados.tienda_id', $id)
                ->sum('pagos_apartado.monto') ?? 0
        ];

        return view('admin.tiendas.ver', compact('tienda', 'stats'));
    }

    /**
     * Formulario para crear tienda
     */
    public function crearTienda()
    {
        return view('admin.tiendas.crear');
    }

    /**
     * Guardar nueva tienda (con transacción)
     */
    public function guardarTienda(Request $request)
    {
        $request->validate([
            'nombre_tienda' => 'required|string|max:100',
            'iniciales' => 'required|string|max:10|unique:tiendas,iniciales',
            'email' => 'required|email|max:100',
            'telefono' => 'required|string|max:20',
            'direccion' => 'required|string',
            'nombre_admin' => 'required|string|max:100',
            'email_admin' => 'required|email|unique:usuarios_tienda,email',
            'password_admin' => 'required|min:6',
            'fecha_renovacion' => 'required|date|after:today'
        ]);

        DB::beginTransaction();

        try {
            // Crear tienda
            $tienda = Tienda::create([
                'super_admin_id' => auth()->id(),
                'nombre_tienda' => $request->nombre_tienda,
                'iniciales' => strtoupper($request->iniciales),
                'email' => $request->email,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
                'fecha_renovacion' => $request->fecha_renovacion,
                'estado' => 'activa'
            ]);

            // Crear admin de tienda
            UsuarioTienda::create([
                'tienda_id' => $tienda->id,
                'nombre' => $request->nombre_admin,
                'email' => $request->email_admin,
                'password' => Hash::make($request->password_admin),
                'rol' => 'admin_tienda',
                'activo' => true
            ]);

            DB::commit();

            // Limpiar caché
            Cache::forget('admin_dashboard_stats');

            return redirect()->route('admin.tiendas')
                ->with('success', "Tienda '{$tienda->nombre_tienda}' creada correctamente");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Error al crear tienda: ' . $e->getMessage());
        }
    }

    /**
     * Formulario para editar tienda
     */
    public function editarTienda($id)
    {
        $tienda = Tienda::findOrFail($id);
        return view('admin.tiendas.editar', compact('tienda'));
    }

    /**
     * Actualizar tienda
     */
    public function actualizarTienda(Request $request, $id)
    {
        $tienda = Tienda::findOrFail($id);

        $request->validate([
            'nombre_tienda' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'telefono' => 'required|string|max:20',
            'direccion' => 'required|string',
            'fecha_renovacion' => 'required|date'
        ]);

        $tienda->update($request->only([
            'nombre_tienda', 'email', 'telefono', 
            'direccion', 'fecha_renovacion'
        ]));

        Cache::forget('admin_dashboard_stats');
        Cache::forget('tienda_' . $id);

        return redirect()->route('admin.tiendas.ver', $id)
            ->with('success', 'Tienda actualizada correctamente');
    }

    /**
     * Suspender tienda
     */
    public function suspenderTienda($id)
    {
        $tienda = Tienda::findOrFail($id);
        $tienda->estado = 'suspendida';
        $tienda->save();

        Cache::forget('admin_dashboard_stats');

        return back()->with('success', "Tienda '{$tienda->nombre_tienda}' suspendida");
    }

    /**
     * Activar tienda
     */
    public function activarTienda($id)
    {
        $tienda = Tienda::findOrFail($id);
        $tienda->estado = 'activa';
        $tienda->save();

        Cache::forget('admin_dashboard_stats');

        return back()->with('success', "Tienda '{$tienda->nombre_tienda}' activada");
    }
}