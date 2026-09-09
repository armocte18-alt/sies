<?php

namespace App\Http\Controllers;

use App\Models\Alcaldia;
use App\Models\Empleado;
use App\Models\Sucursal;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $sucursalesActivas = Sucursal::where('estatus_operativo', 'activa')->count();
        $totalEmpleados = Empleado::where('activo', true)->count();

        $balanceAcumulado = Sucursal::query()
            ->join('sucursal_finanzas', 'sucursal_finanzas.sucursal_id', '=', 'sucursales.id')
            ->selectRaw('COALESCE(SUM(ingreso_estimado - gasto_total), 0) as balance')
            ->value('balance');

        $sucursalesPorAlcaldia = Alcaldia::query()
            ->withCount(['ubicaciones as sucursales_count'])
            ->orderBy('nombre')
            ->get()
            ->filter(fn (Alcaldia $alcaldia) => $alcaldia->sucursales_count > 0)
            ->values();

        return view('dashboard', [
            'sucursalesActivas' => $sucursalesActivas,
            'totalEmpleados' => $totalEmpleados,
            'balanceAcumulado' => (float) $balanceAcumulado,
            'sucursalesPorAlcaldia' => $sucursalesPorAlcaldia,
            'sucursales' => Sucursal::with('ubicacion')->get(),
        ]);
    }
}
