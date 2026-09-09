<?php

namespace App\Http\Controllers;

use App\Models\IndicadorSucursalMensual;
use App\Models\MetaMensual;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MetasAnalisisController extends Controller
{
    private const COLUMNAS_ORDENABLES = [
        'sucursal' => 'nombre_sucursal_legacy',
        'volumen' => 'volumen_total',
        'ingresos' => 'ingresos_total',
        'gasto' => 'gasto_total',
        'balance' => 'balance',
        'productividad' => 'productividad',
    ];

    public function index(Request $request): View
    {
        $metas = MetaMensual::orderByDesc('anio')->orderBy('linea_negocio')->get();

        $anioSeleccionado = (int) ($request->query('anio') ?: IndicadorSucursalMensual::max('anio'));
        $mesSeleccionado = (int) ($request->query('mes') ?: (
            IndicadorSucursalMensual::where('anio', $anioSeleccionado)->max('mes') ?? 1
        ));

        $columna = self::COLUMNAS_ORDENABLES[$request->query('sort')] ?? 'volumen_total';
        $direccion = $request->query('direction') === 'asc' ? 'asc' : 'desc';

        $indicadores = IndicadorSucursalMensual::query()
            ->with('sucursal')
            ->where('anio', $anioSeleccionado)
            ->where('mes', $mesSeleccionado)
            ->when($request->query('buscar'), function ($query, $buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('nombre_sucursal_legacy', 'like', "%{$buscar}%")
                        ->orWhere('clave_sucursal_legacy', 'like', "%{$buscar}%");
                });
            })
            ->orderBy($columna, $direccion)
            ->paginate(20)
            ->withQueryString();

        $resumenMes = IndicadorSucursalMensual::query()
            ->where('anio', $anioSeleccionado)
            ->where('mes', $mesSeleccionado)
            ->selectRaw('COALESCE(SUM(volumen_total),0) as volumen, COALESCE(SUM(ingresos_total),0) as ingresos, COALESCE(SUM(gasto_total),0) as gasto, COALESCE(SUM(balance),0) as balance')
            ->first();

        $aniosDisponibles = IndicadorSucursalMensual::query()->distinct()->orderByDesc('anio')->pluck('anio');
        $mesesDisponibles = IndicadorSucursalMensual::query()
            ->where('anio', $anioSeleccionado)
            ->distinct()->orderBy('mes')->pluck('mes');

        if ($request->ajax()) {
            return view('metas.partials.tabla', compact('indicadores'));
        }

        return view('metas.index', [
            'metas' => $metas,
            'indicadores' => $indicadores,
            'resumenMes' => $resumenMes,
            'anioSeleccionado' => $anioSeleccionado,
            'mesSeleccionado' => $mesSeleccionado,
            'aniosDisponibles' => $aniosDisponibles,
            'mesesDisponibles' => $mesesDisponibles,
            'totalSucursalesActivas' => Sucursal::where('estatus_operativo', 'activa')->count(),
        ]);
    }
}
