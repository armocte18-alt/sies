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

    <form method="GET" action="{{ route('sucursales.index') }}" class="mt-4" role="search" x-data="busquedaEnVivo"
        data-resultados="resultados-sucursales" data-valor-inicial="{{ request('buscar') }}">
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
