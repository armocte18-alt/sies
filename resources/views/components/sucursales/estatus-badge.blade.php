@props(['estatus'])

@php
    $labels = [
        'activa' => 'Activa',
        'inactiva' => 'Inactiva',
        'suspendida' => 'Suspendida',
        'en_apertura' => 'En apertura',
    ];

    $styles = [
        'activa' => 'bg-green-100 text-green-800',
        'inactiva' => 'bg-gray-200 text-gray-700',
        'suspendida' => 'bg-red-100 text-red-800',
        'en_apertura' => 'bg-amber-100 text-amber-800',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide '.($styles[$estatus] ?? 'bg-gray-100 text-gray-700')]) }}>
    {{ $labels[$estatus] ?? $estatus }}
</span>
