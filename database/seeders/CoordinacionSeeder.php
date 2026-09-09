<?php

namespace Database\Seeders;

use App\Models\Coordinacion;
use Illuminate\Database\Seeder;

class CoordinacionSeeder extends Seeder
{
    public function run(): void
    {
        $coordinaciones = [
            ['clave' => 'supervision', 'nombre' => 'Supervisión'],
            ['clave' => 'operacion', 'nombre' => 'Operación'],
            ['clave' => 'creditos', 'nombre' => 'Créditos'],
            ['clave' => 'juridico', 'nombre' => 'Jurídico'],
            ['clave' => 'finanzas', 'nombre' => 'Finanzas'],
            ['clave' => 'administracion', 'nombre' => 'Administración'],
            ['clave' => 'tecnica', 'nombre' => 'Técnica'],
            ['clave' => 'rrhh', 'nombre' => 'Recursos Humanos'],
            ['clave' => 'comercial', 'nombre' => 'Comercial'],
        ];

        foreach ($coordinaciones as $coordinacion) {
            Coordinacion::firstOrCreate(['clave' => $coordinacion['clave']], $coordinacion);
        }
    }
}
