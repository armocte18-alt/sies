<?php

namespace Tests\Feature;

use App\Models\NivelSalarial;
use App\Models\Puesto;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class CatalogosRhTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('rh.catalogos.index'))->assertRedirect(route('login'));
    }

    public function test_user_without_permission_is_forbidden(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('rh.catalogos.index'))
            ->assertForbidden();
    }

    public function test_rrhh_role_can_view_the_catalogs_page(): void
    {
        $user = User::factory()->create();
        $user->assignRole('rrhh');

        $this->actingAs($user)
            ->get(route('rh.catalogos.index'))
            ->assertOk()
            ->assertSee('Niveles salariales')
            ->assertSee('Puestos')
            ->assertSee('Labores');
    }

    public function test_mixed_case_input_is_stored_in_lowercase_and_displayed_title_cased(): void
    {
        $user = User::factory()->create();
        $user->assignRole('rrhh');

        $this->actingAs($user)->post(route('rh.catalogos.items.store', 'puestos'), [
            'nombre' => 'Administrador DE Proyectos',
            'descripcion' => 'Área de TELECOMUNICACIONES',
        ])->assertRedirect();

        $puesto = Puesto::where('nombre', 'administrador de proyectos')->firstOrFail();

        $this->assertSame('administrador de proyectos', $puesto->getRawOriginal('nombre'));
        $this->assertSame('área de telecomunicaciones', $puesto->getRawOriginal('descripcion'));
        $this->assertSame('Administrador De Proyectos', $puesto->nombre);
    }

    public function test_duplicate_names_are_rejected_case_insensitively(): void
    {
        $user = User::factory()->create();
        $user->assignRole('rrhh');
        Puesto::create(['nombre' => 'jefe de oficina', 'activo' => true]);

        $this->actingAs($user)
            ->from(route('rh.catalogos.index'))
            ->post(route('rh.catalogos.items.store', 'puestos'), [
                'nombre' => 'JEFE DE OFICINA',
            ])
            ->assertSessionHasErrors('nombre');

        $this->assertSame(1, Puesto::where('nombre', 'jefe de oficina')->count());
    }

    public function test_niveles_salariales_full_lifecycle(): void
    {
        $user = User::factory()->create();
        $user->assignRole('rrhh');

        $this->actingAs($user)->post(route('rh.catalogos.niveles-salariales.store'), [
            'consecutivo' => 1,
            'nivel_salarial' => '15 A1',
            'sueldo_base' => 10000,
            'compensacion_garantizada' => 7500,
        ])->assertRedirect();

        $nivel = NivelSalarial::where('consecutivo', 1)->firstOrFail();
        $this->assertSame('15 a1', $nivel->getRawOriginal('nivel_salarial'));
        $this->assertSame('15 A1', $nivel->nivel_salarial);
        $this->assertSame('10000.00', (string) $nivel->sueldo_base);

        $this->actingAs($user)->put(route('rh.catalogos.niveles-salariales.update', $nivel), [
            'consecutivo' => 1,
            'nivel_salarial' => '15 A1',
            'sueldo_base' => 11000,
            'compensacion_garantizada' => 7500,
            'activo' => 0,
        ])->assertRedirect();

        $this->assertSame('11000.00', (string) $nivel->fresh()->sueldo_base);
        $this->assertFalse($nivel->fresh()->activo);

        $this->actingAs($user)
            ->delete(route('rh.catalogos.niveles-salariales.destroy', $nivel))
            ->assertRedirect();

        $this->assertDatabaseMissing('niveles_salariales', ['id' => $nivel->id]);
    }
}
