<?php

namespace Database\Factories;

use App\Models\MinutarioOficio;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MinutarioOficio>
 */
class MinutarioOficioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $consecutivo = 0;
        $consecutivo++;

        return [
            'consecutivo' => $consecutivo,
            'anio' => 2026,
            'nomenclatura' => sprintf('4120.-%04d/2026', $consecutivo),
            'asunto' => $this->faker->sentence(6),
            'dirigido_a' => $this->faker->name(),
            'fecha_emision' => now()->format('Y-m-d'),
        ];
    }
}
