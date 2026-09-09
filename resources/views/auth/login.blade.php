<x-guest-layout>
    <div class="px-6 py-8 sm:px-10">
        <h1 class="mb-6 text-center text-xl font-bold text-white">Iniciar sesión | SIES</h1>

        <x-auth-session-status class="mb-4 rounded-md bg-white/90 px-3 py-2 text-sm" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-4" novalidate>
            @csrf

            <div>
                <label for="email" class="sr-only">Correo electrónico</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="Correo Electrónico"
                    aria-describedby="email-error"
                    class="block w-full rounded-md border-0 px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm focus:ring-2 focus:ring-brand-gold"
                >
                <x-input-error id="email-error" :messages="$errors->get('email')" class="mt-2 text-amber-200" />
            </div>

            <div>
                <label for="password" class="sr-only">Contraseña</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="Contraseña"
                    aria-describedby="password-error"
                    class="block w-full rounded-md border-0 px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm focus:ring-2 focus:ring-brand-gold"
                >
                <x-input-error id="password-error" :messages="$errors->get('password')" class="mt-2 text-amber-200" />
            </div>

            <div class="flex items-center justify-between pt-1">
                <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-white">
                    <input id="remember_me" type="checkbox" name="remember"
                        class="rounded border-gray-300 text-brand-gold focus:ring-brand-gold">
                    Recuérdame
                </label>
            </div>

            <div class="flex flex-col-reverse items-center justify-between gap-4 pt-2 sm:flex-row">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}"
                        class="rounded text-sm text-sky-200 underline hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-brand-green focus:ring-white">
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif

                <button type="submit"
                    class="w-full rounded-md bg-brand-accent px-6 py-3 text-sm font-bold uppercase tracking-wide text-white shadow hover:bg-brand-accent-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-brand-green focus:ring-white sm:w-auto">
                    Iniciar Sesión
                </button>
            </div>
        </form>

        @if (Route::has('register'))
            <p class="mt-6 text-center text-sm text-white/90">
                ¿Aún no tienes una cuenta?
                <a href="{{ route('register') }}" class="font-semibold text-sky-200 underline hover:text-white">Regístrate</a>
            </p>
        @endif
    </div>
</x-guest-layout>
