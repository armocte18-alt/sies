<?php

namespace Tests\Feature\Sucursales;

use App\Models\Sucursal;
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
