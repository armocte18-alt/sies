@php /** @var \App\Models\Mantenimiento $mantenimiento */ @endphp

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <div>
        <h4 class="mb-2 text-sm font-semibold text-gray-800">Datos generales</h4>
        @can('mantenimientos.gestionar')
            <form method="POST" action="{{ route('mantenimientos.update', $mantenimiento) }}" class="space-y-3 rounded-lg bg-white p-4 shadow-sm">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <x-input-label value="Título" />
                        <x-text-input name="titulo" type="text" class="mt-1 block w-full" required maxlength="200" value="{{ $mantenimiento->titulo }}" />
                    </div>
                    <div>
                        <x-input-label value="Sucursal" />
                        <select name="sucursal_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                            <option value="">Gerencia / Área Central</option>
                            @foreach ($sucursales as $sucursal)
                                <option value="{{ $sucursal->id }}" @selected($mantenimiento->sucursal_id === $sucursal->id)>{{ \Illuminate\Support\Str::title($sucursal->nombre_oficial) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Tipo" />
                        <select name="tipo_mantenimiento_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                            @foreach ($tipos as $tipo)
                                <option value="{{ $tipo->id }}" @selected($mantenimiento->tipo_mantenimiento_id === $tipo->id)>{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Prioridad" />
                        <select name="prioridad" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                            @foreach (App\Models\Mantenimiento::PRIORIDADES as $valor => $etiqueta)
                                <option value="{{ $valor }}" @selected($mantenimiento->prioridad === $valor)>{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Costo de mano de obra" />
                        <x-text-input name="costo_mano_obra" type="number" step="0.01" min="0" class="mt-1 block w-full" value="{{ $mantenimiento->costo_mano_obra }}" />
                    </div>
                    <div>
                        <x-input-label value="Fecha programada de inicio" />
                        <x-text-input name="fecha_programada_inicio" type="date" class="mt-1 block w-full" required value="{{ $mantenimiento->fecha_programada_inicio->format('Y-m-d') }}" />
                    </div>
                    <div>
                        <x-input-label value="Fecha programada de fin" />
                        <x-text-input name="fecha_programada_fin" type="date" class="mt-1 block w-full" required value="{{ $mantenimiento->fecha_programada_fin->format('Y-m-d') }}" />
                    </div>
                    <div class="sm:col-span-2">
                        <x-input-label value="Descripción" />
                        <textarea name="descripcion" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">{{ $mantenimiento->descripcion }}</textarea>
                    </div>
                </div>
                <button type="submit" class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                    Guardar cambios
                </button>
            </form>
        @else
            <div class="space-y-1 rounded-lg bg-white p-4 text-sm text-gray-600 shadow-sm">
                <p>{{ $mantenimiento->descripcion ?: 'Sin descripción.' }}</p>
            </div>
        @endcan

        @can('mantenimientos.gestionar')
            <h4 class="mb-2 mt-4 text-sm font-semibold text-gray-800">Cambiar estatus</h4>
            <form method="POST" action="{{ route('mantenimientos.estatus', $mantenimiento) }}" class="flex flex-wrap items-end gap-3 rounded-lg bg-white p-4 shadow-sm">
                @csrf
                @method('PATCH')
                <div>
                    <x-input-label value="Nuevo estatus" />
                    <select name="estatus" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                        @foreach (App\Models\Mantenimiento::ESTATUS_LABELS as $valor => $etiqueta)
                            <option value="{{ $valor }}" @selected($mantenimiento->estatus === $valor)>{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1">
                    <x-input-label value="Comentario (opcional)" />
                    <x-text-input name="comentario" type="text" class="mt-1 block w-full" maxlength="500" />
                </div>
                <button type="submit" class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                    Actualizar
                </button>
            </form>
        @endcan
    </div>

    <div class="space-y-4">
        <div>
            <h4 class="mb-2 text-sm font-semibold text-gray-800">Materiales</h4>
            <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold text-gray-600">Material</th>
                            <th class="px-3 py-2 text-left font-semibold text-gray-600">Cant.</th>
                            <th class="px-3 py-2 text-left font-semibold text-gray-600">C. unitario</th>
                            <th class="px-3 py-2 text-left font-semibold text-gray-600">Total</th>
                            @can('mantenimientos.gestionar')
                                <th class="px-3 py-2"><span class="sr-only">Acciones</span></th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($mantenimiento->materiales as $material)
                            <tr>
                                <td class="px-3 py-2 text-gray-700">{{ $material->nombre_material }} ({{ $material->unidad }})</td>
                                <td class="px-3 py-2 text-gray-600">{{ rtrim(rtrim(number_format($material->cantidad, 2), '0'), '.') }}</td>
                                <td class="px-3 py-2 text-gray-600">${{ number_format($material->costo_unitario, 2) }}</td>
                                <td class="px-3 py-2 text-gray-600">${{ number_format($material->costo_total, 2) }}</td>
                                @can('mantenimientos.gestionar')
                                    <td class="px-3 py-2 text-right">
                                        <form method="POST" action="{{ route('mantenimientos.materiales.destroy', [$mantenimiento, $material]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded p-1 text-red-600 hover:bg-red-50">
                                                <x-action-icon icon="trash" label="Eliminar material" />
                                            </button>
                                        </form>
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-3 py-3 text-center text-gray-400">Sin materiales registrados.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @can('mantenimientos.gestionar')
                    <form method="POST" action="{{ route('mantenimientos.materiales.store', $mantenimiento) }}" class="flex flex-wrap items-end gap-2 border-t border-gray-100 p-3">
                        @csrf
                        <input type="text" name="nombre_material" placeholder="Material" required maxlength="150" class="w-32 rounded-md border-gray-300 text-xs shadow-sm focus:border-brand-green focus:ring-brand-green" />
                        <input type="text" name="unidad" placeholder="Unidad" maxlength="30" value="pza" class="w-16 rounded-md border-gray-300 text-xs shadow-sm focus:border-brand-green focus:ring-brand-green" />
                        <input type="number" name="cantidad" placeholder="Cant." step="0.01" min="0.01" required class="w-16 rounded-md border-gray-300 text-xs shadow-sm focus:border-brand-green focus:ring-brand-green" />
                        <input type="number" name="costo_unitario" placeholder="C. unitario" step="0.01" min="0" required class="w-20 rounded-md border-gray-300 text-xs shadow-sm focus:border-brand-green focus:ring-brand-green" />
                        <button type="submit" class="rounded-md bg-brand-green px-3 py-1.5 text-xs font-semibold text-white shadow hover:bg-brand-green-dark">Agregar</button>
                    </form>
                @endcan
            </div>
        </div>

        <div>
            <h4 class="mb-2 text-sm font-semibold text-gray-800">Personal asignado</h4>
            <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                <table class="min-w-full divide-y divide-gray-200 text-xs">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold text-gray-600">Empleado</th>
                            <th class="px-3 py-2 text-left font-semibold text-gray-600">Rol</th>
                            @can('mantenimientos.gestionar')
                                <th class="px-3 py-2"><span class="sr-only">Acciones</span></th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($mantenimiento->personal as $empleado)
                            <tr>
                                <td class="px-3 py-2 text-gray-700">{{ $empleado->nombre }} {{ $empleado->apellido_paterno }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ $empleado->pivot->rol_en_mantenimiento ?: '—' }}</td>
                                @can('mantenimientos.gestionar')
                                    <td class="px-3 py-2 text-right">
                                        <form method="POST" action="{{ route('mantenimientos.personal.destroy', [$mantenimiento, $empleado]) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded p-1 text-red-600 hover:bg-red-50">
                                                <x-action-icon icon="trash" label="Retirar" />
                                            </button>
                                        </form>
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-3 py-3 text-center text-gray-400">Sin personal asignado.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @can('mantenimientos.gestionar')
                    <form method="POST" action="{{ route('mantenimientos.personal.store', $mantenimiento) }}" class="flex flex-wrap items-end gap-2 border-t border-gray-100 p-3">
                        @csrf
                        <select name="empleado_id" required class="min-w-[10rem] rounded-md border-gray-300 text-xs shadow-sm focus:border-brand-green focus:ring-brand-green">
                            <option value="">Selecciona un empleado</option>
                            @foreach ($empleados as $empleado)
                                <option value="{{ $empleado->id }}">{{ $empleado->nombre }} {{ $empleado->apellido_paterno }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="rol_en_mantenimiento" placeholder="Rol (opcional)" maxlength="100" class="w-32 rounded-md border-gray-300 text-xs shadow-sm focus:border-brand-green focus:ring-brand-green" />
                        <button type="submit" class="rounded-md bg-brand-green px-3 py-1.5 text-xs font-semibold text-white shadow hover:bg-brand-green-dark">Asignar</button>
                    </form>
                @endcan
            </div>
        </div>

        <div>
            <h4 class="mb-2 text-sm font-semibold text-gray-800">Bitácora</h4>
            <ul class="max-h-48 space-y-2 overflow-y-auto rounded-lg bg-white p-3 text-xs shadow-sm">
                @forelse ($mantenimiento->bitacora as $entrada)
                    <li class="border-b border-gray-100 pb-2 last:border-0 last:pb-0">
                        <p class="text-gray-700">
                            <span class="font-semibold">{{ $entrada->usuario?->name ?? '—' }}</span>
                            — {{ $entrada->comentario ?: str_replace('_', ' ', $entrada->accion) }}
                            @if ($entrada->valor_anterior || $entrada->valor_nuevo)
                                <span class="text-gray-500">({{ $entrada->valor_anterior }} → {{ $entrada->valor_nuevo }})</span>
                            @endif
                        </p>
                        <p class="text-gray-400">{{ $entrada->created_at->format('d/m/Y H:i') }}</p>
                    </li>
                @empty
                    <li class="text-center text-gray-400">Sin movimientos registrados.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
