<?php

namespace Database\Factories;

use App\Models\Conductor;
use App\Models\SolicitudVehiculo;
use App\Models\SolicitudVehiculoDetalle;
use App\Models\Vehiculo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SolicitudVehiculoDetalle>
 */
class SolicitudVehiculoDetalleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'solicitud_id' => SolicitudVehiculo::factory(),
            'vehiculo_id' => Vehiculo::factory(),
            'conductor_id' => Conductor::factory(),
            'km_inicial' => $this->faker->numberBetween(0, 1000),
        ];
    }
}
