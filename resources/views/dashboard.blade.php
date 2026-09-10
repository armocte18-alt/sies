<x-sies-layout :title="'Dashboard'">
    <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
        Panel principal
    </h1>
    <p class="mt-1 text-sm font-medium text-brand-green">
        SIES · Sistema de Información, Estadística y Servicios
    </p>

    <div class="mt-4 rounded-lg border-l-4 border-brand-green bg-white p-5 shadow-sm">
        <p class="font-semibold text-gray-800">¡Bienvenido al sistema, {{ auth()->user()->name }}!</p>
        <p class="mt-2 text-sm text-gray-600">Has iniciado sesión correctamente en la plataforma institucional.</p>
        <p class="mt-1 text-sm text-gray-600">
            Utiliza el menú lateral para navegar entre los módulos operativos y las coordinaciones de la Gerencia Estatal FINABIEN CDMX.
        </p>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-dashboard.stat-card
            label="Sucursales Activas"
            :value="$sucursalesActivas"
            color="bg-green-600"
            :href="route('sucursales.index')"
        >
            <x-nav-icon name="building" class="h-8 w-8" />
        </x-dashboard.stat-card>

        <x-dashboard.stat-card
            label="Empleados"
            :value="$totalEmpleados"
            color="bg-sky-600"
            :href="route('empleados.index')"
        >
            <x-nav-icon name="users" class="h-8 w-8" />
        </x-dashboard.stat-card>

        <x-dashboard.stat-card
            label="Límite de Caja Total"
            :value="'$'.number_format($limiteExistenciaCajaTotal, 2)"
            color="bg-slate-800"
        >
            <x-nav-icon name="chart" class="h-8 w-8" />
        </x-dashboard.stat-card>

        <x-dashboard.stat-card
            label="Eventos Próximos (7 días)"
            :value="$eventosProximos"
            color="bg-gray-500"
            :href="route('calendario.index')"
        >
            <x-nav-icon name="calendar" class="h-8 w-8" />
        </x-dashboard.stat-card>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-3">
        <section class="flex flex-col rounded-lg bg-white p-4 shadow-sm lg:col-span-2" aria-labelledby="mapa-heading">
            <h2 id="mapa-heading" class="mb-3 flex items-center gap-2 text-base font-semibold text-gray-800">
                <x-nav-icon name="building" class="h-5 w-5 text-brand-green" />
                Ubicación de Sucursales
            </h2>

            @php
                $markers = $sucursales
                    ->filter(fn ($s) => $s->ubicacion?->latitud && $s->ubicacion?->longitud)
                    ->map(fn ($s) => [
                        'nombre' => $s->nombre_oficial,
                        'clave' => $s->clave_financiera,
                        'lat' => (float) $s->ubicacion->latitud,
                        'lng' => (float) $s->ubicacion->longitud,
                    ])->values();
            @endphp

            <div id="sucursales-map" role="img" aria-label="Mapa con la ubicación de las sucursales activas"
                data-markers="{{ $markers->toJson() }}"
                class="h-80 min-h-0 w-full flex-1 rounded-md bg-gray-100"></div>

            @if ($markers->isEmpty())
                <p class="mt-2 text-sm text-gray-500">Aún no hay sucursales con coordenadas capturadas.</p>
            @endif
        </section>

        <section class="rounded-lg bg-white p-4 shadow-sm" aria-labelledby="alcaldias-heading">
            <h2 id="alcaldias-heading" class="mb-3 flex items-center gap-2 text-base font-semibold text-gray-800">
                <x-nav-icon name="chart" class="h-5 w-5 text-brand-green" />
                Sucursales por alcaldía
            </h2>

            @if ($sucursalesPorAlcaldia->isEmpty())
                <p class="text-sm text-gray-500">Sin datos disponibles.</p>
            @else
                @php $max = $sucursalesPorAlcaldia->max('sucursales_count'); @endphp
                <ul class="space-y-3">
                    @foreach ($sucursalesPorAlcaldia as $alcaldia)
                        <li>
                            <div class="flex items-center justify-between text-sm">
                                <a href="{{ route('sucursales.index', ['alcaldia' => $alcaldia->id]) }}"
                                    class="font-medium uppercase text-gray-700 hover:text-brand-green hover:underline">
                                    {{ $alcaldia->nombre }}
                                </a>
                                <span class="text-gray-500">{{ $alcaldia->sucursales_count }}</span>
                            </div>
                            <div class="mt-1 h-2 w-full rounded-full bg-gray-100" role="presentation">
                                <div class="h-2 rounded-full bg-brand-green"
                                    style="width: {{ $max > 0 ? round(($alcaldia->sucursales_count / $max) * 100) : 0 }}%">
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    </div>
</x-sies-layout>
