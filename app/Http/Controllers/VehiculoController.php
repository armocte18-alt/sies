<?php

namespace App\Http\Controllers;

use App\Http\Requests\Vehiculos\DevolucionSolicitudRequest;
use App\Http\Requests\Vehiculos\RechazarSolicitudRequest;
use App\Http\Requests\Vehiculos\StoreConductorRequest;
use App\Http\Requests\Vehiculos\StoreSolicitudVehiculoRequest;
use App\Http\Requests\Vehiculos\StoreVehiculoRequest;
use App\Http\Requests\Vehiculos\UpdateConductorRequest;
use App\Http\Requests\Vehiculos\UpdateVehiculoRequest;
use App\Models\Conductor;
use App\Models\SolicitudVehiculo;
use App\Models\SolicitudVehiculoDetalle;
use App\Models\Vehiculo;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VehiculoController extends Controller
{
    /**
     * Solicitudes en estos estatus siguen "ocupando agenda" para efectos de
     * detectar traslapes de fechas sobre un mismo vehículo o conductor.
     */
    private const ESTATUS_OCUPAN_AGENDA = ['pendiente', 'autorizada'];

    public function index(): View
    {
        $vehiculos = Vehiculo::orderBy('marca')->orderBy('modelo')->get();
        $conductores = Conductor::orderBy('nombre_completo')->get();
        $solicitudes = SolicitudVehiculo::with(['solicitante', 'autorizador', 'detalle.vehiculo', 'detalle.conductor'])
            ->orderByDesc('fecha_salida_desde')
            ->get();

        $vehiculosDisponibles = $vehiculos->where('estatus', 'disponible');
        $conductoresActivos = $conductores->where('estatus', 'activo');

        return view('vehiculos.index', compact('vehiculos', 'conductores', 'solicitudes', 'vehiculosDisponibles', 'conductoresActivos'));
    }

    public function kmSugerido(Vehiculo $vehiculo): JsonResponse
    {
        return response()->json(['km_inicial' => SolicitudVehiculoDetalle::kmInicialSugerido($vehiculo->id)]);
    }

    public function storeVehiculo(StoreVehiculoRequest $request): RedirectResponse
    {
        Vehiculo::create($request->validated());

        return back()->with('status', 'Vehículo agregado al catálogo.');
    }

    public function updateVehiculo(UpdateVehiculoRequest $request, Vehiculo $vehiculo): RedirectResponse
    {
        if ($vehiculo->estatus === 'asignado' && $request->validated('estatus') !== 'asignado') {
            return back()->withErrors(['estatus' => 'Este vehículo está asignado a una solicitud en curso; su estatus se libera automáticamente al registrar la devolución.']);
        }

        $vehiculo->update($request->validated());

        return back()->with('status', "Vehículo {$vehiculo->descripcion()} actualizado.");
    }

    public function storeConductor(StoreConductorRequest $request): RedirectResponse
    {
        Conductor::create([...$request->validated(), 'estatus' => 'activo']);

        return back()->with('status', 'Conductor agregado al catálogo.');
    }

    public function updateConductor(UpdateConductorRequest $request, Conductor $conductor): RedirectResponse
    {
        $conductor->update($request->validated());

        return back()->with('status', "Conductor {$conductor->nombre_completo} actualizado.");
    }

    public function storeSolicitud(StoreSolicitudVehiculoRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $desde = Carbon::parse($datos['fecha_salida_desde']);
        $hasta = Carbon::parse($datos['fecha_salida_hasta']);

        return DB::transaction(function () use ($datos, $desde, $hasta) {
            $vehiculo = Vehiculo::lockForUpdate()->findOrFail($datos['vehiculo_id']);
            $conductor = Conductor::lockForUpdate()->findOrFail($datos['conductor_id']);

            if ($this->vehiculoTieneTraslape($vehiculo->id, $desde, $hasta)) {
                return back()->withErrors(['vehiculo_id' => 'Ese vehículo ya tiene una solicitud pendiente o autorizada que se traslapa con estas fechas.'])->withInput();
            }

            if ($this->conductorTieneTraslape($conductor->id, $desde, $hasta)) {
                return back()->withErrors(['conductor_id' => 'Ese conductor ya tiene una solicitud pendiente o autorizada que se traslapa con estas fechas.'])->withInput();
            }

            $solicitud = SolicitudVehiculo::create([
                'solicitante_id' => Auth::id(),
                'numero_empleado' => $datos['numero_empleado'] ?? null,
                'area' => $datos['area'],
                'area_otro' => $datos['area_otro'] ?? null,
                'fecha_salida_desde' => $desde,
                'fecha_salida_hasta' => $hasta,
                'destinos' => [['lugar' => $datos['destino_lugar'], 'orden' => 1]],
                'motivo' => $datos['motivo'],
                'estatus' => 'pendiente',
            ]);

            SolicitudVehiculoDetalle::create([
                'solicitud_id' => $solicitud->id,
                'vehiculo_id' => $vehiculo->id,
                'conductor_id' => $conductor->id,
                'km_inicial' => SolicitudVehiculoDetalle::kmInicialSugerido($vehiculo->id),
            ]);

            return back()->with('status', 'Solicitud de vehículo registrada; queda pendiente de autorización.');
        });
    }

    public function autorizarSolicitud(SolicitudVehiculo $solicitud): RedirectResponse
    {
        if ($solicitud->estatus !== 'pendiente') {
            return back()->withErrors(['estatus' => 'Solo se pueden autorizar solicitudes en estatus "Pendiente".']);
        }

        return DB::transaction(function () use ($solicitud) {
            $detalle = $solicitud->detalle;
            $vehiculo = Vehiculo::lockForUpdate()->findOrFail($detalle->vehiculo_id);

            if ($vehiculo->estatus !== 'disponible') {
                return back()->withErrors(['estatus' => "El vehículo {$vehiculo->descripcion()} ya no está disponible."]);
            }

            if ($this->vehiculoTieneTraslape($vehiculo->id, $solicitud->fecha_salida_desde, $solicitud->fecha_salida_hasta, $solicitud->id)) {
                return back()->withErrors(['estatus' => 'El vehículo tiene otra solicitud autorizada que se traslapa con estas fechas.']);
            }

            $solicitud->update([
                'estatus' => 'autorizada',
                'autorizado_por' => Auth::id(),
                'autorizado_at' => now(),
            ]);

            $vehiculo->update(['estatus' => 'asignado']);

            return back()->with('status', "Solicitud #{$solicitud->id} autorizada.");
        });
    }

    public function rechazarSolicitud(RechazarSolicitudRequest $request, SolicitudVehiculo $solicitud): RedirectResponse
    {
        if ($solicitud->estatus !== 'pendiente') {
            return back()->withErrors(['estatus' => 'Solo se pueden rechazar solicitudes en estatus "Pendiente".']);
        }

        $solicitud->update([
            'estatus' => 'rechazada',
            'autorizado_por' => Auth::id(),
            'autorizado_at' => now(),
            'motivo_rechazo' => $request->validated('motivo_rechazo'),
        ]);

        return back()->with('status', "Solicitud #{$solicitud->id} rechazada.");
    }

    public function devolucionSolicitud(DevolucionSolicitudRequest $request, SolicitudVehiculo $solicitud): RedirectResponse
    {
        if ($solicitud->estatus !== 'autorizada') {
            return back()->withErrors(['estatus' => 'Solo se puede registrar devolución de solicitudes "Autorizada".']);
        }

        $datos = $request->validated();
        $detalle = $solicitud->detalle;

        if ($datos['km_final'] < $detalle->km_inicial) {
            return back()->withErrors(['km_final' => "El kilometraje final no puede ser menor al inicial ({$detalle->km_inicial} km)."]);
        }

        DB::transaction(function () use ($datos, $solicitud, $detalle) {
            $detalle->update([
                'km_final' => $datos['km_final'],
                'km_recorridos' => $datos['km_final'] - $detalle->km_inicial,
                'combustible_recepcion' => $datos['combustible_recepcion'],
                'carroceria_notas_nuevas' => $datos['carroceria_notas_nuevas'] ?? null,
                'checklist_gato' => $datos['checklist_gato'] ?? false,
                'checklist_llave_cruz' => $datos['checklist_llave_cruz'] ?? false,
                'checklist_reflejantes' => $datos['checklist_reflejantes'] ?? false,
                'checklist_extintor' => $datos['checklist_extintor'] ?? false,
                'observaciones_fallas' => $datos['observaciones_fallas'] ?? null,
                'fecha_hora_devolucion' => now(),
            ]);

            $solicitud->update(['estatus' => 'finalizada']);

            Vehiculo::where('id', $detalle->vehiculo_id)->update([
                'estatus' => 'disponible',
                'kilometraje_actual' => $datos['km_final'],
            ]);
        });

        return back()->with('status', "Devolución registrada; la solicitud #{$solicitud->id} quedó finalizada.");
    }

    public function responsivaPdf(SolicitudVehiculo $solicitud): Response
    {
        abort_unless(in_array($solicitud->estatus, ['autorizada', 'finalizada'], true), 404);

        $solicitud->load(['solicitante', 'autorizador', 'detalle.vehiculo', 'detalle.conductor']);

        $pdf = Pdf::loadView('vehiculos.pdfs.responsiva', ['solicitud' => $solicitud]);

        return $pdf->stream("responsiva-solicitud-{$solicitud->id}.pdf");
    }

    private function vehiculoTieneTraslape(int $vehiculoId, Carbon $desde, Carbon $hasta, ?int $excluirSolicitudId = null): bool
    {
        return SolicitudVehiculoDetalle::where('vehiculo_id', $vehiculoId)
            ->whereHas('solicitud', function ($query) use ($desde, $hasta, $excluirSolicitudId) {
                $query->whereIn('estatus', self::ESTATUS_OCUPAN_AGENDA)
                    ->where('fecha_salida_desde', '<', $hasta)
                    ->where('fecha_salida_hasta', '>', $desde);

                if ($excluirSolicitudId) {
                    $query->where('id', '!=', $excluirSolicitudId);
                }
            })
            ->exists();
    }

    private function conductorTieneTraslape(int $conductorId, Carbon $desde, Carbon $hasta, ?int $excluirSolicitudId = null): bool
    {
        return SolicitudVehiculoDetalle::where('conductor_id', $conductorId)
            ->whereHas('solicitud', function ($query) use ($desde, $hasta, $excluirSolicitudId) {
                $query->whereIn('estatus', self::ESTATUS_OCUPAN_AGENDA)
                    ->where('fecha_salida_desde', '<', $hasta)
                    ->where('fecha_salida_hasta', '>', $desde);

                if ($excluirSolicitudId) {
                    $query->where('id', '!=', $excluirSolicitudId);
                }
            })
            ->exists();
    }
}
