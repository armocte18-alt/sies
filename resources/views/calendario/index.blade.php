@php
    $puedeGestionar = auth()->user()->can('calendario.gestionar');
@endphp

<x-sies-layout :title="'Calendario de Eventos'">
    <h1 class="text-2xl font-bold text-gray-900">Calendario de Eventos</h1>

    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-4">
        @if ($puedeGestionar)
            <div class="rounded-lg bg-white p-4 shadow-sm lg:col-span-1">
                <h2 class="mb-3 text-sm font-semibold text-gray-800">Eventos rápidos</h2>

                <div id="eventos-arrastrables" class="space-y-2"></div>

                <button type="button" id="btn-agregar-rapido"
                    class="mt-3 w-full rounded-md border border-dashed border-gray-300 px-3 py-2 text-sm font-medium text-gray-600 hover:border-brand-green hover:text-brand-green">
                    + Agregar evento rápido
                </button>

                <label class="mt-3 flex items-center gap-2 text-xs text-gray-500">
                    <input type="checkbox" id="quitar-despues-soltar" class="rounded border-gray-300 text-brand-green focus:ring-brand-green">
                    Quitar de la lista al soltar
                </label>
            </div>
        @endif

        <div class="rounded-lg bg-white p-4 shadow-sm {{ $puedeGestionar ? 'lg:col-span-3' : 'lg:col-span-4' }}">
            <div id="calendario"
                data-puede-gestionar="{{ $puedeGestionar ? '1' : '0' }}"
                data-ruta-eventos="{{ route('calendario.eventos') }}"
                @if ($puedeGestionar)
                    data-ruta-eventos-store="{{ route('calendario.eventos.store') }}"
                    data-ruta-eventos-update-base="{{ url('/calendario/eventos') }}"
                    data-ruta-rapidos="{{ route('calendario.rapidos.index') }}"
                    data-ruta-rapidos-store="{{ route('calendario.rapidos.store') }}"
                    data-ruta-rapidos-update-base="{{ url('/calendario/eventos-rapidos') }}"
                @endif
            ></div>
        </div>
    </div>

    {{-- Ficha informativa de solo lectura (usuarios sin permiso de gestión) --}}
    <x-modal name="modal-info-evento" :maxWidth="'md'">
        <div id="modal-info-evento" class="p-6">
            <h2 id="info-nombre-evento" class="text-lg font-medium text-gray-900"></h2>
            <dl class="mt-4 space-y-2 text-sm">
                <div class="flex gap-2"><dt class="w-32 shrink-0 font-semibold text-gray-600">Fecha:</dt><dd id="info-fecha" class="text-gray-800"></dd></div>
                <div class="flex gap-2"><dt class="w-32 shrink-0 font-semibold text-gray-600">Hora:</dt><dd id="info-hora" class="text-gray-800"></dd></div>
                <div class="flex gap-2"><dt class="w-32 shrink-0 font-semibold text-gray-600">Ubicación:</dt><dd id="info-ubicacion" class="text-gray-800"></dd></div>
                <div class="flex gap-2"><dt class="w-32 shrink-0 font-semibold text-gray-600">Convocado por:</dt><dd id="info-convocado-por" class="text-gray-800"></dd></div>
                <div class="flex gap-2"><dt class="w-32 shrink-0 font-semibold text-gray-600">Invitados:</dt><dd id="info-invitados" class="text-gray-800"></dd></div>
                <div id="info-notas-contenedor" class="flex gap-2 hidden"><dt class="w-32 shrink-0 font-semibold text-gray-600">Notas:</dt><dd id="info-notas" class="text-gray-800"></dd></div>
            </dl>
            <div class="mt-6 flex justify-end">
                <button type="button" x-on:click="$dispatch('close-modal', 'modal-info-evento')" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                    Cerrar
                </button>
            </div>
        </div>
    </x-modal>

    @if ($puedeGestionar)
        {{-- Crear / editar evento --}}
        <x-modal name="modal-evento" focusable :maxWidth="'2xl'">
            <form id="form-evento" class="p-6 space-y-4">
                <input type="hidden" id="evento-id">
                <input type="hidden" id="evento-grupo-recurrencia">

                <h2 id="titulo-modal-evento" class="text-lg font-medium text-gray-900">Nuevo evento</h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="sm:col-span-2">
                        <x-input-label for="evento-nombre" value="Nombre del evento" />
                        <x-text-input id="evento-nombre" type="text" class="mt-1 block w-full" required maxlength="255" />
                    </div>
                    <div>
                        <x-input-label for="evento-color" value="Color" />
                        <input id="evento-color" type="color" value="#135c46" class="mt-1 block h-10 w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                </div>

                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" id="evento-todo-el-dia" class="rounded border-gray-300 text-brand-green focus:ring-brand-green">
                    Todo el día (sin hora específica)
                </label>

                <div id="campo-recurrente">
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" id="evento-recurrente" class="rounded border-gray-300 text-brand-green focus:ring-brand-green">
                        Repetir periódicamente
                    </label>
                </div>

                <div id="bloque-recurrencia" class="hidden rounded-md border border-gray-200 bg-gray-50 p-3">
                    <p class="mb-2 text-sm font-semibold text-gray-700">Repetir los días:</p>
                    <div class="flex flex-wrap gap-3">
                        @foreach (['1' => 'Lun', '2' => 'Mar', '3' => 'Mié', '4' => 'Jue', '5' => 'Vie', '6' => 'Sáb', '0' => 'Dom'] as $valor => $etiqueta)
                            <label class="flex items-center gap-1.5 text-sm text-gray-700">
                                <input type="checkbox" class="dia-recurrencia rounded border-gray-300 text-brand-green focus:ring-brand-green" value="{{ $valor }}">
                                {{ $etiqueta }}
                            </label>
                        @endforeach
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Se usará el rango de "Fecha de inicio" y "Fecha de fin" de abajo como el periodo de repetición.</p>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="evento-fecha-inicio" value="Fecha de inicio" />
                        <x-text-input id="evento-fecha-inicio" type="date" class="mt-1 block w-full" required />
                    </div>
                    <div>
                        <x-input-label for="evento-fecha-fin" value="Fecha de fin (solo si dura varios días)" />
                        <x-text-input id="evento-fecha-fin" type="date" class="mt-1 block w-full" />
                    </div>
                </div>

                <div id="fila-horas" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="evento-hora-inicio" value="Hora de inicio" />
                        <x-text-input id="evento-hora-inicio" type="time" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label for="evento-hora-fin" value="Hora de fin" />
                        <x-text-input id="evento-hora-fin" type="time" class="mt-1 block w-full" />
                    </div>
                </div>

                <div>
                    <x-input-label for="evento-ubicacion" value="Ubicación (opcional)" />
                    <x-text-input id="evento-ubicacion" type="text" class="mt-1 block w-full" maxlength="255" />
                </div>

                <div>
                    <x-input-label for="evento-tipo-asociado" value="Evento convocado por" />
                    <select id="evento-tipo-asociado" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                        <option value="">Selecciona...</option>
                        <option value="area_central">Área central</option>
                        <option value="gerencia">Gerencia CDMX</option>
                        <option value="sucursal">Sucursal</option>
                        <option value="externo">Externo</option>
                    </select>
                </div>

                <div>
                    <x-input-label for="evento-invitados" value="Personal invitado / convocado (opcional, separados por coma)" />
                    <x-text-input id="evento-invitados" type="text" class="mt-1 block w-full" placeholder="claudia, juan, ..." />
                </div>

                <div>
                    <x-input-label for="evento-notas" value="Notas (opcional)" />
                    <textarea id="evento-notas" rows="2" maxlength="2000"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green"></textarea>
                </div>

                <div class="flex items-center justify-between">
                    <button type="button" id="btn-eliminar-evento" class="hidden rounded p-1.5 text-red-600 hover:bg-red-50">
                        <x-action-icon icon="trash" label="Eliminar" />
                    </button>
                    <div class="ms-auto flex gap-3">
                        <button type="button" x-on:click="$dispatch('close-modal', 'modal-evento')" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                            Guardar
                        </button>
                    </div>
                </div>
            </form>
        </x-modal>

        {{-- Crear / editar evento rápido --}}
        <x-modal name="modal-evento-rapido" :maxWidth="'sm'">
            <form id="form-evento-rapido" class="p-6 space-y-4">
                <input type="hidden" id="rapido-id">
                <h2 id="titulo-modal-rapido" class="text-lg font-medium text-gray-900">Nuevo evento rápido</h2>

                <div class="grid grid-cols-3 gap-4">
                    <div class="col-span-2">
                        <x-input-label for="rapido-nombre" value="Nombre" />
                        <x-text-input id="rapido-nombre" type="text" class="mt-1 block w-full" required maxlength="150" />
                    </div>
                    <div>
                        <x-input-label for="rapido-color" value="Color" />
                        <input id="rapido-color" type="color" value="#135c46" class="mt-1 block h-10 w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <button type="button" id="btn-eliminar-rapido" class="hidden rounded p-1.5 text-red-600 hover:bg-red-50">
                        <x-action-icon icon="trash" label="Eliminar" />
                    </button>
                    <div class="ms-auto flex gap-3">
                        <button type="button" x-on:click="$dispatch('close-modal', 'modal-evento-rapido')" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                            Guardar
                        </button>
                    </div>
                </div>
            </form>
        </x-modal>
    @endif

    @vite('resources/js/calendario.js')
</x-sies-layout>
