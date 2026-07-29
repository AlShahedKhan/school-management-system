@props([
    'title',
    'breadcrumbParent' => 'School',
    'breadcrumbCurrent' => null,
    'titleId' => 'pageHeader',
    'breadcrumbCurrentId' => 'pageTitle',
    'actionsClass' => 'grid w-full grid-cols-3 gap-2 lg:flex lg:w-auto',
    'keepTitle' => false,
])

@php
    $currentBreadcrumb = $breadcrumbCurrent ?? $title;
@endphp

<section
    {{ $attributes->class('mb-2 border border-gray-200 bg-white p-2.5 sm:p-4')->merge([
        'style' => 'border-radius: 0;',
    ]) }}
>
    <div class="mb-4 flex items-start justify-between">
        <div>
            <h3
                id="{{ $titleId }}"
                @if ($keepTitle) data-keep-header="true" @endif
                class="text-[15px] font-normal leading-tight text-gray-800 sm:text-xl"
            >
                {{ $title }}
            </h3>

            @isset($breadcrumb)
                {{ $breadcrumb }}
            @else
                <div class="mt-1 flex items-center text-[12px] text-slate-400">
                    <span>{{ $breadcrumbParent }}</span>
                    <i class="fas fa-chevron-right mx-1.5 text-[10px]" aria-hidden="true"></i>
                    <span id="{{ $breadcrumbCurrentId }}" class="text-slate-500">
                        {{ $currentBreadcrumb }}
                    </span>
                </div>
            @endisset
        </div>
    </div>

    @if (isset($search) || isset($actions))
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            @isset($search)
                <div class="hidden items-center gap-2 lg:flex">
                    {{ $search }}
                </div>
            @endisset

            @isset($actions)
                <div class="{{ $actionsClass }} lg:ml-auto">
                    {{ $actions }}
                </div>
            @endisset
        </div>
    @endif

    @isset($mobileSearch)
        <div class="mt-3 grid grid-cols-3 gap-2 lg:hidden">
            {{ $mobileSearch }}
        </div>
    @endisset
</section>
