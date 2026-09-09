@php $operacion = $sucursal->operacion; @endphp

<section class="rounded-lg bg-white p-4 shadow-sm" aria-labelledby="horarios-heading">
    <h2 id="horarios-heading" class="mb-3 flex items-center gap-2 text-base font-semibold text-gray-800">
        <x-nav-icon name="calendar" class="h-5 w-5 text-brand-green" />
        Operación y horarios
    </h2>

    @can('updateHorarios', $sucursal)
        <form method="POST" action="{{ route('sucursales.horarios.update', $sucursal) }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <x-input-label for="dias_laborables" value="Días laborables" />
                    <x-text-input id="dias_laborables" name="dias_laborables" type="text" class="mt-1 block w-full"
                        :value="old('dias_laborables', $operacion?->dias_laborables)" placeholder="Lunes a viernes" />
                </div>
                <div>
                    <x-input-label for="hora_apertura_publico" value="Hora apertura (público)" />
                    <input type="time" id="hora_apertura_publico" name="hora_apertura_publico"
                        value="{{ old('hora_apertura_publico', $operacion?->hora_apertura_publico?->format('H:i')) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                    <x-input-error :messages="$errors->get('hora_apertura_publico')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="hora_cierre_publico" value="Hora cierre (público)" />
                    <input type="time" id="hora_cierre_publico" name="hora_cierre_publico"
                        value="{{ old('hora_cierre_publico', $operacion?->hora_cierre_publico?->format('H:i')) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                    <x-input-error :messages="$errors->get('hora_cierre_publico')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label for="hora_inicio_labores_interno" value="Hora inicio labores (interno)" />
                    <input type="time" id="hora_inicio_labores_interno" name="hora_inicio_labores_interno"
                        value="{{ old('hora_inicio_labores_interno', $operacion?->hora_inicio_labores_interno?->format('H:i')) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                </div>
                <div>
                    <x-input-label for="hora_fin_labores_interno" value="Hora fin labores (interno)" />
                    <input type="time" id="hora_fin_labores_interno" name="hora_fin_labores_interno"
                        value="{{ old('hora_fin_labores_interno', $operacion?->hora_fin_labores_interno?->format('H:i')) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                    <x-input-error :messages="$errors->get('hora_fin_labores_interno')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <x-input-label for="tipo_poblacion" value="Tipo de población" />
                    <select id="tipo_poblacion" name="tipo_poblacion"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                        <option value="">Selecciona una opción</option>
                        @foreach ([
                            'urbana_alta_densidad' => 'Urbana de alta densidad',
                            'urbana_media_densidad' => 'Urbana de media densidad',
                            'urbana_baja_densidad' => 'Urbana de baja densidad',
                            'rural' => 'Rural',
                        ] as $value => $label)
                            <option value="{{ $value }}" @selected(old('tipo_poblacion', $operacion?->tipo_poblacion) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="tipo_inmueble" value="Tipo de inmueble" />
                    <select id="tipo_inmueble" name="tipo_inmueble"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                        <option value="">Selecciona una opción</option>
                        @foreach (['propio' => 'Inmueble propio', 'arrendado' => 'Arrendado', 'comodato' => 'Comodato', 'otro' => 'Otro'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('tipo_inmueble', $operacion?->tipo_inmueble) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="comunicacion" value="Comunicación" />
                    <x-text-input id="comunicacion" name="comunicacion" type="text" class="mt-1 block w-full"
                        :value="old('comunicacion', $operacion?->comunicacion)" placeholder="Red local / enlace dedicado" />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <x-input-label for="telefono" value="Teléfono" />
                    <x-text-input id="telefono" name="telefono" type="text" class="mt-1 block w-full"
                        :value="old('telefono', $operacion?->telefono)" />
                </div>
                <div>
                    <x-input-label for="reparto_activo" value="¿Cuenta con reparto activo?" />
                    <select id="reparto_activo" name="reparto_activo"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                        <option value="0" @selected(!old('reparto_activo', $operacion?->reparto_activo))>No</option>
                        <option value="1" @selected(old('reparto_activo', $operacion?->reparto_activo))>Sí</option>
                    </select>
                </div>
                <div>
                    <x-input-label for="enrutamiento" value="Enrutamiento" />
                    <x-text-input id="enrutamiento" name="enrutamiento" type="text" class="mt-1 block w-full"
                        :value="old('enrutamiento', $operacion?->enrutamiento)" />
                </div>
            </div>

            <div class="rounded-md border border-amber-200 bg-amber-50 p-4">
                <p class="mb-3 flex items-center gap-2 text-sm font-semibold text-amber-800">
                    <x-nav-icon name="shield" class="h-4 w-4" /> Días de guardia
                </p>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <x-input-label for="dias_guardia" value="Días de guardia" />
                        <x-text-input id="dias_guardia" name="dias_guardia" type="text" class="mt-1 block w-full"
                            :value="old('dias_guardia', $operacion?->dias_guardia)" placeholder="Sábado" />
                    </div>
                    <div>
                        <x-input-label for="apertura_guardia" value="Apertura guardia" />
                        <input type="time" id="apertura_guardia" name="apertura_guardia"
                            value="{{ old('apertura_guardia', $operacion?->apertura_guardia?->format('H:i')) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                    </div>
                    <div>
                        <x-input-label for="cierre_guardia" value="Cierre guardia" />
                        <input type="time" id="cierre_guardia" name="cierre_guardia"
                            value="{{ old('cierre_guardia', $operacion?->cierre_guardia?->format('H:i')) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                        <x-input-error :messages="$errors->get('cierre_guardia')" class="mt-2" />
                    </div>
                </div>
            </div>

            <button type="submit"
                class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                Guardar operación
            </button>
        </form>
    @else
        @if ($operacion)
            <dl class="grid grid-cols-1 gap-x-4 gap-y-3 text-sm sm:grid-cols-3">
                <div><dt class="text-gray-500">Días laborables</dt><dd class="font-medium text-gray-900">{{ $operacion->dias_laborables ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">Horario público</dt><dd class="font-medium text-gray-900">{{ $operacion->hora_apertura_publico?->format('H:i') }} - {{ $operacion->hora_cierre_publico?->format('H:i') }}</dd></div>
                <div><dt class="text-gray-500">Horario interno</dt><dd class="font-medium text-gray-900">{{ $operacion->hora_inicio_labores_interno?->format('H:i') }} - {{ $operacion->hora_fin_labores_interno?->format('H:i') }}</dd></div>
                <div><dt class="text-gray-500">Teléfono</dt><dd class="font-medium text-gray-900">{{ $operacion->telefono ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">Reparto activo</dt><dd class="font-medium text-gray-900">{{ $operacion->reparto_activo ? 'Sí' : 'No' }}</dd></div>
                <div><dt class="text-gray-500">Días de guardia</dt><dd class="font-medium text-gray-900">{{ $operacion->dias_guardia ?? '—' }}</dd></div>
            </dl>
        @else
            <p class="text-sm text-gray-500">Aún no se ha capturado la operación de esta sucursal.</p>
        @endif
    @endcan
</section>
