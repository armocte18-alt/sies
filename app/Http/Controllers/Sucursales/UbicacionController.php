<?php

namespace App\Http\Controllers\Sucursales;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sucursales\UpdateUbicacionRequest;
use App\Models\Sucursal;
use Illuminate\Http\RedirectResponse;

class UbicacionController extends Controller
{
    public function update(UpdateUbicacionRequest $request, Sucursal $sucursal): RedirectResponse
    {
        $sucursal->ubicacion()->updateOrCreate([], $request->validated());
        $sucursal->touch();

        return back()->with('status', 'Ubicación geográfica actualizada.');
    }
}
