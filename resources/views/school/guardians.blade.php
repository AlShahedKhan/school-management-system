@extends('layouts.school')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">

    <style>
        /* Global & Table Styles */
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
            transition: all .2s;
            border-radius: 0;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
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
            transition: all .2s;
            border-radius: 0;
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
            height: 32px;
            min-width: 32px;
            padding: 0 8px;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
            transition: all .2s;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .pagination-btn:hover:not(:disabled) {
            border-color: #2563eb;
            color: #2563eb;
        }

        .pagination-btn:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        .pagination-btn.active {
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
        }

        .form-input-fixed {
            width: 100%;
            border: 1px solid #cbd5e1 !important;
            padding: .4rem .6rem;
            border-radius: 0;
            font-size: .8rem;
            background: #fff;
            outline: none;
            display: block;
        }

        .form-input-fixed:focus {
            border-color: #2563eb !important;
            box-shadow: none;
        }

        .action-icon-btn {
            font-size: 1.25rem;
            padding: 0px !important;
            transition: color .2s;
            background: none;
            border: none;
            cursor: pointer;
        }

        .custom-scrollbar::-webkit-scrollbar {
            height: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
        }

        .premium-modal {
            backdrop-filter: blur(4px);
        }

        .modal-content-sharp {
            border-radius: 0 !important;
        }

        .image-preview-box {
            width: 45px;
            height: 45px;
            border: 1px dashed #cbd5e1;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: #f8fafc;
        }

        .image-preview-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
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
                            <input type="text" id="studentSearch" placeholder="Search name, ID or mobile..."
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

                        <a href="{{ route('school.student-admission') }}"
                            class="btn-outline-premium border border-gray-200 px-0.5 sm:px-4 h-7 sm:h-9 text-[9px] sm:text-xs tracking-wider flex items-center justify-center flex-1 lg:flex-none whitespace-nowrap">
                            Add Student
                        </a>
                    </div>
                </div>

                <div class="relative w-full mt-3 lg:hidden">
                    <i class="mdi mdi-magnify absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="studentSearchMobile" placeholder="Search name, ID or mobile..."
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
                            Guardian Filter
                        </h3>
                        <div class="h-[1px] w-full bg-gray-200 mt-2.5"></div>
                    </div>

                    <div class="mt-3 mb-4 space-y-3">
                        {{-- Class --}}
                        <div>
                            <label class="text-[10px] text-gray-500 block mb-1">Class</label>
                            <div class="relative">
                                <select id="classFilter"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                    <option value="">All Classes</option>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}">Class {{ $i }}</option>
                                    @endfor
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Group --}}
                        <div>
                            <label class="text-[10px] text-gray-500 block mb-1">Group</label>
                            <div class="relative">
                                <select id="groupFilter"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                    <option value="">Select Group</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Section --}}
                        <div>
                            <label class="text-[10px] text-gray-500 block mb-1">Section</label>
                            <div class="relative">
                                <select id="sectionFilter"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                    <option value="">Select Section</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-400">
                                    <i class="fas fa-chevron-down text-[9px]"></i>
                                </div>
                            </div>
                        </div>

                        {{-- Session --}}
                        <div>
                            <label class="text-[10px] text-gray-500 block mb-1">Session</label>
                            <div class="relative">
                                <select id="sessionFilter"
                                    class="form-input-fixed w-full py-1.5 pl-2 pr-8 text-xs border border-gray-100 outline-none focus:border-blue-500 appearance-none bg-white"
                                    style="border-radius: 0; height: 32px;">
                                    <option value="">Select Session</option>
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
                <div class="table-responsive custom-scrollbar">
                    <table class="min-w-[1600px]">
                        <thead>
                            <tr>
                                <th>sl</th>
                                <th>class</th>
                                <th>group</th>
                                <th>section</th>
                                <th>session</th>
                                <th>id number</th>
                                <th>student name</th>
                                <th>guardian name</th>
                                <th>relation</th>
                                <th>division</th>
                                <th>district</th>
                                <th>upazila</th>
                                <th>village</th>
                                <th>mobile number</th>
                                <th class="text-center">action</th>
                            </tr>
                        </thead>
                        <tbody id="studentTableBody" class="bg-white divide-y divide-gray-100">
                            {{-- AJAX Load --}}
                        </tbody>
                    </table>
                </div>
                <div class="pagination-container">
                    <div class="text-[11px] text-gray-500 lowercase tracking-tight" id="paginationInfo">
                        0 of 0
                    </div>
                    <div class="flex items-center gap-1" id="paginationControls"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Updated Student Profile Modal --}}
    <div id="studentModal"
        class="fixed inset-0 bg-gray-900/60 flex items-center justify-center hidden z-[100] px-8 sm:px-40 py-12 backdrop-blur-sm overflow-y-auto">

        <div
            class="bg-white w-full max-w-2xl modal-content-sharp shadow-2xl overflow-hidden flex flex-col my-auto max-h-[70vh] sm:max-h-[85vh] mx-auto border border-gray-100">

            <div class="px-5 py-3 border-b flex justify-center items-center bg-white sticky top-0 z-10">
                <h3 class="text-gray-800 text-[13px] font-medium leading-tight text-center capitalize tracking-normal">
                    Update guardian profile
                </h3>
            </div>

            <form id="studentForm" class="flex flex-col overflow-hidden m-0" enctype="multipart/form-data">
                <input type="hidden" name="student_id" id="student_id">

                <div class="overflow-y-auto custom-scrollbar p-4 sm:p-6 flex-grow bg-gray-50/30">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-4">

                        {{-- Student Info Section --}}
                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Student
                                name</label>
                            <input type="text" name="student_name" id="edit_name" required
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;" />
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">ID
                                number</label>
                            <input type="text" name="student_id_number" id="edit_id_num" required
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;" />
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Father
                                name</label>
                            <input type="text" name="father_name" id="edit_father" required
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;" />
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Mother
                                name</label>
                            <input type="text" name="mother_name" id="edit_mother" required
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;" />
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Class</label>
                            <input type="text" name="class" id="edit_class" required
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;" />
                        </div>

                        <div class="col-span-1">
                            <label
                                class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Session</label>
                            <input type="text" name="session" id="edit_session" required
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;" />
                        </div>

                        {{-- Guardian Details Section --}}
                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Guardian
                                name</label>
                            <input type="text" name="guardian_name" id="edit_g_name" required
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;" />
                        </div>

                        <div class="col-span-1">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Guardian
                                mobile</label>
                            <input type="text" name="guardian_mobile" id="edit_g_mobile" required
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;" />
                        </div>

                        <div class="col-span-1">
                            <label
                                class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Relation</label>
                            <input type="text" name="relation" id="edit_g_relation"
                                class="form-input-fixed w-full border border-gray-200 py-1.5 px-3 text-xs h-[32px]"
                                style="border-radius: 0;" />
                        </div>

                        <div class="col-span-1 sm:col-span-2">
                            <label class="block text-[10px] capitalize tracking-normal text-gray-500 mb-1.5">Update
                                photo</label>
                            <div class="flex gap-3">
                                <div class="flex-grow">
                                    <input type="file" name="image" id="photoInput"
                                        class="form-input-fixed w-full text-[11px] file:mr-4 file:py-1 file:px-3 file:border file:border-gray-100 file:text-[10px] file:bg-gray-50 file:text-gray-600 border border-gray-200 h-[32px] flex items-center"
                                        accept="image/*" style="border-radius: 0;" />
                                </div>
                                <div class="w-[32px] h-[32px] border border-gray-200 bg-white flex items-center justify-center overflow-hidden flex-shrink-0"
                                    id="imagePreview" style="border-radius: 0;">
                                    <i class="mdi mdi-camera text-gray-300 text-sm"></i>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Footer Actions --}}
                <div
                    class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-white flex flex-row sm:justify-end gap-2 sticky bottom-0">
                    <button type="button" onclick="closeModal()"
                        class="w-1/2 sm:w-auto sm:px-8 h-[32px] btn-outline-secondary border border-gray-200 text-[10px] tracking-normal capitalize transition-all hover:bg-gray-50 flex items-center justify-center whitespace-nowrap"
                        style="border-radius: 0;">
                        Discard
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

    <script>
        let currentPage = 1;
        const studentModal = document.getElementById('studentModal');
        const photoInput = document.getElementById('photoInput');
        const imagePreview = document.getElementById('imagePreview');

        photoInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => imagePreview.innerHTML = `<img src="${e.target.result}" />`;
                reader.readAsDataURL(file);
            }
        });

        function closeModal() {
            studentModal.classList.add('hidden');
            document.getElementById('studentForm').reset();
            imagePreview.innerHTML = `<i class="mdi mdi-camera text-gray-300"></i>`;
        }

        function fetchStudents(page = 1) {
            currentPage = page;
            const search = document.getElementById('studentSearch').value;
            const classVal = document.getElementById('classFilter').value;

            axios.get('{{ url('/api/guardians') }}', {
                    params: {
                        search: search,
                        student_class: classVal,
                        class: classVal,
                        page: page
                    }
                })
                .then(res => {
                    const students = res.data.data;
                    const from = res.data.from || 0;
                    const tbody = document.getElementById('studentTableBody');

                    if (!students || students.length === 0) {
                        tbody.innerHTML =
                            `<tr><td colspan="15" class="text-center py-10 text-gray-400">no records found.</td></tr>`;
                        return;
                    }

                    let rows = '';
                    students.forEach((s, index) => {
                        const g = s.guardian || {};
                        const displayMobile = g.mobile || s.mobile || '-';
                        const sl = from + index;

                        // Logic to pull names from relationships or fallback to IDs
                        const className = s.school_class?.class_name || s.class || '-';
                        const groupName = s.school_group?.group_name || s.group || '-';
                        const sectionName = s.school_section?.section_name || s.section || '-';

                        // Specific fix for Session and Village
                        const sessionYear = s.school_session?.session_year || s.session || '-';
                        const villageName = s.current_village || '-';

                        rows += `<tr class="hover:bg-slate-50 border-b border-gray-100">
                        <td class="text-gray-500 font-mono">${sl}</td>
                        <td class="capitalize text-gray-600">${className}</td>
                        <td class="capitalize text-gray-600">${groupName}</td>
                        <td class="capitalize text-gray-600">${sectionName}</td>
                        <td class="text-gray-600">${sessionYear}</td>
                        <td class="font-mono text-gray-600">${s.student_id_number || '-'}</td>
                        <td class="capitalize text-gray-600">${s.student_name || '-'}</td>
                        <td class="capitalize text-gray-600">${g.name || '-'}</td>
                        <td class="capitalize text-gray-600">${g.relation || '-'}</td>
                        <td class="capitalize text-gray-600">${s.current_division || '-'}</td>
                        <td class="capitalize text-gray-600">${s.current_district || '-'}</td>
                        <td class="capitalize text-gray-600">${s.current_upazila || '-'}</td>
                        <td class="capitalize text-gray-600">${villageName}</td>
                        <td class="text-gray-600">${displayMobile}</td>
                        <td>
                            <div class="flex justify-center gap-3">
                                <button onclick="editStudent(${s.id})" class="action-icon-btn text-blue-500"><i class="far fa-edit" style="font-size: 15px;"></i>
                                </button>
                                <button onclick="deleteStudent(${s.id})" class="action-icon-btn text-red-400"><i class="far fa-trash-alt" style="font-size: 15px;"></i>
                                </button>
                            </div>
                        </td>
                    </tr>`;
                    });

                    tbody.innerHTML = rows;
                    renderPagination(res.data);
                })
                .catch(err => console.error("fetch error:", err));
        }

        function renderPagination(data) {
            const controls = document.getElementById('paginationControls');
            const info = document.getElementById('paginationInfo');
            info.innerText = `${data.to || 0} of ${data.total}`;
            controls.innerHTML = '';

            const prevDisabled = !data.prev_page_url ? 'disabled' : '';
            controls.innerHTML +=
                `<button onclick="fetchStudents(${data.current_page - 1})" class="pagination-btn" ${prevDisabled}><i class="mdi mdi-chevron-left"></i></button>`;

            for (let i = 1; i <= data.last_page; i++) {
                if (data.last_page > 5 && i > 3 && i < data.last_page) {
                    if (i === 4) controls.innerHTML += `<span class="px-1 text-gray-400">...</span>`;
                    continue;
                }
                controls.innerHTML +=
                    `<button onclick="fetchStudents(${i})" class="pagination-btn ${data.current_page === i ? 'active' : ''}">${i}</button>`;
            }

            const nextDisabled = !data.next_page_url ? 'disabled' : '';
            controls.innerHTML +=
                `<button onclick="fetchStudents(${data.current_page + 1})" class="pagination-btn" ${nextDisabled}><i class="mdi mdi-chevron-right"></i></button>`;
        }

        function editStudent(id) {
            axios.get(`{{ url('/api/guardians') }}/${id}`).then(res => {
                const s = res.data;
                const g = s.guardian || {};
                document.getElementById('student_id').value = s.id;
                document.getElementById('edit_name').value = s.student_name;
                document.getElementById('edit_id_num').value = s.student_id_number;
                document.getElementById('edit_father').value = s.father_name;
                document.getElementById('edit_mother').value = s.mother_name;
                document.getElementById('edit_class').value = s.class;
                document.getElementById('edit_session').value = s.session;
                document.getElementById('edit_g_name').value = g.name || '';
                document.getElementById('edit_g_mobile').value = g.mobile || '';
                document.getElementById('edit_g_relation').value = g.relation || '';
                imagePreview.innerHTML = s.image ? `<img src="/storage/${s.image}" />` :
                    `<i class="mdi mdi-camera text-gray-300"></i>`;
                studentModal.classList.remove('hidden');
            });
        }

        document.getElementById('studentForm').onsubmit = function(e) {
            e.preventDefault();
            const id = document.getElementById('student_id').value;
            const formData = new FormData(this);
            formData.append('_method', 'PUT');
            axios.post(`{{ url('/api/guardians') }}/${id}`, formData)
                .then(() => {
                    Toastify({
                        text: "updated successfully",
                        style: {
                            background: "#10b981"
                        }
                    }).showToast();
                    closeModal();
                    fetchStudents(currentPage);
                })
                .catch(() => Swal.fire('error', 'update failed.', 'error'));
        };

        function deleteStudent(id) {
            Swal.fire({
                title: 'are you sure?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3b82f6',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'yes, delete',
                cancelButtonText: 'cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(`{{ url('/api/guardians') }}/${id}`).then(() => {
                        Toastify({
                            text: "deleted",
                            style: {
                                background: "#f87171"
                            }
                        }).showToast();
                        fetchStudents(currentPage);
                    });
                }
            });
        }

        document.getElementById('studentSearch').addEventListener('input', () => fetchStudents(1));
        document.getElementById('classFilter').addEventListener('change', () => fetchStudents(1));

        document.addEventListener('DOMContentLoaded', function() {
            const toggleModal = (id, show) => {
                document.getElementById(id).classList.toggle('hidden', !show);
            };

            document.getElementById('btnFilter').addEventListener('click', () => toggleModal('filterModal', true));
            document.getElementById('resetFilter').addEventListener('click', () => toggleModal('filterModal',
                false));
            document.getElementById('applyFilter').addEventListener('click', () => toggleModal('filterModal',
                false));
            document.getElementById('btnExport').addEventListener('click', () => toggleModal('exportModal', true));
            document.getElementById('closeExport').addEventListener('click', () => toggleModal('exportModal',
                false));

            window.onclick = function(event) {
                if (event.target.classList.contains('premium-modal')) {
                    event.target.classList.add('hidden');
                }
            };
        });

        fetchStudents();
    </script>
@endsection