<x-guest-layout>
    <div class="px-6 py-8 sm:px-10">
        <h1 class="mb-2 text-center text-xl font-bold text-white">Verifica tu correo</h1>
        <p class="mb-6 text-center text-sm text-white/80">
            Gracias por registrarte. Antes de continuar, confirma tu correo electrónico dando clic en el enlace que te enviamos. Si no lo recibiste, con gusto te enviamos otro.
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 rounded-md bg-white/90 px-3 py-2 text-sm font-medium text-emerald-700">
                Se envió un nuevo enlace de verificación al correo que registraste.
            </div>
        @endif

        <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit"
                    class="w-full rounded-md bg-brand-accent px-6 py-3 text-sm font-bold uppercase tracking-wide text-white shadow hover:bg-brand-accent-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-brand-green focus:ring-white sm:w-auto">
                    Reenviar correo de verificación
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="rounded text-sm text-sky-200 underline hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-brand-green focus:ring-white">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
