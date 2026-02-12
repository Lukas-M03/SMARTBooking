@props(
    ['href' => '/']
)
@php
    // Extract the path element of the url to match against
    // Trim leading/trailing slashes for consistency
    // If the href is just '/', we want to match the root
    $route = trim(parse_url($href, PHP_URL_PATH) ?? '', '/') ?: '/';

    $nav_link = 'transition-colors duration-300 font-normal text-gray-700 hover:text-blue-800 hover:border-2 hover:border-black hover:rounded-lg hover:no-underline px-3 py-3';
    $nav_link_current = 'font-semibold text-blue-800 border-2 rounded-lg border-blue-900 px-3 py-3';
@endphp

<a href="{{ $href }}" {{ $attributes->class([
    $nav_link, 
    $nav_link_current => request()->is($route.'*')
]) }}>
    {{ $slot }} 
</a>