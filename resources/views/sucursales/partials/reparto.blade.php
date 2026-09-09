@php
    $puedeGestionarReparto = auth()->user()->can('updateReparto', $sucursal);
@endphp

<div class="space-y-6">
    {{-- Motocicletas --}}
    <section class="rounded-lg bg-white p-4 shadow-sm" aria-labelledby="motos-heading">
        <div class="mb-3 flex items-center justify-between">
            <h2 id="motos-heading" class="flex items-center gap-2 text-base font-semibold text-gray-800">
                <x-nav-icon name="truck" class="h-5 w-5 text-brand-green" />
                Motocicletas de reparto
            </h2>
            @if ($puedeGestionarReparto)
                <button type="button" x-on:click="$dispatch('open-modal', 'agregar-motocicleta')"
                    class="text-xs font-semibold text-brand-green hover:underline">
                    + Agregar motocicleta
                </button>
            @endif
        </div>

        @if ($sucursal->motocicletas->isEmpty())
            <p class="text-sm text-gray-500">Sin motocicletas registradas.</p>
        @else
            <div class="space-y-4">
                @foreach ($sucursal->motocicletas as $moto)
                    <div class="rounded-md border border-gray-100 p-3">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <div>
                                <p class="font-medium text-gray-900">
                                    {{ $moto->placa ?: 'Sin placa' }} — {{ trim(($moto->marca ?? '').' '.($moto->modelo ?? '')) ?: 'Sin marca/modelo' }}
                                    @if ($moto->anio) ({{ $moto->anio }}) @endif
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ number_format($moto->kilometraje_actual) }} km ·
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium
                                        {{ match($moto->estado) {
                                            'operativa' => 'bg-emerald-100 text-emerald-800',
                                            'mantenimiento' => 'bg-amber-100 text-amber-800',
                                            default => 'bg-gray-100 text-gray-600',
                                        } }}">
                                        {{ ['operativa' => 'Operativa', 'mantenimiento' => 'Mantenimiento', 'baja' => 'Baja'][$moto->estado] ?? $moto->estado }}
                                    </span>
                                </p>
                            </div>
                            @if ($puedeGestionarReparto)
                                <div class="flex items-center gap-3 text-xs">
                                    <button type="button" x-on:click="$dispatch('open-modal', 'agregar-combustible-{{ $moto->id }}')"
                                        class="font-semibold text-brand-green hover:underline">
                                        + Carga de combustible
                                    </button>
                                    <form method="POST" action="{{ route('sucursales.motocicletas.destroy', [$sucursal, $moto]) }}"
                                        onsubmit="return confirm('¿Eliminar esta motocicleta?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-semibold text-red-600 hover:underline">Eliminar</button>
                                    </form>
                                </div>
                            @endif
                        </div>

                        @if ($moto->cargasCombustible->isNotEmpty())
                            <ul class="mt-2 space-y-1 border-t border-gray-50 pt-2 text-xs text-gray-500">
                                @foreach ($moto->cargasCombustible->take(3) as $carga)
                                    <li>
                                        {{ $carga->fecha->format('d/m/Y') }} —
                                        @if($carga->litros) {{ $carga->litros }} L @endif
                                        @if($carga->monto) (${{ number_format($carga->monto, 2) }}) @endif
                                        @if($carga->kilometraje) · {{ number_format($carga->kilometraje) }} km @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    @if ($puedeGestionarReparto)
                        <x-modal name="agregar-combustible-{{ $moto->id }}" focusable>
                            <form method="POST" action="{{ route('sucursales.motocicletas.combustible.store', [$sucursal, $moto]) }}" class="p-6 space-y-4">
                                @csrf
                                <h2 class="text-lg font-medium text-gray-900">Registrar carga de combustible</h2>
                                <p class="text-sm text-gray-500">{{ $moto->placa ?: 'Motocicleta sin placa' }}</p>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label value="Fecha" />
                                        <input type="date" name="fecha" required value="{{ now()->format('Y-m-d') }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                                    </div>
                                    <div>
                                        <x-input-label value="Kilometraje actual" />
                                        <x-text-input name="kilometraje" type="number" min="0" class="mt-1 block w-full" value="{{ $moto->kilometraje_actual }}" />
                                    </div>
                                    <div>
                                        <x-input-label value="Litros" />
                                        <x-text-input name="litros" type="number" step="0.01" min="0" class="mt-1 block w-full" />
                                    </div>
                                    <div>
                                        <x-input-label value="Monto" />
                                        <x-text-input name="monto" type="number" step="0.01" min="0" class="mt-1 block w-full" />
                                    </div>
                                </div>
                                <div>
                                    <x-input-label value="Notas (opcional)" />
                                    <x-text-input name="notas" type="text" class="mt-1 block w-full" maxlength="500" />
                                </div>

                                <div class="flex justify-end gap-3">
                                    <button type="button" x-on:click="$dispatch('close-modal', 'agregar-combustible-{{ $moto->id }}')" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                                        Cancelar
                                    </button>
                                    <button type="submit" class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark">
                                        Registrar
                                    </button>
                                </div>
                            </form>
                        </x-modal>
                    @endif
                @endforeach
            </div>
        @endif
    </section>

    {{-- Equipamiento de reparto (EPP) --}}
    <section class="rounded-lg bg-white p-4 shadow-sm" aria-labelledby="epp-heading">
        <div class="mb-3 flex items-center justify-between">
            <h2 id="epp-heading" class="flex items-center gap-2 text-base font-semibold text-gray-800">
                <x-nav-icon name="wrench" class="h-5 w-5 text-brand-green" />
                Equipamiento de mensajería (EPP)
            </h2>
            @if ($puedeGestionarReparto)
                <button type="button" x-on:click="$dispatch('open-modal', 'agregar-epp')"
                    class="text-xs font-semibold text-brand-green hover:underline">
                    + Agregar equipo
                </button>
            @endif
        </div>

        @if ($sucursal->equipamientoReparto->isEmpty())
            <p class="text-sm text-gray-500">Sin equipamiento de mensajería registrado.</p>
        @else
            <div class="overflow-x-auto rounded-lg border border-gray-100">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <caption class="sr-only">Inventario de equipo de protección personal para reparto</caption>
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-3 py-2 text-left font-semibold text-gray-600">Tipo</th>
                            <th scope="col" class="px-3 py-2 text-left font-semibold text-gray-600">Talla</th>
                            <th scope="col" class="px-3 py-2 text-right font-semibold text-gray-600">Total</th>
                            <th scope="col" class="px-3 py-2 text-right font-semibold text-gray-600">Buen estado</th>
                            <th scope="col" class="px-3 py-2 text-right font-semibold text-gray-600">Dañado</th>
                            <th scope="col" class="px-3 py-2"><span class="sr-only">Acciones</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($sucursal->equipamientoReparto as $epp)
                            <tr>
                                <td class="px-3 py-2 capitalize text-gray-800">{{ $epp->tipo }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ $epp->talla ?? '—' }}</td>
                                <td class="px-3 py-2 text-right text-gray-800">{{ $epp->cantidad_total }}</td>
                                <td class="px-3 py-2 text-right text-emerald-700">{{ $epp->cantidad_buen_estado }}</td>
                                <td class="px-3 py-2 text-right text-red-600">{{ $epp->cantidad_danado }}</td>
                                <td class="px-3 py-2 text-right">
                                    @if ($puedeGestionarReparto)
                                        <form method="POST" action="{{ route('sucursales.equipamiento-reparto.destroy', [$sucursal, $epp]) }}"
                                            onsubmit="return confirm('¿Eliminar este registro de equipamiento?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-red-600 hover:underline">Eliminar</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>

