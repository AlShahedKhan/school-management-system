@props([
    'greeting',
    'filterLabel' => 'Filter',
    'filterOptions' => ['Today', 'Last 7 Days', 'This Month', 'This Year', 'Custom'],
    'filterStart' => null,
    'filterEnd' => null,
    'newsLabel' => 'News',
    'newsMessage' => 'No news available',
])

<div
    {{ $attributes->class([
        'mt-3 grid w-full grid-cols-2 items-center gap-3',
        'md:grid-cols-[8rem_minmax(0,1fr)_8rem]',
        'lg:grid-cols-[13rem_minmax(0,1fr)_13rem]',
    ]) }}
>
    <div
        class="col-start-1 row-start-1 inline-flex h-10 min-w-[84px] items-center justify-self-start bg-white px-3 text-xs font-normal leading-none text-slate-500 shadow-sm sm:h-8 sm:w-52 sm:justify-center md:h-10 md:w-full"
    >
        {{ $greeting }}
    </div>

    <div
        class="relative col-start-2 row-start-1 w-full justify-self-end sm:w-52 md:col-start-3 md:w-full"
        data-dashboard-filter
    >
        <button
            type="button"
            class="inline-flex h-10 w-full items-center bg-white px-3 text-xs font-normal leading-none text-slate-500 shadow-sm sm:h-8 sm:w-52 md:h-10 md:w-full"
            aria-expanded="false"
            aria-haspopup="menu"
            data-dashboard-filter-button
        >
            <i class="fas fa-filter w-4 shrink-0 text-left text-[10px] text-slate-400" aria-hidden="true"></i>
            <span
                class="min-w-0 flex-1 truncate whitespace-nowrap px-1 text-center text-xs font-normal leading-none text-slate-500"
                data-dashboard-filter-label
            >{{ $filterLabel }}</span>
            <i
                class="fas fa-chevron-down w-4 shrink-0 text-right text-[7px] text-slate-300 transition-transform duration-150"
                aria-hidden="true"
                data-dashboard-filter-icon
            ></i>
        </button>

        <div
            class="absolute right-0 top-full z-30 mt-1 hidden w-full border border-slate-100 bg-white py-2 shadow-sm sm:w-52 md:w-full lg:w-52"
            role="menu"
            data-dashboard-filter-menu
        >
            @foreach ($filterOptions as $filterOption)
                <button
                    type="button"
                    class="block w-full px-3 py-1.5 text-left !text-xs !font-normal !leading-none text-slate-500 hover:bg-slate-50 hover:text-slate-800"
                    role="menuitem"
                    data-value="{{ $filterOption }}"
                    data-dashboard-filter-option
                >
                    {{ $filterOption }}
                </button>
            @endforeach

            <div
                class="hidden border-t border-slate-100 px-3 pt-3 text-xs font-normal text-slate-500"
                data-dashboard-custom-range
            >
                <label class="dashboard-filter-date-label block min-w-0 overflow-hidden text-xs font-normal text-slate-500">
                    Start date
                    <span
                        class="dashboard-filter-date-wrap relative mt-1 block h-8 w-full min-w-0 overflow-hidden border border-slate-200 bg-white shadow-sm focus-within:border-blue-500"
                    >
                        <span
                            class="flex h-full items-center truncate px-2 pr-6 text-xs text-slate-500"
                            data-dashboard-start-date-display
                        >{{ $filterStart ? $filterStart->format('j-F-Y') : 'Select' }}</span>
                        <input
                            type="date"
                            class="absolute inset-0 z-10 h-full w-full cursor-pointer opacity-0"
                            value="{{ $filterStart ? $filterStart->toDateString() : '' }}"
                            aria-label="Start date"
                            data-dashboard-start-date
                        >
                        <i
                            class="far fa-calendar-alt pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 text-[11px] text-slate-700"
                            aria-hidden="true"
                        ></i>
                    </span>
                </label>

                <label class="dashboard-filter-date-label mt-2 block min-w-0 overflow-hidden text-xs font-normal text-slate-500">
                    End date
                    <span
                        class="dashboard-filter-date-wrap relative mt-1 block h-8 w-full min-w-0 overflow-hidden border border-slate-200 bg-white shadow-sm focus-within:border-blue-500"
                    >
                        <span
                            class="flex h-full items-center truncate px-2 pr-6 text-xs text-slate-500"
                            data-dashboard-end-date-display
                        >{{ $filterEnd ? $filterEnd->format('j-F-Y') : 'Select' }}</span>
                        <input
                            type="date"
                            class="absolute inset-0 z-10 h-full w-full cursor-pointer opacity-0"
                            value="{{ $filterEnd ? $filterEnd->toDateString() : '' }}"
                            aria-label="End date"
                            data-dashboard-end-date
                        >
                        <i
                            class="far fa-calendar-alt pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 text-[11px] text-slate-700"
                            aria-hidden="true"
                        ></i>
                    </span>
                </label>

                <p class="mt-1 hidden text-[9px] text-red-600" data-dashboard-date-error>
                    Select a valid date range.
                </p>
                <button
                    type="button"
                    class="mt-3 h-8 w-full bg-blue-600 text-xs font-semibold text-white hover:bg-blue-700"
                    data-dashboard-apply-range
                >
                    Apply
                </button>
            </div>
        </div>
    </div>

    <div
        class="col-span-2 row-start-2 flex h-10 w-full items-center overflow-hidden border border-blue-100 bg-white shadow-sm md:col-span-1 md:col-start-2 md:row-start-1"
    >
        <div
            class="relative flex h-full w-[70px] shrink-0 items-center justify-center bg-blue-600 pr-1 text-[10px] font-bold uppercase tracking-wide text-white"
        >
            <span class="mr-1 h-2 w-2 rounded-full border-2 border-blue-200 bg-white"></span>
            {{ $newsLabel }}
            <span
                class="absolute -right-3 top-0 h-0 w-0 border-y-[18px] border-l-[12px] border-y-transparent border-l-blue-600"
            ></span>
        </div>

        <p class="dashboard-news-marquee m-0 flex h-full min-w-0 flex-1 items-center overflow-hidden px-4 text-xs text-slate-600">
            <span class="dashboard-news-marquee-track">
                <span class="dashboard-news-marquee-copy">{{ $newsMessage }}</span>
                <span class="dashboard-news-marquee-copy" aria-hidden="true">{{ $newsMessage }}</span>
            </span>
        </p>
    </div>
</div>
