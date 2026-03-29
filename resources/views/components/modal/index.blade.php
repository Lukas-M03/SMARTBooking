@props(['name' => null])

<div
    x-data="{ open: false }"    
    x-on:open-modal.window ="if (!@js($name) || $event.detail === @js($name)) open = true"
    x-on:close-modal.window="if (!@js($name) || $event.detail === @js($name)) open = false"
    x-show="open"
    x-transition
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
    x-on:keydown.escape.window="open = false"
>
    <!-- Modal panel -->
    <div  
        {{-- x-on:click.outside="open = false" --}}
        class="relative bg-white rounded-lg shadow-lg p-6 w-full max-w-md"
    >
    
        <!-- Close button -->
        <button type="button" 
                class="absolute top-2 right-2 text-gray-500 hover:text-gray-700" 
                aria-label="Close modal" 
                x-on:click="$dispatch('close-modal', '{{ $name }}')">
            <x-svg icon="x-mark" /> 
        </button>

        <!-- Modal header -->        
        @isset($header)
            <div class="text-lg font-semibold mb-4 border-b border-gray-200 pb-3 ">
                {{ $header }}
            </div>
        @endisset

        <!-- Modal content -->
        <div class="text-gray-700">
            {{ $slot }}
        </div>

        <!-- Modal footer -->
        @isset($footer)
            <div class="mt-4 border-t border-gray-200 pt-4 text-right">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>
