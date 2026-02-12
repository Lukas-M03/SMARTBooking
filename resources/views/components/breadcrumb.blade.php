@props([
    'crumbs' => []
])
<div class='font-medium px-3 py-3 w-full text-gray-900' aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1">
        <x-svg icon="home"  />

        @foreach ($crumbs as $name => $link)
            <li class="inline-flex items-center gap-2">               
            <x-svg icon="chevron-right" size="xs" />

            <a href="{{ $link }}" 
                @class(['hover:font-semibold' => !$loop->last])>{{ $name }}</a>
            </li>
        @endforeach
        
    </ol>
</div>