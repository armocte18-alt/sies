<?php

namespace App\Http\Controllers;

use App\Http\Requests\Calendario\StoreEventoRequest;
use App\Models\EventoCalendario;
use App\Models\EventoRapido;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CalendarioController extends Controller
{
    private const MESES = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
        7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
    ];

    public function index(Request $request): View
    {
        $anio = (int) ($request->query('anio') ?: now()->year);
        $mes = (int) ($request->query('mes') ?: now()->month);
        $mes = max(1, min(12, $mes));

        $inicioMes = Carbon::create($anio, $mes, 1)->startOfMonth();
        $finMes = $inicioMes->copy()->endOfMonth();
        $inicioGrid = $inicioMes->copy()->startOfWeek(Carbon::SUNDAY);
        $finGrid = $finMes->copy()->endOfWeek(Carbon::SUNDAY);

        $eventos = EventoCalendario::query()
            ->whereDate('fecha_inicio', '<=', $finGrid)
            ->where(function ($q) use ($inicioGrid) {
                $q->whereDate('fecha_fin', '>=', $inicioGrid)
                    ->orWhere(function ($q2) use ($inicioGrid) {
                        $q2->whereNull('fecha_fin')->whereDate('fecha_inicio', '>=', $inicioGrid);
                    });
            })
            ->orderBy('hora_inicio')
            ->get();

        $eventosPorDia = [];
        foreach ($eventos as $evento) {
            $inicio = $evento->fecha_inicio->copy();
            $fin = ($evento->fecha_fin ?? $evento->fecha_inicio)->copy();

            for ($dia = $inicio->copy(); $dia->lte($fin); $dia->addDay()) {
                if ($dia->between($inicioGrid, $finGrid)) {
                    $eventosPorDia[$dia->format('Y-m-d')][] = $evento;
                }
            }
        }

        $semanas = [];
        $semana = [];
        for ($dia = $inicioGrid->copy(); $dia->lte($finGrid); $dia->addDay()) {
            $semana[] = [
                'fecha' => $dia->copy(),
                'esMesActual' => $dia->month === $mes,
                'esHoy' => $dia->isToday(),
                'eventos' => $eventosPorDia[$dia->format('Y-m-d')] ?? [],
            ];

            if ($dia->dayOfWeek === Carbon::SATURDAY) {
                $semanas[] = $semana;
                $semana = [];
            }
        }

        $mesAnterior = $inicioMes->copy()->subMonth();
        $mesSiguiente = $inicioMes->copy()->addMonth();

        $eventosRapidos = EventoRapido::orderBy('orden')->get();

        return view('calendario.index', [
            'semanas' => $semanas,
            'tituloMes' => self::MESES[$mes].' '.$anio,
            'anio' => $anio,
            'mes' => $mes,
            'mesAnterior' => $mesAnterior,
            'mesSiguiente' => $mesSiguiente,
            'eventosRapidos' => $eventosRapidos,
        ]);
    }

    public function store(StoreEventoRequest $request): RedirectResponse
    {
        EventoCalendario::create([
            ...$request->safe()->except('invitados'),
            'invitados' => $this->invitadosDesdeTexto($request->input('invitados')),
            'creado_por_nombre' => Auth::user()->name,
            'registrado_por' => Auth::id(),
        ]);

        return back()->with('status', 'Evento agregado al calendario.');
    }

    public function destroy(EventoCalendario $evento): RedirectResponse
    {
        $evento->delete();

        return back()->with('status', 'Evento eliminado.');
    }

    private function invitadosDesdeTexto(?string $texto): ?array
    {
        if (! $texto) {
            return null;
        }

        $invitados = array_filter(array_map('trim', explode(',', $texto)));

        return $invitados ?: null;
    }
}
