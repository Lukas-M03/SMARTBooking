@props([
    'value' => 0,
    'max' => 5,
    'showValue' => false,
])

@php
    $value = max(0, min((float) $value, (float) $max));
    $percentage = ($value / $max) * 100;
@endphp

<div class="flex items-center space-x-1" aria-label="Rating: {{ number_format($value, 1) }} out of {{ $max }}">
    <div class="relative inline-flex">
        {{-- Empty stars (background) --}}
        <div class="flex text-gray-300">
            @for ($i = 0; $i < $max; $i++)
                <span class="text-2xl leading-none">★</span>
            @endfor
        </div>
        
        {{-- Filled stars (overlay) --}}
        <div class="absolute inset-0 flex overflow-hidden text-yellow-400" style="width: {{ $percentage }}%">
            @for ($i = 0; $i < $max; $i++)
                <span class="text-2xl leading-none">★</span>
            @endfor
        </div>
    </div>

    @if($showValue)
        <span class="ml-1 text-sm text-gray-700">({{ number_format($value, 1) }})</span>
    @endif

</div>