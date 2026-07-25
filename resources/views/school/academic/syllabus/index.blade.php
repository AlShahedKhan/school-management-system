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
            @include('school.academic.syllabus.partials.header')
            @include('school.academic.syllabus.partials.table')
        </div>
    </div>

    <x-modal.form
        id="filterModal"
        form-id="syllabusFilterForm"
        title="Syllabus Filter"
        close-button-id="resetFilter"
        action="#"
        method="GET"
        :enctype="null"
        class="syllabus-filter-modal"
        panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[288px] overflow-visible border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] md:max-w-[480px]"
    >
        <x-input.dropdown-select
            id="syllabusClassFilter"
            name="class_id"
            placeholder="Select Class"
            :value="request('class_id')"
            :options="[]"
            add-button-id="openClassFromSyllabusFilter"
            add-button-label="Add class"
            add-button-target="classModal"
        />
        <x-input.dropdown-select
            id="syllabusGroupFilter"
            name="group_id"
            placeholder="Select Group"
            :value="request('group_id')"
            :options="[]"
            add-button-id="openGroupFromSyllabusFilter"
            add-button-label="Add group"
            add-button-target="groupModal"
        />
        <x-input.dropdown-select
            id="syllabusSectionFilter"
            name="section_id"
            placeholder="Select Section"
            :value="request('section_id')"
            :options="[]"
            add-button-id="openSectionFromSyllabusFilter"
            add-button-label="Add section"
            add-button-target="sectionModal"
        />
        <x-input.dropdown-select
            id="syllabusSessionFilter"
            name="session_id"
            placeholder="Select Session"
            :value="request('session_id')"
            :options="[]"
            add-button-id="openSessionFromSyllabusFilter"
            add-button-label="Add session"
            add-button-target="sessionModal"
        />
        <x-input.dropdown-select
            id="syllabusExamFilter"
            name="exam_id"
            placeholder="Select Exam"
            :value="request('exam_id')"
            :options="[]"
            add-button-id="openExamFromSyllabusFilter"
            add-button-label="Add exam"
            add-button-target="examModal"
        />

        <x-slot:footer>
            <div class="grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 py-3">
                <x-button.secondary id="resetFilter" type="button" class="w-full">Reset</x-button.secondary>
                <x-button.primary id="applyFilter" type="button" class="w-full">Apply</x-button.primary>
            </div>
        </x-slot:footer>
    </x-modal.form>

    @include('school.academic.syllabus.partials.syllabus-modal')
    @include('school.partials.export-dropdown')
    @include('school.academic.class.partials.class-modal')
    @include('school.academic.group.partials.group-modal')
    @include('school.academic.section.partials.section-modal')
    @include('school.academic.session.partials.session-modal')
    @include('school.academic.subject.partials.subject-modal')
    @include('school.exam.exam_name.partials.exam-modal')

    @include('school.academic.syllabus.partials.js.modal-open')
    @include('school.academic.syllabus.partials.js.modal-submit')
    @include('school.academic.syllabus.partials.js.error-validation')
    @include('school.academic.class.partials.js.modal-open')
    @include('school.academic.group.partials.js.modal-open')
    @include('school.academic.section.partials.js.modal-open')
    @include('school.academic.class.partials.js.modal-submit')
    @include('school.academic.group.partials.js.modal-submit')
    @include('school.academic.section.partials.js.modal-submit')
    @include('school.academic.class.partials.js.error-validation')
    @include('school.academic.group.partials.js.error-validation')
    @include('school.academic.section.partials.js.error-validation')
    @include('school.academic.session.partials.js.modal-open')
    @include('school.academic.subject.partials.js.modal-open')
    @include('school.exam.exam_name.partials.js.modal-open')
    @include('school.academic.session.partials.js.modal-submit')
    @include('school.academic.subject.partials.js.modal-submit')
    @include('school.exam.exam_name.partials.js.modal-submit')
    @include('school.academic.session.partials.js.error-validation')
    @include('school.academic.subject.partials.js.error-validation')
    @include('school.exam.exam_name.partials.js.error-validation')

    <script>
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;

        let currentPage = 1;
        let currentFilterClass = '';
        let currentFilterGroup = '';
        let currentFilterSection = '';
        let currentFilterSession = '';
        let currentFilterExam = '';

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

        function loadSyllabusFilterGroupByClass(classId) {
            const params = classId ? { class_id: classId } : {};
            axios.get('/api/get-school-groups', { params }).then(res => {
                const data = res.data.data || [];
                populateDropdown('syllabusGroupFilterMenu', data, 'id', 'group_name');
                setDropdownValue('syllabusGroupFilter', '', 'Select Group');
                setDropdownValue('syllabusSectionFilter', '', 'Select Section');
                setDropdownValue('syllabusSessionFilter', '', 'Select Session');
                setDropdownValue('syllabusExamFilter', '', 'Select Exam');
                populateDropdown('syllabusSectionFilterMenu', [], 'id', 'section_name');
                populateDropdown('syllabusSessionFilterMenu', [], 'id', 'session_year');
                populateDropdown('syllabusExamFilterMenu', [], 'id', 'exam_name');
            }).catch(() => {});
        }

        function loadSyllabusFilterSectionByGroup(classId, groupId) {
            const params = {};
            if (classId) params.class_id = classId;
            if (groupId) params.group_id = groupId;
            axios.get('/api/get-school-sections', { params }).then(res => {
                const data = res.data.data || [];
                populateDropdown('syllabusSectionFilterMenu', data, 'id', 'section_name');
                setDropdownValue('syllabusSessionFilter', '', 'Select Session');
                setDropdownValue('syllabusExamFilter', '', 'Select Exam');
                populateDropdown('syllabusSessionFilterMenu', [], 'id', 'session_year');
                populateDropdown('syllabusExamFilterMenu', [], 'id', 'exam_name');
            }).catch(() => {});
        }

        function loadSyllabusFilterSessionBySection(classId, groupId, sectionId) {
            const params = {};
            if (classId) params.class_id = classId;
            if (groupId) params.group_id = groupId;
            if (sectionId) params.section_id = sectionId;
            axios.get('/api/get-school-sessions', { params }).then(res => {
                const data = res.data.data || [];
                populateDropdown('syllabusSessionFilterMenu', data, 'id', 'session_year');
                setDropdownValue('syllabusExamFilter', '', 'Select Exam');
                populateDropdown('syllabusExamFilterMenu', [], 'id', 'exam_name');
            }).catch(() => {});
        }

        function loadSyllabusFilterExamBySession(classId, groupId, sectionId, sessionId) {
            const params = {};
            if (classId) params.class_id = classId;
            if (groupId) params.group_id = groupId;
            if (sectionId) params.section_id = sectionId;
            if (sessionId) params.session_id = sessionId;
            axios.get('/api/get-school-exams', { params }).then(res => {
                const data = res.data.data || [];
                populateDropdown('syllabusExamFilterMenu', data, 'id', 'exam_name');
            }).catch(() => {});
        }

        function loadFilterOptions() {
            axios.get('/api/get-school-classes').then(res => {
                populateDropdown('syllabusClassFilterMenu', res.data.data || [], 'id', 'class_name');
            }).catch(() => {});
        }

        function fetchSyllabuses(page = 1) {
            currentPage = page;
            const search = document.getElementById('syllabusSearch')?.value || document.getElementById('syllabusSearchMobile')?.value || '';
            axios.get('/api/school-syllabuses', {
                params: {
                    search,
                    page,
                    class_id: currentFilterClass,
                    group_id: currentFilterGroup,
                    section_id: currentFilterSection,
                    session_id: currentFilterSession,
                    exam_id: currentFilterExam
                }
            }).then(res => {
                const meta = res.data;
                const tbody = document.getElementById('syllabusTableBody');
                tbody.innerHTML = '';
                if (!meta.data || meta.data.length === 0) {
                    tbody.innerHTML =
                        `<tr><td colspan="10" class="border border-gray-300 px-3 py-10 text-center text-gray-500">No syllabuses found.</td></tr>`;
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
                                <div class="donate-cell-scroll" title="${item.school_class ? item.school_class.class_name : '-'}">${item.school_class ? item.school_class.class_name : '-'}</div>
                            </td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${item.school_group ? item.school_group.group_name : '-'}</td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${item.school_section ? item.school_section.section_name : '-'}</td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${item.school_session ? item.school_session.session_year : '-'}</td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                <div class="donate-cell-scroll" title="${item.school_subject ? item.school_subject.subject_name : '-'}">${item.school_subject ? item.school_subject.subject_name : '-'}</div>
                            </td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${item.school_exam ? item.school_exam.exam_name : '-'}</td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${item.start_page || '-'}</td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${item.end_page || '-'}</td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                                <div class="flex h-6 w-full items-center justify-center -space-x-[3px]">
                                    <button type="button" title="Edit" onclick="editSyllabus(${item.id})" class="flex h-6 w-[14px] items-center justify-center text-gray-600 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 hover:bg-gray-100 hover:text-blue-600 focus-visible:ring-blue-500">
                                        <i class="far fa-edit text-xs" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" title="Delete" onclick="deleteSyllabus(${item.id})" class="flex h-6 w-[14px] items-center justify-center text-gray-600 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 hover:bg-gray-100 hover:text-red-600 focus-visible:ring-red-500">
                                        <i class="far fa-trash-alt text-xs" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>`;
                });
                renderPagination(meta);
            }).catch(err => console.error('Load Error:', err));
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
                const btn = document.createElement('button');
                btn.className = `pagination-btn ${meta.current_page === i ? 'active' : ''}`;
                btn.innerText = i;
                btn.onclick = () => fetchSyllabuses(i);
                controls.appendChild(btn);
            }
            const nextBtn = document.createElement('button');
            nextBtn.className = 'pagination-btn';
            nextBtn.innerHTML = '<i class="mdi mdi-chevron-right"></i>';
            nextBtn.disabled = meta.current_page === meta.last_page;
            nextBtn.onclick = () => fetchSyllabuses(meta.current_page + 1);
            controls.appendChild(nextBtn);
        }

        function deleteSyllabus(id) {
            Swal.fire({
                title: 'Delete Syllabus?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it'
            }).then(r => {
                if (r.isConfirmed) {
                    axios.delete('/api/school-syllabuses/' + id)
                        .then(() => {
                            Toastify({ text: 'Syllabus Deleted', style: { background: '#ef4444' } }).showToast();
                            fetchSyllabuses(currentPage);
                        })
                        .catch(() => Swal.fire('Error', 'Could not delete syllabus.', 'error'));
                }
            });
        }

        function editSyllabus(id) {
            axios.get('/api/school-syllabuses/' + id)
                .then(res => {
                    const item = res.data;
                    document.getElementById('record_id').value = item.id;
                    document.getElementById('start_page').value = item.start_page || '';
                    document.getElementById('end_page').value = item.end_page || '';

                    loadSyllabusClassSelect(item.class_id);
                    setTimeout(() => {
                        loadSyllabusGroupSelect(item.group_id);
                        setTimeout(() => {
                            loadSyllabusSectionSelect(item.section_id);
                            setTimeout(() => {
                                loadSyllabusSessionSelect(item.session_id);
                                setTimeout(() => {
                                    loadSyllabusExamSelect(item.exam_id);
                                }, 300);
                            }, 300);
                        }, 300);
                    }, 300);

                    loadSyllabusSubjectSelect(item.subject_id);

                    document.getElementById('syllabusModalTitle').innerText = 'Update Syllabus';
                    document.getElementById('syllabusModal').classList.remove('hidden');
                })
                .catch(() => Swal.fire('Error', 'Failed to load syllabus data.', 'error'));
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadFilterOptions();

            document.getElementById('syllabusClassFilter')?.addEventListener('change', function() {
                loadSyllabusFilterGroupByClass(this.value);
            });

            document.getElementById('syllabusGroupFilter')?.addEventListener('change', function() {
                const classInput = document.getElementById('syllabusClassFilter');
                const classId = classInput ? classInput.value : '';
                loadSyllabusFilterSectionByGroup(classId, this.value);
            });

            document.getElementById('syllabusSectionFilter')?.addEventListener('change', function() {
                const classInput = document.getElementById('syllabusClassFilter');
                const groupInput = document.getElementById('syllabusGroupFilter');
                const classId = classInput ? classInput.value : '';
                const groupId = groupInput ? groupInput.value : '';
                loadSyllabusFilterSessionBySection(classId, groupId, this.value);
            });

            document.getElementById('syllabusSessionFilter')?.addEventListener('change', function() {
                const classInput = document.getElementById('syllabusClassFilter');
                const groupInput = document.getElementById('syllabusGroupFilter');
                const sectionInput = document.getElementById('syllabusSectionFilter');
                const classId = classInput ? classInput.value : '';
                const groupId = groupInput ? groupInput.value : '';
                const sectionId = sectionInput ? sectionInput.value : '';
                loadSyllabusFilterExamBySession(classId, groupId, sectionId, this.value);
            });

            document.getElementById('syllabusFormClass')?.addEventListener('change', function() {
                setDropdownValue('syllabusFormGroup', '', 'Select Group');
                setDropdownValue('syllabusFormSection', '', 'Select Section');
                setDropdownValue('syllabusFormSession', '', 'Select Session');
                setDropdownValue('syllabusFormExam', '', 'Select Exam');
                setDropdownValue('syllabusFormSubject', '', 'Select Subject');
                populateDropdown('syllabusFormGroupMenu', [], 'id', 'group_name');
                populateDropdown('syllabusFormSectionMenu', [], 'id', 'section_name');
                populateDropdown('syllabusFormSessionMenu', [], 'id', 'session_year');
                populateDropdown('syllabusFormExamMenu', [], 'id', 'exam_name');
                populateDropdown('syllabusFormSubjectMenu', [], 'id', 'subject_name');
                if (this.value) {
                    loadSyllabusGroupSelect();
                    loadSyllabusSubjectSelect();
                }
            });

            document.getElementById('syllabusFormGroup')?.addEventListener('change', function() {
                setDropdownValue('syllabusFormSection', '', 'Select Section');
                setDropdownValue('syllabusFormSession', '', 'Select Session');
                setDropdownValue('syllabusFormExam', '', 'Select Exam');
                populateDropdown('syllabusFormSectionMenu', [], 'id', 'section_name');
                populateDropdown('syllabusFormSessionMenu', [], 'id', 'session_year');
                populateDropdown('syllabusFormExamMenu', [], 'id', 'exam_name');
                if (this.value) loadSyllabusSectionSelect();
            });

            document.getElementById('syllabusFormSection')?.addEventListener('change', function() {
                setDropdownValue('syllabusFormSession', '', 'Select Session');
                setDropdownValue('syllabusFormExam', '', 'Select Exam');
                populateDropdown('syllabusFormSessionMenu', [], 'id', 'session_year');
                populateDropdown('syllabusFormExamMenu', [], 'id', 'exam_name');
                if (this.value) loadSyllabusSessionSelect();
            });

            document.getElementById('syllabusFormSession')?.addEventListener('change', function() {
                setDropdownValue('syllabusFormExam', '', 'Select Exam');
                populateDropdown('syllabusFormExamMenu', [], 'id', 'exam_name');
                if (this.value) loadSyllabusExamSelect();
            });

            document.getElementById('syllabusSearch')?.addEventListener('input', () => fetchSyllabuses(1));
            document.getElementById('syllabusSearchMobile')?.addEventListener('input', () => fetchSyllabuses(1));

            document.getElementById('btnFilter')?.addEventListener('click', () => {
                document.getElementById('filterModal')?.classList.remove('hidden');
            });

            document.getElementById('resetFilter')?.addEventListener('click', () => {
                setDropdownValue('syllabusClassFilter', '', 'Select Class');
                setDropdownValue('syllabusGroupFilter', '', 'Select Group');
                setDropdownValue('syllabusSectionFilter', '', 'Select Section');
                setDropdownValue('syllabusSessionFilter', '', 'Select Session');
                setDropdownValue('syllabusExamFilter', '', 'Select Exam');
                populateDropdown('syllabusGroupFilterMenu', [], 'id', 'group_name');
                populateDropdown('syllabusSectionFilterMenu', [], 'id', 'section_name');
                populateDropdown('syllabusSessionFilterMenu', [], 'id', 'session_year');
                populateDropdown('syllabusExamFilterMenu', [], 'id', 'exam_name');
                currentFilterClass = '';
                currentFilterGroup = '';
                currentFilterSection = '';
                currentFilterSession = '';
                currentFilterExam = '';
                currentPage = 1;
                fetchSyllabuses(1);
                document.getElementById('filterModal')?.classList.add('hidden');
            });

            document.getElementById('applyFilter')?.addEventListener('click', () => {
                const classInput = document.getElementById('syllabusClassFilter');
                currentFilterClass = classInput ? classInput.value : '';
                const groupInput = document.getElementById('syllabusGroupFilter');
                currentFilterGroup = groupInput ? groupInput.value : '';
                const sectionInput = document.getElementById('syllabusSectionFilter');
                currentFilterSection = sectionInput ? sectionInput.value : '';
                const sessionInput = document.getElementById('syllabusSessionFilter');
                currentFilterSession = sessionInput ? sessionInput.value : '';
                const examInput = document.getElementById('syllabusExamFilter');
                currentFilterExam = examInput ? examInput.value : '';
                currentPage = 1;
                fetchSyllabuses(1);
                document.getElementById('filterModal')?.classList.add('hidden');
            });

            document.getElementById('btnRestoreDesktop')?.addEventListener('click', () => {
                document.getElementById('syllabusSearch').value = '';
                currentFilterClass = '';
                currentFilterGroup = '';
                currentFilterSection = '';
                currentFilterSession = '';
                currentFilterExam = '';
                currentPage = 1;
                fetchSyllabuses(1);
            });

            document.getElementById('btnRestoreMobile')?.addEventListener('click', () => {
                document.getElementById('syllabusSearchMobile').value = '';
                currentFilterClass = '';
                currentFilterGroup = '';
                currentFilterSection = '';
                currentFilterSession = '';
                currentFilterExam = '';
                currentPage = 1;
                fetchSyllabuses(1);
            });

            document.querySelectorAll('[role="dialog"]').forEach(dialog => {
                dialog.addEventListener('click', function(e) {
                    if (e.target === this) this.classList.add('hidden');
                });
            });

            document.addEventListener('click', function(e) {
                const btn = e.target.closest('[data-dropdown-add-target]');
                if (btn) {
                    const map = { classModal: 'Add Class', groupModal: 'Add Group', sectionModal: 'Add Section', sessionModal: 'Add Session', subjectModal: 'Add Subject', examModal: 'Add Exam' };
                    const title = map[btn.dataset.dropdownAddTarget];
                    if (title) {
                        const el = document.getElementById(btn.dataset.dropdownAddTarget + 'Title');
                        if (el) el.innerText = title;
                    }
                }
            });
        });

        fetchSyllabuses();
    </script>
@endsection
