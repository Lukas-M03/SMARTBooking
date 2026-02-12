@props([
    'message' => 'Are you sure?',
    'variant' => 'btn-danger'
])

<div x-data="{ confirm: false }" class="inline-flex items-center gap-2">

    {{-- Default state --}}
    <button x-show="!confirm" type="button"  @click="confirm=true" {{ $attributes }}>
        {{ $slot }}
    </button>

    {{-- Confirmation state --}}
    <div x-show="confirm" class="flex items-center gap-2" role="alertdialog">
        
        <span class="text-sm text-gray-700">{{ $message }}</span>

        <button type="button" @click="$el.closest('form').submit()" class="btn-sm btn {{ $variant }}">
            Yes
        </button>

        <button type="button" @click="confirm=false" class="btn-sm btn-secondary">
            No
        </button>
    </div>
</div>


