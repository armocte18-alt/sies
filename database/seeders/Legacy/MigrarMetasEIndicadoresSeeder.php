<?php

namespace Database\Seeders\Legacy;

use App\Models\IndicadorSucursalMensual;
use App\Models\MetaMensual;
use App\Models\Sucursal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Clona las 8 metas anuales por línea de negocio y los 1,594 indicadores
 * mensuales reales por sucursal de "sios_app_web", y usa el indicador más
 * reciente de cada sucursal para completar sucursal_finanzas con datos
 * reales (hoy solo 1 de 60 sucursales tenía cifras, de la siembra de
 * ejemplo original).
 *
 * Ejecutar manualmente:
 *   php artisan db:seed --class="Database\Seeders\Legacy\MigrarMetasEIndicadoresSeeder"
 */
class MigrarMetasEIndicadoresSeeder extends Seeder
{
    public function run(): void
    {
        foreach (DB::connection('sios_legacy')->table('sios_metas_mensuales')->get() as $fila) {
            MetaMensual::updateOrCreate(
                ['linea_negocio' => trim($fila->linea_negocio), 'anio' => $fila->anio],
                [
                    'tipo' => $fila->tipo,
                    'enero' => $fila->enero, 'febrero' => $fila->febrero, 'marzo' => $fila->marzo,
                    'abril' => $fila->abril, 'mayo' => $fila->mayo, 'junio' => $fila->junio,
                    'julio' => $fila->julio, 'agosto' => $fila->agosto, 'septiembre' => $fila->septiembre,
                    'octubre' => $fila->octubre, 'noviembre' => $fila->noviembre, 'diciembre' => $fila->diciembre,
                    'meta_anual' => $fila->meta_anual,
                    'producto_claves_vinculadas' => $fila->producto_claves_vinculadas,
                ],
            );
        }

        // El campo "sucursal" del legacy es el número de registro (p. ej. 9117),
        // no un id; se resuelve contra clave_financiera con el mismo formato de
        // 5 dígitos usado en toda la app (ver Concerns\FormateaClaveSucursal).
        $sucursalPorClave = Sucursal::pluck('id', 'clave_financiera');

        $sinResolver = 0;
        $filas = DB::connection('sios_legacy')->table('sios_indicadores_sucursal_mensual')->get();

        foreach ($filas as $fila) {
            $claveLegacy = str_pad((string) $fila->sucursal, 5, '0', STR_PAD_LEFT);
            $sucursalId = $sucursalPorClave[$claveLegacy] ?? null;

            if (! $sucursalId) {
                $sinResolver++;
            }

            IndicadorSucursalMensual::updateOrCreate(
                ['clave_sucursal_legacy' => $claveLegacy, 'mes' => $fila->mes, 'anio' => $fila->anio],
                [
                    'sucursal_id' => $sucursalId,
                    'nombre_sucursal_legacy' => trim((string) $fila->admon) ?: null,
                    'edo' => $fila->edo,
                    'region' => $fila->region,
                    'volumen_total' => $fila->volumen_total,
                    'cantidad_situada' => $fila->cantidad_situada,
                    'ingresos_total' => $fila->ingresos_total,
                    'gasto_total' => $fila->gasto_total,
                    'balance' => $fila->balance,
                    'rentabilidad' => $fila->rentabilidad,
                    'productividad' => $fila->productividad,
                    'total_empleados' => $fila->total_empleados,
                    'dias_laborados' => $fila->dias_laborados,
                    'gasto_traslado_valores' => $fila->gasto_traslado_valores,
                    'gasto_servicios_basicos' => $fila->gasto_servicios_basicos,
                    'numero_oficio_carga' => $fila->numero_oficio_carga,
                ],
            );
        }

        $actualizadas = 0;

        foreach (Sucursal::all() as $sucursal) {
            $ultimo = IndicadorSucursalMensual::where('sucursal_id', $sucursal->id)
                ->orderByDesc('anio')->orderByDesc('mes')
                ->first();

            if (! $ultimo) {
                continue;
            }

            $sucursal->finanzas()->updateOrCreate([], [
                'volumen_total' => $ultimo->volumen_total,
                'cantidad_situada' => $ultimo->cantidad_situada,
                'ingreso_estimado' => $ultimo->ingresos_total,
                'gasto_total' => $ultimo->gasto_total,
            ]);
            $actualizadas++;
        }

        $this->command?->info(sprintf(
            '%d metas y %d indicadores mensuales migrados (%d sin sucursal reconocida); finanzas actualizadas en %d sucursales.',
            MetaMensual::count(),
            $filas->count(),
            $sinResolver,
            $actualizadas,
        ));
    }
}
