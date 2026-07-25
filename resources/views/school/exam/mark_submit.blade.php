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
        border-left: none;
        border-right: none;
    }

    /* ================= Table Container ================= */
    .table-responsive {
        width: 100% !important;
        overflow-x: auto !important;
        display: block !important;
        background: white !important;
        padding: 15px !important;
    }

    /* ================= Custom Scrollbar ================= */
    .table-responsive::-webkit-scrollbar {
        height: 6px !important;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f8fafc !important;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #cbd5e1 !important;
        border-radius: 0px !important;
    }

    /* ================= Table Core ================= */
    table {
        width: 100% !important;
        border-collapse: collapse !important;
        table-layout: auto !important;
        border: 1px solid #d1d5db !important;
        font-size: 11px !important;
    }

    /* ================= Table Header ================= */
    th {
        padding: 0 12px !important;
        height: 34px !important;
        line-height: 34px !important;
        white-space: nowrap !important;
        background: #f8fafc !important;
        border-bottom: 1px solid #d1d5db !important;
        border-right: 1px solid #d1d5db !important;
        color: #374151 !important;
        font-weight: 800 !important;
        vertical-align: middle !important;
        text-align: left !important;
        /* Only first letter capitalized */
        text-transform: capitalize !important;
        letter-spacing: 0.01em !important;
    }

    th:last-child {
        border-right: none !important;
    }

    /* ================= Table Body ================= */
    tr {
        height: 32px !important;
    }

    td {
        padding: 0 12px !important;
        vertical-align: middle !important;
        border-bottom: 1px solid #d1d5db !important;
        border-right: 1px solid #d1d5db !important;
        font-size: 11px !important;
        color: #4b5563 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
    }

    td:last-child {
        border-right: none !important;
    }

    tbody tr:hover {
        background: #f9fafb !important;
    }

    /* ================= Pagination Bar (Balanced Height) ================= */
    .pagination-bar {
        padding: 0.6rem 1rem !important;
        border: 1px solid #d1d5db !important;
        border-top: none !important;
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        background: #ffffff !important;
        min-height: 44px !important;
    }

    .pagination-btn {
        height: 26px !important;
        min-width: 26px !important;
        padding: 0 8px !important;
        border: 1px solid #e2e8f0 !important;
        background: white !important;
        cursor: pointer !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 10px !important;
        font-weight: 900 !important;
        color: #64748b !important;
        border-radius: 0 !important;
        /* Sharp Brutalism Corners */
        text-transform: uppercase !important;
        transition: all 0.1s ease !important;
    }

    .pagination-btn.active {
        background: #2563eb !important;
        color: white !important;
        border-color: #2563eb !important;
    }

    .pagination-btn:hover:not(:disabled):not(.active) {
        border-color: #2563eb !important;
        color: #2563eb !important;
    }

    .pagination-btn:disabled {
        opacity: 0.3 !important;
        cursor: not-allowed !important;
        background: #f1f5f9 !important;
    }

    #paginationInfo {
        font-size: 9px !important;
        font-weight: 800 !important;
        color: #94a3b8 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
    }

    /* ================= Mobile Adjustments ================= */
    @media (max-width: 768px) {

        th,
        td {
            padding: 0 8px !important;
            height: 30px !important;
        }

        .pagination-bar {
            min-height: 38px !important;
            padding: 0.4rem 0.75rem !important;
        }

        .pagination-btn {
            height: 24px !important;
            min-width: 24px !important;
        }
    }

    .form-input-fixed {
        width: 100%;
        border: 1px solid #cbd5e1 !important;
        padding: .5rem .7rem;
        border-radius: 0;
        font-size: .85rem;
        outline: none;
        background: #fff;
    }

    .form-input-fixed:focus {
        border-color: #2563eb !important;
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

    .search-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .search-icon {
        position: absolute;
        left: 12px;
        color: #94a3b8;
        font-size: 16px;
        z-index: 10;
    }

    .search-input-premium {
        padding-left: 38px !important;
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

    .step-hidden {
        display: none;
    }

    .action-icon {
        font-size: 18px;
        cursor: pointer;
        transition: color 0.2s;
    }

    .action-icon:hover {
        color: #2563eb;
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 5px;
        /* Vertical width */
        height: 5px;
        /* Horizontal height - Must be same */
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #d1d5db;
        /* gray-300 */
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
        /* gray-400 */
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
                        <input type="text" id="header_search" placeholder="Student ID/Name..."
                            onkeyup="if(event.key === 'Enter') fetchTable(1)"
                            class="pl-8 pr-3 py-2 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                            style="border-radius: 0;" />
                    </div>
                </div>

                <div class="flex flex-row items-center gap-1 w-full lg:w-auto">
                    <button onclick="document.getElementById('filterModal').classList.remove('hidden')"
                        class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                        Filter
                    </button>

                    <button onclick="document.getElementById('exportModal').classList.remove('hidden')"
                        class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                        Export
                    </button>

                    <button onclick="openMarkModal()"
                        class="btn-outline-premium border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                        Add Mark
                    </button>
                </div>
            </div>

            <div class="relative w-full mt-3 lg:hidden">
                <i class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="header_search_mobile" placeholder="Student ID/Name..."
                    onkeyup="if(event.key === 'Enter') { document.getElementById('header_search').value = this.value; fetchTable(1); }"
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
                        Mark filter
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
                            </select>
                            <div
                                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                <i class="fas fa-chevron-down text-[9px]"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Subject Filter --}}
                    <div class="relative">
                        <label class="text-[10px] text-gray-500 block mb-1">Subject</label>
                        <div class="relative">
                            <select id="f_subject"
                                class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                style="border-radius: 0; height: 32px;">
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
                    <button onclick="applyFilters(); document.getElementById('filterModal').classList.add('hidden');"
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
                            <th width="50">Sl</th>
                            <th>Class</th>
                            <th>Subject</th>
                            <th>Exam</th>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Mark</th>
                            <th>Grade</th>
                            <th>Point</th>
                            <th width="100" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="markTableBody" class="bg-white divide-y divide-gray-100">
                        <tr>
                            <td colspan="10" class="text-center py-8 text-gray-400">Loading data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between p-4 bg-white border-t border-gray-100" id="paginationArea">
                <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest" id="paginationInfo">
                    0 of 0
                </div>
                <div class="flex items-center gap-1" id="paginationControls">
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Mark Submit Modal --}}
<div id="markModal"
    class="fixed inset-0 bg-gray-900/60 flex items-center justify-center hidden z-[100] px-8 sm:px-40 py-12 backdrop-blur-sm overflow-y-auto">

    <div
        class="bg-white w-full max-w-4xl modal-content-sharp shadow-2xl overflow-hidden flex flex-col my-auto max-h-[70vh] sm:max-h-[85vh] mx-auto border border-gray-100">

        {{-- Header --}}
        <div class="px-5 py-3 border-b flex justify-center items-center bg-white sticky top-0 z-10">
            <h3 id="modalTitle"
                class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                Step 1: Exam Configuration
            </h3>
        </div>

        {{-- Step 1 Content --}}
        <div id="step1" class="flex flex-col overflow-hidden m-0">
            <div class="overflow-y-auto custom-scrollbar p-4 sm:p-6 flex-grow bg-gray-50/30">
                <input type="hidden" id="edit_id">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-5 gap-y-4">
                    <div>
                        <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Class</label>
                        <select id="m_class" onchange="handleCascade(this, 'm_group')"
                            class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                            style="border-radius: 0;"></select>
                    </div>
                    <div>
                        <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Group</label>
                        <select id="m_group" onchange="handleCascade(this, 'm_section')"
                            class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                            style="border-radius: 0;"></select>
                    </div>
                    <div>
                        <label
                            class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Section</label>
                        <select id="m_section" onchange="handleCascade(this, 'm_session')"
                            class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                            style="border-radius: 0;"></select>
                    </div>
                    <div>
                        <label
                            class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Session</label>
                        <select id="m_session"
                            class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                            style="border-radius: 0;"></select>
                        <input type="hidden" id="action_type" value="exam configure" name="action_type">
                    </div>
                    <div>
                        <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Exam
                            Name</label>
                        <select id="m_exam"
                            class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                            style="border-radius: 0;"></select>
                    </div>
                    <div>
                        <label
                            class="block text-[10px] capitalize tracking-normal text-blue-600 mb-1.5 font-medium">Subject</label>
                        <select id="m_subject"
                            class="form-input-fixed w-full border border-blue-200 py-1.5 px-3 text-xs text-gray-700 h-[32px]"
                            style="border-radius: 0;"></select>
                    </div>
                </div>
            </div>

            {{-- Step 1 Footer --}}
            <div
                class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-white flex flex-row sm:justify-end gap-2 sticky bottom-0 z-10">
                <button type="button" onclick="closeMarkModal()"
                    class="w-1/2 sm:w-auto sm:px-8 h-[32px] btn-outline-secondary border border-gray-200 text-[10px] tracking-normal capitalize transition-all hover:bg-gray-50 flex items-center justify-center whitespace-nowrap"
                    style="border-radius: 0;">
                    Cancel
                </button>
                <button type="button" id="nextBtn" onclick="goToStep2()"
                    class="w-1/2 sm:w-auto sm:px-12 h-[32px] btn-outline-premium border border-gray-200 text-[10px] tracking-normal capitalize flex items-center justify-center whitespace-nowrap"
                    style="border-radius: 0;">
                    Next
                </button>
            </div>
        </div>

        {{-- Step 2 Content --}}
        <div id="step2" class="step-hidden flex flex-col overflow-hidden m-0">
            {{-- py-4 ensures equal top and bottom space inside the gray area --}}
            <div class="px-4 sm:px-6 py-4 flex-grow bg-gray-50/30 flex flex-col overflow-hidden">
                {{-- Table container: overflow-auto + custom-scrollbar handles both axes with identical design --}}
                <div class="border border-gray-200 bg-white overflow-auto custom-scrollbar max-h-[400px]"
                    style="border-radius: 0;">
                    <table class="w-full text-left border-collapse min-w-[600px]">
                        <thead class="sticky top-0 z-10 bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-2 text-[10px] font-bold uppercase text-gray-500">ID</th>
                                <th class="px-4 py-2 text-[10px] font-bold uppercase text-gray-500">Name</th>
                                <th width="120" class="px-4 py-2 text-[10px] font-bold uppercase text-gray-500">
                                    Mark</th>
                                <th class="px-4 py-2 text-[10px] font-bold uppercase text-gray-500">Grade</th>
                                <th class="px-4 py-2 text-[10px] font-bold uppercase text-gray-500">Point</th>
                            </tr>
                        </thead>
                        <tbody id="studentMarkList" class="divide-y divide-gray-100"></tbody>
                    </table>
                </div>
            </div>
            {{-- Step 2 Footer --}}
            <div
                class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-white flex flex-row justify-between items-center gap-2 sticky bottom-0 z-20">
                {{-- This stays on the left --}}
                <button type="button" id="backBtn" onclick="goToStep1()"
                    class="w-1/3 sm:w-auto sm:px-8 h-[32px] btn-outline-secondary border border-gray-200 text-[10px] tracking-normal capitalize transition-all hover:bg-gray-50 flex items-center justify-center whitespace-nowrap"
                    style="border-radius: 0;">
                    Back
                </button>

                {{-- This group moves to the right end --}}
                <div class="flex flex-row gap-2 flex-grow sm:flex-grow-0 justify-end">
                    <button type="button" onclick="submitMarks('draft')"
                        class="flex-1 sm:w-auto sm:px-6 h-[32px] border border-blue-600 text-blue-600 text-[10px] tracking-normal capitalize transition-all hover:bg-blue-50 flex items-center justify-center whitespace-nowrap"
                        style="border-radius: 0;">
                        Draft
                    </button>
                    <button type="button" onclick="submitMarks('published')"
                        class="flex-1 sm:w-auto sm:px-10 h-[32px] bg-blue-600 text-white text-[10px] tracking-normal capitalize hover:bg-blue-700 transition-all flex items-center justify-center whitespace-nowrap"
                        style="border-radius: 0;">
                        Submit
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let gradingSystem = [];
    let studentsForEntry = [];
    let isEditMode = false;
    let currentPage = 1;
    let selectedSubjectType = 1;

    window.onload = function() {
        loadInitialDropdowns();
        fetchGradingRules();
        fetchTable();
    };

    function loadInitialDropdowns() {
        // Load Classes for both Filter and Modal
        axios.get('/api/get-school-classes').then(res => {
            fillOptions('m_class', res.data.data, 'class_name', 'f_class');
        });
        // Initial load for Subjects/Exams (though they will refresh on cascade)
        axios.get('/api/get-school-exams').then(res => fillOptions('m_exam', res.data.data, 'exam_name', 'f_exam'));
        axios.get('/api/get-school-subjects').then(res => fillOptions('m_subject', res.data.data, 'subject_name',
            'f_subject'));
    }

    function fetchGradingRules() {
        axios.get('/api/get-grading-systems').then(res => {
            // We'll fetch the detailed grades separately or just fetch all
            axios.get('/api/school-exam-grades?all=1').then(res => {
                gradingSystem = res.data.data;
                console.log("Grade system loaded", gradingSystem);
            });
        });
    }

    function fillOptions(id, data, field, filterId = null) {
        let html = `<option value="">Select ${field.split('_')[0]}</option>`;
        data.forEach(i => {
            let extraAttrs = '';
            if (i.subject_type) extraAttrs += ` data-type="${i.subject_type}"`;
            if (i.grade_id) extraAttrs += ` data-grade-id="${i.grade_id}"`;
            if (i.marks) {
                const marksStr = typeof i.marks === 'object' ? JSON.stringify(i.marks) : i.marks;
                extraAttrs += ` data-marks='${marksStr}'`;
            }
            html += `<option value="${i[field]}" data-id="${i.id}"${extraAttrs}>${i[field]}</option>`;
        });
        const el = document.getElementById(id);
        if (el) el.innerHTML = html;
        if (filterId) {
            const fEl = document.getElementById(filterId);
            if (fEl) fEl.innerHTML = html;
        }
    }

    async function handleCascade(el, next) {
        const selectedOption = el.options[el.selectedIndex];
        const id = selectedOption?.getAttribute('data-id');
        const val = selectedOption?.value;

        if (!id && !val) return;

        let url = '';
        let fld = '';
        let params = {};

        // Cascade Logic based on your Controller
        if (next.includes('group')) {
            url = `/api/get-school-groups`;
            params = {
                class_id: id
            };
            fld = 'group_name';

            // Also refresh Subjects and Exams for this Class immediately
            refreshSubjects(id);
            refreshExams(val);

        } else if (next.includes('section')) {
            url = `/api/get-school-sections`;
            params = {
                group_id: id
            };
            fld = 'section_name';

            // Refresh Subjects for this Group
            const classId = document.getElementById('m_class').options[document.getElementById('m_class')
                .selectedIndex]?.getAttribute('data-id');
            refreshSubjects(classId, id);

        } else if (next.includes('session')) {
            const isModal = next.startsWith('m_');
            const classEl = document.getElementById(isModal ? 'm_class' : 'f_class');
            const groupEl = document.getElementById(isModal ? 'm_group' : 'f_group');
            const classId = classEl.options[classEl.selectedIndex]?.getAttribute('data-id');
            const groupId = groupEl.options[groupEl.selectedIndex]?.getAttribute('data-id');

            url = `/api/get-school-sessions`;
            params = {
                section_id: id,
                class_id: classId,
                group_id: groupId
            };
            fld = 'session_year';
        }

        const res = await axios.get(url, {
            params
        });
        let tid = next.startsWith('m_') ? next : 'f_' + next.split('_')[1];
        fillOptions(tid, res.data.data, fld);
        return res.data.data;
    }

    // New specific refreshers for Subject and Exam based on selection
    function refreshSubjects(classId, groupId = null, sectionId = null) {
        axios.get('/api/get-school-subjects', {
            params: {
                class_id: classId,
                group_id: groupId,
                section_id: sectionId
            }
        }).then(res => fillOptions('m_subject', res.data.data, 'subject_name', 'f_subject'));
    }

    function refreshExams(className, sessionName = null) {
        axios.get('/api/get-school-exams', {
            params: {
                class_name: className,
                session_name: sessionName
            }
        }).then(res => fillOptions('m_exam', res.data.data, 'exam_name', 'f_exam'));
    }

    function fetchTable(page = 1) {
        currentPage = page;
        const params = {
            page,
            class_name: document.getElementById('f_class').value,
            exam_name: document.getElementById('f_exam').value,
            subject_name: document.getElementById('f_subject').value,
            search: document.getElementById('header_search').value
        };
        axios.get('/api/school-exam-marks', {
            params
        }).then(res => {
            const body = document.getElementById('markTableBody');
            body.innerHTML = '';
            if (!res.data.data || res.data.data.length === 0) {
                body.innerHTML = '<tr><td colspan="10" class="text-center py-8">No records found</td></tr>';
                renderPagination(res.data);
                return;
            }
            res.data.data.forEach((item, i) => {
                body.innerHTML += `
                <tr>
                    <td>${res.data.from + i}</td>
                    <td>${item.class_name}</td>
                    <td>${item.subject_name}</td>
                    <td>${item.exam_name}</td>
                    <td class="font-mono">${item.student_id_number}</td>
                    <td>${item.student_name}</td>
                    <td>${item.mark}</td>
                    <td>${item.letter_name}</td>
                    <td>${item.point}</td>
                    <td class="text-center">
                        <div class="flex justify-center gap-3">
                            <button onclick="editMark(${item.id})" class="action-icon-btn text-blue-500"><i class="far fa-edit" style="font-size: 15px;"></i></button>
                            <button onclick="deleteMark(${item.id})" class="action-icon-btn text-red-400"><i class="far fa-trash-alt" style="font-size: 15px;"></i></button>
                        </div>
                    </td>
                </tr>`;
            });
            renderPagination(res.data);
        });
    }

    function renderPagination(meta) {
        const controls = document.getElementById('paginationControls');
        const info = document.getElementById('paginationInfo');
        if (!controls || !info) return;

        info.innerText = `${meta.to || 0} of ${meta.total || 0}`;
        controls.innerHTML = '';
        const btnClass = "pagination-btn";

        controls.innerHTML += `
            <button class="${btnClass}" ${meta.current_page === 1 ? 'disabled' : ''} onclick="fetchTable(${meta.current_page - 1})">
                <i class="mdi mdi-chevron-left"></i>
            </button>`;

        for (let i = 1; i <= meta.last_page; i++) {
            controls.innerHTML += `
                <button class="${btnClass} ${meta.current_page === i ? 'active' : ''}" onclick="fetchTable(${i})">
                    ${i}
                </button>`;
        }

        controls.innerHTML += `
            <button class="${btnClass}" ${meta.current_page === meta.last_page ? 'disabled' : ''} onclick="fetchTable(${meta.current_page + 1})">
                <i class="mdi mdi-chevron-right"></i>
            </button>`;
    }

    function goToStep2() {
        const classEl = document.getElementById('m_class');
        const groupEl = document.getElementById('m_group');
        const sectionEl = document.getElementById('m_section');
        const sessionEl = document.getElementById('m_session');
        const actionType = document.getElementById('action_type').value;

        const classId = classEl.options[classEl.selectedIndex]?.getAttribute('data-id');
        const groupId = groupEl.options[groupEl.selectedIndex]?.getAttribute('data-id');
        const sectionId = sectionEl.options[sectionEl.selectedIndex]?.getAttribute('data-id');
        const sessionId = sessionEl.options[sessionEl.selectedIndex]?.getAttribute('data-id');

        const exm = document.getElementById('m_exam').value;
        const subEl = document.getElementById('m_subject');
        const sub = subEl.value;
        selectedSubjectType = subEl.options[subEl.selectedIndex]?.getAttribute('data-type') || 1;

        if (!classId || !sessionId || !exm || !sub) {
            return Swal.fire('Error', 'Please configure Class, Session, Exam and Subject', 'error');
        }

        if (isEditMode) {
            renderStudentRows();
            showStep(2);
            return;
        }

        // Fetch students using the IDs matching your Controller logic
        axios.get('/api/get-school-students', {
            params: {
                class_id: classId,
                group_id: groupId,
                section_id: sectionId,
                session_id: sessionId,
                action_type: actionType
            }
        }).then(res => {
            if (res.data.status === 'no_admit_card') {
                return Swal.fire('Admit Card Missing', res.data.message, 'warning');
            }

            studentsForEntry = res.data.data;
            if (!studentsForEntry || studentsForEntry.length === 0) {
                return Swal.fire('No Students', 'No students found for this specific selection', 'info');
            }
            renderStudentRows();
            showStep(2);
        }).catch(err => {
            const msg = err.response?.data?.message || 'Failed to fetch students.';
            Swal.fire('Error', msg, 'error');
        });
    }

    function renderStudentRows() {
        const tbody = document.getElementById('studentMarkList');
        const thead = document.querySelector('#step2 thead tr');

        // Get full mark and mark distribution for placeholders
        const subEl = document.getElementById('m_subject');
        const selectedSub = subEl.options[subEl.selectedIndex];
        const gradeId = selectedSub?.getAttribute('data-grade-id');
        const referenceRule = gradingSystem.find(g => g.id == gradeId);
        const fullMark = referenceRule ? referenceRule.full_mark : 100;

        let theoryMax = '';
        let practicalMax = '';
        const marksDataAttr = selectedSub?.getAttribute('data-marks');
        if (marksDataAttr) {
            try {
                const marks = typeof marksDataAttr === 'string' ? JSON.parse(marksDataAttr) : marksDataAttr;
                theoryMax = marks.theory_marks || '';
                practicalMax = marks.practical_marks || '';
            } catch (e) {}
        }

        // Update Header based on subject type
        let headerHtml = `
                <th class="px-4 py-2 text-[10px] font-bold uppercase text-gray-500">ID</th>
                <th class="px-4 py-2 text-[10px] font-bold uppercase text-gray-500">Name</th>`;

        if (selectedSubjectType == 3) {
            headerHtml += `
                    <th width="100" class="px-4 py-2 text-[10px] font-bold uppercase text-gray-500">Theory</th>
                    <th width="100" class="px-4 py-2 text-[10px] font-bold uppercase text-gray-500">Practical</th>
                    <th width="100" class="px-4 py-2 text-[10px] font-bold uppercase text-gray-500">Total Mark</th>`;
        } else {
            headerHtml += `<th width="120" class="px-4 py-2 text-[10px] font-bold uppercase text-gray-500">Mark</th>`;
        }

        headerHtml += `
                <th class="px-4 py-2 text-[10px] font-bold uppercase text-gray-500">Grade</th>
                <th class="px-4 py-2 text-[10px] font-bold uppercase text-gray-500">Point</th>`;

        thead.innerHTML = headerHtml;

        tbody.innerHTML = '';
        studentsForEntry.forEach((s, idx) => {

            let markInputs = '';
            if (selectedSubjectType == 3) {
                const theoryPlaceholder = theoryMax ? `0 - ${theoryMax}` : 'Min: 0';
                const practicalPlaceholder = practicalMax ? `0 - ${practicalMax}` : 'Min: 0';
                markInputs = `
                        <td><input type="number" placeholder="${theoryPlaceholder}" class="form-input-fixed theory-input" value="${s.theory_mark || ''}" onkeyup="calculateGrade(this, ${idx}, 'theory')" data-idx="${idx}"></td>
                        <td><input type="number" placeholder="${practicalPlaceholder}" class="form-input-fixed practical-input" value="${s.practical_mark || ''}" onkeyup="calculateGrade(this, ${idx}, 'practical')" data-idx="${idx}"></td>
                        <td><input type="number" placeholder="Total" class="form-input-fixed mark-input bg-gray-50" value="${s.mark || ''}" readonly id="total_mark_${idx}"></td>`;
            } else {
                const fullMarkPlaceholder = `0 - ${fullMark}`;
                markInputs = `<td><input type="number" placeholder="${fullMarkPlaceholder}" class="form-input-fixed mark-input" value="${s.mark || ''}" min="0" max="${fullMark}" onkeyup="calculateGrade(this, ${idx}, 'total')" data-idx="${idx}"></td>`;
            }

            tbody.innerHTML += `
            <tr>
                <td class="font-mono text-[10px]">${s.student_id_number}</td>
                <td class="uppercase font-bold">${s.student_name}</td>
                ${markInputs}
                <td id="grade_${idx}" class="font-black text-blue-600">${s.letter_name || '-'}</td>
                <td id="point_${idx}" class="font-bold">${s.point || '-'}</td>
            </tr>`;
        });
    }

    function showStep(step) {
        if (step === 1) {
            document.getElementById('step2').classList.add('step-hidden');
            document.getElementById('step1').classList.remove('step-hidden');
            document.getElementById('modalTitle').innerText = isEditMode ? 'Edit Mode: Configuration' :
                'Step 1: Exam Configuration';
        } else {
            document.getElementById('step1').classList.add('step-hidden');
            document.getElementById('step2').classList.remove('step-hidden');
            document.getElementById('modalTitle').innerText = isEditMode ? 'Edit Mode: Update Mark' :
                'Step 2: Enter Student Marks';
        }
    }

    function goToStep1() {
        showStep(1);
    }

    function calculateGrade(input, idx, type) {
        let theory = 0;
        let practical = 0;
        let total = 0;

        const subEl = document.getElementById('m_subject');
        const gradeId = subEl.options[subEl.selectedIndex]?.getAttribute('data-grade-id');
        const referenceRule = gradingSystem.find(g => g.id == gradeId);
        const fullMark = referenceRule ? referenceRule.full_mark : null;

        if (selectedSubjectType == 3) {
            const tr = input.closest('tr');
            theory = parseFloat(tr.querySelector('.theory-input').value) || 0;
            practical = parseFloat(tr.querySelector('.practical-input').value) || 0;
            total = theory + practical;

            if (fullMark && total > fullMark) {
                Swal.fire('Invalid Mark', `Total mark (${total}) cannot exceed full marks (${fullMark})`, 'warning');
                input.value = '';
                total = 0; // Reset for calculation
            }

            studentsForEntry[idx].theory_mark = theory;
            studentsForEntry[idx].practical_mark = practical;
            studentsForEntry[idx].mark = total;
            document.getElementById(`total_mark_${idx}`).value = total;
        } else {
            total = parseFloat(input.value) || 0;

            if (fullMark && total > fullMark) {
                Swal.fire('Invalid Mark', `Mark cannot exceed full marks (${fullMark})`, 'warning');
                input.value = '';
                total = 0;
            }

            studentsForEntry[idx].mark = total;
        }

        if (total === 0 && input.value === '') {
            document.getElementById(`grade_${idx}`).innerText = '-';
            document.getElementById(`point_${idx}`).innerText = '-';
            return;
        }

        const res = gradingSystem.find(g =>
            (fullMark ? g.full_mark == fullMark : true) &&
            total >= g.mark_from && total <= g.mark_to
        );
        if (res) {
            document.getElementById(`grade_${idx}`).innerText = res.grade_name;
            document.getElementById(`point_${idx}`).innerText = res.grade_point;
            studentsForEntry[idx].letter_name = res.grade_name;
            studentsForEntry[idx].point = res.grade_point;
        } else {
            document.getElementById(`grade_${idx}`).innerText = '-';
            document.getElementById(`point_${idx}`).innerText = '-';
        }
    }

    function submitMarks(status) {
        const editId = document.getElementById('edit_id').value;
        const payload = {
            class_name: document.getElementById('m_class').value,
            group_name: document.getElementById('m_group').value,
            section_name: document.getElementById('m_section').value,
            session_name: document.getElementById('m_session').value,
            exam_name: document.getElementById('m_exam').value,
            subject_name: document.getElementById('m_subject').value,
            status: status,
            marks_data: studentsForEntry.map(s => ({
                student_id_number: s.student_id_number,
                student_name: s.student_name,
                roll_no: s.roll_no || null,
                mark: s.mark,
                theory_mark: s.theory_mark || 0,
                practical_mark: s.practical_mark || 0,
                letter_name: s.letter_name,
                point: s.point
            }))
        };

        const request = isEditMode ? axios.put(`/api/school-exam-marks/${editId}`, payload) : axios.post(
            '/api/school-exam-marks', payload);

        request.then(res => {
            Toastify({
                text: isEditMode ? "Mark Updated!" : "Marks Submitted!",
                style: {
                    background: "#10b981"
                }
            }).showToast();
            closeMarkModal();
            fetchTable(currentPage);
        }).catch(err => {
            if (err.response?.status === 422) {
                Swal.fire('Error', err.response.data.message, 'warning');
            }
        });
    }

    async function editMark(id) {
        isEditMode = true;
        try {
            const res = await axios.get(`/api/school-exam-marks/${id}`);
            const data = res.data;

            document.getElementById('edit_id').value = id;
            document.getElementById('m_class').value = data.class_name;

            await handleCascade(document.getElementById('m_class'), 'm_group');
            document.getElementById('m_group').value = data.group_name;

            await handleCascade(document.getElementById('m_group'), 'm_section');
            document.getElementById('m_section').value = data.section_name;

            await handleCascade(document.getElementById('m_section'), 'm_session');
            document.getElementById('m_session').value = data.session_name;

            document.getElementById('m_exam').value = data.exam_name;
            document.getElementById('m_subject').value = data.subject_name;

            studentsForEntry = [{
                student_id_number: data.student_id_number,
                student_name: data.student_name,
                roll_no: data.roll_no,
                mark: data.mark,
                theory_mark: data.theory_mark,
                practical_mark: data.practical_mark,
                letter_name: data.letter_name,
                point: data.point
            }];

            document.getElementById('markModal').classList.remove('hidden');
            showStep(1);
        } catch (error) {
            Swal.fire('Error', 'Failed to fetch details', 'error');
        }
    }

    function deleteMark(id) {
        Swal.fire({
            title: 'Delete Entry?',
            showCancelButton: true,
            confirmButtonColor: '#ef4444'
        }).then(r => {
            if (r.isConfirmed) axios.delete(`/api/school-exam-marks/${id}`).then(() => fetchTable(currentPage));
        });
    }

    function openMarkModal() {
        isEditMode = false;
        document.getElementById('edit_id').value = '';
        document.getElementById('markModal').classList.remove('hidden');
        showStep(1);
    }

    function closeMarkModal() {
        document.getElementById('markModal').classList.add('hidden');
    }

    function toggleFilterModal() {
        document.getElementById('filterModal').classList.toggle('hidden');
    }

    function applyFilters() {
        fetchTable(1);
        toggleFilterModal();
    }

    function resetFilters() {
        document.getElementById('f_class').value = '';
        document.getElementById('f_exam').value = '';
        document.getElementById('f_subject').value = '';
        document.getElementById('header_search').value = '';
        fetchTable(1);
        toggleFilterModal();
    }
</script>
@endsection