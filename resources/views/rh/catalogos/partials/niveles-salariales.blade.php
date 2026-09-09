@php
    /** @var \Illuminate\Support\Collection $nivelesSalariales */
@endphp

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2 overflow-x-auto rounded-lg bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <caption class="sr-only">Catálogo de niveles salariales</caption>
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Consecutivo</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Nivel salarial</th>
                    <th scope="col" class="px-4 py-3 text-right font-semibold text-gray-600">Sueldo base</th>
                    <th scope="col" class="px-4 py-3 text-right font-semibold text-gray-600">Compensación garantizada</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Observaciones</th>
                    <th scope="col" class="px-4 py-3"><span class="sr-only">Acciones</span></th>
                </tr>
            </thead>
            <tbody x-data="{ editandoId: null }" class="divide-y divide-gray-100">
                @forelse ($nivelesSalariales as $nivel)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-600">{{ $nivel->consecutivo }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $nivel->nivel_salarial }}</td>
                        <td class="px-4 py-3 text-right text-gray-800">${{ number_format($nivel->sueldo_base, 2) }}</td>
                        <td class="px-4 py-3 text-right text-gray-800">${{ number_format($nivel->compensacion_garantizada, 2) }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $nivel->observaciones ?: '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-3">
                                <button type="button" x-show="editandoId !== {{ $nivel->id }}"
                                    x-on:click="editandoId = {{ $nivel->id }}"
                                    class="text-xs font-semibold text-brand-green hover:underline">
                                    Editar
                                </button>
                                <button type="button" x-show="editandoId === {{ $nivel->id }}" x-cloak
                                    x-on:click="editandoId = null"
                                    class="text-xs text-gray-500 hover:underline">
                                    Cancelar
                                </button>
                                <form method="POST" action="{{ route('rh.catalogos.niveles-salariales.destroy', $nivel) }}"
                                    onsubmit="return confirm('¿Eliminar este nivel salarial?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-red-600 hover:underline">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <tr x-show="editandoId === {{ $nivel->id }}" x-cloak>
                        <td colspan="6" class="bg-gray-50 px-4 py-4">
                            <form method="POST" action="{{ route('rh.catalogos.niveles-salariales.update', $nivel) }}"
                                class="grid grid-cols-1 gap-3 sm:grid-cols-3 lg:grid-cols-6 sm:items-end">
                                @csrf
                                @method('PUT')

                                <div>
                                    <x-input-label value="Consecutivo" />
                                    <x-text-input name="consecutivo" type="number" min="1" class="mt-1 block w-full" required value="{{ $nivel->consecutivo }}" />
                                </div>
                                <div>
                                    <x-input-label value="Nivel salarial" />
                                    <x-text-input name="nivel_salarial" type="text" class="mt-1 block w-full" required maxlength="50" value="{{ $nivel->nivel_salarial }}" />
                                </div>
                                <div>
                                    <x-input-label value="Sueldo base" />
                                    <x-text-input name="sueldo_base" type="number" step="0.01" min="0" class="mt-1 block w-full" required value="{{ $nivel->sueldo_base }}" />
                                </div>
                                <div>
                                    <x-input-label value="Compensación garantizada" />
                                    <x-text-input name="compensacion_garantizada" type="number" step="0.01" min="0" class="mt-1 block w-full" value="{{ $nivel->compensacion_garantizada }}" />
                                </div>
                                <div class="lg:col-span-1">
                                    <x-input-label value="Observaciones" />
                                    <x-text-input name="observaciones" type="text" class="mt-1 block w-full" maxlength="255" value="{{ $nivel->observaciones }}" />
                                </div>

                                <div class="flex items-center gap-3">
                                    <label class="flex items-center gap-2 text-sm text-gray-600">
                                        <input type="checkbox" name="activo" value="1" @checked($nivel->activo)
                                            class="rounded border-gray-300 text-brand-green focus:ring-brand-green">
                                        Activo
                                    </label>
                                    <button type="submit"
                                        class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                                        Guardar
                                    </button>
                                </div>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                            Sin niveles salariales registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="rounded-lg bg-white p-5 shadow-sm">
        <h3 class="mb-4 text-sm font-semibold text-gray-800">Agregar nivel salarial</h3>

        <form method="POST" action="{{ route('rh.catalogos.niveles-salariales.store') }}" class="space-y-4">
            @csrf

            <div>
                <x-input-label for="consecutivo" value="Consecutivo" />
                <x-text-input id="consecutivo" name="consecutivo" type="number" min="1" class="mt-1 block w-full" required />
            </div>

            <div>
                <x-input-label for="nivel_salarial" value="Nivel salarial" />
                <x-text-input id="nivel_salarial" name="nivel_salarial" type="text" class="mt-1 block w-full" required maxlength="50" placeholder="Ej. 15 A1" />
            </div>

            <div>
                <x-input-label for="sueldo_base" value="Sueldo base" />
                <x-text-input id="sueldo_base" name="sueldo_base" type="number" step="0.01" min="0" class="mt-1 block w-full" required placeholder="10000.00" />
            </div>

            <div>
                <x-input-label for="compensacion_garantizada" value="Compensación garantizada" />
                <x-text-input id="compensacion_garantizada" name="compensacion_garantizada" type="number" step="0.01" min="0" class="mt-1 block w-full" placeholder="7500.00" />
            </div>

            <div>
                <x-input-label for="observaciones" value="Observaciones (opcional)" />
                <x-text-input id="observaciones" name="observaciones" type="text" class="mt-1 block w-full" maxlength="255" />
            </div>

            <button type="submit" class="w-full rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                Agregar
            </button>
        </form>
    </div>
</div>
