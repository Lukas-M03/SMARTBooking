@props([
    'target'
])

<button type="button" {{ $attributes }} x-data @click="$dispatch('close-modal', '{{ $target }}')">
    {{ $slot }}
</button>