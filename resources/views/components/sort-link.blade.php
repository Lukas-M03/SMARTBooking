@props(['name'])

@php
    // get sort and direction from query string or set default values
    $sort = request()->input('sort') ?? 'id';
    $direction = request()->input('direction') ?? 'asc';

    // generate link url based on current sort and direction
    $nextDirection = ($name === $sort && $direction === 'asc') ? 'desc' : 'asc';
    $url = request()->fullUrlWithQuery([
        'sort' => $name,
        'direction' => $nextDirection,
    ]);
@endphp

<div class="flex items-center">
    <span>{{ $slot }}</span>
    <a href="{{ $url }}">
        @if ($name == $sort && $direction == 'asc')
            <x-svg variant="bars-up"  />
        @elseif ($name == $sort && $direction == 'desc')
            <x-svg variant="bars-down" />
        @else
            <x-svg variant="bars" />
        @endif
    </a>
</div>