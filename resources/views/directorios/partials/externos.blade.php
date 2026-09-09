@php /** @var \Illuminate\Support\Collection $externos */ @endphp

<div>
    <div class="mb-3 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-800">Personal externo</h3>
        @can('directorios.gestionar')
            <button type="button" x-on:click="$dispatch('open-modal', 'agregar-externo')"
                class="text-xs font-semibold text-brand-green hover:underline">
                + Agregar contacto externo
            </button>
        @endcan
    </div>

    <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <caption class="sr-only">Directorio de personal externo</caption>
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Nombre / puesto</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Dependencia</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Contacto</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Estatus</th>
                    <th scope="col" class="px-4 py-3"><span class="sr-only">Acciones</span></th>
                </tr>
            </thead>
            <tbody x-data="{ editandoId: null }" class="divide-y divide-gray-100">
                @forelse ($externos as $externo)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900">{{ $externo->nombre }}</p>
                            @if ($externo->puesto)
                                <p class="text-xs text-gray-500">{{ $externo->puesto }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $externo->dependencia }}
                            @if ($externo->adscripcion)
                                <div class="text-xs text-gray-400">{{ $externo->adscripcion }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            @foreach (($externo->telefonos ?: []) as $telefono)
                                <div class="text-xs">{{ $telefono }}</div>
                            @endforeach
                            @foreach (($externo->correos ?: []) as $correo)
                                <div class="text-xs text-gray-400">{{ $correo }}</div>
                            @endforeach
                            @if (empty($externo->telefonos) && empty($externo->correos))
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $externo->activo ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                {{ $externo->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @can('directorios.gestionar')
                                <div class="flex justify-end gap-1">
                                    <button type="button" x-show="editandoId !== {{ $externo->id }}"
                                        x-on:click="editandoId = {{ $externo->id }}"
                                        class="rounded p-1.5 text-brand-green hover:bg-brand-green-50">
                                        <x-action-icon icon="pencil" label="Editar" />
                                    </button>
                                    <button type="button" x-show="editandoId === {{ $externo->id }}" x-cloak
                                        x-on:click="editandoId = null"
                                        class="rounded p-1.5 text-gray-500 hover:bg-gray-100">
                                        <x-action-icon icon="x-mark" label="Cancelar" />
                                    </button>
                                    <form method="POST" action="{{ route('directorios.externos.estado', $externo) }}"
                                        onsubmit="return confirm('¿{{ $externo->activo ? 'Dar de baja' : 'Reactivar' }} este contacto del directorio?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" @class([
                                            'rounded p-1.5 hover:bg-red-50' => $externo->activo,
                                            'rounded p-1.5 hover:bg-emerald-50' => !$externo->activo,
                                            'text-red-600' => $externo->activo,
                                            'text-emerald-600' => !$externo->activo,
                                        ])>
                                            <x-action-icon :icon="$externo->activo ? 'ban' : 'check-circle'" :label="$externo->activo ? 'Dar de baja' : 'Reactivar'" />
                                        </button>
                                    </form>
                                </div>
                            @endcan
                        </td>
                    </tr>
                    <tr x-show="editandoId === {{ $externo->id }}" x-cloak>
                        <td colspan="5" class="bg-gray-50 px-4 py-4">
                            @include('directorios.partials.externo-form', [
                                'accion' => route('directorios.externos.update', $externo),
                                'metodo' => 'PUT',
                                'externo' => $externo,
                                'boton' => 'Guardar cambios',
                            ])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">Sin contactos externos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @can('directorios.gestionar')
        <x-modal name="agregar-externo" focusable>
            @include('directorios.partials.externo-form', [
                'accion' => route('directorios.externos.store'),
                'metodo' => null,
                'externo' => null,
                'boton' => 'Agregar',
                'modalName' => 'agregar-externo',
            ])
        </x-modal>
    @endcan
</div>
