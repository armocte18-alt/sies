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
        // "slate", no "gray": las clases gray-* se re-mapean en modo oscuro
        // asumiendo texto claro sobre fondo oscuro, lo que deja este badge
        // (fondo claro + texto oscuro) casi ilegible; slate no se toca.
        'inactiva' => 'bg-slate-200 text-slate-700',
        'suspendida' => 'bg-red-100 text-red-800',
        'en_apertura' => 'bg-amber-100 text-amber-800',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide '.($styles[$estatus] ?? 'bg-slate-200 text-slate-700')]) }}>
    {{ $labels[$estatus] ?? $estatus }}
</span>
