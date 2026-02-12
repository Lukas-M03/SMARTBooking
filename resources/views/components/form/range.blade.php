@props([
    'name',
    'min' => 0,
    'max' => 100,
    'step' => 1,
    'value' => 0,
])

<div class="flex items-start space-x-4"
     x-data="{
        v: Number('{{ $value }}'),
        min: Number('{{ $min }}'),
        max: Number('{{ $max }}'),
     }">

    <!-- Left: slider + ticks as a column -->
    <div class="flex flex-col space-y-1 w-full">
        
        <!-- Slider -->
        <input 
            class="w-full h-4 bg-neutral-100 rounded-full border border-neutral-200 appearance-none cursor-pointer"
            id="{{ $name }}"
            name="{{ $name }}"
            type="range"
            min="{{ $min }}"
            max="{{ $max }}"
            step="{{ $step }}"
            x-model="v"
            {{ $attributes->class(['w-full']) }}
        />

        <!-- Ticks -->
        <div class="relative px-2 w-full flex justify-between text-xs text-neutral-500 select-none">
            @for ($i = $min; $i <= $max; $i++)
                <div class="p-0 m-0 w-0.5 h-2 bg-neutral-400"></div>
            @endfor
        </div>
    </div>

    <!-- Right: value -->
    <span x-text="v" class="text-sm font-medium text-neutral-700"></span>

</div>