@if ($puedeGestionarReparto)
    <x-modal name="agregar-motocicleta" focusable>
        <form method="POST" action="{{ route('sucursales.motocicletas.store', $sucursal) }}" class="p-6 space-y-4">
            @csrf
            <h2 class="text-lg font-medium text-gray-900">Agregar motocicleta</h2>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label value="Placa (opcional)" />
                    <x-text-input name="placa" type="text" class="mt-1 block w-full" maxlength="20" />
                </div>
                <div>
                    <x-input-label value="Estado" />
                    <select name="estado" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                        <option value="operativa">Operativa</option>
                        <option value="mantenimiento">En mantenimiento</option>
                        <option value="baja">Baja</option>
                    </select>
                </div>
                <div>
                    <x-input-label value="Marca (opcional)" />
                    <x-text-input name="marca" type="text" class="mt-1 block w-full" maxlength="60" />
                </div>
                <div>
                    <x-input-label value="Modelo (opcional)" />
                    <x-text-input name="modelo" type="text" class="mt-1 block w-full" maxlength="60" />
                </div>
                <div>
                    <x-input-label value="Año (opcional)" />
                    <x-text-input name="anio" type="number" min="1980" :max="date('Y') + 1" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label value="Kilometraje actual" />
                    <x-text-input name="kilometraje_actual" type="number" min="0" class="mt-1 block w-full" value="0" required />
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close-modal', 'agregar-motocicleta')" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                    Cancelar
                </button>
                <button type="submit" class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark">
                    Agregar
                </button>
            </div>
        </form>
    </x-modal>

    <x-modal name="agregar-epp" focusable>
        <form method="POST" action="{{ route('sucursales.equipamiento-reparto.store', $sucursal) }}" class="p-6 space-y-4">
            @csrf
            <h2 class="text-lg font-medium text-gray-900">Agregar equipo de mensajería</h2>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label value="Tipo" />
                    <x-text-input name="tipo" type="text" class="mt-1 block w-full" required maxlength="60" placeholder="Casco, guantes, botas, chamarra..." />
                    <x-input-error :messages="$errors->get('tipo')" class="mt-1" />
                </div>
                <div>
                    <x-input-label value="Talla (opcional)" />
                    <x-text-input name="talla" type="text" class="mt-1 block w-full" maxlength="20" />
                </div>
                <div>
                    <x-input-label value="Cantidad total" />
                    <x-text-input name="cantidad_total" type="number" min="0" class="mt-1 block w-full" value="0" required />
                </div>
                <div>
                    <x-input-label value="En buen estado" />
                    <x-text-input name="cantidad_buen_estado" type="number" min="0" class="mt-1 block w-full" value="0" required />
                </div>
                <div>
                    <x-input-label value="Dañado" />
                    <x-text-input name="cantidad_danado" type="number" min="0" class="mt-1 block w-full" value="0" required />
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" x-on:click="$dispatch('close-modal', 'agregar-epp')" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                    Cancelar
                </button>
                <button type="submit" class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark">
                    Agregar
                </button>
            </div>
        </form>
    </x-modal>
@endif
