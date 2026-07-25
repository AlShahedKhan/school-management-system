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
            @include('school.academic.group.partials.header')
            @include('school.academic.group.partials.table')
        </div>
    </div>

    <x-modal.form
        id="filterModal"
        form-id="groupFilterForm"
        title="Group Filter"
        close-button-id="resetFilter"
        action="#"
        method="GET"
        :enctype="null"
        class="group-filter-modal"
        panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[288px] overflow-visible border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] md:max-w-[480px]"
    >
        <x-input.dropdown-select
            id="classFilter"
            name="class_id"
            placeholder="Select Class"
            :value="request('class_id')"
            :options="[]"
            add-button-id="openClassFromGroupFilter"
            add-button-label="Add class"
            add-button-target="classModal"
        />
        <x-input.dropdown-select
            id="groupFilter"
            name="group_id"
            placeholder="Select Group"
            :value="request('group_id')"
            :options="[]"
            add-button-id="openGroupFromFilter"
            add-button-label="Add group"
            add-button-target="groupModal"
        />

        <x-slot:footer>
            <div class="grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 py-3">
                <x-button.secondary id="resetFilter" type="button" class="w-full">Reset</x-button.secondary>
                <x-button.primary id="applyFilter" type="button" class="w-full">Apply</x-button.primary>
            </div>
        </x-slot:footer>
    </x-modal.form>

    @include('school.academic.group.partials.group-modal')
    @include('school.academic.class.partials.class-modal')
    @include('school.academic.group.partials.js.modal-open')
    @include('school.academic.class.partials.js.modal-open')
    @include('school.academic.group.partials.js.error-validation')
    @include('school.academic.class.partials.js.error-validation')
    @include('school.academic.group.partials.js.modal-submit')
    @include('school.academic.class.partials.js.modal-submit')
    @include('school.partials.export-dropdown')

    <script>
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;

        let currentPage = 1;
        let currentFilterClass = '';
        let currentFilterGroup = '';

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

        async function loadGroupFilterByClass(classId) {
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
                const groupInput = document.getElementById('groupFilter');
                if (groupInput) groupInput.value = '';
                const groupLabel = document.querySelector('#groupFilterButton [data-dropdown-select-label]');
                if (groupLabel) groupLabel.textContent = groupLabel.dataset.placeholder || 'Select Group';
                populateDropdown('groupFilterMenu', uniqueGroups, 'id', 'group_name');
            } catch (err) {
                console.error('Failed to load group filter options:', err);
            }
        }

        async function loadFilterOptions() {
            try {
                const classRes = await axios.get('/api/get-school-classes');
                populateDropdown('classFilterMenu', classRes.data.data || [], 'id', 'class_name');
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

        function loadGroupClassSelect(selectedId = null) {
            axios.get('/api/get-school-classes').then(res => {
                const data = res.data.data || [];
                populateDropdown('groupClassSelectMenu', data, 'id', 'class_name');
                if (selectedId) {
                    const item = data.find(c => String(c.id) === String(selectedId));
                    if (item) setDropdownValue('groupClassSelect', item.id, item.class_name);
                }
            }).catch(err => console.error("Class dropdown error:", err));
        }

        function fetchGroups(page = 1) {
            currentPage = page;
            const search = document.getElementById('groupSearch').value || document.getElementById('groupSearchMobile').value || '';
            axios.get('/api/groups', { params: { search, page } })
                .then(res => {
                    let groups = res.data.data || [];
                    const meta = res.data;
                    if (currentFilterClass) {
                        groups = groups.filter(g => String(g.class_id) === String(currentFilterClass));
                    }
                    if (currentFilterGroup) {
                        groups = groups.filter(g => String(g.id) === String(currentFilterGroup));
                    }
                    const tbody = document.getElementById('groupTableBody');
                    tbody.innerHTML = '';
                    if (groups.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="4" class="border border-gray-300 px-3 py-10 text-center text-gray-500">No groups found.</td></tr>`;
                        document.getElementById('paginationInfo').innerText = '0 of 0';
                        document.getElementById('paginationControls').innerHTML = '';
                        return;
                    }
                    groups.forEach((item, index) => {
                        const sl = meta.from ? meta.from + index : index + 1;
                        tbody.innerHTML += `
                            <tr class="hover:bg-gray-50">
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">${sl}</td>
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                    <div class="donate-cell-scroll" title="${item.school_class ? item.school_class.class_name : 'N/A'}">${item.school_class ? item.school_class.class_name : 'N/A'}</div>
                                </td>
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                    <div class="donate-cell-scroll" title="${item.group_name || 'N/A'}">${item.group_name || 'N/A'}</div>
                                </td>
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                                    <div class="flex h-6 w-full items-center justify-center -space-x-[3px]">
                                        <button type="button" title="Edit ${item.group_name}" aria-label="Edit ${item.group_name}" onclick="editGroup(${item.id})" class="flex h-6 w-[14px] items-center justify-center text-gray-600 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 hover:bg-gray-100 hover:text-blue-600 focus-visible:ring-blue-500">
                                            <i class="far fa-edit text-xs" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" title="Delete ${item.group_name}" aria-label="Delete ${item.group_name}" onclick="deleteGroup(${item.id})" class="flex h-6 w-[14px] items-center justify-center text-gray-600 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 hover:bg-gray-100 hover:text-red-600 focus-visible:ring-red-500">
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
            prevBtn.onclick = () => fetchGroups(meta.current_page - 1);
            controls.appendChild(prevBtn);
            for (let i = 1; i <= meta.last_page; i++) {
                const btn = document.createElement('button');
                btn.className = `pagination-btn ${meta.current_page === i ? 'active' : ''}`;
                btn.innerText = i;
                btn.onclick = () => fetchGroups(i);
                controls.appendChild(btn);
            }
            const nextBtn = document.createElement('button');
            nextBtn.className = 'pagination-btn';
            nextBtn.innerHTML = '<i class="mdi mdi-chevron-right"></i>';
            nextBtn.disabled = meta.current_page === meta.last_page;
            nextBtn.onclick = () => fetchGroups(meta.current_page + 1);
            controls.appendChild(nextBtn);
        }

        document.getElementById('groupSearch')?.addEventListener('input', () => fetchGroups(1));
        document.getElementById('groupSearchMobile')?.addEventListener('input', () => fetchGroups(1));

        function editGroup(id) {
            axios.get('/api/groups/' + id)
                .then(res => {
                    const item = res.data;
                    document.getElementById('group_id').value = item.id;
                    document.getElementById('group_name').value = item.group_name || '';
                    loadGroupClassSelect(item.class_id);
                    document.getElementById('groupModalTitle').innerText = 'Update Group';
                    document.getElementById('groupModal').classList.remove('hidden');
                })
                .catch(() => Swal.fire('Error', 'Failed to load group data.', 'error'));
        }

        function deleteGroup(id) {
            Swal.fire({
                title: 'Delete Group?',
                text: 'Removing this group might affect student records.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it'
            }).then(r => {
                if (r.isConfirmed) {
                    axios.delete('/api/groups/' + id)
                        .then(() => {
                            Toastify({ text: 'Group Deleted', style: { background: '#ef4444' } }).showToast();
                            fetchGroups(currentPage);
                            loadFilterOptions();
                        })
                        .catch(() => Swal.fire('Error', 'Could not delete group.', 'error'));
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadFilterOptions();

            document.getElementById('classFilter')?.addEventListener('change', function() {
                loadGroupFilterByClass(this.value);
            });

            document.getElementById('btnFilter')?.addEventListener('click', () => {
                document.getElementById('filterModal')?.classList.remove('hidden');
            });
            document.getElementById('resetFilter')?.addEventListener('click', () => {
                const classInput = document.getElementById('classFilter');
                if (classInput) classInput.value = '';
                const classLabel = document.querySelector('#classFilterButton [data-dropdown-select-label]');
                if (classLabel) classLabel.textContent = classLabel.dataset.placeholder || 'Select Class';
                const groupInput = document.getElementById('groupFilter');
                if (groupInput) groupInput.value = '';
                const groupLabel = document.querySelector('#groupFilterButton [data-dropdown-select-label]');
                if (groupLabel) groupLabel.textContent = groupLabel.dataset.placeholder || 'Select Group';
                currentFilterClass = '';
                currentFilterGroup = '';
                currentPage = 1;
                fetchGroups(1);
                document.getElementById('filterModal')?.classList.add('hidden');
            });
            document.getElementById('applyFilter')?.addEventListener('click', () => {
                const classInput = document.getElementById('classFilter');
                currentFilterClass = classInput ? classInput.value : '';
                const groupInput = document.getElementById('groupFilter');
                currentFilterGroup = groupInput ? groupInput.value : '';
                currentPage = 1;
                fetchGroups(1);
                document.getElementById('filterModal')?.classList.add('hidden');
            });
            document.getElementById('btnRestoreDesktop')?.addEventListener('click', () => {
                document.getElementById('groupSearch').value = '';
                currentFilterClass = '';
                currentFilterGroup = '';
                currentPage = 1;
                fetchGroups(1);
            });
            document.getElementById('btnRestoreMobile')?.addEventListener('click', () => {
                document.getElementById('groupSearchMobile').value = '';
                currentFilterClass = '';
                currentFilterGroup = '';
                currentPage = 1;
                fetchGroups(1);
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

        fetchGroups();
    </script>
@endsection
