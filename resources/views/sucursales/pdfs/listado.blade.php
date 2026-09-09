<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 40px 30px 50px 30px; }
        body { font-family: 'Helvetica', sans-serif; font-size: 9px; color: #222; }
        .footer { position: fixed; bottom: -35px; left: 0; right: 0; height: 35px; text-align: center; font-size: 8px; color: #666; border-top: 1px solid #ccc; padding-top: 5px; }
        .letterhead { text-align: right; font-size: 8px; font-weight: bold; color: #111; line-height: 1.5; }
        .titulo { text-align: center; font-size: 15px; font-weight: bold; color: #135c46; margin: 14px 0 4px 0; text-transform: uppercase; }
        .filtros { text-align: center; font-size: 9px; font-style: italic; color: #4B5563; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #135c46; color: #fff; padding: 5px 6px; font-size: 8.5px; text-align: left; }
        td { border-bottom: 1px solid #ddd; padding: 5px 6px; vertical-align: top; }
        tr:nth-child(even) td { background: #f5f8f7; }
        .registro, .sucursal { color: #111827; }
        .ubicacion { color: #135c46; }
        .sub { color: #9CA3AF; font-size: 8px; }
        .guardia { color: #D97706; font-size: 8px; }
        .sin-encargado { color: #b91c1c; font-weight: bold; }
    </style>
</head>
<body>

    <div class="footer">
        Directorio de sucursales &mdash; {{ $sucursales->count() }} registro(s) &mdash; Generado por SIES el {{ now()->format('d/m/Y H:i') }}
    </div>

    <div class="letterhead">
        DIRECCIÓN DE SERVICIOS FINANCIEROS Y OPERACIÓN DE SUCURSALES<br>
        SUBDIRECCIÓN DE PROCESOS Y SUPERVISIÓN<br>
        GERENCIA ESTATAL CIUDAD DE MÉXICO<br>
        COORDINACIÓN DE OPERACIÓN
    </div>

    <div class="titulo">Directorio de Sucursales de la Gerencia Estatal en la Ciudad de México</div>
    <div class="filtros">{{ $filtrosResumen }}</div>

    <table>
        <thead>
            <tr>
                <th style="width: 7%;">Registro</th>
                <th style="width: 15%;">Administración</th>
                <th style="width: 26%;">Ubicación</th>
                <th style="width: 11%;">Alcaldía</th>
                <th style="width: 20%;">Horario</th>
                <th style="width: 15%;">Titular</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sucursales as $sucursal)
                <tr>
                    <td class="registro">{{ $sucursal->clave_financiera }}</td>
                    <td class="sucursal">{{ $sucursal->nombre_oficial }}</td>
                    <td>
                        @foreach ($sucursal->lineasUbicacion() as $i => $linea)
                            <div class="{{ $i === 0 ? 'ubicacion' : 'sub' }}">{{ $linea }}</div>
                        @endforeach
                    </td>
                    <td>{{ $sucursal->ubicacion?->alcaldia?->nombre ?? '—' }}</td>
                    <td>
                        @foreach ($sucursal->lineasHorario() as $i => $linea)
                            <div class="{{ $i === 0 ? '' : 'guardia' }}">{{ $linea }}</div>
                        @endforeach
                    </td>
                    <td>
                        @if ($sucursal->titular)
                            {{ $sucursal->titular->nombre_completo }}
                        @else
                            <span class="sin-encargado">Sin encargado</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
