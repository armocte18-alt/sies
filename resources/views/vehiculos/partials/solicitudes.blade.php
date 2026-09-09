@php /** @var \Illuminate\Support\Collection $solicitudes */ @endphp

<div>
    <div class="mb-3 flex items-center justify-between">
        <h3 class="text-sm font-semibold text-gray-800">Solicitudes de uso de vehículo</h3>
        @can('vehiculos.gestionar')
            <button type="button" x-on:click="$dispatch('open-modal', 'nueva-solicitud')"
                class="text-xs font-semibold text-brand-green hover:underline">
                + Nueva solicitud
            </button>
        @endcan
    </div>

    <div class="overflow-x-auto rounded-lg bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <caption class="sr-only">Solicitudes de vehículo</caption>
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Folio</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Solicitante</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Vehículo / Conductor</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Salida</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Regreso</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Destino</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Estatus</th>
                    <th scope="col" class="px-4 py-3"><span class="sr-only">Acciones</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($solicitudes as $solicitud)
                    @php
                        $colores = ['pendiente' => 'bg-amber-100 text-amber-800', 'autorizada' => 'bg-blue-100 text-blue-800', 'rechazada' => 'bg-red-100 text-red-800', 'finalizada' => 'bg-emerald-100 text-emerald-800'];
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">#{{ str_pad($solicitud->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $solicitud->solicitante?->name }}</td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $solicitud->detalle?->vehiculo?->descripcion() }}<br>
                            <span class="text-xs text-gray-400">{{ $solicitud->detalle?->conductor?->nombre_completo }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $solicitud->fecha_salida_desde->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $solicitud->fecha_salida_hasta->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 max-w-xs text-gray-600">{{ $solicitud->destinos[0]['lugar'] ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $colores[$solicitud->estatus] }}">
                                {{ App\Models\SolicitudVehiculo::ESTATUS_LABELS[$solicitud->estatus] }}
                            </span>
                            @if ($solicitud->estatus === 'rechazada' && $solicitud->motivo_rechazo)
                                <p class="mt-1 text-xs text-gray-400">{{ $solicitud->motivo_rechazo }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-1">
                                @can('vehiculos.gestionar')
                                    @if ($solicitud->estatus === 'pendiente')
                                        <form method="POST" action="{{ route('vehiculos.solicitudes.autorizar', $solicitud) }}" onsubmit="return confirm('¿Autorizar esta solicitud? El vehículo quedará marcado como asignado.');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="rounded p-1.5 text-emerald-600 hover:bg-emerald-50">
                                                <x-action-icon icon="check-circle" label="Autorizar" />
                                            </button>
                                        </form>
                                        <button type="button" x-on:click="$dispatch('open-modal', 'rechazar-solicitud-{{ $solicitud->id }}')"
                                            class="rounded p-1.5 text-red-600 hover:bg-red-50">
                                            <x-action-icon icon="ban" label="Rechazar" />
                                        </button>
                                    @endif
                                    @if ($solicitud->estatus === 'autorizada')
                                        <button type="button" x-on:click="$dispatch('open-modal', 'devolucion-solicitud-{{ $solicitud->id }}')"
                                            class="rounded p-1.5 text-brand-green hover:bg-brand-green-50">
                                            <x-action-icon icon="arrow-uturn-left" label="Registrar devolución" />
                                        </button>
                                    @endif
                                @endcan
                                @if (in_array($solicitud->estatus, ['autorizada', 'finalizada']))
                                    <a href="{{ route('vehiculos.solicitudes.responsiva', $solicitud) }}" target="_blank"
                                        class="rounded p-1.5 text-gray-600 hover:bg-gray-100">
                                        <x-action-icon icon="document-arrow-down" label="Descargar responsiva PDF" />
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">Sin solicitudes registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @can('vehiculos.gestionar')
        @foreach ($solicitudes as $solicitud)
            @if ($solicitud->estatus === 'pendiente')
                <x-modal name="rechazar-solicitud-{{ $solicitud->id }}" focusable>
                    <form method="POST" action="{{ route('vehiculos.solicitudes.rechazar', $solicitud) }}" class="p-6 space-y-4">
                        @csrf
                        @method('PATCH')
                        <h2 class="text-lg font-medium text-gray-900">Rechazar solicitud #{{ str_pad($solicitud->id, 5, '0', STR_PAD_LEFT) }}</h2>
                        <div>
                            <x-input-label value="Motivo de rechazo" />
                            <textarea name="motivo_rechazo" rows="3" required minlength="10"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green"></textarea>
                        </div>
                        <div class="flex justify-end gap-3">
                            <button type="button" x-on:click="$dispatch('close-modal', 'rechazar-solicitud-{{ $solicitud->id }}')" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                                Cerrar
                            </button>
                            <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-600">
                                Confirmar rechazo
                            </button>
                        </div>
                    </form>
                </x-modal>
            @endif
            @if ($solicitud->estatus === 'autorizada')
                <x-modal name="devolucion-solicitud-{{ $solicitud->id }}" focusable>
                    <form method="POST" action="{{ route('vehiculos.solicitudes.devolucion', $solicitud) }}" class="p-6 space-y-4">
                        @csrf
                        @method('PATCH')
                        <h2 class="text-lg font-medium text-gray-900">Registrar devolución — solicitud #{{ str_pad($solicitud->id, 5, '0', STR_PAD_LEFT) }}</h2>
                        <p class="text-xs text-gray-500">Kilometraje inicial: {{ number_format($solicitud->detalle->km_inicial) }} km</p>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <x-input-label value="Kilometraje final" />
                                <x-text-input name="km_final" type="number" min="{{ $solicitud->detalle->km_inicial }}" class="mt-1 block w-full" required />
                            </div>
                            <div>
                                <x-input-label value="Combustible al recibir (%)" />
                                <x-text-input name="combustible_recepcion" type="number" min="0" max="100" class="mt-1 block w-full" required />
                            </div>
                            <div class="sm:col-span-2">
                                <x-input-label value="Notas de carrocería" />
                                <textarea name="carroceria_notas_nuevas" rows="2"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green"></textarea>
                            </div>
                            <div class="sm:col-span-2 grid grid-cols-2 gap-2 sm:grid-cols-4">
                                @foreach (['checklist_gato' => 'Gato', 'checklist_llave_cruz' => 'Llave de cruz', 'checklist_reflejantes' => 'Reflejantes', 'checklist_extintor' => 'Extintor'] as $campo => $etiqueta)
                                    <label class="flex items-center gap-2 text-sm text-gray-700">
                                        <input type="checkbox" name="{{ $campo }}" value="1" checked class="rounded border-gray-300 text-brand-green focus:ring-brand-green" />
                                        {{ $etiqueta }}
                                    </label>
                                @endforeach
                            </div>
                            <div class="sm:col-span-2">
                                <x-input-label value="Observaciones de fallas (opcional)" />
                                <textarea name="observaciones_fallas" rows="2"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green"></textarea>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3">
                            <button type="button" x-on:click="$dispatch('close-modal', 'devolucion-solicitud-{{ $solicitud->id }}')" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                                Cerrar
                            </button>
                            <button type="submit" class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                                Registrar devolución
                            </button>
                        </div>
                    </form>
                </x-modal>
            @endif
        @endforeach

        <x-modal name="nueva-solicitud" focusable>
            <form method="POST" action="{{ route('vehiculos.solicitudes.store') }}" class="p-6 space-y-4" x-data="kmSugerido">
                @csrf
                <h2 class="text-lg font-medium text-gray-900">Nueva solicitud de vehículo</h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="solicitud-vehiculo" value="Vehículo" />
                        <select id="solicitud-vehiculo" name="vehiculo_id" required x-on:change="cargar($event.target.value)"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                            <option value="">Selecciona un vehículo disponible</option>
                            @foreach ($vehiculosDisponibles as $vehiculo)
                                <option value="{{ $vehiculo->id }}">{{ $vehiculo->descripcion() }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500" x-show="valor !== null" x-text="'Kilometraje inicial sugerido: ' + valor + ' km'"></p>
                        <x-input-error :messages="$errors->get('vehiculo_id')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="solicitud-conductor" value="Conductor" />
                        <select id="solicitud-conductor" name="conductor_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                            <option value="">Selecciona un conductor activo</option>
                            @foreach ($conductoresActivos as $conductor)
                                <option value="{{ $conductor->id }}">{{ \Illuminate\Support\Str::title($conductor->nombre_completo) }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('conductor_id')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="solicitud-desde" value="Fecha y hora de salida" />
                        <x-text-input id="solicitud-desde" name="fecha_salida_desde" type="datetime-local" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('fecha_salida_desde')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="solicitud-hasta" value="Fecha y hora de regreso" />
                        <x-text-input id="solicitud-hasta" name="fecha_salida_hasta" type="datetime-local" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('fecha_salida_hasta')" class="mt-1" />
                    </div>
                    <div x-data="{ area: 'gerencia_estatal_cdmx' }">
                        <x-input-label for="solicitud-area" value="Área" />
                        <select id="solicitud-area" name="area" x-model="area" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                            <option value="gerencia_estatal_cdmx">Gerencia Estatal CDMX</option>
                            <option value="areas_centrales_disfo">Áreas Centrales DISFO</option>
                            <option value="otro">Otro</option>
                        </select>
                        <div x-show="area === 'otro'" x-cloak class="mt-2">
                            <x-text-input name="area_otro" type="text" class="block w-full" placeholder="Especificar área" />
                        </div>
                    </div>
                    <div>
                        <x-input-label for="solicitud-empleado" value="No. de empleado (opcional)" />
                        <x-text-input id="solicitud-empleado" name="numero_empleado" type="text" class="mt-1 block w-full" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label for="solicitud-destino" value="Destino" />
                        <x-text-input id="solicitud-destino" name="destino_lugar" type="text" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('destino_lugar')" class="mt-1" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label for="solicitud-motivo" value="Motivo" />
                        <textarea id="solicitud-motivo" name="motivo" rows="3" required maxlength="500"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green"></textarea>
                        <x-input-error :messages="$errors->get('motivo')" class="mt-1" />
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close-modal', 'nueva-solicitud')" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                        Registrar solicitud
                    </button>
                </div>
            </form>
        </x-modal>
    @endcan
</div>
