@extends('layouts.school')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">

    <style>
@import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&display=swap');
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
            padding: 1rem;
            box-sizing: border-box;
        }

        @media (max-width: 768px) {
            .main-view-container {
                padding-left: 0.5rem !important;
                padding-right: 0.5rem !important;
            }
        }

        .table-card {
            border: 1px solid #e5e7eb;
            background: #ffffff;
            border-radius: 0px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            width: 100%;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        /* ================= Table Container ================= */
        .table-responsive {
            width: 100% !important;
            overflow-x: auto !important;
            display: block !important;
            background: white !important;
            padding: 0 !important;
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
            border-radius: 3px !important;
        }

table {
width: 100% !important;
border-collapse: collapse !important;
table-layout: auto !important;
border: 1px solid #d1d5db !important;
font-size: 12px !important;
}
th {
padding: 4px 12px !important;
background: #f3f4f6 !important;
color: #1f2937 !important;
font-weight: 600 !important;
vertical-align: middle !important;
text-align: left !important;
text-transform: none !important;
border: 1px solid #d1d5db !important;
white-space: nowrap !important;
height: 32px !important;
}
th:last-child {
text-align: center !important;
}
tr {
border-bottom: 1px solid #d1d5db !important;
}
tr:hover td {
background-color: #f8fafc !important;
}
td {
padding: 4px 12px !important;
vertical-align: middle !important;
border: 1px solid #d1d5db !important;
font-size: 12px !important;
color: #374151 !important;
background: transparent !important;
white-space: nowrap !important;
height: 32px !important;
}
td:last-child {
text-align: center !important;
}

        /* Photo Styling */
        .student-photo {
            width: 40px !important;
            height: 40px !important;
            border-radius: 50% !important;
            object-fit: cover !important;
            border: 1px solid #e5e7eb !important;
        }

        /* Table Photo - Force Circular Shape */
        .table-photo {
            border-radius: 50% !important;
            width: 32px !important;
            height: 32px !important;
            object-fit: cover !important;
            border: 1px solid #e5e7eb !important;
            display: inline-block !important;
            flex-shrink: 0 !important;
        }

        @media (min-width: 640px) {
            .table-photo {
                width: 40px !important;
                height: 40px !important;
            }
        }

        /* Student Name Styling */
        .student-name {
            font-weight: 600 !important;
            color: #111827 !important;
        }

        /* ID Number Styling */
        .student-id {
            font-family: 'Courier New', monospace !important;
            font-weight: 600 !important;
            color: #4b5563 !important;
        }

        /* Cell Data Styling */
        .cell-data {
            font-weight: 400 !important;
            color: #4b5563 !important;
        }

        /* Action Buttons Container */
        .action-buttons {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            gap: 6px !important;
        }

        /* Action Buttons */
        .btn-action {
            width: 32px !important;
            height: 32px !important;
            border-radius: 4px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.2s ease !important;
            cursor: pointer !important;
            border: 1px solid #e5e7eb !important;
            background: #ffffff !important;
            color: #4b5563 !important;
        }

        .btn-action:hover {
            background: #f3f4f6 !important;
            border-color: #d1d5db !important;
        }

        /* Pagination */
        .pagination-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            background: #ffffff;
            border-top: 1px solid #e5e7eb;
        }

        .pagination-btn {
            padding: 6px 12px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #374151;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s ease;
            cursor: pointer;
            border-radius: 4px;
        }

        .pagination-btn:hover:not(:disabled) {
            background: #f9fafb;
            border-color: #d1d5db;
        }

        .pagination-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .pagination-btn.active {
            background: #374151;
            border-color: #374151;
            color: #fff;
        }

        /* Responsive Design */
        @media (max-width: 1023px) {
            .table-responsive table {
                min-width: 1200px !important;
            }
        }

        @media (max-width: 768px) {
            .table-card {
                border-radius: 0px;
            }

            th {
                padding: 10px 12px !important;
                font-size: 11px !important;
            }

            td {
                padding: 10px 12px !important;
                font-size: 12px !important;
            }

            .student-photo {
                width: 32px !important;
                height: 32px !important;
            }

            .student-name {
                font-size: 12px !important;
            }

            .student-id {
                font-size: 11px !important;
            }

            .btn-action {
                width: 28px !important;
                height: 28px !important;
            }

            .btn-action i {
                font-size: 13px !important;
            }

            .pagination-container {
                flex-direction: column;
                gap: 0.75rem;
                padding: 0.75rem;
            }

            .pagination-btn {
                padding: 5px 10px;
                font-size: 12px;
            }

            /* Force circular photos on mobile */
            td img {
                border-radius: 50% !important;
                width: 32px !important;
                height: 32px !important;
                object-fit: cover !important;
            }
        }

        @media (max-width: 480px) {
            th, td {
                padding: 8px 10px !important;
            }

            .student-name {
                font-size: 11px !important;
            }

            .action-buttons {
                gap: 4px !important;
            }

            .btn-action {
                width: 26px !important;
                height: 26px !important;
            }

            .btn-action i {
                font-size: 12px !important;
            }
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #6b7280;
        }

        .empty-state i {
            font-size: 3rem;
            color: #d1d5db;
            margin-bottom: 1rem;
        }

        .empty-state p {
            font-size: 14px;
            color: #9ca3af;
        }

        .btn-outline-premium {
            background: transparent;
            border: 1.5px solid #2563eb;
            color: #2563eb;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            position: relative;
            overflow: hidden;
        }

        .btn-outline-premium::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(37, 99, 235, 0.1);
            opacity: 0;
            transition: opacity 0.3s ease;
            border-radius: 0;
        }

        .btn-outline-premium:hover {
            background: #2563eb;
            color: #fff;
            box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }

        .btn-outline-premium:hover::before {
            opacity: 1;
        }

        .btn-outline-premium:active {
            transform: translateY(0) scale(0.98);
        }

        .btn-outline-secondary {
            background: transparent;
            border: 1.5px solid #64748b;
            color: #64748b;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            position: relative;
            overflow: hidden;
        }

        .btn-outline-secondary::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(100, 116, 139, 0.1);
            opacity: 0;
            transition: opacity 0.3s ease;
            border-radius: 0;
        }

        .btn-outline-secondary:hover {
            background: #64748b;
            color: #fff;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }

        .btn-outline-secondary:hover::before {
            opacity: 1;
        }

        .btn-outline-secondary:active {
            transform: translateY(0) scale(0.98);
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
            padding: 5px 12px;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            transition: all .2s;
            cursor: pointer;
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

        .edit-field-disabled {
            background: #f9fafb !important;
            color: #6b7280 !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
            user-select: none !important;
        }

        .action-icon-btn {
            font-size: 1.25rem;
            padding: 0px !important;
            transition: all .2s;
            background: none;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
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

        /* Status Badges */
        .badge-pending {
            background: #fef3c7;
            color: #92400e;
            padding: 2px 8px;
            font-weight: normal;
            text-transform: capitalize;
            font-size: 9px;
        }

        .badge-approved {
            background: #dcfce7;
            color: #166534;
            padding: 2px 8px;
            font-weight: normal;
            text-transform: capitalize;
            font-size: 9px;
        }

        .badge-rejected {
            background: #fee2e2;
            color: #991b1b;
            padding: 2px 8px;
            font-weight: normal;
            text-transform: capitalize;
            font-size: 9px;
        }

        /* Added on 2026-07-09: Active and Inactive flat badge styles */
        .badge-active {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            padding: 2px 8px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.05em;
        }

        .badge-inactive {
            background: #fdf2f2;
            color: #9b1c1c;
            border: 1px solid #fecdca;
            padding: 2px 8px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.05em;
        }
@media print {
    body * {
        visibility: hidden !important;
    }
    #admissionFormModal,
    #admissionFormModal *,
    #printArea,
    #printArea * {
        visibility: visible !important;
    }
    #admissionFormModal {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        height: auto !important;
        display: block !important;
        padding: 0 !important;
        margin: 0 !important;
        background: transparent !important;
        z-index: auto !important;
    }
    #admissionFormModal .no-print-modal-container {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        height: auto !important;
        max-width: 100% !important;
        max-height: none !important;
        display: block !important;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        box-shadow: none !important;
        overflow: visible !important;
    }
    #printArea {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        height: auto !important;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        background: white !important;
        color: black !important;
        overflow: visible !important;
    }
    .no-print {
        display: none !important;
    }
}

    </style>

    <div class="main-view-container">
        <div class="max-w-full mx-auto w-full">
            <div class="bg-white border border-gray-200 p-2.5 sm:p-4 mb-2" style="border-radius: 0;">
                <div class="mb-4 flex items-start justify-between">
                    <div>
                        <h2 id="pageHeader" class="text-[15px] sm:text-xl text-gray-800 font-normal leading-tight"></h2>
                        <div class="flex items-center text-slate-400 text-[12px] mt-1">
                            <span>School</span>
                            <i class="fas fa-chevron-right mx-1.5 text-[10px]"></i>
                            <span id="pageTitle" class="text-slate-500"></span>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                    <div class="hidden lg:flex items-center gap-2">
                        <x-input.search
                            id="studentSearch"
                            placeholder="Search students..."
                            class="hidden w-full lg:block lg:w-72"
                        />
                        <x-button.secondary id="btnRestoreDesktop" onclick="document.getElementById('studentSearch').value = ''; document.getElementById('studentSearch').dispatchEvent(new Event('input'));">
                            Restore
                        </x-button.secondary>
                    </div>
                    <div class="grid w-full grid-cols-3 gap-2 lg:flex lg:w-auto">
                        <x-button.secondary id="btnFilter">
                            Filter
                        </x-button.secondary>
                        <x-button.secondary id="btnExport">
                            Export
                        </x-button.secondary>
                        <div class="relative inline-block text-left flex-1 lg:flex-none">
                            <x-button.primary id="addStudentDropdownBtn" type="button" class="flex items-center justify-center gap-1 sm:gap-1.5 w-full !px-2 sm:!px-4">
                                Student <i class="fa-solid fa-chevron-down text-[10px]"></i>
                            </x-button.primary>
                            <div id="addStudentDropdownMenu" class="origin-top-right absolute right-0 mt-1 w-44 bg-white border border-slate-200 shadow-sm z-50 rounded-none hidden">
                                <div class="py-1">
                                    <a href="{{ route('school.student-admission') }}" class="block px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition-colors" style="color: #334155 !important;">Admission</a>
                                    <a href="{{ route('school.student-bulk-upload') }}" class="block px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition-colors" style="color: #334155 !important;">Bulk Upload</a>
                                    <a href="{{ route('school.student-re-admission') }}" class="block px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition-colors" style="color: #334155 !important;">Re-Admission</a>
                                    <a href="{{ route('school.student-promote') }}" class="block px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition-colors" style="color: #334155 !important;">Promote</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-3 grid grid-cols-3 gap-2 lg:hidden">
                    <x-input.search
                        id="studentSearchMobile"
                        placeholder="Search students..."
                        class="col-span-2 min-w-0"
                    />
                    <x-button.secondary id="btnRestoreMobile" onclick="document.getElementById('studentSearchMobile').value = ''; document.getElementById('studentSearchMobile').dispatchEvent(new Event('input'));">
                        Restore
                    </x-button.secondary>
                </div>
            </div>

            {{-- Student Filter Modal --}}
            <x-modal.form
                id="filterModal"
                form-id="studentFilterForm"
                title="Student filter"
                close-button-id="closeFilterModal"
                :enctype="null"
                class="student-filter-modal"
                panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[288px] overflow-visible border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] md:max-w-[480px]"
            >
                <div class="relative">
                    <x-input.dropdown-select id="classFilter" name="class" placeholder="Select Class" add-button-id="add_filter_class_btn" add-button-label="Add Class" add-button-target="quickClassModal" />
                    <x-input.floating-label for="classFilter" :floating="false">Class</x-input.floating-label>
                </div>
                <div class="relative">
                    <x-input.dropdown-select id="groupFilter" name="group" placeholder="Select Group" add-button-id="add_filter_group_btn" add-button-label="Add Group" add-button-target="quickGroupModal" />
                    <x-input.floating-label for="groupFilter" :floating="false">Group</x-input.floating-label>
                </div>
                <div class="relative">
                    <x-input.dropdown-select id="sectionFilter" name="section" placeholder="Select Section" add-button-id="add_filter_section_btn" add-button-label="Add Section" add-button-target="quickSectionModal" />
                    <x-input.floating-label for="sectionFilter" :floating="false">Section</x-input.floating-label>
                </div>
                <div class="relative">
                    <x-input.dropdown-select id="sessionFilter" name="session" placeholder="Select Session" add-button-id="add_filter_session_btn" add-button-label="Add Session" add-button-target="quickSessionModal" />
                    <x-input.floating-label for="sessionFilter" :floating="false">Session</x-input.floating-label>
                </div>

                <x-slot:footer>
                    <div class="grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 py-3">
                        <x-button.secondary id="resetFilter" type="button" class="w-full">
                            Reset
                        </x-button.secondary>

                        <x-button.primary id="applyFilter" type="button" class="w-full">
                            Apply
                        </x-button.primary>
                    </div>
                </x-slot:footer>
            </x-modal.form>

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

            <!-- Modified on 2026-07-09: Removed Bulk Upload Modal HTML since bulk upload has been moved to its own page -->

            <div class="table-card">
                <div class="table-responsive custom-scrollbar">
                    <table>
                        <thead>
                            <tr>
                                <th class="text-left">Sl</th>
                                <th class="text-left">Photo</th>
                                <th class="text-left">Id Number</th>
                                <th class="text-left">Student Name</th>
                                <th class="text-left">Mobile Number</th>
                                <th class="text-left">Father Name</th>
                                <th class="text-left">Class</th>
                                <th class="text-left">Group</th>
                                <th class="text-left">Section</th>
                                <th class="text-left">Session</th>
                                <th class="text-left">Student Type</th>
                                <th class="text-left">Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="studentTableBody"></tbody>
                    </table>
                </div>
                <div class="pagination-container">
                    <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest" id="paginationInfo">
                        0 of 0
                    </div>
                    <div class="flex items-center gap-1" id="paginationControls"></div>
                </div>
            </div>
        </div>
    </div>



    @php
        $school = null;
        $user = Auth::user();
        if ($user) {
            if ($user->role === 'teacher') {
                $teacher = \App\Models\Teacher::where('id_number', $user->id_number)->first();
                if ($teacher) {
                    $school = \App\Models\School::find($teacher->school_id);
                }
            } else {
                $school = \App\Models\School::where('user_id', $user->id)->first();
            }
        }
    @endphp
    @include('school.partials.student-details-modal')
    @include('school.partials.student-view-modal')
    @include('school.partials.fee-template-modal')
    @include('school.partials.student-edit-modal')
    @include('school.partials.quick-add-modals')
    @include('school.partials.student-deactivate-modal')
    @include('school.partials.student-activate-modal')
    @include('school.partials.student-delete-modal')

    <script>
        const studentModal = document.getElementById('studentModal');
        const detailsModal = document.getElementById('detailsModal');
        const filterModal  = document.getElementById('filterModal');
        const exportModal  = document.getElementById('exportModal');
        const photoInput   = document.getElementById('photoInput');
        const imagePreview = document.getElementById('imagePreview');
        const detailsForm  = document.getElementById('detailsForm');
        let currentPage    = 1;

        // --- DYNAMIC DROPDOWN POPULATION HELPER ---
        function populateDropdownSelect(id, options, selectedValue, placeholder = 'Select...') {
            const input = document.getElementById(id);
            const button = document.getElementById(id + 'Button');
            const label = button ? button.querySelector('[data-dropdown-select-label]') : null;
            const menu = document.getElementById(id + 'Menu');

            if (!input || !menu) return;

            let menuHtml = '';
            let selectedText = placeholder;

            options.forEach(opt => {
                const isSelected = String(opt.value) === String(selectedValue);
                if (isSelected) {
                    selectedText = opt.label;
                }
                menuHtml += `
                    <button
                        type="button"
                        class="dropdown-select-option m-0 flex min-h-6 w-full items-center border-0 bg-white px-3 py-1 text-left text-[11px] font-normal leading-tight transition-colors hover:bg-slate-100 ${isSelected ? 'bg-slate-100 text-slate-900' : 'text-slate-800'}"
                        data-value="${opt.value}"
                        role="option"
                        aria-selected="${isSelected ? 'true' : 'false'}"
                        data-dropdown-select-option
                    >
                        ${opt.label}
                    </button>
                `;
            });

            menu.innerHTML = menuHtml;
            input.value = selectedValue || '';
            if (label) {
                label.textContent = selectedText;
            }

            menu.querySelectorAll('[data-dropdown-select-option]').forEach(option => {
                option.addEventListener('click', () => {
                    input.value = option.dataset.value || '';
                    if (label) {
                        label.textContent = option.textContent.trim();
                    }

                    menu.querySelectorAll('[data-dropdown-select-option]').forEach(item => {
                        const isSel = item === option;
                        item.classList.toggle('bg-slate-100', isSel);
                        item.classList.toggle('text-slate-900', isSel);
                        item.classList.toggle('text-slate-800', !isSel);
                        item.setAttribute('aria-selected', String(isSel));
                    });

                    // Close the dropdown menu
                    const root = input.closest('[data-dropdown-select]');
                    if (root) {
                        root.classList.remove('is-open');
                    }
                    menu.classList.add('hidden');
                    if (button) {
                        button.setAttribute('aria-expanded', 'false');
                        const icon = button.querySelector('i');
                        if (icon) {
                            icon.classList.remove('rotate-180');
                        }
                    }

                    input.dispatchEvent(new Event('change', { bubbles: true }));
                });
            });
        }

        const geoApi = axios.create({
            baseURL: 'https://bdapis.com/api/v1.2'
        });

        function setSelectedValue(id, value) {
            const input = document.getElementById(id);
            if (!input) return;
            input.value = value;
            const button = document.getElementById(id + 'Button');
            const label = button ? button.querySelector('[data-dropdown-select-label]') : null;
            const menu = document.getElementById(id + 'Menu');
            if (menu) {
                let foundLabel = '';
                menu.querySelectorAll('[data-dropdown-select-option]').forEach(item => {
                    const isSel = String(item.dataset.value) === String(value);
                    item.classList.toggle('bg-slate-100', isSel);
                    item.classList.toggle('text-slate-900', isSel);
                    item.classList.toggle('text-slate-800', !isSel);
                    item.setAttribute('aria-selected', String(isSel));
                    if (isSel) {
                        foundLabel = item.textContent.trim();
                    }
                });
                if (label) {
                    const placeholderEl = button.querySelector('[data-placeholder]');
                    const placeholderText = placeholderEl ? placeholderEl.dataset.placeholder : 'Select...';
                    label.textContent = foundLabel || placeholderText;
                }
            }
            input.dispatchEvent(new Event('change', { bubbles: true }));
        }

        function populateDropdown(elemId, data, valKey, labelKey, defaultText, selectedValue = '') {
            const options = data.map(item => ({ value: item[valKey], label: item[labelKey] }));
            populateDropdownSelect(elemId, options, selectedValue, defaultText);
        }

        async function loadLocationsModal() {
            try {
                const res = await geoApi.get('/divisions');
                const divs = res.data.data || [];
                const options = divs.map(d => ({ value: d.division, label: d.division }));
                populateDropdownSelect('edit_current_division', options, '', 'Select Division');
                populateDropdownSelect('edit_permanent_division', options, '', 'Select Division');
            } catch (err) {
                console.error(err);
            }
        }

        async function populateDistrictModal(divisionValue, distId) {
            populateDropdownSelect(distId, [], '', 'Select District');
            if (!divisionValue) return;
            try {
                const res = await geoApi.get(`/division/${divisionValue}`);
                const options = (res.data.data || []).map(d => ({ value: d.district, label: d.district }));
                populateDropdownSelect(distId, options, '', 'Select District');
            } catch (err) {
                console.error(err);
            }
        }

        async function populateUpazilaModal(districtValue, upaId) {
            populateDropdownSelect(upaId, [], '', 'Select Upazila');
            if (!districtValue) return;
            try {
                const res = await geoApi.get(`/district/${districtValue}`);
                const upazillas = (res.data.data && res.data.data[0]) ? (res.data.data[0].upazillas || []) : [];
                const options = upazillas.map(u => ({ value: u, label: u }));
                populateDropdownSelect(upaId, options, '', 'Select Upazila');
            } catch (err) {
                console.error(err);
            }
        }

        // --- PHOTO PREVIEW LOGIC ---
        photoInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => imagePreview.innerHTML =
                    `<img src="${e.target.result}" class="w-full h-full object-cover" />`;
                reader.readAsDataURL(file);
            } else {
                imagePreview.innerHTML = `<i class="mdi mdi-camera text-gray-300"></i>`;
            }
        });

        // --- CORE FETCH FUNCTION ---
        function fetchStudents(page = 1) {
            currentPage = page;
            const search = document.getElementById('studentSearch').value;

            // Comprehensive Filters
            const classVal = document.getElementById('classFilter').value;
            const groupVal = document.getElementById('groupFilter').value;
            const sectionVal = document.getElementById('sectionFilter').value;
            const sessionVal = document.getElementById('sessionFilter').value;

            axios.get('{{ url('/api/school/students') }}', {
                    params: {
                        search,
                        class: classVal,
                        group: groupVal,
                        section: sectionVal,
                        session: sessionVal,
                        page
                    }
                })
                .then(res => {
                    const students = res.data.data || [];
                    const meta = res.data;
                    const tbody = document.getElementById('studentTableBody');
                    tbody.innerHTML = '';

                    students.forEach((s, index) => {
                        const sl = (meta.current_page - 1) * meta.per_page + (index + 1);
                        const photoUrl = s.image ? `/storage/${s.image}` :
                            `https://ui-avatars.com/api/?background=random&name=${s.student_name}`;
                        let statusText = s.status || 'Active';
                        if (statusText === 'Inactive') statusText = 'Unactive';
                        if (statusText === 'pending') statusText = 'Pending';
                        let badgeClass = 'badge-active';
                        if (statusText === 'Unactive') badgeClass = 'badge-inactive';
                        if (statusText === 'Pending') badgeClass = 'badge-pending';
                        tbody.innerHTML += `
                <tr>
                    <td>${sl}</td>
                    <td>
                        <img src="${photoUrl}" class="table-photo" style="border-radius: 50% !important; width: 24px !important; height: 24px !important; object-fit: cover !important;" />
                    </td>
                    <td><span class="student-id">${s.student_id_number}</span></td>
                    <td><span class="student-name">${s.student_name}</span></td>
                    <td><span class="cell-data">${s.mobile || '-'}</span></td>
                    <td><span class="student-father-name">${s.father_name || '-'}</span></td>
                    <td><span class="cell-data">${s.class_name || '-'}</span></td>
                    <td><span class="cell-data">${s.group_name || '-'}</span></td>
                    <td><span class="cell-data">${s.section_name || '-'}</span></td>
                    <td><span class="cell-data">${s.session_year || '-'}</span></td>
                    <td><span class="cell-data">${s.student_type || 'Admission'}</span></td>
                    <td>
                        <span class="${badgeClass}">
                            ${statusText}
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons flex justify-center gap-2">
                            <button onclick="viewAdmissionForm(${s.id}, ${sl})" title="View" class="btn-action view text-emerald-500">
                                <i class="far fa-eye" style="font-size: 15px;"></i>
                            </button>
                            <button onclick="editStudent(${s.id})" title="Edit" class="btn-action edit text-blue-500">
                                <i class="far fa-edit" style="font-size: 15px;"></i>
                            </button>
                            <button onclick="deleteStudent(${s.id}, \`${s.student_name.replace(/`/g, '\\`').replace(/"/g, '\\"')}\`)" title="Delete" class="btn-action delete text-red-400">
                                <i class="far fa-trash-alt" style="font-size: 15px;"></i>
                            </button>
                            <button onclick="toggleStudentStatus(${s.id}, '${s.status}', '${s.student_name}', '${s.student_id_number}', '${s.inactive_date || ''}')"
                                    title="${s.status === 'Inactive' ? 'Activate Student' : 'Deactivate Student'}"
                                    class="btn-action status">
                                <i class="fas fa-circle" style="font-size: 15px; color: ${s.status === 'Inactive' ? '#ef4444' : '#10b981'} !important;"></i>
                            </button>
                        </div>
                    </td>
                </tr>`;
                    });

                    renderPagination(meta);
                }).catch(e => console.error("Load failed", e));
        }

        // --- DEPENDENT DROPDOWN LOGIC ---
        async function loadFilterOptions() {
            try {
                const res = await axios.get('{{ url('/api/get-school-classes') }}');
                const classesOptions = res.data.data.map(c => ({ value: c.id, label: c.class_name }));
                populateDropdownSelect('classFilter', classesOptions, '', 'All Classes');
                populateDropdownSelect('groupFilter', [], '', 'All Groups');
                populateDropdownSelect('sectionFilter', [], '', 'All Sections');
                populateDropdownSelect('sessionFilter', [], '', 'All Sessions');
            } catch (e) {
                console.error("Class load failed", e);
            }
        }

        document.getElementById('classFilter')?.addEventListener('change', async function() {
            const classId = this.value;
            populateDropdownSelect('groupFilter', [], '', 'All Groups');
            populateDropdownSelect('sectionFilter', [], '', 'All Sections');
            populateDropdownSelect('sessionFilter', [], '', 'All Sessions');

            if (!classId) return;

            try {
                const [gRes, sRes] = await Promise.all([
                    axios.get('{{ url('/api/get-school-groups') }}', { params: { class_id: classId } }),
                    axios.get('{{ url('/api/get-school-sessions') }}', { params: { class_id: classId } })
                ]);
                const groupsOptions = gRes.data.data.map(g => ({ value: g.id, label: g.group_name }));
                populateDropdownSelect('groupFilter', groupsOptions, '', 'All Groups');

                const sessionsOptions = sRes.data.data.map(sess => ({ value: sess.id, label: sess.session_year }));
                populateDropdownSelect('sessionFilter', sessionsOptions, '', 'All Sessions');
            } catch (e) {
                console.error(e);
            }
        });

        document.getElementById('groupFilter')?.addEventListener('change', async function() {
            const groupId = this.value;
            populateDropdownSelect('sectionFilter', [], '', 'All Sections');

            if (!groupId) return;

            try {
                const res = await axios.get('{{ url('/api/get-school-sections') }}', { params: { group_id: groupId } });
                const sectionsOptions = res.data.data.map(sec => ({ value: sec.id, label: sec.section_name }));
                populateDropdownSelect('sectionFilter', sectionsOptions, '', 'All Sections');
            } catch (e) {
                console.error(e);
            }
        });

        // --- ACTIONS ---
        async function editStudent(id) {
            // Show modal immediately — don't wait for API
            studentModal.classList.remove('hidden');

            try {
                // Round 1 (parallel): student details + class list
                const [detailsRes, classRes] = await Promise.all([
                    axios.get('{{ url('/api/school/students/details') }}/' + id),
                    axios.get('{{ url('/api/get-school-classes') }}')
                ]);

                const s = detailsRes.data.student;
                const g = detailsRes.data.guardian;
                document.getElementById('student_id').value = s.id;

                const setVal = (name, value) => {
                    const el = document.querySelector(`#studentForm [name="${name}"]`);
                    if (el) el.value = value ?? '';
                };

                setVal('school', s.school);
                setVal('student_name', s.student_name);
                setVal('father_name', s.father_name);
                setVal('mother_name', s.mother_name);
                setVal('mobile', s.mobile);
                setVal('admission_fee', s.admission_fee);
                setVal('admission_date', s.admission_date);

                // Guardian Info
                setVal('g_name', g ? g.name : '');
                setVal('g_relation', g ? g.relation : '');
                setVal('g_mobile', g ? g.mobile : '');

                // Addresses Current
                setSelectedValue('edit_current_country', s.current_country || 'Bangladesh');
                await loadLocationsModal();
                if (s.current_division) {
                    setSelectedValue('edit_current_division', s.current_division);
                    await populateDistrictModal(s.current_division, 'edit_current_district');
                    if (s.current_district) {
                        setSelectedValue('edit_current_district', s.current_district);
                        await populateUpazilaModal(s.current_district, 'edit_current_upazila');
                        if (s.current_upazila) {
                            setSelectedValue('edit_current_upazila', s.current_upazila);
                        }
                    }
                }
                setVal('current_village', s.current_village);

                // Addresses Permanent
                setSelectedValue('edit_permanent_country', s.permanent_country || 'Bangladesh');
                if (s.permanent_division) {
                    setSelectedValue('edit_permanent_division', s.permanent_division);
                    await populateDistrictModal(s.permanent_division, 'edit_permanent_district');
                    if (s.permanent_district) {
                        setSelectedValue('edit_permanent_district', s.permanent_district);
                        await populateUpazilaModal(s.permanent_district, 'edit_permanent_upazila');
                        if (s.permanent_upazila) {
                            setSelectedValue('edit_permanent_upazila', s.permanent_upazila);
                        }
                    }
                }
                setVal('permanent_village', s.permanent_village);

                document.getElementById('sameAsCurrentAddressModal').checked = false;
                toggleSameAddressModal();

                const photoUrl = s.image ? `/storage/${s.image}` :
                    `https://ui-avatars.com/api/?background=random&name=${encodeURIComponent(s.student_name)}`;
                imagePreview.innerHTML = `<img src="${photoUrl}" class="w-full h-full object-cover" />`;

                // Populate class select
                const classesOptions = classRes.data.data.map(c => ({ value: c.id, label: c.class_name }));
                populateDropdownSelect('edit_class', classesOptions, s.class, 'Select Class');

                // Round 2 (parallel): group + session
                const round2 = [];
                if (s.class) {
                    round2.push(
                        axios.get('{{ url('/api/get-school-groups') }}',   { params: { class_id: s.class } }).then(r => ({ k: 'groups',   d: r.data.data })),
                        axios.get('{{ url('/api/get-school-sessions') }}', { params: { class_id: s.class } }).then(r => ({ k: 'sessions', d: r.data.data }))
                    );
                }

                const r2 = await Promise.all(round2);
                r2.forEach(({ k, d }) => {
                    if (k === 'groups') {
                        const groupsOptions = d.map(g => ({ value: g.id, label: g.group_name }));
                        populateDropdownSelect('edit_group', groupsOptions, s.group, 'Select Group');
                    } else if (k === 'sessions') {
                        const sessionsOptions = d.map(sess => ({ value: sess.id, label: sess.session_year }));
                        populateDropdownSelect('edit_session', sessionsOptions, s.session, 'Select Session');
                    }
                });

                // Round 3 (parallel): sections
                const round3 = [];
                if (s.group) {
                    round3.push(axios.get('{{ url('/api/get-school-sections') }}', { params: { group_id: s.group } }).then(r => ({ k: 'sections', d: r.data.data })));
                }

                const r3 = await Promise.all(round3);
                r3.forEach(({ k, d }) => {
                    if (k === 'sections') {
                        const sectionsOptions = d.map(sec => ({ value: sec.id, label: sec.section_name }));
                        populateDropdownSelect('edit_section', sectionsOptions, s.section, 'Select Section');
                    }
                });

            } catch (err) {
                console.error("Edit failed", err);
            }
        }

        document.getElementById('edit_class')?.addEventListener('change', async function() {
            const classId = this.value;
            populateDropdownSelect('edit_group', [], '', 'Select Group');
            populateDropdownSelect('edit_section', [], '', 'Select Section');
            populateDropdownSelect('edit_session', [], '', 'Select Session');
            if (!classId) {
                loadEditFees();
                return;
            }
            const [gRes, sessRes] = await Promise.all([
                axios.get('{{ url('/api/get-school-groups') }}', { params: { class_id: classId } }),
                axios.get('{{ url('/api/get-school-sessions') }}', { params: { class_id: classId } })
            ]);
            const groupsOptions = gRes.data.data.map(g => ({ value: g.id, label: g.group_name }));
            populateDropdownSelect('edit_group', groupsOptions, '', 'Select Group');

            const sessionsOptions = sessRes.data.data.map(s => ({ value: s.id, label: s.session_year }));
            populateDropdownSelect('edit_session', sessionsOptions, '', 'Select Session');
            loadEditFees();
        });
        document.getElementById('edit_group')?.addEventListener('change', async function() {
            const groupId = this.value;
            populateDropdownSelect('edit_section', [], '', 'Select Section');
            if (!groupId) return;
            const res = await axios.get('{{ url('/api/get-school-sections') }}', { params: { group_id: groupId } });
            const sectionsOptions = res.data.data.map(sec => ({ value: sec.id, label: sec.section_name }));
            populateDropdownSelect('edit_section', sectionsOptions, '', 'Select Section');
        });
        async function loadEditFees() {
            const classId = document.getElementById('edit_class').value;
            const sessionId = document.getElementById('edit_session').value;
            const feeInput = document.querySelector('#studentForm [name="admission_fee"]');
            if (!classId || !sessionId) {
                feeInput.value = '';
                return;
            }
            feeInput.value = 'Loading...';
            try {
                const res = await axios.get('{{ url('/api/fee-templates') }}', {
                    params: {
                        class_id: classId,
                        session_id: sessionId,
                        search: 'Admission',
                        all: 1
                    }
                });
                const fees = res.data.data;
                const admissionFee = fees && fees.length > 0 ? fees[0] : null;
                feeInput.value = admissionFee ? admissionFee.amount : 'No fee defined';
            } catch (e) {
                console.error("Fee Load Error:", e);
                feeInput.value = 'Error';
            }
        }
        document.getElementById('edit_session')?.addEventListener('change', loadEditFees);
        document.getElementById('btnCreateFeeTemplateEdit')?.addEventListener('click', () => {
            const classId = document.getElementById('edit_class').value;
            const sessionId = document.getElementById('edit_session').value;
            if (!classId || !sessionId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Selection Required',
                    text: 'Please select both Class and Session first.',
                    confirmButtonColor: '#2563eb'
                });
                return;
            }
            document.getElementById('feeTemplateModal').classList.remove('hidden');
            const payDateInput = document.getElementById('feePayDateInput');
            if (payDateInput && !payDateInput.value) {
                payDateInput.value = new Date().toISOString().split('T')[0];
            }
        });
        document.getElementById('feeTemplateForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const classId = document.getElementById('edit_class').value;
            const sessionId = document.getElementById('edit_session').value;
            const groupId = document.getElementById('edit_group').value;
            const sectionId = document.getElementById('edit_section').value;
            const feeName = document.getElementById('feeNameInput').value;
            const amount = document.getElementById('feeAmountInput').value;
            const payDate = document.getElementById('feePayDateInput').value;
            Swal.fire({
                title: 'Saving Fee Template...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            axios.post('{{ url('/api/fee-templates') }}', {
                    class_id: classId,
                    session_id: sessionId,
                    group_id: groupId,
                    section_id: sectionId,
                    fee_type_name: 'Admission',
                    fee_name: feeName,
                    amount: amount,
                    pay_date: payDate
                }, {
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                })
                .then(() => {
                    Swal.close();
                    document.getElementById('feeTemplateModal').classList.add('hidden');
                    document.getElementById('feeTemplateForm').reset();
                    loadEditFees();
                })
                .catch(err => {
                    Swal.close();
                    let errorMsg = 'Failed to create fee template.';
                    if (err.response && err.response.data.message) {
                        errorMsg = err.response.data.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMsg
                    });
                });
        });

        // --- GEO FUNCTIONS FOR EDIT MODAL ---
        async function syncPermanentFromCurrentModal() {
            if (!document.getElementById('sameAsCurrentAddressModal').checked) return;
            setSelectedValue('edit_permanent_country', document.getElementById('edit_current_country').value);
            setSelectedValue('edit_permanent_division', document.getElementById('edit_current_division').value);
            setSelectedValue('edit_permanent_district', document.getElementById('edit_current_district').value);
            setSelectedValue('edit_permanent_upazila', document.getElementById('edit_current_upazila').value);
            document.getElementById('edit_permanent_village').value = document.getElementById('edit_current_village').value;
        }

        function toggleSameAddressModal() {
            const checked = document.getElementById('sameAsCurrentAddressModal').checked;
            ['edit_permanent_country', 'edit_permanent_division', 'edit_permanent_district', 'edit_permanent_upazila', 'edit_permanent_village'].forEach(id => {
                if (id === 'edit_permanent_village') {
                    const el = document.getElementById(id);
                    if (el) {
                        el.readOnly = checked;
                        el.style.background = checked ? '#f9fafb' : '';
                        el.style.color = checked ? '#6b7280' : '';
                        el.style.cursor = checked ? 'not-allowed' : '';
                    }
                } else {
                    const button = document.getElementById(`${id}Button`);
                    if (button) {
                        button.disabled = checked;
                        button.classList.toggle('bg-slate-50', checked);
                        button.classList.toggle('text-slate-500', checked);
                        button.classList.toggle('cursor-not-allowed', checked);
                    }
                }
            });
            if (checked) syncPermanentFromCurrentModal();
        }

        document.getElementById('edit_current_country')?.addEventListener('change', syncPermanentFromCurrentModal);
        document.getElementById('edit_current_division')?.addEventListener('change', async (e) => {
            await populateDistrictModal(e.target.value, 'edit_current_district');
            populateDropdownSelect('edit_current_upazila', [], '', 'Select Upazila');
            syncPermanentFromCurrentModal();
        });
        document.getElementById('edit_current_district')?.addEventListener('change', async (e) => {
            await populateUpazilaModal(e.target.value, 'edit_current_upazila');
            syncPermanentFromCurrentModal();
        });
        document.getElementById('edit_current_upazila')?.addEventListener('change', syncPermanentFromCurrentModal);
        document.getElementById('edit_current_village')?.addEventListener('input', syncPermanentFromCurrentModal);

        document.getElementById('edit_permanent_division')?.addEventListener('change', async (e) => {
            await populateDistrictModal(e.target.value, 'edit_permanent_district');
            populateDropdownSelect('edit_permanent_upazila', [], '', 'Select Upazila');
        });
        document.getElementById('edit_permanent_district')?.addEventListener('change', async (e) => {
            await populateUpazilaModal(e.target.value, 'edit_permanent_upazila');
        });


        function viewDetails(id) {
            axios.get('{{ url('/api/school/students/details') }}/' + id).then(res => {
                const s = res.data.student;
                const g = res.data.guardian;
                const form = document.getElementById('detailsForm');
                form.det_student_id.value = s.id;
                form.student_id_number.value = s.student_id_number || '';
                form.class.value = s.class || '';
                form.section.value = s.section || '';
                form.group.value = s.group || '';
                form.session.value = s.session || '';
                form.previous_school.value = s.previous_school || '';
                form.last_exam_result.value = s.last_exam_result || '';
                form.student_name.value = s.student_name || '';
                form.father_name.value = s.father_name || '';
                form.mother_name.value = s.mother_name || '';
                form.mobile.value = s.mobile || '';
                form.g_name.value = g ? (g.name || '') : '';
                form.g_mobile.value = g ? (g.mobile || '') : '';
                form.current_village.value = s.current_village || '';
                form.current_division.value = s.current_division || '';
                form.current_district.value = s.current_district || '';
                form.current_upazila.value = s.current_upazila || '';
                form.permanent_village.value = s.permanent_village || '';
                form.permanent_division.value = s.permanent_division || '';
                form.permanent_district.value = s.permanent_district || '';
                form.permanent_upazila.value = s.permanent_upazila || '';
                form.status.value = s.status || 'pending';
                document.getElementById('detailsModal').classList.remove('hidden');
            }).catch(err => console.error("Details fetch failed", err));
        }
        function viewAdmissionForm(id, sl) {
            axios.get('{{ url('/api/school/students/details') }}/' + id).then(res => {
                const s = res.data.student;
                const g = res.data.guardian;
                let dateStr = '--';
                if (s.admission_date) {
                    const parts = s.admission_date.split('-');
                    if (parts.length === 3) {
                        const year = parts[0].slice(-2);
                        const monthNum = parseInt(parts[1], 10);
                        const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                        const monthName = (monthNum >= 1 && monthNum <= 12) ? monthNames[monthNum - 1] : parts[1];
                        const day = parts[2];
                        dateStr = `${day}-${monthName}-${year}`;
                    }
                }
                const dateBox = document.getElementById('print_date_box');
                if (dateBox) {
                    dateBox.textContent = dateStr;
                }
                const photoEl = document.getElementById('print_student_photo');
                const placeholderEl = document.getElementById('print_photo_placeholder');
                if (s.image) {
                    photoEl.src = '/storage/' + s.image;
                    photoEl.classList.remove('hidden');
                    placeholderEl.classList.add('hidden');
                } else {
                    photoEl.src = '';
                    photoEl.classList.add('hidden');
                    placeholderEl.classList.remove('hidden');
                }
                document.getElementById('print_student_id_number').textContent = s.student_id_number || '-';
                document.getElementById('print_admission_id').textContent = sl !== undefined ? sl : (s.admission_id || '-');
                const rollNoEl = document.getElementById('print_roll_no');
                if (rollNoEl) {
                    rollNoEl.textContent = s.roll_no || '-';
                }
                document.getElementById('print_student_name').textContent = s.student_name || '-';
                const idRefEl = document.getElementById('print_id_number_ref');
                if (idRefEl) {
                    idRefEl.textContent = s.student_id_number || '-';
                }
                document.getElementById('print_father_name').textContent = s.father_name || '-';
                document.getElementById('print_mother_name').textContent = s.mother_name || '-';
                document.getElementById('print_mobile').textContent = s.mobile || '-';
                document.getElementById('print_g_name').textContent = g ? (g.name || '-') : (s.father_name || '-');
                document.getElementById('print_g_relation').textContent = g ? (g.relation || 'পিতা') : 'পিতা';
                document.getElementById('print_g_mobile').textContent = g ? (g.mobile || '-') : (s.mobile || '-');
                document.getElementById('print_class').textContent = s.class_name || s.class || '-';
                document.getElementById('print_group').textContent = s.group_name || s.group || '-';
                document.getElementById('print_section').textContent = s.section_name || s.section || '-';
                document.getElementById('print_session').textContent = s.session_year || s.session || '-';
                document.getElementById('print_student_type').textContent = s.student_type || 'Admission';
                document.getElementById('print_curr_country').textContent = s.current_country || 'Bangladesh';
                document.getElementById('print_curr_division').textContent = s.current_division || '-';
                document.getElementById('print_curr_district').textContent = s.current_district || '-';
                document.getElementById('print_curr_upazila').textContent = s.current_upazila || '-';
                document.getElementById('print_curr_village').textContent = s.current_village || '-';
                document.getElementById('print_perm_country').textContent = s.permanent_country || 'Bangladesh';
                document.getElementById('print_perm_division').textContent = s.permanent_division || '-';
                document.getElementById('print_perm_district').textContent = s.permanent_district || '-';
                document.getElementById('print_perm_upazila').textContent = s.permanent_upazila || '-';
                document.getElementById('print_perm_village').textContent = s.permanent_village || '-';
                document.getElementById('admissionFormModal').classList.remove('hidden');
            }).catch(err => console.error("Admission form fetch failed", err));
        }
        function printAdmissionForm() {
            window.print();
        }

        // Modified: Status toggling function using Blade component modals
        function toggleStudentStatus(id, currentStatus, studentName, studentIdNumber, inactiveSince) {
            const today = new Date().toISOString().split('T')[0];
            if (currentStatus === 'Inactive') {
                document.getElementById('activate_student_id').value = id;
                document.getElementById('activate_student_name').textContent = studentName;
                document.getElementById('activate_student_id_number').textContent = studentIdNumber;
                document.getElementById('activate_inactive_since').textContent = inactiveSince || 'N/A';
                document.getElementById('activateStudentModal').classList.remove('hidden');
            } else {
                document.getElementById('deactivate_student_id').value = id;
                document.getElementById('deactivate_student_name').textContent = studentName;
                document.getElementById('deactivate_student_id_number').textContent = studentIdNumber;
                document.getElementById('deactivate_inactive_date').value = today;
                document.getElementById('deactivate_inactive_reason').value = '';
                document.getElementById('deactivateStudentModal').classList.remove('hidden');
            }
        }

        // Deactivate Student Form Submit
        document.getElementById('deactivateStudentForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('deactivate_student_id').value;
            const inactiveDate = document.getElementById('deactivate_inactive_date').value;
            const inactiveReason = document.getElementById('deactivate_inactive_reason').value;

            axios.post(`{{ url('/api/school/students/status') }}/${id}`, {
                status: 'Inactive',
                inactive_date: inactiveDate,
                inactive_reason: inactiveReason
            }, {
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(res => {
                Toastify({
                    text: "Student Inactivated Successfully!",
                    style: { background: "#ef4444" }
                }).showToast();
                document.getElementById('deactivateStudentModal').classList.add('hidden');
                fetchStudents(currentPage);
            }).catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to deactivate student.' });
            });
        });

        // Activate Student Form Submit
        document.getElementById('activateStudentForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('activate_student_id').value;
            const today = new Date().toISOString().split('T')[0];

            axios.post(`{{ url('/api/school/students/status') }}/${id}`, {
                status: 'Active',
                active_date: today
            }, {
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(res => {
                Toastify({
                    text: "Student Activated Successfully!",
                    style: { background: "#10b981" }
                }).showToast();
                document.getElementById('activateStudentModal').classList.add('hidden');
                fetchStudents(currentPage);
            }).catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to activate student.' });
            });
        });

        function updateFullDetails() {
            const id = document.getElementById('det_student_id').value;
            const formData = new FormData(detailsForm);
            const data = Object.fromEntries(formData.entries());
            axios.post(`{{ url('/api/school/students/update-details') }}/${id}`, data, {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(res => {
                Toastify({
                    text: "Profile Updated Successfully!",
                    style: {
                        background: "#10b981"
                    }
                }).showToast();
                closeDetailsModal();
                fetchStudents(currentPage);
            }).catch(err => alert("Update failed."));
        }

        document.getElementById('studentForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const sid = document.getElementById('student_id').value;
            const formData = new FormData(this);

            // Clear all previous error borders
            document.querySelectorAll('#studentForm input, #studentForm select').forEach(el => {
                el.style.removeProperty('border-color');
            });

            axios.post(`{{ url('/api/school/students') }}/${sid}`, formData, {
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(() => {
                Toastify({
                    text: "Updated Successfully!",
                    style: { background: "#10b981" }
                }).showToast();
                studentModal.classList.add('hidden');
                fetchStudents(currentPage);
            }).catch(err => {
                if (err.response && err.response.status === 422) {
                    const errors = err.response.data.errors;
                    Object.keys(errors).forEach(field => {
                        const el = document.querySelector(`#studentForm [name="${field}"]`);
                        if (el) el.style.setProperty('border-color', '#dc2626', 'important');
                    });
                    Swal.fire({
                        icon: 'warning',
                        title: 'Required',
                        text: 'Please fill all required fields.',
                        toast: true,
                        position: 'top-end',
                        timer: 3000,
                        showConfirmButton: false
                    });
                }
            });
        });

        function deleteStudent(id, studentName) {
            document.getElementById('delete_student_id').value = id;
            document.getElementById('delete_student_name').textContent = studentName || '';
            document.getElementById('deleteStudentModal').classList.remove('hidden');
        }

        // Delete Student Form Submit
        document.getElementById('deleteStudentForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('delete_student_id').value;

            axios.delete('{{ url('/api/school/students') }}/' + id, {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => {
                Toastify({
                    text: "Deleted successfully",
                    style: {
                        background: "#ef4444"
                    }
                }).showToast();
                document.getElementById('deleteStudentModal').classList.add('hidden');
                fetchStudents(currentPage);
            }).catch(err => {
                Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to delete student.' });
            });
        });

        // --- PAGINATION ---
        function renderPagination(meta) {
            const controls = document.getElementById('paginationControls');
            const info = document.getElementById('paginationInfo');
            if (!info || !controls) return;
            info.innerText = `${meta.to || 0} of ${meta.total}`;
            controls.innerHTML = '';

            const prevBtn = document.createElement('button');
            prevBtn.className = 'pagination-btn';
            prevBtn.innerHTML = '<i class="mdi mdi-chevron-left"></i>';
            prevBtn.disabled = meta.current_page === 1;
            prevBtn.onclick = () => fetchStudents(meta.current_page - 1);
            controls.appendChild(prevBtn);

            for (let i = 1; i <= meta.last_page; i++) {
                if (i > 5 && i < meta.last_page) continue;
                const pgBtn = document.createElement('button');
                pgBtn.className = `pagination-btn ${meta.current_page === i ? 'active' : ''}`;
                pgBtn.innerText = i;
                pgBtn.onclick = () => fetchStudents(i);
                controls.appendChild(pgBtn);
            }

            const nextBtn = document.createElement('button');
            nextBtn.className = 'pagination-btn';
            nextBtn.innerHTML = '<i class="mdi mdi-chevron-right"></i>';
            nextBtn.disabled = meta.current_page === meta.last_page;
            nextBtn.onclick = () => fetchStudents(meta.current_page + 1);
            controls.appendChild(nextBtn);
        }

        // --- FILTERS & MODALS ---
        document.getElementById('studentSearch')?.addEventListener('input', () => {
            const val = document.getElementById('studentSearch').value;
            const mobileSearch = document.getElementById('studentSearchMobile');
            if (mobileSearch) mobileSearch.value = val;
            fetchStudents(1);
        });

        document.getElementById('studentSearchMobile')?.addEventListener('input', () => {
            const val = document.getElementById('studentSearchMobile').value;
            const desktopSearch = document.getElementById('studentSearch');
            if (desktopSearch) desktopSearch.value = val;
            fetchStudents(1);
        });

        document.getElementById('applyFilter')?.addEventListener('click', () => {
            fetchStudents(1);
            filterModal?.classList.add('hidden');
        });

        document.getElementById('resetFilter')?.addEventListener('click', () => {
            setSelectedValue('classFilter', '');
            populateDropdownSelect('groupFilter', [], '', 'All Groups');
            populateDropdownSelect('sectionFilter', [], '', 'All Sections');
            populateDropdownSelect('sessionFilter', [], '', 'All Sessions');
            fetchStudents(1);
            filterModal?.classList.add('hidden');
        });

        document.getElementById('closeFilterModal')?.addEventListener('click', () => {
            filterModal?.classList.add('hidden');
        });

        function closeModal() {
            studentModal.classList.add('hidden');
        }

        function closeDetailsModal() {
            detailsModal.classList.add('hidden');
        }
        function closeAdmissionFormModal() {
            document.getElementById('admissionFormModal').classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadFilterOptions();
            fetchStudents();

            // Modal Triggering Logic
            const toggleModal = (id, show) => {
                const el = document.getElementById(id);
                if (el) el.classList.toggle('hidden', !show);
            };

            document.getElementById('btnFilter')?.addEventListener('click', () => toggleModal('filterModal', true));
            document.getElementById('btnExport')?.addEventListener('click', () => toggleModal('exportModal', true));
            document.getElementById('closeExport')?.addEventListener('click', () => toggleModal('exportModal',
                false));
            document.getElementById('closeStudentModal')?.addEventListener('click', closeModal);
            document.getElementById('closeDeactivateStudentModal')?.addEventListener('click', () => toggleModal('deactivateStudentModal', false));
            document.getElementById('closeActivateStudentModal')?.addEventListener('click', () => toggleModal('activateStudentModal', false));
            document.getElementById('closeDeleteStudentModal')?.addEventListener('click', () => toggleModal('deleteStudentModal', false));

            window.onclick = function(event) {
                if (event.target.classList.contains('premium-modal')) {
                    event.target.classList.add('hidden');
                }
                if (event.target.id === 'deactivateStudentModal' || event.target.id === 'activateStudentModal' || event.target.id === 'deleteStudentModal') {
                    event.target.classList.add('hidden');
                }
            };

            // Modified on 2026-07-09: Automatically trigger View or Edit modal based on URL query parameters
            const urlParams = new URLSearchParams(window.location.search);
            const action = urlParams.get('action');
            const studentId = urlParams.get('student_id');
            if (studentId) {
                if (action === 'view') {
                    viewAdmissionForm(studentId);
                } else if (action === 'edit') {
                    editStudent(studentId);
                }
            }
            const dropBtn = document.getElementById('addStudentDropdownBtn');
            const dropMenu = document.getElementById('addStudentDropdownMenu');
            if (dropBtn && dropMenu) {
                dropBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    dropMenu.classList.toggle('hidden');
                });
                document.addEventListener('click', () => {
                    dropMenu.classList.add('hidden');
                });
            }
        });

        // --- QUICK ADD MODALS INTERACTION & SUBMISSION LOGIC ---
    </script>
    @include('school.partials.quick-add-js')
@endsection
