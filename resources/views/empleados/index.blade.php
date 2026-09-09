<x-sies-layout :title="'Empleados'">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Empleados</h1>

        @can('empleados.gestionar')
            <button type="button" x-on:click="$dispatch('open-modal', 'agregar-empleado')"
                class="inline-flex w-fit items-center gap-2 rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                + Nuevo empleado
            </button>
        @endcan
    </div>

    <form method="GET" action="{{ route('empleados.index') }}" class="mt-4 flex flex-col gap-2 sm:flex-row" role="search"
        x-data="busquedaEnVivo" data-resultados="resultados-empleados" data-valor-inicial="{{ request('buscar') }}">
        <label for="buscar" class="sr-only">Buscar empleado por nombre, número o puesto</label>
        <div class="flex max-w-md flex-1 gap-2">
            <input type="search" id="buscar" name="buscar" x-model="valor"
                placeholder="Buscar por nombre, número o puesto..."
                x-on:input="buscar()"
                autocomplete="off"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
            <button type="submit"
                class="rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-800">
                Buscar
            </button>
        </div>

        <label for="sucursal" class="sr-only">Filtrar por sucursal</label>
        <select id="sucursal" name="sucursal" onchange="this.form.submit()"
            class="max-w-xs rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
            <option value="">Todas las sucursales</option>
            @foreach ($sucursales as $sucursalOpcion)
                <option value="{{ $sucursalOpcion->id }}" @selected((string) request('sucursal') === (string) $sucursalOpcion->id)>
                    {{ $sucursalOpcion->nombre_oficial }} ({{ $sucursalOpcion->clave_financiera }})
                </option>
            @endforeach
        </select>
    </form>

    <div id="resultados-empleados" class="mt-4">
        @include('empleados.partials.tabla')
    </div>

    @can('empleados.gestionar')
        <x-modal name="agregar-empleado" focusable>
            @include('empleados.partials.form', [
                'accion' => route('empleados.store'),
                'metodo' => null,
                'empleado' => null,
                'boton' => 'Agregar',
                'modalName' => 'agregar-empleado',
                'sucursales' => $sucursales,
            ])
        </x-modal>
    @endcan
</x-sies-layout>
