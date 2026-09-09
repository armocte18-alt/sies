@php /** @var \Illuminate\Support\Collection $oficios */ @endphp

<div>
    <div class="mb-3 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-800">Oficios</h3>
        @can('minutarios.gestionar')
            <button type="button" x-on:click="$dispatch('open-modal', 'agregar-oficio')"
                class="text-xs font-semibold text-brand-green hover:underline">
                + Nuevo oficio
            </button>
        @endcan
    </div>

    <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <caption class="sr-only">Oficios</caption>
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Folio</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Asunto</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Dirigido a</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Emisión</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Escaneado</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Estatus</th>
                    <th scope="col" class="px-4 py-3"><span class="sr-only">Acciones</span></th>
                </tr>
            </thead>
            <tbody x-data="{ editandoId: null }" class="divide-y divide-gray-100">
                @forelse ($oficios as $oficio)
                    <tr class="hover:bg-gray-50 {{ $oficio->es_cancelado ? 'opacity-60' : '' }}">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $oficio->nomenclatura }}</td>
                        <td class="px-4 py-3 max-w-sm text-gray-600">{{ \Illuminate\Support\Str::title($oficio->asunto) }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ \Illuminate\Support\Str::title($oficio->dirigido_a) }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $oficio->fecha_emision->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            @can('minutarios.gestionar')
                                <form method="POST" action="{{ route('minutarios.oficios.escaneo', $oficio) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $oficio->ya_escaneado ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $oficio->ya_escaneado ? 'Escaneado' : 'Pendiente' }}
                                    </button>
                                </form>
                            @else
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $oficio->ya_escaneado ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $oficio->ya_escaneado ? 'Escaneado' : 'Pendiente' }}
                                </span>
                            @endcan
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $oficio->es_cancelado ? 'bg-red-100 text-red-800' : 'bg-emerald-100 text-emerald-800' }}">
                                {{ $oficio->es_cancelado ? 'Cancelado' : 'Vigente' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @can('minutarios.gestionar')
                                @unless ($oficio->es_cancelado)
                                    <div class="flex justify-end gap-1">
                                        <button type="button" x-show="editandoId !== {{ $oficio->id }}"
                                            x-on:click="editandoId = {{ $oficio->id }}"
                                            class="rounded p-1.5 text-brand-green hover:bg-brand-green-50">
                                            <x-action-icon icon="pencil" label="Editar" />
                                        </button>
                                        <button type="button" x-show="editandoId === {{ $oficio->id }}" x-cloak
                                            x-on:click="editandoId = null"
                                            class="rounded p-1.5 text-gray-500 hover:bg-gray-100">
                                            <x-action-icon icon="x-mark" label="Cancelar edición" />
                                        </button>
                                        <button type="button" x-on:click="$dispatch('open-modal', 'cancelar-oficio-{{ $oficio->id }}')"
                                            class="rounded p-1.5 text-red-600 hover:bg-red-50">
                                            <x-action-icon icon="ban" label="Cancelar oficio" />
                                        </button>
                                    </div>
                                @endunless
                            @endcan
                        </td>
                    </tr>
                    @can('minutarios.gestionar')
                        <tr x-show="editandoId === {{ $oficio->id }}" x-cloak>
                            <td colspan="7" class="bg-gray-50 px-4 py-4">
                                <form method="POST" action="{{ route('minutarios.oficios.update', $oficio) }}" class="grid grid-cols-1 gap-3 sm:grid-cols-2 sm:items-end">
                                    @csrf
                                    @method('PUT')
                                    <div>
                                        <x-input-label value="Asunto" />
                                        <x-text-input name="asunto" type="text" class="mt-1 block w-full" required value="{{ $oficio->asunto }}" />
                                    </div>
                                    <div>
                                        <x-input-label value="Dirigido a" />
                                        <x-text-input name="dirigido_a" type="text" class="mt-1 block w-full" required maxlength="255" value="{{ $oficio->dirigido_a }}" />
                                    </div>
                                    <div class="sm:col-span-2">
                                        <button type="submit" class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                                            Guardar cambios
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>

                        <x-modal name="cancelar-oficio-{{ $oficio->id }}" focusable>
                            <form method="POST" action="{{ route('minutarios.oficios.cancelar', $oficio) }}" class="p-6 space-y-4">
                                @csrf
                                @method('PATCH')
                                <h2 class="text-lg font-medium text-gray-900">Cancelar oficio {{ $oficio->nomenclatura }}</h2>
                                <p class="text-sm text-gray-600">Esta acción marca el folio como cancelado en el minutario; no se elimina del historial.</p>
                                <div>
                                    <x-input-label value="Motivo de cancelación" />
                                    <textarea name="motivo_cancelacion" rows="3" required minlength="10"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green"></textarea>
                                </div>
                                <div class="flex justify-end gap-3">
                                    <button type="button" x-on:click="$dispatch('close-modal', 'cancelar-oficio-{{ $oficio->id }}')" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                                        Cerrar
                                    </button>
                                    <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-600">
                                        Confirmar cancelación
                                    </button>
                                </div>
                            </form>
                        </x-modal>
                    @endcan
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">Sin oficios registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @can('minutarios.gestionar')
        <x-modal name="agregar-oficio" focusable>
            <form method="POST" action="{{ route('minutarios.oficios.store') }}" class="p-6 space-y-4">
                @csrf
                <h2 class="text-lg font-medium text-gray-900">Nuevo oficio</h2>
                <p class="text-xs text-gray-500">El folio se asigna automáticamente al guardar (consecutivo por año).</p>

                <div>
                    <x-input-label for="oficio-asunto" value="Asunto" />
                    <x-text-input id="oficio-asunto" name="asunto" type="text" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('asunto')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="oficio-dirigido" value="Dirigido a" />
                    <x-text-input id="oficio-dirigido" name="dirigido_a" type="text" class="mt-1 block w-full" required maxlength="255" />
                    <x-input-error :messages="$errors->get('dirigido_a')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="oficio-solicitante" value="Nombre del solicitante (opcional)" />
                    <x-text-input id="oficio-solicitante" name="nombre_solicitante" type="text" class="mt-1 block w-full" maxlength="255" />
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close-modal', 'agregar-oficio')" class="text-sm font-medium text-gray-600 hover:text-gray-900">
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
