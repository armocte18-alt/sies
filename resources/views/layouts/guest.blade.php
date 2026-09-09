<!DOCTYPE html>
<html lang="es" class="{{ request()->cookie('tema') === 'oscuro' ? 'dark' : '' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SIES') }}</title>

        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=montserrat:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body
        class="font-sans text-gray-900 antialiased"
        @if (session('status')) data-flash-status="{{ session('status') }}" @endif
    >
        <x-loading-overlay />
        <x-toast-container />

        <div class="relative flex min-h-screen flex-col items-center justify-center gap-8 overflow-hidden bg-gradient-to-br from-brand-green-950 via-brand-green-800 to-brand-green px-4 py-10">
            <img
                src="{{ asset('images/fondo_finabien_circular.jpg') }}"
                alt=""
                aria-hidden="true"
                class="pointer-events-none absolute -right-40 -top-40 h-[32rem] w-[32rem] rounded-full opacity-5 blur-sm sm:h-[40rem] sm:w-[40rem]"
            >
            <img
                src="{{ asset('images/fondo_finabien_circular.jpg') }}"
                alt=""
                aria-hidden="true"
                class="pointer-events-none absolute -bottom-40 -left-40 h-[28rem] w-[28rem] rounded-full opacity-5 blur-sm"
            >

            <img
                src="{{ asset('images/FinabienLogo.png') }}"
                alt="Financiera para el Bienestar"
                class="relative h-28 w-auto sm:h-32"
            >

            <div class="relative w-full max-w-md overflow-hidden rounded-xl border border-white/10 bg-brand-green/70 shadow-xl backdrop-blur-md">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
