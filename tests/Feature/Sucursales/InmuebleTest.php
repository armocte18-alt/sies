<?php

namespace Tests\Feature\Sucursales;

use App\Models\Sucursal;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InmuebleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_administracion_can_capture_proteccion_civil_data(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->create();
        $admin->assignRole('administracion');

        $this->actingAs($admin)->patch(route('sucursales.inmueble.update', $sucursal), [
            'tipo_contrato_posesion' => 'propio',
            'cuenta_proteccion_civil' => '1',
            'numero_dictamen_proteccion_civil' => 'PC-2026-045',
            'vigencia_proteccion_civil' => '2027-01-15',
        ])->assertRedirect();

        $inmueble = $sucursal->inmueble()->firstOrFail();
        $this->assertTrue($inmueble->cuenta_proteccion_civil);
        $this->assertSame('PC-2026-045', $inmueble->numero_dictamen_proteccion_civil);
        $this->assertSame('2027-01-15', $inmueble->vigencia_proteccion_civil->format('Y-m-d'));
    }

    public function test_cuenta_proteccion_civil_defaults_to_false_when_unchecked(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->create();
        $admin->assignRole('administracion');

        // El checkbox no marcado no envía el campo; el <input type="hidden"> de
        // respaldo en la vista sí lo hace con valor "0" — se simula aquí.
        $this->actingAs($admin)->patch(route('sucursales.inmueble.update', $sucursal), [
            'tipo_contrato_posesion' => 'propio',
            'cuenta_proteccion_civil' => '0',
        ])->assertRedirect();

        $this->assertFalse($sucursal->inmueble()->firstOrFail()->cuenta_proteccion_civil);
    }
}
