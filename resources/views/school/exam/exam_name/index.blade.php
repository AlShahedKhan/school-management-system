@extends('layouts.school')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="main-view-container">
        <div class="max-w-full mx-auto w-full">
            @include('school.exam.exam_name.partials.header')
            @include('school.exam.exam_name.partials.table')
        </div>
    </div>

    {{-- Filter Modal --}}
    <x-modal.form
        id="filterModal"
        form-id="examFilterForm"
        title="Exam Filter"
        close-button-id="closeExamFilterModal"
        action="#"
        method="GET"
        :enctype="null"
        class="exam-filter-modal"
        panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[288px] overflow-visible border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] md:max-w-[480px]"
    >
        <x-input.dropdown-select
            id="examClassFilter"
            name="class_id"
            placeholder="Select Class"
            :value="request('class_id')"
            :options="[]"
            add-button-id="openClassFromExamFilter"
            add-button-label="Add class"
            add-button-target="classModal"
        />
        <x-input.dropdown-select
            id="examGroupFilter"
            name="group_id"
            placeholder="Select Group"
            :value="request('group_id')"
            :options="[]"
            add-button-id="openGroupFromExamFilter"
            add-button-label="Add group"
            add-button-target="groupModal"
        />
        <x-input.dropdown-select
            id="examSectionFilter"
            name="section_id"
            placeholder="Select Section"
            :value="request('section_id')"
            :options="[]"
            add-button-id="openSectionFromExamFilter"
            add-button-label="Add section"
            add-button-target="sectionModal"
        />
        <x-input.dropdown-select
            id="examSessionFilter"
            name="session_id"
            placeholder="Select Session"
            :value="request('session_id')"
            :options="[]"
            add-button-id="openSessionFromExamFilter"
            add-button-label="Add session"
            add-button-target="sessionModal"
        />

        <x-slot:footer>
            <div class="grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 py-3">
                <x-button.secondary id="resetFilter" type="button" class="w-full">Reset</x-button.secondary>
                <x-button.primary id="applyFilter" type="button" class="w-full">Apply</x-button.primary>
            </div>
        </x-slot:footer>
    </x-modal.form>

    @include('school.exam.exam_name.partials.exam-modal')
    @include('school.partials.export-dropdown')
    @include('school.academic.class.partials.class-modal')
    @include('school.academic.group.partials.group-modal')
    @include('school.academic.section.partials.section-modal')
    @include('school.academic.session.partials.session-modal')

    @include('school.exam.exam_name.partials.js.modal-open')
    @include('school.exam.exam_name.partials.js.modal-submit')
    @include('school.exam.exam_name.partials.js.error-validation')
    @include('school.academic.class.partials.js.modal-open')
    @include('school.academic.group.partials.js.modal-open')
    @include('school.academic.section.partials.js.modal-open')
    @include('school.academic.session.partials.js.modal-open')
    @include('school.academic.class.partials.js.modal-submit')
    @include('school.academic.group.partials.js.modal-submit')
    @include('school.academic.section.partials.js.modal-submit')
    @include('school.academic.session.partials.js.modal-submit')
    @include('school.academic.class.partials.js.error-validation')
    @include('school.academic.group.partials.js.error-validation')
    @include('school.academic.section.partials.js.error-validation')
    @include('school.academic.session.partials.js.error-validation')

    <script>
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;

        let currentPage = 1;
        let currentFilterClass = '';
        let currentFilterGroup = '';
        let currentFilterSection = '';
        let currentFilterSession = '';

        function getExamSearchTerm() {
            const desktopSearch = document.getElementById('examSearch')?.value || '';
            const mobileSearch = document.getElementById('examSearchMobile')?.value || '';

            return desktopSearch || mobileSearch;
        }

        function clearExamSearchInputs() {
            const desktopSearch = document.getElementById('examSearch');
            const mobileSearch = document.getElementById('examSearchMobile');

            if (desktopSearch) desktopSearch.value = '';
            if (mobileSearch) mobileSearch.value = '';
        }

        function populateDropdown(menuId, data, valueField, labelField) {
            const menu = document.querySelector(`#${menuId}`);
            if (!menu) return;
            menu.innerHTML = '';
            data.forEach(item => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'dropdown-select-option m-0 flex min-h-6 w-full items-center border-0 bg-white px-3 py-1 text-left text-[11px] font-normal leading-tight transition-colors hover:bg-slate-100 text-slate-800';
                btn.dataset.value = String(item[valueField]);
                btn.textContent = item[labelField];
                btn.setAttribute('role', 'option');
                btn.setAttribute('aria-selected', 'false');
                btn.setAttribute('data-dropdown-select-option', '');
                btn.addEventListener('click', function() {
                    const root = menu.closest('[data-dropdown-select]');
                    const input = root.querySelector('[data-dropdown-select-input]');
                    const label = root.querySelector('[data-dropdown-select-label]');
                    input.value = this.dataset.value || '';
                    label.textContent = this.textContent.trim();
                    menu.querySelectorAll('[data-dropdown-select-option]').forEach(item => {
                        const sel = item === this;
                        item.classList.toggle('bg-slate-100', sel);
                        item.classList.toggle('text-slate-900', sel);
                        item.classList.toggle('text-slate-800', !sel);
                        item.setAttribute('aria-selected', String(sel));
                    });
                    menu.classList.add('hidden');
                    root.querySelector('[data-dropdown-select-button]')?.setAttribute('aria-expanded', 'false');
                    const icon = root.querySelector('[data-dropdown-select-button] i');
                    if (icon) icon.classList.remove('rotate-180');
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                });
                menu.appendChild(btn);
            });
        }

        function setDropdownValue(dropdownId, value, label) {
            const input = document.querySelector(`#${dropdownId}`);
            if (input) input.value = value;
            const labelEl = document.querySelector(`#${dropdownId}Button [data-dropdown-select-label]`);
            if (labelEl) labelEl.textContent = label;
        }

        function formatDate(dateString) {
            if (!dateString) return '-';
            const parts = dateString.split('-');
            if (parts.length !== 3) return dateString;
            return `${parts[2]}/${parts[1]}/${parts[0]}`;
        }

        function loadExamFilterGroupByClass(classId) {
            const params = classId ? { class_id: classId } : {};
            axios.get('/api/get-school-groups', { params }).then(res => {
                const data = res.data.data || [];
                populateDropdown('examGroupFilterMenu', data, 'id', 'group_name');
                setDropdownValue('examGroupFilter', '', 'Select Group');
                setDropdownValue('examSectionFilter', '', 'Select Section');
                populateDropdown('examSectionFilterMenu', [], 'id', 'section_name');
                setDropdownValue('examSessionFilter', '', 'Select Session');
                populateDropdown('examSessionFilterMenu', [], 'id', 'session_year');

                loadExamFilterSectionByGroup(classId, '');
            }).catch(() => {});
        }

        function loadExamFilterSectionByGroup(classId, groupId) {
            const params = {};
            if (classId) params.class_id = classId;
            if (groupId) params.group_id = groupId;
            axios.get('/api/get-school-sections', { params }).then(res => {
                const data = res.data.data || [];
                populateDropdown('examSectionFilterMenu', data, 'id', 'section_name');
                setDropdownValue('examSessionFilter', '', 'Select Session');
                populateDropdown('examSessionFilterMenu', [], 'id', 'session_year');
            }).catch(() => {});
        }

        function loadExamFilterSession(classId, groupId, sectionId) {
            const params = {};
            if (classId) params.class_id = classId;
            if (groupId) params.group_id = groupId;
            if (sectionId) params.section_id = sectionId;
            axios.get('/api/get-school-sessions-all', { params }).then(res => {
                const data = res.data.data || [];
                populateDropdown('examSessionFilterMenu', data, 'id', 'session_year');
            }).catch(() => {});
        }

        function loadFilterOptions() {
            axios.get('/api/get-school-classes').then(res => {
                populateDropdown('examClassFilterMenu', res.data.data || [], 'id', 'class_name');
            }).catch(() => {});
        }

        function fetchExams(page = 1) {
            currentPage = page;

            const classInput = document.querySelector('#examClassFilter');
            const groupInput = document.querySelector('#examGroupFilter');
            const sectionInput = document.querySelector('#examSectionFilter');
            const sessionInput = document.querySelector('#examSessionFilter');

            const params = {
                page,
                search: getExamSearchTerm(),
                class_id: classInput ? classInput.value : '',
                group_id: groupInput ? groupInput.value : '',
                section_id: sectionInput ? sectionInput.value : '',
                session_id: sessionInput ? sessionInput.value : '',
            };

            axios.get('/api/school-exam-names', { params }).then(res => {
                const meta = res.data;
                const tbody = document.getElementById('examTableBody');
                tbody.innerHTML = '';

                if (!meta.data || meta.data.length === 0) {
                    tbody.innerHTML =
                        `<tr><td colspan="9" class="border border-gray-300 px-3 py-10 text-center text-gray-500">No exam names found.</td></tr>`;
                    document.getElementById('paginationInfo').innerText = '0 of 0';
                    document.getElementById('paginationControls').innerHTML = '';
                    return;
                }

                meta.data.forEach((item, i) => {
                    const sl = meta.from ? meta.from + i : i + 1;
                    tbody.innerHTML += `
                        <tr class="hover:bg-gray-50">
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">${sl}</td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                <div class="donate-cell-scroll" title="${item.class_name || '-'}">${item.class_name || '-'}</div>
                            </td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                <div class="donate-cell-scroll" title="${item.group_name || '-'}">${item.group_name || '-'}</div>
                            </td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                <div class="donate-cell-scroll" title="${item.section_name || '-'}">${item.section_name || '-'}</div>
                            </td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                <div class="donate-cell-scroll" title="${item.session_name || '-'}">${item.session_name || '-'}</div>
                            </td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                <div class="donate-cell-scroll" title="${item.exam_name || '-'}">${item.exam_name || '-'}</div>
                            </td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                <div class="donate-cell-scroll" title="${formatDate(item.exam_start_date)}">${formatDate(item.exam_start_date)}</div>
                            </td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                <div class="donate-cell-scroll" title="${formatDate(item.exam_end_date)}">${formatDate(item.exam_end_date)}</div>
                            </td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                                <div class="flex h-6 w-full items-center justify-center -space-x-[3px]">
                                    <button type="button" title="Edit" onclick="editExam(${item.id})" class="flex h-6 w-[14px] items-center justify-center text-gray-600 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 hover:bg-gray-100 hover:text-blue-600 focus-visible:ring-blue-500">
                                        <i class="far fa-edit text-xs" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" title="Delete" onclick="deleteExam(${item.id})" class="flex h-6 w-[14px] items-center justify-center text-gray-600 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 hover:bg-gray-100 hover:text-red-600 focus-visible:ring-red-500">
                                        <i class="far fa-trash-alt text-xs" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>`;
                });

                renderPagination(meta);
            }).catch(err => {
                console.error('Load Error:', err);
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
            prevBtn.onclick = () => fetchExams(meta.current_page - 1);
            controls.appendChild(prevBtn);

            for (let i = 1; i <= meta.last_page; i++) {
                const btn = document.createElement('button');
                btn.className = `pagination-btn ${meta.current_page === i ? 'active' : ''}`;
                btn.innerText = i;
                btn.onclick = () => fetchExams(i);
                controls.appendChild(btn);
            }

            const nextBtn = document.createElement('button');
            nextBtn.className = 'pagination-btn';
            nextBtn.innerHTML = '<i class="mdi mdi-chevron-right"></i>';
            nextBtn.disabled = meta.current_page === meta.last_page;
            nextBtn.onclick = () => fetchExams(meta.current_page + 1);
            controls.appendChild(nextBtn);
        }

        function deleteExam(id) {
            Swal.fire({
                title: 'Delete Exam Name?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it'
            }).then(r => {
                if (r.isConfirmed) {
                    axios.delete('/api/school-exam-names/' + id).then(() => {
                        Toastify({ text: 'Exam name deleted', style: { background: '#ef4444' }, duration: 3000 }).showToast();
                        fetchExams(currentPage);
                    }).catch(() => {
                        Swal.fire('Error', 'Could not delete exam name.', 'error');
                    });
                }
            });
        }

        function exportData(type) {
            const classInput = document.querySelector('#examClassFilter');
            const groupInput = document.querySelector('#examGroupFilter');
            const sectionInput = document.querySelector('#examSectionFilter');
            const sessionInput = document.querySelector('#examSessionFilter');

            const params = new URLSearchParams({
                type,
                search: document.getElementById('examSearch')?.value || '',
                class_id: classInput ? classInput.value : '',
                group_id: groupInput ? groupInput.value : '',
                section_id: sectionInput ? sectionInput.value : '',
                session_id: sessionInput ? sessionInput.value : '',
            });
            window.location.href = `/api/school-exam-names-export?${params.toString()}`;
        }

        document.addEventListener('DOMContentLoaded', function () {
            loadFilterOptions();

            document.getElementById('examSearch')?.addEventListener('input', () => fetchExams(1));
            document.getElementById('examSearchMobile')?.addEventListener('input', () => fetchExams(1));

            document.getElementById('exportPdf')?.addEventListener('click', () => exportData('pdf'));
            document.getElementById('exportExcel')?.addEventListener('click', () => exportData('excel'));
            document.getElementById('exportPrint')?.addEventListener('click', () => window.print());

            document.getElementById('btnFilter')?.addEventListener('click', () => {
                document.getElementById('filterModal').classList.remove('hidden');
            });

            document.getElementById('examClassFilter')?.addEventListener('change', function () {
                loadExamFilterGroupByClass(this.value);
            });

            document.getElementById('examGroupFilter')?.addEventListener('change', function () {
                const classInput = document.getElementById('examClassFilter');
                const classId = classInput ? classInput.value : '';
                loadExamFilterSectionByGroup(classId, this.value);
            });

            document.getElementById('examSectionFilter')?.addEventListener('change', function () {
                const classInput = document.getElementById('examClassFilter');
                const classId = classInput ? classInput.value : '';
                const groupInput = document.getElementById('examGroupFilter');
                const groupId = groupInput ? groupInput.value : '';
                loadExamFilterSession(classId, groupId, this.value);
            });

            document.getElementById('resetFilter')?.addEventListener('click', () => {
                setDropdownValue('examClassFilter', '', 'Select Class');
                setDropdownValue('examGroupFilter', '', 'Select Group');
                setDropdownValue('examSectionFilter', '', 'Select Section');
                setDropdownValue('examSessionFilter', '', 'Select Session');
                populateDropdown('examGroupFilterMenu', [], 'id', 'group_name');
                populateDropdown('examSectionFilterMenu', [], 'id', 'section_name');
                populateDropdown('examSessionFilterMenu', [], 'id', 'session_year');
                currentFilterClass = '';
                currentFilterGroup = '';
                currentFilterSection = '';
                currentFilterSession = '';
                currentPage = 1;
                fetchExams(1);
                document.getElementById('filterModal').classList.add('hidden');
            });

            document.getElementById('applyFilter')?.addEventListener('click', () => {
                currentPage = 1;
                fetchExams(1);
                document.getElementById('filterModal').classList.add('hidden');
            });

            document.querySelectorAll('[role="dialog"]').forEach(dialog => {
                dialog.addEventListener('click', function (e) {
                    if (e.target === this) this.classList.add('hidden');
                });
            });

            document.addEventListener('click', function (e) {
                const btn = e.target.closest('[data-dropdown-add-target]');
                if (btn) {
                    const map = { classModal: 'Add Class', groupModal: 'Add Group', sectionModal: 'Add Section', sessionModal: 'Add Session' };
                    const title = map[btn.dataset.dropdownAddTarget];
                    if (title) {
                        const el = document.getElementById(btn.dataset.dropdownAddTarget + 'Title');
                        if (el) el.innerText = title;
                    }
                }
            });
        });

        document.getElementById('btnRestoreDesktop')?.addEventListener('click', () => {
            clearExamSearchInputs();
            currentPage = 1;
            fetchExams(1);
        });

        document.getElementById('btnRestoreMobile')?.addEventListener('click', () => {
            clearExamSearchInputs();
            currentPage = 1;
            fetchExams(1);
        });

        fetchExams();
    </script>
@endsection
