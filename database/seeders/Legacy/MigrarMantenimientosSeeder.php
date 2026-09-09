<?php

namespace Database\Seeders\Legacy;

use App\Models\Empleado;
use App\Models\Mantenimiento;
use App\Models\MantenimientoBitacora;
use App\Models\MantenimientoMaterial;
use App\Models\Sucursal;
use App\Models\TipoMantenimiento;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Clona el catálogo y el historial real de Mantenimientos de "sios_app_web":
 * 5 tipos, 5 mantenimientos (incluyendo los ya dados de baja, respetando su
 * soft-delete tal como está en producción), sus materiales, personal
 * asignado y bitácora de auditoría.
 *
 * Ejecutar manualmente:
 *   php artisan db:seed --class="Database\Seeders\Legacy\MigrarMantenimientosSeeder"
 */
class MigrarMantenimientosSeeder extends Seeder
{
    public function run(): void
    {
        $tipoIdPorLegacy = [];
        foreach (DB::connection('sios_legacy')->table('tipos_mantenimiento')->orderBy('orden')->get() as $fila) {
            $tipo = TipoMantenimiento::updateOrCreate(
                ['slug' => $fila->slug],
                ['nombre' => $fila->nombre, 'color' => $fila->color, 'activo' => (bool) $fila->activo, 'orden' => $fila->orden],
            );
            $tipoIdPorLegacy[$fila->id] = $tipo->id;
        }

        $sucursalPorClave = Sucursal::pluck('id', 'clave_financiera');
        $sucursalPorLegacyId = DB::connection('sios_legacy')->table('sucursales')->pluck('registro_sucursal', 'id_sucursal')
            ->mapWithKeys(fn ($registro, $idLegacy) => [$idLegacy => $sucursalPorClave[str_pad($registro, 5, '0', STR_PAD_LEFT)] ?? null]);

        $idsEmpleadosLegacy = DB::connection('sios_legacy')->table('mantenimiento_personal')->distinct()->pluck('empleado_id');
        $numeroPorEmpleadoLegacy = DB::connection('sios_legacy')->table('empleados')
            ->whereIn('id_employee', $idsEmpleadosLegacy)
            ->pluck('numero_employee', 'id_employee');
        $empleadoIdPorNumero = Empleado::whereIn('no_empleado', $numeroPorEmpleadoLegacy->map(fn ($n) => (string) $n))
            ->pluck('id', 'no_empleado');
        $empleadoPorLegacyId = $numeroPorEmpleadoLegacy
            ->mapWithKeys(fn ($numero, $idLegacy) => [$idLegacy => $empleadoIdPorNumero[(string) $numero] ?? null]);

        $correoPorUsuarioLegacy = DB::connection('sios_legacy')->table('users')->pluck('email', 'id')
            ->map(fn ($correo) => Str::lower($correo));
        $usuarioSiesPorCorreo = User::pluck('id', 'email')->mapWithKeys(fn ($id, $correo) => [Str::lower($correo) => $id]);
        $resolverUsuario = function (?int $idLegacy) use ($correoPorUsuarioLegacy, $usuarioSiesPorCorreo): ?int {
            if (! $idLegacy || ! isset($correoPorUsuarioLegacy[$idLegacy])) {
                return null;
            }

            return $usuarioSiesPorCorreo[$correoPorUsuarioLegacy[$idLegacy]] ?? null;
        };

        $mantenimientoIdPorLegacy = [];
        foreach (DB::connection('sios_legacy')->table('mantenimientos')->orderBy('id')->get() as $fila) {
            $mantenimiento = Mantenimiento::withTrashed()->updateOrCreate(
                ['titulo' => $fila->titulo, 'created_at' => $fila->created_at],
                [
                    'sucursal_id' => $sucursalPorLegacyId[$fila->sucursal_id] ?? null,
                    'tipo_mantenimiento_id' => $tipoIdPorLegacy[$fila->tipo_mantenimiento_id] ?? TipoMantenimiento::first()->id,
                    'descripcion' => $fila->descripcion,
                    'prioridad' => $fila->prioridad,
                    'estatus' => $fila->estatus,
                    'fecha_programada_inicio' => $fila->fecha_programada_inicio,
                    'fecha_programada_fin' => $fila->fecha_programada_fin,
                    'fecha_inicio_real' => $fila->fecha_inicio_real,
                    'fecha_fin_real' => $fila->fecha_fin_real,
                    'costo_mano_obra' => $fila->costo_mano_obra,
                    'costo_materiales' => $fila->costo_materiales,
                    'comentario_cierre' => $fila->comentario_cierre,
                    'creado_por' => $resolverUsuario($fila->creado_por),
                    'cerrado_por' => $resolverUsuario($fila->cerrado_por),
                ],
            );
            $mantenimiento->forceFill([
                'updated_at' => $fila->updated_at,
                'deleted_at' => $fila->deleted_at,
            ])->save();

            $mantenimientoIdPorLegacy[$fila->id] = $mantenimiento->id;
        }

        $materialesCreados = 0;
        foreach (DB::connection('sios_legacy')->table('mantenimiento_materiales')->orderBy('id')->get() as $fila) {
            $mantenimientoId = $mantenimientoIdPorLegacy[$fila->mantenimiento_id] ?? null;

            if (! $mantenimientoId) {
                continue;
            }

            MantenimientoMaterial::updateOrCreate(
                ['mantenimiento_id' => $mantenimientoId, 'nombre_material' => $fila->nombre_material, 'created_at' => $fila->created_at],
                [
                    'unidad' => $fila->unidad,
                    'cantidad' => $fila->cantidad,
                    'costo_unitario' => $fila->costo_unitario,
                    'costo_total' => $fila->costo_total,
                ],
            );
            $materialesCreados++;
        }

        $personalCreados = 0;
        foreach (DB::connection('sios_legacy')->table('mantenimiento_personal')->orderBy('id')->get() as $fila) {
            $mantenimientoId = $mantenimientoIdPorLegacy[$fila->mantenimiento_id] ?? null;
            $empleadoId = $empleadoPorLegacyId[$fila->empleado_id] ?? null;

            if (! $mantenimientoId || ! $empleadoId) {
                continue;
            }

            DB::table('mantenimiento_personal')->updateOrInsert(
                ['mantenimiento_id' => $mantenimientoId, 'empleado_id' => $empleadoId],
                [
                    'rol_en_mantenimiento' => $fila->rol_en_mantenimiento,
                    'created_at' => $fila->created_at,
                    'updated_at' => $fila->updated_at,
                ],
            );
            $personalCreados++;
        }

        $bitacoraCreada = 0;
        foreach (DB::connection('sios_legacy')->table('mantenimiento_bitacora')->orderBy('id')->get() as $fila) {
            $mantenimientoId = $mantenimientoIdPorLegacy[$fila->mantenimiento_id] ?? null;

            if (! $mantenimientoId) {
                continue;
            }

            $existe = MantenimientoBitacora::where('mantenimiento_id', $mantenimientoId)
                ->where('accion', $fila->accion)
                ->where('created_at', $fila->created_at)
                ->exists();

            if ($existe) {
                continue;
            }

            MantenimientoBitacora::create([
                'mantenimiento_id' => $mantenimientoId,
                'usuario_id' => $resolverUsuario($fila->usuario_id),
                'accion' => $fila->accion,
                'comentario' => $fila->comentario,
                'valor_anterior' => $fila->valor_anterior,
                'valor_nuevo' => $fila->valor_nuevo,
            ])->forceFill(['created_at' => $fila->created_at, 'updated_at' => $fila->updated_at])->save();
            $bitacoraCreada++;
        }

        $this->command?->info(sprintf(
            '%d tipos, %d mantenimientos, %d materiales, %d asignaciones de personal y %d entradas de bitácora migrados desde sios_app_web.',
            count($tipoIdPorLegacy),
            count($mantenimientoIdPorLegacy),
            $materialesCreados,
            $personalCreados,
            $bitacoraCreada,
        ));
    }
}
