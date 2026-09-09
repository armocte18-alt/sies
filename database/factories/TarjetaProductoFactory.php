<?php

namespace Database\Factories;

use App\Models\TarjetaProducto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TarjetaProducto>
 */
class TarjetaProductoFactory extends Factory
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
            'slug' => \Illuminate\Support\Str::slug($nombre),
        ];
    }
}
