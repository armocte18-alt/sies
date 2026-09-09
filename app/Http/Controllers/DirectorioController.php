<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAreaCentralRequest;
use App\Http\Requests\StoreGerenciaRequest;
use App\Http\Requests\StorePersonalExternoRequest;
use App\Http\Requests\UpdateAreaCentralRequest;
use App\Http\Requests\UpdateGerenciaRequest;
use App\Http\Requests\UpdatePersonalExternoRequest;
use App\Models\AreaCentral;
use App\Models\Gerencia;
use App\Models\PersonalExterno;
use App\Models\Sucursal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DirectorioController extends Controller
{
    public function index(): View
    {
        return view('directorios.index', [
            'sucursales' => Sucursal::with(['ubicacion.alcaldia', 'operacion'])->orderBy('clave_financiera')->get(),
            'gerencias' => Gerencia::with('creador')->orderBy('nombre')->get(),
            'areas' => AreaCentral::with('gerencia')->orderBy('nombre')->get(),
            'externos' => PersonalExterno::orderBy('nombre')->get(),
        ]);
    }

    // --- Gerencias ---

    public function storeGerencia(StoreGerenciaRequest $request): RedirectResponse
    {
        Gerencia::create($request->validated() + [
            'activo' => true,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return back()->with('status', 'Gerencia registrada en el directorio.');
    }

    public function updateGerencia(UpdateGerenciaRequest $request, Gerencia $gerencia): RedirectResponse
    {
        $gerencia->update($request->validated() + ['updated_by' => Auth::id()]);

        return back()->with('status', 'Gerencia actualizada.');
    }

    public function toggleGerencia(Gerencia $gerencia): RedirectResponse
    {
        abort_unless(auth()->user()->can('directorios.gestionar'), 403);

        $gerencia->update(['activo' => ! $gerencia->activo, 'updated_by' => Auth::id()]);

        return back()->with('status', $gerencia->activo ? 'Gerencia reactivada.' : 'Gerencia dada de baja del directorio.');
    }

    // --- Áreas centrales ---

    public function storeAreaCentral(StoreAreaCentralRequest $request): RedirectResponse
    {
        AreaCentral::create($request->validated() + [
            'activo' => true,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return back()->with('status', 'Área central registrada en el directorio.');
    }

    public function updateAreaCentral(UpdateAreaCentralRequest $request, AreaCentral $areaCentral): RedirectResponse
    {
        $areaCentral->update($request->validated() + ['updated_by' => Auth::id()]);

        return back()->with('status', 'Área central actualizada.');
    }

    public function toggleAreaCentral(AreaCentral $areaCentral): RedirectResponse
    {
        abort_unless(auth()->user()->can('directorios.gestionar'), 403);

        $areaCentral->update(['activo' => ! $areaCentral->activo, 'updated_by' => Auth::id()]);

        return back()->with('status', $areaCentral->activo ? 'Área central reactivada.' : 'Área central dada de baja del directorio.');
    }

    // --- Personal externo ---

    public function storePersonalExterno(StorePersonalExternoRequest $request): RedirectResponse
    {
        PersonalExterno::create($request->validated() + [
            'activo' => true,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return back()->with('status', 'Contacto externo registrado en el directorio.');
    }

    public function updatePersonalExterno(UpdatePersonalExternoRequest $request, PersonalExterno $personalExterno): RedirectResponse
    {
        $personalExterno->update($request->validated() + ['updated_by' => Auth::id()]);

        return back()->with('status', 'Contacto externo actualizado.');
    }

    public function togglePersonalExterno(PersonalExterno $personalExterno): RedirectResponse
    {
        abort_unless(auth()->user()->can('directorios.gestionar'), 403);

        $personalExterno->update(['activo' => ! $personalExterno->activo, 'updated_by' => Auth::id()]);

        return back()->with('status', $personalExterno->activo ? 'Contacto reactivado.' : 'Contacto dado de baja del directorio.');
    }
}
