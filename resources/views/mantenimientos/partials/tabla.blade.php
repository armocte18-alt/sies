@php /** @var \Illuminate\Support\Collection $mantenimientos */ @endphp

<div class="overflow-x-auto rounded-lg bg-white shadow-sm">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <caption class="sr-only">Mantenimientos</caption>
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Título</th>
                <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Sucursal</th>
                <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Tipo</th>
                <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Prioridad</th>
                <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Programado</th>
                <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Costo total</th>
                <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Estatus</th>
                <th scope="col" class="px-4 py-3"><span class="sr-only">Acciones</span></th>
            </tr>
        </thead>
        <tbody x-data="{ abiertoId: null }" class="divide-y divide-gray-100">
            @forelse ($mantenimientos as $mantenimiento)
                @php
                    $coloresPrioridad = ['baja' => 'bg-gray-100 text-gray-600', 'media' => 'bg-sky-100 text-sky-800', 'alta' => 'bg-amber-100 text-amber-800', 'urgente' => 'bg-red-100 text-red-800'];
                    $coloresEstatus = ['pendiente' => 'bg-gray-100 text-gray-600', 'programado' => 'bg-sky-100 text-sky-800', 'en_proceso' => 'bg-amber-100 text-amber-800', 'completado' => 'bg-emerald-100 text-emerald-800', 'cancelado' => 'bg-red-100 text-red-800'];
                    $enRiesgo = $mantenimiento->enRiesgo();
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">
                        {{ $mantenimiento->titulo }}
                        @if ($enRiesgo)
                            <span class="ms-1 inline-flex rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700">En riesgo</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $mantenimiento->sucursal ? \Illuminate\Support\Str::title($mantenimiento->sucursal->nombre_oficial) : 'Gerencia / Área Central' }}</td>
                    <td class="px-4 py-3 text-gray-600">
                        <span class="inline-flex items-center gap-1.5">
                            <span class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $mantenimiento->tipo->color }}"></span>
                            {{ $mantenimiento->tipo->nombre }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $coloresPrioridad[$mantenimiento->prioridad] }}">
                            {{ App\Models\Mantenimiento::PRIORIDADES[$mantenimiento->prioridad] }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        {{ $mantenimiento->fecha_programada_inicio->format('d/m/Y') }}
                        @if (! $mantenimiento->fecha_programada_inicio->equalTo($mantenimiento->fecha_programada_fin))
                            – {{ $mantenimiento->fecha_programada_fin->format('d/m/Y') }}
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-600">${{ number_format($mantenimiento->costoTotal(), 2) }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $coloresEstatus[$mantenimiento->estatus] }}">
                            {{ App\Models\Mantenimiento::ESTATUS_LABELS[$mantenimiento->estatus] }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex justify-end gap-1">
                            <button type="button" x-show="abiertoId !== {{ $mantenimiento->id }}"
                                x-on:click="abiertoId = {{ $mantenimiento->id }}"
                                class="rounded p-1.5 text-brand-green hover:bg-brand-green-50">
                                <x-action-icon icon="clipboard" label="Ver detalle" />
                            </button>
                            <button type="button" x-show="abiertoId === {{ $mantenimiento->id }}" x-cloak
                                x-on:click="abiertoId = null"
                                class="rounded p-1.5 text-gray-500 hover:bg-gray-100">
                                <x-action-icon icon="x-mark" label="Cerrar detalle" />
                            </button>
                            @can('mantenimientos.gestionar')
                                <form method="POST" action="{{ route('mantenimientos.destroy', $mantenimiento) }}" onsubmit="return confirm('¿Eliminar el mantenimiento &quot;{{ $mantenimiento->titulo }}&quot;? Podrás seguir consultándolo en el historial.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded p-1.5 text-red-600 hover:bg-red-50">
                                        <x-action-icon icon="trash" label="Eliminar" />
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                <tr x-show="abiertoId === {{ $mantenimiento->id }}" x-cloak>
                    <td colspan="8" class="bg-gray-50 px-4 py-5">
                        @include('mantenimientos.partials.detalle', ['mantenimiento' => $mantenimiento])
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">Sin mantenimientos registrados con estos filtros.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
