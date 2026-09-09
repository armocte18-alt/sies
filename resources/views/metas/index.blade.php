<x-sies-layout :title="'Metas y Análisis'">
    <h1 class="text-2xl font-bold text-gray-900">Metas y Análisis</h1>
    <p class="mt-1 text-sm text-gray-500">
        Metas anuales por línea de negocio e indicadores mensuales reales por sucursal.
    </p>

    {{-- ===================== Metas anuales ===================== --}}
    <section class="mt-6" aria-labelledby="metas-heading">
        <h2 id="metas-heading" class="mb-3 text-base font-semibold text-gray-800">Metas anuales por línea de negocio</h2>

        <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <caption class="sr-only">Metas anuales por línea de negocio</caption>
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Línea de negocio</th>
                        <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Tipo</th>
                        <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Año</th>
                        <th scope="col" class="px-4 py-3 text-right font-semibold text-gray-600">Meta anual</th>
                        <th scope="col" class="px-4 py-3"><span class="sr-only">Detalle</span></th>
                    </tr>
                </thead>
                <tbody x-data="{ abiertoId: null }" class="divide-y divide-gray-100">
                    @forelse ($metas as $meta)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $meta->linea_negocio }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $meta->tipo === 'core' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ ucfirst($meta->tipo) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $meta->anio }}</td>
                            <td class="px-4 py-3 text-right text-gray-800">${{ number_format($meta->meta_anual, 2) }}</td>
                            <td class="px-4 py-3 text-right">
                                <button type="button" x-on:click="abiertoId = abiertoId === {{ $meta->id }} ? null : {{ $meta->id }}"
                                    class="text-xs font-semibold text-brand-green hover:underline">
                                    <span x-text="abiertoId === {{ $meta->id }} ? 'Ocultar' : 'Ver detalle mensual'"></span>
                                </button>
                            </td>
                        </tr>
                        <tr x-show="abiertoId === {{ $meta->id }}" x-cloak>
                            <td colspan="5" class="bg-gray-50 px-4 py-4">
                                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 lg:grid-cols-6">
                                    @foreach (\App\Models\MetaMensual::MESES as $mes)
                                        <div class="rounded-md bg-white p-2 text-center shadow-sm">
                                            <p class="text-xs uppercase text-gray-500">{{ substr($mes, 0, 3) }}</p>
                                            <p class="text-sm font-semibold text-gray-800">${{ number_format($meta->{$mes}, 2) }}</p>
                                        </div>
                                    @endforeach
                                </div>
                                @if ($meta->producto_claves_vinculadas)
                                    <p class="mt-3 text-xs text-gray-500">
                                        Producto(s) vinculado(s): <span class="font-medium text-gray-700">{{ $meta->producto_claves_vinculadas }}</span>
                                    </p>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">Sin metas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- ===================== Indicadores mensuales por sucursal ===================== --}}
    <section class="mt-8" aria-labelledby="indicadores-heading">
        <h2 id="indicadores-heading" class="mb-3 text-base font-semibold text-gray-800">Indicadores mensuales por sucursal</h2>

        <form method="GET" action="{{ route('metas.index') }}" class="flex flex-col gap-2 sm:flex-row sm:items-center" role="search"
            x-data="busquedaEnVivo" data-resultados="resultados-indicadores" data-valor-inicial="{{ request('buscar') }}">
            <input type="hidden" name="anio" value="{{ $anioSeleccionado }}">
            <input type="hidden" name="mes" value="{{ $mesSeleccionado }}">

            <select name="anio" onchange="this.form.submit()"
                class="rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                @foreach ($aniosDisponibles as $anioOpcion)
                    <option value="{{ $anioOpcion }}" @selected($anioOpcion === $anioSeleccionado)>{{ $anioOpcion }}</option>
                @endforeach
            </select>

            <select name="mes" onchange="this.form.submit()"
                class="rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                @foreach ($mesesDisponibles as $mesOpcion)
                    <option value="{{ $mesOpcion }}" @selected($mesOpcion === $mesSeleccionado)>{{ \App\Models\MetaMensual::MESES[$mesOpcion - 1] ?? $mesOpcion }}</option>
                @endforeach
            </select>

            <div class="flex max-w-md flex-1 gap-2">
                <input type="search" id="buscar" name="buscar" x-model="valor"
                    placeholder="Buscar sucursal por nombre o clave..."
                    x-on:input="buscar()"
                    autocomplete="off"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                <button type="submit"
                    class="rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-800">
                    Buscar
                </button>
            </div>
        </form>

        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-dashboard.stat-card label="Volumen total" :value="number_format($resumenMes->volumen)" color="bg-sky-600">
                <x-nav-icon name="grid" class="h-8 w-8" />
            </x-dashboard.stat-card>
            <x-dashboard.stat-card label="Ingresos totales" :value="'$'.number_format($resumenMes->ingresos, 2)" color="bg-green-600">
                <x-nav-icon name="card" class="h-8 w-8" />
            </x-dashboard.stat-card>
            <x-dashboard.stat-card label="Gasto total" :value="'$'.number_format($resumenMes->gasto, 2)" color="bg-red-600">
                <x-nav-icon name="wrench" class="h-8 w-8" />
            </x-dashboard.stat-card>
            <x-dashboard.stat-card label="Balance" :value="'$'.number_format($resumenMes->balance, 2)" :color="$resumenMes->balance >= 0 ? 'bg-brand-green' : 'bg-gray-800'">
                <x-nav-icon name="chart" class="h-8 w-8" />
            </x-dashboard.stat-card>
        </div>

        <div id="resultados-indicadores" class="mt-4">
            @include('metas.partials.tabla')
        </div>
    </section>
</x-sies-layout>
