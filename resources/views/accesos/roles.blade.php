<x-sies-layout :title="'Catálogo de roles'">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <a href="{{ route('accesos.index') }}" class="text-sm text-brand-green hover:underline">&larr; Control de Accesos</a>
            <h1 class="text-2xl font-bold text-gray-900">Catálogo de roles y permisos</h1>
            <p class="mt-1 text-sm text-gray-500">
                Marca o desmarca las casillas para cambiar qué permisos trae cada rol por defecto y da clic en "Guardar"
                en esa columna. Para dar acceso adicional a una persona puntual sin tocar su rol, usa "Permisos
                especiales" desde su ficha en Control de Accesos.
            </p>
        </div>
        <div class="flex shrink-0 gap-3">
            <button type="button" x-data x-on:click="$dispatch('open-modal', 'nuevo-permiso')"
                class="text-sm font-semibold text-brand-green hover:underline">
                + Nuevo permiso
            </button>
            <button type="button" x-data x-on:click="$dispatch('open-modal', 'nuevo-rol')"
                class="text-sm font-semibold text-brand-green hover:underline">
                + Nuevo rol
            </button>
        </div>
    </div>

    <div class="mt-4 overflow-x-auto rounded-lg bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <caption class="sr-only">Matriz de roles y permisos</caption>
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="sticky left-0 bg-gray-50 px-4 py-3 text-left font-semibold text-gray-600">Permiso</th>
                    @foreach ($roles as $rol)
                        <th scope="col" class="px-3 py-3 text-center font-semibold text-gray-600">
                            <div>{{ ucfirst($rol->name) }}</div>
                            @if ($rol->name === 'administrador')
                                <span class="mt-1 block text-xs font-normal text-gray-400">Siempre todos</span>
                            @else
                                <div class="mt-1.5 flex items-center justify-center gap-2">
                                    <button type="submit" form="form-rol-{{ $rol->id }}"
                                        class="rounded bg-brand-green px-2 py-0.5 text-xs font-semibold text-white hover:bg-brand-green-dark">
                                        Guardar
                                    </button>
                                    @if (! $rol->users->count())
                                        <form method="POST" action="{{ route('accesos.roles.destroy', $rol) }}" onsubmit="return confirm('¿Eliminar el rol &quot;{{ $rol->name }}&quot;? Esta acción no se puede deshacer.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded p-1 text-red-500 hover:bg-red-50" title="Eliminar rol">
                                                <x-action-icon icon="trash" label="Eliminar rol" />
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($permisos as $permiso)
                    <tr class="hover:bg-gray-50">
                        <td class="sticky left-0 bg-white px-4 py-2 font-mono text-xs text-gray-700">{{ $permiso->name }}</td>
                        @foreach ($roles as $rol)
                            <td class="px-3 py-2 text-center">
                                @if ($rol->name === 'administrador')
                                    <input type="checkbox" checked disabled class="rounded border-gray-300 text-brand-green">
                                @else
                                    <input type="checkbox" form="form-rol-{{ $rol->id }}" name="permisos[]" value="{{ $permiso->name }}"
                                        @checked($rol->hasPermissionTo($permiso))
                                        class="rounded border-gray-300 text-brand-green focus:ring-brand-green">
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Un <form> por rol (fuera de la tabla) al que apuntan sus casillas vía el atributo "form". --}}
    @foreach ($roles as $rol)
        @unless ($rol->name === 'administrador')
            <form id="form-rol-{{ $rol->id }}" method="POST" action="{{ route('accesos.roles.permisos.update', $rol) }}">
                @csrf
                @method('PUT')
            </form>
        @endunless
    @endforeach

    <x-modal name="nuevo-rol" focusable>
        <form method="POST" action="{{ route('accesos.roles.store') }}" class="p-6 space-y-4">
            @csrf
            <h2 class="text-lg font-medium text-gray-900">Nuevo rol</h2>
            <p class="text-sm text-gray-500">Se crea sin permisos; agrégaselos después desde la matriz de esta página.</p>
            <div>
                <x-input-label for="rol-nombre" value="Nombre" />
                <x-text-input id="rol-nombre" name="name" type="text" class="mt-1 block w-full" required maxlength="50" placeholder="p. ej. logistica" />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close-modal', 'nuevo-rol')" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                    Cancelar
                </button>
                <button type="submit"
                    class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                    Crear rol
                </button>
            </div>
        </form>
    </x-modal>

    <x-modal name="nuevo-permiso" focusable>
        <form method="POST" action="{{ route('accesos.permisos.store') }}" class="p-6 space-y-4">
            @csrf
            <h2 class="text-lg font-medium text-gray-900">Nuevo permiso</h2>
            <p class="text-sm text-gray-500">
                Formato "modulo.accion" (p. ej. "logistica.ver"). Un permiso nuevo no protege ninguna pantalla por sí
                solo — debe usarse en el código de esa función para que tenga efecto. Se asigna automáticamente al
                rol "administrador".
            </p>
            <div>
                <x-input-label for="permiso-nombre" value="Nombre" />
                <x-text-input id="permiso-nombre" name="name" type="text" class="mt-1 block w-full font-mono" required maxlength="100" placeholder="logistica.ver" />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close-modal', 'nuevo-permiso')" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                    Cancelar
                </button>
                <button type="submit"
                    class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                    Crear permiso
                </button>
            </div>
        </form>
    </x-modal>
</x-sies-layout>
