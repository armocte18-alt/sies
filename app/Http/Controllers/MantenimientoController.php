<?php

namespace App\Http\Controllers;

use App\Http\Requests\Mantenimientos\CambiarEstatusRequest;
use App\Http\Requests\Mantenimientos\StoreMantenimientoRequest;
use App\Http\Requests\Mantenimientos\StoreMaterialRequest;
use App\Http\Requests\Mantenimientos\StorePersonalRequest;
use App\Http\Requests\Mantenimientos\StoreTipoMantenimientoRequest;
use App\Http\Requests\Mantenimientos\UpdateMantenimientoRequest;
use App\Models\Empleado;
use App\Models\Mantenimiento;
use App\Models\MantenimientoBitacora;
use App\Models\MantenimientoMaterial;
use App\Models\Sucursal;
use App\Models\TipoMantenimiento;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MantenimientoController extends Controller
{
    public function index(Request $request): View
    {
        $mantenimientos = Mantenimiento::with(['sucursal', 'tipo', 'personal', 'materiales', 'bitacora.usuario'])
            ->when($request->filled('sucursal_id'), fn ($q) => $q->where('sucursal_id', $request->query('sucursal_id')))
            ->when($request->filled('tipo_mantenimiento_id'), fn ($q) => $q->where('tipo_mantenimiento_id', $request->query('tipo_mantenimiento_id')))
            ->when($request->filled('estatus'), fn ($q) => $q->where('estatus', $request->query('estatus')))
            ->when($request->filled('prioridad'), fn ($q) => $q->where('prioridad', $request->query('prioridad')))
            ->orderByDesc('fecha_programada_inicio')
            ->get();

        $anio = now()->year;
        $base = Mantenimiento::whereYear('fecha_programada_inicio', $anio);
        $kpis = [
            'total' => (clone $base)->count(),
            'pendientes' => (clone $base)->where('estatus', 'pendiente')->count(),
            'en_proceso' => (clone $base)->where('estatus', 'en_proceso')->count(),
            'completados' => (clone $base)->where('estatus', 'completado')->count(),
            'en_riesgo' => (clone $base)->get()->filter->enRiesgo()->count(),
            'costo_total' => (float) (clone $base)->sum(DB::raw('costo_mano_obra + costo_materiales')),
        ];

        $sucursales = Sucursal::orderBy('nombre_oficial')->get(['id', 'nombre_oficial', 'clave_financiera']);
        $tipos = TipoMantenimiento::where('activo', true)->orderBy('orden')->get();
        $empleados = Empleado::where('activo', true)->orderBy('nombre')->get(['id', 'nombre', 'apellido_paterno', 'apellido_materno']);

        return view('mantenimientos.index', compact('mantenimientos', 'kpis', 'sucursales', 'tipos', 'empleados'));
    }

    public function store(StoreMantenimientoRequest $request): RedirectResponse
    {
        $mantenimiento = DB::transaction(function () use ($request) {
            $mantenimiento = Mantenimiento::create([
                ...$request->validated(),
                'estatus' => 'pendiente',
                'creado_por' => Auth::id(),
            ]);

            $this->registrarBitacora($mantenimiento, 'creado', 'Mantenimiento registrado.');

            return $mantenimiento;
        });

        return back()->with('status', "Mantenimiento \"{$mantenimiento->titulo}\" registrado.");
    }

    public function update(UpdateMantenimientoRequest $request, Mantenimiento $mantenimiento): RedirectResponse
    {
        DB::transaction(function () use ($request, $mantenimiento) {
            $mantenimiento->update($request->validated());
            $this->registrarBitacora($mantenimiento, 'editado', 'Datos generales actualizados.');
        });

        return back()->with('status', "Mantenimiento \"{$mantenimiento->titulo}\" actualizado.");
    }

    public function destroy(Mantenimiento $mantenimiento): RedirectResponse
    {
        $mantenimiento->delete();

        return back()->with('status', "Mantenimiento \"{$mantenimiento->titulo}\" eliminado.");
    }

    public function cambiarEstatus(CambiarEstatusRequest $request, Mantenimiento $mantenimiento): RedirectResponse
    {
        $datos = $request->validated();
        $anterior = $mantenimiento->estatus;
        $cambios = ['estatus' => $datos['estatus']];

        if ($datos['estatus'] === 'en_proceso' && ! $mantenimiento->fecha_inicio_real) {
            $cambios['fecha_inicio_real'] = now();
        }
        if (in_array($datos['estatus'], ['completado', 'cancelado'], true)) {
            $cambios['fecha_fin_real'] = now();
            $cambios['cerrado_por'] = Auth::id();
            if (! empty($datos['comentario'])) {
                $cambios['comentario_cierre'] = $datos['comentario'];
            }
        }

        DB::transaction(function () use ($mantenimiento, $cambios, $datos, $anterior) {
            $mantenimiento->update($cambios);

            $this->registrarBitacora(
                $mantenimiento,
                'cambio_estatus',
                $datos['comentario'] ?? null,
                Mantenimiento::ESTATUS_LABELS[$anterior],
                Mantenimiento::ESTATUS_LABELS[$datos['estatus']],
            );
        });

        return back()->with('status', "Mantenimiento \"{$mantenimiento->titulo}\" marcado como \"".Mantenimiento::ESTATUS_LABELS[$datos['estatus']].'".');
    }

    public function storeMaterial(StoreMaterialRequest $request, Mantenimiento $mantenimiento): RedirectResponse
    {
        $datos = $request->validated();
        $costoTotal = round($datos['cantidad'] * $datos['costo_unitario'], 2);

        DB::transaction(function () use ($mantenimiento, $datos, $costoTotal) {
            MantenimientoMaterial::create([
                'mantenimiento_id' => $mantenimiento->id,
                'nombre_material' => $datos['nombre_material'],
                'unidad' => $datos['unidad'] ?? 'pza',
                'cantidad' => $datos['cantidad'],
                'costo_unitario' => $datos['costo_unitario'],
                'costo_total' => $costoTotal,
            ]);

            $mantenimiento->recalcularCostoMateriales();
        });

        return back()->with('status', 'Material agregado.');
    }

    public function destroyMaterial(Mantenimiento $mantenimiento, MantenimientoMaterial $material): RedirectResponse
    {
        abort_unless($material->mantenimiento_id === $mantenimiento->id, 404);

        DB::transaction(function () use ($mantenimiento, $material) {
            $material->delete();
            $mantenimiento->recalcularCostoMateriales();
        });

        return back()->with('status', 'Material eliminado.');
    }

    public function storePersonal(StorePersonalRequest $request, Mantenimiento $mantenimiento): RedirectResponse
    {
        $datos = $request->validated();

        $mantenimiento->personal()->syncWithoutDetaching([
            $datos['empleado_id'] => ['rol_en_mantenimiento' => $datos['rol_en_mantenimiento'] ?? null],
        ]);

        return back()->with('status', 'Personal asignado.');
    }

    public function destroyPersonal(Mantenimiento $mantenimiento, Empleado $empleado): RedirectResponse
    {
        $mantenimiento->personal()->detach($empleado->id);

        return back()->with('status', 'Personal retirado del mantenimiento.');
    }

    public function storeTipo(StoreTipoMantenimientoRequest $request): RedirectResponse
    {
        TipoMantenimiento::create([
            'nombre' => $request->validated('nombre'),
            'slug' => Str::slug($request->validated('nombre'), '_'),
            'color' => $request->validated('color') ?: '#285c4d',
            'activo' => true,
            'orden' => (TipoMantenimiento::max('orden') ?? 0) + 1,
        ]);

        return back()->with('status', 'Tipo de mantenimiento agregado.');
    }

    private function registrarBitacora(Mantenimiento $mantenimiento, string $accion, ?string $comentario = null, ?string $anterior = null, ?string $nuevo = null): void
    {
        MantenimientoBitacora::create([
            'mantenimiento_id' => $mantenimiento->id,
            'usuario_id' => Auth::id(),
            'accion' => $accion,
            'comentario' => $comentario,
            'valor_anterior' => $anterior,
            'valor_nuevo' => $nuevo,
        ]);
    }
}
