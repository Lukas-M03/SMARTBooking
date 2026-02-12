@props([
    'for'
])

<label for="{{ $for }}" {{ $attributes->class("block mb-2 text-xs uppercase font-semibold text-gray-900 flex items-center gap-2")->merge(['style' => 'display: flex; align-items: center; margin: 8px 0;']) }}>
    {{ $slot }}
</label>