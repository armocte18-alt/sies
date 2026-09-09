<?php

namespace App\Http\Controllers\Sucursales;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sucursales\StoreCombustibleRequest;
use App\Http\Requests\Sucursales\StoreEquipamientoRepartoRequest;
use App\Http\Requests\Sucursales\StoreMotocicletaRequest;
use App\Http\Requests\Sucursales\UpdateEquipamientoRepartoRequest;
use App\Http\Requests\Sucursales\UpdateMotocicletaRequest;
use App\Models\Sucursal;
use App\Models\SucursalEquipamientoReparto;
use App\Models\SucursalMotocicleta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class RepartoController extends Controller
{
    // --- Motocicletas ---

    public function storeMotocicleta(StoreMotocicletaRequest $request, Sucursal $sucursal): RedirectResponse
    {
        $sucursal->motocicletas()->create($request->validated());

        return back()->with('status', 'Motocicleta registrada.');
    }

    public function updateMotocicleta(UpdateMotocicletaRequest $request, Sucursal $sucursal, SucursalMotocicleta $motocicleta): RedirectResponse
    {
        abort_unless($motocicleta->sucursal_id === $sucursal->id, 404);

        $motocicleta->update($request->validated());

        return back()->with('status', 'Motocicleta actualizada.');
    }

    public function destroyMotocicleta(Sucursal $sucursal, SucursalMotocicleta $motocicleta): RedirectResponse
    {
        $this->authorize('updateReparto', $sucursal);
        abort_unless($motocicleta->sucursal_id === $sucursal->id, 404);

        $motocicleta->delete();

        return back()->with('status', 'Motocicleta eliminada.');
    }

    // --- Cargas de combustible ---

    public function storeCombustible(StoreCombustibleRequest $request, Sucursal $sucursal, SucursalMotocicleta $motocicleta): RedirectResponse
    {
        abort_unless($motocicleta->sucursal_id === $sucursal->id, 404);

        $motocicleta->cargasCombustible()->create($request->validated() + ['created_by' => Auth::id()]);

        if ($request->filled('kilometraje') && $request->integer('kilometraje') > $motocicleta->kilometraje_actual) {
            $motocicleta->update(['kilometraje_actual' => $request->integer('kilometraje')]);
        }

        return back()->with('status', 'Carga de combustible registrada.');
    }

    // --- Equipamiento de reparto (EPP) ---

    public function storeEquipamiento(StoreEquipamientoRepartoRequest $request, Sucursal $sucursal): RedirectResponse
    {
        $sucursal->equipamientoReparto()->create($request->validated());

        return back()->with('status', 'Equipamiento de reparto registrado.');
    }

    public function updateEquipamiento(UpdateEquipamientoRepartoRequest $request, Sucursal $sucursal, SucursalEquipamientoReparto $equipamientoReparto): RedirectResponse
    {
        abort_unless($equipamientoReparto->sucursal_id === $sucursal->id, 404);

        $equipamientoReparto->update($request->validated());

        return back()->with('status', 'Equipamiento de reparto actualizado.');
    }

    public function destroyEquipamiento(Sucursal $sucursal, SucursalEquipamientoReparto $equipamientoReparto): RedirectResponse
    {
        $this->authorize('updateReparto', $sucursal);
        abort_unless($equipamientoReparto->sucursal_id === $sucursal->id, 404);

        $equipamientoReparto->delete();

        return back()->with('status', 'Equipamiento de reparto eliminado.');
    }
}
