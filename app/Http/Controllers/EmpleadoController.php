<?php

namespace App\Http\Controllers;

use App\Http\Requests\Empleados\StoreEmpleadoRequest;
use App\Http\Requests\Empleados\UpdateEmpleadoRequest;
use App\Models\Empleado;
use App\Models\Sucursal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmpleadoController extends Controller
{
    private const COLUMNAS_ORDENABLES = [
        'no_empleado' => 'empleados.no_empleado',
        'nombre' => 'empleados.nombre',
        'sucursal' => 'sucursales.nombre_oficial',
    ];

    public function index(Request $request): View
    {
        $columna = self::COLUMNAS_ORDENABLES[$request->query('sort')] ?? 'empleados.nombre';
        $direccion = $request->query('direction') === 'desc' ? 'desc' : 'asc';

        $empleados = Empleado::query()
            ->select('empleados.*')
            ->leftJoin('sucursales', 'sucursales.id', '=', 'empleados.sucursal_id')
            ->with('sucursal')
            ->when($request->query('buscar'), function ($query, $buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('empleados.no_empleado', 'like', "%{$buscar}%")
                        ->orWhere('empleados.nombre', 'like', "%{$buscar}%")
                        ->orWhere('empleados.apellido_paterno', 'like', "%{$buscar}%")
                        ->orWhere('empleados.apellido_materno', 'like', "%{$buscar}%")
                        ->orWhere('empleados.puesto', 'like', "%{$buscar}%");
                });
            })
            ->when($request->query('sucursal'), fn ($query, $sucursalId) => $query->where('empleados.sucursal_id', $sucursalId))
            ->orderBy($columna, $direccion)
            ->paginate(20)
            ->withQueryString();

        if ($request->ajax()) {
            return view('empleados.partials.tabla', compact('empleados'));
        }

        $sucursales = Sucursal::orderBy('clave_financiera')->get(['id', 'nombre_oficial', 'clave_financiera']);

        return view('empleados.index', compact('empleados', 'sucursales'));
    }

    public function store(StoreEmpleadoRequest $request): RedirectResponse
    {
        Empleado::create($request->validated());

        return back()->with('status', 'Empleado registrado correctamente.');
    }

    public function update(UpdateEmpleadoRequest $request, Empleado $empleado): RedirectResponse
    {
        $empleado->update($request->validated());

        return back()->with('status', 'Empleado actualizado correctamente.');
    }

    public function toggleActive(Empleado $empleado): RedirectResponse
    {
        $empleado->update(['activo' => ! $empleado->activo]);

        return back()->with('status', $empleado->activo ? 'Empleado reactivado.' : 'Empleado dado de baja.');
    }
}
