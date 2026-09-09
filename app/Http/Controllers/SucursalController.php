<?php

namespace App\Http\Controllers;

use App\Exports\SucursalesExport;
use App\Http\Requests\Sucursales\StoreSucursalRequest;
use App\Http\Requests\Sucursales\UpdateIdentificacionRequest;
use App\Models\Alcaldia;
use App\Models\Sucursal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class SucursalController extends Controller
{
    private const COLUMNAS_ORDENABLES = [
        'nombre' => 'sucursales.nombre_oficial',
        'clave' => 'sucursales.clave_financiera',
        'alcaldia' => 'alcaldias.nombre',
        'estatus' => 'sucursales.estatus_operativo',
    ];

    private const OPCIONES_POR_PAGINA = [15, 25, 50, 100];

    private const ESTATUS_LABELS = [
        'activa' => 'Activa',
        'inactiva' => 'Inactiva',
        'suspendida' => 'Suspendida',
        'en_apertura' => 'En apertura',
    ];

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Sucursal::class);

        $sucursales = $this->sucursalesFiltradas($request)
            ->paginate($this->porPagina($request))
            ->withQueryString();

        if ($request->ajax()) {
            return view('sucursales.partials.tabla', compact('sucursales'));
        }

        return view('sucursales.index', compact('sucursales'));
    }

    public function exportarExcel(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $this->authorize('viewAny', Sucursal::class);

        $sucursales = $this->sucursalesFiltradas($request)->get();
        $filtrosResumen = $this->resumenFiltros($request);

        return Excel::download(new SucursalesExport($sucursales, $filtrosResumen), 'directorio-sucursales.xlsx');
    }

    public function exportarPdf(Request $request): Response
    {
        $this->authorize('viewAny', Sucursal::class);

        $sucursales = $this->sucursalesFiltradas($request)->get();
        $filtrosResumen = $this->resumenFiltros($request);

        $pdf = Pdf::loadView('sucursales.pdfs.listado', compact('sucursales', 'filtrosResumen'))->setPaper('a4', 'landscape');

        return $pdf->download('directorio-sucursales.pdf');
    }

    /**
     * Resume en una línea los filtros con los que se generó la exportación
     * (estatus siempre se indica, incluso cuando es "Todas"), para que el
     * documento deje constancia de su alcance sin necesitar una columna de
     * Estatus repetida en cada fila.
     */
    private function resumenFiltros(Request $request): string
    {
        $partes = [];
        $partes[] = 'Estatus: '.(self::ESTATUS_LABELS[$request->query('estatus')] ?? 'Todas');

        if ($request->filled('alcaldia')) {
            $partes[] = 'Alcaldía: '.(Alcaldia::find($request->query('alcaldia'))?->nombre ?? '—');
        }

        if ($request->filled('buscar')) {
            $partes[] = 'Búsqueda: "'.$request->query('buscar').'"';
        }

        return implode('   ·   ', $partes);
    }

    /**
     * Query base compartida entre el listado paginado y ambas exportaciones,
     * para que "exportar" siempre refleje exactamente lo que está filtrado
     * en pantalla (búsqueda, alcaldía y estatus).
     */
    private function sucursalesFiltradas(Request $request): Builder
    {
        $columna = self::COLUMNAS_ORDENABLES[$request->query('sort')] ?? 'sucursales.clave_financiera';
        $direccion = $request->query('direction') === 'desc' ? 'desc' : 'asc';

        return Sucursal::query()
            ->select('sucursales.*')
            ->leftJoin('sucursal_ubicaciones', 'sucursal_ubicaciones.sucursal_id', '=', 'sucursales.id')
            ->leftJoin('alcaldias', 'alcaldias.id', '=', 'sucursal_ubicaciones.alcaldia_id')
            ->leftJoin('sucursal_operaciones', 'sucursal_operaciones.sucursal_id', '=', 'sucursales.id')
            ->leftJoin('empleados', 'empleados.id', '=', 'sucursales.titular_empleado_id')
            ->with(['ubicacion.alcaldia', 'operacion', 'finanzas', 'titular'])
            ->when($request->query('buscar'), function ($query, $buscar) {
                // Busca en registro, administración, domicilio, alcaldía,
                // horario y titular — no solo nombre/clave. Se parte en
                // palabras y cada una se exige por separado (AND entre
                // palabras, OR entre columnas) para que un nombre completo
                // como "Gonzalez Reyes" encuentre coincidencias aunque el
                // apellido paterno y el materno vivan en columnas distintas.
                $palabras = preg_split('/\s+/', trim($buscar), -1, PREG_SPLIT_NO_EMPTY);

                foreach ($palabras as $palabra) {
                    $query->where(function ($q) use ($palabra) {
                        $q->where('sucursales.nombre_oficial', 'like', "%{$palabra}%")
                            ->orWhere('sucursales.clave_financiera', 'like', "%{$palabra}%")
                            ->orWhere('sucursal_ubicaciones.calle', 'like', "%{$palabra}%")
                            ->orWhere('sucursal_ubicaciones.colonia', 'like', "%{$palabra}%")
                            ->orWhere('sucursal_ubicaciones.entre_calle_1', 'like', "%{$palabra}%")
                            ->orWhere('sucursal_ubicaciones.entre_calle_2', 'like', "%{$palabra}%")
                            ->orWhere('sucursal_ubicaciones.referencia_visual', 'like', "%{$palabra}%")
                            ->orWhere('sucursal_ubicaciones.codigo_postal', 'like', "%{$palabra}%")
                            ->orWhere('alcaldias.nombre', 'like', "%{$palabra}%")
                            ->orWhere('sucursal_operaciones.dias_laborables', 'like', "%{$palabra}%")
                            ->orWhere('sucursal_operaciones.dias_guardia', 'like', "%{$palabra}%")
                            ->orWhere('empleados.nombre', 'like', "%{$palabra}%")
                            ->orWhere('empleados.apellido_paterno', 'like', "%{$palabra}%")
                            ->orWhere('empleados.apellido_materno', 'like', "%{$palabra}%");
                    });
                }
            })
            ->when($request->query('alcaldia'), fn ($query, $alcaldiaId) => $query->where('alcaldias.id', $alcaldiaId))
            ->when($request->query('estatus'), fn ($query, $estatus) => $query->where('sucursales.estatus_operativo', $estatus))
            ->orderBy($columna, $direccion);
    }

    private function porPagina(Request $request): int
    {
        $valor = (int) $request->query('per_page', 15);

        return in_array($valor, self::OPCIONES_POR_PAGINA, true) ? $valor : 15;
    }

    public function create(): View
    {
        $this->authorize('create', Sucursal::class);

        return view('sucursales.create');
    }

    public function store(StoreSucursalRequest $request): RedirectResponse
    {
        $sucursal = Sucursal::create([
            ...$request->validated(),
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return redirect()
            ->route('sucursales.show', $sucursal)
            ->with('status', 'Sucursal creada correctamente. Completa el resto de las secciones desde cada coordinación.');
    }

    public function show(Sucursal $sucursal): View
    {
        $this->authorize('view', $sucursal);

        $sucursal->load([
            'ubicacion.alcaldia',
            'operacion',
            'inmueble',
            'equipamiento',
            'activosTi' => fn ($q) => $q->orderBy('tipo'),
            'finanzas',
            'empleados' => fn ($q) => $q->orderBy('funcion_laboral'),
            'titular',
            'motocicletas' => fn ($q) => $q->orderBy('placa'),
            'motocicletas.cargasCombustible',
            'equipamientoReparto' => fn ($q) => $q->orderBy('tipo'),
        ]);

        return view('sucursales.show', compact('sucursal'));
    }

    public function updateIdentificacion(UpdateIdentificacionRequest $request, Sucursal $sucursal): RedirectResponse
    {
        $sucursal->update([
            ...$request->validated(),
            'updated_by' => Auth::id(),
        ]);

        return back()->with('status', 'Cédula de identificación actualizada.');
    }
}
