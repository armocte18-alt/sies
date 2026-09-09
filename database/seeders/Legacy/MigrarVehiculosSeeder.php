<?php

namespace Database\Seeders\Legacy;

use App\Models\Conductor;
use App\Models\SolicitudVehiculo;
use App\Models\SolicitudVehiculoDetalle;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Clona el catálogo real de Vehículos Oficiales de "sios_app_web": 10
 * vehículos, 2 conductores y 15 solicitudes con su detalle de asignación.
 *
 * Ejecutar manualmente:
 *   php artisan db:seed --class="Database\Seeders\Legacy\MigrarVehiculosSeeder"
 */
class MigrarVehiculosSeeder extends Seeder
{
    public function run(): void
    {
        $vehiculoIdPorLegacy = [];
        foreach (DB::connection('sios_legacy')->table('sios_vehiculos')->orderBy('id')->get() as $fila) {
            $vehiculo = Vehiculo::updateOrCreate(
                ['placa' => Str::lower($fila->placa)],
                [
                    'marca' => Str::title($fila->marca),
                    'modelo' => Str::title($fila->modelo),
                    'anio' => $fila->anio,
                    'tipo' => $fila->tipo_vehiculo,
                    'tipo_otro' => $fila->tipo_vehiculo_otro,
                    'folio_tarjeta_circulacion' => $fila->folio_tarjeta_circulacion,
                    'vigencia_tarjeta_circulacion' => $fila->vigencia_tarjeta_circulacion,
                    'folio_poliza_seguro' => $fila->folio_poliza_seguro,
                    'vigencia_poliza_seguro' => $fila->vigencia_poliza_seguro,
                    'kilometraje_actual' => $fila->kilometraje_actual,
                    'combustible_inicial' => $fila->combustible_inicial,
                    'equipo_seguridad' => $fila->equipo_seguridad ? json_decode($fila->equipo_seguridad, true) : null,
                    'condicion_notas' => $fila->condicion_notas,
                    'fecha_ultima_verificacion' => $fila->fecha_ultima_verificacion,
                    'tipo_holograma' => $fila->tipo_holograma,
                    'fecha_ultimo_servicio' => $fila->fecha_ultimo_servicio,
                    'estatus' => $fila->estatus,
                ],
            );
            $vehiculoIdPorLegacy[$fila->id] = $vehiculo->id;
        }

        $conductorIdPorLegacy = [];
        foreach (DB::connection('sios_legacy')->table('sios_conductores')->orderBy('id')->get() as $fila) {
            $conductor = Conductor::updateOrCreate(
                ['numero_licencia' => $fila->numero_licencia],
                [
                    'nombre_completo' => Str::title(trim($fila->nombre_completo)),
                    'numero_empleado' => $fila->numero_empleado,
                    'area_adscripcion' => $fila->area_adscripcion,
                    'tipo_licencia' => $fila->tipo_licencia,
                    'es_permanente' => (bool) $fila->es_permanente,
                    'vigencia_licencia' => $fila->vigencia_licencia,
                    'estatus' => $fila->estatus === 'activo' ? 'activo' : 'inactivo',
                ],
            );
            $conductorIdPorLegacy[$fila->id] = $conductor->id;
        }

        $correoPorUsuarioLegacy = DB::connection('sios_legacy')->table('users')->pluck('email', 'id')
            ->map(fn ($correo) => Str::lower($correo));
        $usuarioSiesPorCorreo = User::pluck('id', 'email')->mapWithKeys(fn ($id, $correo) => [Str::lower($correo) => $id]);

        $resolverUsuario = function (?int $idLegacy) use ($correoPorUsuarioLegacy, $usuarioSiesPorCorreo): ?int {
            if (! $idLegacy || ! isset($correoPorUsuarioLegacy[$idLegacy])) {
                return null;
            }

            return $usuarioSiesPorCorreo[$correoPorUsuarioLegacy[$idLegacy]] ?? null;
        };

        $detallesPorSolicitud = DB::connection('sios_legacy')->table('sios_solicitud_vehiculo')
            ->orderBy('id')
            ->get()
            ->keyBy('solicitud_id');

        $solicitudesCreadas = 0;
        $detallesCreados = 0;

        foreach (DB::connection('sios_legacy')->table('sios_solicitudes_vehiculos')->orderBy('id')->get() as $fila) {
            $solicitanteId = $resolverUsuario($fila->solicitante_id);

            if (! $solicitanteId) {
                continue;
            }

            $solicitud = SolicitudVehiculo::where('solicitante_id', $solicitanteId)
                ->where('created_at', $fila->created_at)
                ->first();

            if (! $solicitud) {
                $solicitud = new SolicitudVehiculo(['solicitante_id' => $solicitanteId]);
            }

            $solicitud->fill([
                'numero_empleado' => $fila->numero_empleado,
                'area' => $fila->area,
                'area_otro' => $fila->area_otro,
                'fecha_salida_desde' => $fila->fecha_salida_desde,
                'fecha_salida_hasta' => $fila->fecha_salida_hasta,
                'destinos' => $fila->destinos ? json_decode($fila->destinos, true) : [],
                'motivo' => $fila->motivo,
                'estatus' => $fila->estatus === 'en_curso' ? 'autorizada' : $fila->estatus,
                'autorizado_por' => $resolverUsuario($fila->autorizado_por),
                'autorizado_at' => $fila->autorizado_at,
                'motivo_rechazo' => $fila->motivo_rechazo,
            ])->save();

            $solicitud->forceFill([
                'created_at' => $fila->created_at,
                'updated_at' => $fila->updated_at,
            ])->save();
            $solicitudesCreadas++;

            $detalleLegacy = $detallesPorSolicitud->get($fila->id);

            if (! $detalleLegacy) {
                continue;
            }

            $vehiculoId = $vehiculoIdPorLegacy[$detalleLegacy->vehiculo_id] ?? null;
            $conductorId = $conductorIdPorLegacy[$detalleLegacy->conductor_id] ?? null;

            if (! $vehiculoId || ! $conductorId) {
                continue;
            }

            SolicitudVehiculoDetalle::updateOrCreate(
                ['solicitud_id' => $solicitud->id],
                [
                    'vehiculo_id' => $vehiculoId,
                    'conductor_id' => $conductorId,
                    'km_inicial' => $detalleLegacy->km_inicial,
                    'km_final' => $detalleLegacy->km_final,
                    'km_recorridos' => $detalleLegacy->km_recorridos,
                    'combustible_entrega' => $detalleLegacy->combustible_entrega,
                    'combustible_recepcion' => $detalleLegacy->combustible_recepcion,
                    'carroceria_notas_previas' => $detalleLegacy->carroceria_notas_previas,
                    'carroceria_notas_nuevas' => $detalleLegacy->carroceria_notas_nuevas,
                    'checklist_gato' => (bool) $detalleLegacy->checklist_gato,
                    'checklist_llave_cruz' => (bool) $detalleLegacy->checklist_llave_cruz,
                    'checklist_reflejantes' => (bool) $detalleLegacy->checklist_reflejantes,
                    'checklist_extintor' => (bool) $detalleLegacy->checklist_extintor,
                    'fecha_hora_devolucion' => $detalleLegacy->fecha_hora_devolucion,
                    'observaciones_fallas' => $detalleLegacy->observaciones_fallas,
                ],
            );
            $detallesCreados++;
        }

        $this->command?->info(sprintf(
            '%d vehículos, %d conductores, %d solicitudes y %d detalles migrados desde sios_app_web.',
            count($vehiculoIdPorLegacy),
            count($conductorIdPorLegacy),
            $solicitudesCreadas,
            $detallesCreados,
        ));
    }
}
