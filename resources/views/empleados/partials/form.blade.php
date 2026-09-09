@php
    $idPrefix = $empleado?->id ?? 'nuevo';
    $modalName = $modalName ?? null;
@endphp

<form method="POST" action="{{ $accion }}" @class(['space-y-4', 'p-6' => $modalName])>
    @csrf
    @if ($metodo)
        @method($metodo)
    @endif

    @if ($modalName)
        <h2 class="text-lg font-medium text-gray-900">Nuevo empleado</h2>
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <x-input-label for="empleado-no-{{ $idPrefix }}" value="Número de empleado" />
            <x-text-input id="empleado-no-{{ $idPrefix }}" name="no_empleado" type="text" class="mt-1 block w-full" required maxlength="20" value="{{ $empleado?->no_empleado }}" />
            @if (!$empleado) <x-input-error :messages="$errors->get('no_empleado')" class="mt-1" /> @endif
        </div>
        <div>
            <x-input-label for="empleado-sucursal-{{ $idPrefix }}" value="Sucursal (opcional)" />
            <select id="empleado-sucursal-{{ $idPrefix }}" name="sucursal_id"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                <option value="">Sin asignar</option>
                @foreach ($sucursales as $sucursalOpcion)
                    <option value="{{ $sucursalOpcion->id }}" @selected($empleado?->sucursal_id === $sucursalOpcion->id)>
                        {{ $sucursalOpcion->nombre_oficial }} ({{ $sucursalOpcion->clave_financiera }})
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <x-input-label for="empleado-nombre-{{ $idPrefix }}" value="Nombre(s)" />
            <x-text-input id="empleado-nombre-{{ $idPrefix }}" name="nombre" type="text" class="mt-1 block w-full" required maxlength="100" value="{{ $empleado?->nombre }}" />
        </div>
        <div>
            <x-input-label for="empleado-paterno-{{ $idPrefix }}" value="Apellido paterno" />
            <x-text-input id="empleado-paterno-{{ $idPrefix }}" name="apellido_paterno" type="text" class="mt-1 block w-full" required maxlength="100" value="{{ $empleado?->apellido_paterno }}" />
        </div>
        <div>
            <x-input-label for="empleado-materno-{{ $idPrefix }}" value="Apellido materno (opcional)" />
            <x-text-input id="empleado-materno-{{ $idPrefix }}" name="apellido_materno" type="text" class="mt-1 block w-full" maxlength="100" value="{{ $empleado?->apellido_materno }}" />
        </div>
        <div>
            <x-input-label for="empleado-puesto-{{ $idPrefix }}" value="Puesto (opcional)" />
            <x-text-input id="empleado-puesto-{{ $idPrefix }}" name="puesto" type="text" class="mt-1 block w-full" maxlength="100" value="{{ $empleado?->puesto }}" />
        </div>
        <div class="sm:col-span-2">
            <x-input-label for="empleado-funcion-{{ $idPrefix }}" value="Función laboral (opcional)" />
            <x-text-input id="empleado-funcion-{{ $idPrefix }}" name="funcion_laboral" type="text" class="mt-1 block w-full" maxlength="100" value="{{ $empleado?->funcion_laboral }}" />
        </div>
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
