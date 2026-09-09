<x-sies-layout :title="'Calendario de Eventos'">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Calendario de Eventos</h1>

        @can('calendario.gestionar')
            <button type="button" x-on:click="$dispatch('open-modal', 'nuevo-evento')"
                class="inline-flex w-fit items-center gap-2 rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                + Nuevo evento
            </button>
        @endcan
    </div>

    <div class="mt-4 flex items-center justify-between rounded-lg bg-white p-3 shadow-sm">
        <a href="{{ route('calendario.index', ['mes' => $mesAnterior->month, 'anio' => $mesAnterior->year]) }}"
            class="rounded-md p-2 text-gray-500 hover:bg-gray-100" aria-label="Mes anterior">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
        </a>

        <div class="flex items-center gap-3">
            <h2 class="text-lg font-semibold text-gray-800">{{ $tituloMes }}</h2>
            <a href="{{ route('calendario.index') }}" class="text-xs font-semibold text-brand-green hover:underline">Hoy</a>
        </div>

        <a href="{{ route('calendario.index', ['mes' => $mesSiguiente->month, 'anio' => $mesSiguiente->year]) }}"
            class="rounded-md p-2 text-gray-500 hover:bg-gray-100" aria-label="Mes siguiente">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
        </a>
    </div>

    @if ($eventosRapidos->isNotEmpty())
        <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-gray-500">
            <span class="font-semibold uppercase tracking-wide">Eventos frecuentes:</span>
            @foreach ($eventosRapidos as $rapido)
                <span class="inline-flex items-center gap-1.5">
                    <span class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $rapido->color }}"></span>
                    {{ $rapido->nombre }}
                </span>
            @endforeach
        </div>
    @endif

    <div class="mt-4 overflow-x-auto rounded-lg bg-white shadow-sm">
        <table class="min-w-full table-fixed border-collapse text-sm">
            <caption class="sr-only">Calendario de {{ $tituloMes }}</caption>
            <thead>
                <tr>
                    @foreach (['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'] as $dia)
                        <th scope="col" class="border-b border-gray-100 bg-gray-50 px-2 py-2 text-xs font-semibold uppercase text-gray-500">
                            {{ $dia }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($semanas as $semana)
                    <tr>
                        @foreach ($semana as $dia)
                            <td class="h-28 align-top border border-gray-100 p-1.5 {{ $dia['esMesActual'] ? '' : 'bg-gray-50' }}">
                                <div @class([
                                    'mb-1 inline-flex h-6 w-6 items-center justify-center rounded-full text-xs font-semibold',
                                    'bg-brand-green text-white' => $dia['esHoy'],
                                    'text-gray-800' => !$dia['esHoy'] && $dia['esMesActual'],
                                    'text-gray-400' => !$dia['esMesActual'],
                                ])>
                                    {{ $dia['fecha']->day }}
                                </div>
                                <div class="space-y-1">
                                    @foreach ($dia['eventos'] as $evento)
                                        <div class="group flex items-center justify-between gap-1 rounded px-1.5 py-0.5 text-xs text-white"
                                            style="background-color: {{ $evento->color }}" title="{{ $evento->nombre }}">
                                            <span class="truncate">{{ \Illuminate\Support\Str::title($evento->nombre) }}</span>
                                            @can('calendario.gestionar')
                                                <form method="POST" action="{{ route('calendario.destroy', $evento) }}"
                                                    onsubmit="return confirm('¿Eliminar el evento &quot;{{ $evento->nombre }}&quot;?');"
                                                    class="hidden group-hover:block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="leading-none text-white/80 hover:text-white" aria-label="Eliminar evento">&times;</button>
                                                </form>
                                            @endcan
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @can('calendario.gestionar')
        <x-modal name="nuevo-evento" focusable>
            <form method="POST" action="{{ route('calendario.store') }}" class="p-6 space-y-4">
                @csrf
                <h2 class="text-lg font-medium text-gray-900">Nuevo evento</h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <x-input-label for="evento-nombre" value="Nombre del evento" />
                        <x-text-input id="evento-nombre" name="nombre" type="text" class="mt-1 block w-full" required maxlength="255" />
                        <x-input-error :messages="$errors->get('nombre')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="evento-fecha-inicio" value="Fecha de inicio" />
                        <x-text-input id="evento-fecha-inicio" name="fecha_inicio" type="date" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('fecha_inicio')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="evento-fecha-fin" value="Fecha de fin (opcional)" />
                        <x-text-input id="evento-fecha-fin" name="fecha_fin" type="date" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('fecha_fin')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="evento-hora-inicio" value="Hora de inicio (opcional)" />
                        <x-text-input id="evento-hora-inicio" name="hora_inicio" type="time" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label for="evento-hora-fin" value="Hora de fin (opcional)" />
                        <x-text-input id="evento-hora-fin" name="hora_fin" type="time" class="mt-1 block w-full" />
                        <x-input-error :messages="$errors->get('hora_fin')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="evento-ubicacion" value="Ubicación (opcional)" />
                        <x-text-input id="evento-ubicacion" name="ubicacion" type="text" class="mt-1 block w-full" maxlength="255" />
                    </div>
                    <div>
                        <x-input-label for="evento-color" value="Color" />
                        <input id="evento-color" name="color" type="color" value="#135c46"
                            class="mt-1 block h-10 w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <x-input-label for="evento-tipo-asociado" value="Asociado a (opcional)" />
                        <select id="evento-tipo-asociado" name="tipo_asociado"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                            <option value="">Sin asociar</option>
                            <option value="area_central">Área central</option>
                            <option value="gerencia">Gerencia</option>
                            <option value="sucursal">Sucursal</option>
                            <option value="externo">Externo</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="evento-asociado-nombre" value="Nombre del asociado (opcional)" />
                        <x-text-input id="evento-asociado-nombre" name="asociado_nombre" type="text" class="mt-1 block w-full" maxlength="255" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label for="evento-invitados" value="Invitados (opcional, separados por coma)" />
                        <x-text-input id="evento-invitados" name="invitados" type="text" class="mt-1 block w-full" placeholder="claudia, juan, ..." />
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label for="evento-notas" value="Notas (opcional)" />
                        <textarea id="evento-notas" name="notas" rows="2" maxlength="2000"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green"></textarea>
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close-modal', 'nuevo-evento')" class="text-sm font-medium text-gray-600 hover:text-gray-900">
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
