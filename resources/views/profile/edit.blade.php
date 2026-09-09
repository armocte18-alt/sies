<x-sies-layout :title="'Mi perfil'">
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Mi perfil
        </h2>
    </x-slot>

    <div class="mx-auto max-w-3xl space-y-6">
        <div class="rounded-lg bg-white p-4 shadow sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="rounded-lg bg-white p-4 shadow sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="rounded-lg bg-white p-4 shadow sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-sies-layout>
