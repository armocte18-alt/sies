<x-sies-layout :title="'Control de Tarjetas'">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Control de Tarjetas</h1>
            <p class="mt-1 text-sm text-gray-500">Inventario de tarjetas institucionales FINABIEN por producto.</p>
        </div>
        <a href="{{ route('tarjetas.historial') }}" class="text-sm font-semibold text-brand-green hover:underline">
            Ver historial de movimientos →
        </a>
    </div>

    @can('tarjetas.gestionar')
        <div class="mt-4 flex flex-wrap gap-2">
            <button type="button" x-data x-on:click="$dispatch('open-modal', 'agregar-producto')"
                class="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                + Producto
            </button>
            <button type="button" x-data x-on:click="$dispatch('open-modal', 'agregar-tarjeta')"
                class="rounded-md border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50">
                + Alta de tarjeta
            </button>
            <button type="button" x-data x-on:click="$dispatch('open-modal', 'asignar-tarjetas')"
                class="rounded-md bg-brand-green px-3 py-1.5 text-xs font-semibold text-white hover:bg-brand-green-dark">
                Asignar tarjetas (FIFO)
            </button>
            <button type="button" x-data x-on:click="$dispatch('open-modal', 'retirar-por-sucursal')"
                class="rounded-md border border-red-200 bg-white px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50">
                Retiro por cierre de sucursal
            </button>
        </div>
    @endcan

    <form method="GET" action="{{ route('tarjetas.index') }}" class="mt-4">
        <label for="producto" class="sr-only">Producto</label>
        <select id="producto" name="producto" onchange="this.form.submit()"
            class="rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
            @foreach ($productos as $productoOpcion)
                <option value="{{ $productoOpcion->id }}" @selected($productoOpcion->id === $productoId)>{{ $productoOpcion->nombre }}</option>
            @endforeach
        </select>
    </form>

    <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <x-dashboard.stat-card label="En stock" :value="$resumen['en_stock'] ?? 0" color="bg-sky-600">
            <x-nav-icon name="card" class="h-8 w-8" />
        </x-dashboard.stat-card>
        <x-dashboard.stat-card label="Activas" :value="$resumen['activa'] ?? 0" color="bg-brand-green">
            <x-nav-icon name="check-circle" class="h-8 w-8" />
        </x-dashboard.stat-card>
        <x-dashboard.stat-card label="Renominadas" :value="$resumen['renominada'] ?? 0" color="bg-gray-700">
            <x-nav-icon name="card" class="h-8 w-8" />
        </x-dashboard.stat-card>
    </div>

    <div class="mt-4 overflow-x-auto rounded-lg bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <caption class="sr-only">Inventario de tarjetas</caption>
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">No. tarjeta</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Cuenta</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Estatus</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Destino</th>
                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Oficio</th>
                    <th scope="col" class="px-4 py-3"><span class="sr-only">Acciones</span></th>
                </tr>
            </thead>
            <tbody x-data="{ retirarId: null }" class="divide-y divide-gray-100">
                @forelse ($inventario as $tarjeta)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">•••• {{ $tarjeta->numero_tarjeta }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $tarjeta->cuenta }}</td>
                        <td class="px-4 py-3">
                            <span @class([
                                'inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium',
                                'bg-sky-100 text-sky-800' => $tarjeta->estatus === 'en_stock',
                                'bg-emerald-100 text-emerald-800' => $tarjeta->estatus === 'activa',
                                'bg-gray-100 text-gray-600' => $tarjeta->estatus === 'renominada',
                            ])>
                                {{ App\Models\TarjetaInventario::ESTATUS_LABELS[$tarjeta->estatus] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $tarjeta->destinoTexto() ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $tarjeta->numero_oficio ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            @can('tarjetas.gestionar')
                                <div class="flex justify-end gap-1">
                                    @if ($tarjeta->estatus === 'activa')
                                        <form method="POST" action="{{ route('tarjetas.renominar', $tarjeta) }}"
                                            onsubmit="return confirm('¿Marcar esta tarjeta como renominada (entregada al usuario final)?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="rounded p-1.5 text-brand-green hover:bg-brand-green-50">
                                                <x-action-icon icon="check-circle" label="Marcar como renominada" />
                                            </button>
                                        </form>
                                        <button type="button" x-on:click="$dispatch('open-modal', 'retirar-{{ $tarjeta->id }}')"
                                            class="rounded p-1.5 text-red-600 hover:bg-red-50">
                                            <x-action-icon icon="ban" label="Retirar" />
                                        </button>
                                    @elseif ($tarjeta->estatus === 'renominada')
                                        <button type="button" x-on:click="$dispatch('open-modal', 'corregir-{{ $tarjeta->id }}')"
                                            class="rounded p-1.5 text-amber-600 hover:bg-amber-50">
                                            <x-action-icon icon="pencil" label="Corregir renominación" />
                                        </button>
                                    @endif
                                </div>
                            @endcan
                        </td>
                    </tr>

                    @can('tarjetas.gestionar')
                        @if ($tarjeta->estatus === 'activa')
                            <x-modal name="retirar-{{ $tarjeta->id }}" :maxWidth="'sm'">
                                <form method="POST" action="{{ route('tarjetas.retirar', $tarjeta) }}" class="p-6 space-y-4">
                                    @csrf
                                    @method('PATCH')
                                    <h2 class="text-lg font-medium text-gray-900">Retirar tarjeta •••• {{ $tarjeta->numero_tarjeta }}</h2>
                                    <p class="text-sm text-gray-600">Regresa a "En stock" y queda disponible para una nueva asignación.</p>
                                    <div>
                                        <x-input-label value="Motivo (opcional)" />
                                        <textarea name="motivo" rows="2" maxlength="500" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green"></textarea>
                                    </div>
                                    <div class="flex justify-end gap-3">
                                        <button type="button" x-on:click="$dispatch('close-modal', 'retirar-{{ $tarjeta->id }}')" class="text-sm font-medium text-gray-600 hover:text-gray-900">Cancelar</button>
                                        <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-red-700">Retirar</button>
                                    </div>
                                </form>
                            </x-modal>
                        @elseif ($tarjeta->estatus === 'renominada')
                            <x-modal name="corregir-{{ $tarjeta->id }}" :maxWidth="'sm'">
                                <form method="POST" action="{{ route('tarjetas.corregir-renominacion', $tarjeta) }}" class="p-6 space-y-4">
                                    @csrf
                                    @method('PATCH')
                                    <h2 class="text-lg font-medium text-gray-900">Corregir renominación</h2>
                                    <p class="text-sm text-gray-600">Regresa la tarjeta •••• {{ $tarjeta->numero_tarjeta }} a "Activa" (sigue asignada a su destino).</p>
                                    <div>
                                        <x-input-label value="Motivo (obligatorio)" />
                                        <textarea name="motivo" rows="2" required minlength="10" maxlength="500" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green"></textarea>
                                    </div>
                                    <div class="flex justify-end gap-3">
                                        <button type="button" x-on:click="$dispatch('close-modal', 'corregir-{{ $tarjeta->id }}')" class="text-sm font-medium text-gray-600 hover:text-gray-900">Cancelar</button>
                                        <button type="submit" class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark">Confirmar</button>
                                    </div>
                                </form>
                            </x-modal>
                        @endif
                    @endcan
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">Sin tarjetas registradas para este producto.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $inventario->links() }}</div>

    @can('tarjetas.gestionar')
        <x-modal name="agregar-producto" :maxWidth="'sm'">
            <form method="POST" action="{{ route('tarjetas.productos.store') }}" class="p-6 space-y-4">
                @csrf
                <h2 class="text-lg font-medium text-gray-900">Nuevo producto de tarjeta</h2>
                <div>
                    <x-input-label for="producto-nombre" value="Nombre" />
                    <x-text-input id="producto-nombre" name="nombre" type="text" class="mt-1 block w-full" required maxlength="255" />
                    <x-input-error :messages="$errors->get('nombre')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="producto-descripcion" value="Descripción (opcional)" />
                    <x-text-input id="producto-descripcion" name="descripcion" type="text" class="mt-1 block w-full" maxlength="255" />
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close-modal', 'agregar-producto')" class="text-sm font-medium text-gray-600 hover:text-gray-900">Cancelar</button>
                    <button type="submit" class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark">Agregar</button>
                </div>
            </form>
        </x-modal>

        <x-modal name="agregar-tarjeta" focusable>
            <form method="POST" action="{{ route('tarjetas.store') }}" class="p-6 space-y-4">
                @csrf
                <h2 class="text-lg font-medium text-gray-900">Alta de tarjeta</h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="tarjeta-producto" value="Producto" />
                        <select id="tarjeta-producto" name="producto_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                            @foreach ($productos as $productoOpcion)
                                <option value="{{ $productoOpcion->id }}">{{ $productoOpcion->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="tarjeta-fecha" value="Fecha de recepción" />
                        <x-text-input id="tarjeta-fecha" name="fecha_recepcion" type="date" class="mt-1 block w-full" required value="{{ now()->format('Y-m-d') }}" />
                    </div>
                    <div>
                        <x-input-label for="tarjeta-numero" value="Últimos 4 dígitos" />
                        <x-text-input id="tarjeta-numero" name="numero_tarjeta" type="text" class="mt-1 block w-full" required maxlength="4" pattern="[0-9]{4}" />
                        <x-input-error :messages="$errors->get('numero_tarjeta')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="tarjeta-cuenta" value="Cuenta" />
                        <x-text-input id="tarjeta-cuenta" name="cuenta" type="text" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('cuenta')" class="mt-1" />
                    </div>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close-modal', 'agregar-tarjeta')" class="text-sm font-medium text-gray-600 hover:text-gray-900">Cancelar</button>
                    <button type="submit" class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark">Agregar</button>
                </div>
            </form>
        </x-modal>

        <x-modal name="asignar-tarjetas" focusable>
            <form method="POST" action="{{ route('tarjetas.asignar') }}" class="p-6 space-y-4">
                @csrf
                <h2 class="text-lg font-medium text-gray-900">Asignar tarjetas (FIFO)</h2>
                <p class="text-xs text-gray-500">Toma las tarjetas más antiguas "En stock" y las pasa a "Activa" con un folio de oficio nuevo.</p>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <x-input-label for="asignar-producto" value="Producto" />
                        <select id="asignar-producto" name="producto_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                            @foreach ($productos as $productoOpcion)
                                <option value="{{ $productoOpcion->id }}">{{ $productoOpcion->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="asignar-cantidad" value="Cantidad" />
                        <x-text-input id="asignar-cantidad" name="cantidad" type="number" min="1" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('cantidad')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="asignar-sucursal" value="Sucursal destino" />
                        <select id="asignar-sucursal" name="destino_sucursal_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                            <option value="">-- Otra coordinación (ver descripción) --</option>
                            @foreach ($sucursales as $sucursalOpcion)
                                <option value="{{ $sucursalOpcion->id }}">{{ $sucursalOpcion->nombre_oficial }} ({{ $sucursalOpcion->clave_financiera }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="asignar-otro" value="Descripción del destino (si no es una sucursal)" />
                        <x-text-input id="asignar-otro" name="otro_destino_descripcion" type="text" class="mt-1 block w-full" maxlength="255" />
                        <x-input-error :messages="$errors->get('destino_sucursal_id')" class="mt-1" />
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close-modal', 'asignar-tarjetas')" class="text-sm font-medium text-gray-600 hover:text-gray-900">Cancelar</button>
                    <button type="submit" class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark">Asignar</button>
                </div>
            </form>
        </x-modal>

        <x-modal name="retirar-por-sucursal" :maxWidth="'sm'">
            <form method="POST" action="{{ route('tarjetas.retirar-por-sucursal') }}" class="p-6 space-y-4">
                @csrf
                <h2 class="text-lg font-medium text-gray-900">Retiro por cierre de sucursal</h2>
                <p class="text-sm text-gray-600">Regresa a "En stock" todas las tarjetas "Activa" asignadas a esa sucursal.</p>
                <div>
                    <x-input-label for="retirosuc-sucursal" value="Sucursal" />
                    <select id="retirosuc-sucursal" name="destino_sucursal_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                        <option value="" disabled selected>Selecciona una sucursal</option>
                        @foreach ($sucursales as $sucursalOpcion)
                            <option value="{{ $sucursalOpcion->id }}">{{ $sucursalOpcion->nombre_oficial }} ({{ $sucursalOpcion->clave_financiera }})</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('destino_sucursal_id')" class="mt-1" />
                </div>
                <div>
                    <x-input-label value="Motivo (opcional)" />
                    <textarea name="motivo" rows="2" maxlength="500" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green"></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" x-on:click="$dispatch('close-modal', 'retirar-por-sucursal')" class="text-sm font-medium text-gray-600 hover:text-gray-900">Cancelar</button>
                    <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-red-700">Retirar todas</button>
                </div>
            </form>
        </x-modal>
    @endcan
</x-sies-layout>
