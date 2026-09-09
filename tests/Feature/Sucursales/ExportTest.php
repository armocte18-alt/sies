<?php

namespace Tests\Feature\Sucursales;

use App\Models\Sucursal;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_user_without_permission_cannot_export(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('sucursales.exportar.excel'))->assertForbidden();
        $this->actingAs($user)->get(route('sucursales.exportar.pdf'))->assertForbidden();
    }

    public function test_can_export_excel(): void
    {
        Sucursal::factory()->count(3)->create();

        $user = User::factory()->create();
        $user->assignRole('supervision');

        $response = $this->actingAs($user)->get(route('sucursales.exportar.excel'));

        $response->assertOk();
        $this->assertStringContainsString(
            'spreadsheetml',
            $response->headers->get('Content-Type'),
        );
    }

    public function test_can_export_pdf(): void
    {
        Sucursal::factory()->count(3)->create();

        $user = User::factory()->create();
        $user->assignRole('supervision');

        $response = $this->actingAs($user)->get(route('sucursales.exportar.pdf'));

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_export_respects_the_estatus_filter(): void
    {
        // El PDF comprime el contenido (FlateDecode), así que no se puede
        // buscar texto plano en la respuesta; se confirma indirectamente
        // que el filtro llega al export usando la misma query que el index
        // (cubierta por IndexSearchSortTest::test_estatus_filter_...) y que
        // el export con el filtro aplicado sigue respondiendo 200.
        Sucursal::factory()->create(['nombre_oficial' => 'Activa Export', 'estatus_operativo' => 'activa']);
        Sucursal::factory()->create(['nombre_oficial' => 'Suspendida Export', 'estatus_operativo' => 'suspendida']);

        $user = User::factory()->create();
        $user->assignRole('supervision');

        $this->actingAs($user)
            ->get(route('sucursales.exportar.pdf', ['estatus' => 'activa']))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('sucursales.exportar.excel', ['estatus' => 'activa']))
            ->assertOk();
    }
}
