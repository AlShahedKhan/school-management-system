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
            border: 1px solid #2563eb;
            color: #2563eb;
            font-weight: 500;
            cursor: pointer;
            transition: all .2s ease;
        }

        .btn-outline-premium:hover {
            background: #2563eb;
            color: #fff;
        }

        .form-input-fixed {
            width: 100%;
            border: 1px solid #cbd5e1 !important;
            padding: .5rem .7rem;
            font-size: .85rem;
            outline: none;
            border-radius: 0;
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
        @media (max-width: 1023px) {
            .table-responsive table {
                min-width: 900px !important;
            }
        }

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

        .form-input-fixed {
            width: 100%;
            border: 1px solid #cbd5e1 !important;
            padding: .5rem .7rem;
            border-radius: 0;
            font-size: .85rem;
            background: #fff;
            outline: none;
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

        .modal-content-sharp {
            border-radius: 0 !important;
        }

        /* Increase font size for dropdown select components for better visibility */
        [data-dropdown-select] button,
        [data-dropdown-select] .dropdown-select-option {
            font-size: 13px !important;
        }
    </style>

    <div class="main-view-container">
        <div class="max-w-full mx-auto w-full">
            {{-- Header Section --}}
            <div class="bg-white border border-gray-200 p-2.5 sm:p-4 mb-2" style="border-radius:0;">
                <div class="mb-4 flex items-start justify-between">
                    <div>
                        <h3 id="pageHeader" data-keep-header="true" class="text-[15px] sm:text-xl text-gray-800 font-normal leading-tight">Teacher List</h3>
                        <div class="flex items-center text-slate-400 text-[12px] mt-1">
                            <span>School</span>
                            <i class="fas fa-chevron-right mx-1.5 text-[10px]"></i>
                            <span id="pageTitle" class="text-slate-500">Permissions</span>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                    <div class="hidden lg:flex items-center gap-2">
                        <x-input.search
                            id="permSearch"
                            placeholder="Search Teacher..."
                            class="hidden w-full lg:block lg:w-72"
                        />
                        <x-button.secondary id="btnRestoreDesktop" onclick="document.getElementById('permSearch').value = ''; document.getElementById('permSearch').dispatchEvent(new Event('input'));">
                            Restore
                        </x-button.secondary>
                    </div>
                    <div class="grid w-full grid-cols-3 gap-2 lg:flex lg:w-auto">
                        <x-button.secondary id="btnFilter">
                            Filter
                        </x-button.secondary>
                        <x-dropdown button-id="btnExport1" menu-id="exportDropdown" label="Export">
                            <x-dropdown.item id="exportPdf">PDF</x-dropdown.item>
                            <x-dropdown.item id="exportExcel">Excel</x-dropdown.item>
                            <x-dropdown.item id="exportPrint">Print</x-dropdown.item>
                        </x-dropdown>
                        <x-button.primary id="openPermModal" class="flex-1 lg:flex-none" onclick="openModal()">
                            Permission
                        </x-button.primary>
                    </div>
                </div>
                <div class="mt-3 grid grid-cols-3 gap-2 lg:hidden">
                    <x-input.search
                        id="permSearchMobile"
                        placeholder="Search Teacher..."
                        class="col-span-2 min-w-0"
                    />
                    <x-button.secondary id="btnRestoreMobile" onclick="document.getElementById('permSearchMobile').value = ''; document.getElementById('permSearchMobile').dispatchEvent(new Event('input'));">
                        Restore
                    </x-button.secondary>
                </div>
            </div>

            <div class="table-card">
                <div class="table-responsive">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th width="50">Sl</th>
                                <th>Class</th>
                                <th>Group</th>
                                <th>Section</th>
                                <th>Subject</th>
                                <th>Teacher</th>
                                <th>ID Number</th>
                                <th>Designation</th>
                                <th width="100" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="permTableBody"></tbody>
                    </table>
                </div>

                <div class="flex items-center justify-between p-3 bg-white border-t border-gray-100">
                    <div class="text-[10px] text-gray-500 font-bold uppercase tracking-widest" id="paginationInfo">
                        0 of 0</div>
                    <div class="flex items-center gap-1" id="paginationLinks"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Modal --}}
    <x-modal.form
        id="filterModal"
        form-id="permissionFilterForm"
        title="Permission filter"
        close-button-id="resetFilter"
        action="#"
        method="GET"
        :enctype="null"
        class="permission-filter-modal"
        panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[288px] overflow-visible border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] md:max-w-[480px]"
    >
        <div class="relative">
            <x-input.dropdown-select
                id="teacherFilter"
                name="teacher_id"
                placeholder="Select Teacher..."
            />
            <x-input.floating-label for="teacherFilter" :floating="false">Teacher</x-input.floating-label>
        </div>

        <div class="relative">
            <x-input.control
                id="designationAutoShow"
                class="peer placeholder:text-transparent"
                name="designation"
                placeholder=" "
                readonly
            />
            <x-input.floating-label for="designationAutoShow">
                Designation
            </x-input.floating-label>
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



    {{-- Grant Permission Modal --}}
    <x-modal.form
        id="permModal"
        form-id="permForm"
        title="Grant Permission"
        close-button-id="closePermModal"
        title-class="teacher-register-modal-title m-0 text-center font-semibold leading-tight text-slate-800"
    >
        <input type="hidden" id="record_id">

        <div class="relative w-full">
            <x-input.dropdown-select id="class_id" name="class_id" placeholder="Choose class..." add-button-id="add_perm_class_btn" add-button-label="Add Class" add-button-target="quickClassModal" />
            <x-input.floating-label for="class_id" :floating="false">Class</x-input.floating-label>
        </div>

        <div class="relative w-full">
            <x-input.dropdown-select id="group_id" name="group_id" placeholder="None" add-button-id="add_perm_group_btn" add-button-label="Add Group" add-button-target="quickGroupModal" />
            <x-input.floating-label for="group_id" :floating="false">Group</x-input.floating-label>
        </div>

        <div class="relative w-full">
            <x-input.dropdown-select id="section_id" name="section_id" placeholder="None" add-button-id="add_perm_section_btn" add-button-label="Add Section" add-button-target="quickSectionModal" />
            <x-input.floating-label for="section_id" :floating="false">Section</x-input.floating-label>
        </div>

        <div class="relative w-full">
            <x-input.dropdown-select id="subject_id" name="subject_id" placeholder="Choose subject..." add-button-id="add_perm_subject_btn" add-button-label="Add Subject" add-button-target="quickSubjectModal" />
            <x-input.floating-label for="subject_id" :floating="false">Subject</x-input.floating-label>
        </div>

        <div class="relative w-full md:col-span-2">
            <x-input.dropdown-select id="teacher_id" name="teacher_id" placeholder="Choose teacher..." add-button-id="add_perm_teacher_btn" add-button-label="Add Teacher" add-button-target="teacherModal" />
            <x-input.floating-label for="teacher_id" :floating="false">Select Teacher</x-input.floating-label>
        </div>

        <div class="relative w-full md:col-span-2">
            <x-input.control id="teacher_designation" class="peer placeholder:text-transparent bg-gray-50 text-gray-500" placeholder=" " readonly />
            <x-input.floating-label for="teacher_designation">Designation</x-input.floating-label>
        </div>
    </x-modal.form>

    <script>
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;

        let rawClasses = [],
            rawGroups = [],
            rawSections = [],
            rawSubjects = [],
            rawTeachers = [];

        function populateDropdownSelect(id, options, defaultText, emptyValue = '', onOptionClickCallback = null) {
            const menu = document.getElementById(id + 'Menu');
            const input = document.getElementById(id);
            const label = document.querySelector('#' + id + 'Button [data-dropdown-select-label]');

            if (!menu || !input || !label) return;

            menu.innerHTML = '';
            
            if (defaultText) {
                const defaultOpt = document.createElement('button');
                defaultOpt.type = 'button';
                defaultOpt.className = 'dropdown-select-option m-0 flex min-h-6 w-full items-center border-0 bg-white px-3 py-1 text-left text-[11px] font-normal leading-tight text-slate-800 transition-colors hover:bg-slate-100';
                defaultOpt.textContent = defaultText;
                defaultOpt.dataset.value = emptyValue;
                defaultOpt.addEventListener('click', () => {
                    input.value = emptyValue;
                    label.textContent = defaultText;
                    menu.classList.add('hidden');
                    
                    menu.querySelectorAll('[data-dropdown-select-option]').forEach(item => {
                        item.classList.remove('bg-slate-100', 'text-slate-900');
                        item.classList.add('text-slate-800');
                        item.setAttribute('aria-selected', 'false');
                    });
                    defaultOpt.classList.add('bg-slate-100', 'text-slate-900');
                    defaultOpt.setAttribute('aria-selected', 'true');

                    input.dispatchEvent(new Event('change', { bubbles: true }));
                    if (onOptionClickCallback) onOptionClickCallback(emptyValue);
                });
                menu.appendChild(defaultOpt);
            }

            options.forEach(optData => {
                const opt = document.createElement('button');
                opt.type = 'button';
                opt.className = 'dropdown-select-option m-0 flex min-h-6 w-full items-center border-0 bg-white px-3 py-1 text-left text-[11px] font-normal leading-tight text-slate-800 transition-colors hover:bg-slate-100';
                opt.textContent = optData.label;
                opt.dataset.value = optData.value;
                opt.addEventListener('click', () => {
                    input.value = optData.value;
                    label.textContent = optData.label;
                    menu.classList.add('hidden');

                    menu.querySelectorAll('[data-dropdown-select-option]').forEach(item => {
                        const isSelected = item === opt;
                        item.classList.toggle('bg-slate-100', isSelected);
                        item.classList.toggle('text-slate-900', isSelected);
                        item.classList.toggle('text-slate-800', !isSelected);
                        item.setAttribute('aria-selected', String(isSelected));
                    });

                    input.dispatchEvent(new Event('change', { bubbles: true }));
                    if (onOptionClickCallback) onOptionClickCallback(optData.value);
                });
                menu.appendChild(opt);
            });
        }

        function setDropdownSelectValue(id, value, labelText) {
            const input = document.getElementById(id);
            const label = document.querySelector('#' + id + 'Button [data-dropdown-select-label]');
            const menu = document.getElementById(id + 'Menu');

            if (input) input.value = value || '';
            if (label) label.textContent = labelText || (label.dataset.placeholder || 'Select...');

            if (menu) {
                menu.querySelectorAll('[data-dropdown-select-option]').forEach(item => {
                    const isSelected = String(item.dataset.value) === String(value);
                    item.classList.toggle('bg-slate-100', isSelected);
                    item.classList.toggle('text-slate-900', isSelected);
                    item.classList.toggle('text-slate-800', !isSelected);
                    item.setAttribute('aria-selected', String(isSelected));
                });
            }
        }

        function setSelectedValue(id, value) {
            const input = document.getElementById(id);
            if (!input) return;
            input.value = value;
            const menu = document.getElementById(id + 'Menu');
            if (menu) {
                const option = menu.querySelector(`[data-value="${value}"]`);
                const label = document.querySelector(`#${id}Button [data-dropdown-select-label]`);
                if (option && label) {
                    label.textContent = option.textContent.trim();
                    menu.querySelectorAll('[data-dropdown-select-option]').forEach(item => {
                        const isSelected = item === option;
                        item.classList.toggle('bg-slate-100', isSelected);
                        item.classList.toggle('text-slate-900', isSelected);
                        item.classList.toggle('text-slate-800', !isSelected);
                        item.setAttribute('aria-selected', String(isSelected));
                    });
                } else if (label) {
                    label.textContent = label.dataset.placeholder || 'Select...';
                }
            }
            input.dispatchEvent(new Event('change', { bubbles: true }));
        }

        // Separate data fetching from UI rendering to prevent auto-unselect issues
        async function initData() {
            try {
                const [c, g, s, sub, t] = await Promise.all([
                    axios.get('/api/get-school-classes'),
                    axios.get('/api/get-school-groups'),
                    axios.get('/api/get-school-sections'),
                    axios.get('/api/get-school-subjects'),
                    // Modified on 2026-07-11: Fetch all active teachers for the dropdown list
                    axios.get('/api/teachers', { params: { all: true, status: 'Active' } })
                ]);

                rawClasses = c.data.data || [];
                rawGroups = g.data.data || [];
                rawSections = s.data.data || [];
                rawSubjects = sub.data.data || [];
                rawTeachers = t.data.data || [];

                renderStaticDropdowns();
            } catch (error) {
                console.error("Error loading initial data", error);
            }
        }

        function renderStaticDropdowns() {
            const filterMenu = document.getElementById('teacherFilterMenu');
            const filterInput = document.getElementById('teacherFilter');
            const filterLabel = document.querySelector('#teacherFilterButton [data-dropdown-select-label]');
            const currentFilterTeacher = filterInput ? filterInput.value : '';

            if (filterMenu) {
                filterMenu.innerHTML = '';
                const defaultOpt = document.createElement('button');
                defaultOpt.type = 'button';
                defaultOpt.className = 'dropdown-select-option m-0 flex min-h-6 w-full items-center border-0 bg-white px-3 py-1 text-left text-[11px] font-normal leading-tight text-slate-800 transition-colors hover:bg-slate-100';
                defaultOpt.textContent = 'Select Teacher...';
                defaultOpt.dataset.value = '';
                defaultOpt.addEventListener('click', () => {
                    filterInput.value = '';
                    filterLabel.textContent = 'Select Teacher...';
                    filterMenu.classList.add('hidden');
                    filterInput.dispatchEvent(new Event('change', { bubbles: true }));
                });
                filterMenu.appendChild(defaultOpt);
            }

            rawTeachers.forEach(t => {
                if (filterMenu) {
                    const opt = document.createElement('button');
                    opt.type = 'button';
                    opt.className = 'dropdown-select-option m-0 flex min-h-6 w-full items-center border-0 bg-white px-3 py-1 text-left text-[11px] font-normal leading-tight text-slate-800 transition-colors hover:bg-slate-100';
                    opt.textContent = t.name;
                    opt.dataset.value = t.id;
                    opt.dataset.optionDesignation = t.designation || '';
                    opt.addEventListener('click', () => {
                        filterInput.value = t.id;
                        filterLabel.textContent = t.name;
                        filterMenu.classList.add('hidden');
                        
                        filterMenu.querySelectorAll('[data-dropdown-select-option]').forEach(item => {
                            const isSelected = item === opt;
                            item.classList.toggle('bg-slate-100', isSelected);
                            item.classList.toggle('text-slate-900', isSelected);
                            item.classList.toggle('text-slate-800', !isSelected);
                            item.setAttribute('aria-selected', String(isSelected));
                        });

                        filterInput.dispatchEvent(new Event('change', { bubbles: true }));
                    });
                    filterMenu.appendChild(opt);
                }
            });

            if (filterInput && currentFilterTeacher) {
                filterInput.value = currentFilterTeacher;
            }

            const teachersList = rawTeachers.map(t => ({ value: t.id, label: t.name }));
            populateDropdownSelect('teacher_id', teachersList, 'Choose teacher...', '');

            const classesList = rawClasses.map(c => ({ value: c.id, label: c.class_name }));
            populateDropdownSelect('class_id', classesList, 'Choose class...', '');
        }

        function filterDependents() {
            const classId = document.getElementById('class_id').value;

            const filteredGroups = rawGroups.filter(g => g.class_id == classId).map(g => ({ value: g.id, label: g.group_name }));
            populateDropdownSelect('group_id', filteredGroups, 'None', '');

            const filteredSubjects = rawSubjects.filter(sub => sub.class_id == classId).map(sub => ({ value: sub.id, label: sub.subject_name }));
            populateDropdownSelect('subject_id', filteredSubjects, 'Choose subject...', '');

            filterSections();
        }

        function filterSections() {
            const classId = document.getElementById('class_id').value;
            const groupId = document.getElementById('group_id').value;

            const filteredSections = rawSections.filter(s => s.class_id == classId && (!groupId || s.group_id == groupId)).map(s => ({ value: s.id, label: s.section_name }));
            populateDropdownSelect('section_id', filteredSections, 'None', '');
        }

        function updateTeacherDesignation() {
            const teacherId = document.getElementById('teacher_id').value;
            const teacher = rawTeachers.find(t => t.id == teacherId);
            const desigInput = document.getElementById('teacher_designation');

            if (teacher) {
                desigInput.value = teacher.designation || 'N/A';
            } else {
                desigInput.value = '';
            }
        }

        function fetchPermissions(page = 1) {

            const search = document.getElementById('permSearch').value;

            const teacherId = document.getElementById('teacherFilter') ? document.getElementById('teacherFilter').value : '';

            axios.get('/api/teacher-permissions', {

                params: {

                    search,

                    teacher_id: teacherId,

                    page

                }

            }).then(res => {
                const tbody = document.getElementById('permTableBody');
                const {
                    data,
                    from,
                    to,
                    total,
                    current_page,
                    last_page
                } = res.data;

                // PERFORMANCE FIX: Build string once and inject to DOM once
                let rows = '';
                data.forEach((item, index) => {
                    rows += `
                <tr>
                    <td class="text-gray-400 font-mono">${(from || 0) + index}</td>
                    <td>${item.school_class?.class_name || '-'}</td>
                    <td>${item.school_group?.group_name || '-'}</td>
                    <td>${item.school_section?.section_name || '-'}</td>
                    <td>${item.school_subject?.subject_name || '-'}</td>
                    <td><span>${item.teacher_name}</span></td>
                    <td>${item.teacher_id_number || '-'}</td>
                    <td>${item.teacher_designation || '-'}</td>
                    <td>
                        <div class="flex justify-center gap-3">
                            <button onclick="editRecord(${item.id})" class="action-icon-btn text-blue-500"><i class="far fa-edit" style="font-size: 15px;"></i></button>
                            <button onclick="deleteRecord(${item.id})" class="action-icon-btn text-red-400"><i class="far fa-trash-alt" style="font-size: 15px;"></i></button>
                        </div>
                    </td>
                </tr>`;
                });

                tbody.innerHTML = rows;

                const pagInfo = document.getElementById('paginationInfo');
                if (pagInfo) pagInfo.innerText = `${to || 0} OF ${total || 0}`;
                renderPermPagination(current_page, last_page);
            });
        }

        function renderPermPagination(current, last) {
            const container = document.getElementById('paginationLinks');
            if (!container) return;
            container.innerHTML = '';

            const prevBtn = document.createElement('button');
            prevBtn.className = 'pagination-btn';
            prevBtn.innerHTML = '<i class="mdi mdi-chevron-left"></i>';
            prevBtn.disabled = current === 1;
            prevBtn.onclick = () => fetchPermissions(current - 1);
            container.appendChild(prevBtn);

            for (let i = 1; i <= last; i++) {
                if (i === 1 || i === last || (i >= current - 1 && i <= current + 1)) {
                    const pageBtn = document.createElement('button');
                    pageBtn.className = `pagination-btn ${i === current ? 'active' : ''}`;
                    pageBtn.innerText = i;
                    pageBtn.onclick = () => fetchPermissions(i);
                    container.appendChild(pageBtn);
                } else if (i === current - 2 || i === current + 2) {
                    const dots = document.createElement('span');
                    dots.className = 'px-1 text-gray-400';
                    dots.innerText = '...';
                    container.appendChild(dots);
                }
            }

            const nextBtn = document.createElement('button');
            nextBtn.className = 'pagination-btn';
            nextBtn.innerHTML = '<i class="mdi mdi-chevron-right"></i>';
            nextBtn.disabled = current === last;
            nextBtn.onclick = () => fetchPermissions(current + 1);
            container.appendChild(nextBtn);
        }

        async function openModal() {
            document.getElementById('permForm').reset();
            document.getElementById('record_id').value = '';
            document.getElementById('teacher_designation').value = '';

            setDropdownSelectValue('teacher_id', '', 'Choose teacher...');
            setDropdownSelectValue('class_id', '', 'Choose class...');
            setDropdownSelectValue('group_id', '', 'None');
            setDropdownSelectValue('section_id', '', 'None');
            setDropdownSelectValue('subject_id', '', 'Choose subject...');

            if (rawClasses.length === 0) await initData();
            else renderStaticDropdowns();

            document.getElementById('permModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('permModal').classList.add('hidden');
        }

        document.getElementById('permForm').onsubmit = function(e) {
            e.preventDefault();
            const id = document.getElementById('record_id').value;
            const data = {
                teacher_id: document.getElementById('teacher_id').value,
                class_id: document.getElementById('class_id').value,
                subject_id: document.getElementById('subject_id').value,
                group_id: document.getElementById('group_id').value,
                section_id: document.getElementById('section_id').value,
            };

            const req = id ? axios.put(`/api/teacher-permissions/${id}`, data) : axios.post('/api/teacher-permissions',
                data);
            req.then(() => {
                Toastify({
                    text: id ? "Permission Updated" : "Permission Created",
                    style: {
                        background: "#10b981"
                    }
                }).showToast();
                closeModal();
                fetchPermissions();
            });
        };

        async function editRecord(id) {
            const res = await axios.get(`/api/teacher-permissions/${id}`);
            const item = res.data;

            await openModal();

            document.getElementById('record_id').value = item.id;

            const teacher = rawTeachers.find(t => t.id == item.teacher_id);
            setDropdownSelectValue('teacher_id', item.teacher_id, teacher ? teacher.name : 'Choose teacher...');
            updateTeacherDesignation();

            const cls = rawClasses.find(c => c.id == item.class_id);
            setDropdownSelectValue('class_id', item.class_id, cls ? cls.class_name : 'Choose class...');

            filterDependents();

            setTimeout(() => {
                const grp = rawGroups.find(g => g.id == item.group_id);
                setDropdownSelectValue('group_id', item.group_id || '', grp ? grp.group_name : 'None');

                filterSections();

                const sec = rawSections.find(s => s.id == item.section_id);
                setDropdownSelectValue('section_id', item.section_id || '', sec ? sec.section_name : 'None');

                const sub = rawSubjects.find(s => s.id == item.subject_id);
                setDropdownSelectValue('subject_id', item.subject_id, sub ? sub.subject_name : 'Choose subject...');
            }, 100);
        }

        function deleteRecord(id) {
            Swal.fire({
                title: 'Revoke Permission?',
                text: "The teacher will lose access to this section.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Revoke',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(`/api/teacher-permissions/${id}`).then(() => fetchPermissions());
                }
            });
        }

        document.getElementById('permSearch').addEventListener('input', () => fetchPermissions(1));
        document.getElementById('permSearchMobile').addEventListener('input', (e) => {
            document.getElementById('permSearch').value = e.target.value;
            fetchPermissions(1);
        });

        document.addEventListener('DOMContentLoaded', function() {
            const toggleModal = (id, show) => document.getElementById(id).classList.toggle('hidden', !show);
            document.getElementById('btnFilter').addEventListener('click', () => toggleModal('filterModal', true));
             document.getElementById('closePermModal').addEventListener('click', closeModal);
             document.getElementById('resetFilter').addEventListener('click', () => {
                 document.getElementById('teacherFilter').value = '';
                 const filterLabel = document.querySelector('#teacherFilterButton [data-dropdown-select-label]');
                 if (filterLabel) {
                     filterLabel.textContent = filterLabel.dataset.placeholder || 'Select Teacher...';
                 }
                 document.getElementById('designationAutoShow').value = '';
                 fetchPermissions(1);
                 toggleModal('filterModal', false);
             });

            document.getElementById('applyFilter').addEventListener('click', () => {

                fetchPermissions(1);

                toggleModal('filterModal', false);

            });


            window.onclick = (event) => {
                if (event.target.classList.contains('premium-modal')) event.target.classList.add('hidden');
            };

            document.getElementById('teacher_id').addEventListener('change', updateTeacherDesignation);
            document.getElementById('class_id').addEventListener('change', filterDependents);
            document.getElementById('group_id').addEventListener('change', filterSections);

            document.getElementById('teacherFilter').addEventListener('change', () => {

                const val = document.getElementById('teacherFilter').value;

                const teacher = rawTeachers.find(t => t.id == val);

                const autoShow = document.getElementById('designationAutoShow');

                if (teacher) {

                    autoShow.value = teacher.designation || 'N/A';

                } else {

                    autoShow.value = '';

                }

            });

            initData();

            fetchPermissions();
        });
    </script>
    @include('school.partials.teacher-register-modal')
    @include('school.partials.teacher-register-js')
    @include('school.partials.quick-add-modals')
    @include('school.partials.quick-add-js')
    @include('school.partials.export-dropdown')
@endsection