@php /** @var \Illuminate\Support\Collection $areas */ @endphp

<div>
    <div class="mb-3 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-800">Áreas centrales</h3>
        @can('directorios.gestionar')
            <button type="button" x-on:click="$dispatch('open-modal', 'agregar-area-central')"
                class="text-xs font-semibold text-brand-green hover:underline">
                + Agregar área central
            </button>
        @endcan
    </div>

    <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <caption class="sr-only">Directorio de áreas centrales</caption>
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Nombre / puesto</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Adscripción</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Contacto</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Estatus</th>
                    <th scope="col" class="px-4 py-3"><span class="sr-only">Acciones</span></th>
                </tr>
            </thead>
            <tbody x-data="{ editandoId: null }" class="divide-y divide-gray-100">
                @forelse ($areas as $area)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900">{{ $area->nombre }}</p>
                            @if ($area->puesto)
                                <p class="text-xs text-gray-500">{{ $area->puesto }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $area->adscripcion }}
                            @if ($area->gerencia)
                                <div class="text-xs text-gray-400">{{ $area->gerencia->nombre }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            <div>{{ $area->correo_finabien }}</div>
                            <div class="text-xs text-gray-400">
                                {{ $area->telefono ?? '—' }} @if($area->extension) · Ext. {{ $area->extension }} @endif
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $area->activo ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                {{ $area->activo ? 'Activa' : 'Inactiva' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @can('directorios.gestionar')
                                <div class="flex justify-end gap-3">
                                    <button type="button" x-show="editandoId !== {{ $area->id }}"
                                        x-on:click="editandoId = {{ $area->id }}"
                                        class="text-xs font-semibold text-brand-green hover:underline">Editar</button>
                                    <button type="button" x-show="editandoId === {{ $area->id }}" x-cloak
                                        x-on:click="editandoId = null"
                                        class="text-xs text-gray-500 hover:underline">Cancelar</button>
                                    <form method="POST" action="{{ route('directorios.areas.estado', $area) }}"
                                        onsubmit="return confirm('¿{{ $area->activo ? 'Dar de baja' : 'Reactivar' }} esta área del directorio?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-xs font-semibold {{ $area->activo ? 'text-red-600' : 'text-emerald-600' }} hover:underline">
                                            {{ $area->activo ? 'Dar de baja' : 'Reactivar' }}
                                        </button>
                                    </form>
                                </div>
                            @endcan
                        </td>
                    </tr>
                    <tr x-show="editandoId === {{ $area->id }}" x-cloak>
                        <td colspan="5" class="bg-gray-50 px-4 py-4">
                            <form method="POST" action="{{ route('directorios.areas.update', $area) }}"
                                class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                                @csrf
                                @method('PUT')

                                <div>
                                    <x-input-label value="Nombre" />
                                    <x-text-input name="nombre" type="text" class="mt-1 block w-full" required maxlength="150" value="{{ $area->nombre }}" />
                                </div>
                                <div>
                                    <x-input-label value="Puesto" />
                                    <x-text-input name="puesto" type="text" class="mt-1 block w-full" maxlength="150" value="{{ $area->puesto }}" />
                                </div>
                                <div>
                                    <x-input-label value="Adscripción" />
                                    <x-text-input name="adscripcion" type="text" class="mt-1 block w-full" required maxlength="150" value="{{ $area->adscripcion }}" />
                                </div>
                                <div>
                                    <x-input-label value="Gerencia" />
                                    <select name="gerencia_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                                        <option value="">Sin asignar</option>
                                        @foreach ($gerencias as $gerenciaOpcion)
                                            <option value="{{ $gerenciaOpcion->id }}" @selected($area->gerencia_id === $gerenciaOpcion->id)>{{ $gerenciaOpcion->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label value="Teléfono" />
                                    <x-text-input name="telefono" type="text" class="mt-1 block w-full" maxlength="20" value="{{ $area->telefono }}" />
                                </div>
                                <div>
                                    <x-input-label value="Extensión" />
                                    <x-text-input name="extension" type="text" class="mt-1 block w-full" maxlength="10" value="{{ $area->extension }}" />
                                </div>
                                <div>
                                    <x-input-label value="Correo institucional" />
                                    <x-text-input name="correo_finabien" type="email" class="mt-1 block w-full" required maxlength="255" value="{{ $area->correo_finabien }}" />
                                </div>
                                <div>
                                    <x-input-label value="Correo SIGITEL" />
                                    <x-text-input name="correo_sigitel" type="email" class="mt-1 block w-full" maxlength="255" value="{{ $area->correo_sigitel }}" />
                                </div>
                                <div class="sm:col-span-2 lg:col-span-4">
                                    <x-input-label value="Observaciones" />
                                    <textarea name="observaciones" rows="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">{{ $area->observaciones }}</textarea>
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
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">Sin áreas centrales registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @can('directorios.gestionar')
        <x-modal name="agregar-area-central" focusable>
            <form method="POST" action="{{ route('directorios.areas.store') }}" class="p-6 space-y-4">
                @csrf
                <h2 class="text-lg font-medium text-gray-900">Agregar área central</h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="area-nombre" value="Nombre" />
                        <x-text-input id="area-nombre" name="nombre" type="text" class="mt-1 block w-full" required maxlength="150" />
                        <x-input-error :messages="$errors->get('nombre')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="area-puesto" value="Puesto (opcional)" />
                        <x-text-input id="area-puesto" name="puesto" type="text" class="mt-1 block w-full" maxlength="150" />
                    </div>
                    <div>
                        <x-input-label for="area-adscripcion" value="Adscripción" />
                        <x-text-input id="area-adscripcion" name="adscripcion" type="text" class="mt-1 block w-full" required maxlength="150" />
                    </div>
                    <div>
                        <x-input-label for="area-gerencia" value="Gerencia (opcional)" />
                        <select id="area-gerencia" name="gerencia_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                            <option value="">Sin asignar</option>
                            @foreach ($gerencias as $gerenciaOpcion)
                                <option value="{{ $gerenciaOpcion->id }}">{{ $gerenciaOpcion->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="area-telefono" value="Teléfono (opcional)" />
                        <x-text-input id="area-telefono" name="telefono" type="text" class="mt-1 block w-full" maxlength="20" />
                    </div>
                    <div>
                        <x-input-label for="area-extension" value="Extensión (opcional)" />
                        <x-text-input id="area-extension" name="extension" type="text" class="mt-1 block w-full" maxlength="10" />
                    </div>
                    <div>
                        <x-input-label for="area-correo" value="Correo institucional" />
                        <x-text-input id="area-correo" name="correo_finabien" type="email" class="mt-1 block w-full" required maxlength="255" />
                        <x-input-error :messages="$errors->get('correo_finabien')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="area-sigitel" value="Correo SIGITEL (opcional)" />
                        <x-text-input id="area-sigitel" name="correo_sigitel" type="email" class="mt-1 block w-full" maxlength="255" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label for="area-obs" value="Observaciones (opcional)" />
                        <textarea id="area-obs" name="observaciones" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close-modal', 'agregar-area-central')" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                        Agregar
                    </button>
                </div>
            </form>
        </x-modal>
    @endcan
</div>
