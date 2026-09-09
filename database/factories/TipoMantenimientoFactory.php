<?php

namespace Database\Factories;

use App\Models\TipoMantenimiento;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TipoMantenimiento>
 */
class TipoMantenimientoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nombre = $this->faker->unique()->word();

        return [
            'nombre' => $nombre,
            'slug' => Str::slug($nombre, '_'),
            'color' => '#285c4d',
            'activo' => true,
            'orden' => $this->faker->numberBetween(1, 10),
        ];
    }
}
