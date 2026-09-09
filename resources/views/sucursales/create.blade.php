<x-sios-layout :title="'Nueva sucursal'">
    <h1 class="text-2xl font-bold text-gray-900">Nueva sucursal</h1>
    <p class="mt-1 text-sm text-gray-500">
        Registra los datos generales. El resto de la información (ubicación, horarios, inmueble, equipamiento y
        finanzas) se captura desde cada coordinación al entrar al detalle de la sucursal.
    </p>

    <form method="POST" action="{{ route('sucursales.store') }}" class="mt-6 max-w-2xl space-y-5 rounded-lg bg-white p-6 shadow-sm">
        @csrf

        <div>
            <x-input-label for="nombre_oficial" value="Nombre oficial" />
            <x-text-input id="nombre_oficial" name="nombre_oficial" type="text" class="mt-1 block w-full"
                :value="old('nombre_oficial')" required />
            <x-input-error :messages="$errors->get('nombre_oficial')" class="mt-2" />
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <x-input-label for="clave_financiera" value="Clave financiera" />
                <x-text-input id="clave_financiera" name="clave_financiera" type="text" class="mt-1 block w-full"
                    :value="old('clave_financiera')" required />
                <x-input-error :messages="$errors->get('clave_financiera')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="centro_distribucion" value="Centro de distribución" />
                <x-text-input id="centro_distribucion" name="centro_distribucion" type="text" class="mt-1 block w-full"
                    :value="old('centro_distribucion')" />
                <x-input-error :messages="$errors->get('centro_distribucion')" class="mt-2" />
            </div>
        </div>

        <div>
            <x-input-label for="estatus_operativo" value="Estatus operativo" />
            <select id="estatus_operativo" name="estatus_operativo" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                @foreach (['en_apertura' => 'En apertura', 'activa' => 'Activa', 'inactiva' => 'Inactiva', 'suspendida' => 'Suspendida'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('estatus_operativo') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('estatus_operativo')" class="mt-2" />
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit"
                class="rounded-md bg-brand-green px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                Guardar sucursal
            </button>
            <a href="{{ route('sucursales.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                Cancelar
            </a>
        </div>
    </form>
</x-sios-layout>
