@props([
    'empty' => false,
    'emptyColspan' => 100,
    'emptyMessage' => 'No data found.',
    'frameClass' => 'school-data-table-frame border border-gray-200 bg-white p-2.5 shadow-md sm:p-4',
    'minWidth' => '1068px',
    'showFooter' => false,
])

@once
    <style>
        .school-data-table {
            width: 100%;
            min-width: var(--school-data-table-min-width, 1068px);
            table-layout: fixed;
        }

        .school-data-table-frame {
            width: 100%;
            overflow: hidden;
            border: 1px solid #d1d5db;
            background: #ffffff;
        }

        .school-data-table-scroll {
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            background: #ffffff;
            -webkit-overflow-scrolling: touch;
        }

        .school-data-table-cell-scroll {
            display: block;
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            white-space: nowrap;
            -ms-overflow-style: none;
            scrollbar-width: none;
            -webkit-overflow-scrolling: touch;
        }

        .school-data-table-cell-scroll::-webkit-scrollbar {
            display: none;
        }

        .school-data-table-cell-scroll.is-scrollable {
            cursor: grab;
        }

        .school-data-table-cell-scroll.is-dragging {
            cursor: grabbing;
            user-select: none;
        }

        @media (min-width: 1024px) {
            .school-data-table {
                width: 100%;
                min-width: 0;
            }

            .school-data-table-scroll {
                overflow-x: hidden;
            }
        }

        @media (max-width: 1023px) {
            .school-data-table {
                width: var(--school-data-table-min-width, 1068px);
                min-width: var(--school-data-table-min-width, 1068px);
            }
        }
    </style>
@endonce

<x-table
    unstyled
    :empty="$empty"
    :empty-colspan="$emptyColspan"
    :empty-message="$emptyMessage"
    empty-cell-class="border border-gray-300 px-3 py-10 text-center text-gray-500"
    :show-footer="$showFooter"
    footer-class="w-full border-t border-gray-300 px-0 py-3"
    scroll-class="school-data-table-scroll"
    table-class="school-data-table border-collapse border border-gray-300 text-xs"
    head-class="bg-gray-100"
    tbody-class=""
    {{ $attributes->class($frameClass) }}
    style="--school-data-table-min-width: {{ $minWidth }}; border-radius:0;"
>
    @isset($columns)
        <x-slot:columns>
            {{ $columns }}
        </x-slot:columns>
    @endisset

    @isset($head)
        <x-slot:head>
            {{ $head }}
        </x-slot:head>
    @endisset

    {{ $slot }}

    @isset($footer)
        <x-slot:footer>
            {{ $footer }}
        </x-slot:footer>
    @endisset
</x-table>

@once
    <script>
        (() => {
            const scrollSelector = '.school-data-table-cell-scroll';

            function updateScrollableCells(table) {
                table.querySelectorAll(scrollSelector).forEach((cell) => {
                    const canScroll = cell.scrollWidth > cell.clientWidth + 1;
                    cell.classList.toggle('is-scrollable', canScroll);
                    cell.setAttribute('tabindex', canScroll ? '0' : '-1');
                });
            }

            function enableCellDragScroll(cell) {
                if (cell.dataset.dragScrollReady === 'true') {
                    return;
                }

                cell.dataset.dragScrollReady = 'true';

                let startX = 0;
                let startScrollLeft = 0;
                let isDragging = false;
                let hasMoved = false;

                cell.addEventListener('pointerdown', (event) => {
                    if (!cell.classList.contains('is-scrollable')) {
                        return;
                    }

                    if (event.button !== undefined && event.button !== 0) {
                        return;
                    }

                    isDragging = true;
                    hasMoved = false;
                    startX = event.clientX;
                    startScrollLeft = cell.scrollLeft;
                    cell.classList.add('is-dragging');
                    cell.setPointerCapture(event.pointerId);
                });

                cell.addEventListener('pointermove', (event) => {
                    if (!isDragging) {
                        return;
                    }

                    const deltaX = event.clientX - startX;

                    if (Math.abs(deltaX) > 2) {
                        hasMoved = true;
                        event.preventDefault();
                    }

                    cell.scrollLeft = startScrollLeft - deltaX;
                });

                function stopDragging(event) {
                    if (!isDragging) {
                        return;
                    }

                    isDragging = false;
                    cell.classList.remove('is-dragging');

                    if (cell.hasPointerCapture(event.pointerId)) {
                        cell.releasePointerCapture(event.pointerId);
                    }
                }

                cell.addEventListener('pointerup', stopDragging);
                cell.addEventListener('pointercancel', stopDragging);

                cell.addEventListener('click', (event) => {
                    if (!hasMoved) {
                        return;
                    }

                    event.preventDefault();
                    event.stopPropagation();
                    hasMoved = false;
                });

                cell.addEventListener('keydown', (event) => {
                    if (!cell.classList.contains('is-scrollable')) {
                        return;
                    }

                    const scrollAmount = 48;

                    if (event.key === 'ArrowRight') {
                        cell.scrollLeft += scrollAmount;
                        event.preventDefault();
                    }

                    if (event.key === 'ArrowLeft') {
                        cell.scrollLeft -= scrollAmount;
                        event.preventDefault();
                    }

                    if (event.key === 'Home') {
                        cell.scrollLeft = 0;
                        event.preventDefault();
                    }

                    if (event.key === 'End') {
                        cell.scrollLeft = cell.scrollWidth;
                        event.preventDefault();
                    }
                });
            }

            function initializeSchoolDataTables() {
                document.querySelectorAll('.school-data-table').forEach((table) => {
                    table.querySelectorAll(scrollSelector).forEach(enableCellDragScroll);
                    updateScrollableCells(table);

                    if (table.dataset.scrollObserverReady === 'true') {
                        return;
                    }

                    table.dataset.scrollObserverReady = 'true';

                    new MutationObserver(() => {
                        table.querySelectorAll(scrollSelector).forEach(enableCellDragScroll);
                        updateScrollableCells(table);
                    }).observe(table, {
                        childList: true,
                        subtree: true,
                    });
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initializeSchoolDataTables);
            } else {
                initializeSchoolDataTables();
            }

            window.addEventListener('resize', initializeSchoolDataTables);
        })();
    </script>
@endonce
