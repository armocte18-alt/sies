<?php

namespace Tests\Feature\Sucursales;

use App\Models\Sucursal;
use App\Models\SucursalEquipamientoReparto;
use App\Models\SucursalMotocicleta;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RepartoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function sucursalConReparto(bool $repartoActivo = true): Sucursal
    {
        $sucursal = Sucursal::factory()->create();
        $sucursal->operacion()->create(['reparto_activo' => $repartoActivo]);

        return $sucursal;
    }

    public function test_reparto_tab_only_shows_when_reparto_is_active(): void
    {
        $conReparto = $this->sucursalConReparto(true);
        $sinReparto = $this->sucursalConReparto(false);
        $user = User::factory()->create();
        $user->assignRole('operacion');

        $this->actingAs($user)
            ->get(route('sucursales.show', $conReparto))
            ->assertOk()
            ->assertSee('id="tab-reparto"', false);

        $this->actingAs($user)
            ->get(route('sucursales.show', $sinReparto))
            ->assertOk()
            ->assertDontSee('id="tab-reparto"', false);
    }

    public function test_operacion_can_register_a_motorcycle_and_a_fuel_load(): void
    {
        $sucursal = $this->sucursalConReparto();
        $user = User::factory()->create();
        $user->assignRole('operacion');

        $this->actingAs($user)->post(route('sucursales.motocicletas.store', $sucursal), [
            'placa' => 'ABC-123',
            'estado' => 'operativa',
            'kilometraje_actual' => 5000,
        ])->assertRedirect();

        $moto = SucursalMotocicleta::where('sucursal_id', $sucursal->id)->firstOrFail();
        $this->assertSame(5000, $moto->kilometraje_actual);

        $this->actingAs($user)->post(route('sucursales.motocicletas.combustible.store', [$sucursal, $moto]), [
            'fecha' => now()->format('Y-m-d'),
            'litros' => 8.5,
            'monto' => 250,
            'kilometraje' => 5120,
        ])->assertRedirect();

        $this->assertDatabaseHas('sucursal_motocicleta_combustibles', [
            'motocicleta_id' => $moto->id,
            'litros' => '8.50',
        ]);

        // El kilometraje del vehículo se actualiza si la carga trae uno mayor.
        $this->assertSame(5120, $moto->fresh()->kilometraje_actual);
    }

    public function test_administracion_can_manage_reparto(): void
    {
        $sucursal = $this->sucursalConReparto();
        $user = User::factory()->create();
        $user->assignRole('administracion');

        $this->actingAs($user)->post(route('sucursales.motocicletas.store', $sucursal), [
            'placa' => 'XYZ-987',
            'estado' => 'operativa',
            'kilometraje_actual' => 1200,
        ])->assertRedirect();

        $this->assertDatabaseHas('sucursal_motocicletas', [
            'sucursal_id' => $sucursal->id,
            'placa' => 'XYZ-987',
        ]);
    }

    public function test_finanzas_role_cannot_manage_reparto(): void
    {
        $sucursal = $this->sucursalConReparto();
        $finanzas = User::factory()->create();
        $finanzas->assignRole('finanzas');

        $this->actingAs($finanzas)
            ->post(route('sucursales.motocicletas.store', $sucursal), [
                'estado' => 'operativa',
                'kilometraje_actual' => 0,
            ])
            ->assertForbidden();
    }

    public function test_can_register_epp_inventory_and_rejects_duplicate_tipo_talla(): void
    {
        $sucursal = $this->sucursalConReparto();
        $user = User::factory()->create();
        $user->assignRole('operacion');

        $this->actingAs($user)->post(route('sucursales.equipamiento-reparto.store', $sucursal), [
            'tipo' => 'Chamarra',
            'talla' => 'M',
            'cantidad_total' => 10,
            'cantidad_buen_estado' => 8,
            'cantidad_danado' => 2,
        ])->assertRedirect();

        $epp = SucursalEquipamientoReparto::where('sucursal_id', $sucursal->id)->firstOrFail();
        $this->assertSame(10, $epp->cantidad_total);

        $this->actingAs($user)
            ->from(route('sucursales.show', $sucursal))
            ->post(route('sucursales.equipamiento-reparto.store', $sucursal), [
                'tipo' => 'chamarra',
                'talla' => 'm',
                'cantidad_total' => 5,
                'cantidad_buen_estado' => 5,
                'cantidad_danado' => 0,
            ])
            ->assertSessionHasErrors('tipo');

        $this->assertSame(1, SucursalEquipamientoReparto::where('sucursal_id', $sucursal->id)->count());
    }

    public function test_buen_estado_cannot_exceed_cantidad_total(): void
    {
        $sucursal = $this->sucursalConReparto();
        $user = User::factory()->create();
        $user->assignRole('operacion');

        $this->actingAs($user)
            ->from(route('sucursales.show', $sucursal))
            ->post(route('sucursales.equipamiento-reparto.store', $sucursal), [
                'tipo' => 'casco',
                'cantidad_total' => 3,
                'cantidad_buen_estado' => 5,
                'cantidad_danado' => 0,
            ])
            ->assertSessionHasErrors('cantidad_buen_estado');
    }
}
