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
            @include('school.academic.routine.partials.header')
            @include('school.academic.routine.partials.table')
        </div>
    </div>

    <x-modal.form
        id="filterModal"
        form-id="routineFilterForm"
        title="Routine Filter"
        close-button-id="resetFilter"
        action="#"
        method="GET"
        :enctype="null"
        class="routine-filter-modal"
        panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[288px] overflow-visible border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] md:max-w-[480px]"
    >
        <x-input.dropdown-select
            id="routineClassFilter"
            name="class_id"
            placeholder="Select Class"
            :value="request('class_id')"
            :options="[]"
            add-button-id="openClassFromRoutineFilter"
            add-button-label="Add class"
            add-button-target="classModal"
        />
        <x-input.dropdown-select
            id="routineGroupFilter"
            name="group_id"
            placeholder="Select Group"
            :value="request('group_id')"
            :options="[]"
            add-button-id="openGroupFromRoutineFilter"
            add-button-label="Add group"
            add-button-target="groupModal"
        />
        <x-input.dropdown-select
            id="routineSectionFilter"
            name="section_id"
            placeholder="Select Section"
            :value="request('section_id')"
            :options="[]"
            add-button-id="openSectionFromRoutineFilter"
            add-button-label="Add section"
            add-button-target="sectionModal"
        />
        <x-input.dropdown-select
            id="routineSubjectFilter"
            name="subject_id"
            placeholder="Select Subject"
            :value="request('subject_id')"
            :options="[]"
            add-button-id="openSubjectFromRoutineFilter"
            add-button-label="Add subject"
            add-button-target="subjectModal"
        />

        <x-slot:footer>
            <div class="grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 py-3">
                <x-button.secondary id="resetFilter" type="button" class="w-full">Reset</x-button.secondary>
                <x-button.primary id="applyFilter" type="button" class="w-full">Apply</x-button.primary>
            </div>
        </x-slot:footer>
    </x-modal.form>

    @include('school.academic.routine.partials.routine-modal')
    @include('school.partials.export-dropdown')
    @include('school.academic.class.partials.class-modal')
    @include('school.academic.group.partials.group-modal')
    @include('school.academic.section.partials.section-modal')
    @include('school.academic.subject.partials.subject-modal')

    @include('school.academic.routine.partials.js.modal-open')
    @include('school.academic.routine.partials.js.modal-submit')
    @include('school.academic.routine.partials.js.error-validation')
    @include('school.academic.class.partials.js.modal-open')
    @include('school.academic.group.partials.js.modal-open')
    @include('school.academic.section.partials.js.modal-open')
    @include('school.academic.class.partials.js.modal-submit')
    @include('school.academic.group.partials.js.modal-submit')
    @include('school.academic.section.partials.js.modal-submit')
    @include('school.academic.class.partials.js.error-validation')
    @include('school.academic.group.partials.js.error-validation')
    @include('school.academic.section.partials.js.error-validation')
    @include('school.academic.subject.partials.js.modal-open')
    @include('school.academic.subject.partials.js.modal-submit')
    @include('school.academic.subject.partials.js.error-validation')

    <script>
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;

        let currentPage = 1;
        let currentFilterClass = '';
        let currentFilterGroup = '';
        let currentFilterSection = '';
        let currentFilterSubject = '';

        function formatTime(timeString) {
            if (!timeString) return '-';
            const [hourString, minute] = timeString.split(":");
            const hour = +hourString % 24;
            return (hour % 12 || 12) + ":" + minute + (hour < 12 ? " AM" : " PM");
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

        function loadRoutineFilterGroupByClass(classId) {
            const params = classId ? { class_id: classId } : {};
            axios.get('/api/get-school-groups', { params }).then(res => {
                const data = res.data.data || [];
                populateDropdown('routineGroupFilterMenu', data, 'id', 'group_name');
                setDropdownValue('routineGroupFilter', '', 'Select Group');
                setDropdownValue('routineSectionFilter', '', 'Select Section');
                populateDropdown('routineSectionFilterMenu', [], 'id', 'section_name');
                setDropdownValue('routineSubjectFilter', '', 'Select Subject');
                populateDropdown('routineSubjectFilterMenu', [], 'id', 'subject_name');
            }).catch(() => {});
        }

        function loadRoutineFilterSectionByGroup(classId, groupId) {
            const params = {};
            if (classId) params.class_id = classId;
            if (groupId) params.group_id = groupId;
            axios.get('/api/get-school-sections', { params }).then(res => {
                const data = res.data.data || [];
                populateDropdown('routineSectionFilterMenu', data, 'id', 'section_name');
                setDropdownValue('routineSubjectFilter', '', 'Select Subject');
                populateDropdown('routineSubjectFilterMenu', [], 'id', 'subject_name');
            }).catch(() => {});
        }

        function loadRoutineFilterSubjectBySection(classId, groupId, sectionId) {
            const params = {};
            if (classId) params.class_id = classId;
            if (groupId) params.group_id = groupId;
            if (sectionId) params.section_id = sectionId;
            axios.get('/api/get-school-subjects', { params }).then(res => {
                const data = res.data.data || [];
                populateDropdown('routineSubjectFilterMenu', data, 'id', 'subject_name');
            }).catch(() => {});
        }

        function loadFilterOptions() {
            axios.get('/api/get-school-classes').then(res => {
                populateDropdown('routineClassFilterMenu', res.data.data || [], 'id', 'class_name');
            }).catch(() => {});
        }

        function fetchRoutines(page = 1) {
            currentPage = page;
            const search = document.getElementById('routineSearch')?.value || document.getElementById('routineSearchMobile')?.value || '';
            axios.get('/api/school-routines', {
                params: {
                    search,
                    page,
                    class_id: currentFilterClass,
                    group_id: currentFilterGroup,
                    section_id: currentFilterSection,
                    subject_id: currentFilterSubject
                }
            }).then(res => {
                const meta = res.data;
                const tbody = document.getElementById('routineTableBody');
                tbody.innerHTML = '';
                if (!meta.data || meta.data.length === 0) {
                    tbody.innerHTML =
                        `<tr><td colspan="10" class="border border-gray-300 px-3 py-10 text-center text-gray-500">No routines found.</td></tr>`;
                    document.getElementById('paginationInfo').innerText = '0 of 0';
                    document.getElementById('paginationControls').innerHTML = '';
                    return;
                }
                meta.data.forEach((item, i) => {
                    const sl = meta.from ? meta.from + i : i + 1;
                    tbody.innerHTML += `
                        <tr class="hover:bg-gray-50">
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">${sl}</td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-blue-600">${item.day_name || '-'}</td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                <div class="donate-cell-scroll" title="${item.school_class ? item.school_class.class_name : '-'}">${item.school_class ? item.school_class.class_name : '-'}</div>
                            </td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${item.school_group ? item.school_group.group_name : '-'}</td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${item.school_section ? item.school_section.section_name : '-'}</td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                <div class="donate-cell-scroll" title="${item.school_subject ? item.school_subject.subject_name : '-'}">${item.school_subject ? item.school_subject.subject_name : '-'}</div>
                            </td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${item.teacher ? item.teacher.name : '-'}</td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-blue-500">${formatTime(item.start_time)}</td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-blue-500">${formatTime(item.end_time)}</td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                                <div class="flex h-6 w-full items-center justify-center -space-x-[3px]">
                                    <button type="button" title="Edit" onclick="editRoutine(${item.id})" class="flex h-6 w-[14px] items-center justify-center text-gray-600 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 hover:bg-gray-100 hover:text-blue-600 focus-visible:ring-blue-500">
                                        <i class="far fa-edit text-xs" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" title="Delete" onclick="deleteRoutine(${item.id})" class="flex h-6 w-[14px] items-center justify-center text-gray-600 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 hover:bg-gray-100 hover:text-red-600 focus-visible:ring-red-500">
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
            prevBtn.onclick = () => fetchRoutines(meta.current_page - 1);
            controls.appendChild(prevBtn);
            for (let i = 1; i <= meta.last_page; i++) {
                const btn = document.createElement('button');
                btn.className = `pagination-btn ${meta.current_page === i ? 'active' : ''}`;
                btn.innerText = i;
                btn.onclick = () => fetchRoutines(i);
                controls.appendChild(btn);
            }
            const nextBtn = document.createElement('button');
            nextBtn.className = 'pagination-btn';
            nextBtn.innerHTML = '<i class="mdi mdi-chevron-right"></i>';
            nextBtn.disabled = meta.current_page === meta.last_page;
            nextBtn.onclick = () => fetchRoutines(meta.current_page + 1);
            controls.appendChild(nextBtn);
        }

        function deleteRoutine(id) {
            Swal.fire({
                title: 'Delete Routine?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it'
            }).then(r => {
                if (r.isConfirmed) {
                    axios.delete('/api/school-routines/' + id)
                        .then(() => {
                            Toastify({ text: 'Routine Deleted', style: { background: '#ef4444' } }).showToast();
                            fetchRoutines(currentPage);
                        })
                        .catch(() => Swal.fire('Error', 'Could not delete routine.', 'error'));
                }
            });
        }

        function editRoutine(id) {
            axios.get('/api/school-routines/' + id)
                .then(res => {
                    const item = res.data;
                    document.getElementById('record_id').value = item.id;
                    document.getElementById('start_time').value = item.start_time ? item.start_time.substring(0, 5) : '';
                    document.getElementById('end_time').value = item.end_time ? item.end_time.substring(0, 5) : '';

                    loadRoutineDaySelect(item.day_name);
                    loadRoutineTeacherSelect(item.teacher_id);
                    loadRoutineClassSelect(item.class_id);
                    setTimeout(() => {
                        loadRoutineGroupSelect(item.group_id);
                        setTimeout(() => {
                            loadRoutineSectionSelect(item.section_id);
                            setTimeout(() => {
                                loadRoutineSubjectSelect(item.subject_id);
                            }, 300);
                        }, 300);
                    }, 300);

                    document.getElementById('routineModalTitle').innerText = 'Update Routine';
                    document.getElementById('routineModal').classList.remove('hidden');
                })
                .catch(() => Swal.fire('Error', 'Failed to load routine data.', 'error'));
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadFilterOptions();

            document.getElementById('routineClassFilter')?.addEventListener('change', function() {
                loadRoutineFilterGroupByClass(this.value);
            });

            document.getElementById('routineGroupFilter')?.addEventListener('change', function() {
                const classInput = document.getElementById('routineClassFilter');
                const classId = classInput ? classInput.value : '';
                loadRoutineFilterSectionByGroup(classId, this.value);
            });

            document.getElementById('routineSectionFilter')?.addEventListener('change', function() {
                const classInput = document.getElementById('routineClassFilter');
                const classId = classInput ? classInput.value : '';
                const groupInput = document.getElementById('routineGroupFilter');
                const groupId = groupInput ? groupInput.value : '';
                loadRoutineFilterSubjectBySection(classId, groupId, this.value);
            });

            document.getElementById('routineFormClass')?.addEventListener('change', function() {
                setDropdownValue('routineFormGroup', '', 'Select Group');
                setDropdownValue('routineFormSection', '', 'Select Section');
                setDropdownValue('routineFormSubject', '', 'Select Subject');
                populateDropdown('routineFormGroupMenu', [], 'id', 'group_name');
                populateDropdown('routineFormSectionMenu', [], 'id', 'section_name');
                populateDropdown('routineFormSubjectMenu', [], 'id', 'subject_name');
                if (this.value) {
                    loadRoutineGroupSelect();
                }
            });

            document.getElementById('routineFormGroup')?.addEventListener('change', function() {
                setDropdownValue('routineFormSection', '', 'Select Section');
                setDropdownValue('routineFormSubject', '', 'Select Subject');
                populateDropdown('routineFormSectionMenu', [], 'id', 'section_name');
                populateDropdown('routineFormSubjectMenu', [], 'id', 'subject_name');
                if (this.value) loadRoutineSectionSelect();
            });

            document.getElementById('routineFormSection')?.addEventListener('change', function() {
                setDropdownValue('routineFormSubject', '', 'Select Subject');
                populateDropdown('routineFormSubjectMenu', [], 'id', 'subject_name');
                if (this.value) loadRoutineSubjectSelect();
            });

            document.getElementById('routineSearch')?.addEventListener('input', () => fetchRoutines(1));
            document.getElementById('routineSearchMobile')?.addEventListener('input', () => fetchRoutines(1));

            document.getElementById('btnFilter')?.addEventListener('click', () => {
                document.getElementById('filterModal')?.classList.remove('hidden');
            });

            document.getElementById('resetFilter')?.addEventListener('click', () => {
                setDropdownValue('routineClassFilter', '', 'Select Class');
                setDropdownValue('routineGroupFilter', '', 'Select Group');
                setDropdownValue('routineSectionFilter', '', 'Select Section');
                setDropdownValue('routineSubjectFilter', '', 'Select Subject');
                populateDropdown('routineGroupFilterMenu', [], 'id', 'group_name');
                populateDropdown('routineSectionFilterMenu', [], 'id', 'section_name');
                populateDropdown('routineSubjectFilterMenu', [], 'id', 'subject_name');
                currentFilterClass = '';
                currentFilterGroup = '';
                currentFilterSection = '';
                currentFilterSubject = '';
                currentPage = 1;
                fetchRoutines(1);
                document.getElementById('filterModal')?.classList.add('hidden');
            });

            document.getElementById('applyFilter')?.addEventListener('click', () => {
                const classInput = document.getElementById('routineClassFilter');
                currentFilterClass = classInput ? classInput.value : '';
                const groupInput = document.getElementById('routineGroupFilter');
                currentFilterGroup = groupInput ? groupInput.value : '';
                const sectionInput = document.getElementById('routineSectionFilter');
                currentFilterSection = sectionInput ? sectionInput.value : '';
                const subjectInput = document.getElementById('routineSubjectFilter');
                currentFilterSubject = subjectInput ? subjectInput.value : '';
                currentPage = 1;
                fetchRoutines(1);
                document.getElementById('filterModal')?.classList.add('hidden');
            });

            document.getElementById('btnRestoreDesktop')?.addEventListener('click', () => {
                document.getElementById('routineSearch').value = '';
                currentFilterClass = '';
                currentFilterGroup = '';
                currentFilterSection = '';
                currentFilterSubject = '';
                currentPage = 1;
                fetchRoutines(1);
            });

            document.getElementById('btnRestoreMobile')?.addEventListener('click', () => {
                document.getElementById('routineSearchMobile').value = '';
                currentFilterClass = '';
                currentFilterGroup = '';
                currentFilterSection = '';
                currentFilterSubject = '';
                currentPage = 1;
                fetchRoutines(1);
            });

            document.querySelectorAll('[role="dialog"]').forEach(dialog => {
                dialog.addEventListener('click', function(e) {
                    if (e.target === this) this.classList.add('hidden');
                });
            });

            document.addEventListener('click', function(e) {
                const btn = e.target.closest('[data-dropdown-add-target]');
                if (btn) {
                    const map = { classModal: 'Add Class', groupModal: 'Add Group', sectionModal: 'Add Section', subjectModal: 'Add Subject' };
                    const title = map[btn.dataset.dropdownAddTarget];
                    if (title) {
                        const el = document.getElementById(btn.dataset.dropdownAddTarget + 'Title');
                        if (el) el.innerText = title;
                    }
                }
            });
        });

        fetchRoutines();
    </script>
@endsection
