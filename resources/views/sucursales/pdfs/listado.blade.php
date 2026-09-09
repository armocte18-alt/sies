<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 70px 30px 50px 30px; }
        body { font-family: 'Helvetica', sans-serif; font-size: 9px; color: #222; }
        .header { position: fixed; top: -55px; left: 0; right: 0; height: 55px; text-align: center; border-bottom: 2px solid #135c46; padding-bottom: 6px; }
        .header img { height: 32px; }
        .header h1 { font-size: 13px; margin: 4px 0 0 0; color: #103d30; }
        .footer { position: fixed; bottom: -35px; left: 0; right: 0; height: 35px; text-align: center; font-size: 8px; color: #666; border-top: 1px solid #ccc; padding-top: 5px; }
        .titulo { text-align: center; font-size: 13px; font-weight: bold; color: #135c46; margin: 6px 0 12px 0; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #135c46; color: #fff; padding: 5px 6px; font-size: 8.5px; text-align: left; }
        td { border-bottom: 1px solid #ddd; padding: 5px 6px; vertical-align: top; }
        tr:nth-child(even) td { background: #f5f8f7; }
        .muted { color: #777; font-size: 8px; }
        .badge { display: inline-block; padding: 1px 6px; border-radius: 8px; font-size: 8px; font-weight: bold; text-transform: uppercase; }
        .badge-activa { background: #d1fae5; color: #065f46; }
        .badge-inactiva { background: #e5e7eb; color: #374151; }
        .badge-suspendida { background: #fee2e2; color: #991b1b; }
        .badge-en_apertura { background: #fef3c7; color: #92400e; }
        .sin-encargado { color: #b91c1c; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <img src="{{ public_path('images/logo-institucional.png') }}">
        <h1>Financiera para el Bienestar &middot; Gerencia Estatal CDMX</h1>
    </div>

    <div class="footer">
        Listado de sucursales &mdash; {{ $sucursales->count() }} registro(s) &mdash; Generado por SIES el {{ now()->format('d/m/Y H:i') }}
    </div>

    <div class="titulo">Listado de Sucursales</div>

    <table>
        <thead>
            <tr>
                <th style="width: 7%;">Registro</th>
                <th style="width: 14%;">Sucursal</th>
                <th style="width: 10%;">Alcaldía</th>
                <th style="width: 24%;">Domicilio</th>
                <th style="width: 18%;">Horario</th>
                <th style="width: 14%;">Titular</th>
                <th style="width: 8%;">Estatus</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sucursales as $sucursal)
                <tr>
                    <td>{{ $sucursal->clave_financiera }}</td>
                    <td>{{ $sucursal->nombre_oficial }}</td>
                    <td>{{ $sucursal->ubicacion?->alcaldia?->nombre ?? '—' }}</td>
                    <td>
                        {{ $sucursal->ubicacion?->domicilioCompleto() ?? '—' }}
                        @if ($sucursal->ubicacion?->entreCalles())
                            <br><span class="muted">{{ $sucursal->ubicacion->entreCalles() }}</span>
                        @endif
                        @if ($sucursal->ubicacion?->referencia_visual)
                            <br><span class="muted">Ref: {{ $sucursal->ubicacion->referencia_visual }}</span>
                        @endif
                    </td>
                    <td>
                        {{ $sucursal->operacion?->resumenHorario() ?? '—' }}
                        @if ($sucursal->operacion?->resumenGuardia())
                            <br><span class="muted">Guardia: {{ $sucursal->operacion->resumenGuardia() }}</span>
                        @endif
                    </td>
                    <td>
                        @if ($sucursal->titular)
                            {{ $sucursal->titular->nombre_completo }}
                        @else
                            <span class="sin-encargado">Sin encargado</span>
                        @endif
                    </td>
                    <td><span class="badge badge-{{ $sucursal->estatus_operativo }}">{{ ucfirst(str_replace('_', ' ', $sucursal->estatus_operativo)) }}</span></td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
