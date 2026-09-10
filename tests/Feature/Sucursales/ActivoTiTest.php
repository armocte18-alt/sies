<?php

namespace Tests\Feature\Sucursales;

use App\Models\ActivoTi;
use App\Models\Sucursal;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivoTiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_tecnica_can_register_and_remove_an_activo(): void
    {
        $sucursal = Sucursal::factory()->create();
        $tecnica = User::factory()->create();
        $tecnica->assignRole('tecnica');

        $this->actingAs($tecnica)->post(route('sucursales.activos-ti.store', $sucursal), [
            'tipo' => 'computadora',
            'marca' => 'HP',
            'modelo' => 'ProDesk 400',
            'numero_serie' => 'ABC123',
            'etiqueta_inventario' => 'INV-001',
            'estado' => 'operativo',
        ])->assertRedirect();

        $activo = ActivoTi::where('sucursal_id', $sucursal->id)->firstOrFail();
        $this->assertSame('INV-001', $activo->etiqueta_inventario);
        $this->assertSame('ABC123', $activo->numero_serie);

        $this->actingAs($tecnica)
            ->delete(route('sucursales.activos-ti.destroy', [$sucursal, $activo]))
            ->assertRedirect();

        $this->assertDatabaseMissing('activos_ti', ['id' => $activo->id]);
    }

    public function test_finanzas_role_cannot_register_an_activo(): void
    {
        $sucursal = Sucursal::factory()->create();
        $finanzas = User::factory()->create();
        $finanzas->assignRole('finanzas');

        $this->actingAs($finanzas)
            ->post(route('sucursales.activos-ti.store', $sucursal), [
                'tipo' => 'computadora',
                'estado' => 'operativo',
            ])
            ->assertForbidden();
    }

    public function test_tecnica_can_register_security_equipment_types(): void
    {
        $sucursal = Sucursal::factory()->create();
        $tecnica = User::factory()->create();
        $tecnica->assignRole('tecnica');

        foreach (['camara', 'panel_alarma', 'sensor_movimiento'] as $tipo) {
            $this->actingAs($tecnica)->post(route('sucursales.activos-ti.store', $sucursal), [
                'tipo' => $tipo,
                'marca' => 'Hikvision',
                'modelo' => 'X-100',
                'numero_serie' => 'SN-'.$tipo,
                'etiqueta_inventario' => 'INV-'.$tipo,
                'estado' => 'operativo',
            ])->assertRedirect();
        }

        $this->assertSame(3, ActivoTi::where('sucursal_id', $sucursal->id)->count());
        $this->assertDatabaseHas('activos_ti', ['sucursal_id' => $sucursal->id, 'tipo' => 'panel_alarma', 'numero_serie' => 'SN-panel_alarma']);
        $this->assertDatabaseHas('activos_ti', ['sucursal_id' => $sucursal->id, 'tipo' => 'sensor_movimiento', 'numero_serie' => 'SN-sensor_movimiento']);
    }

    public function test_cannot_delete_an_activo_belonging_to_another_sucursal(): void
    {
        $sucursalA = Sucursal::factory()->create();
        $sucursalB = Sucursal::factory()->create();
        $activo = ActivoTi::create([
            'sucursal_id' => $sucursalA->id,
            'tipo' => 'computadora',
            'estado' => 'operativo',
        ]);

        $tecnica = User::factory()->create();
        $tecnica->assignRole('tecnica');

        $this->actingAs($tecnica)
            ->delete(route('sucursales.activos-ti.destroy', [$sucursalB, $activo]))
            ->assertNotFound();

        $this->assertDatabaseHas('activos_ti', ['id' => $activo->id]);
    }
}
