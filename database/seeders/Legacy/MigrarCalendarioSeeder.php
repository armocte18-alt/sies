<?php

namespace Database\Seeders\Legacy;

use App\Models\EventoCalendario;
use App\Models\EventoRapido;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Clona los eventos reales de calendario y los accesos rápidos de
 * "sios_app_web".
 *
 * Ejecutar manualmente:
 *   php artisan db:seed --class="Database\Seeders\Legacy\MigrarCalendarioSeeder"
 */
class MigrarCalendarioSeeder extends Seeder
{
    public function run(): void
    {
        $usuariosLegacy = DB::connection('sios_legacy')->table('users')->pluck('name', 'id');

        $eventos = DB::connection('sios_legacy')->table('eventos_calendario')->orderBy('id_evento')->get();

        foreach ($eventos as $fila) {
            $existe = EventoCalendario::where('nombre', trim($fila->nombre_evento))
                ->whereDate('fecha_inicio', $fila->fecha_inicio_evento)
                ->exists();

            if ($existe) {
                continue;
            }

            EventoCalendario::create([
                'grupo_recurrencia_id' => $fila->grupo_recurrencia_id,
                'nombre' => trim($fila->nombre_evento),
                'fecha_inicio' => $fila->fecha_inicio_evento,
                'fecha_fin' => $fila->fecha_fin_evento,
                'hora_inicio' => $fila->hora_inicio_evento,
                'hora_fin' => $fila->hora_fin_evento,
                'ubicacion' => $fila->ubicacion_evento,
                'tipo_asociado' => $fila->asociado_tipo_evento,
                'color' => $fila->color_evento ?: '#135c46',
                'invitados' => $fila->invitados_evento ? json_decode($fila->invitados_evento, true) : null,
                'creado_por_nombre' => $usuariosLegacy[$fila->creado_por_evento] ?? null,
                'notas' => $fila->notas_evento,
            ]);
        }

        foreach (DB::connection('sios_legacy')->table('eventos_rapidos_calendario')->orderBy('orden_evento_rapido')->get() as $fila) {
            EventoRapido::updateOrCreate(
                ['nombre' => Str::title($fila->nombre_evento_rapido)],
                ['color' => $fila->color_evento_rapido, 'orden' => $fila->orden_evento_rapido],
            );
        }

        $this->command?->info(sprintf(
            '%d eventos y %d accesos rápidos migrados desde sios_app_web.',
            $eventos->count(),
            DB::connection('sios_legacy')->table('eventos_rapidos_calendario')->count(),
        ));
    }
}
