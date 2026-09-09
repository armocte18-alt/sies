@php $equipamiento = $sucursal->equipamiento; @endphp

<section class="rounded-lg bg-white p-4 shadow-sm" aria-labelledby="equipamiento-heading">
    <h2 id="equipamiento-heading" class="mb-3 flex items-center gap-2 text-base font-semibold text-gray-800">
        <x-nav-icon name="wrench" class="h-5 w-5 text-brand-green" />
        Equipamiento técnico (Técnica)
    </h2>

    @can('updateEquipamiento', $sucursal)
        <form method="POST" action="{{ route('sucursales.equipamiento.update', $sucursal) }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3">
                <div>
                    <x-input-label for="num_computadoras" value="Computadoras" />
                    <x-text-input id="num_computadoras" name="num_computadoras" type="number" min="0" class="mt-1 block w-full"
                        :value="old('num_computadoras', $equipamiento?->num_computadoras ?? 0)" required />
                </div>
                <div>
                    <x-input-label for="num_impresoras" value="Impresoras" />
                    <x-text-input id="num_impresoras" name="num_impresoras" type="number" min="0" class="mt-1 block w-full"
                        :value="old('num_impresoras', $equipamiento?->num_impresoras ?? 0)" required />
                </div>
                <div>
                    <x-input-label for="num_servidores" value="Servidores" />
                    <x-text-input id="num_servidores" name="num_servidores" type="number" min="0" class="mt-1 block w-full"
                        :value="old('num_servidores', $equipamiento?->num_servidores ?? 0)" required />
                </div>
            </div>

            <fieldset class="rounded-md border border-gray-200 p-4">
                <legend class="px-1 text-sm font-semibold text-gray-700">Seguridad física</legend>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="flex items-center gap-2">
                        <input type="hidden" name="tiene_camaras" value="0">
                        <input type="checkbox" id="tiene_camaras" name="tiene_camaras" value="1"
                            @checked(old('tiene_camaras', $equipamiento?->tiene_camaras))
                            class="rounded border-gray-300 text-brand-green focus:ring-brand-green">
                        <label for="tiene_camaras" class="text-sm text-gray-700">Cuenta con cámaras</label>
                    </div>
                    <div>
                        <x-input-label for="num_camaras" value="Número de cámaras" />
                        <x-text-input id="num_camaras" name="num_camaras" type="number" min="0" class="mt-1 block w-full"
                            :value="old('num_camaras', $equipamiento?->num_camaras ?? 0)" required />
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="hidden" name="tiene_alarma" value="0">
                        <input type="checkbox" id="tiene_alarma" name="tiene_alarma" value="1"
                            @checked(old('tiene_alarma', $equipamiento?->tiene_alarma))
                            class="rounded border-gray-300 text-brand-green focus:ring-brand-green">
                        <label for="tiene_alarma" class="text-sm text-gray-700">Cuenta con alarma</label>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="hidden" name="tiene_extintores" value="0">
                        <input type="checkbox" id="tiene_extintores" name="tiene_extintores" value="1"
                            @checked(old('tiene_extintores', $equipamiento?->tiene_extintores))
                            class="rounded border-gray-300 text-brand-green focus:ring-brand-green">
                        <label for="tiene_extintores" class="text-sm text-gray-700">Cuenta con extintores</label>
                    </div>
                    <div>
                        <x-input-label for="num_extintores" value="Número de extintores" />
                        <x-text-input id="num_extintores" name="num_extintores" type="number" min="0" class="mt-1 block w-full"
                            :value="old('num_extintores', $equipamiento?->num_extintores ?? 0)" required />
                    </div>
                </div>
            </fieldset>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <x-input-label for="proveedor_internet" value="Proveedor de internet" />
                    <x-text-input id="proveedor_internet" name="proveedor_internet" type="text" class="mt-1 block w-full"
                        :value="old('proveedor_internet', $equipamiento?->proveedor_internet)" />
                </div>
                <div>
                    <x-input-label for="tipo_enlace" value="Tipo de enlace" />
                    <x-text-input id="tipo_enlace" name="tipo_enlace" type="text" class="mt-1 block w-full"
                        :value="old('tipo_enlace', $equipamiento?->tipo_enlace)" />
                </div>
                <div>
                    <x-input-label for="velocidad_contratada" value="Velocidad contratada" />
                    <x-text-input id="velocidad_contratada" name="velocidad_contratada" type="text" class="mt-1 block w-full"
                        :value="old('velocidad_contratada', $equipamiento?->velocidad_contratada)" placeholder="20 Mbps" />
                </div>
            </div>

            <div>
                <x-input-label for="observaciones" value="Observaciones" />
                <textarea id="observaciones" name="observaciones" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">{{ old('observaciones', $equipamiento?->observaciones) }}</textarea>
            </div>

            <button type="submit"
                class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                Guardar equipamiento
            </button>
        </form>
    @else
        @if ($equipamiento)
            <dl class="grid grid-cols-1 gap-x-4 gap-y-3 text-sm sm:grid-cols-3">
                <div><dt class="text-gray-500">Computadoras</dt><dd class="font-medium text-gray-900">{{ $equipamiento->num_computadoras }}</dd></div>
                <div><dt class="text-gray-500">Cámaras</dt><dd class="font-medium text-gray-900">{{ $equipamiento->tiene_camaras ? $equipamiento->num_camaras : 'No' }}</dd></div>
                <div><dt class="text-gray-500">Proveedor de internet</dt><dd class="font-medium text-gray-900">{{ $equipamiento->proveedor_internet ?? '—' }}</dd></div>
            </dl>
        @else
            <p class="text-sm text-gray-500">Aún no se ha capturado el equipamiento técnico.</p>
        @endif
    @endcan

    <div class="mt-6 border-t border-gray-100 pt-4">
        <div class="mb-3 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-800">Inventario detallado de activos</h3>
            @can('updateEquipamiento', $sucursal)
                <button type="button" x-on:click="$dispatch('open-modal', 'agregar-activo-ti')"
                    class="text-xs font-semibold text-brand-green hover:underline">
                    + Agregar activo
                </button>
            @endcan
        </div>

        @if ($sucursal->activosTi->isEmpty())
            <p class="text-sm text-gray-500">Sin activos registrados en el inventario detallado.</p>
        @else
            <div class="overflow-x-auto rounded-lg border border-gray-100">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <caption class="sr-only">Inventario detallado de activos de TI</caption>
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-3 py-2 text-left font-semibold text-gray-600">Tipo</th>
                            <th scope="col" class="px-3 py-2 text-left font-semibold text-gray-600">Marca / modelo</th>
                            <th scope="col" class="px-3 py-2 text-left font-semibold text-gray-600">No. serie</th>
                            <th scope="col" class="px-3 py-2 text-left font-semibold text-gray-600">No. inventario</th>
                            <th scope="col" class="px-3 py-2 text-left font-semibold text-gray-600">Estado</th>
                            <th scope="col" class="px-3 py-2"><span class="sr-only">Acciones</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($sucursal->activosTi as $activo)
                            <tr>
                                <td class="px-3 py-2 capitalize text-gray-800">{{ $activo->tipo }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ trim(($activo->marca ?? '').' '.($activo->modelo ?? '')) ?: '—' }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ $activo->numero_serie ?? '—' }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ $activo->etiqueta_inventario ?? '—' }}</td>
                                <td class="px-3 py-2">
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium
                                        {{ match($activo->estado) {
                                            'operativo' => 'bg-emerald-100 text-emerald-800',
                                            'mantenimiento' => 'bg-amber-100 text-amber-800',
                                            default => 'bg-gray-100 text-gray-600',
                                        } }}">
                                        {{ ucfirst($activo->estado) }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-right">
                                    @can('updateEquipamiento', $sucursal)
                                        <form method="POST" action="{{ route('sucursales.activos-ti.destroy', [$sucursal, $activo]) }}"
                                            onsubmit="return confirm('¿Eliminar este activo del inventario?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded p-1.5 text-red-600 hover:bg-red-50">
                                                <x-action-icon icon="trash" label="Eliminar" />
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @can('updateEquipamiento', $sucursal)
        <x-modal name="agregar-activo-ti" focusable>
            <form method="POST" action="{{ route('sucursales.activos-ti.store', $sucursal) }}" class="p-6 space-y-4">
                @csrf
                <h2 class="text-lg font-medium text-gray-900">Agregar activo al inventario</h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="activo-tipo" value="Tipo" />
                        <select id="activo-tipo" name="tipo" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                            @foreach (['computadora', 'impresora', 'servidor', 'camara', 'switch', 'router', 'otro'] as $tipoOpcion)
                                <option value="{{ $tipoOpcion }}">{{ ucfirst($tipoOpcion) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="activo-estado" value="Estado" />
                        <select id="activo-estado" name="estado" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                            <option value="operativo">Operativo</option>
                            <option value="mantenimiento">En mantenimiento</option>
                            <option value="baja">Baja</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="activo-marca" value="Marca (opcional)" />
                        <x-text-input id="activo-marca" name="marca" type="text" class="mt-1 block w-full" maxlength="60" />
                    </div>
                    <div>
                        <x-input-label for="activo-modelo" value="Modelo (opcional)" />
                        <x-text-input id="activo-modelo" name="modelo" type="text" class="mt-1 block w-full" maxlength="60" />
                    </div>
                    <div>
                        <x-input-label for="activo-serie" value="Número de serie (opcional)" />
                        <x-text-input id="activo-serie" name="numero_serie" type="text" class="mt-1 block w-full" maxlength="60" />
                    </div>
                    <div>
                        <x-input-label for="activo-inventario" value="Número de inventario (opcional)" />
                        <x-text-input id="activo-inventario" name="etiqueta_inventario" type="text" class="mt-1 block w-full" maxlength="60" />
                    </div>
                    <div>
                        <x-input-label for="activo-fecha" value="Fecha de asignación (opcional)" />
                        <x-text-input id="activo-fecha" name="fecha_asignacion" type="date" class="mt-1 block w-full" />
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close-modal', 'agregar-activo-ti')" class="text-sm font-medium text-gray-600 hover:text-gray-900">
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
</section>
