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
            overflow-x: hidden !important;
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

        .table-responsive {
            width: 100% !important;
            overflow-x: auto !important;
            display: block !important;
            background: white !important;
            padding: 15px !important;
        }

        .table-responsive::-webkit-scrollbar {
            height: 6px !important;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f8fafc !important;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #cbd5e1 !important;
        }

        table {
            width: 100% !important;
            border-collapse: collapse !important;
            table-layout: auto !important;
            border: 1px solid #d1d5db !important;
            font-size: 11px !important;
        }

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
            text-align: left !important;
            text-transform: capitalize !important;
        }

        th:last-child {
            border-right: none !important;
        }

        td {
            padding: 0 12px !important;
            vertical-align: middle !important;
            border-bottom: 1px solid #d1d5db !important;
            border-right: 1px solid #d1d5db !important;
            font-size: 11px !important;
            color: #4b5563 !important;
            white-space: nowrap !important;
        }

        td:last-child {
            border-right: none !important;
        }

        .pagination-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            background: #fff;
            border-top: 1px solid #edf2f7;
        }

        .pagination-btn {
            padding: 6px 10px;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            min-width: 32px;
            transition: all 0.2s;
        }

        .pagination-btn:hover:not(:disabled) {
            border-color: #2563eb;
            color: #2563eb;
        }

        .pagination-btn.active {
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
        }

        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .btn-outline-premium {
            background: transparent;
            border: 1.5px solid #2563eb;
            font-weight: 600;
            border-radius: 0;
            cursor: pointer;
        }

        .btn-outline-premium:hover {
            background: #2563eb;
            color: #fff;
        }

        .btn-outline-secondary {
            background: transparent;
            border: 1.5px solid #64748b;
            color: #64748b;
            font-weight: 600;
            border-radius: 0;
            cursor: pointer;
        }

        .form-input-fixed {
            width: 100%;
            border: 1px solid #cbd5e1 !important;
            padding: .5rem .7rem;
            border-radius: 0;
            font-size: .85rem;
            outline: none;
        }

        .form-input-fixed:focus {
            border-color: #2563eb !important;
        }

        .audience-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            background: #f1f5f9;
            color: #475569;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
            margin-right: 4px;
            border: 1px solid #e2e8f0;
        }

        .class-label {
            font-size: 11px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 2px;
            display: block;
        }

        .action-btn {
            font-size: 16px;
            transition: opacity 0.2s;
        }

        .action-btn:hover {
            opacity: 0.7;
        }
    </style>

    <div class="main-view-container">
        <div class="max-w-full mx-auto w-full">
            <div class="bg-white border border-gray-200 p-2.5 sm:p-4 mb-4" style="border-radius: 0;">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">

                    <div class="w-full lg:w-auto">
                        <h2 id="pageHeader" class="text-[15px] sm:text-xl text-gray-800 font-normal leading-tight">
                            Announcements</h2>
                        <div class="flex items-center text-slate-400 text-[12px] mt-1">
                            <span>School</span>
                            <i class="fas fa-chevron-right mx-1.5 text-[10px]"></i>
                            <span id="pageTitle" class="text-slate-500">Notice Management</span>
                        </div>

                        <div class="relative w-full sm:w-64 mt-3 hidden lg:block">
                            <i id="searchIcon"
                                class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" id="noticeSearch" placeholder="Search notices..."
                                class="pl-8 pr-3 py-2 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                                style="border-radius: 0;" />
                        </div>
                    </div>

                    <div class="flex flex-row items-center gap-1 w-full lg:w-auto">
                        <button onclick="openFilterModal()"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Filter
                        </button>

                        <button onclick="openExportModal()"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Export
                        </button>

                        <button onclick="openNoticeModal()"
                            class="btn-outline-premium border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            New Notice
                        </button>
                    </div>
                </div>

                <div class="relative w-full mt-3 lg:hidden">
                    <i class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="noticeSearchMobile" placeholder="Search notices..."
                        class="pl-8 pr-3 py-1.5 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                        style="border-radius: 0;" />
                </div>
            </div>

            {{-- Filter Modal --}}
            <div id="filterModal"
                class="premium-modal fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center p-12 sm:p-20"
                onclick="closeOnOutsideClick(event, 'filterModal')">
                <div class="bg-white p-4 w-full max-w-[320px] modal-content-sharp shadow-2xl" style="border-radius: 0;"
                    onclick="event.stopPropagation()">

                    <div>
                        <h3
                            class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                            Notice filter
                        </h3>
                        <div class="h-[1px] w-full bg-gray-200 mt-2.5"></div>
                    </div>

                    <div class="mt-3 mb-4 space-y-3">
                        {{-- Class Filter --}}
                        <div class="relative">
                            <label class="text-[10px] text-gray-500 block mb-1">Class</label>
                            <div class="relative">
                                <select id="filter_class" onchange="handleCascade(this, 'filter', 'group')"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
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
                                <select id="filter_group" onchange="handleCascade(this, 'filter', 'section')"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
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
                                <select id="filter_section" onchange="handleCascade(this, 'filter', 'session')"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
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
                                <select id="filter_session"
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
                        <button onclick="applyFilters()"
                            class="btn-outline-premium border border-gray-200 w-full text-[11px] capitalize flex items-center justify-center"
                            style="border-radius: 0; height: 32px;">Apply</button>
                    </div>
                </div>
            </div>

            {{-- Export Modal --}}
            <div id="exportModal"
                class="premium-modal fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center p-12 sm:p-20">
                <div class="bg-white p-4 w-auto min-w-[140px] modal-content-sharp shadow-2xl">
                    <div class="flex flex-col gap-1.5">
                        <button
                            class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap">
                            PDF
                        </button>
                        <button
                            class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap">
                            EXCEL
                        </button>
                        <button
                            class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap">
                            PRINT
                        </button>
                        <button id="closeExport"
                            class="mt-1 py-1.5 text-[10px] text-gray-400 hover:text-gray-600 w-full text-center border border-gray-200 transition-all">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-card">
                <div class="table-responsive">
                    <table class="w-full min-w-[1000px]">
                        <thead>
                            <tr>
                                <th width="60">Sl</th>
                                <th width="110">Date</th>
                                <th width="100">Type</th>
                                <th width="250">Target Audience</th>
                                <th width="200">Title</th>
                                <th>Details</th>
                                <th width="100" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="noticeTableBody" class="bg-white divide-y divide-gray-100"></tbody>
                    </table>
                </div>
                <div class="pagination-container">
                    <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest" id="paginationInfo">0 of 0
                    </div>
                    <div class="flex items-center gap-1" id="paginationControls"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Notice Modal --}}
    <div id="noticeModal"
        class="fixed inset-0 bg-gray-900/60 flex items-center justify-center hidden z-[100] px-4 sm:px-40 py-12 backdrop-blur-sm overflow-y-auto"
        onclick="closeOnOutsideClick(event, 'noticeModal')">

        <div class="bg-white w-full max-w-2xl modal-content-sharp shadow-2xl overflow-hidden flex flex-col my-auto max-h-[90vh] sm:max-h-[85vh] mx-auto border border-gray-100"
            onclick="event.stopPropagation()" style="border-radius:0;">

            {{-- Header --}}
            <div class="px-5 py-3 border-b flex justify-center items-center bg-white sticky top-0 z-10">
                <h3 id="modalTitle"
                    class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                    Create Announcement
                </h3>
            </div>

            <form id="noticeForm" class="flex flex-col overflow-hidden m-0">
                <input type="hidden" id="edit_id">

                <div class="overflow-y-auto custom-scrollbar p-4 sm:p-6 flex-grow bg-gray-50/30">
                    <div class="grid grid-cols-1 gap-y-4">

                        {{-- Type Selection --}}
                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Announcement
                                Type</label>
                            <select id="type"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                onchange="toggleFields()" required style="border-radius: 0;">
                                <option value="General">General Announcement</option>
                                <option value="Class Wise">Class Specific</option>
                            </select>
                        </div>

                        {{-- Targeted Audience Section - Stacks on Mobile --}}
                        <div id="classFields" class="col-span-1 hidden bg-white p-4 border border-dashed border-gray-200">
                            <p class="text-[10px] font-medium text-blue-600 capitalize mb-3 flex items-center gap-2">
                                <i class="mdi mdi-account-group-outline"></i> Targeted Audience
                            </p>
                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                                <div>
                                    <label class="text-[9px] text-gray-500 capitalize block mb-1">Class</label>
                                    <select id="class_name"
                                        class="form-input-fixed w-full border border-gray-100 py-1 px-2 text-[11px] h-[30px]"
                                        onchange="loadGroups(this.value)" style="border-radius:0;">
                                        <option value="">Select</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-[9px] text-gray-500 capitalize block mb-1">Group</label>
                                    <select id="group_name"
                                        class="form-input-fixed w-full border border-gray-100 py-1 px-2 text-[11px] h-[30px]"
                                        onchange="loadSections(this.value)" style="border-radius:0;">
                                        <option value="">Select</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-[9px] text-gray-500 capitalize block mb-1">Section</label>
                                    <select id="section_name"
                                        class="form-input-fixed w-full border border-gray-100 py-1 px-2 text-[11px] h-[30px]"
                                        onchange="loadSessions()" style="border-radius:0;">
                                        <option value="">Select</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="text-[9px] text-gray-500 capitalize block mb-1">Session</label>
                                    <select id="session"
                                        class="form-input-fixed w-full border border-gray-100 py-1 px-2 text-[11px] h-[30px]"
                                        style="border-radius:0;">
                                        <option value="">Select</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Title and Date - One after another on Mobile --}}
                        <div
                            class="col-span-1 mt-2 pt-4 border-t border-gray-200/60 grid grid-cols-1 sm:grid-cols-12 gap-4">
                            <div class="sm:col-span-8">
                                <label
                                    class="block text-[10px] capitalize tracking-normal text-blue-600 mb-1.5 font-medium">Notice
                                    Title</label>
                                <input type="text" id="title" placeholder="Enter heading..."
                                    class="form-input-fixed w-full border border-blue-200 py-1.5 px-3 text-xs text-gray-700 h-[32px]"
                                    required style="border-radius: 0;">
                            </div>
                            <div class="sm:col-span-4">
                                <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Notice
                                    Date</label>
                                <input type="date" id="date" value="{{ date('Y-m-d') }}"
                                    class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs text-gray-700 h-[32px]"
                                    required style="border-radius: 0;">
                            </div>
                        </div>

                        {{-- Details --}}
                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Notice
                                Details</label>
                            <textarea id="details" placeholder="Type announcement..."
                                class="form-input-fixed w-full border border-gray-200 py-2 px-3 text-xs text-gray-700 h-32 resize-none" required
                                style="border-radius: 0;"></textarea>
                        </div>

                    </div>
                </div>

                {{-- Footer Actions --}}
                <div
                    class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-white flex flex-row sm:justify-end gap-2 sticky bottom-0">
                    <button type="button" onclick="closeNoticeModal()"
                        class="flex-1 sm:flex-none sm:px-8 h-[32px] btn-outline-secondary border border-gray-200 text-[10px] tracking-normal capitalize transition-all hover:bg-gray-50 flex items-center justify-center whitespace-nowrap"
                        style="border-radius: 0;">
                        Cancel
                    </button>
                    <button type="submit" id="saveBtn"
                        class="flex-1 sm:flex-none sm:px-12 h-[32px] btn-outline-premium border border-gray-200 text-[10px] tracking-normal capitalize flex items-center justify-center whitespace-nowrap"
                        style="border-radius: 0;">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;

        let currentPage = 1;
        let searchTimer;

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        /**
         * Unified Cascade Logic
         */
        window.handleCascade = async function(element, context, nextStep) {
            const val = element.value;
            if (nextStep === 'group') {
                await loadGroups(val, context);
            } else if (nextStep === 'section') {
                await loadSections(val, context);
            } else if (nextStep === 'session') {
                await loadSessions(context);
            }
        }

        async function loadClasses() {
            try {
                const res = await axios.get('/api/get-school-classes');
                const data = res.data.data || [];
                const formSelect = document.getElementById('class_name');
                const filterSelect = document.getElementById('filter_class');
                let options = '<option value="">Select class</option>';
                data.forEach(item => {
                    options +=
                        `<option value="${item.id}" data-name="${item.class_name}">${item.class_name}</option>`;
                });
                if (formSelect) formSelect.innerHTML = options;
                if (filterSelect) {
                    filterSelect.innerHTML = options;
                    filterSelect.value = "";
                    fetchNotices(1, true);
                }
            } catch (err) {
                console.error("Class load failed", err);
            }
        }

        async function loadGroups(classId, context = 'form') {
            const selectId = (context === 'filter') ? 'filter_group' : 'group_name';
            const select = document.getElementById(selectId);
            if (!select) return;
            if (!classId) {
                select.innerHTML = '<option value="">Select group</option>';
                return;
            }
            try {
                const res = await axios.get(`/api/get-school-groups?class_id=${classId}`);
                const data = res.data.data || [];
                let options = '<option value="">Select group</option>';
                data.forEach(item => {
                    options +=
                        `<option value="${item.id}" data-name="${item.group_name}">${item.group_name}</option>`;
                });
                select.innerHTML = options;
                if (context === 'filter') await loadSections('', 'filter');
            } catch (err) {
                console.error("Group load failed", err);
            }
        }

        async function loadSections(groupId, context = 'form') {
            const selectId = (context === 'filter') ? 'filter_section' : 'section_name';
            const select = document.getElementById(selectId);
            if (!select) return;
            if (!groupId) {
                select.innerHTML = '<option value="">Select section</option>';
                return;
            }
            try {
                const res = await axios.get(`/api/get-school-sections?group_id=${groupId}`);
                const data = res.data.data || [];
                let options = '<option value="">Select section</option>';
                data.forEach(item => {
                    options +=
                        `<option value="${item.id}" data-name="${item.section_name}">${item.section_name}</option>`;
                });
                select.innerHTML = options;
                if (context === 'filter') await loadSessions('filter');
            } catch (err) {
                console.error("Section load failed", err);
            }
        }

        async function loadSessions(context = 'form') {
            const isFilter = (context === 'filter');
            const classId = document.getElementById(isFilter ? 'filter_class' : 'class_name')?.value;
            const groupId = document.getElementById(isFilter ? 'filter_group' : 'group_name')?.value;
            const sectionId = document.getElementById(isFilter ? 'filter_section' : 'section_name')?.value;
            const sessionSelect = document.getElementById(isFilter ? 'filter_session' : 'session');
            if (!sessionSelect || !classId) return;
            try {
                const res = await axios.get(`/api/get-school-sessions`, {
                    params: {
                        class_id: classId,
                        group_id: groupId,
                        section_id: sectionId
                    }
                });
                const data = res.data.data || [];
                let options = '<option value="">Select session</option>';
                data.forEach(item => {
                    options += `<option value="${item.session_year}">${item.session_year}</option>`;
                });
                sessionSelect.innerHTML = options;
            } catch (err) {
                console.error("Session load failed", err);
            }
        }

        window.applyFilters = function() {
            currentPage = 1;
            fetchNotices(1);
            closeFilterModal();
        }

        window.resetFilters = async function() {
            document.getElementById('noticeSearch').value = '';
            const fGroup = document.getElementById('filter_group');
            const fSection = document.getElementById('filter_section');
            const fSession = document.getElementById('filter_session');
            if (fGroup) fGroup.innerHTML = '<option value="">Select group</option>';
            if (fSection) fSection.innerHTML = '<option value="">Select section</option>';
            if (fSession) fSession.innerHTML = '<option value="">Select session</option>';
            await loadClasses();
            closeFilterModal();
        }

        window.fetchNotices = function(page = 1, initial = false) {
            currentPage = page;
            const search = document.getElementById('noticeSearch').value;
            const fClass = document.getElementById('filter_class');
            const fGroup = document.getElementById('filter_group');
            const fSection = document.getElementById('filter_section');
            const fSession = document.getElementById('filter_session');

            const params = {
                search: search,
                page: page,
                class_name: fClass?.options[fClass.selectedIndex]?.getAttribute('data-name') || '',
                group_name: fGroup?.options[fGroup.selectedIndex]?.getAttribute('data-name') || '',
                section_name: fSection?.options[fSection.selectedIndex]?.getAttribute('data-name') || '',
                session: fSession?.value || ''
            };

            if (initial && !params.class_name) params.type = 'Everyone';

            const searchIcon = document.getElementById('searchIcon');
            if (searchIcon) searchIcon.className =
                "mdi mdi-loading mdi-spin absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400";

            axios.get('/api/school-announcements', {
                    params
                })
                .then(res => {
                    const notices = res.data.data;
                    const meta = res.data;
                    const tbody = document.getElementById('noticeTableBody');
                    tbody.innerHTML = '';

                    if (!notices || notices.length === 0) {
                        tbody.innerHTML =
                            `<tr><td colspan="7" class="text-center py-6 text-gray-400 tracking-tighter text-[11px]">No notices found</td></tr>`;
                        updatePaginationUI(meta);
                        return;
                    }

                    notices.forEach((n, i) => {
                        let formattedDate = n.date;
                        if (n.date) {
                            const d = new Date(n.date);
                            if (!isNaN(d.getTime())) {
                                const day = String(d.getDate()).padStart(2, '0');
                                const month = String(d.getMonth() + 1).padStart(2, '0');
                                formattedDate = `${day}/${month}/${d.getFullYear()}`;
                            }
                        }

                        let audienceHtml = n.type === 'Class Wise' ? `
                    <div class="flex items-center gap-2">
                        <span class="text-gray-600 text-[10px] whitespace-nowrap">${n.class_name}</span>
                        <div class="flex gap-1">
                            <span class="px-1 py-0.5 bg-gray-50 text-gray-500 text-[8px] border border-gray-100 leading-none">${n.section_name}</span>
                            <span class="px-1 py-0.5 bg-gray-50 text-gray-500 text-[8px] border border-gray-100 leading-none">${n.session}</span>
                        </div>
                    </div>` : `<span class="text-gray-400 text-[10px] whitespace-nowrap">Everyone</span>`;

                        tbody.innerHTML += `
                    <tr class="hover:bg-gray-50/50 transition-colors border-b border-gray-100">
                        <td class="text-gray-400 text-[10px] px-2 py-2">${(meta.from || 1) + i}</td>
                        <td class="text-gray-500 text-[10px] py-2 whitespace-nowrap">${formattedDate}</td>
                        <td class="py-2"><span class="px-1.5 py-0.5 text-[8px] tracking-wider bg-gray-100 text-gray-500 border border-gray-200">${n.type}</span></td>
                        <td class="py-2">${audienceHtml}</td>
                        <td class="text-gray-600 text-[11px] py-2 truncate max-w-[150px]">${n.title}</td>
                        <td class="text-gray-50 text-[10px] py-2"><div class="truncate max-w-[200px]" title="${n.details}">${n.details}</div></td>
                        <td class="text-center py-2">
                            <div class="flex justify-center gap-3">
                                <button onclick="editNotice(${n.id})" class="action-icon-btn text-blue-500"><i class="far fa-edit" style="font-size: 15px;"></i></button>
                                <button onclick="deleteNotice(${n.id})" class="action-icon-btn text-red-400"><i class="far fa-trash-alt" style="font-size: 15px;"></i></button>
                            </div>
                        </td>
                    </tr>`;
                    });
                    updatePaginationUI(meta);
                })
                .finally(() => {
                    if (searchIcon) searchIcon.className =
                        "mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400";
                });
        }

        function updatePaginationUI(meta) {
            const controls = document.getElementById('paginationControls');
            const info = document.getElementById('paginationInfo');
            if (info) info.innerText = `${meta.to || 0} of ${meta.total || 0}`;
            if (!controls) return;
            controls.innerHTML = '';
            if (!meta || meta.total === 0) return;

            controls.innerHTML +=
                `<button onclick="fetchNotices(${meta.current_page - 1})" class="pagination-btn" ${meta.current_page === 1 ? 'disabled' : ''}><i class="mdi mdi-chevron-left"></i></button>`;
            for (let i = 1; i <= meta.last_page; i++) {
                if (i === 1 || i === meta.last_page || (i >= meta.current_page - 1 && i <= meta.current_page + 1)) {
                    controls.innerHTML +=
                        `<button onclick="fetchNotices(${i})" class="pagination-btn ${meta.current_page === i ? 'active' : ''}">${i}</button>`;
                }
            }
            controls.innerHTML +=
                `<button onclick="fetchNotices(${meta.current_page + 1})" class="pagination-btn" ${meta.current_page === meta.last_page ? 'disabled' : ''}><i class="mdi mdi-chevron-right"></i></button>`;
        }

        window.editNotice = async function(id) {
            try {
                const res = await axios.get(`/api/school-announcements/${id}`);
                const n = res.data;
                document.getElementById('edit_id').value = n.id;
                document.getElementById('modalTitle').innerText = "Edit Announcement";
                document.getElementById('type').value = n.type;
                document.getElementById('title').value = n.title;
                document.getElementById('date').value = n.date;
                document.getElementById('details').value = n.details;

                window.toggleFields();

                if (n.type === 'Class Wise') {
                    const classSelect = document.getElementById('class_name');
                    for (let option of classSelect.options) {
                        if (option.getAttribute('data-name') === n.class_name) {
                            classSelect.value = option.value;
                            break;
                        }
                    }
                    await loadGroups(classSelect.value);
                    const groupSelect = document.getElementById('group_name');
                    for (let option of groupSelect.options) {
                        if (option.getAttribute('data-name') === n.group_name) {
                            groupSelect.value = option.value;
                            break;
                        }
                    }
                    await loadSections(groupSelect.value);
                    const sectionSelect = document.getElementById('section_name');
                    for (let option of sectionSelect.options) {
                        if (option.getAttribute('data-name') === n.section_name) {
                            sectionSelect.value = option.value;
                            break;
                        }
                    }
                    await loadSessions();
                    document.getElementById('session').value = n.session;
                }

                document.getElementById('noticeModal').classList.remove('hidden');
            } catch (err) {
                Swal.fire('Error', 'Could not fetch notice details', 'error');
            }
        }

        window.deleteNotice = function(id) {
            Swal.fire({
                title: 'Are you sure?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it'
            }).then(r => {
                if (r.isConfirmed) {
                    axios.delete(`/api/school-announcements/${id}`).then(() => {
                        fetchNotices(currentPage);
                        Toast.fire({
                            icon: 'success',
                            title: 'Notice deleted successfully'
                        });
                    });
                }
            });
        }

        window.toggleFields = function() {
            const type = document.getElementById('type').value;
            document.getElementById('classFields').classList.toggle('hidden', type !== 'Class Wise');
        }

        /* Restored Modal Controls */
        window.openNoticeModal = function() {
            document.getElementById('noticeForm').reset();
            document.getElementById('edit_id').value = '';
            document.getElementById('modalTitle').innerText = "Create announcement";
            document.getElementById('classFields').classList.add('hidden');
            document.getElementById('noticeModal').classList.remove('hidden');
        }
        window.closeNoticeModal = function() {
            document.getElementById('noticeModal').classList.add('hidden');
        }

        window.openFilterModal = function() {
            document.getElementById('filterModal').classList.remove('hidden');
        }
        window.closeFilterModal = function() {
            document.getElementById('filterModal').classList.add('hidden');
        }

        window.openExportModal = function() {
            document.getElementById('exportModal').classList.remove('hidden');
        }
        window.closeExportModal = function() {
            document.getElementById('exportModal').classList.add('hidden');
        }

        window.closeOnOutsideClick = function(event, modalId) {
            const modal = document.getElementById(modalId);
            if (event.target === modal) modal.classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('noticeForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const id = document.getElementById('edit_id').value;
                    const saveBtn = document.getElementById('saveBtn');

                    const classSelect = document.getElementById('class_name');
                    const groupSelect = document.getElementById('group_name');
                    const sectionSelect = document.getElementById('section_name');

                    const data = {
                        type: document.getElementById('type').value,
                        class_name: classSelect?.options[classSelect.selectedIndex]?.getAttribute(
                            'data-name') || '',
                        group_name: groupSelect?.options[groupSelect.selectedIndex]?.getAttribute(
                            'data-name') || '',
                        section_name: sectionSelect?.options[sectionSelect.selectedIndex]?.getAttribute(
                            'data-name') || '',
                        session: document.getElementById('session').value,
                        title: document.getElementById('title').value,
                        date: document.getElementById('date').value,
                        details: document.getElementById('details').value,
                    };

                    saveBtn.disabled = true;
                    const url = id ? `/api/school-announcements/${id}` : '/api/school-announcements';
                    const method = id ? 'put' : 'post';

                    axios({
                        method,
                        url,
                        data
                    }).then(() => {
                        closeNoticeModal();
                        fetchNotices(currentPage);
                        Toast.fire({
                            icon: 'success',
                            title: id ? 'Updated' : 'Created'
                        });
                    }).catch(err => {
                        let errorMessage = "Validation Failed:<br>";
                        if (err.response && err.response.status === 422) {
                            const errors = err.response.data.errors;
                            Object.keys(errors).forEach(key => {
                                errorMessage += `• ${errors[key][0]}<br>`;
                            });
                        } else {
                            errorMessage = err.response?.data?.message || "Error occurred.";
                        }
                        Swal.fire({
                            title: 'Error',
                            html: `<div class="text-left text-sm text-red-600">${errorMessage}</div>`,
                            icon: 'error'
                        });
                    }).finally(() => {
                        saveBtn.disabled = false;
                    });
                });
            }

            const searchInput = document.getElementById('noticeSearch');
            if (searchInput) {
                searchInput.addEventListener('input', () => {
                    clearTimeout(searchTimer);
                    searchTimer = setTimeout(() => fetchNotices(1), 500);
                });
            }

            window.addEventListener('click', (e) => {
                closeOnOutsideClick(e, 'noticeModal');
                closeOnOutsideClick(e, 'filterModal');
                closeOnOutsideClick(e, 'exportModal');
            });

            loadClasses();
        });
    </script>
@endsection
