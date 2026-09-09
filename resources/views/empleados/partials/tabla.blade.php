@php $sucursales = $sucursales ?? \App\Models\Sucursal::orderBy('clave_financiera')->get(['id', 'nombre_oficial', 'clave_financiera']); @endphp

<div class="overflow-x-auto rounded-lg bg-white shadow-sm">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <caption class="sr-only">Listado de empleados</caption>
        <thead class="bg-gray-50">
            <tr>
                <x-sortable-th field="no_empleado" label="No. Empleado" />
                <x-sortable-th field="nombre" label="Nombre completo" />
                <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Puesto</th>
                <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Función laboral</th>
                <x-sortable-th field="sucursal" label="Sucursal" />
                <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Estatus</th>
                <th scope="col" class="px-4 py-3"><span class="sr-only">Acciones</span></th>
            </tr>
        </thead>
        <tbody x-data="{ editandoId: null }" class="divide-y divide-gray-100">
            @forelse ($empleados as $empleado)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-600">{{ $empleado->no_empleado }}</td>
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $empleado->nombre_completo }}</td>
                    <td class="px-4 py-3 capitalize text-gray-600">{{ \Illuminate\Support\Str::title($empleado->puesto ?? '') ?: '—' }}</td>
                    <td class="px-4 py-3 uppercase text-gray-600">{{ $empleado->funcion_laboral ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-600">
                        {{ $empleado->sucursal?->etiqueta ?? '—' }}
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $empleado->activo ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                            {{ $empleado->activo ? 'Activo' : 'Baja' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        @can('empleados.gestionar')
                            <div class="flex justify-end gap-1">
                                <button type="button" x-show="editandoId !== {{ $empleado->id }}"
                                    x-on:click="editandoId = {{ $empleado->id }}"
                                    class="rounded p-1.5 text-brand-green hover:bg-brand-green-50">
                                    <x-action-icon icon="pencil" label="Editar" />
                                </button>
                                <button type="button" x-show="editandoId === {{ $empleado->id }}" x-cloak
                                    x-on:click="editandoId = null"
                                    class="rounded p-1.5 text-gray-500 hover:bg-gray-100">
                                    <x-action-icon icon="x-mark" label="Cancelar" />
                                </button>
                                <form method="POST" action="{{ route('empleados.estado', $empleado) }}"
                                    onsubmit="return confirm('¿{{ $empleado->activo ? 'Dar de baja' : 'Reactivar' }} a {{ $empleado->nombre_completo }}?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" @class([
                                        'rounded p-1.5 hover:bg-red-50' => $empleado->activo,
                                        'rounded p-1.5 hover:bg-emerald-50' => !$empleado->activo,
                                        'text-red-600' => $empleado->activo,
                                        'text-emerald-600' => !$empleado->activo,
                                    ])>
                                        <x-action-icon :icon="$empleado->activo ? 'ban' : 'check-circle'" :label="$empleado->activo ? 'Dar de baja' : 'Reactivar'" />
                                    </button>
                                </form>
                            </div>
                        @endcan
                    </td>
                </tr>
                @can('empleados.gestionar')
                    <tr x-show="editandoId === {{ $empleado->id }}" x-cloak>
                        <td colspan="7" class="bg-gray-50 px-4 py-4">
                            @include('empleados.partials.form', [
                                'accion' => route('empleados.update', $empleado),
                                'metodo' => 'PUT',
                                'empleado' => $empleado,
                                'boton' => 'Guardar cambios',
                                'modalName' => null,
                                'sucursales' => $sucursales,
                            ])
                        </td>
                    </tr>
                @endcan
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                        No se encontraron empleados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $empleados->links() }}
</div>
