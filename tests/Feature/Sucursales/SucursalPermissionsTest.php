<?php

namespace Tests\Feature\Sucursales;

use App\Models\Sucursal;
use App\Models\User;
use Database\Seeders\CoordinacionSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SucursalPermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(CoordinacionSeeder::class);
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $sucursal = Sucursal::factory()->create();

        $this->get(route('sucursales.index'))->assertRedirect(route('login'));
        $this->get(route('sucursales.show', $sucursal))->assertRedirect(route('login'));
    }

    public function test_user_without_permission_cannot_view_sucursales(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($user)
            ->get(route('sucursales.index'))
            ->assertForbidden();
    }

    public function test_tecnica_can_update_equipamiento_but_not_finanzas(): void
    {
        $sucursal = Sucursal::factory()->create();
        $tecnica = User::factory()->create(['email_verified_at' => now()]);
        $tecnica->assignRole('tecnica');

        $this->actingAs($tecnica)
            ->patch(route('sucursales.equipamiento.update', $sucursal), [
                'comunicacion' => 'Red Local / Enlace Dedicado (TELMEX)',
                'num_computadoras' => 3,
                'num_impresoras' => 1,
                'num_servidores' => 0,
                'tiene_camaras' => 0,
                'num_camaras' => 0,
                'tiene_alarma' => 0,
                'tiene_extintores' => 0,
                'num_extintores' => 0,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('sucursal_equipamientos', [
            'sucursal_id' => $sucursal->id,
            'comunicacion' => 'Red Local / Enlace Dedicado (TELMEX)',
            'num_computadoras' => 3,
        ]);

        $this->actingAs($tecnica)
            ->patch(route('sucursales.finanzas.update', $sucursal), [
                'limite_existencia_caja' => 1000,
            ])
            ->assertForbidden();
    }

    public function test_finanzas_role_cannot_update_identificacion(): void
    {
        $sucursal = Sucursal::factory()->create();
        $finanzas = User::factory()->create(['email_verified_at' => now()]);
        $finanzas->assignRole('finanzas');

        $this->actingAs($finanzas)
            ->patch(route('sucursales.identificacion.update', $sucursal), [
                'nombre_oficial' => 'Intento no autorizado',
                'clave_financiera' => $sucursal->clave_financiera,
                'estatus_operativo' => 'activa',
            ])
            ->assertForbidden();
    }

    public function test_administrador_can_do_everything(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->create(['email_verified_at' => now()]);
        $admin->assignRole('administrador');

        $this->actingAs($admin)
            ->patch(route('sucursales.finanzas.update', $sucursal), [
                'limite_existencia_caja' => 2000,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('sucursal_finanzas', [
            'sucursal_id' => $sucursal->id,
            'limite_existencia_caja' => 2000,
        ]);
    }
}
