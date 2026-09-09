<?php

namespace Database\Factories;

use App\Models\Conductor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Conductor>
 */
class ConductorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre_completo' => $this->faker->name(),
            'numero_empleado' => $this->faker->unique()->numerify('#####'),
            'area_adscripcion' => $this->faker->randomElement(['Coordinación de Operación', 'Coordinación Comercial']),
            'numero_licencia' => $this->faker->unique()->numerify('########'),
            'tipo_licencia' => 'A2',
            'es_permanente' => true,
            'vigencia_licencia' => $this->faker->dateTimeBetween('now', '+2 years'),
            'estatus' => 'activo',
        ];
    }
}
