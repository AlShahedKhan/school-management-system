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
            @include('school.academic.session.partials.header')
            @include('school.academic.session.partials.table')
        </div>
    </div>

    <x-modal.form
        id="filterModal"
        form-id="sessionFilterForm"
        title="Session Filter"
        close-button-id="resetFilter"
        action="#"
        method="GET"
        :enctype="null"
        class="session-filter-modal"
        panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[288px] overflow-visible border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] md:max-w-[480px]"
    >
        <x-input.dropdown-select
            id="sessionClassFilter"
            name="class_id"
            placeholder="Select Class"
            :value="request('class_id')"
            :options="[]"
            add-button-id="openClassFromSessionFilter"
            add-button-label="Add class"
            add-button-target="classModal"
        />
        <x-input.dropdown-select
            id="sessionGroupFilter"
            name="group_id"
            placeholder="Select Group"
            :value="request('group_id')"
            :options="[]"
            add-button-id="openGroupFromSessionFilter"
            add-button-label="Add group"
            add-button-target="groupModal"
        />
        <x-input.dropdown-select
            id="sessionSectionFilter"
            name="section_id"
            placeholder="Select Section"
            :value="request('section_id')"
            :options="[]"
            add-button-id="openSectionFromSessionFilter"
            add-button-label="Add section"
            add-button-target="sectionModal"
        />
        <x-input.dropdown-select
            id="sessionYearFilter"
            name="session_year"
            placeholder="Select Session Year"
            :value="request('session_year')"
            :options="[]"
            add-button-id="openSessionFromSessionFilter"
            add-button-label="Add Session"
            add-button-target="sessionModal"
        />

        <x-slot:footer>
            <div class="grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 py-3">
                <x-button.secondary id="resetFilter" type="button" class="w-full">Reset</x-button.secondary>
                <x-button.primary id="applyFilter" type="button" class="w-full">Apply</x-button.primary>
            </div>
        </x-slot:footer>
    </x-modal.form>

    @include('school.academic.class.partials.class-modal')
    @include('school.academic.group.partials.group-modal')
    @include('school.academic.section.partials.section-modal')
    @include('school.academic.session.partials.session-modal')
    @include('school.academic.session.partials.js.modal-open')
    @include('school.academic.class.partials.js.modal-open')
    @include('school.academic.group.partials.js.modal-open')
    @include('school.academic.section.partials.js.modal-open')
    @include('school.academic.session.partials.js.error-validation')
    @include('school.academic.class.partials.js.error-validation')
    @include('school.academic.group.partials.js.error-validation')
    @include('school.academic.section.partials.js.error-validation')
    @include('school.academic.session.partials.js.modal-submit')
    @include('school.academic.class.partials.js.modal-submit')
    @include('school.academic.group.partials.js.modal-submit')
    @include('school.academic.section.partials.js.modal-submit')
    @include('school.partials.export-dropdown')

    <script>
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;

        let currentPage = 1;
        let currentFilterClass = '';
        let currentFilterGroup = '';
        let currentFilterSection = '';
        let currentFilterSession = '';

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

        async function loadSessionGroupFilterByClass(classId) {
            try {
                const params = classId ? { class_id: classId } : {};
                const groupRes = await axios.get('/api/get-school-groups', { params });
                const groups = groupRes.data.data || [];
                const uniqueGroups = [];
                const seen = new Set();
                groups.forEach(g => {
                    if (!seen.has(g.group_name)) {
                        seen.add(g.group_name);
                        uniqueGroups.push(g);
                    }
                });
                setDropdownValue('sessionGroupFilter', '', 'Select Group');
                populateDropdown('sessionGroupFilterMenu', uniqueGroups, 'id', 'group_name');
                setDropdownValue('sessionSectionFilter', '', 'Select Section');
                populateDropdown('sessionSectionFilterMenu', [], 'id', 'section_name');
                setDropdownValue('sessionYearFilter', '', 'Select Session Year');
                populateDropdown('sessionYearFilterMenu', [], 'id', 'session_year');
            } catch (err) {
                console.error('Failed to load group filter options:', err);
            }
        }

        async function loadSessionSectionFilterByGroup(classId, groupId) {
            try {
                const params = {};
                if (classId) params.class_id = classId;
                if (groupId) params.group_id = groupId;
                const sectionRes = await axios.get('/api/get-school-sections', { params });
                const sections = sectionRes.data.data || [];
                const uniqueSections = [];
                const seen = new Set();
                sections.forEach(s => {
                    if (!seen.has(s.section_name)) {
                        seen.add(s.section_name);
                        uniqueSections.push(s);
                    }
                });
                populateDropdown('sessionSectionFilterMenu', uniqueSections, 'id', 'section_name');
                setDropdownValue('sessionYearFilter', '', 'Select Session Year');
                populateDropdown('sessionYearFilterMenu', [], 'id', 'session_year');
            } catch (err) {
                console.error('Failed to load section filter options:', err);
            }
        }

        async function loadSessionYearFilter(classId, groupId, sectionId) {
            try {
                const params = {};
                if (classId) params.class_id = classId;
                if (groupId) params.group_id = groupId;
                if (sectionId) params.section_id = sectionId;
                const sessionRes = await axios.get('/api/school-sessions', { params });
                const sessions = sessionRes.data.data || [];
                const uniqueYears = [...new Set(sessions.map(s => s.session_year).filter(Boolean))];
                const yearItems = uniqueYears.map(y => ({ id: y, session_year: y }));
                populateDropdown('sessionYearFilterMenu', yearItems, 'id', 'session_year');
            } catch (err) {
                console.error('Failed to load session year filter options:', err);
            }
        }

        async function loadFilterOptions() {
            try {
                const classRes = await axios.get('/api/get-school-classes');
                populateDropdown('sessionClassFilterMenu', classRes.data.data || [], 'id', 'class_name');
            } catch (err) {
                console.error('Failed to load filter options:', err);
            }
        }

        function loadSessionClassSelect(selectedId = null) {
            axios.get('/api/get-school-classes').then(res => {
                const data = res.data.data || [];
                populateDropdown('sessionFormClassMenu', data, 'id', 'class_name');
                if (selectedId) {
                    const item = data.find(c => String(c.id) === String(selectedId));
                    if (item) setDropdownValue('sessionFormClass', item.id, item.class_name);
                }
            }).catch(err => console.error("Class dropdown error:", err));
        }

        function loadSessionGroupSelect(selectedId = null) {
            const classId = document.querySelector('#sessionFormClass')?.value;
            const params = classId ? { class_id: classId } : {};
            axios.get('/api/get-school-groups', { params }).then(res => {
                const data = res.data.data || [];
                populateDropdown('sessionFormGroupMenu', data, 'id', 'group_name');
                if (selectedId) {
                    const item = data.find(g => String(g.id) === String(selectedId));
                    if (item) setDropdownValue('sessionFormGroup', item.id, item.group_name);
                }
            }).catch(err => console.error("Group dropdown error:", err));
        }

        function loadSessionSectionSelect(selectedId = null) {
            const classId = document.querySelector('#sessionFormClass')?.value;
            const groupId = document.querySelector('#sessionFormGroup')?.value;
            const params = {};
            if (classId) params.class_id = classId;
            if (groupId) params.group_id = groupId;
            axios.get('/api/get-school-sections', { params }).then(res => {
                const data = res.data.data || [];
                populateDropdown('sessionFormSectionMenu', data, 'id', 'section_name');
                if (selectedId) {
                    const item = data.find(s => String(s.id) === String(selectedId));
                    if (item) setDropdownValue('sessionFormSection', item.id, item.section_name);
                }
            }).catch(err => console.error("Section dropdown error:", err));
        }

        function fetchSessions(page = 1) {
            currentPage = page;
            const search = document.getElementById('sessionSearch').value || document.getElementById('sessionSearchMobile').value || '';
            axios.get('/api/school-sessions', { params: { search, page, class_id: currentFilterClass, group_id: currentFilterGroup, section_id: currentFilterSection, session_year: currentFilterSession } })
                .then(res => {
                    const sessions = res.data.data || [];
                    const meta = res.data;
                    const tbody = document.getElementById('sessionTableBody');
                    tbody.innerHTML = '';
                    if (sessions.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="10" class="border border-gray-300 px-3 py-10 text-center text-gray-500">No sessions configured.</td></tr>`;
                        document.getElementById('paginationInfo').innerText = '0 of 0';
                        document.getElementById('paginationControls').innerHTML = '';
                        return;
                    }
                    sessions.forEach((item, index) => {
                        const sl = meta.from ? meta.from + index : index + 1;
                        tbody.innerHTML += `
                            <tr class="hover:bg-gray-50">
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">${sl}</td>
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                    <div class="donate-cell-scroll" title="${item.school_class ? item.school_class.class_name : '-'}">${item.school_class ? item.school_class.class_name : '-'}</div>
                                </td>
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                    <div class="donate-cell-scroll" title="${item.school_group ? item.school_group.group_name : 'General'}">${item.school_group ? item.school_group.group_name : 'General'}</div>
                                </td>
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                    <div class="donate-cell-scroll" title="${item.school_section ? item.school_section.section_name : '-'}">${item.school_section ? item.school_section.section_name : '-'}</div>
                                </td>
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                    <div class="donate-cell-scroll" title="${item.session_year || '-'}">${item.session_year || '-'}</div>
                                </td>
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${formatDate(item.start_date)}</td>
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${formatDate(item.end_date)}</td>
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3"><span class="bg-slate-100 px-2 py-0.5 text-gray-700">${item.total_days || 0} Days</span></td>
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3"><span class="${item.remaining_days !== null && item.remaining_days <= 0 ? 'text-red-500 font-semibold' : 'text-gray-700'}">${item.remaining_days !== null ? item.remaining_days + ' Days' : '-'}</span></td>
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                                    <div class="flex h-6 w-full items-center justify-center -space-x-[3px]">
                                        <button type="button" title="Edit" aria-label="Edit" onclick="editSession(${item.id})" class="flex h-6 w-[14px] items-center justify-center text-gray-600 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 hover:bg-gray-100 hover:text-blue-600 focus-visible:ring-blue-500">
                                            <i class="far fa-edit text-xs" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" title="Delete" aria-label="Delete" onclick="deleteSession(${item.id})" class="flex h-6 w-[14px] items-center justify-center text-gray-600 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 hover:bg-gray-100 hover:text-red-600 focus-visible:ring-red-500">
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
            prevBtn.onclick = () => fetchSessions(meta.current_page - 1);
            controls.appendChild(prevBtn);
            for (let i = 1; i <= meta.last_page; i++) {
                const btn = document.createElement('button');
                btn.className = `pagination-btn ${meta.current_page === i ? 'active' : ''}`;
                btn.innerText = i;
                btn.onclick = () => fetchSessions(i);
                controls.appendChild(btn);
            }
            const nextBtn = document.createElement('button');
            nextBtn.className = 'pagination-btn';
            nextBtn.innerHTML = '<i class="mdi mdi-chevron-right"></i>';
            nextBtn.disabled = meta.current_page === meta.last_page;
            nextBtn.onclick = () => fetchSessions(meta.current_page + 1);
            controls.appendChild(nextBtn);
        }

        document.getElementById('sessionSearch')?.addEventListener('input', () => fetchSessions(1));
        document.getElementById('sessionSearchMobile')?.addEventListener('input', () => fetchSessions(1));

        function editSession(id) {
            axios.get('/api/school-sessions/' + id)
                .then(res => {
                    const item = res.data;
                    document.getElementById('record_id').value = item.id;
                    document.getElementById('start_date').value = item.start_date || '';
                    document.getElementById('end_date').value = item.end_date || '';
                    document.getElementById('session_year').value = item.session_year || '';
                    document.getElementById('total_days').value = item.total_days || 0;
                    document.getElementById('remaining_days').value = item.remaining_days !== null ? item.remaining_days : 0;
                    loadSessionClassSelect(item.class_id);
                    setTimeout(() => {
                        loadSessionGroupSelect(item.group_id);
                        setTimeout(() => {
                            loadSessionSectionSelect(item.section_id);
                        }, 300);
                    }, 300);
                    document.getElementById('sessionModalTitle').innerText = 'Update Session';
                    document.getElementById('sessionModal').classList.remove('hidden');
                })
                .catch(() => Swal.fire('Error', 'Failed to load session data.', 'error'));
        }

        function deleteSession(id) {
            Swal.fire({
                title: 'Delete Session?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it'
            }).then(r => {
                if (r.isConfirmed) {
                    axios.delete('/api/school-sessions/' + id)
                        .then(() => {
                            Toastify({ text: 'Session Deleted', style: { background: '#ef4444' } }).showToast();
                            fetchSessions(currentPage);
                        })
                        .catch(() => Swal.fire('Error', 'Could not delete session.', 'error'));
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadFilterOptions();

            document.getElementById('sessionClassFilter')?.addEventListener('change', function() {
                loadSessionGroupFilterByClass(this.value);
            });

            document.getElementById('sessionGroupFilter')?.addEventListener('change', function() {
                const classInput = document.getElementById('sessionClassFilter');
                const classId = classInput ? classInput.value : '';
                loadSessionSectionFilterByGroup(classId, this.value);
            });

            document.getElementById('sessionSectionFilter')?.addEventListener('change', function() {
                const classInput = document.getElementById('sessionClassFilter');
                const classId = classInput ? classInput.value : '';
                const groupInput = document.getElementById('sessionGroupFilter');
                const groupId = groupInput ? groupInput.value : '';
                loadSessionYearFilter(classId, groupId, this.value);
            });

            document.getElementById('sessionFormClass')?.addEventListener('change', function() {
                setDropdownValue('sessionFormGroup', '', 'Select Group');
                setDropdownValue('sessionFormSection', '', 'Select Section');
                populateDropdown('sessionFormGroupMenu', [], 'id', 'group_name');
                populateDropdown('sessionFormSectionMenu', [], 'id', 'section_name');
                if (this.value) loadSessionGroupSelect();
            });

            document.getElementById('sessionFormGroup')?.addEventListener('change', function() {
                setDropdownValue('sessionFormSection', '', 'Select Section');
                populateDropdown('sessionFormSectionMenu', [], 'id', 'section_name');
                if (this.value) loadSessionSectionSelect();
            });

            document.getElementById('btnFilter')?.addEventListener('click', () => {
                document.getElementById('filterModal')?.classList.remove('hidden');
            });
            document.getElementById('resetFilter')?.addEventListener('click', () => {
                setDropdownValue('sessionClassFilter', '', 'Select Class');
                setDropdownValue('sessionGroupFilter', '', 'Select Group');
                setDropdownValue('sessionSectionFilter', '', 'Select Section');
                setDropdownValue('sessionYearFilter', '', 'Select Session Year');
                populateDropdown('sessionGroupFilterMenu', [], 'id', 'group_name');
                populateDropdown('sessionSectionFilterMenu', [], 'id', 'section_name');
                populateDropdown('sessionYearFilterMenu', [], 'id', 'session_year');
                currentFilterClass = '';
                currentFilterGroup = '';
                currentFilterSection = '';
                currentFilterSession = '';
                currentPage = 1;
                fetchSessions(1);
                document.getElementById('filterModal')?.classList.add('hidden');
            });
            document.getElementById('applyFilter')?.addEventListener('click', () => {
                const classInput = document.getElementById('sessionClassFilter');
                currentFilterClass = classInput ? classInput.value : '';
                const groupInput = document.getElementById('sessionGroupFilter');
                currentFilterGroup = groupInput ? groupInput.value : '';
                const sectionInput = document.getElementById('sessionSectionFilter');
                currentFilterSection = sectionInput ? sectionInput.value : '';
                const sessionInput = document.getElementById('sessionYearFilter');
                currentFilterSession = sessionInput ? sessionInput.value : '';
                currentPage = 1;
                fetchSessions(1);
                document.getElementById('filterModal')?.classList.add('hidden');
            });
            document.getElementById('btnRestoreDesktop')?.addEventListener('click', () => {
                document.getElementById('sessionSearch').value = '';
                currentFilterClass = '';
                currentFilterGroup = '';
                currentFilterSection = '';
                currentFilterSession = '';
                currentPage = 1;
                fetchSessions(1);
            });
            document.getElementById('btnRestoreMobile')?.addEventListener('click', () => {
                document.getElementById('sessionSearchMobile').value = '';
                currentFilterClass = '';
                currentFilterGroup = '';
                currentFilterSection = '';
                currentFilterSession = '';
                currentPage = 1;
                fetchSessions(1);
            });
            document.querySelectorAll('[role="dialog"]').forEach(dialog => {
                dialog.addEventListener('click', function(e) {
                    if (e.target === this) this.classList.add('hidden');
                });
            });

            document.addEventListener('click', function(e) {
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

        fetchSessions();
    </script>
@endsection
