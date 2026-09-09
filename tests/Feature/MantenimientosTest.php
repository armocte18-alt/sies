<?php

namespace Tests\Feature;

use App\Models\Empleado;
use App\Models\Mantenimiento;
use App\Models\MantenimientoMaterial;
use App\Models\TipoMantenimiento;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MantenimientosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function usuarioConAcceso(): User
    {
        $user = User::factory()->create();
        $user->assignRole('administracion');

        return $user;
    }

    public function test_user_without_permission_cannot_view_mantenimientos(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('mantenimientos.index'))->assertForbidden();
    }

    public function test_tecnica_can_view_but_not_manage(): void
    {
        $user = User::factory()->create();
        $user->assignRole('tecnica');

        $this->actingAs($user)->get(route('mantenimientos.index'))->assertOk();
        $this->actingAs($user)->post(route('mantenimientos.store'), ['titulo' => 'x'])->assertForbidden();
    }

    public function test_store_creates_a_pending_maintenance_with_bitacora_entry(): void
    {
        $tipo = TipoMantenimiento::factory()->create();
        $user = $this->usuarioConAcceso();

        $this->actingAs($user)->post(route('mantenimientos.store'), [
            'tipo_mantenimiento_id' => $tipo->id,
            'titulo' => 'Cambio de luminarias',
            'prioridad' => 'media',
            'fecha_programada_inicio' => now()->addDay()->format('Y-m-d'),
            'fecha_programada_fin' => now()->addDays(2)->format('Y-m-d'),
        ])->assertRedirect();

        $this->assertDatabaseHas('mantenimientos', [
            'titulo' => 'Cambio de luminarias',
            'estatus' => 'pendiente',
            'creado_por' => $user->id,
        ]);
        $mantenimiento = Mantenimiento::first();
        $this->assertDatabaseHas('mantenimiento_bitacora', [
            'mantenimiento_id' => $mantenimiento->id,
            'accion' => 'creado',
        ]);
    }

    public function test_cambiar_estatus_a_completado_sets_fecha_fin_real_and_cerrado_por(): void
    {
        $mantenimiento = Mantenimiento::factory()->create(['estatus' => 'en_proceso']);
        $user = $this->usuarioConAcceso();

        $this->actingAs($user)->patch(route('mantenimientos.estatus', $mantenimiento), [
            'estatus' => 'completado',
            'comentario' => 'Trabajo terminado.',
        ])->assertRedirect();

        $mantenimiento->refresh();
        $this->assertSame('completado', $mantenimiento->estatus);
        $this->assertNotNull($mantenimiento->fecha_fin_real);
        $this->assertSame($user->id, $mantenimiento->cerrado_por);
        $this->assertSame('Trabajo terminado.', $mantenimiento->comentario_cierre);
    }

    public function test_adding_a_material_recalculates_costo_materiales(): void
    {
        $mantenimiento = Mantenimiento::factory()->create(['costo_materiales' => 0]);
        $user = $this->usuarioConAcceso();

        $this->actingAs($user)->post(route('mantenimientos.materiales.store', $mantenimiento), [
            'nombre_material' => 'Pintura',
            'unidad' => 'litro',
            'cantidad' => 3,
            'costo_unitario' => 150,
        ])->assertRedirect();

        $this->assertSame('450.00', $mantenimiento->fresh()->costo_materiales);
    }

    public function test_removing_a_material_recalculates_costo_materiales(): void
    {
        $mantenimiento = Mantenimiento::factory()->create();
        $material = MantenimientoMaterial::factory()->create([
            'mantenimiento_id' => $mantenimiento->id,
            'costo_total' => 200,
        ]);
        $mantenimiento->recalcularCostoMateriales();
        $user = $this->usuarioConAcceso();

        $this->actingAs($user)->delete(route('mantenimientos.materiales.destroy', [$mantenimiento, $material]))->assertRedirect();

        $this->assertSame('0.00', $mantenimiento->fresh()->costo_materiales);
    }

    public function test_personal_can_be_assigned_and_removed(): void
    {
        $mantenimiento = Mantenimiento::factory()->create();
        $empleado = Empleado::factory()->create();
        $user = $this->usuarioConAcceso();

        $this->actingAs($user)->post(route('mantenimientos.personal.store', $mantenimiento), [
            'empleado_id' => $empleado->id,
            'rol_en_mantenimiento' => 'Responsable',
        ])->assertRedirect();

        $this->assertDatabaseHas('mantenimiento_personal', [
            'mantenimiento_id' => $mantenimiento->id,
            'empleado_id' => $empleado->id,
        ]);

        $this->actingAs($user)->delete(route('mantenimientos.personal.destroy', [$mantenimiento, $empleado]))->assertRedirect();

        $this->assertDatabaseMissing('mantenimiento_personal', [
            'mantenimiento_id' => $mantenimiento->id,
            'empleado_id' => $empleado->id,
        ]);
    }

    public function test_destroy_soft_deletes_and_hides_from_the_default_listing(): void
    {
        $mantenimiento = Mantenimiento::factory()->create();
        $user = $this->usuarioConAcceso();

        $this->actingAs($user)->delete(route('mantenimientos.destroy', $mantenimiento))->assertRedirect();

        $this->assertSoftDeleted('mantenimientos', ['id' => $mantenimiento->id]);
        $this->assertSame(0, Mantenimiento::count());
        $this->assertSame(1, Mantenimiento::withTrashed()->count());
    }
}
