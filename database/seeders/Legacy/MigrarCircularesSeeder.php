<?php

namespace Database\Seeders\Legacy;

use App\Models\Circular;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Clona las 1,421 circulares reales de "sios_app_web", incluyendo los PDF
 * adjuntos que existan localmente en su disco (storage/app/public/circulares).
 *
 * Ejecutar manualmente:
 *   php artisan db:seed --class="Database\Seeders\Legacy\MigrarCircularesSeeder"
 */
class MigrarCircularesSeeder extends Seeder
{
    private const RUTA_ARCHIVOS_LEGACY = 'D:\\Servidor\\www\\sios-app-web\\storage\\app\\public\\circulares';

    public function run(): void
    {
        $usuariosLegacy = DB::connection('sios_legacy')->table('users')->pluck('name', 'id');

        $filas = DB::connection('sios_legacy')->table('circulares')->orderBy('id_circular')->get();

        $archivosCopiados = 0;
        $archivosFaltantes = 0;

        foreach ($filas as $fila) {
            $archivoPath = null;

            if ($fila->archivo_circular) {
                $nombreArchivo = basename($fila->archivo_circular);
                $origen = self::RUTA_ARCHIVOS_LEGACY.DIRECTORY_SEPARATOR.$nombreArchivo;

                if (File::exists($origen)) {
                    $destinoRelativo = 'circulares/'.$nombreArchivo;

                    if (! Storage::disk('public')->exists($destinoRelativo)) {
                        Storage::disk('public')->put($destinoRelativo, File::get($origen));
                    }

                    $archivoPath = $destinoRelativo;
                    $archivosCopiados++;
                } else {
                    $archivosFaltantes++;
                }
            }

            $circular = Circular::updateOrCreate(
                ['numero' => trim($fila->numero_circular)],
                [
                    'asunto' => trim($fila->asunto_circular),
                    'fecha_aplicacion' => $this->fechaValida($fila->fecha_aplicacion_circular),
                    'ambito' => $fila->ambito_circular ?: null,
                    'enlace_externo' => $fila->enlace_circular ?: null,
                    'archivo_path' => $archivoPath,
                    'tips' => $fila->tips_circular ?: null,
                    'creado_por' => $usuariosLegacy[$fila->user_id] ? Str::title($usuariosLegacy[$fila->user_id]) : null,
                    'activo' => (bool) $fila->estatus_circular,
                ],
            );

            // Conserva las fechas reales de publicación en lugar de "ahora".
            $circular->forceFill([
                'created_at' => $this->fechaValida($fila->created_at) ?? now(),
                'updated_at' => $this->fechaValida($fila->updated_at) ?? now(),
            ])->save();
        }

        $this->command?->info(sprintf(
            '%d circulares migradas desde sios_app_web (%d archivos PDF copiados, %d referenciados pero no encontrados en disco).',
            $filas->count(),
            $archivosCopiados,
            $archivosFaltantes,
        ));
    }

    /**
     * Algunos registros legacy traen fechas corruptas (p. ej. "-0001-11-30").
     */
    private function fechaValida(?string $fecha): ?string
    {
        if (! $fecha) {
            return null;
        }

        $anio = (int) substr($fecha, 0, 4);

        return ($anio >= 1990 && $anio <= 2100) ? $fecha : null;
    }
}
