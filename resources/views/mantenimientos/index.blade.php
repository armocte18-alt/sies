<x-sies-layout :title="'Mantenimientos'">
    <h1 class="text-2xl font-bold text-gray-900">Mantenimientos</h1>
    <p class="mt-1 text-sm text-gray-500">Órdenes de mantenimiento a sucursales, con materiales, personal asignado y bitácora de cambios.</p>

    <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
        <div class="rounded-lg bg-white p-4 shadow-sm">
            <p class="text-xs font-medium text-gray-500">Total {{ now()->year }}</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $kpis['total'] }}</p>
        </div>
        <div class="rounded-lg bg-white p-4 shadow-sm">
            <p class="text-xs font-medium text-gray-500">Pendientes</p>
            <p class="mt-1 text-2xl font-bold text-gray-500">{{ $kpis['pendientes'] }}</p>
        </div>
        <div class="rounded-lg bg-white p-4 shadow-sm">
            <p class="text-xs font-medium text-gray-500">En proceso</p>
            <p class="mt-1 text-2xl font-bold text-amber-600">{{ $kpis['en_proceso'] }}</p>
        </div>
        <div class="rounded-lg bg-white p-4 shadow-sm">
            <p class="text-xs font-medium text-gray-500">Completados</p>
            <p class="mt-1 text-2xl font-bold text-emerald-600">{{ $kpis['completados'] }}</p>
        </div>
        <div class="rounded-lg bg-white p-4 shadow-sm">
            <p class="text-xs font-medium text-gray-500">Costo total</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">${{ number_format($kpis['costo_total'], 2) }}</p>
        </div>
    </div>

    <form method="GET" action="{{ route('mantenimientos.index') }}" x-data class="mt-6 flex flex-wrap items-end gap-3">
        <div>
            <x-input-label for="filtro-sucursal" value="Sucursal" />
            <select id="filtro-sucursal" name="sucursal_id" x-on:change="$el.form.submit()"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                <option value="">Todas</option>
                @foreach ($sucursales as $sucursal)
                    <option value="{{ $sucursal->id }}" @selected(request('sucursal_id') == $sucursal->id)>{{ \Illuminate\Support\Str::title($sucursal->nombre_oficial) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <x-input-label for="filtro-tipo" value="Tipo" />
            <select id="filtro-tipo" name="tipo_mantenimiento_id" x-on:change="$el.form.submit()"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                <option value="">Todos</option>
                @foreach ($tipos as $tipo)
                    <option value="{{ $tipo->id }}" @selected(request('tipo_mantenimiento_id') == $tipo->id)>{{ $tipo->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <x-input-label for="filtro-estatus" value="Estatus" />
            <select id="filtro-estatus" name="estatus" x-on:change="$el.form.submit()"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                <option value="">Todos</option>
                @foreach (App\Models\Mantenimiento::ESTATUS_LABELS as $valor => $etiqueta)
                    <option value="{{ $valor }}" @selected(request('estatus') === $valor)>{{ $etiqueta }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <x-input-label for="filtro-prioridad" value="Prioridad" />
            <select id="filtro-prioridad" name="prioridad" x-on:change="$el.form.submit()"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                <option value="">Todas</option>
                @foreach (App\Models\Mantenimiento::PRIORIDADES as $valor => $etiqueta)
                    <option value="{{ $valor }}" @selected(request('prioridad') === $valor)>{{ $etiqueta }}</option>
                @endforeach
            </select>
        </div>
        @if (request()->hasAny(['sucursal_id', 'tipo_mantenimiento_id', 'estatus', 'prioridad']))
            <a href="{{ route('mantenimientos.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">Limpiar filtros</a>
        @endif

        @can('mantenimientos.gestionar')
            <button type="button" x-on:click="$dispatch('open-modal', 'nuevo-mantenimiento')"
                class="ms-auto text-xs font-semibold text-brand-green hover:underline">
                + Nuevo mantenimiento
            </button>
        @endcan
    </form>

    <div class="mt-4">
        @include('mantenimientos.partials.tabla')
    </div>

    @can('mantenimientos.gestionar')
        @include('mantenimientos.partials.modales')
    @endcan
</x-sies-layout>
