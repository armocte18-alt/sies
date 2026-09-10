<?php

namespace Database\Factories;

use App\Models\TipoDocumentoAcervo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TipoDocumentoAcervo>
 */
class TipoDocumentoAcervoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nombre = $this->faker->unique()->words(3, true);

        return [
            'nombre' => $nombre,
            'slug' => Str::slug($nombre, '_'),
            'activo' => true,
            'orden' => $this->faker->numberBetween(1, 20),
        ];
    }
}
