@php
    /**
     * @var string $catalogo clave de ruta (p.ej. 'nombramientos')
     * @var string $titulo
     * @var \Illuminate\Support\Collection $items
     */
@endphp

<div>
    <div class="mb-3 flex items-center justify-between">
        <h3 class="text-sm font-semibold capitalize text-gray-800">{{ $titulo }}</h3>
        <button type="button" x-on:click="$dispatch('open-modal', 'agregar-{{ $catalogo }}')"
            class="text-xs font-semibold text-brand-green hover:underline">
            + Agregar a {{ $titulo }}
        </button>
    </div>

    <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <caption class="sr-only">Catálogo de {{ $titulo }}</caption>
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Nombre</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Descripción</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Estatus</th>
                    <th scope="col" class="px-4 py-3"><span class="sr-only">Acciones</span></th>
                </tr>
            </thead>
            <tbody x-data="{ editandoId: null }" class="divide-y divide-gray-100">
                @forelse ($items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $item->nombre }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $item->descripcion ?: '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $item->activo ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                {{ $item->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-1">
                                <button type="button" x-show="editandoId !== {{ $item->id }}"
                                    x-on:click="editandoId = {{ $item->id }}"
                                    class="rounded p-1.5 text-brand-green hover:bg-brand-green-50">
                                    <x-action-icon icon="pencil" label="Editar" />
                                </button>
                                <button type="button" x-show="editandoId === {{ $item->id }}" x-cloak
                                    x-on:click="editandoId = null"
                                    class="rounded p-1.5 text-gray-500 hover:bg-gray-100">
                                    <x-action-icon icon="x-mark" label="Cancelar" />
                                </button>
                                <form method="POST" action="{{ route('rh.catalogos.items.destroy', [$catalogo, $item->id]) }}"
                                    onsubmit="return confirm('¿Eliminar este elemento del catálogo?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded p-1.5 text-red-600 hover:bg-red-50">
                                        <x-action-icon icon="trash" label="Eliminar" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <tr x-show="editandoId === {{ $item->id }}" x-cloak>
                        <td colspan="4" class="bg-gray-50 px-4 py-4">
                            <form method="POST" action="{{ route('rh.catalogos.items.update', [$catalogo, $item->id]) }}"
                                class="grid grid-cols-1 gap-3 sm:grid-cols-4 sm:items-end">
                                @csrf
                                @method('PUT')

                                <div>
                                    <x-input-label value="Nombre" />
                                    <x-text-input name="nombre" type="text" class="mt-1 block w-full" required maxlength="150"
                                        value="{{ $item->nombre }}" />
                                </div>

                                <div>
                                    <x-input-label value="Descripción" />
                                    <x-text-input name="descripcion" type="text" class="mt-1 block w-full" maxlength="255"
                                        value="{{ $item->descripcion }}" />
                                </div>

                                <label class="flex items-center gap-2 text-sm text-gray-600">
                                    <input type="checkbox" name="activo" value="1" @checked($item->activo)
                                        class="rounded border-gray-300 text-brand-green focus:ring-brand-green">
                                    Activo
                                </label>

                                <button type="submit"
                                    class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                                    Guardar cambios
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                            Sin elementos registrados en este catálogo.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <x-modal name="agregar-{{ $catalogo }}" focusable>
        <form method="POST" action="{{ route('rh.catalogos.items.store', $catalogo) }}" class="p-6 space-y-4">
            @csrf
            <h2 class="text-lg font-medium capitalize text-gray-900">Agregar a {{ $titulo }}</h2>

            <div>
                <x-input-label for="nombre-{{ $catalogo }}" value="Nombre" />
                <x-text-input id="nombre-{{ $catalogo }}" name="nombre" type="text" class="mt-1 block w-full" required maxlength="150" />
            </div>

            <div>
                <x-input-label for="descripcion-{{ $catalogo }}" value="Descripción (opcional)" />
                <x-text-input id="descripcion-{{ $catalogo }}" name="descripcion" type="text" class="mt-1 block w-full" maxlength="255" />
            </div>

            <div>
                <x-input-label for="orden-{{ $catalogo }}" value="Orden (opcional)" />
                <x-text-input id="orden-{{ $catalogo }}" name="orden" type="number" min="0" class="mt-1 block w-full" />
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close-modal', 'agregar-{{ $catalogo }}')" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                    Cancelar
                </button>
                <button type="submit" class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                    Agregar
                </button>
            </div>
        </form>
    </x-modal>
</div>
