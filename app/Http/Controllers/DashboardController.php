<?php

namespace App\Http\Controllers;

use App\Models\Alcaldia;
use App\Models\Empleado;
use App\Models\EventoCalendario;
use App\Models\Sucursal;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $sucursalesActivas = Sucursal::where('estatus_operativo', 'activa')->count();
        $totalEmpleados = Empleado::where('activo', true)->count();

        $limiteExistenciaCajaTotal = Sucursal::query()
            ->join('sucursal_finanzas', 'sucursal_finanzas.sucursal_id', '=', 'sucursales.id')
            ->selectRaw('COALESCE(SUM(limite_existencia_caja), 0) as total')
            ->value('total');

        $sucursalesPorAlcaldia = Alcaldia::query()
            ->withCount(['ubicaciones as sucursales_count'])
            ->orderBy('nombre')
            ->get()
            ->filter(fn (Alcaldia $alcaldia) => $alcaldia->sucursales_count > 0)
            ->values();

        $eventosProximos = EventoCalendario::query()
            ->whereBetween('fecha_inicio', [now()->startOfDay(), now()->addDays(7)->endOfDay()])
            ->count();

        return view('dashboard', [
            'sucursalesActivas' => $sucursalesActivas,
            'totalEmpleados' => $totalEmpleados,
            'limiteExistenciaCajaTotal' => (float) $limiteExistenciaCajaTotal,
            'sucursalesPorAlcaldia' => $sucursalesPorAlcaldia,
            'sucursales' => Sucursal::with('ubicacion')->get(),
            'eventosProximos' => $eventosProximos,
        ]);
    }
}
