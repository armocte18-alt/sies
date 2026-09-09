<?php

namespace Tests\Feature;

use App\Models\EventoCalendario;
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

    public function test_supervision_can_view_the_current_month(): void
    {
        EventoCalendario::factory()->create([
            'nombre' => 'Reunión de prueba',
            'fecha_inicio' => now()->format('Y-m-d'),
        ]);

        $user = User::factory()->create();
        $user->assignRole('supervision');

        $this->actingAs($user)
            ->get(route('calendario.index'))
            ->assertOk()
            ->assertSee('Reunión de prueba');
    }

    public function test_supervision_cannot_create_events(): void
    {
        $user = User::factory()->create();
        $user->assignRole('supervision');

        $this->actingAs($user)
            ->post(route('calendario.store'), ['nombre' => 'x', 'fecha_inicio' => now()->format('Y-m-d')])
            ->assertForbidden();
    }

    public function test_operacion_can_create_and_delete_an_event(): void
    {
        $user = User::factory()->create();
        $user->assignRole('operacion');

        $this->actingAs($user)->post(route('calendario.store'), [
            'nombre' => 'Feria de Paz',
            'fecha_inicio' => now()->addDays(3)->format('Y-m-d'),
            'color' => '#d416c4',
            'invitados' => 'ana, luis',
        ])->assertRedirect();

        $evento = EventoCalendario::where('nombre', 'Feria de Paz')->firstOrFail();
        $this->assertSame(['ana', 'luis'], $evento->invitados);

        $this->actingAs($user)
            ->delete(route('calendario.destroy', $evento))
            ->assertRedirect();

        $this->assertModelMissing($evento);
    }

    public function test_navigating_to_a_different_month_shows_its_events_only(): void
    {
        EventoCalendario::factory()->create(['nombre' => 'Evento de otro mes', 'fecha_inicio' => now()->addMonths(2)->startOfMonth()->addDays(2)]);
        EventoCalendario::factory()->create(['nombre' => 'Evento de este mes', 'fecha_inicio' => now()]);

        $user = User::factory()->create();
        $user->assignRole('supervision');

        $otroMes = now()->addMonths(2);

        $this->actingAs($user)
            ->get(route('calendario.index', ['mes' => $otroMes->month, 'anio' => $otroMes->year]))
            ->assertOk()
            ->assertSee('Evento de otro mes')
            ->assertDontSee('Evento de este mes');
    }
}
