<?php

namespace Tests\Feature;

use App\Models\Sucursal;
use App\Models\TarjetaInventario;
use App\Models\TarjetaProducto;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TarjetasTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_user_without_permission_cannot_view_tarjetas(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('tarjetas.index'))->assertForbidden();
    }

    public function test_finanzas_can_view_the_inventory(): void
    {
        $producto = TarjetaProducto::factory()->create(['nombre' => 'FINABIEN']);
        TarjetaInventario::factory()->create(['producto_id' => $producto->id, 'cuenta' => '1112223334']);

        $user = User::factory()->create();
        $user->assignRole('finanzas');

        $this->actingAs($user)
            ->get(route('tarjetas.index'))
            ->assertOk()
            ->assertSee('1112223334');
    }

    public function test_supervision_cannot_manage_tarjetas(): void
    {
        $user = User::factory()->create();
        $user->assignRole('supervision');

        $this->actingAs($user)
            ->post(route('tarjetas.asignar'), ['producto_id' => 1, 'cantidad' => 1])
            ->assertForbidden();
    }

    public function test_asignar_moves_the_oldest_stock_cards_to_activa_with_a_shared_folio(): void
    {
        $producto = TarjetaProducto::factory()->create();
        $sucursal = Sucursal::factory()->create();
        $tarjetas = TarjetaInventario::factory()->count(3)->create(['producto_id' => $producto->id]);

        $user = User::factory()->create();
        $user->assignRole('finanzas');

        $this->actingAs($user)->post(route('tarjetas.asignar'), [
            'producto_id' => $producto->id,
            'cantidad' => 2,
            'destino_sucursal_id' => $sucursal->id,
        ])->assertRedirect();

        $asignadas = TarjetaInventario::where('estatus', 'activa')->get();
        $this->assertCount(2, $asignadas);
        $this->assertSame(1, $asignadas->pluck('numero_oficio')->unique()->count());
        $this->assertTrue($tarjetas->first()->fresh()->estatus === 'activa');
    }

    public function test_asignar_fails_when_stock_is_insufficient(): void
    {
        $producto = TarjetaProducto::factory()->create();
        $sucursal = Sucursal::factory()->create();
        TarjetaInventario::factory()->create(['producto_id' => $producto->id]);

        $user = User::factory()->create();
        $user->assignRole('finanzas');

        $this->actingAs($user)
            ->from(route('tarjetas.index'))
            ->post(route('tarjetas.asignar'), [
                'producto_id' => $producto->id,
                'cantidad' => 5,
                'destino_sucursal_id' => $sucursal->id,
            ])
            ->assertSessionHasErrors('cantidad');

        $this->assertDatabaseHas('tarjetas_inventario', ['estatus' => 'en_stock']);
    }

    public function test_retirar_returns_an_active_card_to_stock_and_logs_history(): void
    {
        $tarjeta = TarjetaInventario::factory()->create(['estatus' => 'activa']);
        $user = User::factory()->create();
        $user->assignRole('finanzas');

        $this->actingAs($user)->patch(route('tarjetas.retirar', $tarjeta), [
            'motivo' => 'Sucursal cerrada temporalmente.',
        ])->assertRedirect();

        $this->assertSame('en_stock', $tarjeta->fresh()->estatus);
        $this->assertDatabaseHas('tarjetas_historial', [
            'tarjeta_id' => $tarjeta->id,
            'accion' => 'retiro_individual',
        ]);
    }

    public function test_renominar_and_correction_round_trip(): void
    {
        $tarjeta = TarjetaInventario::factory()->create(['estatus' => 'activa']);
        $user = User::factory()->create();
        $user->assignRole('finanzas');

        $this->actingAs($user)->patch(route('tarjetas.renominar', $tarjeta))->assertRedirect();
        $this->assertSame('renominada', $tarjeta->fresh()->estatus);

        $this->actingAs($user)->patch(route('tarjetas.corregir-renominacion', $tarjeta), [
            'motivo' => 'Se renominó por error, corrigiendo.',
        ])->assertRedirect();
        $this->assertSame('activa', $tarjeta->fresh()->estatus);
    }

    public function test_retirar_por_sucursal_returns_all_its_active_cards(): void
    {
        $sucursal = Sucursal::factory()->create();
        $otraSucursal = Sucursal::factory()->create();
        TarjetaInventario::factory()->count(2)->create(['estatus' => 'activa', 'destino_sucursal_id' => $sucursal->id]);
        TarjetaInventario::factory()->create(['estatus' => 'activa', 'destino_sucursal_id' => $otraSucursal->id]);

        $user = User::factory()->create();
        $user->assignRole('finanzas');

        $this->actingAs($user)->post(route('tarjetas.retirar-por-sucursal'), [
            'destino_sucursal_id' => $sucursal->id,
        ])->assertRedirect();

        $this->assertSame(0, TarjetaInventario::where('destino_sucursal_id', $sucursal->id)->where('estatus', 'activa')->count());
        $this->assertSame(1, TarjetaInventario::where('destino_sucursal_id', $otraSucursal->id)->where('estatus', 'activa')->count());
    }
}
