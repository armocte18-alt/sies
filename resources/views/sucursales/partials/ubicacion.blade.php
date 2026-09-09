@php $ubicacion = $sucursal->ubicacion; @endphp

<section class="rounded-lg bg-white p-4 shadow-sm" aria-labelledby="ubicacion-heading">
    <h2 id="ubicacion-heading" class="mb-3 flex items-center gap-2 text-base font-semibold text-gray-800">
        <x-nav-icon name="building" class="h-5 w-5 text-brand-green" />
        Ubicación geográfica
    </h2>

    @can('updateUbicacion', $sucursal)
        <form method="POST" action="{{ route('sucursales.ubicacion.update', $sucursal) }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="sm:col-span-2">
                    <x-input-label for="calle" value="Calle" />
                    <x-text-input id="calle" name="calle" type="text" class="mt-1 block w-full"
                        :value="old('calle', $ubicacion?->calle)" required />
                    <x-input-error :messages="$errors->get('calle')" class="mt-2" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="num_ext" value="Núm. Ext" />
                        <x-text-input id="num_ext" name="num_ext" type="text" class="mt-1 block w-full"
                            :value="old('num_ext', $ubicacion?->num_ext)" />
                    </div>
                    <div>
                        <x-input-label for="num_int" value="Núm. Int" />
                        <x-text-input id="num_int" name="num_int" type="text" class="mt-1 block w-full"
                            :value="old('num_int', $ubicacion?->num_int)" />
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <x-input-label for="colonia" value="Colonia" />
                    <x-text-input id="colonia" name="colonia" type="text" class="mt-1 block w-full"
                        :value="old('colonia', $ubicacion?->colonia)" required />
                    <x-input-error :messages="$errors->get('colonia')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="alcaldia_id" value="Alcaldía / Municipio" />
                    <select id="alcaldia_id" name="alcaldia_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                        <option value="">Selecciona una alcaldía</option>
                        @foreach (\App\Models\Alcaldia::orderBy('nombre')->get() as $alcaldia)
                            <option value="{{ $alcaldia->id }}" @selected((int) old('alcaldia_id', $ubicacion?->alcaldia_id) === $alcaldia->id)>
                                {{ $alcaldia->nombre }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('alcaldia_id')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="codigo_postal" value="Código postal" />
                    <x-text-input id="codigo_postal" name="codigo_postal" type="text" inputmode="numeric" maxlength="5" class="mt-1 block w-full"
                        :value="old('codigo_postal', $ubicacion?->codigo_postal)" required />
                    <x-input-error :messages="$errors->get('codigo_postal')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <x-input-label for="entre_calle_1" value="Entre calle 1" />
                    <x-text-input id="entre_calle_1" name="entre_calle_1" type="text" class="mt-1 block w-full"
                        :value="old('entre_calle_1', $ubicacion?->entre_calle_1)" />
                </div>
                <div>
                    <x-input-label for="entre_calle_2" value="Entre calle 2" />
                    <x-text-input id="entre_calle_2" name="entre_calle_2" type="text" class="mt-1 block w-full"
                        :value="old('entre_calle_2', $ubicacion?->entre_calle_2)" />
                </div>
                <div>
                    <x-input-label for="referencia_visual" value="Referencia visual" />
                    <x-text-input id="referencia_visual" name="referencia_visual" type="text" class="mt-1 block w-full"
                        :value="old('referencia_visual', $ubicacion?->referencia_visual)" />
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <x-input-label for="latitud" value="Latitud (Google Maps)" />
                    <x-text-input id="latitud" name="latitud" type="text" inputmode="decimal" class="mt-1 block w-full"
                        :value="old('latitud', $ubicacion?->latitud)" />
                    <x-input-error :messages="$errors->get('latitud')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="longitud" value="Longitud (Google Maps)" />
                    <x-text-input id="longitud" name="longitud" type="text" inputmode="decimal" class="mt-1 block w-full"
                        :value="old('longitud', $ubicacion?->longitud)" />
                    <x-input-error :messages="$errors->get('longitud')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="clave_geografica_inegi" value="Clave geográfica INEGI" />
                    <x-text-input id="clave_geografica_inegi" name="clave_geografica_inegi" type="text" class="mt-1 block w-full"
                        :value="old('clave_geografica_inegi', $ubicacion?->clave_geografica_inegi)" />
                </div>
            </div>

            <button type="submit"
                class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                Guardar ubicación
            </button>
        </form>
    @else
        @if ($ubicacion)
            <dl class="grid grid-cols-1 gap-x-4 gap-y-3 text-sm sm:grid-cols-3">
                <div><dt class="text-gray-500">Calle</dt><dd class="font-medium text-gray-900">{{ $ubicacion->calle }} {{ $ubicacion->num_ext }}</dd></div>
                <div><dt class="text-gray-500">Colonia</dt><dd class="font-medium text-gray-900">{{ $ubicacion->colonia }}</dd></div>
                <div><dt class="text-gray-500">Alcaldía</dt><dd class="font-medium text-gray-900">{{ $ubicacion->alcaldia?->nombre }}</dd></div>
                <div><dt class="text-gray-500">Código postal</dt><dd class="font-medium text-gray-900">{{ $ubicacion->codigo_postal }}</dd></div>
                <div><dt class="text-gray-500">Referencia visual</dt><dd class="font-medium text-gray-900">{{ $ubicacion->referencia_visual ?? '—' }}</dd></div>
                <div><dt class="text-gray-500">Clave INEGI</dt><dd class="font-medium text-gray-900">{{ $ubicacion->clave_geografica_inegi ?? '—' }}</dd></div>
            </dl>
        @else
            <p class="text-sm text-gray-500">Aún no se ha capturado la ubicación de esta sucursal.</p>
        @endif
    @endcan
</section>
