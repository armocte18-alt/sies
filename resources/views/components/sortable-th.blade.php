@props(['field', 'label'])

@php
    $current = request('sort');
    $direction = request('direction') === 'desc' ? 'desc' : 'asc';
    $isActive = $current === $field;
    $nextDirection = $isActive && $direction === 'asc' ? 'desc' : 'asc';
    $url = request()->fullUrlWithQuery(['sort' => $field, 'direction' => $nextDirection, 'page' => null]);
@endphp

<th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">
    <a href="{{ $url }}" class="inline-flex items-center gap-1 hover:text-gray-900">
        {{ $label }}
        @if ($isActive)
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-brand-green" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" aria-hidden="true">
                @if ($direction === 'asc')
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                @else
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                @endif
            </svg>
        @else
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
            </svg>
        @endif
        <span class="sr-only">(ordenar{{ $isActive ? ', orden actual: '.($direction === 'asc' ? 'ascendente' : 'descendente') : '' }})</span>
    </a>
</th>
