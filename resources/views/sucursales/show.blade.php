@php
    $finanzas = $sucursal->finanzas;
@endphp

<x-sios-layout :title="$sucursal->nombre_oficial">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="{{ route('sucursales.index') }}" class="text-sm text-brand-green hover:underline">&larr; Sucursales</a>
            <h1 class="text-2xl font-bold text-gray-900">{{ $sucursal->nombre_oficial }}</h1>
        </div>
        <x-sucursales.estatus-badge :estatus="$sucursal->estatus_operativo" class="text-sm" />
    </div>

    <p class="mt-1 text-xs text-gray-400">
        <i>Cifras sin telegramas (francos) ni programas sociales.</i>
    </p>

    {{-- Resumen financiero --}}
    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <x-sucursales.metric-card label="Volumen Total" :value="number_format($finanzas->volumen_total ?? 0)" icon="grid" color="text-sky-600" />
        <x-sucursales.metric-card label="Cantidad Situada" :value="'$'.number_format($finanzas->cantidad_situada ?? 0, 2)" icon="chart" color="text-green-600" />
        <x-sucursales.metric-card label="Ingreso Estimado" :value="'$'.number_format($finanzas->ingreso_estimado ?? 0, 2)" icon="card" color="text-amber-600" />
        <x-sucursales.metric-card label="Gasto Total" :value="'$'.number_format($finanzas->gasto_total ?? 0, 2)" icon="wrench" color="text-red-600" />
        <x-sucursales.metric-card label="Balance" :value="'$'.number_format($finanzas->balance ?? 0, 2)" icon="chart" color="text-slate-700" />
        <div class="flex items-center justify-between rounded-lg bg-white p-4 shadow-sm">
            <span class="text-sm font-medium text-gray-500">Estatus financiero</span>
            @php $estatusFinanciero = $finanzas->estatus_financiero ?? 'superavitaria'; @endphp
            <span @class([
                'inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-bold uppercase text-white',
                'bg-red-600' => $estatusFinanciero === 'deficitaria',
                'bg-green-600' => $estatusFinanciero !== 'deficitaria',
            ])>
                {{ $estatusFinanciero === 'deficitaria' ? '↓ Deficitaria' : '↑ Superavitaria' }}
            </span>
        </div>
    </div>

    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
        @include('sucursales.partials.identificacion')

        <section class="rounded-lg bg-white p-4 shadow-sm" aria-labelledby="personal-heading">
            <h2 id="personal-heading" class="mb-3 flex items-center gap-2 text-base font-semibold text-gray-800">
                <x-nav-icon name="users" class="h-5 w-5 text-brand-green" />
                Plantilla de personal adscrito
            </h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase text-gray-500">
                            <th scope="col" class="py-2 pr-4">No. Emp</th>
                            <th scope="col" class="py-2 pr-4">Nombre del colaborador</th>
                            <th scope="col" class="py-2">Función laboral</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($sucursal->empleados as $empleado)
                            <tr>
                                <td class="py-2 pr-4 text-gray-500">{{ $empleado->no_empleado }}</td>
                                <td class="py-2 pr-4 font-medium text-brand-green">{{ $empleado->nombre_completo }}</td>
                                <td class="py-2 uppercase text-gray-600">{{ $empleado->funcion_laboral }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 text-center text-gray-400">Sin personal registrado.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    {{-- Tabs por coordinación --}}
    <div x-data="{ tab: 'ubicacion' }" class="mt-6">
        <div role="tablist" aria-label="Secciones de la sucursal" class="flex gap-1 overflow-x-auto border-b border-gray-200">
            @foreach ([
                'ubicacion' => 'Ubicación geográfica',
                'horarios' => 'Operación y horarios',
                'inmueble' => 'Inmueble',
                'equipamiento' => 'Equipamiento técnico',
                'finanzas' => 'Finanzas',
            ] as $key => $label)
                <button type="button" role="tab" id="tab-{{ $key }}" aria-controls="panel-{{ $key }}"
                    x-on:click="tab = '{{ $key }}'" :aria-selected="(tab === '{{ $key }}').toString()"
                    :tabindex="tab === '{{ $key }}' ? 0 : -1"
                    :class="tab === '{{ $key }}' ? 'border-brand-green text-brand-green' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="whitespace-nowrap border-b-2 px-4 py-3 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-brand-green focus:ring-offset-2">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <div role="tabpanel" id="panel-ubicacion" aria-labelledby="tab-ubicacion" x-show="tab === 'ubicacion'" x-cloak class="pt-4">
            @include('sucursales.partials.ubicacion')
        </div>
        <div role="tabpanel" id="panel-horarios" aria-labelledby="tab-horarios" x-show="tab === 'horarios'" x-cloak class="pt-4">
            @include('sucursales.partials.horarios')
        </div>
        <div role="tabpanel" id="panel-inmueble" aria-labelledby="tab-inmueble" x-show="tab === 'inmueble'" x-cloak class="pt-4">
            @include('sucursales.partials.inmueble')
        </div>
        <div role="tabpanel" id="panel-equipamiento" aria-labelledby="tab-equipamiento" x-show="tab === 'equipamiento'" x-cloak class="pt-4">
            @include('sucursales.partials.equipamiento')
        </div>
        <div role="tabpanel" id="panel-finanzas" aria-labelledby="tab-finanzas" x-show="tab === 'finanzas'" x-cloak class="pt-4">
            @include('sucursales.partials.finanzas')
        </div>
    </div>
</x-sios-layout>
