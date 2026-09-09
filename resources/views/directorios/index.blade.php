<x-sies-layout :title="'Directorios'">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Directorios</h1>
        <p class="mt-1 text-sm text-gray-500">
            Sucursales, gerencias, áreas centrales y contactos externos de la Gerencia Estatal FINABIEN CDMX.
        </p>
    </div>

    <div x-data="{ tab: 'sucursales' }" class="mt-6">
        <div role="tablist" aria-label="Secciones del directorio" class="flex flex-wrap gap-1 overflow-x-auto border-b border-gray-200">
            @foreach ([
                'sucursales' => 'Sucursales',
                'gerencias' => 'Gerencias',
                'areas' => 'Áreas centrales',
                'externos' => 'Personal externo',
            ] as $key => $label)
                <button type="button" role="tab" id="tab-{{ $key }}" aria-controls="panel-{{ $key }}"
                    x-on:click="tab = '{{ $key }}'" :aria-selected="(tab === '{{ $key }}').toString()"
                    :tabindex="tab === '{{ $key }}' ? 0 : -1"
                    :class="tab === '{{ $key }}' ? 'border-brand-green text-brand-green' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="whitespace-nowrap border-b-2 px-4 py-3 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-brand-green focus:ring-offset-2">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <div role="tabpanel" id="panel-sucursales" aria-labelledby="tab-sucursales" x-show="tab === 'sucursales'" class="pt-4">
            @include('directorios.partials.sucursales')
        </div>
        <div role="tabpanel" id="panel-gerencias" aria-labelledby="tab-gerencias" x-show="tab === 'gerencias'" x-cloak class="pt-4">
            @include('directorios.partials.gerencias')
        </div>
        <div role="tabpanel" id="panel-areas" aria-labelledby="tab-areas" x-show="tab === 'areas'" x-cloak class="pt-4">
            @include('directorios.partials.areas-centrales')
        </div>
        <div role="tabpanel" id="panel-externos" aria-labelledby="tab-externos" x-show="tab === 'externos'" x-cloak class="pt-4">
            @include('directorios.partials.externos')
        </div>
    </div>
</x-sies-layout>
