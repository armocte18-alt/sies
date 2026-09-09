<?php

namespace Database\Factories;

use App\Models\Mantenimiento;
use App\Models\TipoMantenimiento;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mantenimiento>
 */
class MantenimientoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $inicio = $this->faker->dateTimeBetween('-1 week', '+1 week');

        return [
            'tipo_mantenimiento_id' => TipoMantenimiento::factory(),
            'titulo' => $this->faker->sentence(3),
            'prioridad' => 'media',
            'estatus' => 'pendiente',
            'fecha_programada_inicio' => $inicio,
            'fecha_programada_fin' => (clone $inicio)->modify('+2 days'),
            'costo_mano_obra' => 0,
            'costo_materiales' => 0,
        ];
    }
}
