<x-sies-layout :title="'Vehículos Oficiales'">
    <h1 class="text-2xl font-bold text-gray-900">Vehículos Oficiales</h1>
    <p class="mt-1 text-sm text-gray-500">Catálogo de vehículos y conductores, y control de solicitudes de uso con validación de traslapes de agenda.</p>

    <div x-data="{ tab: 'solicitudes' }" class="mt-6">
        <div role="tablist" aria-label="Secciones de vehículos oficiales" class="flex gap-1 overflow-x-auto border-b border-gray-200">
            @foreach (['solicitudes' => 'Solicitudes', 'vehiculos' => 'Vehículos', 'conductores' => 'Conductores'] as $key => $label)
                <button type="button" role="tab" id="tab-{{ $key }}" aria-controls="panel-{{ $key }}"
                    x-on:click="tab = '{{ $key }}'" :aria-selected="(tab === '{{ $key }}').toString()"
                    :tabindex="tab === '{{ $key }}' ? 0 : -1"
                    :class="tab === '{{ $key }}' ? 'border-brand-green text-brand-green' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="whitespace-nowrap border-b-2 px-4 py-3 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-brand-green focus:ring-offset-2">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <div role="tabpanel" id="panel-solicitudes" aria-labelledby="tab-solicitudes" x-show="tab === 'solicitudes'" class="pt-4">
            @include('vehiculos.partials.solicitudes')
        </div>
        <div role="tabpanel" id="panel-vehiculos" aria-labelledby="tab-vehiculos" x-show="tab === 'vehiculos'" x-cloak class="pt-4">
            @include('vehiculos.partials.vehiculos')
        </div>
        <div role="tabpanel" id="panel-conductores" aria-labelledby="tab-conductores" x-show="tab === 'conductores'" x-cloak class="pt-4">
            @include('vehiculos.partials.conductores')
        </div>
    </div>
</x-sies-layout>
