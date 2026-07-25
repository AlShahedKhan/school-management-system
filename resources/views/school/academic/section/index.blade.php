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
            @include('school.academic.section.partials.header')
            @include('school.academic.section.partials.table')
        </div>
    </div>

    <x-modal.form
        id="filterModal"
        form-id="sectionFilterForm"
        title="Section Filter"
        close-button-id="resetFilter"
        action="#"
        method="GET"
        :enctype="null"
        class="section-filter-modal"
        panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[288px] overflow-visible border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] md:max-w-[480px]"
    >
        <x-input.dropdown-select
            id="sectionClassFilter"
            name="class_id"
            placeholder="Select Class"
            :value="request('class_id')"
            :options="[]"
            add-button-id="openClassFromSectionFilter"
            add-button-label="Add class"
            add-button-target="classModal"
        />
        <x-input.dropdown-select
            id="sectionGroupFilter"
            name="group_id"
            placeholder="Select Group"
            :value="request('group_id')"
            :options="[]"
            add-button-id="openGroupFromSectionFilter"
            add-button-label="Add group"
            add-button-target="groupModal"
        />
        <x-input.dropdown-select
            id="sectionFilter"
            name="section_name"
            placeholder="Select Section"
            :value="request('section_name')"
            :options="[]"
            add-button-id="openSectionFromFilter"
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

    @include('school.academic.section.partials.section-modal')
    @include('school.academic.class.partials.class-modal')
    @include('school.academic.group.partials.group-modal')
    @include('school.academic.section.partials.js.modal-open')
    @include('school.academic.class.partials.js.modal-open')
    @include('school.academic.group.partials.js.modal-open')
    @include('school.academic.section.partials.js.error-validation')
    @include('school.academic.class.partials.js.error-validation')
    @include('school.academic.group.partials.js.error-validation')
    @include('school.academic.section.partials.js.modal-submit')
    @include('school.academic.class.partials.js.modal-submit')
    @include('school.academic.group.partials.js.modal-submit')
    @include('school.partials.export-dropdown')

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

        async function loadSectionGroupFilterByClass(classId) {
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
                const groupInput = document.getElementById('sectionGroupFilter');
                if (groupInput) groupInput.value = '';
                const groupLabel = document.querySelector('#sectionGroupFilterButton [data-dropdown-select-label]');
                if (groupLabel) groupLabel.textContent = groupLabel.dataset.placeholder || 'Select Group';
                populateDropdown('sectionGroupFilterMenu', uniqueGroups, 'id', 'group_name');

                const sectionInput = document.getElementById('sectionFilter');
                if (sectionInput) sectionInput.value = '';
                const sectionLabel = document.querySelector('#sectionFilterButton [data-dropdown-select-label]');
                if (sectionLabel) sectionLabel.textContent = sectionLabel.dataset.placeholder || 'Select Section';
                populateDropdown('sectionFilterMenu', [], 'id', 'section_name');
            } catch (err) {
                console.error('Failed to load group filter options:', err);
            }
        }

        async function loadSectionFilterOptions(classId, groupId) {
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
                populateDropdown('sectionFilterMenu', uniqueSections, 'section_name', 'section_name');
            } catch (err) {
                console.error('Failed to load section filter options:', err);
            }
        }

        async function loadFilterOptions() {
            try {
                const classRes = await axios.get('/api/get-school-classes');
                populateDropdown('sectionClassFilterMenu', classRes.data.data || [], 'id', 'class_name');
            } catch (err) {
                console.error('Failed to load filter options:', err);
            }
        }

        function setDropdownValue(dropdownId, value, label) {
            const input = document.querySelector(`#${dropdownId}`);
            if (input) input.value = value;
            const labelEl = document.querySelector(`#${dropdownId}Button [data-dropdown-select-label]`);
            if (labelEl) labelEl.textContent = label;
        }

        function loadSectionClassSelect(selectedId = null) {
            axios.get('/api/get-school-classes').then(res => {
                const data = res.data.data || [];
                populateDropdown('sectionClassSelectMenu', data, 'id', 'class_name');
                if (selectedId) {
                    const item = data.find(c => String(c.id) === String(selectedId));
                    if (item) setDropdownValue('sectionClassSelect', item.id, item.class_name);
                }
            }).catch(err => console.error("Class dropdown error:", err));
        }

        function loadSectionGroupSelect(classId = null, selectedId = null) {
            const params = classId ? { class_id: classId } : {};
            axios.get('/api/get-school-groups', { params }).then(res => {
                const data = res.data.data || [];
                populateDropdown('sectionGroupSelectMenu', data, 'id', 'group_name');
                if (selectedId) {
                    const item = data.find(g => String(g.id) === String(selectedId));
                    if (item) setDropdownValue('sectionGroupSelect', item.id, item.group_name);
                }
            }).catch(err => console.error("Group dropdown error:", err));
        }

        function fetchSections(page = 1) {
            currentPage = page;
            const search = document.getElementById('sectionSearch').value || document.getElementById('sectionSearchMobile').value || '';
            axios.get('/api/sections', { params: { search, page, class_id: currentFilterClass, group_id: currentFilterGroup, section_name: currentFilterSection } })
                .then(res => {
                    const sections = res.data.data || [];
                    const meta = res.data;
                    const tbody = document.getElementById('sectionTableBody');
                    tbody.innerHTML = '';
                    if (sections.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="5" class="border border-gray-300 px-3 py-10 text-center text-gray-500">No sections found.</td></tr>`;
                        document.getElementById('paginationInfo').innerText = '0 of 0';
                        document.getElementById('paginationControls').innerHTML = '';
                        return;
                    }
                    sections.forEach((item, index) => {
                        const sl = meta.from ? meta.from + index : index + 1;
                        tbody.innerHTML += `
                            <tr class="hover:bg-gray-50">
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">${sl}</td>
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                    <div class="donate-cell-scroll" title="${item.school_class ? item.school_class.class_name : 'N/A'}">${item.school_class ? item.school_class.class_name : 'N/A'}</div>
                                </td>
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                    <div class="donate-cell-scroll" title="${item.school_group ? item.school_group.group_name : 'General'}">${item.school_group ? item.school_group.group_name : 'General'}</div>
                                </td>
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                    <div class="donate-cell-scroll" title="${item.section_name || 'N/A'}">${item.section_name || 'N/A'}</div>
                                </td>
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                                    <div class="flex h-6 w-full items-center justify-center -space-x-[3px]">
                                        <button type="button" title="Edit ${item.section_name}" aria-label="Edit ${item.section_name}" onclick="editSection(${item.id})" class="flex h-6 w-[14px] items-center justify-center text-gray-600 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 hover:bg-gray-100 hover:text-blue-600 focus-visible:ring-blue-500">
                                            <i class="far fa-edit text-xs" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" title="Delete ${item.section_name}" aria-label="Delete ${item.section_name}" onclick="deleteSection(${item.id})" class="flex h-6 w-[14px] items-center justify-center text-gray-600 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 hover:bg-gray-100 hover:text-red-600 focus-visible:ring-red-500">
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
            prevBtn.onclick = () => fetchSections(meta.current_page - 1);
            controls.appendChild(prevBtn);
            for (let i = 1; i <= meta.last_page; i++) {
                const btn = document.createElement('button');
                btn.className = `pagination-btn ${meta.current_page === i ? 'active' : ''}`;
                btn.innerText = i;
                btn.onclick = () => fetchSections(i);
                controls.appendChild(btn);
            }
            const nextBtn = document.createElement('button');
            nextBtn.className = 'pagination-btn';
            nextBtn.innerHTML = '<i class="mdi mdi-chevron-right"></i>';
            nextBtn.disabled = meta.current_page === meta.last_page;
            nextBtn.onclick = () => fetchSections(meta.current_page + 1);
            controls.appendChild(nextBtn);
        }

        document.getElementById('sectionSearch')?.addEventListener('input', () => fetchSections(1));
        document.getElementById('sectionSearchMobile')?.addEventListener('input', () => fetchSections(1));

        function editSection(id) {
            axios.get('/api/sections/' + id)
                .then(res => {
                    const item = res.data;
                    document.getElementById('section_record_id').value = item.id;
                    document.getElementById('section_name').value = item.section_name || '';
                    loadSectionClassSelect(item.class_id);
                    loadSectionGroupSelect(item.class_id, item.group_id);
                    document.getElementById('sectionModalTitle').innerText = 'Update Section';
                    document.getElementById('sectionModal').classList.remove('hidden');
                })
                .catch(() => Swal.fire('Error', 'Failed to load section data.', 'error'));
        }

        function deleteSection(id) {
            Swal.fire({
                title: 'Delete Section?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it'
            }).then(r => {
                if (r.isConfirmed) {
                    axios.delete('/api/sections/' + id)
                        .then(() => {
                            Toastify({ text: 'Section Deleted', style: { background: '#ef4444' } }).showToast();
                            fetchSections(currentPage);
                        })
                        .catch(() => Swal.fire('Error', 'Could not delete section.', 'error'));
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadFilterOptions();

            document.getElementById('sectionClassFilter')?.addEventListener('change', function() {
                loadSectionGroupFilterByClass(this.value);
                const sectionInput = document.getElementById('sectionFilter');
                if (sectionInput) sectionInput.value = '';
                const sectionLabel = document.querySelector('#sectionFilterButton [data-dropdown-select-label]');
                if (sectionLabel) sectionLabel.textContent = sectionLabel.dataset.placeholder || 'Select Section';
                populateDropdown('sectionFilterMenu', [], 'id', 'section_name');
            });

            document.getElementById('sectionGroupFilter')?.addEventListener('change', function() {
                const classInput = document.getElementById('sectionClassFilter');
                const classId = classInput ? classInput.value : '';
                loadSectionFilterOptions(classId, this.value);
            });

            document.getElementById('sectionClassSelect')?.addEventListener('change', function() {
                setDropdownValue('sectionGroupSelect', '', 'Select Group');
                populateDropdown('sectionGroupSelectMenu', [], 'id', 'group_name');
                if (this.value) loadSectionGroupSelect(this.value);
            });

            document.getElementById('btnFilter')?.addEventListener('click', () => {
                document.getElementById('filterModal')?.classList.remove('hidden');
            });
            document.getElementById('resetFilter')?.addEventListener('click', () => {
                const classInput = document.getElementById('sectionClassFilter');
                if (classInput) classInput.value = '';
                const classLabel = document.querySelector('#sectionClassFilterButton [data-dropdown-select-label]');
                if (classLabel) classLabel.textContent = classLabel.dataset.placeholder || 'Select Class';
                const groupInput = document.getElementById('sectionGroupFilter');
                if (groupInput) groupInput.value = '';
                const groupLabel = document.querySelector('#sectionGroupFilterButton [data-dropdown-select-label]');
                if (groupLabel) groupLabel.textContent = groupLabel.dataset.placeholder || 'Select Group';
                const sectionInput = document.getElementById('sectionFilter');
                if (sectionInput) sectionInput.value = '';
                const sectionLabel = document.querySelector('#sectionFilterButton [data-dropdown-select-label]');
                if (sectionLabel) sectionLabel.textContent = sectionLabel.dataset.placeholder || 'Select Section';
                populateDropdown('sectionGroupFilterMenu', [], 'id', 'group_name');
                populateDropdown('sectionFilterMenu', [], 'id', 'section_name');
                currentFilterClass = '';
                currentFilterGroup = '';
                currentFilterSection = '';
                currentPage = 1;
                fetchSections(1);
                document.getElementById('filterModal')?.classList.add('hidden');
            });
            document.getElementById('applyFilter')?.addEventListener('click', () => {
                const classInput = document.getElementById('sectionClassFilter');
                currentFilterClass = classInput ? classInput.value : '';
                const groupInput = document.getElementById('sectionGroupFilter');
                currentFilterGroup = groupInput ? groupInput.value : '';
                const sectionInput = document.getElementById('sectionFilter');
                currentFilterSection = sectionInput ? sectionInput.value : '';
                currentPage = 1;
                fetchSections(1);
                document.getElementById('filterModal')?.classList.add('hidden');
            });
            document.getElementById('btnRestoreDesktop')?.addEventListener('click', () => {
                document.getElementById('sectionSearch').value = '';
                currentFilterClass = '';
                currentFilterGroup = '';
                currentFilterSection = '';
                currentPage = 1;
                fetchSections(1);
            });
            document.getElementById('btnRestoreMobile')?.addEventListener('click', () => {
                document.getElementById('sectionSearchMobile').value = '';
                currentFilterClass = '';
                currentFilterGroup = '';
                currentFilterSection = '';
                currentPage = 1;
                fetchSections(1);
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

        fetchSections();
    </script>
@endsection
