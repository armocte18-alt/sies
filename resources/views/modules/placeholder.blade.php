<x-sios-layout :title="$titulo">
    <h1 class="text-2xl font-bold text-gray-900">{{ $titulo }}</h1>

    <div class="mt-6 flex flex-col items-center justify-center rounded-lg border border-dashed border-gray-300 bg-white px-6 py-16 text-center">
        <x-nav-icon name="wrench" class="h-10 w-10 text-gray-400" />
        <p class="mt-4 text-lg font-medium text-gray-700">Módulo en construcción</p>
        <p class="mt-2 max-w-md text-sm text-gray-500">{{ $descripcion }}</p>
    </div>
</x-sios-layout>
