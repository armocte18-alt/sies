<?php

namespace App\Http\Controllers;

use App\Http\Requests\Sucursales\StoreSucursalRequest;
use App\Http\Requests\Sucursales\UpdateIdentificacionRequest;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SucursalController extends Controller
{
    private const COLUMNAS_ORDENABLES = [
        'nombre' => 'sucursales.nombre_oficial',
        'clave' => 'sucursales.clave_financiera',
        'alcaldia' => 'alcaldias.nombre',
        'estatus' => 'sucursales.estatus_operativo',
    ];

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Sucursal::class);

        $columna = self::COLUMNAS_ORDENABLES[$request->query('sort')] ?? 'sucursales.clave_financiera';
        $direccion = $request->query('direction') === 'desc' ? 'desc' : 'asc';

        $sucursales = Sucursal::query()
            ->select('sucursales.*')
            ->leftJoin('sucursal_ubicaciones', 'sucursal_ubicaciones.sucursal_id', '=', 'sucursales.id')
            ->leftJoin('alcaldias', 'alcaldias.id', '=', 'sucursal_ubicaciones.alcaldia_id')
            ->with(['ubicacion.alcaldia', 'finanzas', 'titular'])
            ->when($request->query('buscar'), function ($query, $buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('sucursales.nombre_oficial', 'like', "%{$buscar}%")
                        ->orWhere('sucursales.clave_financiera', 'like', "%{$buscar}%");
                });
            })
            ->when($request->query('alcaldia'), fn ($query, $alcaldiaId) => $query->where('alcaldias.id', $alcaldiaId))
            ->orderBy($columna, $direccion)
            ->paginate(15)
            ->withQueryString();

        if ($request->ajax()) {
            return view('sucursales.partials.tabla', compact('sucursales'));
        }

        return view('sucursales.index', compact('sucursales'));
    }

    public function create(): View
    {
        $this->authorize('create', Sucursal::class);

        return view('sucursales.create');
    }

    public function store(StoreSucursalRequest $request): RedirectResponse
    {
        $sucursal = Sucursal::create([
            ...$request->validated(),
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()
            ->route('sucursales.show', $sucursal)
            ->with('status', 'Sucursal creada correctamente. Completa el resto de las secciones desde cada coordinación.');
    }

    public function show(Sucursal $sucursal): View
    {
        $this->authorize('view', $sucursal);

        $sucursal->load([
            'ubicacion.alcaldia',
            'operacion',
            'inmueble',
            'equipamiento',
            'activosTi' => fn ($q) => $q->orderBy('tipo'),
            'finanzas',
            'empleados' => fn ($q) => $q->orderBy('funcion_laboral'),
            'titular',
            'motocicletas' => fn ($q) => $q->orderBy('placa'),
            'motocicletas.cargasCombustible',
            'equipamientoReparto' => fn ($q) => $q->orderBy('tipo'),
        ]);

        return view('sucursales.show', compact('sucursal'));
    }

    public function updateIdentificacion(UpdateIdentificacionRequest $request, Sucursal $sucursal): RedirectResponse
    {
        $sucursal->update([
            ...$request->validated(),
            'updated_by' => Auth::id(),
        ]);

        return back()->with('status', 'Cédula de identificación actualizada.');
    }
}
