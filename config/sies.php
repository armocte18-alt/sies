<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Navegación principal SIES
    |--------------------------------------------------------------------------
    | Cada entrada controla su visibilidad mediante un permiso de
    | spatie/laravel-permission. Los módulos aún no implementados se sirven
    | mediante un controlador de marcador de posición hasta su desarrollo.
    */

    'nav_items' => [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'home', 'permission' => null],
        ['label' => 'Sucursales', 'route' => 'sucursales.index', 'icon' => 'building', 'permission' => 'sucursales.ver'],
        ['label' => 'Directorios', 'route' => 'directorios.index', 'icon' => 'book', 'permission' => 'directorios.ver'],
        ['label' => 'Minutarios', 'route' => 'minutarios.index', 'icon' => 'clipboard', 'permission' => 'minutarios.ver'],
        ['label' => 'Empleados', 'route' => 'empleados.index', 'icon' => 'users', 'permission' => 'empleados.ver'],
        ['label' => 'Circulares', 'route' => 'circulares.index', 'icon' => 'megaphone', 'permission' => 'circulares.ver'],
        ['label' => 'Calendario de Eventos', 'route' => 'calendario.index', 'icon' => 'calendar', 'permission' => 'calendario.ver'],
        ['label' => 'Control de Tarjetas', 'route' => 'tarjetas.index', 'icon' => 'card', 'permission' => 'tarjetas.ver'],
        ['label' => 'Vehículos Oficiales', 'route' => 'vehiculos.index', 'icon' => 'truck', 'permission' => 'vehiculos.ver'],
        ['label' => 'Mantenimientos', 'route' => 'mantenimientos.index', 'icon' => 'wrench', 'permission' => 'mantenimientos.ver'],
    ],

    'nav_sections' => [
        [
            'title' => 'Productividad y Rentabilidad',
            'items' => [
                ['label' => 'Metas y Análisis', 'route' => 'metas.index', 'icon' => 'chart', 'permission' => 'metas.ver'],
            ],
        ],
        [
            'title' => 'Ajustes RR.HH.',
            'items' => [
                ['label' => 'Ajustes Kárdex', 'route' => 'kardex.index', 'icon' => 'clipboard', 'permission' => 'kardex.gestionar'],
            ],
        ],
        [
            'title' => 'Ajustes del Sistema',
            'items' => [
                ['label' => 'Control de Accesos', 'route' => 'accesos.index', 'icon' => 'shield', 'permission' => 'accesos.gestionar'],
            ],
        ],
    ],
];
