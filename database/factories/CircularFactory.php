<?php

namespace Database\Factories;

use App\Models\Circular;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Circular>
 */
class CircularFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'numero' => $this->faker->unique()->numerify('##/2026'),
            'asunto' => $this->faker->sentence(8),
            'fecha_aplicacion' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'ambito' => $this->faker->randomElement(['general', 'cobranza', 'giro_nal.', null]),
            'activo' => true,
        ];
    }
}
