<x-guest-layout>
    <div class="px-6 py-8 sm:px-10">
        <h1 class="mb-2 text-center text-xl font-bold text-white">Recuperar contraseña</h1>
        <p class="mb-6 text-center text-sm text-white/80">
            ¿Olvidaste tu contraseña? No hay problema. Indícanos tu correo electrónico y te enviaremos un enlace para restablecerla.
        </p>

        <x-auth-session-status class="mb-4 rounded-md bg-white/90 px-3 py-2 text-sm" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4" novalidate>
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
                    placeholder="Correo Electrónico"
                    aria-describedby="email-error"
                    class="block w-full rounded-md border-0 px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm focus:ring-2 focus:ring-brand-gold"
                >
                <x-input-error id="email-error" :messages="$errors->get('email')" class="mt-2 text-amber-200" />
            </div>

            <div class="flex flex-col-reverse items-center justify-between gap-4 pt-2 sm:flex-row">
                <a href="{{ route('login') }}"
                    class="rounded text-sm text-sky-200 underline hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-brand-green focus:ring-white">
                    &larr; Volver al inicio de sesión
                </a>

                <button type="submit"
                    class="w-full rounded-md bg-brand-accent px-6 py-3 text-sm font-bold uppercase tracking-wide text-white shadow hover:bg-brand-accent-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-brand-green focus:ring-white sm:w-auto">
                    Enviar enlace
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
