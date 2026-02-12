@props([
    'target'
])

<button type="button" {{ $attributes }} x-data @click="$dispatch('open-modal', '{{ $target }}')">
    {{ $slot }}
</button>