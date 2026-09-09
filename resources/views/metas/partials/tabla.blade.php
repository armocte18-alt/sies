<div class="overflow-x-auto rounded-lg bg-white shadow-sm">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <caption class="sr-only">Indicadores mensuales por sucursal</caption>
        <thead class="bg-gray-50">
            <tr>
                <x-sortable-th field="sucursal" label="Sucursal" />
                <x-sortable-th field="volumen" label="Volumen" />
                <x-sortable-th field="ingresos" label="Ingresos" />
                <x-sortable-th field="gasto" label="Gasto" />
                <x-sortable-th field="balance" label="Balance" />
                <x-sortable-th field="productividad" label="Productividad" />
                <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Empleados</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($indicadores as $indicador)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">
                        @if ($indicador->sucursal)
                            <a href="{{ route('sucursales.show', $indicador->sucursal) }}" class="text-brand-green hover:underline">
                                {{ $indicador->sucursal->etiqueta }}
                            </a>
                        @else
                            {{ \Illuminate\Support\Str::title($indicador->nombre_sucursal_legacy ?? '—') }}
                            <span class="text-xs text-gray-400">({{ $indicador->clave_sucursal_legacy }})</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right text-gray-600">{{ number_format($indicador->volumen_total) }}</td>
                    <td class="px-4 py-3 text-right text-gray-600">${{ number_format($indicador->ingresos_total, 2) }}</td>
                    <td class="px-4 py-3 text-right text-gray-600">${{ number_format($indicador->gasto_total, 2) }}</td>
                    <td class="px-4 py-3 text-right">
                        <span class="font-semibold {{ $indicador->balance >= 0 ? 'text-emerald-700' : 'text-red-600' }}">
                            ${{ number_format($indicador->balance, 2) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right text-gray-600">{{ number_format($indicador->productividad, 2) }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $indicador->total_empleados }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                        No hay indicadores capturados para el periodo seleccionado.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $indicadores->links() }}
</div>
