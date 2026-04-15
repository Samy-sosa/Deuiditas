<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tienda;
use Carbon\Carbon;

class VerificarSuscripcionesVencidas extends Command
{
    protected $signature = 'suscripciones:verificar';
    protected $description = 'Verifica y suspende suscripciones vencidas';

    public function handle()
    {
        $this->info('🔍 Verificando suscripciones vencidas...');
        
        $tiendas = Tienda::where('estado', 'activa')
            ->where('fecha_expiracion', '<', Carbon::now())
            ->get();

        $contador = 0;
        foreach ($tiendas as $tienda) {
            $tienda->estado = 'suspendida';
            $tienda->save();
            $contador++;
            $this->line("   ✅ Tienda {$tienda->nombre_tienda} (ID: {$tienda->id}) suspendida");
        }

        $this->info("✅ Proceso completado. {$contador} tiendas suspendidas.");
        
        return Command::SUCCESS;
    }
}