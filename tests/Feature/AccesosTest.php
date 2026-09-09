<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AccesosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->assignRole('administrador');

        return $admin;
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('accesos.index'))->assertRedirect(route('login'));
    }

    public function test_user_without_permission_is_forbidden(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('accesos.index'))->assertForbidden();
    }

    public function test_admin_can_view_users_list_roles_catalog_and_user_edit_page(): void
    {
        $admin = $this->admin();
        $otro = User::factory()->create();
        $otro->assignRole('finanzas');

        $this->actingAs($admin)->get(route('accesos.index'))->assertOk()->assertSee($otro->email);
        $this->actingAs($admin)->get(route('accesos.roles'))->assertOk()->assertSee('sucursales.ver');
        $this->actingAs($admin)->get(route('accesos.edit', $otro))->assertOk()->assertSee($otro->name);
    }

    public function test_admin_can_change_another_users_roles(): void
    {
        $admin = $this->admin();
        $otro = User::factory()->create();

        $this->actingAs($admin)
            ->put(route('accesos.roles.update', $otro), ['roles' => ['finanzas']])
            ->assertRedirect();

        $this->assertTrue($otro->fresh()->hasRole('finanzas'));
    }

    public function test_admin_can_grant_a_special_permission_beyond_the_users_role(): void
    {
        $admin = $this->admin();
        $otro = User::factory()->create();
        $otro->assignRole('finanzas'); // no incluye sucursales.crear

        $this->assertFalse($otro->can('sucursales.crear'));

        $this->actingAs($admin)
            ->put(route('accesos.permisos.update', $otro), ['permisos' => ['sucursales.ver', 'sucursales.editar.finanzas', 'sucursales.crear']])
            ->assertRedirect();

        $this->assertTrue($otro->fresh()->can('sucursales.crear'));
    }

    public function test_admin_cannot_edit_their_own_roles_or_permissions(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->put(route('accesos.roles.update', $admin), ['roles' => []])
            ->assertForbidden();

        $this->actingAs($admin)
            ->put(route('accesos.permisos.update', $admin), ['permisos' => []])
            ->assertForbidden();

        $this->assertTrue($admin->fresh()->hasRole('administrador'));
    }

    public function test_cannot_remove_administrador_role_from_the_last_active_admin(): void
    {
        $admin = $this->admin();
        $otroAdmin = $this->admin();

        // Quitarle el rol a $otroAdmin es seguro porque $admin sigue siendo administrador activo.
        $this->actingAs($admin)
            ->put(route('accesos.roles.update', $otroAdmin), ['roles' => ['finanzas']])
            ->assertRedirect();
        $this->assertFalse($otroAdmin->fresh()->hasRole('administrador'));

        // Ahora $admin es el único administrador: otra persona con acceso a
        // este módulo (pero sin ser ella misma administradora) tampoco puede
        // quitárselo, porque dejaría el sistema sin administradores.
        $gestorAccesos = User::factory()->create();
        $gestorAccesos->givePermissionTo('accesos.gestionar');
        $this->actingAs($gestorAccesos)
            ->from(route('accesos.edit', $admin))
            ->put(route('accesos.roles.update', $admin), ['roles' => ['finanzas']])
            ->assertRedirect(route('accesos.edit', $admin));

        $this->assertTrue($admin->fresh()->hasRole('administrador'));
    }

    public function test_admin_cannot_deactivate_their_own_account(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->patch(route('accesos.estado.update', $admin))
            ->assertRedirect();

        $this->assertTrue($admin->fresh()->activo);
    }

    public function test_cannot_deactivate_the_last_active_admin(): void
    {
        $admin = $this->admin();
        $otroAdmin = $this->admin();

        $this->actingAs($admin)
            ->patch(route('accesos.estado.update', $otroAdmin))
            ->assertRedirect();
        $this->assertFalse($otroAdmin->fresh()->activo);

        $gestorAccesos = User::factory()->create();
        $gestorAccesos->givePermissionTo('accesos.gestionar');
        $this->actingAs($gestorAccesos)
            ->patch(route('accesos.estado.update', $admin))
            ->assertRedirect();

        $this->assertTrue($admin->fresh()->activo, 'El último administrador activo no debe poder desactivarse.');
    }

    public function test_deactivated_users_cannot_log_in(): void
    {
        $user = User::factory()->create(['activo' => false]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_deactivating_a_logged_in_user_ends_their_session_on_next_request(): void
    {
        $user = User::factory()->create();
        $admin = $this->admin();

        $this->actingAs($admin)->patch(route('accesos.estado.update', $user));
        $this->assertFalse($user->fresh()->activo);

        // actingAs() with the original in-memory instance would still carry
        // the stale activo=true it had before the admin's update above.
        $this->actingAs($user->fresh())
            ->get(route('dashboard'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
