<?php

namespace Tests\Feature;

use App\Models\IndicadorSucursalMensual;
use App\Models\MetaMensual;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MetasAnalisisTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_user_without_permission_cannot_view_metas(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('metas.index'))->assertForbidden();
    }

    public function test_comercial_can_view_metas_and_defaults_to_the_latest_available_month(): void
    {
        MetaMensual::create([
            'linea_negocio' => 'Cobranza', 'anio' => 2026, 'tipo' => 'core', 'meta_anual' => 592231.16,
        ]);

        IndicadorSucursalMensual::create([
            'clave_sucursal_legacy' => '09003', 'nombre_sucursal_legacy' => 'Mayo (mas reciente)',
            'mes' => 5, 'anio' => 2026, 'volumen_total' => 163, 'ingresos_total' => 2899.99, 'gasto_total' => 24504.81, 'balance' => -21604.82,
        ]);
        IndicadorSucursalMensual::create([
            'clave_sucursal_legacy' => '09004', 'nombre_sucursal_legacy' => 'Abril (anterior)',
            'mes' => 4, 'anio' => 2026, 'volumen_total' => 100, 'ingresos_total' => 1000, 'gasto_total' => 500, 'balance' => 500,
        ]);

        $user = User::factory()->create();
        $user->assignRole('comercial');

        $this->actingAs($user)
            ->get(route('metas.index'))
            ->assertOk()
            ->assertSee('Cobranza')
            // El mes más reciente disponible (5) debe ser el que se muestra por defecto.
            ->assertSee('Mayo (Mas Reciente)')
            ->assertDontSee('Abril (Anterior)');
    }

    public function test_filters_indicadores_by_selected_month(): void
    {
        IndicadorSucursalMensual::create([
            'clave_sucursal_legacy' => '09003', 'nombre_sucursal_legacy' => 'Sucursal De Abril',
            'mes' => 4, 'anio' => 2026, 'volumen_total' => 10,
        ]);
        IndicadorSucursalMensual::create([
            'clave_sucursal_legacy' => '09004', 'nombre_sucursal_legacy' => 'Sucursal De Mayo',
            'mes' => 5, 'anio' => 2026, 'volumen_total' => 20,
        ]);

        $user = User::factory()->create();
        $user->assignRole('comercial');

        $this->actingAs($user)
            ->get(route('metas.index', ['anio' => 2026, 'mes' => 4]))
            ->assertOk()
            ->assertSee('Sucursal De Abril')
            ->assertDontSee('Sucursal De Mayo');
    }

    public function test_can_search_indicadores_by_sucursal_name(): void
    {
        IndicadorSucursalMensual::create([
            'clave_sucursal_legacy' => '09003', 'nombre_sucursal_legacy' => 'Izazaga 151',
            'mes' => 5, 'anio' => 2026,
        ]);
        IndicadorSucursalMensual::create([
            'clave_sucursal_legacy' => '09004', 'nombre_sucursal_legacy' => 'Tlalpan',
            'mes' => 5, 'anio' => 2026,
        ]);

        $user = User::factory()->create();
        $user->assignRole('comercial');

        $this->actingAs($user)
            ->get(route('metas.index', ['anio' => 2026, 'mes' => 5, 'buscar' => 'Izazaga']))
            ->assertOk()
            ->assertSee('Izazaga 151')
            ->assertDontSee('Tlalpan');
    }
}
