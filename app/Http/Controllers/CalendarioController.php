<?php

namespace App\Http\Controllers;

use App\Http\Requests\Calendario\StoreEventoRapidoRequest;
use App\Http\Requests\Calendario\StoreEventoRequest;
use App\Http\Requests\Calendario\UpdateEventoRequest;
use App\Models\EventoCalendario;
use App\Models\EventoRapido;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CalendarioController extends Controller
{
    public function index(): View
    {
        return view('calendario.index');
    }

    /**
     * Eventos en formato de FullCalendar para el rango visible (start/end,
     * enviados automáticamente por el propio calendario al navegar).
     */
    public function eventosJson(Request $request): JsonResponse
    {
        $inicio = Carbon::parse($request->query('start'))->startOfDay();
        $fin = Carbon::parse($request->query('end'))->endOfDay();

        $eventos = EventoCalendario::query()
            ->whereDate('fecha_inicio', '<=', $fin)
            ->where(function ($q) use ($inicio) {
                $q->whereDate('fecha_fin', '>=', $inicio)
                    ->orWhere(function ($q2) use ($inicio) {
                        $q2->whereNull('fecha_fin')->whereDate('fecha_inicio', '>=', $inicio);
                    });
            })
            ->get();

        return response()->json($eventos->map(fn (EventoCalendario $evento) => $this->aFullCalendar($evento)));
    }

    private function aFullCalendar(EventoCalendario $evento): array
    {
        $fin = $evento->fecha_fin ?? $evento->fecha_inicio;
        $todoElDia = ! $evento->hora_inicio;

        return [
            'id' => $evento->id,
            'title' => Str::title($evento->nombre),
            'start' => $todoElDia
                ? $evento->fecha_inicio->format('Y-m-d')
                : $evento->fecha_inicio->format('Y-m-d').'T'.$evento->hora_inicio,
            'end' => $todoElDia
                ? $fin->copy()->addDay()->format('Y-m-d')
                : $fin->format('Y-m-d').'T'.($evento->hora_fin ?? $evento->hora_inicio),
            'allDay' => $todoElDia,
            'color' => $evento->color,
            'extendedProps' => [
                'ubicacion' => $evento->ubicacion,
                'asociado_tipo' => $evento->tipo_asociado,
                'invitados' => $evento->invitados ?? [],
                'notas' => $evento->notas,
                'hora_inicio' => $evento->hora_inicio,
                'hora_fin' => $evento->hora_fin,
                'fecha_inicio_real' => $evento->fecha_inicio->format('Y-m-d'),
                'fecha_fin_real' => $fin->format('Y-m-d'),
                'grupo_recurrencia' => $evento->grupo_recurrencia_id,
            ],
        ];
    }

    public function store(StoreEventoRequest $request): JsonResponse
    {
        $datos = $request->safe()->except(['es_recurrente', 'dias_semana']);
        $datos['creado_por_nombre'] = Auth::user()->name;
        $datos['registrado_por'] = Auth::id();
        $datos['color'] = $datos['color'] ?? '#135c46';

        if (! $request->boolean('es_recurrente')) {
            $evento = EventoCalendario::create($datos);

            return response()->json($this->aFullCalendar($evento));
        }

        $grupoRecurrencia = Str::uuid()->toString();
        $diasSemana = $request->input('dias_semana', []);
        $periodo = CarbonPeriod::create($datos['fecha_inicio'], $datos['fecha_fin']);
        $creados = 0;

        foreach ($periodo as $dia) {
            if (! in_array($dia->dayOfWeek, $diasSemana, true)) {
                continue;
            }

            EventoCalendario::create([
                ...$datos,
                'fecha_inicio' => $dia->format('Y-m-d'),
                'fecha_fin' => null,
                'grupo_recurrencia_id' => $grupoRecurrencia,
            ]);
            $creados++;
        }

        return response()->json(['creados' => $creados]);
    }

    public function update(UpdateEventoRequest $request, EventoCalendario $evento): JsonResponse
    {
        $evento->update($request->validated());

        return response()->json($this->aFullCalendar($evento->fresh()));
    }

    public function destroy(EventoCalendario $evento): JsonResponse
    {
        $evento->delete();

        return response()->json(['eliminado' => true]);
    }

    public function destroySerie(EventoCalendario $evento): JsonResponse
    {
        $eliminados = EventoCalendario::where('grupo_recurrencia_id', $evento->grupo_recurrencia_id)->delete();

        return response()->json(['eliminados' => $eliminados]);
    }

    public function rapidosIndex(): JsonResponse
    {
        return response()->json(EventoRapido::orderBy('orden')->get());
    }

    public function rapidosStore(StoreEventoRapidoRequest $request): JsonResponse
    {
        $ultimoOrden = (int) EventoRapido::max('orden');

        $rapido = EventoRapido::create([...$request->validated(), 'orden' => $ultimoOrden + 1]);

        return response()->json($rapido);
    }

    public function rapidosUpdate(StoreEventoRapidoRequest $request, EventoRapido $rapido): JsonResponse
    {
        $rapido->update($request->validated());

        return response()->json($rapido);
    }

    public function rapidosDestroy(EventoRapido $rapido): JsonResponse
    {
        $rapido->delete();

        return response()->json(['eliminado' => true]);
    }
}
