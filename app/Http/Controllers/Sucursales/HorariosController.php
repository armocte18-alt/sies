<?php

namespace App\Http\Controllers\Sucursales;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sucursales\UpdateHorariosRequest;
use App\Models\Sucursal;
use Illuminate\Http\RedirectResponse;

class HorariosController extends Controller
{
    public function update(UpdateHorariosRequest $request, Sucursal $sucursal): RedirectResponse
    {
        $sucursal->operacion()->updateOrCreate([], $request->validated());
        $sucursal->touch();

        return back()->with('status', 'Horarios y operación actualizados.');
    }
}
