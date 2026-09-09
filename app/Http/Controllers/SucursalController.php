<?php

namespace App\Http\Controllers;

use App\Http\Requests\Sucursales\StoreSucursalRequest;
use App\Http\Requests\Sucursales\UpdateIdentificacionRequest;
use App\Models\Sucursal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SucursalController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Sucursal::class);

        $sucursales = Sucursal::query()
            ->with(['ubicacion.alcaldia', 'finanzas', 'titular'])
            ->orderBy('nombre_oficial')
            ->when(request('buscar'), function ($query, $buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('nombre_oficial', 'like', "%{$buscar}%")
                        ->orWhere('clave_financiera', 'like', "%{$buscar}%");
                });
            })
            ->paginate(15)
            ->withQueryString();

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
