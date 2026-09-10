@php
    $documentosPorTipo = $sucursal->documentosAcervo->keyBy('tipo_documento_id');
    $puedeGestionarAcervo = auth()->user()->can('updateAcervo', $sucursal);
@endphp

<section class="rounded-lg bg-white p-4 shadow-sm" aria-labelledby="acervo-heading" x-data="{ historialAbierto: null }">
    <div class="mb-3 flex items-center justify-between">
        <h2 id="acervo-heading" class="flex items-center gap-2 text-base font-semibold text-gray-800">
            <x-nav-icon name="book" class="h-5 w-5 text-brand-green" />
            Acervo Documental
        </h2>
        @can('sucursales.editar.acervo')
            <button type="button" x-on:click="$dispatch('open-modal', 'nuevo-tipo-acervo')"
                class="text-xs font-semibold text-brand-green hover:underline">
                + Nuevo tipo de documento
            </button>
        @endcan
    </div>

    <div class="overflow-x-auto rounded-lg border border-gray-100">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <caption class="sr-only">Acervo documental de la sucursal</caption>
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-4 py-2 text-left font-semibold text-gray-600">Tipo de documento</th>
                    <th scope="col" class="px-4 py-2 text-left font-semibold text-gray-600">Estatus</th>
                    <th scope="col" class="px-4 py-2 text-left font-semibold text-gray-600">Versión vigente</th>
                    <th scope="col" class="px-4 py-2"><span class="sr-only">Acciones</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($tiposAcervo as $tipo)
                    @php $documento = $documentosPorTipo->get($tipo->id); @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $tipo->nombre }}</td>
                        <td class="px-4 py-3">
                            @if ($documento?->versionActual)
                                <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800">Vigente</span>
                            @else
                                <span class="inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-500">Sin cargar</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            @if ($documento?->versionActual)
                                {{ $documento->versionActual->nombre_original }}
                                <span class="text-xs text-gray-400">(v{{ $documento->versionActual->numero_version }})</span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-1">
                                @if ($documento?->versionActual)
                                    <a href="{{ route('sucursales.acervo.versiones.descargar', $documento->versionActual) }}"
                                        class="rounded p-1.5 text-brand-green hover:bg-brand-green-50" download>
                                        <x-action-icon icon="document-arrow-down" label="Descargar" />
                                    </a>
                                @endif
                                @if ($documento && $documento->versiones->count() > 1)
                                    <button type="button"
                                        x-on:click="historialAbierto = historialAbierto === {{ $tipo->id }} ? null : {{ $tipo->id }}"
                                        class="rounded p-1.5 text-gray-500 hover:bg-gray-100">
                                        <x-action-icon icon="clock" label="Ver historial de versiones" />
                                    </button>
                                @endif
                                @can('updateAcervo', $sucursal)
                                    <button type="button" x-on:click="$dispatch('open-modal', 'subir-documento-{{ $tipo->id }}')"
                                        class="rounded p-1.5 text-brand-green hover:bg-brand-green-50">
                                        <x-action-icon icon="arrow-up-tray" label="{{ $documento?->versionActual ? 'Subir nueva versión' : 'Subir archivo' }}" />
                                    </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @if ($documento && $documento->versiones->count() > 1)
                        <tr x-show="historialAbierto === {{ $tipo->id }}" x-cloak>
                            <td colspan="4" class="bg-gray-50 px-4 py-3">
                                <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Historial de versiones</p>
                                <ul class="space-y-1.5">
                                    @foreach ($documento->versiones->sortByDesc('numero_version') as $version)
                                        <li class="flex items-center justify-between gap-2 text-sm">
                                            <span class="text-gray-700">
                                                v{{ $version->numero_version }} — {{ $version->nombre_original }}
                                                <span class="text-xs text-gray-400">
                                                    {{ $version->subidoPor?->name ?? '—' }}, {{ $version->created_at->format('d/m/Y H:i') }}
                                                </span>
                                                @if ($version->id === $documento->version_actual_id)
                                                    <span class="ms-1 inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-800">Vigente</span>
                                                @endif
                                            </span>
                                            <span class="flex shrink-0 gap-1">
                                                <a href="{{ route('sucursales.acervo.versiones.descargar', $version) }}"
                                                    class="rounded p-1 text-brand-green hover:bg-brand-green-50" download>
                                                    <x-action-icon icon="document-arrow-down" label="Descargar esta versión" />
                                                </a>
                                                @can('updateAcervo', $sucursal)
                                                    @if ($version->id !== $documento->version_actual_id)
                                                        <form method="POST" action="{{ route('sucursales.acervo.versiones.restaurar', $version) }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="rounded p-1 text-sky-600 hover:bg-sky-50">
                                                                <x-action-icon icon="arrow-uturn-left" label="Restaurar como vigente" />
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endcan
                                                @role('administrador')
                                                    <form method="POST" action="{{ route('sucursales.acervo.versiones.destroy', $version) }}"
                                                        onsubmit="return confirm('¿Eliminar esta versión de forma permanente? No se puede deshacer.');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="rounded p-1 text-red-600 hover:bg-red-50">
                                                            <x-action-icon icon="trash" label="Eliminar versión" />
                                                        </button>
                                                    </form>
                                                @endrole
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">Sin tipos de documento configurados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @can('updateAcervo', $sucursal)
        @foreach ($tiposAcervo as $tipo)
            @php $documento = $documentosPorTipo->get($tipo->id); @endphp
            <x-modal name="subir-documento-{{ $tipo->id }}" focusable>
                <form method="POST" action="{{ route('sucursales.acervo.store', $sucursal) }}" enctype="multipart/form-data" class="p-6 space-y-4">
                    @csrf
                    <input type="hidden" name="tipo_documento_id" value="{{ $tipo->id }}">
                    <h2 class="text-lg font-medium text-gray-900">{{ $tipo->nombre }}</h2>
                    <p class="text-xs text-gray-500">
                        @if ($documento?->versionActual)
                            Se guardará como una nueva versión; la anterior queda conservada en el historial.
                        @else
                            Primera carga de este tipo de documento para esta sucursal.
                        @endif
                    </p>

                    <div>
                        <x-input-label value="Archivo (PDF, imagen, Word o Excel — máx. 20 MB)" />
                        <input type="file" name="archivo" required
                            class="mt-1 block w-full text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-brand-green file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-brand-green-dark">
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <x-input-label value="Folio (opcional)" />
                            <x-text-input name="folio" type="text" class="mt-1 block w-full" maxlength="255" value="{{ $documento?->folio }}" />
                        </div>
                        <div>
                            <x-input-label value="Fecha del documento (opcional)" />
                            <x-text-input name="fecha_documento" type="date" class="mt-1 block w-full" value="{{ $documento?->fecha_documento?->format('Y-m-d') }}" />
                        </div>
                    </div>
                    <div>
                        <x-input-label value="Comentario de esta versión (opcional)" />
                        <textarea name="comentario_version" rows="2" maxlength="500"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green"></textarea>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" x-on:click="$dispatch('close-modal', 'subir-documento-{{ $tipo->id }}')" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                            Subir
                        </button>
                    </div>
                </form>
            </x-modal>
        @endforeach

        <x-modal name="nuevo-tipo-acervo" focusable>
            <form method="POST" action="{{ route('sucursales.acervo.tipos.store') }}" class="p-6 space-y-4">
                @csrf
                <h2 class="text-lg font-medium text-gray-900">Nuevo tipo de documento</h2>
                <div>
                    <x-input-label for="tipo-acervo-nombre" value="Nombre" />
                    <x-text-input id="tipo-acervo-nombre" name="nombre" type="text" class="mt-1 block w-full" required maxlength="100" />
                    <x-input-error :messages="$errors->get('nombre')" class="mt-1" />
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close-modal', 'nuevo-tipo-acervo')" class="text-sm font-medium text-gray-600 hover:text-gray-900">
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
</section>
