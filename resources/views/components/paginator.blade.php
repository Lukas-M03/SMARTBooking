@props([
    'items', 
    'size' => 10, 
    'options' => ['5' => 5,'10' => 10, '25' => 25, '50' => 50, '100' => 100, '500' => 500],
    'variant' => 'default'
])

@php
    $size = request()->query('size', $size);
    $current = $items->currentPage();
    $last = $items->lastPage();
    $window = 2; // Number of pages on each side of current

    [$bg, $darkBg] = match($variant) {
        'green' => ['bg-green-500', 'dark:bg-green-600'],
        'red' => ['bg-red-500', 'dark:bg-red-600'],
        'dark' => ['bg-gray-500', 'dark:bg-gray-600'],
        'purple' => ['bg-purple-500', 'dark:bg-purple-600'],
        'light' => ['bg-gray-300', 'dark:bg-gray-600'],
        default => ['bg-blue-500', 'dark:bg-blue-600'],
    };
   
@endphp

@if ($items instanceof \Illuminate\Pagination\AbstractPaginator && $items->hasPages())
    <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-2 p-4 text-sm">

        {{-- Pagination --}}
        <nav role="navigation" aria-label="Pagination Navigation">
            <ul class="inline-flex items-center justify-center gap-1 flex-wrap">
                {{-- Previous --}}
                <li>
                    @if ($items->onFirstPage())
                        <span class="px-3 py-2 rounded bg-gray-200 dark:bg-gray-700 text-gray-500 cursor-not-allowed">
                            &laquo;
                        </span>
                    @else
                        <a href="{{ $items->previousPageUrl() }}" class="px-3 py-2 rounded bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                            &laquo;
                        </a>
                    @endif
                </li>

                {{-- Page Links (Desktop only) --}}
                <div class="hidden sm:flex items-center gap-1">
                    {{-- First page and leading ellipsis --}}
                    @if ($current > $window + 1)
                        <li>
                            <a href="{{ $items->url(1) }}" class="px-3 py-2 rounded bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">1</a>
                        </li>
                        @if ($current > $window + 2)
                            <li><span class="px-2 text-gray-400">…</span></li>
                        @endif
                    @endif

                    {{-- Page Window --}}
                    @for ($i = max(1, $current - $window); $i <= min($last, $current + $window); $i++)
                        @if ($i === $current)
                            <li>
                                <span @class(["px-3 py-2 rounded text-white font-semibold",$bg,$darkBg])>{{ $i }}</span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $items->url($i) }}" class="px-3 py-2 rounded bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">{{ $i }}</a>
                            </li>
                        @endif
                    @endfor

                    {{-- Trailing ellipsis and last page --}}
                    @if ($current < $last - $window)
                        @if ($current < $last - $window - 1)
                            <li><span class="px-2 text-gray-400">…</span></li>
                        @endif
                        <li>
                            <a href="{{ $items->url($last) }}" class="px-3 py-2 rounded bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">{{ $last }}</a>
                        </li>
                    @endif
                </div>

                {{-- Next --}}
                <li>
                    @if ($items->hasMorePages())
                        <a href="{{ $items->nextPageUrl() }}" class="px-3 py-2 rounded bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300">
                            &raquo;
                        </a>
                    @else
                        <span class="px-3 py-2 rounded bg-gray-200 dark:bg-gray-700 text-gray-500 cursor-not-allowed">
                            &raquo;
                        </span>
                    @endif
                </li>
            </ul>
        </nav>

        <div class="text-gray-600 dark:text-gray-300 text-sm">
            Page {{ $current }} of {{ $last }}
        </div>

        {{-- Page size form --}}
        <form method="get" action="{{ request()->url() }}" class="flex items-center gap-2">
            @foreach(request()->except(['size', 'page']) as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
            <x-form.select placeholder="" name="size" :options="$options" value="{{ $size }}" onchange="this.form.submit()"/> 
        </form>
    </div>
@endif