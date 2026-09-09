@props(['title' => null])
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ? $title.' · SIES' : 'SIES | GECDMX' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900">
    <a href="#contenido-principal"
        class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-md focus:bg-white focus:px-4 focus:py-2 focus:shadow-lg">
        Saltar al contenido principal
    </a>

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
            <div class="flex items-center gap-2 px-5 py-5 text-lg font-bold">
                <x-nav-icon name="grid" class="h-6 w-6 text-brand-gold" />
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

                <div class="hidden text-sm text-gray-500 lg:block">
                    Sistema de Información, Estadística y Servicios
                </div>

                <div class="relative ms-auto" x-data="{ open: false }" x-on:keydown.escape="open = false">
                    <button type="button" x-on:click="open = !open" :aria-expanded="open.toString()"
                        class="flex items-center gap-2 rounded-md px-2 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-brand-green">
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-brand-green text-xs font-bold text-white">
                            {{ Illuminate\Support\Str::of(auth()->user()->name)->explode(' ')->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->join('') }}
                        </span>
                        <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>

                    <div x-show="open" x-transition x-cloak x-on:click.outside="open = false"
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
                @if (session('status'))
                    <div role="status"
                        class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                        {{ session('status') }}
                    </div>
                @endif

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
