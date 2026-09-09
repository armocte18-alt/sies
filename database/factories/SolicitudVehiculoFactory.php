<?php

namespace Database\Factories;

use App\Models\SolicitudVehiculo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SolicitudVehiculo>
 */
class SolicitudVehiculoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $desde = $this->faker->dateTimeBetween('+1 day', '+2 days');

        return [
            'solicitante_id' => User::factory(),
            'numero_empleado' => $this->faker->numerify('#####'),
            'area' => 'gerencia_estatal_cdmx',
            'fecha_salida_desde' => $desde,
            'fecha_salida_hasta' => (clone $desde)->modify('+4 hours'),
            'destinos' => [['lugar' => $this->faker->city(), 'orden' => 1]],
            'motivo' => $this->faker->sentence(),
            'estatus' => 'pendiente',
        ];
    }
}
