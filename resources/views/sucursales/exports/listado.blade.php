<table>
    {{-- Filas reservadas para el logotipo (WithDrawings lo ancla en A1); sin
         texto para no encimarse con la imagen. --}}
    <tr><td colspan="6">&nbsp;</td></tr>
    <tr><td colspan="6">&nbsp;</td></tr>
    <tr><td colspan="6">&nbsp;</td></tr>
    <tr><td colspan="6" style="text-align:right; font-weight:bold; font-size:8pt;">DIRECCIÓN DE SERVICIOS FINANCIEROS Y OPERACIÓN DE SUCURSALES</td></tr>
    <tr><td colspan="6" style="text-align:right; font-weight:bold; font-size:8pt;">SUBDIRECCIÓN DE PROCESOS Y SUPERVISIÓN</td></tr>
    <tr><td colspan="6" style="text-align:right; font-weight:bold; font-size:8pt;">GERENCIA ESTATAL CIUDAD DE MÉXICO</td></tr>
    <tr><td colspan="6" style="text-align:right; font-weight:bold; font-size:8pt;">COORDINACIÓN DE OPERACIÓN</td></tr>
    <tr><td colspan="6">&nbsp;</td></tr>
    <tr><td colspan="6" style="font-weight:bold; font-size:16pt; color:#1e5b4f;">DIRECTORIO DE SUCURSALES DE LA GERENCIA ESTATAL EN LA CIUDAD DE MÉXICO</td></tr>
    <tr><td colspan="6" style="font-style:italic; font-size:10pt; color:#4B5563;">{{ $filtrosResumen }}</td></tr>
    <tr><td colspan="6">&nbsp;</td></tr>
    <tr>
        <td style="background-color:#1e5b4f; color:#ffffff; font-weight:bold;">Registro</td>
        <td style="background-color:#1e5b4f; color:#ffffff; font-weight:bold;">Administración</td>
        <td style="background-color:#1e5b4f; color:#ffffff; font-weight:bold;">Ubicación</td>
        <td style="background-color:#1e5b4f; color:#ffffff; font-weight:bold;">Alcaldía</td>
        <td style="background-color:#1e5b4f; color:#ffffff; font-weight:bold;">Horario</td>
        <td style="background-color:#1e5b4f; color:#ffffff; font-weight:bold;">Titular</td>
    </tr>
    @foreach ($sucursales as $sucursal)
        @php
            $ubic = $sucursal->lineasUbicacion();
            $hor = $sucursal->lineasHorario();
            $filas = max(count($ubic), count($hor), 1);
        @endphp
        @for ($i = 0; $i < $filas; $i++)
            <tr>
                @if ($i === 0)
                    <td rowspan="{{ $filas }}" style="color:#111827; vertical-align:top;">{{ $sucursal->clave_financiera }}</td>
                    <td rowspan="{{ $filas }}" style="color:#111827; vertical-align:top;">{{ $sucursal->nombre_oficial }}</td>
                @endif
                <td style="color: {{ $i === 0 ? '#1e5b4f' : '#9CA3AF' }}; vertical-align:top;">{{ $ubic[$i] ?? '' }}</td>
                @if ($i === 0)
                    <td rowspan="{{ $filas }}" style="color:#4B5563; vertical-align:top;">{{ $sucursal->ubicacion?->alcaldia?->nombre ?? '—' }}</td>
                @endif
                <td style="color: {{ $i === 0 ? '#4B5563' : '#D97706' }}; vertical-align:top;">{{ $hor[$i] ?? '' }}</td>
                @if ($i === 0)
                    <td rowspan="{{ $filas }}" style="vertical-align:top; {{ $sucursal->titular ? 'color:#4B5563;' : 'color:#b91c1c; font-weight:bold;' }}">
                        {{ $sucursal->titular?->nombre_completo ?? 'Sin encargado' }}
                    </td>
                @endif
            </tr>
        @endfor
    @endforeach
</table>
