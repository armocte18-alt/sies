<div class="overflow-x-auto rounded-lg bg-white shadow-sm">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <caption class="sr-only">Listado de sucursales registradas</caption>
        <thead class="bg-gray-50">
            <tr>
                <x-sortable-th field="nombre" label="Nombre oficial" />
                <x-sortable-th field="clave" label="Clave" />
                <x-sortable-th field="alcaldia" label="Alcaldía" />
                <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Titular</th>
                <x-sortable-th field="estatus" label="Estatus" />
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
