@php /** @var \Illuminate\Support\Collection $sucursales */ @endphp

@cannot('sucursales.ver')
    <p class="text-sm text-gray-500">No tienes permiso para consultar el directorio de sucursales.</p>
@else
<div class="overflow-x-auto rounded-lg bg-white shadow-sm">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <caption class="sr-only">Directorio de sucursales</caption>
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Nombre oficial</th>
                <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Alcaldía</th>
                <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Teléfono</th>
                <th scope="col" class="px-4 py-3"><span class="sr-only">Acciones</span></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($sucursales as $sucursal)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $sucursal->etiqueta }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $sucursal->ubicacion?->alcaldia?->nombre ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $sucursal->operacion?->telefono ?? '—' }}</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('sucursales.show', $sucursal) }}" class="font-semibold text-brand-green hover:underline">
                            Ver ficha completa
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">Sin sucursales registradas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<p class="mt-3 text-xs text-gray-500">
    Los datos completos de cada sucursal (identificación, ubicación, horarios, inmueble, equipamiento y finanzas)
    se administran desde el módulo <a href="{{ route('sucursales.index') }}" class="text-brand-green hover:underline">Sucursales</a>.
</p>
@endcannot
