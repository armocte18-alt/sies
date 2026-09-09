@php /** @var \Illuminate\Support\Collection $vehiculos */ @endphp

<div>
    <div class="mb-3 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-800">Catálogo de vehículos</h3>
        @can('vehiculos.gestionar')
            <button type="button" x-on:click="$dispatch('open-modal', 'agregar-vehiculo')"
                class="text-xs font-semibold text-brand-green hover:underline">
                + Nuevo vehículo
            </button>
        @endcan
    </div>

    <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <caption class="sr-only">Vehículos</caption>
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Vehículo</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Placa</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Tipo</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Kilometraje</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Estatus</th>
                    <th scope="col" class="px-4 py-3"><span class="sr-only">Acciones</span></th>
                </tr>
            </thead>
            <tbody x-data="{ editandoId: null }" class="divide-y divide-gray-100">
                @forelse ($vehiculos as $vehiculo)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->anio }})</td>
                        <td class="px-4 py-3 text-gray-600 uppercase">{{ $vehiculo->placa }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ ucfirst($vehiculo->tipo) }}{{ $vehiculo->tipo === 'otro' && $vehiculo->tipo_otro ? " ({$vehiculo->tipo_otro})" : '' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ number_format($vehiculo->kilometraje_actual) }} km</td>
                        <td class="px-4 py-3">
                            @php
                                $colores = ['disponible' => 'bg-emerald-100 text-emerald-800', 'asignado' => 'bg-amber-100 text-amber-800', 'mantenimiento' => 'bg-blue-100 text-blue-800', 'fuera_de_servicio' => 'bg-red-100 text-red-800'];
                            @endphp
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $colores[$vehiculo->estatus] }}">
                                {{ App\Models\Vehiculo::ESTATUS_LABELS[$vehiculo->estatus] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @can('vehiculos.gestionar')
                                <div class="flex justify-end gap-1">
                                    <button type="button" x-show="editandoId !== {{ $vehiculo->id }}"
                                        x-on:click="editandoId = {{ $vehiculo->id }}"
                                        class="rounded p-1.5 text-brand-green hover:bg-brand-green-50">
                                        <x-action-icon icon="pencil" label="Editar" />
                                    </button>
                                    <button type="button" x-show="editandoId === {{ $vehiculo->id }}" x-cloak
                                        x-on:click="editandoId = null"
                                        class="rounded p-1.5 text-gray-500 hover:bg-gray-100">
                                        <x-action-icon icon="x-mark" label="Cancelar edición" />
                                    </button>
                                </div>
                            @endcan
                        </td>
                    </tr>
                    @can('vehiculos.gestionar')
                        <tr x-show="editandoId === {{ $vehiculo->id }}" x-cloak>
                            <td colspan="6" class="bg-gray-50 px-4 py-4">
                                <form method="POST" action="{{ route('vehiculos.update', $vehiculo) }}" class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                    @csrf
                                    @method('PUT')
                                    <div>
                                        <x-input-label value="Marca" />
                                        <x-text-input name="marca" type="text" class="mt-1 block w-full" required value="{{ $vehiculo->marca }}" />
                                    </div>
                                    <div>
                                        <x-input-label value="Modelo" />
                                        <x-text-input name="modelo" type="text" class="mt-1 block w-full" required value="{{ $vehiculo->modelo }}" />
                                    </div>
                                    <div>
                                        <x-input-label value="Año" />
                                        <x-text-input name="anio" type="number" class="mt-1 block w-full" required value="{{ $vehiculo->anio }}" />
                                    </div>
                                    <div>
                                        <x-input-label value="Tipo" />
                                        <select name="tipo" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                                            @foreach (['propio' => 'Propio', 'arrendado' => 'Arrendado', 'otro' => 'Otro'] as $valor => $etiqueta)
                                                <option value="{{ $valor }}" @selected($vehiculo->tipo === $valor)>{{ $etiqueta }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <x-input-label value="Placa" />
                                        <x-text-input name="placa" type="text" class="mt-1 block w-full uppercase" required value="{{ $vehiculo->placa }}" />
                                    </div>
                                    <div>
                                        <x-input-label value="Kilometraje actual" />
                                        <x-text-input name="kilometraje_actual" type="number" class="mt-1 block w-full" required value="{{ $vehiculo->kilometraje_actual }}" />
                                    </div>
                                    <div>
                                        <x-input-label value="Folio tarjeta de circulación" />
                                        <x-text-input name="folio_tarjeta_circulacion" type="text" class="mt-1 block w-full" value="{{ $vehiculo->folio_tarjeta_circulacion }}" />
                                    </div>
                                    <div>
                                        <x-input-label value="Vigencia tarjeta de circulación" />
                                        <x-text-input name="vigencia_tarjeta_circulacion" type="date" class="mt-1 block w-full" value="{{ $vehiculo->vigencia_tarjeta_circulacion?->format('Y-m-d') }}" />
                                    </div>
                                    <div>
                                        <x-input-label value="Estatus" />
                                        <select name="estatus" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green" @disabled($vehiculo->estatus === 'asignado')>
                                            @foreach (['disponible' => 'Disponible', 'mantenimiento' => 'En mantenimiento', 'fuera_de_servicio' => 'Fuera de servicio'] as $valor => $etiqueta)
                                                <option value="{{ $valor }}" @selected($vehiculo->estatus === $valor)>{{ $etiqueta }}</option>
                                            @endforeach
                                            @if ($vehiculo->estatus === 'asignado')
                                                <option value="asignado" selected>Asignado (automático)</option>
                                            @endif
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
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">Sin vehículos registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @can('vehiculos.gestionar')
        <x-modal name="agregar-vehiculo" focusable>
            <form method="POST" action="{{ route('vehiculos.store') }}" class="p-6 space-y-4">
                @csrf
                <h2 class="text-lg font-medium text-gray-900">Nuevo vehículo</h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2" x-data="{ tipo: 'propio' }">
                    <div>
                        <x-input-label for="vehiculo-marca" value="Marca" />
                        <x-text-input id="vehiculo-marca" name="marca" type="text" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('marca')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="vehiculo-modelo" value="Modelo" />
                        <x-text-input id="vehiculo-modelo" name="modelo" type="text" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('modelo')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="vehiculo-anio" value="Año" />
                        <x-text-input id="vehiculo-anio" name="anio" type="number" class="mt-1 block w-full" required value="{{ now()->year }}" />
                        <x-input-error :messages="$errors->get('anio')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="vehiculo-placa" value="Placa" />
                        <x-text-input id="vehiculo-placa" name="placa" type="text" class="mt-1 block w-full uppercase" required />
                        <x-input-error :messages="$errors->get('placa')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="vehiculo-tipo" value="Tipo" />
                        <select id="vehiculo-tipo" name="tipo" x-model="tipo" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                            <option value="propio">Propio</option>
                            <option value="arrendado">Arrendado</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    <div x-show="tipo === 'otro'" x-cloak>
                        <x-input-label for="vehiculo-tipo-otro" value="Especificar tipo" />
                        <x-text-input id="vehiculo-tipo-otro" name="tipo_otro" type="text" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label for="vehiculo-km" value="Kilometraje actual" />
                        <x-text-input id="vehiculo-km" name="kilometraje_actual" type="number" class="mt-1 block w-full" value="0" />
                    </div>
                    <div>
                        <x-input-label for="vehiculo-combustible" value="Combustible inicial (%)" />
                        <x-text-input id="vehiculo-combustible" name="combustible_inicial" type="number" min="0" max="100" class="mt-1 block w-full" value="100" />
                    </div>
                    <div>
                        <x-input-label for="vehiculo-folio-tc" value="Folio tarjeta de circulación" />
                        <x-text-input id="vehiculo-folio-tc" name="folio_tarjeta_circulacion" type="text" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label for="vehiculo-vig-tc" value="Vigencia tarjeta de circulación" />
                        <x-text-input id="vehiculo-vig-tc" name="vigencia_tarjeta_circulacion" type="date" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label for="vehiculo-folio-seguro" value="Folio póliza de seguro" />
                        <x-text-input id="vehiculo-folio-seguro" name="folio_poliza_seguro" type="text" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label for="vehiculo-vig-seguro" value="Vigencia póliza de seguro" />
                        <x-text-input id="vehiculo-vig-seguro" name="vigencia_poliza_seguro" type="date" class="mt-1 block w-full" />
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close-modal', 'agregar-vehiculo')" class="text-sm font-medium text-gray-600 hover:text-gray-900">
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
