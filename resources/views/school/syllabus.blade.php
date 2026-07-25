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
            font-family: 'Inter', sans-serif;
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

        .btn-outline-secondary {
            background: transparent;
            border: 1.5px solid #64748b;
            color: #64748b;
            font-weight: 600;
            transition: all .2s ease;
            border-radius: 0;
            cursor: pointer;
        }

        .btn-outline-secondary:hover {
            background: #64748b;
            color: #fff;
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
            padding: 5px 10px;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            transition: all 0.2s;
        }

        .pagination-btn:hover:not(:disabled) {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .pagination-btn.active {
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
        }

        .pagination-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .form-input-fixed {
            width: 100%;
            border: 1px solid #cbd5e1 !important;
            padding: .5rem .7rem;
            border-radius: 0;
            font-size: .85rem;
            background: #fff;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-input-fixed:focus {
            border-color: #2563eb !important;
        }

        .action-icon-btn {
            font-size: 1.25rem;
            padding: 0px !important;
            background: none;
            border: none;
            cursor: pointer;
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

                        {{-- Desktop Search: Inside the left column --}}
                        <div class="relative w-full sm:w-64 mt-3 hidden lg:block">
                            <i class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" id="syllabusSearch" placeholder="Search syllabus..."
                                class="pl-8 pr-3 py-2 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                                style="border-radius: 0;" />
                        </div>
                    </div>

                    <div class="flex flex-row items-center gap-1 w-full lg:w-auto">
                        <button id="btnFilter"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Filter
                        </button>

                        <button id="btnExport"
                            class="btn-outline-secondary border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Export
                        </button>

                        <button onclick="openModal()"
                            class="btn-outline-premium border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Add Syllabus
                        </button>
                    </div>
                </div>

                {{-- Mobile Search: Full width at the bottom of the card --}}
                <div class="relative w-full mt-3 lg:hidden">
                    <i class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="syllabusSearchMobile" placeholder="Search syllabus..."
                        class="pl-8 pr-3 py-1.5 w-full border border-gray-200 text-xs outline-none focus:border-blue-500"
                        style="border-radius: 0;" />
                </div>
            </div>

            {{-- Filter Modal --}}
            <div id="filterModal"
                class="premium-modal fixed inset-0 bg-black/50 hidden z-[9999] flex items-center justify-center p-12 sm:p-20">
                <div class="bg-white p-4 w-full max-w-[320px] modal-content-sharp shadow-2xl" style="border-radius: 0;">

                    <div>
                        <h3
                            class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                            Syllabus Filter
                        </h3>
                        <div class="h-[1px] w-full bg-gray-200 mt-2.5"></div>
                    </div>

                    <div class="mt-3 mb-4 space-y-3">
                        <div>
                            <label class="text-[10px] text-gray-500 block mb-1">Class</label>
                            <div class="relative">
                                <select id="classFilter"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                    <option value="">Select Class...</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="text-[10px] text-gray-500 block mb-1">Group</label>
                            <div class="relative">
                                <select id="groupFilter"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                    <option value="">Select Group...</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="text-[10px] text-gray-500 block mb-1">Section</label>
                            <div class="relative">
                                <select id="sectionFilter"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                    <option value="">Select Section...</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="text-[10px] text-gray-500 block mb-1">Session</label>
                            <div class="relative">
                                <select id="sessionFilter"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                    <option value="">Select Session...</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="text-[10px] text-gray-500 block mb-1">Exam</label>
                            <div class="relative">
                                <select id="examFilter"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                    <option value="">Select Exam...</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button id="resetFilter"
                            class="btn-outline-secondary border border-gray-200 w-full text-[11px] capitalize flex items-center justify-center"
                            style="border-radius: 0; height: 32px;">Reset</button>
                        <button id="applyFilter"
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
                            class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap">PDF</button>
                        <button
                            class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap">EXCEL</button>
                        <button
                            class="btn-outline-secondary border border-gray-200 py-1.5 px-4 text-[10px] tracking-widest flex items-center justify-center w-full whitespace-nowrap">PRINT</button>
                        <button id="closeExport"
                            class="mt-1 py-1.5 text-[10px] text-gray-400 hover:text-gray-600 w-full text-center border border-gray-200 transition-all">Cancel</button>
                    </div>
                </div>
            </div>
            <div class="table-card">
                <div class="table-responsive">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th width="50">SL</th>
                                <th>Class</th>
                                <th>Group</th>
                                <th>Section</th>
                                <th>Session</th>
                                <th>Subject</th>
                                <th>Exam Name</th>
                                <th>Start</th>
                                <th>End</th>
                                <th width="100" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="syllabusTableBody" class="bg-white divide-y divide-gray-100"></tbody>
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

    {{-- Syllabus Configuration Modal --}}
    <div id="syllabusModal"
        class="fixed inset-0 bg-gray-900/60 flex items-center justify-center hidden z-[100] px-8 sm:px-40 py-12 backdrop-blur-sm overflow-y-auto">

        <div
            class="bg-white w-full max-w-2xl modal-content-sharp shadow-2xl overflow-hidden flex flex-col my-auto max-h-[70vh] sm:max-h-[85vh] mx-auto border border-gray-100">

            {{-- Header --}}
            <div class="px-5 py-3 border-b flex justify-center items-center bg-white sticky top-0 z-10">
                <h3 id="modalTitle"
                    class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                    Create Syllabus
                </h3>
            </div>

            <form id="syllabusForm" class="flex flex-col overflow-hidden m-0">
                <input type="hidden" id="record_id">

                {{-- Scrollable Content Area --}}
                <div class="overflow-y-auto custom-scrollbar p-4 sm:p-6 flex-grow bg-gray-50/30">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-4">

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Class</label>
                            <select id="class_id" onchange="filterGroups()" 
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;">
                                <option value="">Choose Class...</option>
                            </select>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Group</label>
                            <select id="group_id" onchange="filterSections()"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;">
                                <option value="">No Group</option>
                            </select>
                        </div>

                        <div class="col-span-1">
                            <label
                                class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Section</label>
                            <select id="section_id" onchange="refreshDependents()"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;">
                                <option value="">No Section</option>
                            </select>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Academic
                                Session</label>
                            <select id="session_id"  onchange="loadExams()"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;">
                                <option value="">Choose Session...</option>
                            </select>
                        </div>

                        {{-- Divider --}}
                        <div class="col-span-1 sm:col-span-2 mt-2 pt-4 border-t border-gray-200/60">
                            <label class="block text-[10px] capitalize tracking-normal text-blue-600 mb-1.5 font-medium">
                                Academic & Subject Details
                            </label>
                        </div>

                        <div class="col-span-1">
                            <label
                                class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Subject</label>
                            <select id="subject_id" 
                                class="form-input-fixed w-full border border-blue-200 py-1.5 px-3 text-xs h-[32px] bg-blue-50/10"
                                style="border-radius: 0;">
                                <option value="">Choose Subject...</option>
                            </select>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Exam
                                Name</label>
                            <select id="exam_name" 
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;">
                                <option value="">Select Exam...</option>
                            </select>
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Start
                                Page</label>
                            <input type="text" id="start_page" placeholder="e.g. Chapter 1"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;">
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">End
                                Page</label>
                            <input type="text" id="end_page" placeholder="e.g. Page 50"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;">
                        </div>

                    </div>
                </div>

                {{-- Footer Actions --}}
                <div
                    class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-white flex flex-row sm:justify-end gap-2 sticky bottom-0">
                    <button type="button" onclick="closeModal()"
                        class="w-1/2 sm:w-auto sm:px-8 h-[32px] btn-outline-secondary border border-gray-200 text-[10px] tracking-normal capitalize transition-all hover:bg-gray-50 flex items-center justify-center whitespace-nowrap"
                        style="border-radius: 0;">
                        Cancel
                    </button>
                    <button type="submit" id="saveBtn"
                        class="w-1/2 sm:w-auto sm:px-12 h-[32px] btn-outline-premium border border-gray-200 text-[10px] tracking-normal capitalize flex items-center justify-center whitespace-nowrap"
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
        let currentFilterClass = '';
        let currentFilterGroup = '';
        let currentFilterSection = '';
        let currentFilterSession = '';
        let currentFilterExam = '';

        /**
         * Initial data load for the modal. 
         */
        async function initStaticData() {
            try {
                const resClasses = await axios.get('/api/get-school-classes');

                const clsSel = document.getElementById('class_id');
                clsSel.innerHTML = '<option value="">Choose Class...</option>';
                resClasses.data.data.forEach(item => {
                    clsSel.innerHTML +=
                        `<option value="${item.id}" data-name="${item.class_name}">${item.class_name}</option>`;
                });

                // Reset dependent dropdowns
                document.getElementById('session_id').innerHTML = '<option value="">Choose Class first...</option>';
                document.getElementById('group_id').innerHTML = '<option value="">No Group</option>';
                document.getElementById('section_id').innerHTML = '<option value="">No Section</option>';

            } catch (err) {
                console.error("Load Error:", err);
            }
        }

        /**
         * Triggered when Class changes. Fetches Groups and Subjects.
         * Also triggers session and section refreshes.
         */
        async function filterGroups() {
            const classId = document.getElementById('class_id').value;
            const grpSel = document.getElementById('group_id');
            const subSel = document.getElementById('subject_id');

            grpSel.innerHTML = '<option value="">No Group</option>';
            subSel.innerHTML = '<option value="">Choose Subject...</option>';

            if (classId) {
                try {
                    const [resGroups, resSubjects] = await Promise.all([
                        axios.get('/api/get-school-groups', {
                            params: {
                                class_id: classId
                            }
                        }),
                        axios.get('/api/get-school-subjects', {
                            params: {
                                class_id: classId
                            }
                        })
                    ]);

                    resGroups.data.data.forEach(g => {
                        grpSel.innerHTML +=
                            `<option value="${g.id}" data-name="${g.group_name}">${g.group_name}</option>`;
                    });

                    resSubjects.data.data.forEach(sub => {
                        subSel.innerHTML += `<option value="${sub.id}">${sub.subject_name}</option>`;
                    });

                } catch (err) {
                    console.error("Dependency Load Error:", err);
                }
            }

            // When class changes, we must refresh sections and sessions
            await filterSections();
        }

        /**
         * Fetches Sections and refreshes Sessions based on Class + Group selection.
         */
        async function filterSections() {
            const classId = document.getElementById('class_id').value;
            const groupId = document.getElementById('group_id').value;
            const secSel = document.getElementById('section_id');
            secSel.innerHTML = '<option value="">No Section</option>';

            if (classId) {
                try {
                    const res = await axios.get('/api/get-school-sections', {
                        params: {
                            class_id: classId,
                            group_id: groupId
                        }
                    });
                    res.data.data.forEach(s => {
                        secSel.innerHTML +=
                            `<option value="${s.id}" data-name="${s.section_name}">${s.section_name}</option>`;
                    });
                } catch (err) {
                    console.error("Section Load Error:", err);
                }
            }

            // Refresh sessions whenever class, group, or section context changes
            await refreshSessions();
        }

        /**
         * Specifically fetches Sessions based on Class, Group, and Section.
         */
        async function refreshSessions() {
            const classId = document.getElementById('class_id').value;
            const groupId = document.getElementById('group_id').value;
            const sectionId = document.getElementById('section_id').value;
            const sessSel = document.getElementById('session_id');

            sessSel.innerHTML = '<option value="">Choose Session...</option>';

            if (classId) {
                try {
                    const res = await axios.get('/api/get-school-sessions', {
                        params: {
                            class_id: classId,
                            group_id: groupId,
                            section_id: sectionId
                        }
                    });

                    res.data.data.forEach(item => {
                        sessSel.innerHTML +=
                            `<option value="${item.id}" data-name="${item.session_year}">${item.session_year || 'N/A'}</option>`;
                    });
                } catch (err) {
                    console.error("Session Refresh Error:", err);
                }
            }
            refreshDependents();
        }

        function refreshDependents() {
            loadExams();
        }

        async function loadExams() {
            const classEl = document.getElementById('class_id');
            const groupEl = document.getElementById('group_id');
            const sectionEl = document.getElementById('section_id');
            const sessionEl = document.getElementById('session_id');
            const examSel = document.getElementById('exam_name');

            if (!classEl.value || !sessionEl.value) {
                examSel.innerHTML = '<option value="">Select Exam...</option>';
                return;
            }

            try {
                const params = {
                    class_name: classEl.options[classEl.selectedIndex].getAttribute('data-name'),
                    session_name: sessionEl.options[sessionEl.selectedIndex].getAttribute('data-name'),
                    group_name: groupEl.value ? groupEl.options[groupEl.selectedIndex].getAttribute('data-name') :
                        '',
                    section_name: sectionEl.value ? sectionEl.options[sectionEl.selectedIndex].getAttribute(
                        'data-name') : ''
                };

                const res = await axios.get('/api/get-school-exams', {
                    params
                });
                examSel.innerHTML = '<option value="">Select Exam...</option>';
                res.data.data.forEach(ex => {
                    examSel.innerHTML += `<option value="${ex.exam_name}">${ex.exam_name}</option>`;
                });
            } catch (err) {
                console.error("Exam Load Error", err);
            }
        }

        async function openModal() {
            document.getElementById('syllabusForm').reset();
            document.getElementById('record_id').value = '';
            document.getElementById('modalTitle').innerText = "Set Syllabus";
            document.getElementById('syllabusModal').classList.remove('hidden');
            await initStaticData();
        }

        function closeModal() {
            document.getElementById('syllabusModal').classList.add('hidden');
        }

        // Filter Modal Functions
        async function loadClassesForFilter() {
            try {
                const res = await axios.get('/api/get-school-classes');
                const select = document.getElementById('classFilter');
                const data = res.data.data;
                select.innerHTML = '<option value="">Select Class...</option>';
                data.forEach(c => {
                    select.innerHTML += `<option value="${c.id}">${c.class_name}</option>`;
                });
            } catch (err) {
                console.error("Filter Classes Error:", err);
            }
        }

        async function loadGroupsForFilter(classId) {
            const select = document.getElementById('groupFilter');
            select.innerHTML = '<option value="">Select Group...</option>';
            if (!classId) return;
            try {
                const res = await axios.get('/api/get-school-groups', {
                    params: { class_id: classId }
                });
                res.data.data.forEach(g => {
                    select.innerHTML += `<option value="${g.id}">${g.group_name}</option>`;
                });
            } catch (err) {
                console.error("Filter Groups Error:", err);
            }
        }

        async function loadSectionsForFilter(classId, groupId) {
            const select = document.getElementById('sectionFilter');
            select.innerHTML = '<option value="">Select Section...</option>';
            if (!classId) return;
            try {
                const res = await axios.get('/api/get-school-sections', {
                    params: { class_id: classId, group_id: groupId }
                });
                res.data.data.forEach(s => {
                    select.innerHTML += `<option value="${s.id}">${s.section_name}</option>`;
                });
            } catch (err) {
                console.error("Filter Sections Error:", err);
            }
        }

        async function loadSessionsForFilter(classId, groupId, sectionId) {
            try {
                const res = await axios.get('/api/get-school-sessions', {
                    params: {
                        class_id: classId,
                        group_id: groupId,
                        section_id: sectionId
                    }
                });
                const select = document.getElementById('sessionFilter');
                const data = res.data.data;
                select.innerHTML = '<option value="">Select Session...</option>';
                
                data.forEach(s => {
                    select.innerHTML += `<option value="${s.id}">${s.session_year || 'N/A'}</option>`;
                });
            } catch (err) {
                console.error("Filter Sessions Error:", err);
            }
        }

        async function loadExamsForFilter(sessionId, classId, groupId, sectionId) {
            const select = document.getElementById('examFilter');
            select.innerHTML = '<option value="">Loading...</option>';

            try {
                // Resolve names from the filter dropdowns (API filters by name, not ID)
                const classEl   = document.getElementById('classFilter');
                const groupEl   = document.getElementById('groupFilter');
                const sectionEl = document.getElementById('sectionFilter');
                const sessionEl = document.getElementById('sessionFilter');

                const params = {};
                if (classEl.value)
                    params.class_name   = classEl.options[classEl.selectedIndex].text;
                if (groupEl.value)
                    params.group_name   = groupEl.options[groupEl.selectedIndex].text;
                if (sectionEl.value)
                    params.section_name = sectionEl.options[sectionEl.selectedIndex].text;
                if (sessionEl.value)
                    params.session_name = sessionEl.options[sessionEl.selectedIndex].text;

                const res  = await axios.get('/api/get-school-exams', { params });
                const data = res.data.data || [];

                select.innerHTML = '<option value="">Select Exam...</option>';
                if (data.length === 0) {
                    select.innerHTML = '<option value="">No exams found</option>';
                    return;
                }
                data.forEach(e => {
                    if (e.exam_name) {
                        select.innerHTML += `<option value="${e.exam_name}">${e.exam_name}</option>`;
                    }
                });
            } catch (err) {
                console.error("Filter Exams Error:", err);
                select.innerHTML = '<option value="">Failed to load</option>';
            }
        }

        function handleClassFilterChange() {
            const classId = document.getElementById('classFilter').value;
            const groupSelect = document.getElementById('groupFilter');
            const sectionSelect = document.getElementById('sectionFilter');
            const sessionSelect = document.getElementById('sessionFilter');
            const examSelect = document.getElementById('examFilter');
            
            // Reset all dependent dropdowns
            groupSelect.innerHTML = '<option value="">Select Group...</option>';
            sectionSelect.innerHTML = '<option value="">Select Section...</option>';
            sessionSelect.innerHTML = '<option value="">Select Session...</option>';
            examSelect.innerHTML = '<option value="">Select Exam...</option>';
            
            if (classId) {
                loadGroupsForFilter(classId);
            }
        }

        function handleGroupFilterChange() {
            const classId = document.getElementById('classFilter').value;
            const groupId = document.getElementById('groupFilter').value;
            const sectionSelect = document.getElementById('sectionFilter');
            const sessionSelect = document.getElementById('sessionFilter');
            const examSelect = document.getElementById('examFilter');
            
            // Reset dependent dropdowns
            sectionSelect.innerHTML = '<option value="">Select Section...</option>';
            sessionSelect.innerHTML = '<option value="">Select Session...</option>';
            examSelect.innerHTML = '<option value="">Select Exam...</option>';
            
            if (classId && groupId) {
                loadSectionsForFilter(classId, groupId);
            }
        }

        function handleSectionFilterChange() {
            const classId = document.getElementById('classFilter').value;
            const groupId = document.getElementById('groupFilter').value;
            const sectionId = document.getElementById('sectionFilter').value;
            const sessionSelect = document.getElementById('sessionFilter');
            const examSelect = document.getElementById('examFilter');
            
            // Reset dependent dropdowns
            sessionSelect.innerHTML = '<option value="">Select Session...</option>';
            examSelect.innerHTML = '<option value="">Select Exam...</option>';
            
            if (classId && groupId && sectionId) {
                loadSessionsForFilter(classId, groupId, sectionId);
            }
        }

        function handleSessionFilterChange() {
            const classId = document.getElementById('classFilter').value;
            const groupId = document.getElementById('groupFilter').value;
            const sectionId = document.getElementById('sectionFilter').value;
            const sessionId = document.getElementById('sessionFilter').value;
            const examSelect = document.getElementById('examFilter');
            
            // Reset exam dropdown
            examSelect.innerHTML = '<option value="">Select Exam...</option>';
            
            if (sessionId) {
                loadExamsForFilter(sessionId, classId, groupId, sectionId);
            }
        }

        function fetchSyllabuses(page = 1) {
            currentPage = page;
            const search = document.getElementById('syllabusSearch').value;
            axios.get('/api/school-syllabuses', {
                params: {
                    search,
                    page,
                    class_id: currentFilterClass,
                    group_id: currentFilterGroup,
                    section_id: currentFilterSection,
                    session_id: currentFilterSession,
                    exam_name: currentFilterExam
                }
            }).then(res => {
                const meta = res.data;
                const tbody = document.getElementById('syllabusTableBody');
                tbody.innerHTML = '';
                if (!meta.data || meta.data.length === 0) {
                    tbody.innerHTML =
                        `<tr><td colspan="10" class="text-center py-10 text-gray-400">No records found</td></tr>`;
                    return;
                }
                meta.data.forEach((item, index) => {
                    tbody.innerHTML += `
                <tr>
                    <td class="text-center text-gray-400">${meta.from + index}</td>
                    <td class="text-gray-600">${item.school_class?.class_name || '-'}</td>
                    <td class="text-[11px] text-gray-500">${item.school_group?.group_name || '-'}</td>
                    <td class="text-gray-600">${item.school_section?.section_name || '-'}</td>
                    <td class="text-gray-600">${item.school_session?.session_year || '-'}</td>
                    <td class="text-gray-600">${item.school_subject?.subject_name || '-'}</td>
                    <td class="text-gray-600 text-[11px]">${item.exam_name}</td>
                    <td>${item.start_page || '-'}</td>
                    <td>${item.end_page || '-'}</td>
                    <td>
                        <div class="flex justify-center gap-3">
                        <button onclick="editRecord(${item.id})" class="action-icon-btn text-blue-500"><i class="far fa-edit" style="font-size: 15px;"></i></button>
                        <button onclick="deleteRecord(${item.id})" class="action-icon-btn text-red-400"><i class="far fa-trash-alt" style="font-size: 15px;"></i></button>
                        </div>
                    </td>
                </tr>`;
                });
                renderPagination(meta);
            });
        }

        function renderPagination(meta) {
            const controls = document.getElementById('paginationControls');
            document.getElementById('paginationInfo').innerText = `${meta.to || 0} of ${meta.total}`;
            controls.innerHTML = '';
            const prevBtn = document.createElement('button');
            prevBtn.className = 'pagination-btn';
            prevBtn.innerHTML = '<i class="mdi mdi-chevron-left"></i>';
            prevBtn.disabled = meta.current_page === 1;
            prevBtn.onclick = () => fetchSyllabuses(meta.current_page - 1);
            controls.appendChild(prevBtn);
            for (let i = 1; i <= meta.last_page; i++) {
                if (i === 1 || i === meta.last_page || (i >= meta.current_page - 1 && i <= meta.current_page + 1)) {
                    const btn = document.createElement('button');
                    btn.className = `pagination-btn ${meta.current_page === i ? 'active' : ''}`;
                    btn.innerText = i;
                    btn.onclick = () => fetchSyllabuses(i);
                    controls.appendChild(btn);
                }
            }
            const nextBtn = document.createElement('button');
            nextBtn.className = 'pagination-btn';
            nextBtn.innerHTML = '<i class="mdi mdi-chevron-right"></i>';
            nextBtn.disabled = meta.current_page === meta.last_page;
            nextBtn.onclick = () => fetchSyllabuses(meta.current_page + 1);
            controls.appendChild(nextBtn);
        }

        document.getElementById('syllabusSearch').addEventListener('input', () => fetchSyllabuses(1));

        document.getElementById('syllabusForm').onsubmit = function(e) {
            e.preventDefault();
            const id = document.getElementById('record_id').value;
            const data = {
                session_id: document.getElementById('session_id').value,
                class_id: document.getElementById('class_id').value,
                group_id: document.getElementById('group_id').value,
                section_id: document.getElementById('section_id').value,
                subject_id: document.getElementById('subject_id').value,
                exam_name: document.getElementById('exam_name').value,
                start_page: document.getElementById('start_page').value,
                end_page: document.getElementById('end_page').value,
            };
            const req = id ? axios.put(`/api/school-syllabuses/${id}`, data) : axios.post('/api/school-syllabuses',
                data);
            req.then(() => {
                Toastify({
                    text: "Success",
                    style: {
                        background: "#10b981"
                    }
                }).showToast();
                closeModal();
                fetchSyllabuses(currentPage);
            }).catch(err => {
                const errors = err.response?.data?.errors;
                const message = err.response?.data?.message || 'Validation failed';
                if (errors) {
                    const formatted = Object.values(errors).flat().join('<br>');
                    Swal.fire({
                        title: 'Error',
                        html: formatted,
                        icon: 'error'
                    });
                } else {
                    Swal.fire('Error', message, 'error');
                }
            });
        };

        async function editRecord(id) {
            const res = await axios.get(`/api/school-syllabuses/${id}`);
            const item = res.data;
            document.getElementById('syllabusModal').classList.remove('hidden');

            await initStaticData();

            document.getElementById('record_id').value = item.id;
            document.getElementById('class_id').value = item.class_id;

            // Sequence dependency: Class -> Groups/Subjects -> Sections -> Sessions
            await filterGroups();

            document.getElementById('group_id').value = item.group_id || '';
            await filterSections();

            document.getElementById('section_id').value = item.section_id || '';
            document.getElementById('session_id').value = item.session_id;
            document.getElementById('subject_id').value = item.subject_id;

            await loadExams();
            document.getElementById('exam_name').value = item.exam_name;

            document.getElementById('start_page').value = item.start_page || '';
            document.getElementById('end_page').value = item.end_page || '';
            document.getElementById('modalTitle').innerText = "Edit Syllabus";
        }

        function deleteRecord(id) {
            Swal.fire({
                title: 'Delete?',
                icon: 'warning',
                showCancelButton: true
            }).then(r => {
                if (r.isConfirmed) axios.delete(`/api/school-syllabuses/${id}`).then(() => fetchSyllabuses(
                    currentPage));
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const toggleModal = (id, show) => {
                document.getElementById(id).classList.toggle('hidden', !show);
            };

            document.getElementById('btnFilter').addEventListener('click', () => {
                loadClassesForFilter();
                // Reset all filter dropdowns
                document.getElementById('classFilter').value = '';
                document.getElementById('groupFilter').innerHTML = '<option value="">Select Group...</option>';
                document.getElementById('sectionFilter').innerHTML = '<option value="">Select Section...</option>';
                document.getElementById('sessionFilter').innerHTML = '<option value="">Select Session...</option>';
                document.getElementById('examFilter').innerHTML = '<option value="">Select Exam...</option>';
                toggleModal('filterModal', true);
            });

            document.getElementById('classFilter').addEventListener('change', handleClassFilterChange);
            document.getElementById('groupFilter').addEventListener('change', handleGroupFilterChange);
            document.getElementById('sectionFilter').addEventListener('change', handleSectionFilterChange);
            document.getElementById('sessionFilter').addEventListener('change', handleSessionFilterChange);

            document.getElementById('resetFilter').addEventListener('click', () => {
                document.getElementById('classFilter').value = '';
                document.getElementById('groupFilter').innerHTML = '<option value="">Select Group...</option>';
                document.getElementById('sectionFilter').innerHTML = '<option value="">Select Section...</option>';
                document.getElementById('sessionFilter').innerHTML = '<option value="">Select Session...</option>';
                document.getElementById('examFilter').innerHTML = '<option value="">Select Exam...</option>';
                currentFilterClass = '';
                currentFilterGroup = '';
                currentFilterSection = '';
                currentFilterSession = '';
                currentFilterExam = '';
                currentPage = 1;
                fetchSyllabuses(1);
                toggleModal('filterModal', false);
            });

            document.getElementById('applyFilter').addEventListener('click', () => {
                currentFilterClass = document.getElementById('classFilter').value;
                currentFilterGroup = document.getElementById('groupFilter').value;
                currentFilterSection = document.getElementById('sectionFilter').value;
                currentFilterSession = document.getElementById('sessionFilter').value;
                currentFilterExam = document.getElementById('examFilter').value;
                currentPage = 1;
                fetchSyllabuses(1);
                toggleModal('filterModal', false);
            });

            document.getElementById('btnExport').addEventListener('click', () => toggleModal('exportModal', true));
            document.getElementById('closeExport').addEventListener('click', () => toggleModal('exportModal',
                false));

            window.onclick = function(event) {
                if (event.target.classList.contains('premium-modal')) {
                    event.target.classList.add('hidden');
                }
            };
        });

        fetchSyllabuses();
    </script>
@endsection