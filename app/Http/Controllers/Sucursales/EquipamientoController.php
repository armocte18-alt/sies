<?php

namespace App\Http\Controllers\Sucursales;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sucursales\StoreActivoTiRequest;
use App\Http\Requests\Sucursales\UpdateActivoTiRequest;
use App\Http\Requests\Sucursales\UpdateEquipamientoRequest;
use App\Models\ActivoTi;
use App\Models\Sucursal;
use Illuminate\Http\RedirectResponse;

class EquipamientoController extends Controller
{
    public function update(UpdateEquipamientoRequest $request, Sucursal $sucursal): RedirectResponse
    {
        $sucursal->equipamiento()->updateOrCreate([], $request->validated());
        $sucursal->touch();

        return back()->with('status', 'Equipamiento técnico actualizado.');
    }

    public function storeActivo(StoreActivoTiRequest $request, Sucursal $sucursal): RedirectResponse
    {
        $sucursal->activosTi()->create($request->validated());

        return back()->with('status', 'Activo de TI registrado.');
    }

    public function updateActivo(UpdateActivoTiRequest $request, Sucursal $sucursal, ActivoTi $activo): RedirectResponse
    {
        abort_unless($activo->sucursal_id === $sucursal->id, 404);

        $activo->update($request->validated());

        return back()->with('status', 'Activo de TI actualizado.');
    }

    public function destroyActivo(Sucursal $sucursal, ActivoTi $activo): RedirectResponse
    {
        $this->authorize('updateEquipamiento', $sucursal);
        abort_unless($activo->sucursal_id === $sucursal->id, 404);

        $activo->delete();

        return back()->with('status', 'Activo de TI eliminado.');
    }
}
