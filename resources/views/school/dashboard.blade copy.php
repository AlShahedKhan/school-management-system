@extends('layouts.school')

@section('title', 'School Dashboard')
@section('page-title', 'Dashboard')

@section('content')

    <style>
        html,
        body {
            max-width: 100vw;
            overflow-x: hidden !important;
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif;
        }

        * {
            font-family: 'Roboto', sans-serif;
        }

        .main-view-container {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            width: 100%;
            padding: .75rem;
            box-sizing: border-box;
            margin-top: 8px !important;
            margin-bottom: 8px !important;
        }

        /* Outer wrapper: Full width, no side padding */
        .main-content-wrapper {
            padding: 20px 0;
            width: 100%;
            box-sizing: border-box;
        }

        /* Dashboard Top Header: No side padding */
        /* Dashboard Top Header */
        .dashboard-top {
            display: grid;
            /* Desktop: School name takes remaining space, Filter box is 220px */
            grid-template-columns: 1fr 282px;
            gap: 16px;
            margin-bottom: 15px;
            align-items: center;
            padding: 0;
        }

        @media (max-width: 640px) {
            .dashboard-top {
                /* Mobile: School name takes remaining space, Filter box is 160px */
                grid-template-columns: 1fr 143px !important;
                gap: 16px;
            }

            #dropdownBtn {
                padding-left: 8px;
                padding-right: 4px;
                font-size: 11px;
            }

            #dropdownBtn svg {
                width: 14px;
                height: 14px;
                margin-left: 2px;
            }

            .main-view-container {
                margin-top: 3px !important;
                margin-bottom: 8px !important;
            }
        }

        /* --- IMAGE SLIDER --- */
        .slider-box {
            width: 100%;
            aspect-ratio: 1177 / 300;
            height: auto;
            overflow: hidden;
            border: none;
            position: relative;
            margin-bottom: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            background: #f3f4f6;
        }

        .slider-wrapper {
            display: flex;
            width: 100%;
            height: 100%;
            transition: transform 0.6s ease-in-out;
        }

        .slide {
            width: 100%;
            height: 100%;
            flex-shrink: 0;
            position: relative;
            overflow: hidden;
        }

        .slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            background: #ffffff;
            display: block;
        }

        /* Navigation Arrows */
        .slider-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 44px;
            height: 44px;
            background: rgba(255, 255, 255, 0.95);
            border: 2px solid rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 10;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
        }

        .slider-arrow:hover {
            background: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
            transform: translateY(-50%) scale(1.1);
        }

        .slider-arrow i {
            color: #374151;
            font-size: 18px;
            transition: color 0.3s ease;
        }

        .slider-arrow:hover i {
            color: #2563eb;
        }

        .slider-arrow.prev {
            left: 16px;
        }

        .slider-arrow.next {
            right: 16px;
        }

        .slider-box:hover .slider-arrow {
            opacity: 1;
        }

        /* Dot Indicators */
        .slider-dots {
            position: absolute;
            bottom: 16px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
            z-index: 10;
            background: rgba(0, 0, 0, 0.5);
            padding: 6px 12px;
            border-radius: 16px;
            backdrop-filter: blur(4px);
        }

        .slider-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid rgba(255, 255, 255, 0.7);
        }

        .slider-dot:hover {
            background: rgba(255, 255, 255, 0.8);
            transform: scale(1.2);
        }

        .slider-dot.active {
            background: white;
            transform: scale(1.3);
            border-color: white;
            box-shadow: 0 0 8px rgba(255, 255, 255, 0.5);
        }

        /* Loading State */
        .slider-loading {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f3f4f6;
            z-index: 5;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid rgba(37, 99, 235, 0.2);
            border-top: 4px solid #2563eb;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Image Counter */
        .slider-counter {
            position: absolute;
            top: 16px;
            right: 16px;
            background: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            z-index: 10;
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .badge {
            padding: 2px 8px;
            font-size: 12px;
            border-radius: 9999px;
            display: inline-block;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #ca8a04;
        }

        /* Marquee Box */
        .school-name-box {
            background: #fffefb;
            border: 1px solid #e5e7eb;
            padding: 0;
            height: 40px;
            display: flex;
            align-items: center;
            overflow: hidden;
            position: relative;
            border-radius: 0;
            flex: 1;
            min-width: 0;
        }

        .marquee-content {
            position: absolute;
            white-space: nowrap;
            will-change: transform;
            animation: marqueeDesktop 10s linear infinite;
        }

        .school-name-text {
            font-weight: 600;
            font-size: 16px;
            color: #1e40af;
            display: inline-block;
            padding-left: 10px;
        }

        @keyframes marqueeDesktop {
            0% {
                left: 100%;
                transform: translateX(0);
            }

            100% {
                left: 0;
                transform: translateX(-100%);
            }
        }


        @media (min-width: 1024px) {
            .chart-box {
                height: 250px;
            }

            .canvas-container {
                max-height: 160px;
            }
        }

        /* --- Table Styles: Removed side margins --- */
        .table-container {
            background-color: #ffffff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            padding: 15px;
            margin: 0;
            width: 100%;
            box-sizing: border-box;
        }

        .table-header {
            background-color: #f3f4f6;
            color: #4b5563;
            font-size: 12px;
            border: 1px solid #bcbdbe;
        }

        .table-cell {
            padding: 8px 12px;
            font-size: 14px;
            border: 1px solid #bcbdbe;
        }

        .table-row {
            border: 1px solid #bcbdbe;
            transition: background 0.2s;
        }

        .table-row:hover {
            background-color: #f9fafb;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background-color: #dadde2;
            border-radius: 9999px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 9999px;
            background: #a78bfa;
        }

        @media (max-width: 768px) {
            .main-content-wrapper {
                padding: 15px 0;
            }

            .dashboard-top {
                grid-template-columns: 1fr 145px;
                margin-bottom: 15px;
                padding: 0;
            }

            .marquee-content {
                animation: marqueeMobile 10s linear infinite;
            }

            @keyframes marqueeMobile {
                0% {
                    left: 100%;
                    transform: translateX(0);
                }

                100% {
                    left: 0;
                    transform: translateX(-100%);
                }
            }

            .school-name-text {
                font-size: 12px;
            }

            .school-name-box {
                height: 38px;
            }

            .slider-box {
                margin-bottom: 15px;
            }

            .slider-arrow {
                width: 36px;
                height: 36px;
                opacity: 0.9;
            }

            .slider-arrow i {
                font-size: 14px;
            }

            .slider-arrow.prev {
                left: 8px;
            }

            .slider-arrow.next {
                right: 8px;
            }

            .slider-dots {
                bottom: 10px;
                padding: 6px 12px;
                gap: 6px;
            }

            .dot {
                width: 8px;
                height: 8px;
            }

            .slider-counter {
                top: 10px;
                right: 10px;
                padding: 4px 10px;
                font-size: 11px;
            }

            .canvas-container {
                max-height: 140px;
            }
        }

        @media (max-width: 480px) {
            .slider-arrow {
                width: 32px;
                height: 32px;
            }

            .slider-arrow i {
                font-size: 12px;
            }

            .slider-counter {
                top: 8px;
                right: 8px;
                padding: 3px 8px;
                font-size: 10px;
            }
        }


        /* ------------Chart style-------------- */

        /* CARD */
        .card {
            background: white;
            padding: 16px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
            border-radius: 0 !important;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 16px;
        }

        .legend-item {
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* DOT FIX */
        .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
        }

        .chart-box {
            width: 100%;
            max-width: 240px;
            aspect-ratio: 1/1;
        }

        /* CHART WRAPPER (IMPORTANT FIX) */
        .chart-wrapper {
            width: 100%;
            aspect-ratio: 16 / 9;
            min-height: 220px;
            max-height: 420px;
        }

        /* MOBILE */
        @media (max-width: 600px) {
            .title {
                font-size: 16px;
            }

            .chart-wrapper {
                aspect-ratio: 1 / 1;
            }
        }

        @media (max-width: 768px) {
            .main-view-container {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
        }
    </style>

    <div class="main-view-container">
        <div class="w-full">
            <!-- slider -->
            @php
                // Get the dynamic settings (id 1)
                $dynamicSettings = \App\Models\DynamicOperation::find(1);
                // Get banners or empty array, then take only max 3
                $banners = collect($dynamicSettings->school_dashboard_banners ?? [])->take(3);
            @endphp

            <div class="slider-box" id="sliderBox">
                <!-- Navigation Arrows -->
                {{-- <button class="slider-arrow prev" id="prevArrow">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <button class="slider-arrow next" id="nextArrow">
                    <i class="fa-solid fa-chevron-right"></i>
                </button> --}}

                <!-- Image Counter -->
                {{-- <div class="slider-counter" id="sliderCounter">
                    <span id="currentIndex">1</span> / <span id="totalSlides">{{ max(count($banners), 1) }}</span>
                </div> --}}

                <!-- Loading Spinner -->
                <div class="slider-loading" id="sliderLoading">
                    <div class="spinner"></div>
                </div>

                <div class="slider-wrapper" id="sliderWrapper">
                    @if (count($banners) > 0)
                        @foreach ($banners as $banner)
                            <div class="slide">
                                <img src="{{ asset('storage/' . $banner) }}" alt="Dashboard Banner">
                            </div>
                        @endforeach
                    @else
                        {{-- Fallback if no images exist --}}
                        <div class="slide">
                            <img src="{{ asset('images/banner1.jpg') }}" alt="Default Banner">
                        </div>
                    @endif
                </div>

                <!-- Dot Indicators -->
                {{-- <div class="slider-dots" id="sliderDots"></div> --}}
            </div>

            <div class="dashboard-top">
                <div class="school-name-box">
                    <div class="marquee-content">
                        <span class="school-name-text">
                            {{ auth()->user()->school_name ?? 'Global International Excellence School Management System' }}
                        </span>
                    </div>
                </div>

                <div class="relative inline-block text-left w-full">
                    <button id="dropdownBtn"
                        class="inline-flex justify-between items-center w-full h-[40px] px-3 py-2 border border-gray-200 bg-white text-gray-500 font-medium text-xs sm:text-sm transition-colors hover:bg-gray-50">
                        <span id="btnText">Today</span>
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div id="dropdownMenu"
                        class="hidden absolute right-0 mt-1 w-full bg-white border border-gray-200 shadow-lg z-50">
                        <div class="py-1">
                            <a href="#"
                                class="filter-opt block px-4 py-2 text-gray-700 hover:bg-gray-100 text-sm">Today</a>
                            <a href="#"
                                class="filter-opt block px-4 py-2 text-gray-700 hover:bg-gray-100 text-sm">Last 7 days</a>
                            <a href="#"
                                class="filter-opt block px-4 py-2 text-gray-700 hover:bg-gray-100 text-sm">Monthly</a>
                            <a href="#"
                                class="filter-opt block px-4 py-2 text-gray-700 hover:bg-gray-100 text-sm">Yearly</a>
                            <hr class="border-gray-100" />
                            <a href="#" id="customOpt"
                                class="block px-4 py-2 text-gray-700 hover:bg-gray-100 flex justify-between items-center text-sm">
                                Custom
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 5l7 7-7 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    </path>
                                </svg>
                            </a>
                        </div>

                        <div id="customFields" class="hidden p-3 border-t border-gray-100 bg-gray-50">
                            <div class="space-y-2">
                                <div>
                                    <label class="block text-[9px] font-bold uppercase text-gray-400">Start</label>
                                    <input type="date"
                                        class="w-full mt-1 px-1 py-1 border border-gray-200 bg-white text-xs focus:outline-none focus:border-blue-500" />
                                </div>
                                <div>
                                    <label class="block text-[9px] font-bold uppercase text-gray-400">End</label>
                                    <input type="date"
                                        class="w-full mt-1 px-1 py-1 border border-gray-200 bg-white text-xs focus:outline-none focus:border-blue-500" />
                                </div>
                                <button id="searchBtn"
                                    class="w-full bg-blue-600 text-white py-1.5 text-[10px] font-bold uppercase tracking-wider hover:bg-blue-700 transition-colors">
                                    Search
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!---------------------- Card section ---------------->

            <div id="cardContainer" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 mt-3 mb-3"></div>




            <!------------------- Graph--------------------->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="card">
                    <div class="title text-center">Payment vs Due</div>
                    <div class="legend flex items-center justify-center gap-4 mt-9 mb-3">
                        <div class="legend-item">
                            <span class="dot" style="background: green"></span>
                            Payment: {{ number_format($totalCollection) }}
                        </div>
                        <div class="legend-item">
                            <span class="dot" style="background: #ef4444"></span>
                            Due: {{ number_format($totalDue) }}
                        </div>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="transactionChart"></canvas>
                    </div>
                </div>

                <div class="card">
                    <div class="title text-center">Income vs Expense</div>
                    <div class="legend flex items-center justify-center gap-4 mt-3 mb-3">
                        <div class="legend-item">
                            <span class="dot" style="background: #1570ef"></span>
                            Income: {{ number_format($totalIncome) }}
                        </div>
                        <div class="legend-item">
                            <span class="dot" style="background: #ff4405"></span>
                            Expense: {{ number_format($totalExpense) }}
                        </div>
                    </div>
                    <div class="chart-wrapper">
                        <canvas id="incomeExpenseChart"></canvas>
                    </div>
                </div>

                <div class="card">
                    <div class="title text-center">Loss & Profit</div>
                    <div class="wrapper flex flex-col items-center justify-center mt-1">
                        <div class="chart-box">
                            <canvas id="pieChart"></canvas>
                        </div>
                    </div>
                    <div class="flex justify-center items-center gap-6 mt-2">
                        <div class="indicator">
                            <span class="dot" style="background: #3b82f6"></span>
                            Profit: {{ $profitPercent }}%
                        </div>
                        <div class="indicator">
                            <span class="dot" style="background: #ef4444"></span>
                            Loss: {{ $lossPercent }}%
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="title text-center">Bank vs Cash</div>
                    <div class="body flex flex-col items-center justify-center mt-2">
                        <div class="chart-box">
                            <canvas id="doughnutChart"></canvas>
                        </div>
                        <div class="flex gap-5 items-center justify-center mt-1">
                            <div>Bank: <b style="color: #1570ef">{{ number_format($bankTotal) }}</b></div>
                            <div class="divider"></div>
                            <div class="label">
                                Cash: <b style="color: #16a34a">{{ number_format($cashTotal) }}</b>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Table section -->

            <section>
                <div class="grid grid-cols-1 mt-5">

                    <div class="table-container">
                        <h2 class="mb-2 text-left font-semibold text-gray-700">Teachers</h2>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left min-w-[800px]">
                                <thead class="table-header">
                                    <tr>
                                        <th class="table-cell">Sl</th>
                                        <th class="table-cell">Name</th>
                                        <th class="table-cell">ID</th>
                                        <th class="table-cell">Designation</th>
                                        <th class="table-cell">Salary</th>
                                        <th class="table-cell">Due</th>
                                        <th class="table-cell">Attendance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($teachersList as $key => $tchr)
                                        <tr class="table-row hover:bg-gray-50 transition">
                                            <td class="table-cell">{{ $key + 1 }}</td>
                                            <td class="table-cell">{{ $tchr->name }}</td>
                                            <td class="table-cell">{{ $tchr->id_number }}</td>
                                            <td class="table-cell">{{ $tchr->designation }}</td>
                                            <td class="table-cell">0</td>
                                            <td class="table-cell text-red-500 font-medium">0</td>
                                            <td class="table-cell text-blue-500">0</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">No teachers found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="table-container">
                        <h2 class="mb-2 text-left font-semibold text-gray-700">Top Class</h2>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left min-w-[700px]">
                                <thead class="table-header">
                                    <tr>
                                        <th class="table-cell">Sl</th>
                                        <th class="table-cell">Class</th>
                                        <th class="table-cell">Group</th>
                                        <th class="table-cell">Section</th>
                                        <th class="table-cell">Session</th>
                                        <th class="table-cell">Exam Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topClasses as $key => $class)
                                        <tr class="table-row">
                                            <td class="table-cell">{{ $key + 1 }}</td>
                                            <td class="table-cell">{{ $class->class_name }}</td>
                                            <td class="table-cell">{{ $class->group_name }}</td>
                                            <td class="table-cell">{{ $class->section_name }}</td>
                                            <td class="table-cell">{{ $class->session_name }}</td>
                                            <td class="table-cell">
                                                <span
                                                    class="badge {{ $class->remaining_subject == 0 ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                                    {{ $class->remaining_subject == 0 ? 'Completed' : 'Running' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">No class data found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="table-container">
                        <h2 class="mb-2 text-left font-semibold text-gray-700">
                            Due List ({{ now()->format('F') }})
                        </h2>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left min-w-[900px]">
                                <thead class="table-header">
                                    <tr>
                                        <th class="table-cell">Sl</th>
                                        <th class="table-cell">Class</th>
                                        <th class="table-cell">Group</th>
                                        <th class="table-cell">Section</th>
                                        <th class="table-cell">Session</th>
                                        <th class="table-cell">ID</th>
                                        <th class="table-cell">Student Name</th>
                                        <th class="table-cell">Total Fee</th>
                                        <th class="table-cell">Paid Fee</th>
                                        <th class="table-cell">Due Fee</th>
                                        <th class="table-cell">Pay Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($dueList as $key => $due)
                                        <tr class="table-row">
                                            <td class="table-cell">{{ $key + 1 }}</td>
                                            <td class="table-cell">{{ $due->student->schoolClass->class_name ?? 'N/A' }}
                                            </td>
                                            <td class="table-cell">{{ $due->student->schoolGroup->group_name ?? 'N/A' }}
                                            </td>
                                            <td class="table-cell">
                                                {{ $due->student->schoolSection->section_name ?? 'N/A' }}</td>
                                            <td class="table-cell">
                                                {{ $due->student->schoolSession->session_year ?? 'N/A' }}</td>
                                            <td class="table-cell">{{ $due->student->student_id_number ?? 'N/A' }}</td>
                                            <td class="table-cell">{{ $due->student->student_name ?? 'N/A' }}</td>
                                            <td class="table-cell">{{ number_format($due->total_payable, 2) }}</td>
                                            <td class="table-cell">{{ number_format($due->type_amount, 2) }}</td>
                                            <td class="table-cell text-red-500 font-medium">{{ number_format($due->payable_due, 2) }}</td>
                                            <td class="table-cell text-orange-500">{{ $due->pay_date ?? '---' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center py-4">No due payments for this month.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="table-container">
                        <h2 class="mb-2 text-left font-semibold text-gray-700">Upcoming Exam</h2>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left min-w-[900px]">
                                <thead class="table-header">
                                    <tr>
                                        <th class="table-cell">Sl</th>
                                        <th class="table-cell">Class</th>
                                        <th class="table-cell">Group</th>
                                        <th class="table-cell">Section</th>
                                        <th class="table-cell">Session</th>
                                        <th class="table-cell">Exam</th>
                                        <th class="table-cell">Exam Fee</th>
                                        <th class="table-cell">Fee Due</th>
                                        <th class="table-cell">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($upcomingExams as $key => $exam)
                                        <tr class="table-row">
                                            <td class="table-cell">{{ $key + 1 }}</td>
                                            <td class="table-cell">{{ $exam->class_name }}</td>
                                            <td class="table-cell">{{ $exam->group_name }}</td>
                                            <td class="table-cell">{{ $exam->section_name }}</td>
                                            <td class="table-cell">{{ $exam->session_name }}</td>
                                            <td class="table-cell">{{ $exam->exam_name }} ({{ $exam->subject_name }})
                                            </td>
                                            <td class="table-cell">0</td>
                                            <td class="table-cell text-red-500 font-medium">0</td>
                                            <td class="table-cell">
                                                <span
                                                    class="badge bg-blue-100 text-blue-700">{{ $exam->exam_date }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4">No upcoming exams scheduled.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        </main>

        <!--------Dropdown Script---------->
        <script>
            const dropdownBtn = document.getElementById("dropdownBtn");
            const dropdownMenu = document.getElementById("dropdownMenu");
            const btnText = document.getElementById("btnText");
            const filterOptions = document.querySelectorAll(".filter-opt");
            const customOpt = document.getElementById("customOpt");
            const customFields = document.getElementById("customFields");
            const searchBtn = document.getElementById("searchBtn");

            // Toggle Main Dropdown
            dropdownBtn.addEventListener("click", () => {
                dropdownMenu.classList.toggle("hidden");
            });

            // Handle Standard Options Click
            filterOptions.forEach((option) => {
                option.addEventListener("click", (e) => {
                    e.preventDefault();
                    btnText.innerText = option.innerText; // Change Button Name
                    dropdownMenu.classList.add("hidden"); // Close Menu
                    customFields.classList.add("hidden"); // Reset custom fields
                });
            });

            // Toggle Custom Fields
            customOpt.addEventListener("click", (e) => {
                e.preventDefault();
                customFields.classList.toggle("hidden");
            });

            // Search Button Action
            searchBtn.addEventListener("click", () => {
                btnText.innerText = "Custom ";
                dropdownMenu.classList.add("hidden");
            });

            // Close dropdown when clicking outside
            window.addEventListener("click", (e) => {
                if (
                    !dropdownBtn.contains(e.target) &&
                    !dropdownMenu.contains(e.target)
                ) {
                    dropdownMenu.classList.add("hidden");
                }
            });


            /* --- SIMPLIFIED SLIDER LOGIC --- */
            document.addEventListener("DOMContentLoaded", function() {
                const wrapper = document.getElementById('sliderWrapper');
                const sliderBox = document.getElementById('sliderBox');
                const prevArrow = document.getElementById('prevArrow');
                const nextArrow = document.getElementById('nextArrow');
                const dotsContainer = document.getElementById('sliderDots');
                const currentIndexEl = document.getElementById('currentIndex');
                const loadingEl = document.getElementById('sliderLoading');

                if (!wrapper) return;

                const slides = wrapper.querySelectorAll('.slide');
                const totalSlides = slides.length;
                let currentIndex = 0;
                let autoPlayInterval;

                // Hide loading spinner
                if (loadingEl) loadingEl.style.display = 'none';

                // Create dot indicators
                function createDots() {
                    if (!dotsContainer) return;
                    dotsContainer.innerHTML = '';
                    for (let i = 0; i < totalSlides; i++) {
                        const dot = document.createElement('div');
                        dot.className = `slider-dot ${i === 0 ? 'active' : ''}`;
                        dot.addEventListener('click', () => goToSlide(i));
                        dotsContainer.appendChild(dot);
                    }
                }

                // Update counter
                function updateCounter() {
                    if (currentIndexEl) {
                        currentIndexEl.textContent = currentIndex + 1;
                    }
                }

                // Update active dot
                function updateDots() {
                    const dots = dotsContainer.querySelectorAll('.slider-dot');
                    dots.forEach((dot, index) => {
                        dot.classList.toggle('active', index === currentIndex);
                    });
                }

                // Go to specific slide
                function goToSlide(index) {
                    if (index < 0) index = totalSlides - 1;
                    if (index >= totalSlides) index = 0;
                    
                    currentIndex = index;
                    const percentage = currentIndex * 100;
                    wrapper.style.transform = `translateX(-${percentage}%)`;
                    
                    updateCounter();
                    updateDots();
                }

                // Next slide
                function nextSlide() {
                    goToSlide(currentIndex + 1);
                }

                // Previous slide
                function prevSlide() {
                    goToSlide(currentIndex - 1);
                }

                // Auto-play functionality
                function startAutoPlay() {
                    if (autoPlayInterval) clearInterval(autoPlayInterval);
                    autoPlayInterval = setInterval(nextSlide, 2000);
                }

                function stopAutoPlay() {
                    if (autoPlayInterval) {
                        clearInterval(autoPlayInterval);
                    }
                }

                // Initialize slider
                if (totalSlides > 1) {
                    createDots();
                    updateCounter();
                    startAutoPlay();

                    // Navigation arrows
                    if (prevArrow) {
                        prevArrow.style.opacity = '0';
                        prevArrow.addEventListener('click', () => {
                            stopAutoPlay();
                            prevSlide();
                            startAutoPlay();
                        });
                    }

                    if (nextArrow) {
                        nextArrow.style.opacity = '0';
                        nextArrow.addEventListener('click', () => {
                            stopAutoPlay();
                            nextSlide();
                            startAutoPlay();
                        });
                    }

                    // Show arrows on hover
                    sliderBox.addEventListener('mouseenter', () => {
                        stopAutoPlay();
                        if (prevArrow) prevArrow.style.opacity = '1';
                        if (nextArrow) nextArrow.style.opacity = '1';
                    });

                    sliderBox.addEventListener('mouseleave', () => {
                        startAutoPlay();
                        if (prevArrow) prevArrow.style.opacity = '0';
                        if (nextArrow) nextArrow.style.opacity = '0';
                    });

                    // Touch/Swipe support for mobile
                    let touchStartX = 0;
                    let touchEndX = 0;

                    sliderBox.addEventListener('touchstart', (e) => {
                        touchStartX = e.changedTouches[0].screenX;
                        stopAutoPlay();
                    }, { passive: true });

                    sliderBox.addEventListener('touchend', (e) => {
                        touchEndX = e.changedTouches[0].screenX;
                        const diff = touchStartX - touchEndX;
                        
                        if (Math.abs(diff) > 50) {
                            if (diff > 0) {
                                nextSlide();
                            } else {
                                prevSlide();
                            }
                        }
                        startAutoPlay();
                    }, { passive: true });

                    // Keyboard navigation
                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'ArrowLeft') {
                            stopAutoPlay();
                            prevSlide();
                            startAutoPlay();
                        } else if (e.key === 'ArrowRight') {
                            stopAutoPlay();
                            nextSlide();
                            startAutoPlay();
                        }
                    });
                } else {
                    // Hide navigation for single image
                    if (prevArrow) prevArrow.style.display = 'none';
                    if (nextArrow) nextArrow.style.display = 'none';
                    if (dotsContainer) dotsContainer.style.display = 'none';
                }
            });
        </script>


        <!------------CARDS SCRIPT---------------->
        <script>
            // Map Laravel data to JS variables
            const cards = [
                "Classes",
                "Teachers",
                "Students",
                "Fees",
                "Payment",
                "Due Fees",
                "Income",
                "Expense",
                "Payroll",
                "Profit",
                "Loss",
                "Balance",
                "Bank",
                // Dynamic Title: Fallback to 'No Plan' if subscription is missing
                "{{ $subscription->package->package_type ?? 'No Plan' }}",
            ];

            const cardValues = [
                "{{ $classes ?? 0 }}",
                "{{ $teachersCount ?? 0 }}",
                "{{ $studentsCount ?? 0 }}",
                "{{ number_format($totalFees ?? 0) }}",
                "{{ number_format($totalCollection ?? 0) }}",
                "{{ number_format($totalDue ?? 0) }}",
                "{{ number_format($totalIncome ?? 0) }}",
                "{{ number_format($totalExpense ?? 0) }}",
                "0",
                "{{ number_format(($totalIncome ?? 0) - ($totalExpense ?? 0)) }}",
                "{{ ($totalExpense ?? 0) > ($totalIncome ?? 0) ? number_format($totalExpense - $totalIncome) : '0' }}",
                "{{ number_format(($totalIncome ?? 0) - ($totalExpense ?? 0)) }}",
                "0",
                // Status: 1 if subscription exists and is active, else 0
                "{{ $subscription && $subscription->status == 'active' ? 1 : 0 }}",
            ];

            const icons = [
                "fa-school",
                "fa-user",
                "fa-user-graduate",
                "fa-money-bill",
                "fa-credit-card",
                "fa-hand-holding-dollar",
                "fa-coins",
                "fa-wallet",
                "fa-user-tie",
                "fa-chart-line",
                "fa-arrow-down",
                "fa-balance-scale",
                "fa-building-columns",
                "fa-box",
            ];

            const container = document.getElementById("cardContainer");

            cards.forEach((t, i) => {
                container.innerHTML += `
            <div class="bg-white shadow p-3 flex items-center gap-3 sm:gap-4 rounded-none border-dotted border-2 border-gray-200">
                <div class="flex-shrink-0 flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 text-blue-600 rounded-full bg-gray-100 border-[0.5px] border-gray-200">
                    <i class="fa ${icons[i]} text-sm sm:text-lg"></i>
                </div>                
                <div class="min-w-0">
                    <div class="text-xs sm:text-lg font-midium truncate">${t}</div>
                    <div class="text-sm sm:text-base font-normal truncate">${cardValues[i]}</div>
                </div>
            </div>`;
            });
        </script>

        <!------------CHARTS SCRIPT---------------->
        <script>
            const dashboardChartFont = "'Roboto', sans-serif";
            const chartLabels = {!! json_encode($months) !!};

            function prepareCanvas(canvas) {
                if (!canvas) {
                    return null;
                }

                const rect = canvas.getBoundingClientRect();
                const ratio = window.devicePixelRatio || 1;

                canvas.width = Math.max(rect.width * ratio, 1);
                canvas.height = Math.max(rect.height * ratio, 1);

                const ctx = canvas.getContext('2d');
                ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
                ctx.clearRect(0, 0, rect.width, rect.height);
                ctx.font = `10px ${dashboardChartFont}`;
                ctx.lineCap = 'round';
                ctx.lineJoin = 'round';

                return {
                    ctx,
                    width: rect.width,
                    height: rect.height,
                };
            }

            function drawGrid(ctx, width, height, padding) {
                ctx.strokeStyle = '#e5e7eb';
                ctx.lineWidth = 1;
                ctx.setLineDash([5, 5]);

                for (let i = 0; i <= 4; i++) {
                    const y = padding.top + ((height - padding.top - padding.bottom) / 4) * i;
                    ctx.beginPath();
                    ctx.moveTo(padding.left, y);
                    ctx.lineTo(width - padding.right, y);
                    ctx.stroke();
                }

                ctx.setLineDash([]);
            }

            function normalizeMax(series) {
                const values = series.flat().map(Number);
                const max = Math.max(...values, 0);

                return max > 0 ? max : 1;
            }

            function drawBarChart(canvasId, labels, datasets) {
                const chart = prepareCanvas(document.getElementById(canvasId));

                if (!chart) {
                    return;
                }

                const { ctx, width, height } = chart;
                const padding = { top: 14, right: 12, bottom: 24, left: 28 };
                const plotWidth = width - padding.left - padding.right;
                const plotHeight = height - padding.top - padding.bottom;
                const maxValue = normalizeMax(datasets.map((dataset) => dataset.data));
                const groupWidth = plotWidth / Math.max(labels.length, 1);
                const barWidth = Math.min(10, groupWidth / (datasets.length + 1));

                drawGrid(ctx, width, height, padding);

                labels.forEach((label, labelIndex) => {
                    const groupCenter = padding.left + groupWidth * labelIndex + groupWidth / 2;

                    datasets.forEach((dataset, datasetIndex) => {
                        const value = Number(dataset.data[labelIndex] || 0);
                        const barHeight = (value / maxValue) * plotHeight;
                        const x = groupCenter + (datasetIndex - (datasets.length - 1) / 2) * (barWidth + 5) - barWidth / 2;
                        const y = padding.top + plotHeight - barHeight;

                        ctx.fillStyle = dataset.color;
                        ctx.fillRect(x, y, barWidth, barHeight);
                    });

                    ctx.fillStyle = '#64748b';
                    ctx.textAlign = 'center';
                    ctx.fillText(label, groupCenter, height - 6);
                });
            }

            function drawLineChart(canvasId, labels, datasets) {
                const chart = prepareCanvas(document.getElementById(canvasId));

                if (!chart) {
                    return;
                }

                const { ctx, width, height } = chart;
                const padding = { top: 14, right: 12, bottom: 24, left: 28 };
                const plotWidth = width - padding.left - padding.right;
                const plotHeight = height - padding.top - padding.bottom;
                const maxValue = normalizeMax(datasets.map((dataset) => dataset.data));

                drawGrid(ctx, width, height, padding);

                datasets.forEach((dataset) => {
                    const points = dataset.data.map((value, index) => {
                        const x = labels.length > 1
                            ? padding.left + (plotWidth / (labels.length - 1)) * index
                            : padding.left + plotWidth / 2;
                        const y = padding.top + plotHeight - (Number(value || 0) / maxValue) * plotHeight;

                        return { x, y };
                    });

                    if (points.length < 1) {
                        return;
                    }

                    const gradient = ctx.createLinearGradient(0, padding.top, 0, height - padding.bottom);
                    gradient.addColorStop(0, dataset.fill);
                    gradient.addColorStop(1, 'rgba(255,255,255,0)');

                    ctx.beginPath();
                    points.forEach((point, index) => {
                        if (index === 0) {
                            ctx.moveTo(point.x, point.y);
                        } else {
                            ctx.lineTo(point.x, point.y);
                        }
                    });
                    ctx.lineTo(points[points.length - 1].x, height - padding.bottom);
                    ctx.lineTo(points[0].x, height - padding.bottom);
                    ctx.closePath();
                    ctx.fillStyle = gradient;
                    ctx.fill();

                    ctx.beginPath();
                    points.forEach((point, index) => {
                        if (index === 0) {
                            ctx.moveTo(point.x, point.y);
                        } else {
                            ctx.lineTo(point.x, point.y);
                        }
                    });
                    ctx.strokeStyle = dataset.color;
                    ctx.lineWidth = 2.5;
                    ctx.stroke();
                });

                labels.forEach((label, index) => {
                    const x = labels.length > 1
                        ? padding.left + (plotWidth / (labels.length - 1)) * index
                        : padding.left + plotWidth / 2;

                    ctx.fillStyle = '#64748b';
                    ctx.textAlign = 'center';
                    ctx.fillText(label, x, height - 6);
                });
            }

            function drawDoughnutChart(canvasId, values, colors, cutout = 0) {
                const chart = prepareCanvas(document.getElementById(canvasId));

                if (!chart) {
                    return;
                }

                const { ctx, width, height } = chart;
                const total = values.reduce((sum, value) => sum + Number(value || 0), 0) || 1;
                const radius = Math.max(Math.min(width, height) / 2 - 8, 1);
                const centerX = width / 2;
                const centerY = height / 2;
                let startAngle = -Math.PI / 2;

                values.forEach((value, index) => {
                    const angle = (Number(value || 0) / total) * Math.PI * 2;

                    ctx.beginPath();
                    ctx.moveTo(centerX, centerY);
                    ctx.arc(centerX, centerY, radius, startAngle, startAngle + angle);
                    ctx.closePath();
                    ctx.fillStyle = colors[index];
                    ctx.fill();

                    startAngle += angle;
                });

                if (cutout > 0) {
                    ctx.beginPath();
                    ctx.arc(centerX, centerY, radius * cutout, 0, Math.PI * 2);
                    ctx.fillStyle = '#ffffff';
                    ctx.fill();
                }
            }

            function renderDashboardCharts() {
                drawBarChart('transactionChart', chartLabels, [
                    { data: {!! json_encode($monthlyPayments) !!}, color: 'green' },
                    { data: {!! json_encode($monthlyDues) !!}, color: '#EF4444' },
                ]);

                drawLineChart('incomeExpenseChart', chartLabels, [
                    { data: {!! json_encode($monthlyIncomes) !!}, color: '#1570EF', fill: 'rgba(21,112,239,0.2)' },
                    { data: {!! json_encode($monthlyExpenses) !!}, color: '#FF4405', fill: 'rgba(255,68,5,0.2)' },
                ]);

                drawDoughnutChart('pieChart', [{{ $profitPercent }}, {{ $lossPercent }}], ['#3B82F6', '#EF4444']);
                drawDoughnutChart('doughnutChart', [{{ $bankTotal }}, {{ $cashTotal }}], ['#1570EF', '#16A34A'], 0.55);
            }

            renderDashboardCharts();
            window.addEventListener('resize', renderDashboardCharts);
        </script>
@endsection
