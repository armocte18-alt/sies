@props(['title' => null])
<!DOCTYPE html>
<html lang="es" class="{{ request()->cookie('tema') === 'oscuro' ? 'dark' : '' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ? $title.' · SIES' : 'SIES | GECDMX' }}</title>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon-32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon-16.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=montserrat:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
@php
    // Some flows (Breeze's profile/password controllers) flash a machine
    // sentinel through 'status' instead of a human sentence, meant only for
    // the inline "Saved." indicator next to their own form. Translate the
    // known sentinels for the toast; anything else is already a message.
    $statusSentinels = [
        'profile-updated' => 'Perfil actualizado correctamente.',
        'password-updated' => 'Contraseña actualizada correctamente.',
        'verification-link-sent' => 'Enlace de verificación enviado a tu correo.',
    ];
    $flashStatus = session('status') ? ($statusSentinels[session('status')] ?? session('status')) : null;
@endphp
<body
    class="font-sans antialiased text-gray-900"
    @if ($flashStatus) data-flash-status="{{ $flashStatus }}" @endif
    @if (session('error')) data-flash-error="{{ session('error') }}" @endif
    @if (session('warning')) data-flash-warning="{{ session('warning') }}" @endif
>
    <a href="#contenido-principal"
        class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-md focus:bg-white focus:px-4 focus:py-2 focus:shadow-lg">
        Saltar al contenido principal
    </a>

    <x-loading-overlay />
    <x-toast-container />

    <div x-data="{ sidebarOpen: false }" x-on:keydown.escape.window="sidebarOpen = false" class="min-h-screen bg-gray-100 lg:flex">

        {{-- Overlay móvil --}}
        <div x-show="sidebarOpen" x-transition.opacity x-cloak
            class="fixed inset-0 z-30 bg-gray-900/50 lg:hidden"
            x-on:click="sidebarOpen = false" aria-hidden="true"></div>

        {{-- Sidebar --}}
        <aside
            id="sidebar"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full transform flex-col overflow-y-auto bg-brand-green text-white transition-transform duration-200 ease-in-out lg:static lg:z-auto lg:flex lg:translate-x-0"
            aria-label="Barra lateral"
        >
            <div class="flex items-center gap-3 px-5 py-4 text-lg font-bold">
                <img src="{{ asset('images/favicon-48.png') }}" alt="" class="h-10 w-10 shrink-0" aria-hidden="true">
                <span>SIES | GECDMX</span>
            </div>

            <nav aria-label="Navegación principal" class="flex-1 space-y-6 px-3 pb-8">
                <ul class="space-y-1">
                    @foreach (config('sies.nav_items') as $item)
                        @if (is_null($item['permission']) || auth()->user()->can($item['permission']))
                            @php
                                $group = explode('.', $item['route'])[0];
                                $active = request()->routeIs($item['route']) || request()->routeIs("{$group}.*");
                            @endphp
                            <li>
                                <a href="{{ route($item['route']) }}"
                                    @class([
                                        'flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition',
                                        'bg-white/15 text-white' => $active,
                                        'text-white/85 hover:bg-white/10 hover:text-white' => !$active,
                                    ])
                                    @if ($active) aria-current="page" @endif
                                >
                                    <x-nav-icon :name="$item['icon']" />
                                    {{ $item['label'] }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>

                @foreach (config('sies.nav_sections') as $section)
                    @php
                        $visibleItems = collect($section['items'])->filter(
                            fn ($item) => is_null($item['permission']) || auth()->user()->can($item['permission'])
                        );
                    @endphp

                    @if ($visibleItems->isNotEmpty())
                        <div>
                            <p class="px-3 text-xs font-semibold uppercase tracking-wider text-white/60">
                                {{ $section['title'] }}
                            </p>
                            <ul class="mt-2 space-y-1">
                                @foreach ($visibleItems as $item)
                                    <li>
                                        <a href="{{ route($item['route']) }}"
                                            @class([
                                                'flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition',
                                                'bg-white/15 text-white' => request()->routeIs($item['route']),
                                                'text-white/85 hover:bg-white/10 hover:text-white' => !request()->routeIs($item['route']),
                                            ])
                                            @if (request()->routeIs($item['route'])) aria-current="page" @endif
                                        >
                                            <x-nav-icon :name="$item['icon']" />
                                            {{ $item['label'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                @endforeach
            </nav>
        </aside>

        <div class="flex min-h-screen flex-1 flex-col">
            {{-- Topbar --}}
            <header class="flex items-center justify-between gap-4 border-b border-gray-200 bg-white px-4 py-3 sm:px-6">
                <button type="button" x-on:click="sidebarOpen = !sidebarOpen"
                    class="rounded-md p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand-green lg:hidden"
                    aria-controls="sidebar" :aria-expanded="sidebarOpen.toString()">
                    <span class="sr-only">Abrir menú de navegación</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                    </svg>
                </button>

                <div class="hidden text-sm font-medium text-gray-500 lg:block">
                    {{ $title ?? 'Panel principal' }}
                </div>

                <div class="ms-auto flex items-center gap-1">
                    <button type="button" id="theme-toggle"
                        class="rounded-md p-2 text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-brand-green dark:text-gray-300 dark:hover:bg-gray-700"
                        aria-label="Cambiar a modo oscuro">
                        <svg class="h-5 w-5 dark:hidden" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                        </svg>
                        <svg class="hidden h-5 w-5 dark:block" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                        </svg>
                    </button>
                </div>

                <div
                    class="relative"
                    x-data="{ open: false }"
                    x-on:keydown.escape.window="open = false"
                    x-on:click.outside="open = false"
                >
                    <button type="button" x-on:click.stop="open = !open" :aria-expanded="open.toString()"
                        class="flex items-center gap-2 rounded-md px-2 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-brand-green dark:text-gray-200 dark:hover:bg-gray-700">
                        <img
                            src="{{ auth()->user()->avatar_url }}"
                            alt=""
                            class="h-8 w-8 shrink-0 rounded-full object-cover"
                            aria-hidden="true"
                        >
                        <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>

                    <div x-show="open" x-transition x-cloak
                        class="absolute right-0 z-50 mt-2 w-48 rounded-md border border-gray-100 bg-white py-1 shadow-lg"
                        role="menu">
                        <a href="{{ route('profile.edit') }}" role="menuitem"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Mi perfil</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" role="menuitem"
                                class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50">
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            @isset($header)
                <div class="bg-white px-4 py-4 shadow-sm sm:px-6">
                    {{ $header }}
                </div>
            @endisset

            <main id="contenido-principal" class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                {{ $slot }}
            </main>

            <footer class="border-t border-gray-200 bg-white px-4 py-3 text-center text-xs text-gray-400 sm:px-6">
                SIES &middot; Sistema de Información, Estadística y Servicios &mdash;
                Sistema desarrollado por la Coordinación de Operación GECDMX &middot; V.1.0.0
            </footer>
        </div>
    </div>
</body>
</html>
