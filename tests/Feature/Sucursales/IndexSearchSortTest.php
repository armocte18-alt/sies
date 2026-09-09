<?php

namespace Tests\Feature\Sucursales;

use App\Models\Alcaldia;
use App\Models\Empleado;
use App\Models\Sucursal;
use App\Models\SucursalOperacion;
use App\Models\SucursalUbicacion;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexSearchSortTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_buscar_filters_by_nombre_or_clave(): void
    {
        Sucursal::factory()->create(['nombre_oficial' => 'Tlalpan Centro', 'clave_financiera' => '09081']);
        Sucursal::factory()->create(['nombre_oficial' => 'Xochimilco', 'clave_financiera' => '09090']);

        $user = User::factory()->create();
        $user->assignRole('supervision');

        $response = $this->actingAs($user)->get(route('sucursales.index', ['buscar' => 'tlalpan']));

        $response->assertOk()->assertSee('Tlalpan Centro')->assertDontSee('Xochimilco');
    }

    public function test_ajax_request_returns_only_the_results_partial(): void
    {
        Sucursal::factory()->create(['nombre_oficial' => 'Tlalpan Centro', 'clave_financiera' => '09081']);

        $user = User::factory()->create();
        $user->assignRole('supervision');

        $response = $this->actingAs($user)->get(
            route('sucursales.index'),
            ['X-Requested-With' => 'XMLHttpRequest'],
        );

        $response->assertOk()->assertSee('Tlalpan Centro');
        $response->assertDontSee('SIES | GECDMX');
    }

    public function test_buscar_matches_alcaldia_domicilio_horario_and_titular(): void
    {
        $conEsto = Sucursal::factory()->create(['nombre_oficial' => 'Con Esto', 'clave_financiera' => '09200']);
        $sinEsto = Sucursal::factory()->create(['nombre_oficial' => 'Sin Esto', 'clave_financiera' => '09201']);

        // Minúsculas para no depender de que SQLite (motor de pruebas) pliegue
        // mayúsculas acentuadas igual que MySQL (producción) al comparar LIKE.
        $alcaldia = Alcaldia::create(['nombre' => 'alcaldía única de prueba']);
        SucursalUbicacion::create([
            'sucursal_id' => $conEsto->id,
            'calle' => 'calle domicilio unico de prueba',
            'colonia' => 'colonia cualquiera',
            'alcaldia_id' => $alcaldia->id,
            'codigo_postal' => '00000',
        ]);
        SucursalOperacion::create([
            'sucursal_id' => $conEsto->id,
            'dias_laborables' => 'horario unico de prueba',
        ]);
        $titular = Empleado::factory()->create(['nombre' => 'Titularunico', 'apellido_paterno' => 'DePrueba']);
        $conEsto->update(['titular_empleado_id' => $titular->id]);

        $user = User::factory()->create();
        $user->assignRole('supervision');

        foreach (['alcaldía única de prueba', 'domicilio unico', 'horario unico de prueba', 'titularunico'] as $termino) {
            $this->actingAs($user)
                ->get(route('sucursales.index', ['buscar' => $termino]))
                ->assertOk()
                ->assertSee('Con Esto')
                ->assertDontSee('Sin Esto');
        }
    }

    public function test_buscar_matches_a_full_name_split_across_apellido_paterno_and_materno(): void
    {
        $conTitular = Sucursal::factory()->create(['nombre_oficial' => 'Con Titular', 'clave_financiera' => '09210']);
        $sinTitular = Sucursal::factory()->create(['nombre_oficial' => 'Sin Titular', 'clave_financiera' => '09211']);

        $titular = Empleado::factory()->create(['nombre' => 'Evelin', 'apellido_paterno' => 'Gonzalez', 'apellido_materno' => 'Reyes']);
        $conTitular->update(['titular_empleado_id' => $titular->id]);

        $user = User::factory()->create();
        $user->assignRole('supervision');

        $this->actingAs($user)
            ->get(route('sucursales.index', ['buscar' => 'gonzalez reyes']))
            ->assertOk()
            ->assertSee('Con Titular')
            ->assertDontSee('Sin Titular');
    }

    public function test_estatus_filter_shows_only_matching_sucursales(): void
    {
        Sucursal::factory()->create(['nombre_oficial' => 'Activa Uno', 'estatus_operativo' => 'activa']);
        Sucursal::factory()->create(['nombre_oficial' => 'Suspendida Uno', 'estatus_operativo' => 'suspendida']);

        $user = User::factory()->create();
        $user->assignRole('supervision');

        $response = $this->actingAs($user)->get(route('sucursales.index', ['estatus' => 'suspendida']));

        $response->assertOk()->assertSee('Suspendida Uno')->assertDontSee('Activa Uno');
    }

    public function test_per_page_selector_limits_the_number_of_results(): void
    {
        Sucursal::factory()->count(20)->create();

        $user = User::factory()->create();
        $user->assignRole('supervision');

        $response = $this->actingAs($user)->get(route('sucursales.index', ['per_page' => 15]));
        $response->assertOk()->assertViewHas('sucursales', fn ($p) => $p->perPage() === 15);

        $response = $this->actingAs($user)->get(route('sucursales.index', ['per_page' => 25]));
        $response->assertOk()->assertViewHas('sucursales', fn ($p) => $p->perPage() === 25);
    }

    public function test_per_page_rejects_values_outside_the_allowed_list(): void
    {
        Sucursal::factory()->count(3)->create();

        $user = User::factory()->create();
        $user->assignRole('supervision');

        $response = $this->actingAs($user)->get(route('sucursales.index', ['per_page' => 9999]));

        $response->assertOk()->assertViewHas('sucursales', fn ($p) => $p->perPage() === 15);
    }

    public function test_can_sort_by_clave_descending(): void
    {
        Sucursal::factory()->create(['nombre_oficial' => 'A', 'clave_financiera' => '09010']);
        Sucursal::factory()->create(['nombre_oficial' => 'B', 'clave_financiera' => '09090']);

        $user = User::factory()->create();
        $user->assignRole('supervision');

        $response = $this->actingAs($user)->get(
            route('sucursales.index', ['sort' => 'clave', 'direction' => 'desc']),
        );

        $response->assertOk();
        $content = $response->getContent();

        $this->assertGreaterThan(
            strpos($content, '09090'),
            strpos($content, '09010'),
            'Se esperaba 09090 antes que 09010 al ordenar la clave descendente.',
        );
    }
}
