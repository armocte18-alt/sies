<?php

namespace Database\Seeders\Legacy;

use App\Models\Alcaldia;
use App\Models\Sucursal;
use Database\Seeders\Legacy\Concerns\FormateaClaveSucursal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Clona las 60 sucursales reales de la base "sios_app_web" (conexión de solo
 * lectura "sios_legacy") hacia el esquema normalizado de SIES: sucursales,
 * sucursal_ubicaciones, sucursal_operaciones y sucursal_inmuebles.
 *
 * Ejecutar manualmente:
 *   php artisan db:seed --class="Database\Seeders\Legacy\MigrarSucursalesSeeder"
 */
class MigrarSucursalesSeeder extends Seeder
{
    use FormateaClaveSucursal;

    private const ESTATUS = [
        1 => 'activa',          // Activa
        2 => 'suspendida',      // Cierre Temporal
        3 => 'inactiva',        // Cierre Definitivo
        4 => 'en_apertura',     // Próxima apertura
    ];

    private const TIPO_INMUEBLE = [
        1 => 'propio',      // Inmueble Propio
        2 => 'arrendado',   // Inmueble Arrendado
        3 => 'comodato',    // Comodato
        4 => 'otro',        // Prestado / Donado
    ];

    private const TIPO_POBLACION = [
        1 => 'urbana_alta_densidad', // Urbana de Alta Densidad
        2 => 'urbana_media_densidad', // Semiurbana
        3 => 'rural',                 // Rural / Vulnerable
    ];

    /**
     * Str::title() no reconoce siglas; corrige los casos conocidos entre
     * los 60 nombres reales de sucursales (p. ej. "Xxi" -> "XXI").
     */
    private const CORRECCIONES_NOMBRE = [
        'Xxi' => 'XXI',
        'Ii' => 'II',
        'Cdmx' => 'CDMX',
        'Imss' => 'IMSS',
        'Scop' => 'SCOP',
        'C.u.p.' => 'C.U.P.',
        'C.u.' => 'C.U.',
    ];

    public function run(): void
    {
        $comunicacionCatalogo = DB::connection('sios_legacy')->table('sucursales_tipo_comunicacion')->pluck('nombre_comunicacion_tipo', 'id_comunicacion_tipo');
        $alcaldiasLegacy = DB::connection('sios_legacy')->table('alcaldias')->pluck('nombre_alcaldia', 'id_alcaldia');
        $alcaldiasSies = Alcaldia::all()->keyBy(fn ($a) => $this->normaliza($a->nombre));

        $filas = DB::connection('sios_legacy')->table('sucursales')->orderBy('id_sucursal')->get();

        $sinAlcaldia = 0;

        foreach ($filas as $fila) {
            $clave = $this->claveSucursal($fila->registro_sucursal);

            $sucursal = Sucursal::updateOrCreate(
                ['clave_financiera' => $clave],
                [
                    'nombre_oficial' => $this->nombreOficial($fila->nombre_sucursal),
                    'centro_distribucion' => $fila->centro_distribucion_sucursal ?: null,
                    'estatus_operativo' => self::ESTATUS[$fila->estatus_sucursal] ?? 'activa',
                ],
            );

            $sucursal->operacion()->updateOrCreate([], [
                'dias_laborables' => $fila->dias_laborables_sucursal ?: null,
                'hora_apertura_publico' => $fila->hora_apertura_sucursal,
                'hora_cierre_publico' => $fila->hora_cierre_sucursal,
                'hora_inicio_labores_interno' => $fila->hora_inicio_sucursal,
                'hora_fin_labores_interno' => $fila->hora_fin_sucursal,
                'tipo_poblacion' => self::TIPO_POBLACION[$fila->poblacion_tipo_sucursal] ?? null,
                'tipo_inmueble' => self::TIPO_INMUEBLE[$fila->inmueble_tipo_sucursal] ?? null,
                'comunicacion' => $comunicacionCatalogo[$fila->comunicacion_tipo_sucursal] ?? null,
                'telefono' => $fila->telefono_sucursal ?: null,
                'reparto_activo' => (bool) $fila->reparto_sucursal,
                'enrutamiento' => $fila->enrutamiento_sucursal ?: null,
                'dias_guardia' => $fila->dias_guardia_sucursal ?: null,
                'apertura_guardia' => $fila->hora_apertura_guardia_sucursal,
                'cierre_guardia' => $fila->hora_cierre_guardia_sucursal,
            ]);

            $sucursal->inmueble()->updateOrCreate([], [
                'tipo_contrato_posesion' => self::TIPO_INMUEBLE[$fila->inmueble_tipo_sucursal] ?? null,
            ]);

            $nombreAlcaldia = $alcaldiasLegacy[$fila->alcaldia_sucursal] ?? null;
            $alcaldia = $nombreAlcaldia ? ($alcaldiasSies[$this->normaliza($nombreAlcaldia)] ?? null) : null;

            if ($alcaldia) {
                $sucursal->ubicacion()->updateOrCreate([], [
                    'calle' => $fila->calle_sucursal,
                    'num_ext' => $fila->num_ext_sucursal ?: null,
                    'num_int' => $fila->num_int_sucursal ?: null,
                    'colonia' => $fila->colonia_sucursal,
                    'alcaldia_id' => $alcaldia->id,
                    'codigo_postal' => str_pad((string) $fila->codigoPostal_sucursal, 5, '0', STR_PAD_LEFT),
                    'entre_calle_1' => $fila->entreCalle_uno_sucursal ?: null,
                    'entre_calle_2' => $fila->entreCalle_dos_sucursal ?: null,
                    'referencia_visual' => $fila->referencia_sucursal ?: null,
                    'latitud' => is_numeric($fila->latitud_sucursal) ? $fila->latitud_sucursal : null,
                    'longitud' => is_numeric($fila->longitud_sucursal) ? $fila->longitud_sucursal : null,
                    'clave_geografica_inegi' => $fila->inegi_sucursal ?: null,
                ]);
            } else {
                $sinAlcaldia++;
            }
        }

        $this->command?->info(sprintf(
            '%d sucursales migradas desde sios_app_web (%d sin alcaldía reconocida).',
            $filas->count(),
            $sinAlcaldia,
        ));
    }

    private function normaliza(string $texto): string
    {
        return Str::of($texto)->ascii()->lower()->trim()->value();
    }

    private function nombreOficial(string $nombreLegacy): string
    {
        return strtr(Str::title($nombreLegacy), self::CORRECCIONES_NOMBRE);
    }
}
