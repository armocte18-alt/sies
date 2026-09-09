<x-sies-layout :title="'Control de Accesos'">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Control de Accesos</h1>
            <p class="mt-1 text-sm text-gray-500">Roles, permisos y estado de cuenta de cada usuario del sistema.</p>
        </div>

        <a href="{{ route('accesos.roles') }}"
            class="inline-flex w-fit items-center gap-2 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
            Ver catálogo de roles
        </a>
    </div>

    <form method="GET" action="{{ route('accesos.index') }}" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end" role="search">
        <div class="flex-1">
            <label for="buscar" class="sr-only">Buscar por nombre o correo</label>
            <input type="search" id="buscar" name="buscar" value="{{ request('buscar') }}"
                placeholder="Buscar por nombre o correo..."
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
        </div>

        <div>
            <label for="rol" class="sr-only">Filtrar por rol</label>
            <select id="rol" name="rol" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green sm:w-56">
                <option value="">Todos los roles</option>
                @foreach ($roles as $rol)
                    <option value="{{ $rol->name }}" @selected(request('rol') === $rol->name)>{{ ucfirst($rol->name) }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit"
            class="rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-800">
            Filtrar
        </button>
    </form>

    <div class="mt-4 overflow-x-auto rounded-lg bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <caption class="sr-only">Listado de usuarios del sistema</caption>
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Usuario</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Coordinación</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Roles</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Estado</th>
                    <th scope="col" class="px-4 py-3"><span class="sr-only">Acciones</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($usuarios as $usuario)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ $usuario->avatar_url }}" alt="" class="h-8 w-8 rounded-full object-cover" aria-hidden="true">
                                <div>
                                    <p class="font-medium text-gray-900">
                                        {{ $usuario->name }}
                                        @if ($usuario->id === auth()->id())
                                            <span class="text-xs font-normal text-gray-400">(tú)</span>
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500">{{ $usuario->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $usuario->coordinacion?->nombre ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1">
                                @forelse ($usuario->roles as $rol)
                                    <span class="inline-flex rounded-full bg-brand-green/10 px-2 py-0.5 text-xs font-medium text-brand-green">
                                        {{ ucfirst($rol->name) }}
                                    </span>
                                @empty
                                    <span class="text-xs text-gray-400">Sin rol asignado</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $usuario->activo ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                {{ $usuario->activo ? 'Activa' : 'Desactivada' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-3">
                                <a href="{{ route('accesos.edit', $usuario) }}" class="font-semibold text-brand-green hover:underline">
                                    Gestionar accesos
                                </a>
                                @if ($usuario->id !== auth()->id())
                                    <form method="POST" action="{{ route('accesos.estado.update', $usuario) }}"
                                        onsubmit="return confirm('¿{{ $usuario->activo ? 'Desactivar' : 'Reactivar' }} la cuenta de {{ $usuario->name }}?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" @class([
                                            'rounded p-1.5 hover:bg-red-50' => $usuario->activo,
                                            'rounded p-1.5 hover:bg-emerald-50' => !$usuario->activo,
                                            'text-red-600' => $usuario->activo,
                                            'text-emerald-600' => !$usuario->activo,
                                        ])>
                                            <x-action-icon :icon="$usuario->activo ? 'ban' : 'check-circle'" :label="$usuario->activo ? 'Desactivar' : 'Reactivar'" />
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                            No se encontraron usuarios.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $usuarios->links() }}
    </div>
</x-sies-layout>
