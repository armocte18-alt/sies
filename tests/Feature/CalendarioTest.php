<?php

namespace Tests\Feature;

use App\Models\EventoCalendario;
use App\Models\EventoRapido;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarioTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_user_without_permission_cannot_view_calendario(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('calendario.index'))->assertForbidden();
    }

    public function test_supervision_can_view_the_calendar_shell(): void
    {
        $user = User::factory()->create();
        $user->assignRole('supervision');

        $this->actingAs($user)
            ->get(route('calendario.index'))
            ->assertOk()
            ->assertSee('id="calendario"', false)
            ->assertDontSee('eventos-arrastrables');
    }

    public function test_eventos_json_returns_only_events_overlapping_the_requested_range(): void
    {
        EventoCalendario::factory()->create(['nombre' => 'dentro del rango', 'fecha_inicio' => '2026-08-10']);
        EventoCalendario::factory()->create(['nombre' => 'fuera del rango', 'fecha_inicio' => '2026-10-10']);

        $user = User::factory()->create();
        $user->assignRole('supervision');

        $response = $this->actingAs($user)->getJson(route('calendario.eventos', [
            'start' => '2026-08-01', 'end' => '2026-08-31',
        ]))->assertOk();

        $titulos = collect($response->json())->pluck('title');
        $this->assertTrue($titulos->contains('Dentro Del Rango'));
        $this->assertFalse($titulos->contains('Fuera Del Rango'));
    }

    public function test_supervision_cannot_create_events(): void
    {
        $user = User::factory()->create();
        $user->assignRole('supervision');

        $this->actingAs($user)
            ->postJson(route('calendario.eventos.store'), ['nombre' => 'x', 'fecha_inicio' => now()->format('Y-m-d')])
            ->assertForbidden();
    }

    public function test_operacion_can_create_update_and_delete_a_single_event(): void
    {
        $user = User::factory()->create();
        $user->assignRole('operacion');

        $respuesta = $this->actingAs($user)->postJson(route('calendario.eventos.store'), [
            'nombre' => 'Feria de Paz',
            'fecha_inicio' => now()->addDays(3)->format('Y-m-d'),
            'color' => '#d416c4',
            'invitados' => ['ana', 'luis'],
        ])->assertOk();

        $evento = EventoCalendario::where('nombre', 'Feria de Paz')->firstOrFail();
        $this->assertSame(['ana', 'luis'], $evento->invitados);
        $this->assertSame($evento->id, $respuesta->json('id'));

        $this->actingAs($user)->putJson(route('calendario.eventos.update', $evento), [
            'nombre' => 'Feria de Paz (reprogramada)',
            'fecha_inicio' => now()->addDays(5)->format('Y-m-d'),
        ])->assertOk();

        $this->assertSame('Feria de Paz (reprogramada)', $evento->fresh()->nombre);

        $this->actingAs($user)
            ->deleteJson(route('calendario.eventos.destroy', $evento))
            ->assertOk();

        $this->assertModelMissing($evento);
    }

    public function test_creating_a_recurring_event_generates_one_row_per_matching_weekday(): void
    {
        $user = User::factory()->create();
        $user->assignRole('operacion');

        // 2026-08-03 es lunes; generamos la serie para todo agosto en lunes y jueves.
        $respuesta = $this->actingAs($user)->postJson(route('calendario.eventos.store'), [
            'nombre' => 'Audiencia Ciudadana',
            'fecha_inicio' => '2026-08-03',
            'fecha_fin' => '2026-08-31',
            'es_recurrente' => true,
            'dias_semana' => [1, 4],
        ])->assertOk();

        $creados = EventoCalendario::where('nombre', 'Audiencia Ciudadana')->get();
        $this->assertSame($creados->count(), $respuesta->json('creados'));
        $this->assertGreaterThan(1, $creados->count());
        $this->assertTrue($creados->pluck('grupo_recurrencia_id')->unique()->count() === 1);

        foreach ($creados as $evento) {
            $this->assertContains($evento->fecha_inicio->dayOfWeek, [1, 4]);
        }
    }

    public function test_deleting_a_series_removes_every_occurrence(): void
    {
        $user = User::factory()->create();
        $user->assignRole('operacion');

        $this->actingAs($user)->postJson(route('calendario.eventos.store'), [
            'nombre' => 'Serie de prueba',
            'fecha_inicio' => '2026-09-01',
            'fecha_fin' => '2026-09-14',
            'es_recurrente' => true,
            'dias_semana' => [2],
        ])->assertOk();

        $eventos = EventoCalendario::where('nombre', 'Serie de prueba')->get();
        $this->assertGreaterThan(1, $eventos->count());

        $this->actingAs($user)
            ->deleteJson(route('calendario.eventos.destroy-serie', $eventos->first()))
            ->assertOk();

        $this->assertDatabaseCount('eventos_calendario', 0);
    }

    public function test_operacion_can_manage_eventos_rapidos(): void
    {
        $user = User::factory()->create();
        $user->assignRole('operacion');

        $this->actingAs($user)->postJson(route('calendario.rapidos.store'), [
            'nombre' => 'Feria de Empleo',
            'color' => '#285c4d',
        ])->assertOk();

        $rapido = EventoRapido::where('nombre', 'Feria de Empleo')->firstOrFail();

        $this->actingAs($user)
            ->getJson(route('calendario.rapidos.index'))
            ->assertOk()
            ->assertJsonFragment(['nombre' => 'Feria de Empleo']);

        $this->actingAs($user)
            ->deleteJson(route('calendario.rapidos.destroy', $rapido))
            ->assertOk();

        $this->assertModelMissing($rapido);
    }
}
