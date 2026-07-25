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
            @include('school.academic.subject.partials.header')
            @include('school.academic.subject.partials.table')
        </div>
    </div>

    <x-modal.form
        id="filterModal"
        form-id="subjectFilterForm"
        title="Subject Filter"
        close-button-id="resetFilter"
        action="#"
        method="GET"
        :enctype="null"
        class="subject-filter-modal"
        panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[288px] overflow-visible border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] md:max-w-[480px]"
    >
        <x-input.dropdown-select
            id="subjectClassFilter"
            name="class_id"
            placeholder="Select Class"
            :value="request('class_id')"
            :options="[]"
            add-button-id="openClassFromSubjectFilter"
            add-button-label="Add class"
            add-button-target="classModal"
        />
        <x-input.dropdown-select
            id="subjectGroupFilter"
            name="group_id"
            placeholder="Select Group"
            :value="request('group_id')"
            :options="[]"
            add-button-id="openGroupFromSubjectFilter"
            add-button-label="Add group"
            add-button-target="groupModal"
        />
        <x-input.dropdown-select
            id="subjectSectionFilter"
            name="section_id"
            placeholder="Select Section"
            :value="request('section_id')"
            :options="[]"
            add-button-id="openSectionFromSubjectFilter"
            add-button-label="Add section"
            add-button-target="sectionModal"
        />

        <x-slot:footer>
            <div class="grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 py-3">
                <x-button.secondary id="resetFilter" type="button" class="w-full">Reset</x-button.secondary>
                <x-button.primary id="applyFilter" type="button" class="w-full">Apply</x-button.primary>
            </div>
        </x-slot:footer>
    </x-modal.form>

    @include('school.academic.subject.partials.subject-modal')
    @include('school.partials.export-dropdown')
    @include('school.academic.class.partials.class-modal')
    @include('school.academic.group.partials.group-modal')
    @include('school.academic.section.partials.section-modal')

    @include('school.academic.subject.partials.js.modal-open')
    @include('school.academic.subject.partials.js.modal-submit')
    @include('school.academic.subject.partials.js.error-validation')
    @include('school.academic.class.partials.js.modal-open')
    @include('school.academic.group.partials.js.modal-open')
    @include('school.academic.section.partials.js.modal-open')
    @include('school.academic.class.partials.js.modal-submit')
    @include('school.academic.group.partials.js.modal-submit')
    @include('school.academic.section.partials.js.modal-submit')
    @include('school.academic.class.partials.js.error-validation')
    @include('school.academic.group.partials.js.error-validation')
    @include('school.academic.section.partials.js.error-validation')

    <script>
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;

        let currentPage = 1;
        let currentFilterClass = '';
        let currentFilterGroup = '';
        let currentFilterSection = '';

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

        function loadSubjectFilterGroupByClass(classId) {
            const params = classId ? { class_id: classId } : {};
            axios.get('/api/get-school-groups', { params }).then(res => {
                const data = res.data.data || [];
                populateDropdown('subjectGroupFilterMenu', data, 'id', 'group_name');
                setDropdownValue('subjectGroupFilter', '', 'Select Group');
                setDropdownValue('subjectSectionFilter', '', 'Select Section');
                populateDropdown('subjectSectionFilterMenu', [], 'id', 'section_name');
            }).catch(() => {});
        }

        function loadSubjectFilterSectionByGroup(classId, groupId) {
            const params = {};
            if (classId) params.class_id = classId;
            if (groupId) params.group_id = groupId;
            axios.get('/api/get-school-sections', { params }).then(res => {
                const data = res.data.data || [];
                populateDropdown('subjectSectionFilterMenu', data, 'id', 'section_name');
            }).catch(() => {});
        }

        function loadFilterOptions() {
            axios.get('/api/get-school-classes').then(res => {
                populateDropdown('subjectClassFilterMenu', res.data.data || [], 'id', 'class_name');
            }).catch(() => {});
        }

        function fetchSubjects(page = 1) {
            currentPage = page;
            const search = document.getElementById('subjectSearch')?.value || document.getElementById('subjectSearchMobile')?.value || '';
            axios.get('/api/school-subjects', { params: { search, page, class_id: currentFilterClass, group_id: currentFilterGroup, section_id: currentFilterSection } })
                .then(res => {
                    const meta = res.data;
                    const tbody = document.getElementById('subjectTableBody');
                    tbody.innerHTML = '';
                    if (!meta.data || meta.data.length === 0) {
                        tbody.innerHTML =
                            `<tr><td colspan="9" class="border border-gray-300 px-3 py-10 text-center text-gray-500">No subjects found.</td></tr>`;
                        document.getElementById('paginationInfo').innerText = '0 of 0';
                        document.getElementById('paginationControls').innerHTML = '';
                        return;
                    }
                    meta.data.forEach((item, i) => {
                        const sl = meta.from ? meta.from + i : i + 1;
                        const marks = item.marks || {};
                        const tutorial = marks.tutorial_mark || 0;
                        const mcq = marks.mcq_mark || 0;
                        const writing = marks.writing_mark || 0;
                        const practical = marks.practical_mark || 0;
                        const total = marks.total_mark || (tutorial + mcq + writing + practical);
                        const failMark = item.fail_mark ?? '-';
                        const marksDetail = [];
                        if (tutorial) marksDetail.push('T: ' + tutorial);
                        if (mcq) marksDetail.push('MCQ: ' + mcq);
                        if (writing) marksDetail.push('W: ' + writing);
                        if (practical) marksDetail.push('P: ' + practical);
                        const gradeLabel = item.grade_type ? item.grade_type.full_mark + ' Mark' : '-';
                        tbody.innerHTML += `
                            <tr class="hover:bg-gray-50">
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">${sl}</td>
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                    <div class="donate-cell-scroll" title="${item.subject_name || '-'}">${item.subject_name || '-'}</div>
                                </td>
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                    <div class="donate-cell-scroll" title="${item.school_class ? item.school_class.class_name : '-'}">${item.school_class ? item.school_class.class_name : '-'}</div>
                                </td>
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${item.subject_code || '-'}</td>
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${gradeLabel}</td>
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3 font-semibold">${total}</td>
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${failMark}</td>
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${marksDetail.length ? marksDetail.join(', ') : '-'}</td>
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                                    <div class="flex h-6 w-full items-center justify-center -space-x-[3px]">
                                        <button type="button" title="Edit" onclick="editSubject(${item.id})" class="flex h-6 w-[14px] items-center justify-center text-gray-600 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 hover:bg-gray-100 hover:text-blue-600 focus-visible:ring-blue-500">
                                            <i class="far fa-edit text-xs" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" title="Delete" onclick="deleteSubject(${item.id})" class="flex h-6 w-[14px] items-center justify-center text-gray-600 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 hover:bg-gray-100 hover:text-red-600 focus-visible:ring-red-500">
                                            <i class="far fa-trash-alt text-xs" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>`;
                    });
                    renderPagination(meta);
                })
                .catch(err => console.error('Load Error:', err));
        }

        function renderPagination(meta) {
            const controls = document.getElementById('paginationControls');
            document.getElementById('paginationInfo').innerText = `${meta.to || 0} of ${meta.total}`;
            controls.innerHTML = '';
            const prevBtn = document.createElement('button');
            prevBtn.className = 'pagination-btn';
            prevBtn.innerHTML = '<i class="mdi mdi-chevron-left"></i>';
            prevBtn.disabled = meta.current_page === 1;
            prevBtn.onclick = () => fetchSubjects(meta.current_page - 1);
            controls.appendChild(prevBtn);
            for (let i = 1; i <= meta.last_page; i++) {
                const btn = document.createElement('button');
                btn.className = `pagination-btn ${meta.current_page === i ? 'active' : ''}`;
                btn.innerText = i;
                btn.onclick = () => fetchSubjects(i);
                controls.appendChild(btn);
            }
            const nextBtn = document.createElement('button');
            nextBtn.className = 'pagination-btn';
            nextBtn.innerHTML = '<i class="mdi mdi-chevron-right"></i>';
            nextBtn.disabled = meta.current_page === meta.last_page;
            nextBtn.onclick = () => fetchSubjects(meta.current_page + 1);
            controls.appendChild(nextBtn);
        }

        function deleteSubject(id) {
            Swal.fire({
                title: 'Delete Subject?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it'
            }).then(r => {
                if (r.isConfirmed) {
                    axios.delete('/api/school-subjects/' + id)
                        .then(() => {
                            Toastify({ text: 'Subject Deleted', style: { background: '#ef4444' } }).showToast();
                            fetchSubjects(currentPage);
                        })
                        .catch(() => Swal.fire('Error', 'Could not delete subject.', 'error'));
                }
            });
        }

        function editSubject(id) {
            axios.get('/api/school-subjects/' + id)
                .then(res => {
                    const item = res.data;
                    document.getElementById('record_id').value = item.id;
                    document.getElementById('subject_name').value = item.subject_name || '';
                    document.getElementById('subject_code').value = item.subject_code || '';
                    const marks = item.marks || {};
                    document.getElementById('tutorial_mark').value = marks.tutorial_mark || 0;
                    document.getElementById('mcq_mark').value = marks.mcq_mark || 0;
                    document.getElementById('writing_mark').value = marks.writing_mark || 0;
                    document.getElementById('practical_mark').value = marks.practical_mark || 0;
                    calculateTotalMark();
                    document.getElementById('fail_mark').value = item.fail_mark ?? 0;

                    loadSubjectClassSelect(item.class_id);
                    setTimeout(() => {
                        loadSubjectGroupSelect(item.group_id);
                        setTimeout(() => {
                            loadSubjectSectionSelect(item.section_id);
                        }, 300);
                        loadSubjectGradeSelect(item.grade_id);
                    }, 300);

                    document.getElementById('subjectModalTitle').innerText = 'Update Subject';
                    document.getElementById('subjectModal').classList.remove('hidden');
                })
                .catch(() => Swal.fire('Error', 'Failed to load subject data.', 'error'));
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadFilterOptions();

            document.getElementById('subjectClassFilter')?.addEventListener('change', function() {
                loadSubjectFilterGroupByClass(this.value);
            });

            document.getElementById('subjectGroupFilter')?.addEventListener('change', function() {
                const classInput = document.getElementById('subjectClassFilter');
                const classId = classInput ? classInput.value : '';
                loadSubjectFilterSectionByGroup(classId, this.value);
            });

            document.getElementById('subjectFormClass')?.addEventListener('change', function() {
                setDropdownValue('subjectFormGroup', '', 'Select Group');
                setDropdownValue('subjectFormSection', '', 'Select Section');
                populateDropdown('subjectFormGroupMenu', [], 'id', 'group_name');
                populateDropdown('subjectFormSectionMenu', [], 'id', 'section_name');
                if (this.value) loadSubjectGroupSelect();
            });

            document.getElementById('subjectFormGroup')?.addEventListener('change', function() {
                setDropdownValue('subjectFormSection', '', 'Select Section');
                populateDropdown('subjectFormSectionMenu', [], 'id', 'section_name');
                if (this.value) loadSubjectSectionSelect();
            });

            document.getElementById('subjectFormGrade')?.addEventListener('change', function() {
                toggleMarkFields();
                updateMaxAllowedMark();
            });

            document.getElementById('subjectSearch')?.addEventListener('input', () => fetchSubjects(1));
            document.getElementById('subjectSearchMobile')?.addEventListener('input', () => fetchSubjects(1));

            document.getElementById('btnFilter')?.addEventListener('click', () => {
                document.getElementById('filterModal')?.classList.remove('hidden');
            });

            document.getElementById('resetFilter')?.addEventListener('click', () => {
                setDropdownValue('subjectClassFilter', '', 'Select Class');
                setDropdownValue('subjectGroupFilter', '', 'Select Group');
                setDropdownValue('subjectSectionFilter', '', 'Select Section');
                populateDropdown('subjectGroupFilterMenu', [], 'id', 'group_name');
                populateDropdown('subjectSectionFilterMenu', [], 'id', 'section_name');
                currentFilterClass = '';
                currentFilterGroup = '';
                currentFilterSection = '';
                currentPage = 1;
                fetchSubjects(1);
                document.getElementById('filterModal')?.classList.add('hidden');
            });

            document.getElementById('applyFilter')?.addEventListener('click', () => {
                const classInput = document.getElementById('subjectClassFilter');
                currentFilterClass = classInput ? classInput.value : '';
                const groupInput = document.getElementById('subjectGroupFilter');
                currentFilterGroup = groupInput ? groupInput.value : '';
                const sectionInput = document.getElementById('subjectSectionFilter');
                currentFilterSection = sectionInput ? sectionInput.value : '';
                currentPage = 1;
                fetchSubjects(1);
                document.getElementById('filterModal')?.classList.add('hidden');
            });

            document.getElementById('btnRestoreDesktop')?.addEventListener('click', () => {
                document.getElementById('subjectSearch').value = '';
                currentFilterClass = '';
                currentFilterGroup = '';
                currentFilterSection = '';
                currentPage = 1;
                fetchSubjects(1);
            });

            document.getElementById('btnRestoreMobile')?.addEventListener('click', () => {
                document.getElementById('subjectSearchMobile').value = '';
                currentFilterClass = '';
                currentFilterGroup = '';
                currentFilterSection = '';
                currentPage = 1;
                fetchSubjects(1);
            });

            document.querySelectorAll('[role="dialog"]').forEach(dialog => {
                dialog.addEventListener('click', function(e) {
                    if (e.target === this) this.classList.add('hidden');
                });
            });

            document.addEventListener('click', function(e) {
                const btn = e.target.closest('[data-dropdown-add-target]');
                if (btn) {
                    const map = { classModal: 'Add Class', groupModal: 'Add Group', sectionModal: 'Add Section' };
                    const title = map[btn.dataset.dropdownAddTarget];
                    if (title) {
                        const el = document.getElementById(btn.dataset.dropdownAddTarget + 'Title');
                        if (el) el.innerText = title;
                    }
                }
            });
        });

        fetchSubjects();
    </script>
@endsection
