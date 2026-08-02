@extends('layouts.school')
@section('title', 'School Dashboard')
@section('page-title', 'Dashboard')

@push('styles')
    <style>
        .dashboard-news-marquee {
            position: relative;
        }

        .dashboard-news-marquee-track {
            display: inline-block;
            white-space: nowrap;
            animation: dashboardNewsTrain 32s linear infinite;
            will-change: transform;
        }

        .dashboard-news-marquee-copy {
            display: inline-block;
            padding-right: 40px;
            white-space: nowrap;
        }

        .dashboard-news-marquee:hover .dashboard-news-marquee-track,
        .dashboard-news-marquee:focus-within .dashboard-news-marquee-track {
            animation-play-state: paused;
        }

        .dashboard-hero-slide {
            opacity: 0;
            transition: opacity 700ms ease-in-out;
        }

        .dashboard-hero-slide.is-active {
            opacity: 1;
        }

        @media (min-width: 768px) {
            .dashboard-recent-tables > * {
                grid-column: span 1 / span 1 !important;
                min-width: 0;
            }

            .dashboard-recent-tables .school-data-table-scroll {
                overflow-x: auto;
                overflow-y: hidden;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: thin;
                scrollbar-color: #cbd5e1 transparent;
            }

            .dashboard-recent-tables .school-data-table-scroll::-webkit-scrollbar {
                height: 3px;
            }

            .dashboard-recent-tables .school-data-table-scroll::-webkit-scrollbar-track {
                background: transparent;
            }

            .dashboard-recent-tables .school-data-table-scroll::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 9999px;
            }

            .dashboard-recent-tables .school-data-table-scroll::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }

            .dashboard-recent-tables .school-data-table-scroll.is-drag-scrollable {
                cursor: grab;
            }

            .dashboard-recent-tables .school-data-table-scroll.is-dragging {
                cursor: grabbing;
                user-select: none;
            }

            .dashboard-recent-tables .school-data-table {
                width: var(--school-data-table-min-width, 1068px);
                min-width: var(--school-data-table-min-width, 1068px);
            }
        }

        @keyframes dashboardNewsTrain {
            from {
                transform: translateX(0);
            }

            to {
                transform: translateX(-50%);
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .dashboard-news-marquee-track {
                animation: none;
                transform: none;
            }

            .dashboard-hero-slide {
                transition: none;
            }
        }
    </style>
@endpush

@section('content')
    <x-dashboard.shell>
        <x-dashboard.toolbar
            :greeting="$greeting"
            :filter-label="$dashboardFilterLabel ?? 'Filter'"
            :filter-start="$dashboardFilterStart ?? null"
            :filter-end="$dashboardFilterEnd ?? null"
            :news-label="$dashboardNews['label'] ?? 'News'"
            :news-message="$dashboardNews['message'] ?? 'No news available'"
        />

        <x-dashboard.hero
            :slides="[
                [
                    'src' => asset('images/school-dashboard/boys-classroom-banner.png'),
                    'alt' => 'Boys learning in classroom',
                ],
                [
                    'src' => asset('images/school-dashboard/madrasa-boys-banner.png'),
                    'alt' => 'Boys learning in a madrasa classroom',
                ],
                [
                    'src' => asset('images/school-dashboard/kindergarten-boys-banner.png'),
                    'alt' => 'Boys learning in a kindergarten classroom',
                ],
            ]"
        />

        @php
            $dashboardCards = [
                ['label' => 'Class', 'value' => $classes, 'icon' => 'fa-book-open', 'icon_style' => 'bg-blue-50 text-blue-600'],
                ['label' => 'Teacher', 'value' => $teachersCount, 'icon' => 'fa-graduation-cap', 'icon_style' => 'bg-emerald-50 text-emerald-600'],
                ['label' => 'Student', 'value' => $studentsCount, 'icon' => 'fa-user-graduate', 'icon_style' => 'bg-indigo-50 text-indigo-600'],
                ['label' => 'Employee', 'value' => $employeesCount, 'icon' => 'fa-users', 'icon_style' => 'bg-orange-50 text-orange-600'],
                ['label' => 'Admission', 'value' => $admissionsCount, 'icon' => 'fa-user-plus', 'icon_style' => 'bg-cyan-50 text-cyan-600'],
                ['label' => 'Promote', 'value' => $promotionsCount, 'icon' => 'fa-level-up-alt', 'icon_style' => 'bg-violet-50 text-violet-600'],
                ['label' => 'Tuition', 'value' => $totalTuitionFees, 'icon' => 'fa-book', 'icon_style' => 'bg-green-50 text-green-600'],
                ['label' => 'Food', 'value' => $totalFoodFees, 'icon' => 'fa-utensils', 'icon_style' => 'bg-amber-50 text-amber-600'],
                ['label' => 'Fine', 'value' => $totalFineFees, 'icon' => 'fa-exclamation-circle', 'icon_style' => 'bg-red-50 text-red-600'],
                ['label' => 'Session', 'value' => $sessionsCount, 'icon' => 'fa-calendar-alt', 'icon_style' => 'bg-sky-50 text-sky-600'],
                ['label' => 'Exam', 'value' => $examsCount, 'icon' => 'fa-clipboard-list', 'icon_style' => 'bg-pink-50 text-pink-600'],
                ['label' => 'Total Fee', 'value' => $totalFees, 'icon' => 'fa-file-invoice-dollar', 'icon_style' => 'bg-orange-50 text-orange-600'],
                ['label' => 'Collection', 'value' => 5000, 'icon' => 'fa-hand-holding-usd', 'icon_style' => 'bg-emerald-50 text-emerald-600'],
                ['label' => 'Due', 'value' => $totalDue, 'icon' => 'fa-hourglass-half', 'icon_style' => 'bg-yellow-50 text-yellow-600'],
                ['label' => 'Overdue', 'value' => $overdueAmount, 'icon' => 'fa-calendar-times', 'icon_style' => 'bg-rose-50 text-rose-600'],
                ['label' => 'Payroll', 'value' => $totalPayroll, 'icon' => 'fa-money-check-alt', 'icon_style' => 'bg-cyan-50 text-cyan-700'],
                ['label' => 'Expense', 'value' => $totalExpense, 'icon' => 'fa-receipt', 'icon_style' => 'bg-fuchsia-50 text-fuchsia-600'],
                ['label' => 'Cash', 'value' => $totalCash, 'icon' => 'fa-money-bill-wave', 'icon_style' => 'bg-teal-50 text-teal-600'],
                ['label' => 'Bank', 'value' => $totalBank, 'icon' => 'fa-university', 'icon_style' => 'bg-blue-50 text-blue-700'],
                ['label' => 'Profit', 'value' => 5000, 'icon' => 'fa-chart-line', 'icon_style' => 'bg-lime-50 text-lime-700'],
                ['label' => 'Loss', 'value' => 5000, 'icon' => 'fa-chart-line fa-flip-vertical', 'icon_style' => 'bg-red-50 text-red-700'],
                ['label' => 'Balance', 'value' => 5000, 'icon' => 'fa-balance-scale', 'icon_style' => 'bg-violet-50 text-violet-700'],
            ];

        @endphp

        <x-dashboard.stats :cards="$dashboardCards" />

        @php
            $recentPanels = [
                [
                    'title' => 'Recent Admissions',
                    'icon' => 'fa-user-plus',
                    'icon_style' => 'bg-cyan-50 text-cyan-600',
                    'items' => $recentAdmissions,
                ],
                /*
                [
                    'title' => 'Recent Promotions',
                    'icon' => 'fa-level-up-alt',
                    'icon_style' => 'bg-violet-50 text-violet-600',
                    'items' => $recentPromotions,
                ],
                [
                    'title' => 'Recent Payments',
                    'icon' => 'fa-receipt',
                    'icon_style' => 'bg-emerald-50 text-emerald-600',
                    'items' => $recentPayments,
                ],
                [
                    'title' => 'Recent Dues',
                    'icon' => 'fa-hourglass-half',
                    'icon_style' => 'bg-amber-50 text-amber-600',
                    'items' => $recentDues,
                ],
                [
                    'title' => 'Recent Overdues',
                    'icon' => 'fa-calendar-times',
                    'icon_style' => 'bg-rose-50 text-rose-600',
                    'items' => $recentOverdues,
                ],
                [
                    'title' => 'Recent Leaves',
                    'icon' => 'fa-calendar-minus',
                    'icon_style' => 'bg-indigo-50 text-indigo-600',
                    'items' => $recentLeaves,
                ],
                [
                    'title' => 'Recent Holidays',
                    'icon' => 'fa-umbrella-beach',
                    'icon_style' => 'bg-orange-50 text-orange-600',
                    'items' => $recentHolidays,
                ],
                */
            ];
        @endphp

        <x-dashboard.recent-grid>
            @foreach ($recentPanels as $panel)
                @switch($panel['title'])
                    @case('Recent Admissions')
                        <x-school.recent-admissions-table :items="$panel['items']" class="md:col-span-2" />
                        @break

                    {{--
                    @case('Recent Promotions')
                        <x-school.recent-promotions-table :items="$panel['items']" class="md:col-span-2" />
                        @break

                    @case('Recent Payments')
                        <x-school.recent-payments-table :items="$panel['items']" class="md:col-span-2" />
                        @break

                    @case('Recent Dues')
                        <x-school.recent-dues-table :items="$panel['items']" class="md:col-span-2" />
                        @break

                    @case('Recent Overdues')
                        <x-school.recent-dues-table
                            :items="$panel['items']"
                            title="Recent Overdues"
                            title-id="recent-overdues-title"
                            empty-message="No overdue fees found."
                            amount-label="Overdue"
                            date-label="Overdue Date"
                            class="md:col-span-2"
                        />
                        @break

                    @case('Recent Leaves')
                        <x-school.recent-leaves-table :items="$panel['items']" class="md:col-span-2" />
                        @break

                    @case('Recent Holidays')
                        <x-school.recent-holidays-table :items="$panel['items']" class="md:col-span-2" />
                        @break

                    @default
                        <x-school.recent-list :title="$panel['title']" :items="$panel['items']" :icon="$panel['icon']"
                            :icon-style="$panel['icon_style']" />
                    --}}
                @endswitch
            @endforeach
        </x-dashboard.recent-grid>


    </x-dashboard.shell>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const hero = document.querySelector('[data-dashboard-hero]');

            if (!hero) {
                return;
            }

            const slides = [...hero.querySelectorAll('[data-dashboard-hero-slide]')];
            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            let activeIndex = 0;
            let rotation;

            const showSlide = (index) => {
                activeIndex = index;

                slides.forEach((slide, slideIndex) => {
                    slide.classList.toggle('is-active', slideIndex === activeIndex);
                });

            };

            const startRotation = () => {
                if (reduceMotion || slides.length < 2) {
                    return;
                }

                window.clearInterval(rotation);
                rotation = window.setInterval(() => {
                    showSlide((activeIndex + 1) % slides.length);
                }, 5000);
            };

            startRotation();
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const filter = document.querySelector('[data-dashboard-filter]');

            if (!filter) {
                return;
            }

            const button = filter.querySelector('[data-dashboard-filter-button]');
            const menu = filter.querySelector('[data-dashboard-filter-menu]');
            const icon = filter.querySelector('[data-dashboard-filter-icon]');
            const label = filter.querySelector('[data-dashboard-filter-label]');
            const customRange = filter.querySelector('[data-dashboard-custom-range]');
            const startDate = filter.querySelector('[data-dashboard-start-date]');
            const endDate = filter.querySelector('[data-dashboard-end-date]');
            const startDateDisplay = filter.querySelector('[data-dashboard-start-date-display]');
            const endDateDisplay = filter.querySelector('[data-dashboard-end-date-display]');
            const applyRange = filter.querySelector('[data-dashboard-apply-range]');
            const dateError = filter.querySelector('[data-dashboard-date-error]');
            const filterValues = {
                Today: 'today',
                'Last 7 Days': 'last_7_days',
                'This Month': 'this_month',
                'This Year': 'this_year',
            };

            const formatDate = (value) => {
                if (!value) return 'Select';
                const d = new Date(`${value}T00:00:00`);
                if (isNaN(d.getTime())) return value;
                const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
                return d.getDate() + '-' + months[d.getMonth()] + '-' + d.getFullYear();
            };

            startDate.addEventListener('change', () => {
                startDateDisplay.textContent = formatDate(startDate.value);
            });

            endDate.addEventListener('change', () => {
                endDateDisplay.textContent = formatDate(endDate.value);
            });

            const setOpen = (open) => {
                menu.classList.toggle('hidden', !open);
                button.setAttribute('aria-expanded', String(open));
                icon.classList.toggle('rotate-180', open);
            };

            const applyFilter = (filterValue, extraParams = {}) => {
                const url = new URL(window.location.href);

                url.searchParams.set('filter', filterValue);
                url.searchParams.delete('start_date');
                url.searchParams.delete('end_date');

                Object.entries(extraParams).forEach(([key, value]) => {
                    if (value) {
                        url.searchParams.set(key, value);
                    }
                });

                window.location.assign(url.toString());
            };

            button.addEventListener('click', (event) => {
                event.stopPropagation();
                setOpen(menu.classList.contains('hidden'));
            });

            filter.querySelectorAll('[data-dashboard-filter-option]').forEach((option) => {
                option.addEventListener('click', (event) => {
                    event.stopPropagation();

                    const value = option.dataset.value;

                    if (value === 'Custom') {
                        label.textContent = 'Custom';
                        customRange.classList.remove('hidden');
                        startDate.focus();
                        return;
                    }

                    label.textContent = value;
                    customRange.classList.add('hidden');
                    dateError.classList.add('hidden');
                    setOpen(false);
                    applyFilter(filterValues[value]);
                });
            });

            document.addEventListener('click', (event) => {
                if (!filter.contains(event.target)) {
                    setOpen(false);
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    setOpen(false);
                }
            });
            applyRange.addEventListener('click', (event) => {
                event.stopPropagation();

                if (
                    !startDate.value ||
                    !endDate.value ||
                    startDate.value > endDate.value
                ) {
                    dateError.classList.remove('hidden');
                    return;
                }

                dateError.classList.add('hidden');
                label.textContent = 'Custom';
                setOpen(false);
                applyFilter('custom', {
                    start_date: startDate.value,
                    end_date: endDate.value,
                });
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.dashboard-recent-tables .school-data-table-scroll').forEach((scrollArea) => {
                if (scrollArea.scrollWidth <= scrollArea.clientWidth) {
                    return;
                }

                scrollArea.classList.add('is-drag-scrollable');

                let startX = 0;
                let startScrollLeft = 0;
                let moved = false;

                scrollArea.addEventListener('pointerdown', (event) => {
                    if (
                        event.button !== 0 ||
                        event.target.closest('a, button, input, select, textarea, .school-data-table-cell-scroll')
                    ) {
                        return;
                    }

                    startX = event.clientX;
                    startScrollLeft = scrollArea.scrollLeft;
                    moved = false;
                    scrollArea.classList.add('is-dragging');
                    scrollArea.setPointerCapture(event.pointerId);
                });

                scrollArea.addEventListener('pointermove', (event) => {
                    if (!scrollArea.hasPointerCapture(event.pointerId)) {
                        return;
                    }

                    const distance = event.clientX - startX;

                    if (Math.abs(distance) > 2) {
                        moved = true;
                        scrollArea.scrollLeft = startScrollLeft - distance;
                    }
                });

                const stopDragging = (event) => {
                    if (!scrollArea.hasPointerCapture(event.pointerId)) {
                        return;
                    }

                    scrollArea.releasePointerCapture(event.pointerId);
                    scrollArea.classList.remove('is-dragging');
                };

                scrollArea.addEventListener('pointerup', stopDragging);
                scrollArea.addEventListener('pointercancel', stopDragging);

                scrollArea.addEventListener('click', (event) => {
                    if (!moved) {
                        return;
                    }

                    event.preventDefault();
                    event.stopPropagation();
                    moved = false;
                });
            });
        });
    </script>
@endpush
