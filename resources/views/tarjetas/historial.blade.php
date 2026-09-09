<x-sies-layout :title="'Historial de Tarjetas'">
    <a href="{{ route('tarjetas.index') }}" class="text-sm text-brand-green hover:underline">&larr; Control de Tarjetas</a>
    <h1 class="mt-1 text-2xl font-bold text-gray-900">Historial de movimientos</h1>

    <div class="mt-4 overflow-x-auto rounded-lg bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <caption class="sr-only">Historial de movimientos de tarjetas</caption>
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Fecha</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Tarjeta</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Acción</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Cambio de estatus</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Motivo</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Usuario</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($historial as $registro)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-600">{{ $registro->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">•••• {{ $registro->tarjeta->numero_tarjeta }}</td>
                        <td class="px-4 py-3 capitalize text-gray-600">{{ str_replace('_', ' ', $registro->accion) }}</td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ App\Models\TarjetaInventario::ESTATUS_LABELS[$registro->estatus_anterior] ?? $registro->estatus_anterior }}
                            &rarr;
                            {{ App\Models\TarjetaInventario::ESTATUS_LABELS[$registro->estatus_nuevo] ?? $registro->estatus_nuevo }}
                        </td>
                        <td class="px-4 py-3 max-w-xs text-gray-600">{{ $registro->motivo ?: '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $registro->usuario }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">Sin movimientos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $historial->links() }}</div>
</x-sies-layout>
