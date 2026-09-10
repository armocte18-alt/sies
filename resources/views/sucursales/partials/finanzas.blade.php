@php $finanzas = $sucursal->finanzas; @endphp

<section class="rounded-lg bg-white p-4 shadow-sm" aria-labelledby="finanzas-heading">
    <h2 id="finanzas-heading" class="mb-3 flex items-center gap-2 text-base font-semibold text-gray-800">
        <x-nav-icon name="card" class="h-5 w-5 text-brand-green" />
        Finanzas
    </h2>

    @can('updateFinanzas', $sucursal)
        <form method="POST" action="{{ route('sucursales.finanzas.update', $sucursal) }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <div class="max-w-xs">
                <x-input-label for="limite_existencia_caja" value="Límite de existencia en caja" />
                <x-text-input id="limite_existencia_caja" name="limite_existencia_caja" type="number" step="0.01" min="0" class="mt-1 block w-full"
                    :value="old('limite_existencia_caja', $finanzas?->limite_existencia_caja ?? 0)" required />
                <x-input-error :messages="$errors->get('limite_existencia_caja')" class="mt-2" />
            </div>

            <button type="submit"
                class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                Guardar finanzas
            </button>
        </form>
    @else
        @if ($finanzas)
            <dl class="grid grid-cols-1 gap-x-4 gap-y-3 text-sm sm:grid-cols-3">
                <div><dt class="text-gray-500">Límite de caja</dt><dd class="font-medium text-gray-900">${{ number_format($finanzas->limite_existencia_caja, 2) }}</dd></div>
            </dl>
        @else
            <p class="text-sm text-gray-500">Aún no se ha capturado información financiera.</p>
        @endif
    @endcan
</section>
