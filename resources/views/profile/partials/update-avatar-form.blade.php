<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">Foto de perfil</h2>
        <p class="mt-1 text-sm text-gray-600">
            Formatos JPG, PNG o WEBP. Tamaño máximo 2&nbsp;MB.
        </p>
    </header>

    <div class="mt-6 flex items-center gap-6">
        <img
            src="{{ $user->avatar_url }}"
            alt="Foto de perfil de {{ $user->name }}"
            class="h-20 w-20 rounded-full border border-gray-200 object-cover"
        >

        <div class="flex flex-col gap-3">
            <form
                method="POST"
                action="{{ route('profile.avatar.update') }}"
                enctype="multipart/form-data"
                class="flex flex-wrap items-center gap-3"
            >
                @csrf

                <label for="avatar" class="sr-only">Elegir nueva foto de perfil</label>
                <input
                    id="avatar"
                    name="avatar"
                    type="file"
                    accept="image/png,image/jpeg,image/webp"
                    required
                    class="block text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-brand-green file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-brand-green-dark"
                >

                <button type="submit" class="rounded-md bg-brand-green px-4 py-2 text-sm font-semibold text-white shadow hover:bg-brand-green-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green">
                    Subir foto
                </button>
            </form>

            @if ($user->avatar_path)
                <form method="POST" action="{{ route('profile.avatar.destroy') }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm font-medium text-red-600 hover:underline">
                        Quitar foto y usar el avatar por defecto
                    </button>
                </form>
            @endif

            <x-input-error :messages="$errors->get('avatar')" />
        </div>
    </div>
</section>
