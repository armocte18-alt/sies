<x-sies-layout :title="'Sucursales'">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Sucursales</h1>

        @can('create', App\Models\Sucursal::class)
            <a href="{{ route('sucursales.create') }}"
                class="inline-flex w-fit items-center gap-2 rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                + Nueva sucursal
            </a>
        @endcan
    </div>

    <form method="GET" action="{{ route('sucursales.index') }}" class="mt-4 flex flex-wrap items-end gap-4" role="search" x-data="busquedaEnVivo"
        data-resultados="resultados-sucursales" data-valor-inicial="{{ request('buscar') }}">
        <input type="hidden" name="alcaldia" value="{{ request('alcaldia') }}">

        <div>
            <label for="buscar" class="sr-only">Buscar sucursal por nombre o clave financiera</label>
            <div class="flex max-w-md gap-2">
                <input type="search" id="buscar" name="buscar" x-model="valor"
                    placeholder="Buscar por nombre o clave financiera..."
                    x-on:input="buscar()"
                    autocomplete="off"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                <button type="submit"
                    class="rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-800">
                    Buscar
                </button>
            </div>
        </div>

        <div>
            <span class="mb-1 block text-xs font-medium text-gray-500">Estatus</span>
            @php
                $pildoras = [
                    '' => ['label' => 'Todas', 'activo' => 'bg-gray-800 text-white border-gray-800', 'inactivo' => 'border-gray-300 text-gray-600 hover:bg-gray-50'],
                    'activa' => ['label' => 'Activa', 'activo' => 'bg-emerald-600 text-white border-emerald-600', 'inactivo' => 'border-emerald-200 text-emerald-700 hover:bg-emerald-50'],
                    'suspendida' => ['label' => 'Suspendida', 'activo' => 'bg-red-600 text-white border-red-600', 'inactivo' => 'border-red-200 text-red-700 hover:bg-red-50'],
                    'en_apertura' => ['label' => 'En apertura', 'activo' => 'bg-sky-600 text-white border-sky-600', 'inactivo' => 'border-sky-200 text-sky-700 hover:bg-sky-50'],
                    'inactiva' => ['label' => 'Inactiva', 'activo' => 'bg-gray-500 text-white border-gray-500', 'inactivo' => 'border-gray-300 text-gray-500 hover:bg-gray-50'],
                ];
            @endphp
            <div class="flex flex-wrap gap-1.5">
                @foreach ($pildoras as $valor => $config)
                    @php
                        $esActivo = (request('estatus') ?? '') === $valor;
                        $query = array_merge(request()->except(['estatus', 'page']), $valor !== '' ? ['estatus' => $valor] : []);
                    @endphp
                    <a href="{{ route('sucursales.index', $query) }}"
                        class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold transition {{ $esActivo ? $config['activo'] : $config['inactivo'] }}">
                        {{ $config['label'] }}
                    </a>
                @endforeach
            </div>
        </div>

        <div>
            <label for="per_page" class="mb-1 block text-xs font-medium text-gray-500">Mostrar</label>
            <select id="per_page" name="per_page" x-on:change="$el.form.submit()"
                class="rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                @foreach ([15, 25, 50, 100] as $cantidad)
                    <option value="{{ $cantidad }}" @selected((int) request('per_page', 15) === $cantidad)>{{ $cantidad }} registros</option>
                @endforeach
            </select>
        </div>

        <div class="ms-auto flex gap-2">
            <a href="{{ route('sucursales.exportar.excel', request()->query()) }}" download
                class="inline-flex items-center gap-1.5 rounded-md border border-emerald-600 px-3 py-2 text-sm font-medium text-emerald-700 hover:bg-emerald-50">
                <x-nav-icon name="table-cells" class="h-4 w-4" />
                Excel
            </a>
            <a href="{{ route('sucursales.exportar.pdf', request()->query()) }}" download
                class="inline-flex items-center gap-1.5 rounded-md border border-red-600 px-3 py-2 text-sm font-medium text-red-700 hover:bg-red-50">
                <x-nav-icon name="document-arrow-down" class="h-4 w-4" />
                PDF
            </a>
        </div>
    </form>

    @if (request('alcaldia'))
        @php $alcaldiaFiltro = \App\Models\Alcaldia::find(request('alcaldia')); @endphp
        @if ($alcaldiaFiltro)
            <p class="mt-3 text-sm text-gray-600">
                Filtrando por alcaldía: <span class="font-semibold">{{ $alcaldiaFiltro->nombre }}</span>
                &middot; <a href="{{ route('sucursales.index') }}" class="text-brand-green hover:underline">Quitar filtro</a>
            </p>
        @endif
    @endif

    <div id="resultados-sucursales" class="mt-4">
        @include('sucursales.partials.tabla')
    </div>
</x-sies-layout>
