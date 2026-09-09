<x-sies-layout :title="'Minutarios'">
    <h1 class="text-2xl font-bold text-gray-900">Minutarios</h1>
    <p class="mt-1 text-sm text-gray-500">Boletines informativos y oficios de la Gerencia Estatal, con folio consecutivo por año.</p>

    <div x-data="{ tab: 'boletines' }" class="mt-6">
        <div role="tablist" aria-label="Secciones de minutarios" class="flex gap-1 overflow-x-auto border-b border-gray-200">
            @foreach (['boletines' => 'Boletines', 'oficios' => 'Oficios'] as $key => $label)
                <button type="button" role="tab" id="tab-{{ $key }}" aria-controls="panel-{{ $key }}"
                    x-on:click="tab = '{{ $key }}'" :aria-selected="(tab === '{{ $key }}').toString()"
                    :tabindex="tab === '{{ $key }}' ? 0 : -1"
                    :class="tab === '{{ $key }}' ? 'border-brand-green text-brand-green' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="whitespace-nowrap border-b-2 px-4 py-3 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-brand-green focus:ring-offset-2">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <div role="tabpanel" id="panel-boletines" aria-labelledby="tab-boletines" x-show="tab === 'boletines'" class="pt-4">
            @include('minutarios.partials.boletines')
        </div>
        <div role="tabpanel" id="panel-oficios" aria-labelledby="tab-oficios" x-show="tab === 'oficios'" x-cloak class="pt-4">
            @include('minutarios.partials.oficios')
        </div>
    </div>
</x-sies-layout>
