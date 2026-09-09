<x-sies-layout :title="'Catálogos de RR.HH.'">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Ajustes RR.HH. · Catálogos
        </h2>
    </x-slot>

    <div x-data="{ tab: 'niveles-salariales' }">
        <div role="tablist" aria-label="Catálogos de RR.HH." class="mb-6 flex flex-wrap gap-1 overflow-x-auto border-b border-gray-200">
            @foreach ([
                'niveles-salariales' => 'Niveles salariales',
                'nombramientos' => 'Tipo de nombramiento',
                'asistencia' => 'Control de asistencia',
                'escolaridad' => 'Escolaridad',
                'especialidades' => 'Especialidad',
                'puestos' => 'Puestos',
                'labores' => 'Labores',
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

        <div role="tabpanel" id="panel-niveles-salariales" aria-labelledby="tab-niveles-salariales" x-show="tab === 'niveles-salariales'" x-cloak>
            @include('rh.catalogos.partials.niveles-salariales')
        </div>

        @foreach ([
            'nombramientos' => 'tipos de nombramiento',
            'asistencia' => 'tipos de control de asistencia',
            'escolaridad' => 'niveles de escolaridad',
            'especialidades' => 'especialidades',
            'puestos' => 'puestos',
            'labores' => 'labores',
        ] as $clave => $titulo)
            <div role="tabpanel" id="panel-{{ $clave }}" aria-labelledby="tab-{{ $clave }}" x-show="tab === '{{ $clave }}'" x-cloak>
                @include('rh.catalogos.partials.catalogo-simple', [
                    'catalogo' => $clave,
                    'titulo' => $titulo,
                    'items' => $catalogos[$clave],
                ])
            </div>
        @endforeach
    </div>
</x-sies-layout>
