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

    <form method="GET" action="{{ route('sucursales.index') }}" class="mt-4" role="search">
        <label for="buscar" class="sr-only">Buscar sucursal por nombre o clave financiera</label>
        <div class="flex max-w-md gap-2">
            <input type="search" id="buscar" name="buscar" value="{{ request('buscar') }}"
                placeholder="Buscar por nombre o clave financiera..."
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
            <button type="submit"
                class="rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-800">
                Buscar
            </button>
        </div>
    </form>

    <div class="mt-4 overflow-x-auto rounded-lg bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <caption class="sr-only">Listado de sucursales registradas</caption>
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Nombre oficial</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Clave</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Alcaldía</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Titular</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Estatus</th>
                    <th scope="col" class="px-4 py-3"><span class="sr-only">Acciones</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($sucursales as $sucursal)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $sucursal->nombre_oficial }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $sucursal->clave_financiera }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $sucursal->ubicacion?->alcaldia?->nombre ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $sucursal->titular?->nombre_completo ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <x-sucursales.estatus-badge :estatus="$sucursal->estatus_operativo" />
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('sucursales.show', $sucursal) }}"
                                class="font-semibold text-brand-green hover:underline focus:outline-none focus:ring-2 focus:ring-brand-green rounded">
                                Ver detalle
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
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
</x-sies-layout>
