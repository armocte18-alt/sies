<?php

namespace Tests\Feature;

use App\Models\AreaCentral;
use App\Models\Gerencia;
use App\Models\PersonalExterno;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class DirectoriosTest extends TestCase
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
        $this->get(route('directorios.index'))->assertRedirect(route('login'));
    }

    public function test_user_without_permission_is_forbidden(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('directorios.index'))->assertForbidden();
    }

    public function test_rrhh_role_can_view_the_directory(): void
    {
        $user = User::factory()->create();
        $user->assignRole('rrhh');

        $this->actingAs($user)
            ->get(route('directorios.index'))
            ->assertOk()
            ->assertSee('Gerencias')
            ->assertSee('Personal externo');
    }

    public function test_a_view_only_permission_cannot_create_records(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('directorios.ver');

        $this->actingAs($user)
            ->post(route('directorios.gerencias.store'), [
                'nombre' => 'Test',
                'coordinacion' => 'Test',
                'correo_finabien' => 'test@finabien.gob.mx',
            ])
            ->assertForbidden();
    }

    public function test_can_create_and_update_a_gerencia_with_normalized_case(): void
    {
        $user = User::factory()->create();
        $user->assignRole('rrhh');

        $this->actingAs($user)->post(route('directorios.gerencias.store'), [
            'nombre' => 'LIC. Juan PÉREZ',
            'coordinacion' => 'COORDINACIÓN DE OPERACIÓN',
            'correo_finabien' => 'Juan.Perez@Finabien.Gob.Mx',
        ])->assertRedirect();

        $gerencia = Gerencia::firstOrFail();
        $this->assertSame('lic. juan pérez', $gerencia->getRawOriginal('nombre'));
        $this->assertSame('Lic. Juan Pérez', $gerencia->nombre);
        $this->assertTrue($gerencia->activo);

        $this->actingAs($user)->put(route('directorios.gerencias.update', $gerencia), [
            'nombre' => $gerencia->nombre,
            'coordinacion' => 'nueva coordinación',
            'correo_finabien' => $gerencia->correo_finabien,
        ])->assertRedirect();

        $this->assertSame('Nueva Coordinación', $gerencia->fresh()->coordinacion);
    }

    public function test_can_toggle_gerencia_active_status(): void
    {
        $user = User::factory()->create();
        $user->assignRole('rrhh');
        $gerencia = Gerencia::create(['nombre' => 'test', 'coordinacion' => 'test', 'correo_finabien' => 'a@finabien.gob.mx', 'activo' => true]);

        $this->actingAs($user)
            ->patch(route('directorios.gerencias.estado', $gerencia))
            ->assertRedirect();

        $this->assertFalse($gerencia->fresh()->activo);
    }

    public function test_can_create_area_central_linked_to_a_gerencia(): void
    {
        $user = User::factory()->create();
        $user->assignRole('rrhh');
        $gerencia = Gerencia::create(['nombre' => 'test', 'coordinacion' => 'test', 'correo_finabien' => 'a@finabien.gob.mx', 'activo' => true]);

        $this->actingAs($user)->post(route('directorios.areas.store'), [
            'nombre' => 'Área de Sistemas',
            'adscripcion' => 'Tecnología',
            'gerencia_id' => $gerencia->id,
            'correo_finabien' => 'sistemas@finabien.gob.mx',
        ])->assertRedirect();

        $area = AreaCentral::firstOrFail();
        $this->assertSame($gerencia->id, $area->gerencia_id);
        $this->assertTrue($area->gerencia->is($gerencia));
    }

    public function test_can_create_personal_externo_with_multiple_phones_and_emails(): void
    {
        $user = User::factory()->create();
        $user->assignRole('rrhh');

        $this->actingAs($user)->post(route('directorios.externos.store'), [
            'nombre' => 'Juan Externo',
            'dependencia' => 'SHCP',
            'telefonos' => ['5555555555', '', '5566778899'],
            'correos' => ['juan@shcp.gob.mx', ''],
        ])->assertRedirect();

        $externo = PersonalExterno::firstOrFail();
        $this->assertSame(['5555555555', '5566778899'], $externo->telefonos);
        $this->assertSame(['juan@shcp.gob.mx'], $externo->correos);
    }

    public function test_area_central_requires_a_valid_gerencia_id(): void
    {
        $user = User::factory()->create();
        $user->assignRole('rrhh');

        $this->actingAs($user)
            ->from(route('directorios.index'))
            ->post(route('directorios.areas.store'), [
                'nombre' => 'Área',
                'adscripcion' => 'X',
                'gerencia_id' => 9999,
                'correo_finabien' => 'x@finabien.gob.mx',
            ])
            ->assertSessionHasErrors('gerencia_id');
    }
}
