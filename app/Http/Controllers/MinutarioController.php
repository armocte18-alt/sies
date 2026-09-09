<?php

namespace App\Http\Controllers;

use App\Http\Requests\Minutarios\CancelOficioRequest;
use App\Http\Requests\Minutarios\StoreBoletinRequest;
use App\Http\Requests\Minutarios\StoreOficioRequest;
use App\Http\Requests\Minutarios\UpdateBoletinRequest;
use App\Http\Requests\Minutarios\UpdateOficioRequest;
use App\Models\MinutarioBoletin;
use App\Models\MinutarioOficio;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MinutarioController extends Controller
{
    public function index(): View
    {
        $boletines = MinutarioBoletin::with('registrador')->orderByDesc('anio')->orderByDesc('consecutivo')->get();
        $oficios = MinutarioOficio::with('registrador')->orderByDesc('anio')->orderByDesc('consecutivo')->get();

        return view('minutarios.index', compact('boletines', 'oficios'));
    }

    public function storeBoletin(StoreBoletinRequest $request): RedirectResponse
    {
        $boletin = $this->crearConFolio(MinutarioBoletin::class, 'B-%04d/%d', [
            ...$request->validated(),
            'fecha_elaboracion' => now()->format('Y-m-d'),
            'registrado_por' => Auth::id(),
        ]);

        return back()->with('status', "Boletín {$boletin->nomenclatura} registrado correctamente.");
    }

    public function updateBoletin(UpdateBoletinRequest $request, MinutarioBoletin $boletin): RedirectResponse
    {
        $boletin->update([
            ...$request->validated(),
            'fecha_ultima_modificacion' => now(),
        ]);

        return back()->with('status', "Boletín {$boletin->nomenclatura} actualizado correctamente.");
    }

    public function storeOficio(StoreOficioRequest $request): RedirectResponse
    {
        $oficio = $this->crearConFolio(MinutarioOficio::class, '4120.-%04d/%d', [
            ...$request->validated(),
            'fecha_emision' => now()->format('Y-m-d'),
            'registrado_por' => Auth::id(),
        ]);

        return back()->with('status', "Oficio {$oficio->nomenclatura} registrado correctamente.");
    }

    public function updateOficio(UpdateOficioRequest $request, MinutarioOficio $oficio): RedirectResponse
    {
        $oficio->update($request->validated());

        return back()->with('status', "Oficio {$oficio->nomenclatura} actualizado correctamente.");
    }

    public function cancelOficio(CancelOficioRequest $request, MinutarioOficio $oficio): RedirectResponse
    {
        $oficio->update([
            'es_cancelado' => true,
            'motivo_cancelacion' => $request->validated('motivo_cancelacion'),
            'cancelado_por_nombre' => Auth::user()->name,
            'fecha_cancelacion' => now(),
        ]);

        return back()->with('status', "Oficio {$oficio->nomenclatura} marcado como cancelado.");
    }

    public function toggleEscaneoOficio(MinutarioOficio $oficio): RedirectResponse
    {
        $oficio->update(['ya_escaneado' => ! $oficio->ya_escaneado]);

        return back()->with('status', $oficio->ya_escaneado ? 'Oficio marcado como escaneado.' : 'Oficio marcado como pendiente de escanear.');
    }

    /**
     * Reserva el folio (consecutivo/año) de forma atómica y crea el registro,
     * igual que sios-app-web: nomenclatura = "{prefijo}-{consecutivo}/{año}".
     *
     * @template TModel of \Illuminate\Database\Eloquent\Model
     *
     * @param  class-string<TModel>  $modelo
     * @return TModel
     */
    private function crearConFolio(string $modelo, string $formato, array $datos)
    {
        return DB::transaction(function () use ($modelo, $formato, $datos) {
            $anio = (int) now()->year;
            $ultimoConsecutivo = (int) $modelo::where('anio', $anio)->lockForUpdate()->max('consecutivo');
            $consecutivo = $ultimoConsecutivo + 1;

            return $modelo::create([
                ...$datos,
                'consecutivo' => $consecutivo,
                'anio' => $anio,
                'nomenclatura' => sprintf($formato, $consecutivo, $anio),
            ]);
        });
    }
}
