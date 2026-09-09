@php
    $idPrefix = $externo?->id ?? 'nuevo';
    $modalName = $modalName ?? null;
@endphp

<form method="POST" action="{{ $accion }}" @class(['space-y-4', 'p-6' => $modalName])>
    @csrf
    @if ($metodo)
        @method($metodo)
    @endif

    @if ($modalName)
        <h2 class="text-lg font-medium text-gray-900">{{ $boton === 'Agregar' ? 'Agregar contacto externo' : $boton }}</h2>
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <x-input-label for="externo-nombre-{{ $idPrefix }}" value="Nombre" />
            <x-text-input id="externo-nombre-{{ $idPrefix }}" name="nombre" type="text" class="mt-1 block w-full" required maxlength="150" value="{{ $externo?->nombre }}" />
            @if (!$externo) <x-input-error :messages="$errors->get('nombre')" class="mt-1" /> @endif
        </div>
        <div>
            <x-input-label for="externo-puesto-{{ $idPrefix }}" value="Puesto (opcional)" />
            <x-text-input id="externo-puesto-{{ $idPrefix }}" name="puesto" type="text" class="mt-1 block w-full" maxlength="150" value="{{ $externo?->puesto }}" />
        </div>
        <div>
            <x-input-label for="externo-dependencia-{{ $idPrefix }}" value="Dependencia" />
            <x-text-input id="externo-dependencia-{{ $idPrefix }}" name="dependencia" type="text" class="mt-1 block w-full" required maxlength="150" value="{{ $externo?->dependencia }}" />
        </div>
        <div>
            <x-input-label for="externo-adscripcion-{{ $idPrefix }}" value="Adscripción (opcional)" />
            <x-text-input id="externo-adscripcion-{{ $idPrefix }}" name="adscripcion" type="text" class="mt-1 block w-full" maxlength="150" value="{{ $externo?->adscripcion }}" />
        </div>
        <div class="sm:col-span-2">
            <x-input-label for="externo-domicilio-{{ $idPrefix }}" value="Domicilio (opcional)" />
            <x-text-input id="externo-domicilio-{{ $idPrefix }}" name="domicilio" type="text" class="mt-1 block w-full" maxlength="255" value="{{ $externo?->domicilio }}" />
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div x-data="{ telefonos: {{ json_encode(($externo?->telefonos) ?: ['']) }} }">
            <x-input-label value="Teléfonos" />
            <template x-for="(telefono, index) in telefonos" :key="index">
                <div class="mt-1 flex gap-2">
                    <input type="text" name="telefonos[]" x-model="telefonos[index]" maxlength="20"
                        class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-green focus:ring-brand-green">
                    <button type="button" x-on:click="telefonos.splice(index, 1)" x-show="telefonos.length > 1"
                        class="rounded-md border border-gray-300 px-2 text-xs text-gray-500 hover:bg-gray-50">&times;</button>
                </div>
            </template>
            <button type="button" x-on:click="telefonos.push('')" class="mt-1 text-xs font-medium text-brand-green hover:underline">
                + Agregar teléfono
            </button>
        </div>

        <div x-data="{ correos: {{ json_encode(($externo?->correos) ?: ['']) }} }">
            <x-input-label value="Correos" />
            <template x-for="(correo, index) in correos" :key="index">
                <div class="mt-1 flex gap-2">
                    <input type="email" name="correos[]" x-model="correos[index]" maxlength="255"
                        class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-green focus:ring-brand-green">
                    <button type="button" x-on:click="correos.splice(index, 1)" x-show="correos.length > 1"
                        class="rounded-md border border-gray-300 px-2 text-xs text-gray-500 hover:bg-gray-50">&times;</button>
                </div>
            </template>
            <button type="button" x-on:click="correos.push('')" class="mt-1 text-xs font-medium text-brand-green hover:underline">
                + Agregar correo
            </button>
        </div>
    </div>

    <div>
        <x-input-label for="externo-obs-{{ $idPrefix }}" value="Observaciones (opcional)" />
        <textarea id="externo-obs-{{ $idPrefix }}" name="observaciones" rows="2"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">{{ $externo?->observaciones }}</textarea>
    </div>

    <div @class(['flex justify-end gap-3' => $modalName])>
        @if ($modalName)
            <button type="button" x-on:click="$dispatch('close-modal', '{{ $modalName }}')" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                Cancelar
            </button>
        @endif
        <button type="submit" class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
            {{ $boton }}
        </button>
    </div>
</form>
