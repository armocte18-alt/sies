@php /** @var \Illuminate\Support\Collection $gerencias */ @endphp

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2 overflow-x-auto rounded-lg bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <caption class="sr-only">Directorio de gerencias</caption>
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Nombre</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Coordinación</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Contacto</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Estatus</th>
                    <th scope="col" class="px-4 py-3"><span class="sr-only">Acciones</span></th>
                </tr>
            </thead>
            <tbody x-data="{ editandoId: null }" class="divide-y divide-gray-100">
                @forelse ($gerencias as $gerencia)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $gerencia->nombre }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $gerencia->coordinacion }}</td>
                        <td class="px-4 py-3 text-gray-600">
                            <div>{{ $gerencia->correo_finabien }}</div>
                            @if ($gerencia->extension)
                                <div class="text-xs text-gray-400">Ext. {{ $gerencia->extension }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $gerencia->activo ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                {{ $gerencia->activo ? 'Activa' : 'Inactiva' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @can('directorios.gestionar')
                                <div class="flex justify-end gap-3">
                                    <button type="button" x-show="editandoId !== {{ $gerencia->id }}"
                                        x-on:click="editandoId = {{ $gerencia->id }}"
                                        class="text-xs font-semibold text-brand-green hover:underline">Editar</button>
                                    <button type="button" x-show="editandoId === {{ $gerencia->id }}" x-cloak
                                        x-on:click="editandoId = null"
                                        class="text-xs text-gray-500 hover:underline">Cancelar</button>
                                    <form method="POST" action="{{ route('directorios.gerencias.estado', $gerencia) }}"
                                        onsubmit="return confirm('¿{{ $gerencia->activo ? 'Dar de baja' : 'Reactivar' }} esta gerencia del directorio?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-xs font-semibold {{ $gerencia->activo ? 'text-red-600' : 'text-emerald-600' }} hover:underline">
                                            {{ $gerencia->activo ? 'Dar de baja' : 'Reactivar' }}
                                        </button>
                                    </form>
                                </div>
                            @endcan
                        </td>
                    </tr>
                    <tr x-show="editandoId === {{ $gerencia->id }}" x-cloak>
                        <td colspan="5" class="bg-gray-50 px-4 py-4">
                            <form method="POST" action="{{ route('directorios.gerencias.update', $gerencia) }}"
                                class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                                @csrf
                                @method('PUT')

                                <div>
                                    <x-input-label value="Nombre" />
                                    <x-text-input name="nombre" type="text" class="mt-1 block w-full" required maxlength="150" value="{{ $gerencia->nombre }}" />
                                </div>
                                <div>
                                    <x-input-label value="Coordinación" />
                                    <x-text-input name="coordinacion" type="text" class="mt-1 block w-full" required maxlength="150" value="{{ $gerencia->coordinacion }}" />
                                </div>
                                <div>
                                    <x-input-label value="Extensión" />
                                    <x-text-input name="extension" type="text" class="mt-1 block w-full" maxlength="10" value="{{ $gerencia->extension }}" />
                                </div>
                                <div>
                                    <x-input-label value="Comité" />
                                    <x-text-input name="comite" type="text" class="mt-1 block w-full" maxlength="150" value="{{ $gerencia->comite }}" />
                                </div>
                                <div>
                                    <x-input-label value="Correo institucional" />
                                    <x-text-input name="correo_finabien" type="email" class="mt-1 block w-full" required maxlength="255" value="{{ $gerencia->correo_finabien }}" />
                                </div>
                                <div>
                                    <x-input-label value="Correo SIGITEL" />
                                    <x-text-input name="correo_sigitel" type="email" class="mt-1 block w-full" maxlength="255" value="{{ $gerencia->correo_sigitel }}" />
                                </div>
                                <div class="sm:col-span-2 lg:col-span-2">
                                    <x-input-label value="Observaciones" />
                                    <textarea name="observaciones" rows="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">{{ $gerencia->observaciones }}</textarea>
                                </div>

                                <div class="sm:col-span-2 lg:col-span-4">
                                    <button type="submit"
                                        class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                                        Guardar cambios
                                    </button>
                                </div>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">Sin gerencias registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @can('directorios.gestionar')
        <div class="rounded-lg bg-white p-5 shadow-sm">
            <h3 class="mb-4 text-sm font-semibold text-gray-800">Agregar gerencia</h3>

            <form method="POST" action="{{ route('directorios.gerencias.store') }}" class="space-y-4">
                @csrf

                <div>
                    <x-input-label for="gerencia-nombre" value="Nombre" />
                    <x-text-input id="gerencia-nombre" name="nombre" type="text" class="mt-1 block w-full" required maxlength="150" />
                    <x-input-error :messages="$errors->get('nombre')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="gerencia-coordinacion" value="Coordinación" />
                    <x-text-input id="gerencia-coordinacion" name="coordinacion" type="text" class="mt-1 block w-full" required maxlength="150" />
                </div>
                <div>
                    <x-input-label for="gerencia-extension" value="Extensión (opcional)" />
                    <x-text-input id="gerencia-extension" name="extension" type="text" class="mt-1 block w-full" maxlength="10" />
                </div>
                <div>
                    <x-input-label for="gerencia-comite" value="Comité (opcional)" />
                    <x-text-input id="gerencia-comite" name="comite" type="text" class="mt-1 block w-full" maxlength="150" />
                </div>
                <div>
                    <x-input-label for="gerencia-correo" value="Correo institucional" />
                    <x-text-input id="gerencia-correo" name="correo_finabien" type="email" class="mt-1 block w-full" required maxlength="255" />
                    <x-input-error :messages="$errors->get('correo_finabien')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="gerencia-sigitel" value="Correo SIGITEL (opcional)" />
                    <x-text-input id="gerencia-sigitel" name="correo_sigitel" type="email" class="mt-1 block w-full" maxlength="255" />
                </div>
                <div>
                    <x-input-label for="gerencia-obs" value="Observaciones (opcional)" />
                    <textarea id="gerencia-obs" name="observaciones" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green"></textarea>
                </div>

                <button type="submit" class="w-full rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                    Agregar
                </button>
            </form>
        </div>
    @endcan
</div>
