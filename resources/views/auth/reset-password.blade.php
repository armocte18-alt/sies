<x-guest-layout>
    <div class="px-6 py-8 sm:px-10">
        <h1 class="mb-6 text-center text-xl font-bold text-white">Restablecer contraseña</h1>

        <form method="POST" action="{{ route('password.store') }}" class="space-y-4" novalidate>
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div>
                <label for="email" class="sr-only">Correo electrónico</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email', $request->email) }}"
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
                <label for="password" class="sr-only">Nueva contraseña</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    placeholder="Nueva contraseña"
                    aria-describedby="password-error"
                    class="block w-full rounded-md border-0 px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm focus:ring-2 focus:ring-brand-gold"
                >
                <x-input-error id="password-error" :messages="$errors->get('password')" class="mt-2 text-amber-200" />
            </div>

            <div>
                <label for="password_confirmation" class="sr-only">Confirmar nueva contraseña</label>
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Confirmar nueva contraseña"
                    aria-describedby="password-confirmation-error"
                    class="block w-full rounded-md border-0 px-4 py-3 text-gray-900 placeholder-gray-400 shadow-sm focus:ring-2 focus:ring-brand-gold"
                >
                <x-input-error id="password-confirmation-error" :messages="$errors->get('password_confirmation')" class="mt-2 text-amber-200" />
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit"
                    class="w-full rounded-md bg-brand-accent px-6 py-3 text-sm font-bold uppercase tracking-wide text-white shadow hover:bg-brand-accent-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-brand-green focus:ring-white sm:w-auto">
                    Restablecer contraseña
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>
