<div class="overflow-x-auto rounded-lg bg-white shadow-sm">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <caption class="sr-only">Listado de circulares</caption>
        <thead class="bg-gray-50">
            <tr>
                <x-sortable-th field="numero" label="Número" />
                <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Asunto</th>
                <x-sortable-th field="ambito" label="Ámbito" />
                <x-sortable-th field="fecha" label="Fecha de aplicación" />
                <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Archivo</th>
                <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Estatus</th>
                <th scope="col" class="px-4 py-3"><span class="sr-only">Acciones</span></th>
            </tr>
        </thead>
        <tbody x-data="{ editandoId: null }" class="divide-y divide-gray-100">
            @forelse ($circulares as $circular)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $circular->numero }}</td>
                    <td class="px-4 py-3 max-w-md text-gray-600">{{ \Illuminate\Support\Str::title($circular->asunto) }}</td>
                    <td class="px-4 py-3 capitalize text-gray-600">{{ $circular->ambito ?: 'General' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $circular->fecha_aplicacion?->format('d/m/Y') ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @if ($circular->archivo_path)
                            <a href="{{ $circular->archivoUrl() }}" target="_blank" rel="noopener"
                                class="inline-flex items-center gap-1 font-semibold text-brand-green hover:underline">
                                PDF
                            </a>
                        @elseif ($circular->enlace_externo)
                            <a href="{{ $circular->enlace_externo }}" target="_blank" rel="noopener"
                                class="inline-flex items-center gap-1 font-semibold text-brand-green hover:underline">
                                Enlace
                            </a>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $circular->activo ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                            {{ $circular->activo ? 'Vigente' : 'De baja' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        @can('circulares.gestionar')
                            <div class="flex justify-end gap-1">
                                <button type="button" x-show="editandoId !== {{ $circular->id }}"
                                    x-on:click="editandoId = {{ $circular->id }}"
                                    class="rounded p-1.5 text-brand-green hover:bg-brand-green-50">
                                    <x-action-icon icon="pencil" label="Editar" />
                                </button>
                                <button type="button" x-show="editandoId === {{ $circular->id }}" x-cloak
                                    x-on:click="editandoId = null"
                                    class="rounded p-1.5 text-gray-500 hover:bg-gray-100">
                                    <x-action-icon icon="x-mark" label="Cancelar" />
                                </button>
                                <form method="POST" action="{{ route('circulares.estado', $circular) }}"
                                    onsubmit="return confirm('¿{{ $circular->activo ? 'Dar de baja' : 'Reactivar' }} esta circular?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" @class([
                                        'rounded p-1.5 hover:bg-red-50' => $circular->activo,
                                        'rounded p-1.5 hover:bg-emerald-50' => !$circular->activo,
                                        'text-red-600' => $circular->activo,
                                        'text-emerald-600' => !$circular->activo,
                                    ])>
                                        <x-action-icon :icon="$circular->activo ? 'ban' : 'check-circle'" :label="$circular->activo ? 'Dar de baja' : 'Reactivar'" />
                                    </button>
                                </form>
                            </div>
                        @endcan
                    </td>
                </tr>
                @can('circulares.gestionar')
                    <tr x-show="editandoId === {{ $circular->id }}" x-cloak>
                        <td colspan="7" class="bg-gray-50 px-4 py-4">
                            <form method="POST" action="{{ route('circulares.update', $circular) }}" enctype="multipart/form-data" class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                                @csrf
                                @method('PUT')

                                <div>
                                    <x-input-label value="Número" />
                                    <x-text-input name="numero" type="text" class="mt-1 block w-full" required maxlength="30" value="{{ $circular->numero }}" />
                                </div>
                                <div>
                                    <x-input-label value="Fecha de aplicación" />
                                    <x-text-input name="fecha_aplicacion" type="date" class="mt-1 block w-full" value="{{ $circular->fecha_aplicacion?->format('Y-m-d') }}" />
                                </div>
                                <div>
                                    <x-input-label value="Ámbito" />
                                    <x-text-input name="ambito" type="text" class="mt-1 block w-full" maxlength="60" value="{{ $circular->ambito }}" />
                                </div>
                                <div>
                                    <x-input-label value="Reemplazar archivo PDF" />
                                    <input name="archivo" type="file" accept="application/pdf"
                                        class="mt-1 block w-full text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-brand-green file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-white hover:file:bg-brand-green-dark">
                                </div>
                                <div class="sm:col-span-2 lg:col-span-4">
                                    <x-input-label value="Asunto" />
                                    <textarea name="asunto" rows="2" required maxlength="500" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">{{ $circular->asunto }}</textarea>
                                </div>
                                <div class="sm:col-span-2 lg:col-span-4">
                                    <x-input-label value="Enlace externo" />
                                    <x-text-input name="enlace_externo" type="url" class="mt-1 block w-full" maxlength="500" value="{{ $circular->enlace_externo }}" />
                                </div>
                                <div class="sm:col-span-2 lg:col-span-4">
                                    <x-input-label value="Tips / notas" />
                                    <textarea name="tips" rows="2" maxlength="2000" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">{{ $circular->tips }}</textarea>
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
                @endcan
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                        No se encontraron circulares.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $circulares->links() }}
</div>
