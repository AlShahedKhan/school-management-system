@if ($paginator->hasPages())
<style>
    @media (max-width: 320px) {
        nav[aria-label="Pagination Navigation"] .pagination-nearby-page {
            display: none;
        }
    }
</style>

<nav role="navigation" aria-label="Pagination Navigation" class="w-full">
    <div class="flex w-full items-center justify-between gap-2">

        <div class="flex h-8 shrink-0 items-center">
            <p class="m-0 inline-flex h-full items-center gap-1 border border-gray-200 bg-white px-3 text-[10px] leading-none text-slate-600 shadow-sm">
                <span>{{ $paginator->firstItem() }}</span>
                <span>{!! __('OF') !!}</span>
                <span class="font-small">{{ $paginator->total() }}</span>
            </p>
        </div>

        <div class="shrink-0">
            <span class="inline-flex h-8 items-center gap-1 overflow-visible rounded-none border-0 bg-transparent shadow-none">

                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="Previous">
                    <span class="inline-flex h-8 w-8 min-w-0 items-center justify-center border border-transparent p-0 text-[10px] font-medium leading-5 text-gray-400 cursor-not-allowed dark:border-gray-600 dark:bg-gray-700 dark:text-gray-400" aria-hidden="true">
                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </span>
                @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="!no-underline inline-flex h-8 w-8 min-w-0 items-center justify-center border border-transparent p-0 text-[10px] font-medium leading-5 text-slate-600 transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 dark:bg-gray-800 dark:border-gray-600 dark:active:bg-gray-700 dark:focus:border-blue-800 dark:text-gray-300 dark:hover:bg-gray-900 dark:hover:text-gray-300" aria-label="Previous">
                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </a>
                @endif

                <span aria-current="page">
                    <span class="inline-flex h-8 w-8 min-w-0 items-center justify-center border border-blue-600 bg-blue-600 p-0 text-[10px] font-semibold leading-5 text-white cursor-default dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                        {{ $paginator->currentPage() }}
                    </span>
                </span>

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="!no-underline inline-flex h-8 w-8 min-w-0 items-center justify-center border border-transparent p-0 text-[10px] font-medium leading-5 text-slate-600 transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 dark:bg-gray-800 dark:border-gray-600 dark:active:bg-gray-700 dark:focus:border-blue-800 dark:text-gray-300 dark:hover:bg-gray-900 dark:hover:text-gray-300" aria-label="Next">
                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </a>
                @else
                <span aria-disabled="true" aria-label="Next">
                    <span class="inline-flex h-8 w-8 min-w-0 items-center justify-center border border-transparent p-0 text-[10px] font-medium leading-5 text-gray-400 cursor-not-allowed dark:border-gray-600 dark:bg-gray-700 dark:text-gray-400" aria-hidden="true">
                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </span>
                @endif
            </span>
        </div>
    </div>
</nav>
@endif
