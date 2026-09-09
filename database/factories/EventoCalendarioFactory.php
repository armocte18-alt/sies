<?php

namespace Database\Factories;

use App\Models\EventoCalendario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventoCalendario>
 */
class EventoCalendarioFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->sentence(3),
            'fecha_inicio' => $this->faker->dateTimeBetween('now', '+2 months')->format('Y-m-d'),
            'color' => $this->faker->randomElement(['#135c46', '#c2841e', '#9a1750']),
        ];
    }
}
