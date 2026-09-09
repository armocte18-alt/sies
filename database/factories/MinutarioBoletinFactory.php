<?php

namespace Database\Factories;

use App\Models\MinutarioBoletin;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MinutarioBoletin>
 */
class MinutarioBoletinFactory extends Factory
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
            'nomenclatura' => sprintf('B-%04d/2026', $consecutivo),
            'asunto' => $this->faker->sentence(6),
            'contenido' => $this->faker->paragraph(),
            'fecha_elaboracion' => now()->format('Y-m-d'),
            'fecha_vigor' => now()->addDays(15)->format('Y-m-d'),
            'dirigido_tipo' => 'todos',
        ];
    }
}
