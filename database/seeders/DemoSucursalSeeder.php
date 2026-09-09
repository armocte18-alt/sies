<?php

namespace Database\Seeders;

use App\Models\Alcaldia;
use App\Models\Empleado;
use App\Models\Sucursal;
use Illuminate\Database\Seeder;

/**
 * Datos de ejemplo para desarrollo local. No se ejecuta en producción
 * (ver DatabaseSeeder::run).
 */
class DemoSucursalSeeder extends Seeder
{
    public function run(): void
    {
        $sucursal = Sucursal::firstOrCreate(
            ['clave_financiera' => '09003'],
            [
                'nombre_oficial' => 'San Miguel Ajusco',
                'centro_distribucion' => '50003',
                'estatus_operativo' => 'activa',
            ],
        );

        $jefa = Empleado::firstOrCreate(
            ['no_empleado' => '27439'],
            [
                'sucursal_id' => $sucursal->id,
                'nombre' => 'Evelin',
                'apellido_paterno' => 'Gonzalez',
                'apellido_materno' => 'Reyes',
                'funcion_laboral' => 'Jefe de Sucursal',
            ],
        );

        Empleado::firstOrCreate(
            ['no_empleado' => '23697'],
            [
                'sucursal_id' => $sucursal->id,
                'nombre' => 'Olga Lydia',
                'apellido_paterno' => 'Navarro',
                'apellido_materno' => 'Gallo',
                'funcion_laboral' => 'Operativas',
            ],
        );

        $sucursal->update(['titular_empleado_id' => $jefa->id]);

        $tlalpan = Alcaldia::where('nombre', 'Tlalpan')->first();

        if ($tlalpan) {
            $sucursal->ubicacion()->updateOrCreate([], [
                'calle' => 'Av. Hidalgo Oriente',
                'colonia' => 'Pueblo San Miguel Ajusco',
                'alcaldia_id' => $tlalpan->id,
                'codigo_postal' => '14700',
                'entre_calle_1' => 'Cda. Mariano Escobedo',
                'entre_calle_2' => 'Abasolo',
                'referencia_visual' => 'En la parte inferior del kiosko',
                'latitud' => 19.225441,
                'longitud' => -99.200699,
                'clave_geografica_inegi' => '90120026',
            ]);
        }

        $sucursal->operacion()->updateOrCreate([], [
            'dias_laborables' => 'Lunes a Viernes',
            'hora_apertura_publico' => '08:00',
            'hora_cierre_publico' => '15:30',
            'hora_inicio_labores_interno' => '08:00',
            'hora_fin_labores_interno' => '16:00',
            'tipo_poblacion' => 'urbana_alta_densidad',
            'tipo_inmueble' => 'propio',
            'comunicacion' => 'Red Local / Enlace Dedicado (TELMEX)',
            'telefono' => '55-1315-2018',
            'reparto_activo' => false,
            'enrutamiento' => 'MESIMJ',
            'dias_guardia' => 'Sábado',
            'apertura_guardia' => '09:00',
            'cierre_guardia' => '13:00',
        ]);

        $sucursal->finanzas()->updateOrCreate([], [
            'limite_existencia_caja' => 100000,
            'volumen_total' => 18527,
            'cantidad_situada' => 42567222.26,
            'ingreso_estimado' => 280732.64,
            'gasto_total' => 359950.28,
        ]);
    }
}
