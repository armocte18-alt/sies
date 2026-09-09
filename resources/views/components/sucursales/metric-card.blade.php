@props(['label', 'value', 'icon' => 'chart', 'color' => 'text-brand-green'])

<div class="flex items-center gap-3 rounded-lg bg-white p-4 shadow-sm">
    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-md bg-gray-50 {{ $color }}">
        <x-nav-icon :name="$icon" class="h-6 w-6" />
    </span>
    <div class="min-w-0">
        <p class="truncate text-sm text-gray-500">{{ $label }}</p>
        <p class="truncate text-lg font-bold text-gray-900">{{ $value }}</p>
    </div>
</div>
