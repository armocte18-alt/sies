@php $inmueble = $sucursal->inmueble; @endphp

<section class="rounded-lg bg-white p-4 shadow-sm" aria-labelledby="inmueble-heading">
    <h2 id="inmueble-heading" class="mb-3 flex items-center gap-2 text-base font-semibold text-gray-800">
        <x-nav-icon name="building" class="h-5 w-5 text-brand-green" />
        Inmueble (Administración)
    </h2>

    @can('updateInmueble', $sucursal)
        <form method="POST" action="{{ route('sucursales.inmueble.update', $sucursal) }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <x-input-label for="tipo_contrato_posesion" value="Tipo de contrato de posesión" />
                    <select id="tipo_contrato_posesion" name="tipo_contrato_posesion"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                        <option value="">Selecciona una opción</option>
                        @foreach (['propio' => 'Propio', 'arrendado' => 'Arrendado', 'comodato' => 'Comodato', 'otro' => 'Otro'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('tipo_contrato_posesion', $inmueble?->tipo_contrato_posesion) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="superficie_m2" value="Superficie (m²)" />
                    <x-text-input id="superficie_m2" name="superficie_m2" type="number" step="0.01" min="0" class="mt-1 block w-full"
                        :value="old('superficie_m2', $inmueble?->superficie_m2)" />
                    <x-input-error :messages="$errors->get('superficie_m2')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="medidas" value="Medidas" />
                    <x-text-input id="medidas" name="medidas" type="text" class="mt-1 block w-full"
                        :value="old('medidas', $inmueble?->medidas)" placeholder="10m x 8m" />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label for="fecha_inicio_contrato" value="Fecha de inicio de contrato" />
                    <input type="date" id="fecha_inicio_contrato" name="fecha_inicio_contrato"
                        value="{{ old('fecha_inicio_contrato', $inmueble?->fecha_inicio_contrato?->format('Y-m-d')) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                </div>
                <div>
                    <x-input-label for="fecha_fin_contrato" value="Fecha de fin de contrato" />
                    <input type="date" id="fecha_fin_contrato" name="fecha_fin_contrato"
                        value="{{ old('fecha_fin_contrato', $inmueble?->fecha_fin_contrato?->format('Y-m-d')) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                    <x-input-error :messages="$errors->get('fecha_fin_contrato')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <x-input-label for="monto_renta_mensual" value="Monto de renta mensual" />
                    <x-text-input id="monto_renta_mensual" name="monto_renta_mensual" type="number" step="0.01" min="0" class="mt-1 block w-full"
                        :value="old('monto_renta_mensual', $inmueble?->monto_renta_mensual)" />
                </div>
                <div>
                    <x-input-label for="propietario_arrendador" value="Propietario / arrendador" />
                    <x-text-input id="propietario_arrendador" name="propietario_arrendador" type="text" class="mt-1 block w-full"
                        :value="old('propietario_arrendador', $inmueble?->propietario_arrendador)" />
                </div>
                <div>
                    <x-input-label for="numero_escritura_contrato" value="Número de escritura o contrato" />
                    <x-text-input id="numero_escritura_contrato" name="numero_escritura_contrato" type="text" class="mt-1 block w-full"
                        :value="old('numero_escritura_contrato', $inmueble?->numero_escritura_contrato)" />
                </div>
            </div>

            <div>
                <x-input-label for="observaciones" value="Observaciones" />
                <textarea id="observaciones" name="observaciones" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">{{ old('observaciones', $inmueble?->observaciones) }}</textarea>
            </div>

            <button type="submit"
                class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                Guardar inmueble
            </button>
        </form>
    @else
        @if ($inmueble)
            <dl class="grid grid-cols-1 gap-x-4 gap-y-3 text-sm sm:grid-cols-3">
                <div><dt class="text-gray-500">Tipo de contrato</dt><dd class="font-medium text-gray-900">{{ $inmueble->tipo_contrato_posesion ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">Superficie</dt><dd class="font-medium text-gray-900">{{ $inmueble->superficie_m2 ?? '—' }} m²</dd></div>
                <div><dt class="text-gray-500">Renta mensual</dt><dd class="font-medium text-gray-900">${{ number_format($inmueble->monto_renta_mensual ?? 0, 2) }}</dd></div>
            </dl>
        @else
            <p class="text-sm text-gray-500">Aún no se ha capturado la información del inmueble.</p>
        @endif
    @endcan
</section>
