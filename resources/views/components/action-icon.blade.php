@props(['icon', 'label'])

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1']) }} title="{{ $label }}">
    <x-nav-icon :name="$icon" class="h-4 w-4" />
    <span class="sr-only">{{ $label }}</span>
</span>
