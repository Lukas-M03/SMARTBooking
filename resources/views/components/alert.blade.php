@php
    // Determine alert type and styling from session
    [$type, $colourClasses] = match(true) {
        session()->has('success') => ['success', 'text-green-700 bg-green-50 border-green-300'],
        session()->has('warning') => ['warning', 'text-yellow-700 bg-yellow-50 border-yellow-300'],
        session()->has('info')    => ['info',    'text-blue-700 bg-blue-50 border-blue-300'],
        session()->has('error')   => ['error',   'text-red-700 bg-red-50 border-red-300'],
        default                   => [null, null],
    };

    // Base alert box styling
    $baseClasses = 'flex items-start gap-3 px-6 py-5 rounded-lg border text-sm font-medium shadow-lg transition-all duration-300 max-w-md fixed right-5 top-15 z-[9999]';
@endphp

@if ($type)
    <div {{ $attributes->class([$baseClasses, $colourClasses]) }}
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 5000)"
        x-show="show"
        x-transition.opacity.duration.300ms
        role="alert"
    >
        <!-- Icon -->
        <x-svg :icon="$type" class="w-5 h-5 shrink-0" />

        <!-- Message content -->
        <div>
            <div class="font-semibold text-black">{{ ucfirst($type) }}</div>
            <div class="text-gray-700">{{ session($type) }}</div>
        </div>

        <!-- Close button -->
        <button
            @click="show = false"
            type="button"
            aria-label="Close"
            class="absolute top-0.3 right-1 px-1 py-0.5 rounded border border-transparent text-black transition-colors duration-200 hover:font-bold hover:cursor-pointer hover:bg-gray-200 hover:border-gray-300 focus:outline-none">
            &times;
        </button>
    </div>
@endif
