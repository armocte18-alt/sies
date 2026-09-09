<x-guest-layout>
    <div class="px-6 py-8 sm:px-10">
        <h1 class="mb-6 text-center text-xl font-bold text-white">Crear cuenta | SIES</h1>

        <form method="POST" action="{{ route('register') }}" class="space-y-4" novalidate>
            @csrf

            <div>
                <label for="name" class="sr-only">Nombre completo</label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Nombre completo"
                    aria-describedby="name-error"
                    class="block w-full rounded-md border-0 px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm focus:ring-2 focus:ring-brand-gold"
                >
                <x-input-error id="name-error" :messages="$errors->get('name')" class="mt-2 text-amber-200" />
            </div>

            <div>
                <label for="email" class="sr-only">Correo electrónico</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
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
                    autocomplete="new-password"
                    placeholder="Contraseña"
                    aria-describedby="password-error"
                    class="block w-full rounded-md border-0 px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm focus:ring-2 focus:ring-brand-gold"
                >
                <x-input-error id="password-error" :messages="$errors->get('password')" class="mt-2 text-amber-200" />
            </div>

            <div>
                <label for="password_confirmation" class="sr-only">Confirmar contraseña</label>
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Confirmar contraseña"
                    aria-describedby="password-confirmation-error"
                    class="block w-full rounded-md border-0 px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm focus:ring-2 focus:ring-brand-gold"
                >
                <x-input-error id="password-confirmation-error" :messages="$errors->get('password_confirmation')" class="mt-2 text-amber-200" />
            </div>

            <div class="flex flex-col-reverse items-center justify-between gap-4 pt-2 sm:flex-row">
                <a href="{{ route('login') }}"
                    class="rounded text-sm text-sky-200 underline hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-brand-green focus:ring-white">
                    ¿Ya tienes una cuenta?
                </a>

                <button type="submit"
                    class="w-full rounded-md bg-brand-accent px-6 py-3 text-sm font-bold uppercase tracking-wide text-white shadow hover:bg-brand-accent-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-brand-green focus:ring-white sm:w-auto">
                    Registrarse
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
