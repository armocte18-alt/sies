<x-sies-layout :title="'Circulares'">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Circulares</h1>

        @can('circulares.gestionar')
            <button type="button" x-on:click="$dispatch('open-modal', 'agregar-circular')"
                class="inline-flex w-fit items-center gap-2 rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                + Nueva circular
            </button>
        @endcan
    </div>

    <form method="GET" action="{{ route('circulares.index') }}" class="mt-4" role="search" x-data="busquedaEnVivo"
        data-resultados="resultados-circulares" data-valor-inicial="{{ request('buscar') }}">
        <label for="buscar" class="sr-only">Buscar circular por número o asunto</label>
        <div class="flex max-w-md gap-2">
            <input type="search" id="buscar" name="buscar" x-model="valor"
                placeholder="Buscar por número o asunto..."
                x-on:input="buscar()"
                autocomplete="off"
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
            <button type="submit"
                class="rounded-md bg-gray-800 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-800">
                Buscar
            </button>
        </div>
    </form>

    <div id="resultados-circulares" class="mt-4">
        @include('circulares.partials.tabla')
    </div>

    @can('circulares.gestionar')
        <x-modal name="agregar-circular" focusable>
            <form method="POST" action="{{ route('circulares.store') }}" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <h2 class="text-lg font-medium text-gray-900">Nueva circular</h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="numero" value="Número" />
                        <x-text-input id="numero" name="numero" type="text" class="mt-1 block w-full" required maxlength="30" placeholder="Ej. 84/2026" />
                        <x-input-error :messages="$errors->get('numero')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="fecha_aplicacion" value="Fecha de aplicación (opcional)" />
                        <x-text-input id="fecha_aplicacion" name="fecha_aplicacion" type="date" class="mt-1 block w-full" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label for="asunto" value="Asunto" />
                        <textarea id="asunto" name="asunto" rows="2" required maxlength="500"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green"></textarea>
                        <x-input-error :messages="$errors->get('asunto')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="ambito" value="Ámbito (opcional)" />
                        <x-text-input id="ambito" name="ambito" type="text" class="mt-1 block w-full" maxlength="60" placeholder="General, cobranza..." />
                    </div>
                    <div>
                        <x-input-label for="archivo" value="Archivo PDF (opcional)" />
                        <input id="archivo" name="archivo" type="file" accept="application/pdf"
                            class="mt-1 block w-full text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-brand-green file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-white hover:file:bg-brand-green-dark">
                        <x-input-error :messages="$errors->get('archivo')" class="mt-1" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label for="enlace_externo" value="Enlace externo (opcional)" />
                        <x-text-input id="enlace_externo" name="enlace_externo" type="url" class="mt-1 block w-full" maxlength="500" placeholder="https://..." />
                        <x-input-error :messages="$errors->get('enlace_externo')" class="mt-1" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label for="tips" value="Tips / notas (opcional)" />
                        <textarea id="tips" name="tips" rows="2" maxlength="2000"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close-modal', 'agregar-circular')" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                        Agregar
                    </button>
                </div>
            </form>
        </x-modal>
    @endcan
</x-sies-layout>
