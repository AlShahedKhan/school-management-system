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
            @include('school.academic.class.partials.header')
            @include('school.academic.class.partials.table')
        </div>
    </div>

    {{-- Filter Modal --}}
    <x-modal.form
        id="filterModal"
        form-id="classFilterForm"
        title="Class Filter"
        close-button-id="resetFilter"
        action="#"
        method="GET"
        :enctype="null"
        class="class-filter-modal"
        panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[288px] overflow-visible border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] md:max-w-[480px]"
    >
        <x-input.dropdown-select
            id="classFilter"
            name="class_id"
            placeholder="Class"
            :value="request('class_id')"
            :options="[]"
            add-button-id="openClassFromFilter"
            add-button-label="Add class"
            add-button-target="classModal"
        />

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

    @include('school.academic.class.partials.class-modal')
    @include('school.academic.class.partials.js.modal-open')
    @include('school.academic.class.partials.js.error-validation')
    @include('school.academic.class.partials.js.modal-submit')
    @include('school.partials.export-dropdown')

    <script>
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;

        let currentPage = 1;
        let currentFilterClass = '';

        async function loadClassFilterOptions() {
            try {
                const res = await axios.get('/api/classes');
                const menu = document.querySelector('#classFilterMenu');
                if (!menu) return;
                const data = res.data.data || [];
                menu.innerHTML = '';
                data.forEach(c => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'dropdown-select-option m-0 flex min-h-6 w-full items-center border-0 bg-white px-3 py-1 text-left text-[11px] font-normal leading-tight transition-colors hover:bg-slate-100 text-slate-800';
                    btn.dataset.value = String(c.id);
                    btn.textContent = c.class_name;
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
            } catch (err) {
                console.error('Failed to load class filter options:', err);
            }
        }

        function fetchClasses(page = 1) {
            currentPage = page;
            const search = document.getElementById('classSearch').value || document.getElementById('classSearchMobile').value || '';

            axios.get('/api/classes', { params: { search, page } })
                .then(res => {
                    let classes = res.data.data || [];
                    const meta = res.data;
                    const tbody = document.getElementById('classTableBody');
                    tbody.innerHTML = '';

                    if (currentFilterClass) {
                        classes = classes.filter(c => String(c.id) === String(currentFilterClass));
                    }

                    if (classes.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="3" class="border border-gray-300 px-3 py-10 text-center text-gray-500">No classes found.</td></tr>`;
                        document.getElementById('paginationInfo').innerText = '0 of 0';
                        document.getElementById('paginationControls').innerHTML = '';
                        return;
                    }

                    classes.forEach((item, index) => {
                        const sl = meta.from ? meta.from + index : index + 1;
                        tbody.innerHTML += `
                            <tr class="hover:bg-gray-50">
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">${sl}</td>
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                    <div class="donate-cell-scroll" title="${item.class_name}">${item.class_name}</div>
                                </td>
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                                    <div class="flex h-6 w-full items-center justify-center -space-x-[3px]">
                                        <button type="button" title="Edit ${item.class_name}" aria-label="Edit ${item.class_name}" onclick="editClass(${item.id})" class="flex h-6 w-[14px] items-center justify-center text-gray-600 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 hover:bg-gray-100 hover:text-blue-600 focus-visible:ring-blue-500">
                                            <i class="far fa-edit text-xs" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" title="Delete ${item.class_name}" aria-label="Delete ${item.class_name}" onclick="deleteClass(${item.id})" class="flex h-6 w-[14px] items-center justify-center text-gray-600 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 hover:bg-gray-100 hover:text-red-600 focus-visible:ring-red-500">
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
            prevBtn.onclick = () => fetchClasses(meta.current_page - 1);
            controls.appendChild(prevBtn);

            for (let i = 1; i <= meta.last_page; i++) {
                const btn = document.createElement('button');
                btn.className = `pagination-btn ${meta.current_page === i ? 'active' : ''}`;
                btn.innerText = i;
                btn.onclick = () => fetchClasses(i);
                controls.appendChild(btn);
            }

            const nextBtn = document.createElement('button');
            nextBtn.className = 'pagination-btn';
            nextBtn.innerHTML = '<i class="mdi mdi-chevron-right"></i>';
            nextBtn.disabled = meta.current_page === meta.last_page;
            nextBtn.onclick = () => fetchClasses(meta.current_page + 1);
            controls.appendChild(nextBtn);
        }

        document.getElementById('classSearch')?.addEventListener('input', () => fetchClasses(1));
        document.getElementById('classSearchMobile')?.addEventListener('input', () => fetchClasses(1));

        function editClass(id) {
            axios.get('/api/classes/' + id)
                .then(res => {
                    const item = res.data;
                    document.getElementById('class_id').value = item.id;
                    document.getElementById('class_name').value = item.class_name;
                    document.getElementById('classModalTitle').innerText = 'Update Class';
                    document.getElementById('classModal').classList.remove('hidden');
                })
                .catch(() => Swal.fire('Error', 'Failed to load class data.', 'error'));
        }

        function deleteClass(id) {
            Swal.fire({
                title: 'Delete Class?',
                text: 'Removing this class may affect associated students and fees.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it'
            }).then(r => {
                if (r.isConfirmed) {
                    axios.delete('/api/classes/' + id)
                        .then(() => {
                            Toastify({ text: 'Class Deleted', style: { background: '#ef4444' } }).showToast();
                            fetchClasses(currentPage);
                            loadClassFilterOptions();
                        })
                        .catch(() => Swal.fire('Error', 'Could not delete class.', 'error'));
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadClassFilterOptions();

            document.getElementById('btnFilter')?.addEventListener('click', () => {
                document.getElementById('filterModal')?.classList.remove('hidden');
            });

            document.getElementById('resetFilter')?.addEventListener('click', () => {
                const input = document.getElementById('classFilter');
                if (input) input.value = '';
                const label = document.querySelector('#classFilterButton [data-dropdown-select-label]');
                if (label) label.textContent = label.dataset.placeholder || 'Class';
                currentFilterClass = '';
                currentPage = 1;
                fetchClasses(1);
                document.getElementById('filterModal')?.classList.add('hidden');
            });

            document.getElementById('applyFilter')?.addEventListener('click', () => {
                const input = document.getElementById('classFilter');
                currentFilterClass = input ? input.value : '';
                currentPage = 1;
                fetchClasses(1);
                document.getElementById('filterModal')?.classList.add('hidden');
            });

            document.getElementById('btnRestoreDesktop')?.addEventListener('click', () => {
                document.getElementById('classSearch').value = '';
                currentFilterClass = '';
                currentPage = 1;
                fetchClasses(1);
            });

            document.getElementById('btnRestoreMobile')?.addEventListener('click', () => {
                document.getElementById('classSearchMobile').value = '';
                currentFilterClass = '';
                currentPage = 1;
                fetchClasses(1);
            });

            document.querySelectorAll('[role="dialog"]').forEach(dialog => {
                dialog.addEventListener('click', function(e) {
                    if (e.target === this) {
                        this.classList.add('hidden');
                    }
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

        fetchClasses();
    </script>
@endsection
