@props(['label', 'value', 'color' => 'bg-brand-green', 'href' => null])

@php
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }}
    @if ($href) href="{{ $href }}" @endif
    {{ $attributes->merge([
        'class' => "relative flex items-center justify-between overflow-hidden rounded-lg p-5 text-white shadow-sm $color".
            ($href ? ' transition hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-green' : ''),
    ]) }}
>
    <div>
        <p class="text-3xl font-bold leading-tight">{{ $value }}</p>
        <p class="mt-1 text-sm text-white/90">{{ $label }}</p>
        @if ($href)
            <span class="mt-3 inline-block text-xs font-semibold underline underline-offset-2">Más info →</span>
        @endif
    </div>
    <div class="opacity-70">
        {{ $slot }}
    </div>
</{{ $tag }}>
