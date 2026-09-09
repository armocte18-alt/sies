<?php

namespace Tests\Feature;

use App\Models\Conductor;
use App\Models\SolicitudVehiculo;
use App\Models\SolicitudVehiculoDetalle;
use App\Models\User;
use App\Models\Vehiculo;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehiculosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function usuarioConAcceso(): User
    {
        $user = User::factory()->create();
        $user->assignRole('administracion');

        return $user;
    }

    public function test_user_without_permission_cannot_view_vehiculos(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('vehiculos.index'))->assertForbidden();
    }

    public function test_supervision_cannot_manage_vehiculos(): void
    {
        $user = User::factory()->create();
        $user->assignRole('supervision');

        $this->actingAs($user)
            ->post(route('vehiculos.store'), ['marca' => 'Nissan'])
            ->assertForbidden();
    }

    public function test_administracion_can_view_the_catalog(): void
    {
        $vehiculo = Vehiculo::factory()->create(['placa' => 'abc123']);

        $this->actingAs($this->usuarioConAcceso())
            ->get(route('vehiculos.index'))
            ->assertOk()
            ->assertSee('abc123');
    }

    public function test_store_solicitud_creates_a_pending_request_with_detail(): void
    {
        $vehiculo = Vehiculo::factory()->create(['estatus' => 'disponible']);
        $conductor = Conductor::factory()->create(['estatus' => 'activo']);
        $user = $this->usuarioConAcceso();

        $this->actingAs($user)->post(route('vehiculos.solicitudes.store'), [
            'area' => 'gerencia_estatal_cdmx',
            'fecha_salida_desde' => now()->addDay()->format('Y-m-d\TH:i'),
            'fecha_salida_hasta' => now()->addDay()->addHours(3)->format('Y-m-d\TH:i'),
            'destino_lugar' => 'Sucursal Milpa Alta',
            'motivo' => 'Comisión de supervisión',
            'vehiculo_id' => $vehiculo->id,
            'conductor_id' => $conductor->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('solicitudes_vehiculos', ['solicitante_id' => $user->id, 'estatus' => 'pendiente']);
        $solicitud = SolicitudVehiculo::first();
        $this->assertSame($vehiculo->id, $solicitud->detalle->vehiculo_id);
        $this->assertSame($conductor->id, $solicitud->detalle->conductor_id);
    }

    public function test_store_solicitud_fails_when_vehicle_has_an_overlapping_request(): void
    {
        $vehiculo = Vehiculo::factory()->create(['estatus' => 'disponible']);
        $conductor = Conductor::factory()->create(['estatus' => 'activo']);
        $otroConductor = Conductor::factory()->create(['estatus' => 'activo']);
        $user = $this->usuarioConAcceso();

        $existente = SolicitudVehiculo::factory()->create([
            'estatus' => 'autorizada',
            'fecha_salida_desde' => now()->addDay()->setTime(8, 0),
            'fecha_salida_hasta' => now()->addDay()->setTime(14, 0),
        ]);
        SolicitudVehiculoDetalle::factory()->create([
            'solicitud_id' => $existente->id,
            'vehiculo_id' => $vehiculo->id,
            'conductor_id' => $otroConductor->id,
        ]);

        $this->actingAs($user)
            ->from(route('vehiculos.index'))
            ->post(route('vehiculos.solicitudes.store'), [
                'area' => 'gerencia_estatal_cdmx',
                'fecha_salida_desde' => now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i'),
                'fecha_salida_hasta' => now()->addDay()->setTime(12, 0)->format('Y-m-d\TH:i'),
                'destino_lugar' => 'Sucursal Milpa Alta',
                'motivo' => 'Comisión de supervisión',
                'vehiculo_id' => $vehiculo->id,
                'conductor_id' => $conductor->id,
            ])
            ->assertSessionHasErrors('vehiculo_id');

        $this->assertSame(1, SolicitudVehiculo::count());
    }

    public function test_autorizar_assigns_the_vehicle_and_marks_the_request_authorized(): void
    {
        $vehiculo = Vehiculo::factory()->create(['estatus' => 'disponible']);
        $solicitud = SolicitudVehiculo::factory()->create(['estatus' => 'pendiente']);
        SolicitudVehiculoDetalle::factory()->create(['solicitud_id' => $solicitud->id, 'vehiculo_id' => $vehiculo->id]);

        $this->actingAs($this->usuarioConAcceso())
            ->patch(route('vehiculos.solicitudes.autorizar', $solicitud))
            ->assertRedirect();

        $this->assertSame('autorizada', $solicitud->fresh()->estatus);
        $this->assertSame('asignado', $vehiculo->fresh()->estatus);
    }

    public function test_rechazar_requires_a_reason_and_marks_the_request_rejected(): void
    {
        $solicitud = SolicitudVehiculo::factory()->create(['estatus' => 'pendiente']);

        $this->actingAs($this->usuarioConAcceso())
            ->patch(route('vehiculos.solicitudes.rechazar', $solicitud), [
                'motivo_rechazo' => 'El vehículo entrará a servicio esos días.',
            ])
            ->assertRedirect();

        $this->assertSame('rechazada', $solicitud->fresh()->estatus);
    }

    public function test_devolucion_finalizes_the_request_and_frees_the_vehicle(): void
    {
        $vehiculo = Vehiculo::factory()->create(['estatus' => 'asignado', 'kilometraje_actual' => 100]);
        $solicitud = SolicitudVehiculo::factory()->create(['estatus' => 'autorizada']);
        $detalle = SolicitudVehiculoDetalle::factory()->create([
            'solicitud_id' => $solicitud->id,
            'vehiculo_id' => $vehiculo->id,
            'km_inicial' => 100,
        ]);

        $this->actingAs($this->usuarioConAcceso())
            ->patch(route('vehiculos.solicitudes.devolucion', $solicitud), [
                'km_final' => 150,
                'combustible_recepcion' => 80,
                'checklist_gato' => 1,
                'checklist_llave_cruz' => 1,
                'checklist_reflejantes' => 1,
                'checklist_extintor' => 1,
            ])
            ->assertRedirect();

        $this->assertSame('finalizada', $solicitud->fresh()->estatus);
        $this->assertSame('disponible', $vehiculo->fresh()->estatus);
        $this->assertSame(150, $vehiculo->fresh()->kilometraje_actual);
        $this->assertSame(50, $detalle->fresh()->km_recorridos);
    }
}
