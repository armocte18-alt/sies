<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tarjetas\AsignarTarjetasRequest;
use App\Http\Requests\Tarjetas\CorregirRenominacionRequest;
use App\Http\Requests\Tarjetas\RetirarPorSucursalRequest;
use App\Http\Requests\Tarjetas\RetirarTarjetaRequest;
use App\Http\Requests\Tarjetas\StoreProductoRequest;
use App\Http\Requests\Tarjetas\StoreTarjetaRequest;
use App\Models\Sucursal;
use App\Models\TarjetaHistorial;
use App\Models\TarjetaInventario;
use App\Models\TarjetaProducto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TarjetaController extends Controller
{
    public function index(Request $request): View
    {
        $productos = TarjetaProducto::orderBy('nombre')->get();
        $productoId = (int) ($request->query('producto') ?: $productos->first()?->id);

        $inventario = TarjetaInventario::with(['producto', 'destinoSucursal'])
            ->when($productoId, fn ($q) => $q->where('producto_id', $productoId))
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $resumen = TarjetaInventario::when($productoId, fn ($q) => $q->where('producto_id', $productoId))
            ->selectRaw("estatus, count(*) as total")
            ->groupBy('estatus')
            ->pluck('total', 'estatus');

        $sucursales = Sucursal::orderBy('clave_financiera')->get(['id', 'nombre_oficial', 'clave_financiera']);

        return view('tarjetas.index', [
            'productos' => $productos,
            'productoId' => $productoId,
            'inventario' => $inventario,
            'resumen' => $resumen,
            'sucursales' => $sucursales,
        ]);
    }

    public function historial(): View
    {
        $historial = TarjetaHistorial::with('tarjeta.producto')->latest()->paginate(30);

        return view('tarjetas.historial', compact('historial'));
    }

    public function storeProducto(StoreProductoRequest $request): RedirectResponse
    {
        TarjetaProducto::create([
            ...$request->validated(),
            'slug' => Str::slug($request->validated('nombre')),
        ]);

        return back()->with('status', 'Producto de tarjeta agregado.');
    }

    public function storeTarjeta(StoreTarjetaRequest $request): RedirectResponse
    {
        TarjetaInventario::create([
            ...$request->validated(),
            'usuario_recibe' => Auth::user()->name,
            'estatus' => 'en_stock',
        ]);

        return back()->with('status', 'Tarjeta dada de alta en el inventario.');
    }

    public function asignar(AsignarTarjetasRequest $request): RedirectResponse
    {
        $datos = $request->validated();

        return DB::transaction(function () use ($datos) {
            $disponibles = TarjetaInventario::where('producto_id', $datos['producto_id'])
                ->where('estatus', 'en_stock')
                ->orderBy('id')
                ->lockForUpdate()
                ->take($datos['cantidad'])
                ->get();

            if ($disponibles->count() < $datos['cantidad']) {
                return back()->withErrors([
                    'cantidad' => "Inventario insuficiente: solo hay {$disponibles->count()} tarjetas en stock de este producto.",
                ]);
            }

            $totalOficios = TarjetaInventario::whereNotNull('numero_oficio')->distinct('numero_oficio')->count('numero_oficio');
            $folio = 'OF-FINABIEN-GCDMX-'.str_pad((string) ($totalOficios + 1), 4, '0', STR_PAD_LEFT).'-'.now()->format('Y');

            TarjetaInventario::whereIn('id', $disponibles->pluck('id'))->update([
                'estatus' => 'activa',
                'fecha_asignacion' => now()->format('Y-m-d'),
                'destino_sucursal_id' => $datos['destino_sucursal_id'] ?? null,
                'otro_destino_descripcion' => $datos['otro_destino_descripcion'] ?? null,
                'usuario_asigna' => Auth::user()->name,
                'numero_oficio' => $folio,
            ]);

            return back()->with('status', "Se asignaron {$disponibles->count()} tarjetas bajo el folio {$folio}.");
        });
    }

    public function retirar(RetirarTarjetaRequest $request, TarjetaInventario $tarjeta): RedirectResponse
    {
        if ($tarjeta->estatus !== 'activa') {
            return back()->withErrors(['estatus' => 'Solo se pueden retirar tarjetas en estatus "Activa".']);
        }

        DB::transaction(function () use ($request, $tarjeta) {
            TarjetaHistorial::create([
                'tarjeta_id' => $tarjeta->id,
                'accion' => 'retiro_individual',
                'estatus_anterior' => 'activa',
                'estatus_nuevo' => 'en_stock',
                'motivo' => $request->validated('motivo'),
                'usuario' => Auth::user()->name,
            ]);

            $tarjeta->update([
                'estatus' => 'en_stock',
                'destino_sucursal_id' => null,
                'otro_destino_descripcion' => null,
                'fecha_asignacion' => null,
                'numero_oficio' => null,
            ]);
        });

        return back()->with('status', 'La tarjeta regresó a "En stock".');
    }

    public function retirarPorSucursal(RetirarPorSucursalRequest $request): RedirectResponse
    {
        $sucursalId = $request->validated('destino_sucursal_id');

        return DB::transaction(function () use ($request, $sucursalId) {
            $tarjetas = TarjetaInventario::where('destino_sucursal_id', $sucursalId)
                ->where('estatus', 'activa')
                ->lockForUpdate()
                ->get();

            if ($tarjetas->isEmpty()) {
                return back()->withErrors(['destino_sucursal_id' => 'No hay tarjetas "Activa" asignadas a esa sucursal.']);
            }

            foreach ($tarjetas as $tarjeta) {
                TarjetaHistorial::create([
                    'tarjeta_id' => $tarjeta->id,
                    'accion' => 'retiro_por_cierre_sucursal',
                    'estatus_anterior' => 'activa',
                    'estatus_nuevo' => 'en_stock',
                    'motivo' => $request->validated('motivo'),
                    'usuario' => Auth::user()->name,
                ]);
            }

            TarjetaInventario::whereIn('id', $tarjetas->pluck('id'))->update([
                'estatus' => 'en_stock',
                'destino_sucursal_id' => null,
                'otro_destino_descripcion' => null,
                'fecha_asignacion' => null,
                'numero_oficio' => null,
            ]);

            return back()->with('status', "Se retiraron {$tarjetas->count()} tarjetas y volvieron a \"En stock\".");
        });
    }

    public function renominar(TarjetaInventario $tarjeta): RedirectResponse
    {
        if ($tarjeta->estatus !== 'activa') {
            return back()->withErrors(['estatus' => 'Solo se pueden renominar tarjetas en estatus "Activa".']);
        }

        DB::transaction(function () use ($tarjeta) {
            TarjetaHistorial::create([
                'tarjeta_id' => $tarjeta->id,
                'accion' => 'renominacion',
                'estatus_anterior' => 'activa',
                'estatus_nuevo' => 'renominada',
                'usuario' => Auth::user()->name,
            ]);

            $tarjeta->update(['estatus' => 'renominada', 'fecha_renominacion' => now()->format('Y-m-d')]);
        });

        return back()->with('status', 'Tarjeta marcada como renominada.');
    }

    public function corregirRenominacion(CorregirRenominacionRequest $request, TarjetaInventario $tarjeta): RedirectResponse
    {
        if ($tarjeta->estatus !== 'renominada') {
            return back()->withErrors(['estatus' => 'Solo se pueden corregir tarjetas en estatus "Renominada".']);
        }

        DB::transaction(function () use ($request, $tarjeta) {
            TarjetaHistorial::create([
                'tarjeta_id' => $tarjeta->id,
                'accion' => 'correccion_renominacion',
                'estatus_anterior' => 'renominada',
                'estatus_nuevo' => 'activa',
                'motivo' => $request->validated('motivo'),
                'usuario' => Auth::user()->name,
            ]);

            $tarjeta->update(['estatus' => 'activa', 'fecha_renominacion' => null]);
        });

        return back()->with('status', 'Se corrigió la renominación; la tarjeta volvió a "Activa".');
    }
}
