<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCatalogoItemRequest;
use App\Http\Requests\StoreNivelSalarialRequest;
use App\Http\Requests\UpdateCatalogoItemRequest;
use App\Http\Requests\UpdateNivelSalarialRequest;
use App\Models\NivelSalarial;
use App\Support\CatalogoRhRegistro;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CatalogosRhController extends Controller
{
    public function index(): View
    {
        $catalogos = collect(CatalogoRhRegistro::MODELOS)
            ->mapWithKeys(fn (string $modelo, string $clave) => [
                $clave => $modelo::orderBy('orden')->orderBy('nombre')->get(),
            ]);

        return view('rh.catalogos.index', [
            'nivelesSalariales' => NivelSalarial::orderBy('consecutivo')->get(),
            'catalogos' => $catalogos,
        ]);
    }

    public function storeNivelSalarial(StoreNivelSalarialRequest $request): RedirectResponse
    {
        NivelSalarial::create($request->validated() + ['activo' => true]);

        return back()->with('status', 'Nivel salarial agregado.');
    }

    public function updateNivelSalarial(UpdateNivelSalarialRequest $request, NivelSalarial $nivelSalarial): RedirectResponse
    {
        $nivelSalarial->update($request->validated());

        return back()->with('status', 'Nivel salarial actualizado.');
    }

    public function destroyNivelSalarial(NivelSalarial $nivelSalarial): RedirectResponse
    {
        $nivelSalarial->delete();

        return back()->with('status', 'Nivel salarial eliminado.');
    }

    public function storeItem(StoreCatalogoItemRequest $request, string $catalogo): RedirectResponse
    {
        $modelo = CatalogoRhRegistro::modelo($catalogo);

        $modelo::create($request->validated() + ['activo' => true]);

        return back()->with('status', 'Elemento agregado al catálogo.');
    }

    public function updateItem(UpdateCatalogoItemRequest $request, string $catalogo, int $id): RedirectResponse
    {
        $modelo = CatalogoRhRegistro::modelo($catalogo);

        $modelo::findOrFail($id)->update($request->validated());

        return back()->with('status', 'Elemento del catálogo actualizado.');
    }

    public function destroyItem(string $catalogo, int $id): RedirectResponse
    {
        CatalogoRhRegistro::modelo($catalogo)::findOrFail($id)->delete();

        return back()->with('status', 'Elemento del catálogo eliminado.');
    }
}
