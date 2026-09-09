@php /** @var \Illuminate\Support\Collection $conductores */ @endphp

<div>
    <div class="mb-3 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-800">Catálogo de conductores</h3>
        @can('vehiculos.gestionar')
            <button type="button" x-on:click="$dispatch('open-modal', 'agregar-conductor')"
                class="text-xs font-semibold text-brand-green hover:underline">
                + Nuevo conductor
            </button>
        @endcan
    </div>

    <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <caption class="sr-only">Conductores</caption>
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Nombre</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">No. empleado</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Área de adscripción</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Licencia</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Vigencia</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Estatus</th>
                    <th scope="col" class="px-4 py-3"><span class="sr-only">Acciones</span></th>
                </tr>
            </thead>
            <tbody x-data="{ editandoId: null }" class="divide-y divide-gray-100">
                @forelse ($conductores as $conductor)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ \Illuminate\Support\Str::title($conductor->nombre_completo) }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $conductor->numero_empleado }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $conductor->area_adscripcion }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $conductor->numero_licencia }} ({{ $conductor->tipo_licencia }})</td>
                        <td class="px-4 py-3 text-gray-600">{{ $conductor->vigencia_licencia?->format('d/m/Y') ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $conductor->estatus === 'activo' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                {{ $conductor->estatus === 'activo' ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @can('vehiculos.gestionar')
                                <div class="flex justify-end gap-1">
                                    <button type="button" x-show="editandoId !== {{ $conductor->id }}"
                                        x-on:click="editandoId = {{ $conductor->id }}"
                                        class="rounded p-1.5 text-brand-green hover:bg-brand-green-50">
                                        <x-action-icon icon="pencil" label="Editar" />
                                    </button>
                                    <button type="button" x-show="editandoId === {{ $conductor->id }}" x-cloak
                                        x-on:click="editandoId = null"
                                        class="rounded p-1.5 text-gray-500 hover:bg-gray-100">
                                        <x-action-icon icon="x-mark" label="Cancelar edición" />
                                    </button>
                                </div>
                            @endcan
                        </td>
                    </tr>
                    @can('vehiculos.gestionar')
                        <tr x-show="editandoId === {{ $conductor->id }}" x-cloak>
                            <td colspan="7" class="bg-gray-50 px-4 py-4">
                                <form method="POST" action="{{ route('vehiculos.conductores.update', $conductor) }}" class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                    @csrf
                                    @method('PUT')
                                    <div>
                                        <x-input-label value="Nombre completo" />
                                        <x-text-input name="nombre_completo" type="text" class="mt-1 block w-full" required value="{{ $conductor->nombre_completo }}" />
                                    </div>
                                    <div>
                                        <x-input-label value="No. de empleado" />
                                        <x-text-input name="numero_empleado" type="text" class="mt-1 block w-full" required value="{{ $conductor->numero_empleado }}" />
                                    </div>
                                    <div>
                                        <x-input-label value="Área de adscripción" />
                                        <x-text-input name="area_adscripcion" type="text" class="mt-1 block w-full" value="{{ $conductor->area_adscripcion }}" />
                                    </div>
                                    <div>
                                        <x-input-label value="No. de licencia" />
                                        <x-text-input name="numero_licencia" type="text" class="mt-1 block w-full" required value="{{ $conductor->numero_licencia }}" />
                                    </div>
                                    <div>
                                        <x-input-label value="Tipo de licencia" />
                                        <select name="tipo_licencia" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                                            @foreach (['A1', 'A2', 'B', 'C', 'D', 'E', 'E1', 'SICT_A', 'SICT_B', 'SICT_C', 'SICT_D', 'SICT_E', 'SICT_F'] as $tipo)
                                                <option value="{{ $tipo }}" @selected($conductor->tipo_licencia === $tipo)>{{ $tipo }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <x-input-label value="Vigencia de licencia" />
                                        <x-text-input name="vigencia_licencia" type="date" class="mt-1 block w-full" value="{{ $conductor->vigencia_licencia?->format('Y-m-d') }}" />
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input type="checkbox" name="es_permanente" value="1" id="permanente-{{ $conductor->id }}" @checked($conductor->es_permanente) class="rounded border-gray-300 text-brand-green focus:ring-brand-green" />
                                        <label for="permanente-{{ $conductor->id }}" class="text-sm text-gray-700">Conductor permanente</label>
                                    </div>
                                    <div>
                                        <x-input-label value="Estatus" />
                                        <select name="estatus" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                                            <option value="activo" @selected($conductor->estatus === 'activo')>Activo</option>
                                            <option value="inactivo" @selected($conductor->estatus === 'inactivo')>Inactivo</option>
                                        </select>
                                    </div>
                                    <div class="sm:col-span-3">
                                        <button type="submit" class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                                            Guardar cambios
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @endcan
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">Sin conductores registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @can('vehiculos.gestionar')
        <x-modal name="agregar-conductor" focusable>
            <form method="POST" action="{{ route('vehiculos.conductores.store') }}" class="p-6 space-y-4">
                @csrf
                <h2 class="text-lg font-medium text-gray-900">Nuevo conductor</h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <x-input-label for="conductor-nombre" value="Nombre completo" />
                        <x-text-input id="conductor-nombre" name="nombre_completo" type="text" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('nombre_completo')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="conductor-empleado" value="No. de empleado" />
                        <x-text-input id="conductor-empleado" name="numero_empleado" type="text" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('numero_empleado')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="conductor-area" value="Área de adscripción" />
                        <x-text-input id="conductor-area" name="area_adscripcion" type="text" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label for="conductor-licencia" value="No. de licencia" />
                        <x-text-input id="conductor-licencia" name="numero_licencia" type="text" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('numero_licencia')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="conductor-tipo-licencia" value="Tipo de licencia" />
                        <select id="conductor-tipo-licencia" name="tipo_licencia" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                            @foreach (['A1', 'A2', 'B', 'C', 'D', 'E', 'E1', 'SICT_A', 'SICT_B', 'SICT_C', 'SICT_D', 'SICT_E', 'SICT_F'] as $tipo)
                                <option value="{{ $tipo }}">{{ $tipo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="conductor-vigencia" value="Vigencia de licencia" />
                        <x-text-input id="conductor-vigencia" name="vigencia_licencia" type="date" class="mt-1 block w-full" />
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="es_permanente" value="1" id="conductor-permanente" checked class="rounded border-gray-300 text-brand-green focus:ring-brand-green" />
                        <label for="conductor-permanente" class="text-sm text-gray-700">Conductor permanente</label>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close-modal', 'agregar-conductor')" class="text-sm font-medium text-gray-600 hover:text-gray-900">
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
