@php /** @var \Illuminate\Support\Collection $boletines */ @endphp

<div>
    <div class="mb-3 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-800">Boletines informativos</h3>
        @can('minutarios.gestionar')
            <button type="button" x-on:click="$dispatch('open-modal', 'agregar-boletin')"
                class="text-xs font-semibold text-brand-green hover:underline">
                + Nuevo boletín
            </button>
        @endcan
    </div>

    <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <caption class="sr-only">Boletines informativos</caption>
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Folio</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Asunto</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Cobertura</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Elaboración</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Vigor</th>
                    <th scope="col" class="px-4 py-3"><span class="sr-only">Acciones</span></th>
                </tr>
            </thead>
            <tbody x-data="{ editandoId: null }" class="divide-y divide-gray-100">
                @forelse ($boletines as $boletin)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $boletin->nomenclatura }}</td>
                        <td class="px-4 py-3 max-w-sm text-gray-600">{{ \Illuminate\Support\Str::title($boletin->asunto) }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ App\Models\MinutarioBoletin::COBERTURAS[$boletin->dirigido_tipo] }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $boletin->fecha_elaboracion->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $boletin->fecha_vigor->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-right">
                            @can('minutarios.gestionar')
                                <div class="flex justify-end gap-1">
                                    <button type="button" x-show="editandoId !== {{ $boletin->id }}"
                                        x-on:click="editandoId = {{ $boletin->id }}"
                                        class="rounded p-1.5 text-brand-green hover:bg-brand-green-50">
                                        <x-action-icon icon="pencil" label="Editar" />
                                    </button>
                                    <button type="button" x-show="editandoId === {{ $boletin->id }}" x-cloak
                                        x-on:click="editandoId = null"
                                        class="rounded p-1.5 text-gray-500 hover:bg-gray-100">
                                        <x-action-icon icon="x-mark" label="Cancelar" />
                                    </button>
                                </div>
                            @endcan
                        </td>
                    </tr>
                    @can('minutarios.gestionar')
                        <tr x-show="editandoId === {{ $boletin->id }}" x-cloak>
                            <td colspan="6" class="bg-gray-50 px-4 py-4">
                                <form method="POST" action="{{ route('minutarios.boletines.update', $boletin) }}" class="space-y-3">
                                    @csrf
                                    @method('PUT')
                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                        <div class="sm:col-span-2">
                                            <x-input-label value="Asunto" />
                                            <x-text-input name="asunto" type="text" class="mt-1 block w-full" required maxlength="255" value="{{ $boletin->asunto }}" />
                                        </div>
                                        <div>
                                            <x-input-label value="Fecha de vigor" />
                                            <x-text-input name="fecha_vigor" type="date" class="mt-1 block w-full" required value="{{ $boletin->fecha_vigor->format('Y-m-d') }}" />
                                        </div>
                                        <div>
                                            <x-input-label value="Cobertura" />
                                            <select name="dirigido_tipo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                                                @foreach (App\Models\MinutarioBoletin::COBERTURAS as $valor => $etiqueta)
                                                    <option value="{{ $valor }}" @selected($boletin->dirigido_tipo === $valor)>{{ $etiqueta }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="sm:col-span-2">
                                            <x-input-label value="Contenido" />
                                            <textarea name="contenido" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">{{ $boletin->contenido }}</textarea>
                                        </div>
                                    </div>
                                    <button type="submit" class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                                        Guardar cambios
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endcan
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">Sin boletines registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @can('minutarios.gestionar')
        <x-modal name="agregar-boletin" focusable>
            <form method="POST" action="{{ route('minutarios.boletines.store') }}" class="p-6 space-y-4">
                @csrf
                <h2 class="text-lg font-medium text-gray-900">Nuevo boletín</h2>
                <p class="text-xs text-gray-500">El folio se asigna automáticamente al guardar (consecutivo por año).</p>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <x-input-label for="boletin-asunto" value="Asunto" />
                        <x-text-input id="boletin-asunto" name="asunto" type="text" class="mt-1 block w-full" required maxlength="255" />
                        <x-input-error :messages="$errors->get('asunto')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="boletin-vigor" value="Fecha de vigor" />
                        <x-text-input id="boletin-vigor" name="fecha_vigor" type="date" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('fecha_vigor')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="boletin-cobertura" value="Cobertura" />
                        <select id="boletin-cobertura" name="dirigido_tipo" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                            @foreach (App\Models\MinutarioBoletin::COBERTURAS as $valor => $etiqueta)
                                <option value="{{ $valor }}">{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label for="boletin-contenido" value="Contenido" />
                        <textarea id="boletin-contenido" name="contenido" rows="6" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green"></textarea>
                        <x-input-error :messages="$errors->get('contenido')" class="mt-1" />
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close-modal', 'agregar-boletin')" class="text-sm font-medium text-gray-600 hover:text-gray-900">
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
