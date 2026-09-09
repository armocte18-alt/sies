<?php

namespace App\Http\Controllers\Sucursales;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sucursales\UpdateFinanzasRequest;
use App\Models\Sucursal;
use Illuminate\Http\RedirectResponse;

class FinanzasController extends Controller
{
    public function update(UpdateFinanzasRequest $request, Sucursal $sucursal): RedirectResponse
    {
        $sucursal->finanzas()->updateOrCreate([], $request->validated());
        $sucursal->touch();

        return back()->with('status', 'Información financiera actualizada.');
    }
}
