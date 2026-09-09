<?php

namespace Tests\Feature;

use App\Models\MinutarioBoletin;
use App\Models\MinutarioOficio;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MinutariosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_user_without_permission_cannot_view_minutarios(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('minutarios.index'))->assertForbidden();
    }

    public function test_juridico_can_view_minutarios(): void
    {
        MinutarioBoletin::factory()->create(['asunto' => 'Boletin de prueba']);

        $user = User::factory()->create();
        $user->assignRole('juridico');

        $this->actingAs($user)
            ->get(route('minutarios.index'))
            ->assertOk()
            ->assertSee('Boletin De Prueba');
    }

    public function test_creating_a_boletin_assigns_a_sequential_folio_per_year(): void
    {
        $user = User::factory()->create();
        $user->assignRole('juridico');

        $this->actingAs($user)->post(route('minutarios.boletines.store'), [
            'asunto' => 'Primer boletin',
            'contenido' => 'Contenido de prueba',
            'fecha_vigor' => now()->addDays(10)->format('Y-m-d'),
            'dirigido_tipo' => 'todos',
        ])->assertRedirect();

        $this->actingAs($user)->post(route('minutarios.boletines.store'), [
            'asunto' => 'Segundo boletin',
            'contenido' => 'Contenido de prueba',
            'fecha_vigor' => now()->addDays(10)->format('Y-m-d'),
            'dirigido_tipo' => 'todos',
        ])->assertRedirect();

        $anio = now()->year;
        $this->assertSame("B-0001/{$anio}", MinutarioBoletin::where('asunto', 'Primer boletin')->firstOrFail()->nomenclatura);
        $this->assertSame("B-0002/{$anio}", MinutarioBoletin::where('asunto', 'Segundo boletin')->firstOrFail()->nomenclatura);
    }

    public function test_creating_an_oficio_assigns_a_sequential_folio_per_year(): void
    {
        $user = User::factory()->create();
        $user->assignRole('juridico');

        $this->actingAs($user)->post(route('minutarios.oficios.store'), [
            'asunto' => 'Oficio de prueba', 'dirigido_a' => 'Direccion General',
        ])->assertRedirect();

        $anio = now()->year;
        $this->assertSame("4120.-0001/{$anio}", MinutarioOficio::where('asunto', 'Oficio de prueba')->firstOrFail()->nomenclatura);
    }

    public function test_supervision_cannot_manage_minutarios(): void
    {
        $user = User::factory()->create();
        $user->assignRole('supervision');

        $this->actingAs($user)
            ->post(route('minutarios.boletines.store'), ['asunto' => 'x'])
            ->assertForbidden();
    }

    public function test_can_cancel_an_oficio_with_a_reason(): void
    {
        $oficio = MinutarioOficio::factory()->create();
        $user = User::factory()->create();
        $user->assignRole('juridico');

        $this->actingAs($user)->patch(route('minutarios.oficios.cancelar', $oficio), [
            'motivo_cancelacion' => 'Oficio duplicado por error de captura.',
        ])->assertRedirect();

        $oficio->refresh();
        $this->assertTrue($oficio->es_cancelado);
        $this->assertSame('Oficio duplicado por error de captura.', $oficio->motivo_cancelacion);
        $this->assertSame($user->name, $oficio->cancelado_por_nombre);
    }

    public function test_can_toggle_escaneo_status(): void
    {
        $oficio = MinutarioOficio::factory()->create(['ya_escaneado' => false]);
        $user = User::factory()->create();
        $user->assignRole('juridico');

        $this->actingAs($user)
            ->patch(route('minutarios.oficios.escaneo', $oficio))
            ->assertRedirect();

        $this->assertTrue($oficio->fresh()->ya_escaneado);
    }
}
