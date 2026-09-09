<?php

namespace App\Http\Controllers\Sucursales;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sucursales\UpdateInmuebleRequest;
use App\Models\Sucursal;
use Illuminate\Http\RedirectResponse;

class InmuebleController extends Controller
{
    public function update(UpdateInmuebleRequest $request, Sucursal $sucursal): RedirectResponse
    {
        $sucursal->inmueble()->updateOrCreate([], $request->validated());
        $sucursal->touch();

        return back()->with('status', 'Información del inmueble actualizada.');
    }
}
