<?php

namespace Database\Factories;

use App\Models\Sucursal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sucursal>
 */
class SucursalFactory extends Factory
{
    protected $model = Sucursal::class;

    public function definition(): array
    {
        return [
            'nombre_oficial' => fake()->company(),
            'clave_financiera' => fake()->unique()->numerify('#####'),
            'centro_distribucion' => fake()->numerify('#####'),
            'estatus_operativo' => 'activa',
        ];
    }
}
