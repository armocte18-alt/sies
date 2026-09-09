<x-modal name="nuevo-mantenimiento" focusable>
    <form method="POST" action="{{ route('mantenimientos.store') }}" class="p-6 space-y-4">
        @csrf
        <h2 class="text-lg font-medium text-gray-900">Nuevo mantenimiento</h2>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <x-input-label for="mant-titulo" value="Título" />
                <x-text-input id="mant-titulo" name="titulo" type="text" class="mt-1 block w-full" required maxlength="200" />
                <x-input-error :messages="$errors->get('titulo')" class="mt-1" />
            </div>
            <div>
                <x-input-label for="mant-sucursal" value="Sucursal" />
                <select id="mant-sucursal" name="sucursal_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                    <option value="">Gerencia / Área Central</option>
                    @foreach ($sucursales as $sucursal)
                        <option value="{{ $sucursal->id }}">{{ \Illuminate\Support\Str::title($sucursal->nombre_oficial) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <x-input-label for="mant-tipo" value="Tipo" />
                    <select id="mant-tipo" name="tipo_mantenimiento_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                        @foreach ($tipos as $tipo)
                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="button" x-on:click="$dispatch('close-modal', 'nuevo-mantenimiento'); $dispatch('open-modal', 'nuevo-tipo-mantenimiento')"
                    class="mb-0.5 whitespace-nowrap text-xs font-semibold text-brand-green hover:underline">
                    + Tipo
                </button>
            </div>
            <div>
                <x-input-label for="mant-prioridad" value="Prioridad" />
                <select id="mant-prioridad" name="prioridad" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                    @foreach (App\Models\Mantenimiento::PRIORIDADES as $valor => $etiqueta)
                        <option value="{{ $valor }}" @selected($valor === 'media')>{{ $etiqueta }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <x-input-label for="mant-costo" value="Costo de mano de obra (opcional)" />
                <x-text-input id="mant-costo" name="costo_mano_obra" type="number" step="0.01" min="0" class="mt-1 block w-full" value="0" />
            </div>
            <div>
                <x-input-label for="mant-inicio" value="Fecha programada de inicio" />
                <x-text-input id="mant-inicio" name="fecha_programada_inicio" type="date" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('fecha_programada_inicio')" class="mt-1" />
            </div>
            <div>
                <x-input-label for="mant-fin" value="Fecha programada de fin" />
                <x-text-input id="mant-fin" name="fecha_programada_fin" type="date" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('fecha_programada_fin')" class="mt-1" />
            </div>
            <div class="sm:col-span-2">
                <x-input-label for="mant-descripcion" value="Descripción (opcional)" />
                <textarea id="mant-descripcion" name="descripcion" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green"></textarea>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <button type="button" x-on:click="$dispatch('close-modal', 'nuevo-mantenimiento')" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                Cancelar
            </button>
            <button type="submit"
                class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                Registrar
            </button>
        </div>
    </form>
</x-modal>

<x-modal name="nuevo-tipo-mantenimiento" focusable>
    <form method="POST" action="{{ route('mantenimientos.tipos.store') }}" class="p-6 space-y-4">
        @csrf
        <h2 class="text-lg font-medium text-gray-900">Nuevo tipo de mantenimiento</h2>
        <div>
            <x-input-label for="tipo-nombre" value="Nombre" />
            <x-text-input id="tipo-nombre" name="nombre" type="text" class="mt-1 block w-full" required maxlength="100" />
            <x-input-error :messages="$errors->get('nombre')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="tipo-color" value="Color" />
            <input id="tipo-color" name="color" type="color" value="#285c4d" class="mt-1 block h-10 w-20 rounded-md border-gray-300 shadow-sm" />
        </div>
        <div class="flex justify-end gap-3">
            <button type="button" x-on:click="$dispatch('close-modal', 'nuevo-tipo-mantenimiento')" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                Cancelar
            </button>
            <button type="submit"
                class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                Agregar
            </button>
        </div>
    </form>
</x-modal>
