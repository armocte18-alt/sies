<?php

namespace Database\Seeders\Legacy;

use App\Models\Gerencia;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Importa el directorio de gerencias desde la base de datos del sistema
 * SIOS anterior (D:\Servidor\www\sios-app-web). Requiere que la conexión
 * 'sios_legacy' esté configurada en .env (ver config/database.php) — no se
 * ejecuta como parte del flujo normal de `php artisan db:seed`.
 *
 * Uso: php artisan db:seed --class="Database\Seeders\Legacy\MigrarGerenciasSeeder"
 */
class MigrarGerenciasSeeder extends Seeder
{
    public function run(): void
    {
        $registros = DB::connection('sios_legacy')->table('gerencias')->get();

        foreach ($registros as $registro) {
            Gerencia::updateOrCreate(
                ['correo_finabien' => mb_strtolower(trim($registro->correo_finabien))],
                [
                    'nombre' => $registro->nombre_gerencia,
                    'coordinacion' => $registro->coordinacion_gerencia,
                    'extension' => $registro->extension_gerencia,
                    'correo_sigitel' => $registro->correo_sigitel,
                    'comite' => $registro->comite_gerencia,
                    'observaciones' => $registro->observaciones_gerencia,
                    'activo' => (int) $registro->estatus_gerencia === 1,
                ],
            );
        }

        $this->command?->info(count($registros).' gerencias importadas desde el sistema SIOS anterior.');
    }
}
