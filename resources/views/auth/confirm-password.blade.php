<x-guest-layout>
    <div class="px-6 py-8 sm:px-10">
        <h1 class="mb-2 text-center text-xl font-bold text-white">Confirma tu contraseña</h1>
        <p class="mb-6 text-center text-sm text-white/80">
            Esta es un área segura de la aplicación. Confirma tu contraseña antes de continuar.
        </p>

        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4" novalidate>
            @csrf

            <div>
                <label for="password" class="sr-only">Contraseña</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autofocus
                    autocomplete="current-password"
                    placeholder="Contraseña"
                    aria-describedby="password-error"
                    class="block w-full rounded-md border-0 px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm focus:ring-2 focus:ring-brand-gold"
                >
                <x-input-error id="password-error" :messages="$errors->get('password')" class="mt-2 text-amber-200" />
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit"
                    class="w-full rounded-md bg-brand-accent px-6 py-3 text-sm font-bold uppercase tracking-wide text-white shadow hover:bg-brand-accent-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-brand-green focus:ring-white sm:w-auto">
                    Confirmar
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
