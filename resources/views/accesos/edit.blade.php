@php
    $permisosDeRol = $usuario->getPermissionsViaRoles()->pluck('name')->all();
    $permisosDirectos = $usuario->getDirectPermissions()->pluck('name')->all();
@endphp

<x-sies-layout :title="'Accesos de ' . $usuario->name">
    <a href="{{ route('accesos.index') }}" class="text-sm text-brand-green hover:underline">&larr; Control de Accesos</a>

    <div class="mt-2 flex items-center gap-4">
        <img src="{{ $usuario->avatar_url }}" alt="" class="h-14 w-14 rounded-full object-cover" aria-hidden="true">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $usuario->name }}</h1>
            <p class="text-sm text-gray-500">{{ $usuario->email }} &middot; {{ $usuario->coordinacion?->nombre ?? 'Sin coordinación' }}</p>
        </div>
        <span class="ml-auto inline-flex rounded-full px-3 py-1 text-xs font-medium {{ $usuario->activo ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
            {{ $usuario->activo ? 'Cuenta activa' : 'Cuenta desactivada' }}
        </span>
    </div>

    @if ($esUnoMismo)
        <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
            Por seguridad, no puedes modificar tus propios roles o permisos ni desactivar tu propia cuenta.
            Pide a otro administrador que haga este cambio si es necesario.
        </div>
    @endif

    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Roles --}}
        <section class="rounded-lg bg-white p-5 shadow-sm">
            <h2 class="text-base font-semibold text-gray-800">Roles</h2>
            <p class="mt-1 text-sm text-gray-500">
                Cada rol otorga automáticamente el conjunto de permisos de su coordinación.
                <a href="{{ route('accesos.roles') }}" class="text-brand-green hover:underline">Ver detalle de cada rol</a>.
            </p>

            <form method="POST" action="{{ route('accesos.roles.update', $usuario) }}" class="mt-4 space-y-2">
                @csrf
                @method('PUT')

                @foreach ($roles as $rol)
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="roles[]" value="{{ $rol->name }}"
                            @checked($usuario->hasRole($rol->name))
                            @disabled($esUnoMismo)
                            class="rounded border-gray-300 text-brand-green focus:ring-brand-green">
                        {{ ucfirst($rol->name) }}
                    </label>
                @endforeach

                @unless ($esUnoMismo)
                    <button type="submit" class="mt-3 rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                        Guardar roles
                    </button>
                @endunless
            </form>
        </section>

        {{-- Permisos especiales --}}
        <section class="rounded-lg bg-white p-5 shadow-sm">
            <h2 class="text-base font-semibold text-gray-800">Permisos especiales</h2>
            <p class="mt-1 text-sm text-gray-500">
                Permisos adicionales solo para esta persona, más allá de lo que ya le da su rol.
                Los que ya vienen incluidos por rol se muestran marcados y no se pueden quitar aquí.
            </p>

            <form method="POST" action="{{ route('accesos.permisos.update', $usuario) }}" class="mt-4 space-y-4">
                @csrf
                @method('PUT')

                @foreach ($permisosPorModulo as $modulo => $permisos)
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">{{ $modulo }}</p>
                        <div class="mt-1 grid grid-cols-1 gap-1 sm:grid-cols-2">
                            @foreach ($permisos as $permiso)
                                @php $incluidoPorRol = in_array($permiso->name, $permisosDeRol, true); @endphp
                                <label class="flex items-center gap-2 text-sm {{ $incluidoPorRol ? 'text-gray-400' : 'text-gray-700' }}">
                                    <input type="checkbox" name="permisos[]" value="{{ $permiso->name }}"
                                        @checked($incluidoPorRol || in_array($permiso->name, $permisosDirectos, true))
                                        @disabled($esUnoMismo || $incluidoPorRol)
                                        class="rounded border-gray-300 text-brand-green focus:ring-brand-green">
                                    <span class="font-mono text-xs">{{ $permiso->name }}</span>
                                    @if ($incluidoPorRol)
                                        <span class="text-xs">(por rol)</span>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                @unless ($esUnoMismo)
                    <button type="submit" class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                        Guardar permisos especiales
                    </button>
                @endunless
            </form>
        </section>
    </div>
</x-sies-layout>
