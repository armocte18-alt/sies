<?php

namespace App\Http\Controllers\Sucursales;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sucursales\UpdateEquipamientoRequest;
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
}
