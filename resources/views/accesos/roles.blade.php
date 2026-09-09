<x-sies-layout :title="'Catálogo de roles'">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="{{ route('accesos.index') }}" class="text-sm text-brand-green hover:underline">&larr; Control de Accesos</a>
            <h1 class="text-2xl font-bold text-gray-900">Catálogo de roles y permisos</h1>
            <p class="mt-1 text-sm text-gray-500">
                Referencia de lo que cada rol puede hacer por defecto. Para dar acceso adicional a una persona
                puntual, usa "Permisos especiales" desde su ficha en Control de Accesos.
            </p>
        </div>
    </div>

    <div class="mt-4 overflow-x-auto rounded-lg bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <caption class="sr-only">Matriz de roles y permisos</caption>
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="sticky left-0 bg-gray-50 px-4 py-3 text-left font-semibold text-gray-600">Permiso</th>
                    @foreach ($roles as $rol)
                        <th scope="col" class="px-3 py-3 text-center font-semibold text-gray-600">{{ ucfirst($rol->name) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($permisos as $permiso)
                    <tr class="hover:bg-gray-50">
                        <td class="sticky left-0 bg-white px-4 py-2 font-mono text-xs text-gray-700">{{ $permiso->name }}</td>
                        @foreach ($roles as $rol)
                            <td class="px-3 py-2 text-center">
                                @if ($rol->hasPermissionTo($permiso))
                                    <span class="text-emerald-600" aria-label="Sí">✓</span>
                                @else
                                    <span class="text-gray-300" aria-hidden="true">—</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-sies-layout>
