<?php

namespace Database\Factories;

use App\Models\Mantenimiento;
use App\Models\MantenimientoMaterial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MantenimientoMaterial>
 */
class MantenimientoMaterialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mantenimiento_id' => Mantenimiento::factory(),
            'nombre_material' => $this->faker->word(),
            'unidad' => 'pza',
            'cantidad' => 1,
            'costo_unitario' => $this->faker->randomFloat(2, 10, 500),
            'costo_total' => 0,
        ];
    }
}
