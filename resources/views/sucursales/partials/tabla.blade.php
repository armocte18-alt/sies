<div class="overflow-x-auto rounded-lg bg-white shadow-sm">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <caption class="sr-only">Listado de sucursales registradas</caption>
        <thead class="bg-brand-green">
            <tr>
                <x-sortable-th field="clave" label="Registro" on-dark />
                <x-sortable-th field="nombre" label="Sucursal" on-dark />
                <th scope="col" class="px-4 py-3 text-left font-semibold text-white">Ubicación</th>
                <x-sortable-th field="alcaldia" label="Alcaldía" on-dark />
                <th scope="col" class="px-4 py-3 text-left font-semibold text-white">Horario</th>
                <th scope="col" class="px-4 py-3 text-left font-semibold text-white">Titular</th>
                <x-sortable-th field="estatus" label="Estatus" on-dark />
                <th scope="col" class="px-4 py-3"><span class="sr-only">Acciones</span></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($sucursales as $sucursal)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $sucursal->clave_financiera }}</td>
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $sucursal->nombre_oficial }}</td>
                    <td class="px-4 py-3 max-w-xs text-gray-600">
                        @if ($sucursal->ubicacion)
                            @php $urlMapa = $sucursal->ubicacion->urlMapa(); @endphp
                            <div class="flex items-start gap-1.5">
                                <x-nav-icon name="map-pin" class="mt-0.5 h-4 w-4 shrink-0 text-gray-400" />
                                <div>
                                    @if ($urlMapa)
                                        <a href="{{ $urlMapa }}" target="_blank" rel="noopener" class="text-brand-green hover:underline">
                                            {{ $sucursal->ubicacion->domicilioCompleto() }}
                                        </a>
                                    @else
                                        <span>{{ $sucursal->ubicacion->domicilioCompleto() }}</span>
                                    @endif
                                    @if ($sucursal->ubicacion->entreCalles())
                                        <p class="text-xs text-gray-400">{{ $sucursal->ubicacion->entreCalles() }}</p>
                                    @endif
                                    @if ($sucursal->ubicacion->referencia_visual)
                                        <p class="text-xs text-gray-400">Ref: {{ $sucursal->ubicacion->referencia_visual }}</p>
                                    @endif
                                </div>
                            </div>
                        @else
                            <span class="text-gray-400">Sin ubicación registrada</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $sucursal->ubicacion?->alcaldia?->nombre ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-600">
                        @if ($sucursal->operacion?->resumenHorario())
                            <div class="flex items-center gap-1.5">
                                <x-nav-icon name="clock" class="h-4 w-4 shrink-0 text-gray-400" />
                                <span>{{ $sucursal->operacion->resumenHorario() }}</span>
                            </div>
                            @if ($sucursal->operacion->resumenGuardia())
                                <div class="mt-0.5 flex items-center gap-1.5 text-amber-600">
                                    <x-nav-icon name="shield" class="h-3.5 w-3.5 shrink-0" />
                                    <span class="text-xs font-medium">{{ $sucursal->operacion->resumenGuardia() }}</span>
                                </div>
                            @endif
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        @if ($sucursal->titular)
                            {{ $sucursal->titular->nombre_completo }}
                        @else
                            <span class="text-xs font-semibold uppercase text-red-600">Sin encargado</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <x-sucursales.estatus-badge :estatus="$sucursal->estatus_operativo" />
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('sucursales.show', $sucursal) }}"
                            class="inline-flex items-center gap-1 rounded-full bg-brand-green px-3 py-1 text-xs font-semibold text-white shadow-sm transition hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                            Ver detalle
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                        No se encontraron sucursales.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $sucursales->links() }}
</div>
