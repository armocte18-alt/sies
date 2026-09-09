<?php

namespace Database\Factories;

use App\Models\TarjetaInventario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TarjetaInventario>
 */
class TarjetaInventarioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'producto_id' => \App\Models\TarjetaProducto::factory(),
            'numero_tarjeta' => $this->faker->numerify('####'),
            'cuenta' => $this->faker->unique()->numerify('##########'),
            'fecha_recepcion' => now()->format('Y-m-d'),
            'estatus' => 'en_stock',
        ];
    }
}
