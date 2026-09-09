<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 90px 40px 70px 40px; }
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #222; }
        .header { position: fixed; top: -70px; left: 0; right: 0; height: 70px; text-align: center; border-bottom: 2px solid #285c4d; padding-bottom: 8px; }
        .header img { height: 40px; }
        .header h1 { font-size: 14px; margin: 4px 0 0 0; color: #1a3e34; }
        .footer { position: fixed; bottom: -50px; left: 0; right: 0; height: 50px; text-align: center; font-size: 9px; color: #666; border-top: 1px solid #ccc; padding-top: 6px; }
        table.datos { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        table.datos td { padding: 4px 6px; vertical-align: top; }
        table.datos td.label { font-weight: bold; color: #1a3e34; width: 160px; }
        table.vehiculos { width: 100%; border-collapse: collapse; margin: 12px 0; }
        table.vehiculos th { background: #1a3e34; color: #fff; padding: 6px; font-size: 10px; }
        table.vehiculos td { border: 1px solid #ccc; padding: 6px; font-size: 10px; }
        .titulo { text-align: center; font-size: 15px; font-weight: bold; color: #98224e; margin: 10px 0 18px 0; text-transform: uppercase; }
        .firmas { width: 100%; margin-top: 60px; }
        .firmas td { width: 33.33%; text-align: center; font-size: 10px; padding-top: 4px; }
        .firmas .linea { border-top: 1px solid #222; margin: 0 20px 4px 20px; }
    </style>
</head>
<body>

    <div class="header">
        <img src="{{ public_path('images/logo-institucional.png') }}">
        <h1>Financiera para el Bienestar</h1>
    </div>

    <div class="footer">
        Folio #{{ str_pad((string) $solicitud->id, 5, '0', STR_PAD_LEFT) }} — Documento generado por SIES el {{ now()->format('d/m/Y H:i') }}
    </div>

    <div class="titulo">Responsiva de Vehículo Oficial</div>

    <table class="datos">
        <tr>
            <td class="label">Folio:</td>
            <td>#{{ str_pad((string) $solicitud->id, 5, '0', STR_PAD_LEFT) }}</td>
            <td class="label">Área:</td>
            <td>{{ App\Models\SolicitudVehiculo::AREA_LABELS[$solicitud->area] ?? $solicitud->area_otro }}</td>
        </tr>
        <tr>
            <td class="label">Solicitante:</td>
            <td>{{ $solicitud->solicitante->name }}</td>
            <td class="label">No. de empleado:</td>
            <td>{{ $solicitud->numero_empleado }}</td>
        </tr>
        <tr>
            <td class="label">Salida:</td>
            <td>{{ $solicitud->fecha_salida_desde->format('d/m/Y H:i') }}</td>
            <td class="label">Regreso estimado:</td>
            <td>{{ $solicitud->fecha_salida_hasta->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td class="label">Motivo:</td>
            <td colspan="3">{{ $solicitud->motivo }}</td>
        </tr>
        <tr>
            <td class="label">Destinos:</td>
            <td colspan="3">
                @foreach ($solicitud->destinos as $d)
                    {{ $d['orden'] }}. {{ $d['lugar'] }}{{ ! $loop->last ? ' — ' : '' }}
                @endforeach
            </td>
        </tr>
    </table>

    <table class="vehiculos">
        <thead>
            <tr>
                <th>Placa</th>
                <th>Marca / Modelo</th>
                <th>Conductor</th>
                <th>No. Licencia</th>
                <th>Km inicial</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $solicitud->detalle->vehiculo->placa }}</td>
                <td>{{ $solicitud->detalle->vehiculo->marca }} {{ $solicitud->detalle->vehiculo->modelo }}</td>
                <td>{{ $solicitud->detalle->conductor->nombre_completo }}</td>
                <td>{{ $solicitud->detalle->conductor->numero_licencia }}</td>
                <td>{{ number_format($solicitud->detalle->km_inicial) }} km</td>
            </tr>
        </tbody>
    </table>

    <table class="firmas">
        <tr>
            <td>
                <div class="linea"></div>
                Nombre y firma del solicitante
            </td>
            <td>
                <div class="linea"></div>
                Nombre y firma de quien conduce
            </td>
            <td>
                <div class="linea"></div>
                Nombre y firma del Gerente Estatal
            </td>
        </tr>
    </table>

</body>
</html>
