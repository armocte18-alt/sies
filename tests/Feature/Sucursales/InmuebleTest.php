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

    public function test_administracion_can_capture_tipo_inmueble_and_caja_fuerte(): void
    {
        $sucursal = Sucursal::factory()->create();
        $admin = User::factory()->create();
        $admin->assignRole('administracion');

        $this->actingAs($admin)->patch(route('sucursales.inmueble.update', $sucursal), [
            'tipo_inmueble' => 'arrendado',
            'tipo_contrato_posesion' => 'arrendado',
            'tipo_caja_fuerte' => 'disco',
            'modelo_caja_fuerte' => 'Sentry Safe X055',
            'numero_inventario_caja_fuerte' => 'CF-001',
            'caja_fuerte_tiene_llave' => '1',
        ])->assertRedirect();

        $inmueble = $sucursal->inmueble()->firstOrFail();
        $this->assertSame('arrendado', $inmueble->tipo_inmueble);
        $this->assertSame('disco', $inmueble->tipo_caja_fuerte);
        $this->assertSame('Sentry Safe X055', $inmueble->modelo_caja_fuerte);
        $this->assertSame('CF-001', $inmueble->numero_inventario_caja_fuerte);
        $this->assertTrue($inmueble->caja_fuerte_tiene_llave);
    }
}
