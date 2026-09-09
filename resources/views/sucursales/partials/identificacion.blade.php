<section class="rounded-lg bg-white p-4 shadow-sm" aria-labelledby="identificacion-heading">
    <h2 id="identificacion-heading" class="mb-3 flex items-center gap-2 text-base font-semibold text-gray-800">
        <x-nav-icon name="clipboard" class="h-5 w-5 text-brand-green" />
        Cédula de identificación
    </h2>

    @can('updateIdentificacion', $sucursal)
        <form method="POST" action="{{ route('sucursales.identificacion.update', $sucursal) }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <x-input-label for="nombre_oficial" value="Nombre oficial" />
                <x-text-input id="nombre_oficial" name="nombre_oficial" type="text" class="mt-1 block w-full"
                    :value="old('nombre_oficial', $sucursal->nombre_oficial)" required />
                <x-input-error :messages="$errors->get('nombre_oficial')" class="mt-2" />
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <x-input-label for="clave_financiera" value="Clave financiera" />
                    <x-text-input id="clave_financiera" name="clave_financiera" type="text" class="mt-1 block w-full"
                        :value="old('clave_financiera', $sucursal->clave_financiera)" required />
                    <x-input-error :messages="$errors->get('clave_financiera')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="centro_distribucion" value="Centro de distribución" />
                    <x-text-input id="centro_distribucion" name="centro_distribucion" type="text" class="mt-1 block w-full"
                        :value="old('centro_distribucion', $sucursal->centro_distribucion)" />
                    <x-input-error :messages="$errors->get('centro_distribucion')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label for="estatus_operativo" value="Estatus operativo" />
                <select id="estatus_operativo" name="estatus_operativo" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                    @foreach (['en_apertura' => 'En apertura', 'activa' => 'Activa', 'inactiva' => 'Inactiva', 'suspendida' => 'Suspendida'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('estatus_operativo', $sucursal->estatus_operativo) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('estatus_operativo')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="titular_empleado_id" value="Titular / Jefe de sucursal" />
                <select id="titular_empleado_id" name="titular_empleado_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-brand-green focus:ring-brand-green">
                    <option value="">Sin asignar</option>
                    @foreach ($sucursal->empleados as $empleado)
                        <option value="{{ $empleado->id }}" @selected((int) old('titular_empleado_id', $sucursal->titular_empleado_id) === $empleado->id)>
                            {{ $empleado->nombre_completo }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('titular_empleado_id')" class="mt-2" />
            </div>

            <button type="submit"
                class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                Guardar cédula
            </button>
        </form>
    @else
        <dl class="grid grid-cols-1 gap-x-4 gap-y-3 text-sm sm:grid-cols-2">
            <div>
                <dt class="text-gray-500">Nombre oficial</dt>
                <dd class="font-medium text-gray-900">{{ $sucursal->nombre_oficial }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Clave financiera</dt>
                <dd class="font-medium text-gray-900">{{ $sucursal->clave_financiera }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Estatus operativo</dt>
                <dd><x-sucursales.estatus-badge :estatus="$sucursal->estatus_operativo" /></dd>
            </div>
            <div>
                <dt class="text-gray-500">Titular / Jefe</dt>
                <dd class="font-medium text-gray-900">{{ $sucursal->titular?->nombre_completo ?? 'Sin asignar' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Centro de distribución</dt>
                <dd class="font-medium text-gray-900">{{ $sucursal->centro_distribucion ?? '—' }}</dd>
            </div>
        </dl>
    @endcan
</section>
