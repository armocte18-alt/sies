<?php

namespace Tests\Feature;

use App\Models\Empleado;
use App\Models\Sucursal;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmpleadosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_user_without_permission_cannot_view_empleados(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('empleados.index'))->assertForbidden();
    }

    public function test_supervision_can_view_and_search_empleados(): void
    {
        Empleado::factory()->create(['nombre' => 'Evelin', 'apellido_paterno' => 'Gonzalez', 'no_empleado' => '111']);
        Empleado::factory()->create(['nombre' => 'Olga', 'apellido_paterno' => 'Navarro', 'no_empleado' => '222']);

        $user = User::factory()->create();
        $user->assignRole('supervision');

        $this->actingAs($user)
            ->get(route('empleados.index', ['buscar' => 'Evelin']))
            ->assertOk()
            ->assertSee('Evelin')
            ->assertDontSee('Olga');
    }

    public function test_supervision_cannot_manage_empleados(): void
    {
        $user = User::factory()->create();
        $user->assignRole('supervision');

        $this->actingAs($user)
            ->post(route('empleados.store'), ['no_empleado' => '999', 'nombre' => 'x', 'apellido_paterno' => 'y'])
            ->assertForbidden();
    }

    public function test_rrhh_can_create_update_and_toggle_an_empleado(): void
    {
        $sucursal = Sucursal::factory()->create();
        $user = User::factory()->create();
        $user->assignRole('rrhh');

        $this->actingAs($user)->post(route('empleados.store'), [
            'no_empleado' => '30001',
            'nombre' => 'Ana',
            'apellido_paterno' => 'Lopez',
            'sucursal_id' => $sucursal->id,
        ])->assertRedirect();

        $empleado = Empleado::where('no_empleado', '30001')->firstOrFail();
        $this->assertSame($sucursal->id, $empleado->sucursal_id);

        $this->actingAs($user)->put(route('empleados.update', $empleado), [
            'no_empleado' => '30001',
            'nombre' => 'Ana',
            'apellido_paterno' => 'Lopez Reyes',
        ])->assertRedirect();

        $this->assertSame('Lopez Reyes', $empleado->fresh()->apellido_paterno);

        $this->actingAs($user)
            ->patch(route('empleados.estado', $empleado))
            ->assertRedirect();

        $this->assertFalse($empleado->fresh()->activo);
    }

    public function test_no_empleado_must_be_unique(): void
    {
        Empleado::factory()->create(['no_empleado' => '555']);

        $user = User::factory()->create();
        $user->assignRole('rrhh');

        $this->actingAs($user)
            ->from(route('empleados.index'))
            ->post(route('empleados.store'), ['no_empleado' => '555', 'nombre' => 'x', 'apellido_paterno' => 'y'])
            ->assertSessionHasErrors('no_empleado');
    }

    public function test_can_filter_by_sucursal(): void
    {
        $sucursalA = Sucursal::factory()->create();
        $sucursalB = Sucursal::factory()->create();
        Empleado::factory()->create(['nombre' => 'De Sucursal A', 'sucursal_id' => $sucursalA->id]);
        Empleado::factory()->create(['nombre' => 'De Sucursal B', 'sucursal_id' => $sucursalB->id]);

        $user = User::factory()->create();
        $user->assignRole('supervision');

        $this->actingAs($user)
            ->get(route('empleados.index', ['sucursal' => $sucursalA->id]))
            ->assertOk()
            ->assertSee('De Sucursal A')
            ->assertDontSee('De Sucursal B');
    }
}
