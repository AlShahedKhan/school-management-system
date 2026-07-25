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
            @include('school.fees.student-fees.partials.header')
            @include('school.fees.student-fees.partials.table')
        </div>
    </div>

    @php
        $statusOptions = collect(config('feestatus'))->map(function($cfg, $key) {
            return $cfg['label'];
        })->toArray();
    @endphp

    <x-modal.form
        id="filterModal"
        form-id="feeFilterForm"
        title="Student Fees Filter"
        close-button-id="resetFilter"
        action="#"
        method="GET"
        :enctype="null"
        class="fee-filter-modal"
        panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[288px] overflow-visible border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] md:max-w-[480px]"
    >
        <x-input.dropdown-select
            id="classFilter"
            name="class_id"
            placeholder="Select Class"
            :value="request('class_id')"
            :options="[]"
            add-button-id="openClassFromFeeFilter"
            add-button-label="Add class"
            add-button-target="classModal"
        />
        <x-input.dropdown-select
            id="sessionFilter"
            name="session_id"
            placeholder="Select Session"
            :value="request('session_id')"
            :options="[]"
            add-button-id="openSessionFromFeeFilter"
            add-button-label="Add session"
            add-button-target="sessionModal"
        />
        <x-input.dropdown-select
            id="feeTypeFilter"
            name="fee_type_name"
            placeholder="Select Fee Type"
            :value="request('fee_type_name')"
            :options="[
                'Tuition' => 'Tuition',
                'Admission' => 'Admission',
                'Exams' => 'Exams',
                'Food' => 'Food',
                'Session' => 'Session',
                'Fine' => 'Fine',
                'Others' => 'Others',
            ]"
        />
        <x-input.dropdown-select
            id="statusFilter"
            name="status"
            placeholder="Select Status"
            :value="request('status')"
            :options="$statusOptions"
        />

        <x-slot:footer>
            <div class="grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 py-3">
                <x-button.secondary id="resetFilter" type="button" class="w-full">Reset</x-button.secondary>
                <x-button.primary id="applyFilter" type="button" class="w-full">Apply</x-button.primary>
            </div>
        </x-slot:footer>
    </x-modal.form>

    @include('school.fees.student-fees.partials.student-fees-modal')
    @include('school.academic.class.partials.class-modal')
    @include('school.academic.session.partials.session-modal')
    @include('school.fees.student-fees.partials.js.modal-open')
    @include('school.academic.class.partials.js.modal-open')
    @include('school.academic.session.partials.js.modal-open')
    @include('school.fees.student-fees.partials.js.error-validation')
    @include('school.academic.class.partials.js.error-validation')
    @include('school.academic.session.partials.js.error-validation')
    @include('school.fees.student-fees.partials.js.modal-submit')
    @include('school.academic.class.partials.js.modal-submit')
    @include('school.academic.session.partials.js.modal-submit')
    @include('school.partials.export-dropdown')

    <script>
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;

        let currentPage = 1;
        let currentFilterClass = '';
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

        function fetchFees(page = 1) {
            currentPage = page;
            const search = document.getElementById('feeSearch')?.value || document.getElementById('feeSearchMobile')?.value || '';
            axios.get('/api/student-fees', {
                params: {
                    page,
                    search,
                    class_id: currentFilterClass,
                    session_id: currentFilterSession,
                    fee_type_name: document.getElementById('feeTypeFilter')?.value || '',
                    status: document.getElementById('statusFilter')?.value || '',
                }
            })
            .then(res => {
                const meta = res.data;
                const items = res.data.data || [];
                const tbody = document.getElementById('feeTableBody');
                tbody.innerHTML = '';
                if (items.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="11" class="border border-gray-300 px-3 py-10 text-center text-gray-500">No student fees found.</td></tr>';
                    document.getElementById('paginationInfo').innerText = '0 of 0';
                    document.getElementById('paginationControls').innerHTML = '';
                    return;
                }
                items.forEach((item, index) => {
                    const student = item.student || {};
                    const studentName = student.student_name || 'N/A';
                    const studentIdNumber = student.student_id_number || 'N/A';
                    const className = student.school_class?.class_name || student.class_name || 'N/A';
                    let totalPaid = item.total_paid || 0;
                    let remainingDue = item.remaining_due || parseFloat(item.amount) - totalPaid;
                    if (remainingDue < 0) remainingDue = 0;
                    const sl = meta.from ? meta.from + index : index + 1;
                    tbody.innerHTML += `
                        <tr class="hover:bg-gray-50">
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">${sl}</td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${studentName}</td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${studentIdNumber}</td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${className}</td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${item.fee_type_name || '-'}</td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${item.fee_name || '-'}</td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${parseFloat(item.amount).toFixed(2)}</td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${totalPaid.toFixed(2)}</td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3 ${remainingDue > 0 ? 'text-red-500 font-medium' : ''}">${remainingDue.toFixed(2)}</td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                <span class="status-badge status-${item.status || 'pending'}">${item.status || 'pending'}</span>
                            </td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                                <div class="flex h-6 w-full items-center justify-center -space-x-[3px]">
                                    <button type="button" title="Edit" aria-label="Edit" onclick="editFee(${item.id})" class="flex h-6 w-[14px] items-center justify-center text-gray-600 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 hover:bg-gray-100 hover:text-blue-600 focus-visible:ring-blue-500">
                                        <i class="far fa-edit text-xs" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" title="Delete" aria-label="Delete" onclick="deleteFee(${item.id})" class="flex h-6 w-[14px] items-center justify-center text-gray-600 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 hover:bg-gray-100 hover:text-red-600 focus-visible:ring-red-500">
                                        <i class="far fa-trash-alt text-xs" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>`;
                });
                renderPagination(meta);
            })
            .catch(() => {
                document.getElementById('feeTableBody').innerHTML = '<tr><td colspan="11" class="border border-gray-300 px-3 py-10 text-center text-red-400">Error loading data.</td></tr>';
            });
        }

        function renderPagination(meta) {
            const controls = document.getElementById('paginationControls');
            document.getElementById('paginationInfo').innerText = `${meta.to || 0} of ${meta.total || 0}`;
            controls.innerHTML = '';
            if (!meta.total || meta.total === 0) return;
            const prevBtn = document.createElement('button');
            prevBtn.className = 'pagination-btn';
            prevBtn.innerHTML = '<i class="mdi mdi-chevron-left"></i>';
            prevBtn.disabled = meta.current_page === 1;
            prevBtn.onclick = () => fetchFees(meta.current_page - 1);
            controls.appendChild(prevBtn);
            for (let i = 1; i <= meta.last_page; i++) {
                const btn = document.createElement('button');
                btn.className = `pagination-btn ${meta.current_page === i ? 'active' : ''}`;
                btn.innerText = i;
                btn.onclick = () => fetchFees(i);
                controls.appendChild(btn);
            }
            const nextBtn = document.createElement('button');
            nextBtn.className = 'pagination-btn';
            nextBtn.innerHTML = '<i class="mdi mdi-chevron-right"></i>';
            nextBtn.disabled = meta.current_page === meta.last_page;
            nextBtn.onclick = () => fetchFees(meta.current_page + 1);
            controls.appendChild(nextBtn);
        }

        function editFee(id) {
            axios.get('/api/student-fees/' + id)
                .then(res => {
                    const item = res.data;
                    document.getElementById('fee_id').value = item.id;
                    document.getElementById('feeModalTitle').innerText = 'Edit Student Fee';
                    document.getElementById('amount').value = item.amount || '';
                    if (item.pay_date) {
                        const d = new Date(item.pay_date);
                        if (!isNaN(d.getTime())) {
                            document.getElementById('pay_date').value = d.toISOString().split('T')[0];
                        } else {
                            document.getElementById('pay_date').value = item.pay_date;
                        }
                    }
                    document.getElementById('fee_name_input').value = item.fee_name || '';
                    setDropdownValue('status_input', item.status || 'pending', item.status || 'pending');
                    document.getElementById('feeModal').classList.remove('hidden');
                })
                .catch(() => Swal.fire('Error', 'Failed to fetch record.', 'error'));
        }

        function deleteFee(id) {
            Swal.fire({
                title: 'Delete this fee record?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it'
            }).then(r => {
                if (r.isConfirmed) {
                    axios.delete('/api/student-fees/' + id)
                        .then(() => {
                            Toastify({ text: 'Deleted Successfully', style: { background: '#ef4444' } }).showToast();
                            fetchFees(currentPage);
                        })
                        .catch(() => Swal.fire('Error', 'Could not delete fee record.', 'error'));
                }
            });
        }

        function loadFilterOptions() {
            axios.get('/api/get-school-classes').then(res => {
                populateDropdown('classFilterMenu', res.data.data || [], 'id', 'class_name');
            }).catch(() => {});
        }

        function loadSessionFilterByClass(classId) {
            if (!classId) {
                populateDropdown('sessionFilterMenu', [], 'id', 'session_year');
                return;
            }
            axios.get('/api/school-sessions', { params: { class_id: classId } }).then(res => {
                const sessions = res.data.data || [];
                const uniqueYears = [...new Set(sessions.map(s => s.session_year).filter(Boolean))];
                const yearItems = uniqueYears.map(y => ({ id: y, session_year: y }));
                populateDropdown('sessionFilterMenu', yearItems, 'id', 'session_year');
            }).catch(() => {});
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadFilterOptions();

            document.getElementById('feeSearch')?.addEventListener('input', () => fetchFees(1));
            document.getElementById('feeSearchMobile')?.addEventListener('input', function() {
                document.getElementById('feeSearch').value = this.value;
                fetchFees(1);
            });

            document.getElementById('btnFilter')?.addEventListener('click', () => {
                document.getElementById('filterModal')?.classList.remove('hidden');
            });
            document.getElementById('resetFilter')?.addEventListener('click', () => {
                setDropdownValue('classFilter', '', 'Select Class');
                setDropdownValue('sessionFilter', '', 'Select Session');
                populateDropdown('sessionFilterMenu', [], 'id', 'session_year');
                setDropdownValue('feeTypeFilter', '', 'Select Fee Type');
                setDropdownValue('statusFilter', '', 'Select Status');
                currentFilterClass = '';
                currentFilterSession = '';
                currentPage = 1;
                fetchFees(1);
                document.getElementById('filterModal')?.classList.add('hidden');
            });
            document.getElementById('applyFilter')?.addEventListener('click', () => {
                const classInput = document.getElementById('classFilter');
                currentFilterClass = classInput ? classInput.value : '';
                const sessionInput = document.getElementById('sessionFilter');
                currentFilterSession = sessionInput ? sessionInput.value : '';
                currentPage = 1;
                fetchFees(1);
                document.getElementById('filterModal')?.classList.add('hidden');
            });
            document.getElementById('btnRestoreDesktop')?.addEventListener('click', () => {
                document.getElementById('feeSearch').value = '';
                document.getElementById('feeSearchMobile').value = '';
                currentFilterClass = '';
                currentFilterSession = '';
                currentPage = 1;
                fetchFees(1);
            });
            document.getElementById('btnRestoreMobile')?.addEventListener('click', () => {
                document.getElementById('feeSearchMobile').value = '';
                document.getElementById('feeSearch').value = '';
                currentFilterClass = '';
                currentFilterSession = '';
                currentPage = 1;
                fetchFees(1);
            });

            document.getElementById('classFilter')?.addEventListener('change', function() {
                setDropdownValue('sessionFilter', '', 'Select Session');
                populateDropdown('sessionFilterMenu', [], 'id', 'session_year');
                loadSessionFilterByClass(this.value);
            });

            const closeFeeModalBtn = document.getElementById('closeFeeModal');
            if (closeFeeModalBtn) {
                closeFeeModalBtn.addEventListener('click', closeFeeModal);
            }

            document.querySelectorAll('[role="dialog"]').forEach(dialog => {
                dialog.addEventListener('click', function(e) {
                    if (e.target === this) this.classList.add('hidden');
                });
            });

            document.addEventListener('click', function(e) {
                const btn = e.target.closest('[data-dropdown-add-target]');
                if (btn) {
                    const map = { classModal: 'Add Class', sessionModal: 'Add Session' };
                    const title = map[btn.dataset.dropdownAddTarget];
                    if (title) {
                        const el = document.getElementById(btn.dataset.dropdownAddTarget + 'Title');
                        if (el) el.innerText = title;
                    }
                }
            });
        });

        fetchFees(1);
    </script>
@endsection
