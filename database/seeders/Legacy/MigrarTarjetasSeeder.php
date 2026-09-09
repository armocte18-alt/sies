<?php

namespace Database\Seeders\Legacy;

use App\Models\Sucursal;
use App\Models\TarjetaHistorial;
use App\Models\TarjetaInventario;
use App\Models\TarjetaProducto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Clona el inventario real de Control de Tarjetas de "sios_app_web": 1
 * producto (FINABIEN), 9 tarjetas y su historial de movimientos.
 *
 * Ejecutar manualmente:
 *   php artisan db:seed --class="Database\Seeders\Legacy\MigrarTarjetasSeeder"
 */
class MigrarTarjetasSeeder extends Seeder
{
    private const MAPA_ESTATUS = [
        'En Stock' => 'en_stock',
        'Activa' => 'activa',
        'Renominada' => 'renominada',
    ];

    public function run(): void
    {
        $productoIdPorLegacy = [];
        foreach (DB::connection('sios_legacy')->table('sios_tarjetas_productos')->get() as $fila) {
            $producto = TarjetaProducto::updateOrCreate(
                ['slug' => $fila->slug_producto],
                ['nombre' => $fila->nombre_producto, 'descripcion' => $fila->descripcion_producto],
            );
            $productoIdPorLegacy[$fila->id_producto] = $producto->id;
        }

        $sucursalPorClave = Sucursal::pluck('id', 'clave_financiera');

        $tarjetaIdPorLegacy = [];
        foreach (DB::connection('sios_legacy')->table('sios_tarjetas_inventario')->orderBy('id_registro')->get() as $fila) {
            $destinoSucursalId = null;
            if ($fila->destino_vinculado) {
                $clave = str_pad($fila->destino_vinculado, 5, '0', STR_PAD_LEFT);
                $destinoSucursalId = $sucursalPorClave[$clave] ?? null;
            }

            $tarjeta = TarjetaInventario::updateOrCreate(
                ['cuenta' => $fila->cuenta],
                [
                    'producto_id' => $productoIdPorLegacy[$fila->id_producto] ?? TarjetaProducto::first()?->id,
                    'numero_tarjeta' => $fila->numero_tarjeta,
                    'fecha_recepcion' => $fila->fecha_recepcion,
                    'usuario_recibe' => $fila->usuario_recibe,
                    'estatus' => self::MAPA_ESTATUS[$fila->estatus] ?? 'en_stock',
                    'fecha_asignacion' => $fila->fecha_asignacion,
                    'fecha_renominacion' => $fila->fecha_renominacion,
                    'destino_sucursal_id' => $destinoSucursalId,
                    'otro_destino_descripcion' => $destinoSucursalId ? null : ($fila->otro_destino_descripcion ?: ($fila->destino_vinculado ?: null)),
                    'usuario_asigna' => $fila->usuario_asigna,
                    'numero_oficio' => $fila->numero_oficio_pdf,
                ],
            );

            $tarjetaIdPorLegacy[$fila->id_registro] = $tarjeta->id;
        }

        $historialCreados = 0;
        foreach (DB::connection('sios_legacy')->table('sios_tarjetas_historial')->orderBy('id')->get() as $fila) {
            $tarjetaId = $tarjetaIdPorLegacy[$fila->id_registro] ?? null;

            if (! $tarjetaId) {
                continue;
            }

            $existe = TarjetaHistorial::where('tarjeta_id', $tarjetaId)
                ->where('accion', $fila->accion)
                ->where('created_at', $fila->created_at)
                ->exists();

            if ($existe) {
                continue;
            }

            TarjetaHistorial::create([
                'tarjeta_id' => $tarjetaId,
                'accion' => $fila->accion,
                'estatus_anterior' => self::MAPA_ESTATUS[$fila->estatus_anterior] ?? Str::lower($fila->estatus_anterior),
                'estatus_nuevo' => self::MAPA_ESTATUS[$fila->estatus_nuevo] ?? Str::lower($fila->estatus_nuevo),
                'motivo' => $fila->motivo,
                'usuario' => $fila->usuario,
            ]);
            $historialCreados++;
        }

        $this->command?->info(sprintf(
            '%d producto(s), %d tarjetas y %d movimientos de historial migrados desde sios_app_web.',
            count($productoIdPorLegacy),
            count($tarjetaIdPorLegacy),
            $historialCreados,
        ));
    }
}
