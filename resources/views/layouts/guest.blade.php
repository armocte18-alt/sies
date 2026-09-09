<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SIOS') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col items-center justify-center gap-8 bg-gray-50 px-4 py-10">
            <div class="flex flex-col items-center gap-1 text-center">
                {{-- Sustituir por el logo oficial en public/images/logo-finabien.png --}}
                <span class="text-2xl font-bold text-brand-green">Financiera <span class="text-brand-gold">para el</span></span>
                <span class="font-serif text-3xl italic text-brand-green">Bienestar</span>
            </div>

            <div class="w-full max-w-md overflow-hidden rounded-xl bg-brand-green shadow-xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
