<?php

namespace Database\Seeders\Legacy;

use App\Models\Empleado;
use App\Models\Sucursal;
use Database\Seeders\Legacy\Concerns\FormateaClaveSucursal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Clona los 183 empleados reales de "sios_app_web" y los liga a la sucursal
 * ya migrada por MigrarSucursalesSeeder (debe ejecutarse primero), además de
 * asignar el titular de cada sucursal a partir de "encargado_sucursal".
 *
 * Ejecutar manualmente:
 *   php artisan db:seed --class="Database\Seeders\Legacy\MigrarEmpleadosSeeder"
 */
class MigrarEmpleadosSeeder extends Seeder
{
    use FormateaClaveSucursal;

    public function run(): void
    {
        $puestos = DB::connection('sios_legacy')->table('empleado_tipo_puesto')->pluck('nombre_puesto_tipo', 'id_puesto_tipo');
        $labores = DB::connection('sios_legacy')->table('empleado_tipo_labor')->pluck('nombre_labor_tipo', 'id_labor_tipo');
        $sucursalesLegacy = DB::connection('sios_legacy')->table('sucursales')->pluck('registro_sucursal', 'id_sucursal');

        // legacy id_sucursal -> Sucursal::id de SIES, vía la clave financiera ya migrada.
        $sucursalIdPorLegacy = [];
        foreach ($sucursalesLegacy as $legacyId => $registro) {
            $sucursal = Sucursal::where('clave_financiera', $this->claveSucursal($registro))->first();
            if ($sucursal) {
                $sucursalIdPorLegacy[$legacyId] = $sucursal->id;
            }
        }

        $filas = DB::connection('sios_legacy')->table('empleados')->orderBy('id_employee')->get();

        // legacy id_employee -> Empleado::id de SIES, para ligar titulares al final.
        $empleadoIdPorLegacy = [];
        $sinSucursal = 0;

        foreach ($filas as $fila) {
            $empleado = Empleado::updateOrCreate(
                ['no_empleado' => (string) $fila->numero_employee],
                [
                    'sucursal_id' => $sucursalIdPorLegacy[$fila->centro_adscripion_id] ?? null,
                    'nombre' => Str::title($fila->nombre_employee),
                    'apellido_paterno' => Str::title($fila->a_paterno_employee),
                    'apellido_materno' => $fila->a_materno_employee ? Str::title($fila->a_materno_employee) : null,
                    'funcion_laboral' => $labores[$fila->labores_id] ?? null,
                    'puesto' => $this->limpiaPuesto($puestos[$fila->puesto_id] ?? null),
                    'activo' => ! in_array(Str::lower((string) $fila->estatus_employee), ['baja', 'inactivo'], true),
                ],
            );

            $empleadoIdPorLegacy[$fila->id_employee] = $empleado->id;

            if (! ($sucursalIdPorLegacy[$fila->centro_adscripion_id] ?? null)) {
                $sinSucursal++;
            }
        }

        $titularesAsignados = 0;

        foreach (DB::connection('sios_legacy')->table('sucursales')->whereNotNull('encargado_sucursal')->get(['id_sucursal', 'encargado_sucursal']) as $fila) {
            $sucursalId = $sucursalIdPorLegacy[$fila->id_sucursal] ?? null;
            $empleadoId = $empleadoIdPorLegacy[$fila->encargado_sucursal] ?? null;

            if ($sucursalId && $empleadoId) {
                Sucursal::whereKey($sucursalId)->update(['titular_empleado_id' => $empleadoId]);
                $titularesAsignados++;
            }
        }

        $this->command?->info(sprintf(
            '%d empleados migrados desde sios_app_web (%d sin sucursal reconocida); %d titulares asignados.',
            $filas->count(),
            $sinSucursal,
            $titularesAsignados,
        ));
    }

    /**
     * El catálogo legacy de puestos trae comillas de cierre triplicadas en
     * algunos valores, p. ej. 'JEFE DE OFICINA TELEGRÁFICA "A"""'.
     */
    private function limpiaPuesto(?string $puesto): ?string
    {
        return $puesto ? preg_replace('/"+$/', '"', trim($puesto)) : null;
    }
}
