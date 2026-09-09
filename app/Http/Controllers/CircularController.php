<?php

namespace App\Http\Controllers;

use App\Http\Requests\Circulares\StoreCircularRequest;
use App\Http\Requests\Circulares\UpdateCircularRequest;
use App\Models\Circular;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CircularController extends Controller
{
    private const COLUMNAS_ORDENABLES = [
        'numero' => 'numero',
        'fecha' => 'fecha_aplicacion',
        'ambito' => 'ambito',
    ];

    public function index(Request $request): View
    {
        $columna = self::COLUMNAS_ORDENABLES[$request->query('sort')] ?? 'fecha_aplicacion';
        $direccion = $request->query('direction') === 'asc' ? 'asc' : 'desc';

        $circulares = Circular::query()
            ->when($request->query('buscar'), function ($query, $buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('numero', 'like', "%{$buscar}%")
                        ->orWhere('asunto', 'like', "%{$buscar}%");
                });
            })
            ->orderBy($columna, $direccion)
            ->paginate(20)
            ->withQueryString();

        if ($request->ajax()) {
            return view('circulares.partials.tabla', compact('circulares'));
        }

        return view('circulares.index', compact('circulares'));
    }

    public function store(StoreCircularRequest $request): RedirectResponse
    {
        Circular::create([
            ...$request->safe()->except('archivo'),
            'archivo_path' => $this->guardarArchivo($request),
            'registrado_por' => Auth::id(),
        ]);

        return back()->with('status', 'Circular registrada correctamente.');
    }

    public function update(UpdateCircularRequest $request, Circular $circular): RedirectResponse
    {
        $archivoPath = $this->guardarArchivo($request);

        if ($archivoPath && $circular->archivo_path) {
            Storage::disk('public')->delete($circular->archivo_path);
        }

        $circular->update([
            ...$request->safe()->except('archivo'),
            'archivo_path' => $archivoPath ?: $circular->archivo_path,
        ]);

        return back()->with('status', 'Circular actualizada correctamente.');
    }

    public function toggleActive(Circular $circular): RedirectResponse
    {
        $circular->update(['activo' => ! $circular->activo]);

        return back()->with('status', $circular->activo ? 'Circular reactivada.' : 'Circular dada de baja.');
    }

    private function guardarArchivo(Request $request): ?string
    {
        if (! $request->hasFile('archivo')) {
            return null;
        }

        return $request->file('archivo')->store('circulares', 'public');
    }
}
