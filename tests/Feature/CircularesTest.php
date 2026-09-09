<?php

namespace Tests\Feature;

use App\Models\Circular;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CircularesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_user_without_permission_cannot_view_circulares(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('circulares.index'))->assertForbidden();
    }

    public function test_juridico_can_view_and_search_circulares(): void
    {
        Circular::factory()->create(['numero' => '10/2026', 'asunto' => 'Sobre créditos solidarios']);
        Circular::factory()->create(['numero' => '11/2026', 'asunto' => 'Sobre cobranza']);

        $user = User::factory()->create();
        $user->assignRole('juridico');

        $this->actingAs($user)
            ->get(route('circulares.index', ['buscar' => 'solidarios']))
            ->assertOk()
            ->assertSee('10/2026')
            ->assertDontSee('11/2026');
    }

    public function test_juridico_can_create_a_circular_with_a_pdf_attachment(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $user->assignRole('juridico');

        $this->actingAs($user)->post(route('circulares.store'), [
            'numero' => '99/2026',
            'asunto' => 'Circular de prueba',
            'archivo' => UploadedFile::fake()->create('aviso.pdf', 100, 'application/pdf'),
        ])->assertRedirect();

        $circular = Circular::where('numero', '99/2026')->firstOrFail();
        $this->assertNotNull($circular->archivo_path);
        Storage::disk('public')->assertExists($circular->archivo_path);
    }

    public function test_finanzas_role_cannot_manage_circulares(): void
    {
        $circular = Circular::factory()->create();
        $finanzas = User::factory()->create();
        $finanzas->assignRole('finanzas');

        $this->actingAs($finanzas)
            ->post(route('circulares.store'), ['numero' => 'x', 'asunto' => 'y'])
            ->assertForbidden();

        $this->actingAs($finanzas)
            ->patch(route('circulares.estado', $circular))
            ->assertForbidden();
    }

    public function test_can_toggle_circular_estado(): void
    {
        $circular = Circular::factory()->create(['activo' => true]);
        $user = User::factory()->create();
        $user->assignRole('juridico');

        $this->actingAs($user)
            ->patch(route('circulares.estado', $circular))
            ->assertRedirect();

        $this->assertFalse($circular->fresh()->activo);
    }

    public function test_numero_must_be_unique(): void
    {
        Circular::factory()->create(['numero' => '50/2026']);

        $user = User::factory()->create();
        $user->assignRole('juridico');

        $this->actingAs($user)
            ->from(route('circulares.index'))
            ->post(route('circulares.store'), ['numero' => '50/2026', 'asunto' => 'Duplicada'])
            ->assertSessionHasErrors('numero');
    }
}
