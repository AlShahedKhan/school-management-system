@extends('layouts.school')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        html,
        body {
            max-width: 100vw;
            overflow-x: hidden;
            margin: 0;
            padding: 0;
        }

        .main-view-container {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            width: 100%;
            padding: .75rem;
            box-sizing: border-box;
        }

        @media (max-width: 768px) {
            .main-view-container {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
        }

        .table-card {
            border: 1px solid #e2e8f0;
            background: #ffffff;
            border-radius: 0;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            width: 100%;
            overflow: hidden;
        }

        /* ================= Table Container ================= */
        .table-responsive {
            width: 100%;
            overflow-x: auto !important;
            display: block;
            background: white;
            padding: 15px;
        }

        /* ================= Table ================= */
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
            border: 1px solid #d1d5db;
            font-size: 12px;
        }

        /* ================= Table Header ================= */
        th {
            padding: 0 10px !important;
            height: 32px !important;
            min-height: 32px !important;
            line-height: 32px !important;
            white-space: nowrap;
            background: #f8fafc;
            border-bottom: 1px solid #d1d5db;
            border-right: 1px solid #d1d5db;
            color: #374151;
            /* Natural dark text color */
            font-weight: 700;
            /* Normal Bold */
            vertical-align: middle;
            text-align: left;
            text-transform: none !important;
            /* Removes any forced casing */
        }

        /* Specifically center the Photo (first) and Action (last) headers */
        th:last-child {
            text-align: center !important;
        }

        th:last-child {
            text-align: center !important;
        }

        /* ================= Table Body ================= */
        tr {
            height: 32px !important;
            min-height: 32px !important;
        }

        td {
            padding: 0 10px !important;
            vertical-align: middle;
            border-bottom: 1px solid #d1d5db;
            border-right: 1px solid #d1d5db;
            font-size: 12px;
            height: 32px !important;
            min-height: 32px !important;
            color: inherit;
            /* Inherits normal text color */
        }

        /* ================= Circular Image Styling ================= */
        td img {
            /* Equal width and height are vital to prevent the "egg" shape */
            width: 28px !important;
            height: 28px !important;
            border-radius: 50% !important;
            object-fit: cover !important;
            /* Crops the image to fit the circle */
            display: block;
            margin: 0 auto;
            padding: 0 !important;
        }

        /* Shrink all other elements inside td */
        td *:not(img) {
            margin: 0 !important;
            padding: 0 !important;
            line-height: 1.2 !important;
            height: auto !important;
            display: inline-block;
            max-height: 28px;
        }

        /* Remove right border for last column */
        td:last-child {
            border-right: none;
        }

        /* ================= Hover Effect ================= */
        tbody tr:hover {
            background: #f3f4f6;
        }

        /* ================= Mobile Adjustments ================= */
        @media (max-width: 768px) {

            tr,
            th,
            td {
                height: 30px !important;
                min-height: 30px !important;
                line-height: 30px !important;
            }

            th,
            td {
                padding: 0 6px !important;
            }

            td img {
                width: 24px !important;
                height: 24px !important;
            }

            .table-responsive {
                padding: 12px;
            }
        }


        .btn-outline-premium {
            background: transparent;
            border: 1.5px solid #2563eb;
            color: #2563eb;
            font-weight: 600;
            transition: all .2s ease;
            border-radius: 0;
            cursor: pointer;
        }

        .btn-outline-premium:hover {
            background: #2563eb;
            color: #fff;
        }

        .form-input-fixed {
            width: 100%;
            border: 1px solid #cbd5e1 !important;
            padding: .5rem .7rem;
            border-radius: 0;
            font-size: .85rem;
            outline: none;
        }

        .action-icon-btn {
            font-size: 1.25rem;
            padding: 0px !important;
            background: none;
            border: none;
            cursor: pointer;
        }

        .loader-row {
            text-align: center;
            padding: 2rem !important;
            color: #64748b;
            font-style: italic;
        }

        /* Search Input Styling */
        .search-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-icon {
            position: absolute;
            left: 10px;
            color: #94a3b8;
            font-size: 16px;
        }

        .search-input-premium {
            padding-left: 32px !important;
            border: 1px solid #e2e8f0 !important;
            background: #fcfcfc;
            width: 250px;
            transition: all 0.3s ease;
        }

        .search-input-premium:focus {
            width: 300px;
            border-color: #2563eb !important;
            background: #fff;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }

        /* Pagination Styling */
        .pagination-container {
            padding: 1rem;
            border-top: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fdfdfd;
        }

        .page-link-premium {
            padding: 5px 12px;
            border: 1px solid #e2e8f0;
            font-size: 11px;
            font-weight: bold;
            color: #64748b;
            transition: all 0.2s;
            background: white;
        }

        .page-link-premium:hover:not(.disabled) {
            border-color: #2563eb;
            color: #2563eb;
        }

        .page-link-premium.active {
            background: #2563eb;
            color: white;
            border-color: #2563eb;
        }

        .page-link-premium.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
    </style>

    <div class="main-view-container">
        <div class="max-w-full mx-auto w-full">
            <div class="bg-white border border-gray-200 p-2.5 sm:p-4 mb-4" style="border-radius: 0;">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">

                    <div class="w-full lg:w-auto">
                        <h2 id="pageHeader" class="text-[15px] sm:text-xl text-gray-800 font-normal leading-tight"></h2>
                        <div class="flex items-center text-slate-400 text-[12px] mt-1">
                            <span>School</span>
                            <i class="fas fa-chevron-right mx-1.5 text-[10px]"></i>
                            <span id="pageTitle" class="text-slate-500"></span>
                        </div>

                        <div class="relative w-full sm:w-64 mt-3 hidden lg:block">
                            <i class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" id="tableSearch" onkeyup="liveSearch()"
                                placeholder="Search Subject or Date..."
                                class="pl-8 pr-3 py-2 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                                style="border-radius: 0;" />
                        </div>
                    </div>

                    <div class="flex flex-row items-center gap-1 w-full lg:w-auto">
                        <button onclick="openFilterModal()"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Filter
                        </button>

                        <button onclick="document.getElementById('exportModal').classList.remove('hidden')"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Export
                        </button>

                        <button onclick="openRoutineModal()"
                            class="btn-outline-premium border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Create Routine
                        </button>
                    </div>
                </div>

                <div class="relative w-full mt-3 lg:hidden">
                    <i class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" onkeyup="document.getElementById('tableSearch').value = this.value; liveSearch()"
                        placeholder="Search Subject or Date..."
                        class="pl-8 pr-3 py-1.5 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                        style="border-radius: 0;" />
                </div>
            </div>

            {{-- Filter Modal --}}
            <div id="filterModal"
                class="premium-modal fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center p-12 sm:p-20"
                onclick="this.classList.add('hidden')">
                <div class="bg-white p-4 w-full max-w-[320px] modal-content-sharp shadow-2xl" style="border-radius: 0;"
                    onclick="event.stopPropagation()">

                    <div>
                        <h3
                            class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                            Routine filter
                        </h3>
                        <div class="h-[1px] w-full bg-gray-200 mt-2.5"></div>
                    </div>

                    <div class="mt-3 mb-4 space-y-3">
                        {{-- Class Filter --}}
                        <div class="relative">
                            <label class="text-[10px] text-gray-500 block mb-1">Class</label>
                            <div class="relative">
                                <select id="f_class" onchange="handleCascade(this, 'f_group')"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                    <option value="">All Classes</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Group Filter --}}
                        <div class="relative">
                            <label class="text-[10px] text-gray-500 block mb-1">Group</label>
                            <div class="relative">
                                <select id="f_group" onchange="handleCascade(this, 'f_section')"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                    <option value="">All Groups</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Section Filter --}}
                        <div class="relative">
                            <label class="text-[10px] text-gray-500 block mb-1">Section</label>
                            <div class="relative">
                                <select id="f_section" onchange="handleCascade(this, 'f_session')"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                    <option value="">All Sections</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Session Filter --}}
                        <div class="relative">
                            <label class="text-[10px] text-gray-500 block mb-1">Session</label>
                            <div class="relative">
                                <select id="f_session" onchange="loadFilterExams()"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                    <option value="">All Sessions</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Exam Filter --}}
                        <div class="relative">
                            <label class="text-[10px] text-gray-500 block mb-1">Exam</label>
                            <div class="relative">
                                <select id="f_exam"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                    <option value="">All Exams</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button onclick="resetFilters()"
                            class="btn-outline-secondary border border-gray-200 w-full text-[11px] capitalize flex items-center justify-center"
                            style="border-radius: 0; height: 32px;">Reset</button>
                        <button
                            onclick="fetchRoutines(1, true); document.getElementById('filterModal').classList.add('hidden');"
                            class="btn-outline-premium border border-gray-200 w-full text-[11px] capitalize flex items-center justify-center"
                            style="border-radius: 0; height: 32px;">Apply</button>
                    </div>
                </div>
            </div>

            {{-- Export Modal --}}
            <div id="exportModal"
                class="premium-modal fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center p-12 sm:p-20"
                onclick="this.classList.add('hidden')">
                <div class="bg-white p-4 w-auto min-w-[140px] modal-content-sharp shadow-2xl"
                    onclick="event.stopPropagation()">
                    <div class="flex flex-col gap-1.5">
                        <button onclick="exportData('pdf')"
                            class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap">
                            PDF
                        </button>
                        <button onclick="exportData('excel')"
                            class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap">
                            EXCEL
                        </button>
                        <button onclick="window.print()"
                            class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap">
                            PRINT
                        </button>
                        <button onclick="document.getElementById('exportModal').classList.add('hidden')"
                            class="mt-1 py-1.5 text-[10px] text-gray-400 hover:text-gray-600 w-full text-center border border-gray-200 transition-all">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-card">
                <div class="table-responsive">
                    <table class="min-w-[1000px]">
                        <thead>
                            <tr>
                                <th width="60">Sl</th>
                                <th>Date</th>
                                <th>Day Name</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Subject</th>
                                <th width="120" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="routineTableBody">
                            <tr>
                                <td colspan="7" class="loader-row">Loading routines...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="pagination-container" id="paginationControls">
                    <div class="text-[11px] text-gray-500 font-medium" id="paginationInfo">
                        0 of 0
                    </div>
                    <div class="flex items-center gap-1" id="paginationLinks">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Exam Routine Modal --}}
    <div id="routineModal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modalTitle"
        class="fixed inset-0 bg-gray-900/60 flex items-center justify-center hidden z-[100] px-8 sm:px-40 py-12 backdrop-blur-sm overflow-y-auto">

        <div
            class="bg-white w-full max-w-2xl modal-content-sharp shadow-2xl overflow-hidden flex flex-col my-auto max-h-[70vh] sm:max-h-[85vh] mx-auto border border-gray-100">

            {{-- Header --}}
            <div class="px-5 py-3 border-b flex justify-center items-center bg-white sticky top-0 z-10">
                <h3 id="modalTitle"
                    class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                    Create Exam Routine
                </h3>
            </div>

            <form id="routineForm" class="flex flex-col overflow-hidden m-0">
                <input type="hidden" id="routine_edit_id">

                {{-- Scrollable Content Area --}}
                <div class="overflow-y-auto custom-scrollbar p-4 sm:p-6 flex-grow bg-gray-50/30">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-4">

                        {{-- Schedule Section --}}
                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Date</label>
                            <input type="date" id="exam_date" name="exam_date"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                onchange="updateDayName()" style="border-radius: 0;">
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Day
                                Name</label>
                            <input type="text" id="day_name" name="day_name"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px] bg-gray-100/50"
                                readonly style="border-radius: 0;">
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Start
                                Time</label>
                            <input type="time" id="start_time" name="start_time"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                onchange="calculateHours()" style="border-radius: 0;">
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">End
                                Time</label>
                            <input type="time" id="end_time" name="end_time"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                onchange="calculateHours()" style="border-radius: 0;">
                        </div>

                        <div class="col-span-1 sm:col-span-2">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Total
                                Hours</label>
                            <input type="text" id="total_hours" name="total_hours"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px] bg-blue-50/30 text-blue-600 font-medium"
                                readonly style="border-radius: 0;">
                        </div>

                        {{-- Divider --}}
                        <div class="col-span-1 sm:col-span-2 mt-2 pt-4 border-t border-gray-200/60">
                            <label class="block text-[10px] capitalize tracking-normal text-blue-600 mb-1.5 font-medium">
                                Academic & Subject Details
                            </label>
                        </div>

                        {{-- Academic Selection --}}
                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Class</label>
                            <x-input.dropdown-select
                                id="class_name"
                                name="class_name"
                                placeholder="Select Class"
                                :options="[]"
                                add-button-id="openClassFromRoutineForm"
                                add-button-label="Add class"
                                add-button-target="classModal"
                            />
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Group</label>
                            <x-input.dropdown-select
                                id="group_name"
                                name="group_name"
                                placeholder="Select Group"
                                :options="[]"
                                add-button-id="openGroupFromRoutineForm"
                                add-button-label="Add group"
                                add-button-target="groupModal"
                            />
                        </div>

                        <div class="col-span-1">
                            <label
                                class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Section</label>
                            <x-input.dropdown-select
                                id="section_name"
                                name="section_name"
                                placeholder="Select Section"
                                :options="[]"
                                add-button-id="openSectionFromRoutineForm"
                                add-button-label="Add section"
                                add-button-target="sectionModal"
                            />
                        </div>

                        <div class="col-span-1">
                            <label
                                class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Session</label>
                            <x-input.dropdown-select
                                id="session_name"
                                name="session_name"
                                placeholder="Select Session"
                                :options="[]"
                                add-button-id="openSessionFromRoutineForm"
                                add-button-label="Add session"
                                add-button-target="sessionModal"
                            />
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Exam
                                Name</label>
                            <x-input.dropdown-select
                                id="exam_name"
                                name="exam_name"
                                placeholder="Select Exam"
                                :options="[]"
                                add-button-id="openExamFromRoutineForm"
                                add-button-label="Add exam"
                                add-button-target="examModal"
                            />
                        </div>

                        <div class="col-span-1">
                            <label
                                class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Subject</label>
                            <select id="subject_name" name="subject_name"
                                class="form-input-fixed w-full border border-blue-200 py-1.5 px-3 text-xs h-[32px] bg-blue-50/10"
                                style="border-radius: 0;"></select>
                        </div>

                    </div>
                </div>

                {{-- Footer Actions --}}
                <div
                    class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-white flex flex-row sm:justify-end gap-2 sticky bottom-0">
                    <button type="button" onclick="closeRoutineModal()"
                        class="w-1/2 sm:w-auto sm:px-8 h-[32px] btn-outline-secondary border border-gray-200 text-[10px] tracking-normal capitalize transition-all hover:bg-gray-50 flex items-center justify-center whitespace-nowrap"
                        style="border-radius: 0;">
                        Cancel
                    </button>
                    <button type="submit"
                        class="w-1/2 sm:w-auto sm:px-12 h-[32px] btn-outline-premium border border-gray-200 text-[10px] tracking-normal capitalize flex items-center justify-center whitespace-nowrap"
                        style="border-radius: 0;">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    @include('school.academic.class.partials.class-modal')
    @include('school.academic.group.partials.group-modal')
    @include('school.academic.section.partials.section-modal')
    @include('school.academic.session.partials.session-modal')
    @include('school.exam.exam_name.partials.exam-modal')

    @include('school.academic.class.partials.js.modal-open')
    @include('school.academic.group.partials.js.modal-open')
    @include('school.academic.section.partials.js.modal-open')
    @include('school.academic.session.partials.js.modal-open')
    @include('school.exam.exam_name.partials.js.modal-open')
    @include('school.academic.class.partials.js.modal-submit')
    @include('school.academic.group.partials.js.modal-submit')
    @include('school.academic.section.partials.js.modal-submit')
    @include('school.academic.session.partials.js.modal-submit')
    @include('school.exam.exam_name.partials.js.modal-submit')
    @include('school.academic.class.partials.js.error-validation')
    @include('school.academic.group.partials.js.error-validation')
    @include('school.academic.section.partials.js.error-validation')
    @include('school.academic.session.partials.js.error-validation')
    @include('school.exam.exam_name.partials.js.error-validation')

    <script>
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;

        let currentPage = 1;

        // --- Live Search Implementation ---
        function liveSearch() {
            const input = document.getElementById("tableSearch");
            const filter = input.value.toUpperCase();
            const table = document.getElementById("routineTable");
            const tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) {
                let found = false;
                const tds = tr[i].getElementsByTagName("td");
                for (let j = 0; j < tds.length; j++) {
                    if (tds[j]) {
                        const txtValue = tds[j].textContent || tds[j].innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }
                tr[i].style.display = found ? "" : "none";
            }
        }

        // Utilities
        function formatTime12h(timeStr) {
            if (!timeStr) return '';
            const [hours, minutes] = timeStr.split(':');
            let h = parseInt(hours);
            const ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12 || 12;
            return `${h}:${minutes} ${ampm}`;
        }

        function formatDateDDMMYYYY(dateStr) {
            if (!dateStr) return '';
            const [y, m, d] = dateStr.split('-');
            return `${d}/${m}/${y}`;
        }

        function updateDayName() {
            const dateInput = document.getElementById('exam_date').value;
            if (!dateInput) return;
            const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            const date = new Date(dateInput);
            document.getElementById('day_name').value = days[date.getUTCDay()];
        }

        function calculateHours() {
            const start = document.getElementById('start_time').value;
            const end = document.getElementById('end_time').value;
            if (start && end) {
                const s = new Date(`2026-01-01 ${start}`);
                const e = new Date(`2026-01-01 ${end}`);
                let diff = (e - s) / (1000 * 60 * 60);
                if (diff < 0) diff += 24;
                document.getElementById('total_hours').value = diff.toFixed(2) + ' Hrs';
            }
        }

        // Data Loading
        async function loadInitialData() {
            try {
                const res = await axios.get('/api/get-school-classes');
                let opts = '<option value="">Select Class</option>';
                res.data.data.forEach(c => opts +=
                    `<option value="${c.class_name}" data-id="${c.id}">${c.class_name}</option>`);
                populateComponentDropdown('class_nameMenu', res.data.data || [], 'class_name', 'class_name', {
                    id: 'id'
                });
                setComponentDropdownValue('class_name', '', 'Select Class');
                document.getElementById('f_class').innerHTML = opts.replace('Select', 'All');
            } catch (e) {
                console.error("Initial Load Error", e);
            }
        }

        function populateComponentDropdown(menuId, data, valueField, labelField, dataFields = {}) {
            const menu = document.getElementById(menuId);
            if (!menu) return;

            menu.innerHTML = '';

            data.forEach(item => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'dropdown-select-option m-0 flex min-h-6 w-full items-center border-0 bg-white px-3 py-1 text-left text-[11px] font-normal leading-tight transition-colors hover:bg-slate-100 text-slate-800';
                btn.dataset.value = String(item[valueField] ?? '');
                btn.textContent = item[labelField] ?? '';
                btn.setAttribute('role', 'option');
                btn.setAttribute('aria-selected', 'false');
                btn.setAttribute('data-dropdown-select-option', '');

                Object.entries(dataFields).forEach(([dataKey, sourceKey]) => {
                    btn.dataset[`option${dataKey.charAt(0).toUpperCase()}${dataKey.slice(1)}`] = String(item[sourceKey] ?? '');
                });

                btn.addEventListener('click', function() {
                    const root = menu.closest('[data-dropdown-select]');
                    const input = root?.querySelector('[data-dropdown-select-input]');
                    const label = root?.querySelector('[data-dropdown-select-label]');

                    if (!root || !input || !label) return;

                    input.value = this.dataset.value || '';
                    label.textContent = this.textContent.trim();

                    menu.querySelectorAll('[data-dropdown-select-option]').forEach(option => {
                        const selected = option === this;
                        option.classList.toggle('bg-slate-100', selected);
                        option.classList.toggle('text-slate-900', selected);
                        option.classList.toggle('text-slate-800', !selected);
                        option.setAttribute('aria-selected', String(selected));
                    });

                    menu.classList.add('hidden');
                    root.querySelector('[data-dropdown-select-button]')?.setAttribute('aria-expanded', 'false');
                    root.querySelector('[data-dropdown-select-button] i')?.classList.remove('rotate-180');
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                });

                menu.appendChild(btn);
            });
        }

        function setComponentDropdownValue(dropdownId, value, label) {
            const input = document.getElementById(dropdownId);
            const labelEl = document.querySelector(`#${dropdownId}Button [data-dropdown-select-label]`);
            const menu = document.getElementById(`${dropdownId}Menu`);

            if (input) input.value = value || '';
            if (labelEl) labelEl.textContent = label || labelEl.dataset.placeholder || 'Select...';

            menu?.querySelectorAll('[data-dropdown-select-option]').forEach(option => {
                const selected = String(option.dataset.value || '') === String(value || '');
                option.classList.toggle('bg-slate-100', selected);
                option.classList.toggle('text-slate-900', selected);
                option.classList.toggle('text-slate-800', !selected);
                option.setAttribute('aria-selected', String(selected));
            });
        }

        function populateDropdown(menuId, data, valueField, labelField) {
            populateComponentDropdown(menuId, data, valueField, labelField);
        }

        function setDropdownValue(dropdownId, value, label) {
            setComponentDropdownValue(dropdownId, value, label);
        }

        function getSelectedDataId(elementOrId) {
            const el = typeof elementOrId === 'string' ? document.getElementById(elementOrId) : elementOrId;
            if (!el) return '';

            if (el.matches?.('[data-dropdown-select-input]')) {
                const menu = document.getElementById(`${el.id}Menu`);
                const selected = menu?.querySelector(`[data-value="${CSS.escape(el.value)}"]`);
                return selected?.dataset.optionId || '';
            }

            return el.options?.[el.selectedIndex]?.getAttribute('data-id') || '';
        }

        // Reset downstream dropdowns to empty placeholder
        function resetDropdown(id, label, isFilter = false) {
            const element = document.getElementById(id);
            const placeholder = `${isFilter ? 'All' : 'Select'} ${label}`;

            if (!element) return;

            if (element.matches?.('[data-dropdown-select-input]')) {
                populateComponentDropdown(`${id}Menu`, [], 'value', 'label');
                setComponentDropdownValue(id, '', placeholder);
                return;
            }

            element.innerHTML = `<option value="">${placeholder}</option>`;
        }

        // Updated Cascade Logic
        async function handleCascade(el, next) {
            const isFilter = next.startsWith('f_');
            const id = getSelectedDataId(el);

            // Reset all fields downstream of the changed one
            if (next === 'group' || next === 'f_group') {
                resetDropdown(isFilter ? 'f_group'   : 'group_name',   'Group',   isFilter);
                resetDropdown(isFilter ? 'f_section' : 'section_name', 'Section', isFilter);
                resetDropdown(isFilter ? 'f_session' : 'session_name', 'Session', isFilter);
                if (!isFilter) {
                    resetDropdown('exam_name',    'Exam');
                    resetDropdown('subject_name', 'Subject');
                }
            } else if (next === 'section' || next === 'f_section') {
                resetDropdown(isFilter ? 'f_section' : 'section_name', 'Section', isFilter);
                resetDropdown(isFilter ? 'f_session' : 'session_name', 'Session', isFilter);
                if (!isFilter) {
                    resetDropdown('exam_name',    'Exam');
                    resetDropdown('subject_name', 'Subject');
                }
            } else if (next === 'session' || next === 'f_session') {
                resetDropdown(isFilter ? 'f_session' : 'session_name', 'Session', isFilter);
                if (!isFilter) {
                    resetDropdown('exam_name',    'Exam');
                    resetDropdown('subject_name', 'Subject');
                }
            }

            if (!id) return;

            // NEW: Get the class_id for session filtering mandate
            const classEl = document.getElementById(isFilter ? 'f_class' : 'class_name');
            const classId = getSelectedDataId(classEl);

            try {
                if (next === 'group' || next === 'f_group') {
                    const res = await axios.get(`/api/get-school-groups?class_id=${id}`);
                    fillDropdown(isFilter ? 'f_group' : 'group_name', res.data.data, 'group_name', 'Group', isFilter);
                } else if (next === 'section' || next === 'f_section') {
                    const res = await axios.get(`/api/get-school-sections?group_id=${id}`);
                    fillDropdown(isFilter ? 'f_section' : 'section_name', res.data.data, 'section_name', 'Section', isFilter);
                } else if (next === 'session' || next === 'f_session') {
                    const res = await axios.get(`/api/get-school-sessions?class_id=${classId}&section_id=${id}`);
                    fillDropdown(isFilter ? 'f_session' : 'session_name', res.data.data, 'session_year', 'Session', isFilter);
                }
            } catch (e) {
                console.error("Cascade Error", e);
            }
        }

        function fillDropdown(target, data, field, label, isFilter = false) {
            const element = document.getElementById(target);
            const placeholder = `${isFilter ? 'All' : 'Select'} ${label}`;

            if (!element) return;

            if (element.matches?.('[data-dropdown-select-input]')) {
                populateComponentDropdown(`${target}Menu`, data, field, field, {
                    id: 'id'
                });
                setComponentDropdownValue(target, '', placeholder);
                return;
            }

            let opts = `<option value="">${placeholder}</option>`;
            data.forEach(item => opts += `<option value="${item[field]}" data-id="${item.id}">${item[field]}</option>`);
            element.innerHTML = opts;
        }

        async function loadExamAndSubject() {
            const classSelect = document.getElementById('class_name');
            const className = classSelect.value;
            const groupName = document.getElementById('group_name').value;
            const sectionName = document.getElementById('section_name').value;
            const sessionName = document.getElementById('session_name').value;
            const classId = getSelectedDataId(classSelect);

            // Always reset exam and subject first
            resetDropdown('exam_name',    'Exam');
            resetDropdown('subject_name', 'Subject');

            if (!className || !sessionName) return;

            // Build exam query params including group and section for precise filtering
            const examParams = new URLSearchParams({ class_name: className, session_name: sessionName });
            if (groupName) examParams.append('group_name', groupName);
            if (sectionName) examParams.append('section_name', sectionName);

            try {
                const [examRes, subRes] = await Promise.all([
                    axios.get(`/api/get-school-exams?${examParams.toString()}`),
                    axios.get(`/api/get-school-subjects?class_id=${classId}`)
                ]);
                fillDropdown('exam_name', examRes.data.data, 'exam_name', 'Exam');
                fillDropdown('subject_name', subRes.data.data, 'subject_name', 'Subject');
            } catch (e) {
                console.error("Exam/Sub Load Error", e);
            }
        }

        async function loadFilterExams() {
            const className = document.getElementById('f_class').value;
            const sessionName = document.getElementById('f_session').value;
            const res = await axios.get(`/api/get-school-exams?class_name=${className}&session_name=${sessionName}`);
            fillDropdown('f_exam', res.data.data, 'exam_name', 'Exam', true);
        }

        document.addEventListener('school:class-saved', async function (event) {
            const { classItem, returnModalId, isNew } = event.detail || {};
            if (returnModalId !== 'routineModal' || !isNew || !classItem?.id) {
                return;
            }
            event.preventDefault();

            try {
                const response = await axios.get('/api/get-school-classes');
                populateComponentDropdown('class_nameMenu', response.data.data || [], 'class_name', 'class_name', { id: 'id' });
                setComponentDropdownValue('class_name', classItem.class_name, classItem.class_name);
                resetDropdown('group_name', 'Group');
                resetDropdown('section_name', 'Section');
                resetDropdown('session_name', 'Session');
                resetDropdown('exam_name', 'Exam');
                document.getElementById('subject_name').innerHTML = '<option value="">Select Subject</option>';
            } finally {
                document.getElementById('routineModal')?.classList.remove('hidden');
            }
        });

        document.addEventListener('school:group-saved', async function (event) {
            const { groupItem, returnModalId, isNew } = event.detail || {};
            if (returnModalId !== 'routineModal' || !isNew || !groupItem?.id || !groupItem?.class_id) {
                return;
            }
            event.preventDefault();

            try {
                const classResponse = await axios.get('/api/get-school-classes');
                const classes = classResponse.data.data || [];
                populateComponentDropdown('class_nameMenu', classes, 'class_name', 'class_name', { id: 'id' });
                const selectedClass = classes.find(item => String(item.id) === String(groupItem.class_id));
                if (selectedClass) {
                    setComponentDropdownValue('class_name', selectedClass.class_name, selectedClass.class_name);
                }

                const groupResponse = await axios.get(`/api/get-school-groups?class_id=${groupItem.class_id}`);
                populateComponentDropdown('group_nameMenu', groupResponse.data.data || [], 'group_name', 'group_name', { id: 'id' });
                setComponentDropdownValue('group_name', groupItem.group_name, groupItem.group_name);
                resetDropdown('section_name', 'Section');
                resetDropdown('session_name', 'Session');
                resetDropdown('exam_name', 'Exam');
                document.getElementById('subject_name').innerHTML = '<option value="">Select Subject</option>';
            } finally {
                document.getElementById('routineModal')?.classList.remove('hidden');
            }
        });

        document.addEventListener('school:section-saved', async function (event) {
            const { sectionItem, returnModalId, isNew } = event.detail || {};
            if (returnModalId !== 'routineModal' || !isNew || !sectionItem?.id || !sectionItem?.class_id || !sectionItem?.group_id) {
                return;
            }
            event.preventDefault();

            try {
                const classResponse = await axios.get('/api/get-school-classes');
                const classes = classResponse.data.data || [];
                populateComponentDropdown('class_nameMenu', classes, 'class_name', 'class_name', { id: 'id' });
                const selectedClass = classes.find(item => String(item.id) === String(sectionItem.class_id));
                if (selectedClass) {
                    setComponentDropdownValue('class_name', selectedClass.class_name, selectedClass.class_name);
                }

                const groupResponse = await axios.get(`/api/get-school-groups?class_id=${sectionItem.class_id}`);
                populateComponentDropdown('group_nameMenu', groupResponse.data.data || [], 'group_name', 'group_name', { id: 'id' });
                const selectedGroup = groupResponse.data.data.find(item => String(item.id) === String(sectionItem.group_id));
                if (selectedGroup) {
                    setComponentDropdownValue('group_name', selectedGroup.group_name, selectedGroup.group_name);
                }

                const sectionResponse = await axios.get(`/api/get-school-sections?class_id=${sectionItem.class_id}&group_id=${sectionItem.group_id}`);
                populateComponentDropdown('section_nameMenu', sectionResponse.data.data || [], 'section_name', 'section_name', { id: 'id' });
                setComponentDropdownValue('section_name', sectionItem.section_name, sectionItem.section_name);
                resetDropdown('session_name', 'Session');
                resetDropdown('exam_name', 'Exam');
                document.getElementById('subject_name').innerHTML = '<option value="">Select Subject</option>';
            } finally {
                document.getElementById('routineModal')?.classList.remove('hidden');
            }
        });

        document.addEventListener('school:session-saved', async function (event) {
            const { sessionItem, returnModalId, isNew } = event.detail || {};
            if (returnModalId !== 'routineModal' || !isNew || !sessionItem?.id || !sessionItem?.class_id || !sessionItem?.group_id || !sessionItem?.section_id) {
                return;
            }
            event.preventDefault();

            try {
                const classResponse = await axios.get('/api/get-school-classes');
                const classes = classResponse.data.data || [];
                populateComponentDropdown('class_nameMenu', classes, 'class_name', 'class_name', { id: 'id' });
                const selectedClass = classes.find(item => String(item.id) === String(sessionItem.class_id));
                if (selectedClass) {
                    setDropdownValue('class_name', selectedClass.class_name, selectedClass.class_name);
                }

                const groupResponse = await axios.get(`/api/get-school-groups?class_id=${sessionItem.class_id}`);
                populateComponentDropdown('group_nameMenu', groupResponse.data.data || [], 'group_name', 'group_name', { id: 'id' });
                const selectedGroup = groupResponse.data.data.find(item => String(item.id) === String(sessionItem.group_id));
                if (selectedGroup) {
                    setDropdownValue('group_name', selectedGroup.group_name, selectedGroup.group_name);
                }

                const sectionResponse = await axios.get(`/api/get-school-sections?class_id=${sessionItem.class_id}&group_id=${sessionItem.group_id}`);
                populateComponentDropdown('section_nameMenu', sectionResponse.data.data || [], 'section_name', 'section_name', { id: 'id' });
                const selectedSection = sectionResponse.data.data.find(item => String(item.id) === String(sessionItem.section_id));
                if (selectedSection) {
                    setDropdownValue('section_name', selectedSection.section_name, selectedSection.section_name);
                }

                const sessionResponse = await axios.get(`/api/get-school-sessions?class_id=${sessionItem.class_id}&section_id=${sessionItem.section_id}`);
                populateComponentDropdown('session_nameMenu', sessionResponse.data.data || [], 'session_year', 'session_year', { id: 'id' });
                setDropdownValue('session_name', sessionItem.session_year, sessionItem.session_year);
                resetDropdown('exam_name', 'Exam');
                document.getElementById('subject_name').innerHTML = '<option value="">Select Subject</option>';
                await loadExamAndSubject();
            } finally {
                document.getElementById('routineModal')?.classList.remove('hidden');
            }
        });

        document.addEventListener('school:exam-saved', async function (event) {
            const { examItem, returnModalId } = event.detail || {};
            if (returnModalId !== 'routineModal' || !examItem?.id) {
                return;
            }
            event.preventDefault();

            try {
                await loadExamAndSubject();
                setComponentDropdownValue('exam_name', examItem.exam_name, examItem.exam_name);
            } finally {
                document.getElementById('routineModal')?.classList.remove('hidden');
            }
        });

        // CRUD Operations
        async function fetchRoutines(page = 1, isFiltering = false) {
            currentPage = page;
            const tbody = document.getElementById('routineTableBody');
            tbody.innerHTML = '<tr><td colspan="7" class="loader-row text-center py-4">Loading...</td></tr>';

            try {
                const params = new URLSearchParams({
                    page: page,
                    class_name: document.getElementById('f_class').value,
                    group_name: document.getElementById('f_group').value,
                    section_name: document.getElementById('f_section').value,
                    session_name: document.getElementById('f_session').value,
                    exam_name: document.getElementById('f_exam').value
                });

                const res = await axios.get(`/api/school-exam-routines?${params.toString()}`);
                const data = res.data;
                const items = data.data;

                tbody.innerHTML = '';
                if (!items || items.length === 0) {
                    tbody.innerHTML =
                        '<tr><td colspan="7" class="text-center py-4 text-gray-400">No routines found matching the criteria.</td></tr>';
                } else {
                    items.forEach((item, i) => {
                        const sl = (data.current_page - 1) * data.per_page + (i + 1);
                        tbody.innerHTML += `
                            <tr>
                                <td>${sl}</td>
                                <td>${formatDateDDMMYYYY(item.exam_date)}</td>
                                <td>${item.day_name}</td>
                                <td>${formatTime12h(item.start_time)}</td>
                                <td>${formatTime12h(item.end_time)}</td>
                                <td class="text-gray-700">${item.subject_name}</td>
                                <td class="text-center">
                                    <div class="flex justify-center gap-3">
                                    <button onclick="editRoutine(${item.id})" class="action-icon-btn text-blue-500"><i class="far fa-edit" style="font-size: 15px;"></i></button>
                                    <button onclick="deleteRoutine(${item.id})" class="action-icon-btn text-red-400"><i class="far fa-trash-alt" style="font-size: 15px;"></i></button>
                                    </div>
                                </td>
                            </tr>`;
                    });
                }
                renderPagination(data);
                document.getElementById('tableSearch').value = '';
                if (isFiltering) closeFilterModal();
            } catch (err) {
                tbody.innerHTML =
                    '<tr><td colspan="7" class="text-center py-4 text-red-500">Failed to load data.</td></tr>';
            }
        }

        function renderPagination(data) {
            const info = document.getElementById('paginationInfo');
            const links = document.getElementById('paginationLinks');
            if (!data.total || data.total === 0) {
                info.innerText = '0 of 0';
                links.innerHTML = '';
                return;
            }
            info.innerText = `${data.to} of ${data.total}`;
            let html =
                `<button onclick="fetchRoutines(${data.current_page - 1})" class="page-link-premium ${data.current_page === 1 ? 'disabled' : ''}" ${data.current_page === 1 ? 'disabled' : ''}><i class="mdi mdi-chevron-left"></i></button>`;
            for (let i = 1; i <= data.last_page; i++) {
                if (i === 1 || i === data.last_page || (i >= data.current_page - 1 && i <= data.current_page + 1)) {
                    html +=
                        `<button onclick="fetchRoutines(${i})" class="page-link-premium ${i === data.current_page ? 'active' : ''}">${i}</button>`;
                } else if (i === data.current_page - 2 || i === data.current_page + 2) {
                    html += `<span class="px-2 text-gray-400 text-xs">...</span>`;
                }
            }
            html +=
                `<button onclick="fetchRoutines(${data.current_page + 1})" class="page-link-premium ${data.current_page === data.last_page ? 'disabled' : ''}" ${data.current_page === data.last_page ? 'disabled' : ''}><i class="mdi mdi-chevron-right"></i></button>`;
            links.innerHTML = html;
        }

        // Updated Edit Logic
        async function editRoutine(id) {
            try {
                const res = await axios.get(`/api/school-exam-routines/${id}`);
                const data = res.data;

                document.getElementById('routine_edit_id').value = data.id;
                document.getElementById('exam_date').value = data.exam_date;
                document.getElementById('day_name').value = data.day_name;
                document.getElementById('start_time').value = data.start_time;
                document.getElementById('end_time').value = data.end_time;
                document.getElementById('total_hours').value = data.total_hours;

                const classSelect = document.getElementById('class_name');
                setComponentDropdownValue('class_name', data.class_name, data.class_name || 'Select Class');
                // Important: Trigger cascade and WAIT for it
                await handleCascade(classSelect, 'group');

                const groupSelect = document.getElementById('group_name');
                setComponentDropdownValue('group_name', data.group_name, data.group_name || 'Select Group');
                await handleCascade(groupSelect, 'section');

                const sectionSelect = document.getElementById('section_name');
                setComponentDropdownValue('section_name', data.section_name, data.section_name || 'Select Section');

                // MODIFIED: Fetch sessions with explicit class_id
                const classId = getSelectedDataId(classSelect);
                const sectionId = getSelectedDataId(sectionSelect);
                const sessRes = await axios.get(`/api/get-school-sessions?class_id=${classId}&section_id=${sectionId}`);
                fillDropdown('session_name', sessRes.data.data, 'session_year', 'Session');

                setComponentDropdownValue('session_name', data.session_name, data.session_name || 'Select Session');

                await loadExamAndSubject();
                setComponentDropdownValue('exam_name', data.exam_name, data.exam_name || 'Select Exam');
                document.getElementById('subject_name').value = data.subject_name;

                document.getElementById('modalTitle').innerText = "Edit Exam Routine";
                openRoutineModal();
            } catch (e) {
                console.error("Edit Load Error", e);
                Swal.fire('Error', 'Could not load routine data', 'error');
            }
        }

        async function deleteRoutine(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Delete this routine entry?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                confirmButtonText: 'Yes, delete it!'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        await axios.delete(`/api/school-exam-routines/${id}`);
                        Toastify({
                            text: "Routine Deleted",
                            style: {
                                background: "#ef4444"
                            }
                        }).showToast();
                        fetchRoutines(currentPage);
                    } catch (e) {
                        console.error("Delete Error", e);
                    }
                }
            });
        }

        document.getElementById('routineForm').onsubmit = async function(e) {
            e.preventDefault();
            const id = document.getElementById('routine_edit_id').value;
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());

            try {
                const req = id ? axios.put(`/api/school-exam-routines/${id}`, data) : axios.post(
                    '/api/school-exam-routines', data);
                await req;
                Toastify({
                    text: id ? "Routine Updated" : "Routine Created",
                    style: {
                        background: "#2563eb"
                    }
                }).showToast();
                closeRoutineModal();
                fetchRoutines(id ? currentPage : 1);
            } catch (err) {
                const errors = err.response?.data?.errors;
                const message = err.response?.data?.message || 'An unexpected error occurred.';

                if (errors) {
                    const formatted = Object.values(errors).flat().join('<br>');
                    Swal.fire({
                        title: 'Validation Error',
                        html: formatted,
                        icon: 'warning'
                    });
                } else {
                    Swal.fire('Error', message, 'error');
                }
            }
        };

        function resetFilters() {
            ['f_class', 'f_group', 'f_section', 'f_session', 'f_exam'].forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = "";
            });
            fetchRoutines(1, true);
        }

        function exportData(type) {
            const params = new URLSearchParams({
                type,
                search: document.getElementById('tableSearch').value,
                class_name: document.getElementById('f_class').value,
                group_name: document.getElementById('f_group').value,
                section_name: document.getElementById('f_section').value,
                session_name: document.getElementById('f_session').value,
                exam_name: document.getElementById('f_exam').value
            });
            window.location.href = `/api/school-exam-routines-export?${params.toString()}`;
            document.getElementById('exportModal').classList.add('hidden');
        }

        function openRoutineModal() {
            if (!document.getElementById('routine_edit_id').value) {
                document.getElementById('routineForm').reset();
                setComponentDropdownValue('class_name', '', 'Select Class');
                document.getElementById('modalTitle').innerText = "Create Exam Routine";
            }
            document.getElementById('routineModal').classList.remove('hidden');
        }

        function closeRoutineModal() {
            document.getElementById('routineModal').classList.add('hidden');
            document.getElementById('routineForm').reset();
            setComponentDropdownValue('class_name', '', 'Select Class');
            document.getElementById('routine_edit_id').value = "";
        }

        function openFilterModal() {
            document.getElementById('filterModal').classList.remove('hidden');
        }

        function closeFilterModal() {
            document.getElementById('filterModal').classList.add('hidden');
        }

        window.onload = () => {
            loadInitialData();
            document.getElementById('class_name')?.addEventListener('change', function() {
                handleCascade(this, 'group');
            });
            fetchRoutines();
        };
    </script>
@endsection
