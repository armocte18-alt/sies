<?php

namespace Database\Seeders\Legacy;

use App\Models\MinutarioBoletin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * De los 3 boletines y 6 oficios reales en "sios_app_web", solo el boletín
 * B-0001/2026 es contenido real; el resto son pruebas del equipo al armar
 * ese módulo (asuntos como "QWERTY", "ASDFASD", o un oficio cuyo propio
 * asunto dice "prueba de que no guarda el solicitante"). Migrar esa
 * contaminación de prueba a un sistema en producción no aporta nada, así
 * que solo se clona el registro legítimo.
 *
 * Ejecutar manualmente:
 *   php artisan db:seed --class="Database\Seeders\Legacy\MigrarMinutariosSeeder"
 */
class MigrarMinutariosSeeder extends Seeder
{
    private const MAPA_COBERTURA = [
        1 => 'todos',
        2 => 'gerencia',
        3 => 'sucursales',
    ];

    public function run(): void
    {
        $fila = DB::connection('sios_legacy')->table('minutario_boletines')->where('id_boletin', 1)->first();

        if (! $fila) {
            $this->command?->warn('No se encontró el boletín B-0001/2026 en sios_app_web; nada que migrar.');

            return;
        }

        MinutarioBoletin::updateOrCreate(
            ['nomenclatura' => $fila->nomenclatura_completa],
            [
                'consecutivo' => $fila->consecutivo,
                'anio' => $fila->anio_boletin,
                'asunto' => $fila->asunto_boletin,
                'contenido' => $fila->contenido_boletin,
                'fecha_elaboracion' => $fila->fecha_elaboracion,
                'fecha_vigor' => $fila->fecha_vigor,
                'dirigido_tipo' => self::MAPA_COBERTURA[$fila->dirigido_tipo_id] ?? 'todos',
            ],
        );

        $this->command?->info('1 boletín real migrado desde sios_app_web (B-0001/2026); se omitieron 2 boletines y 6 oficios de prueba sin contenido real.');
    }
}
