<?php

namespace Database\Factories;

use App\Models\Vehiculo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vehiculo>
 */
class VehiculoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'marca' => $this->faker->randomElement(['Nissan', 'Volkswagen', 'Chevrolet']),
            'modelo' => $this->faker->word(),
            'anio' => $this->faker->numberBetween(1990, 2026),
            'tipo' => 'propio',
            'placa' => $this->faker->unique()->bothify('???###'),
            'kilometraje_actual' => $this->faker->numberBetween(0, 100000),
            'combustible_inicial' => 100,
            'estatus' => 'disponible',
        ];
    }
}
