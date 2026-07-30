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
            id="feeClassFilter"
            name="class_id"
            placeholder="Select Class"
            :value="request('class_id')"
            :options="[]"
            add-button-id="openClassFromStudentFeeFilter"
            add-button-label="Add class"
            add-button-target="classModal"
        />
        <x-input.dropdown-select
            id="feeGroupFilter"
            name="group_id"
            placeholder="Select Group"
            :value="request('group_id')"
            :options="[]"
            add-button-id="openGroupFromStudentFeeFilter"
            add-button-label="Add group"
            add-button-target="groupModal"
        />
        <x-input.dropdown-select
            id="feeSectionFilter"
            name="section_id"
            placeholder="Select Section"
            :value="request('section_id')"
            :options="[]"
            add-button-id="openSectionFromStudentFeeFilter"
            add-button-label="Add section"
            add-button-target="sectionModal"
        />
        <x-input.dropdown-select
            id="feeSessionFilter"
            name="session_id"
            placeholder="Select Session"
            :value="request('session_id')"
            :options="[]"
            add-button-id="openSessionFromStudentFeeFilter"
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
                'Others' => 'Others',
            ]"
        />
        <x-input.dropdown-select
            id="statusFilter"
            name="status"
            placeholder="Select Status"
            :value="request('status')"
            :options="[
                'paid' => 'Paid',
                'partial_paid' => 'Partial Paid',
                'due' => 'Due',
                'due_partial' => 'Due Partial',
                'over_due' => 'Over Due',
                'over_due_partial' => 'Over Due Partial',
                'advance' => 'Advance',
                'advance_partial' => 'Advance Partial',
                'pending' => 'Pending',
            ]"
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
    @include('school.academic.class.partials.js.modal-open')
    @include('school.academic.group.partials.js.modal-open')
    @include('school.academic.section.partials.js.modal-open')
    @include('school.academic.session.partials.js.modal-open')
    @include('school.academic.class.partials.js.error-validation')
    @include('school.academic.group.partials.js.error-validation')
    @include('school.academic.section.partials.js.error-validation')
    @include('school.academic.session.partials.js.error-validation')
    @include('school.partials.export-dropdown')

    <script>
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        axios.defaults.headers.common['X-CSRF-TOKEN'] = token;

        let currentPage = 1;
        let currentFilterClass = '';
        let currentFilterGroup = '';
        let currentFilterSection = '';
        let currentFilterSession = '';
        let currentFilterFeeType = '';
        let currentFilterStatus = '';

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
            const d = new Date(dateString);
            if (isNaN(d.getTime())) return dateString;
            const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
            return d.getDate() + '-' + months[d.getMonth()] + '-' + d.getFullYear();
        }

        function setDropdownValueFromMenu(inputId, value) {
            const input = document.getElementById(inputId);
            if (input) input.value = value;
            const menu = document.querySelector('#' + inputId + 'Menu');
            const label = document.querySelector('#' + inputId + 'Button [data-dropdown-select-label]');
            if (label) {
                const opt = menu?.querySelector('[data-value="' + value + '"]');
                label.textContent = opt ? opt.textContent.trim() : (label.dataset.placeholder || 'Select...');
            }
        }

        async function loadFeeFilterOptions() {
            try {
                const classRes = await axios.get('/api/get-school-classes');
                populateDropdown('feeClassFilterMenu', classRes.data.data || [], 'id', 'class_name');
            } catch (err) {
                console.error('Failed to load filter options:', err);
            }
        }

        async function loadFeeGroupFilter() {
            const classId = document.getElementById('feeClassFilter')?.value;
            setDropdownValue('feeGroupFilter', '', 'Select Group');
            setDropdownValue('feeSectionFilter', '', 'Select Section');
            setDropdownValue('feeSessionFilter', '', 'Select Session');
            if (!classId) return;
            const params = classId ? { class_id: classId } : {};
            try {
                const res = await axios.get('/api/get-school-groups', { params });
                populateDropdown('feeGroupFilterMenu', res.data.data || [], 'id', 'group_name');
            } catch (e) { console.error(e); }
            loadFeeSectionFilter();
        }

        async function loadFeeSectionFilter() {
            const classId = document.getElementById('feeClassFilter')?.value;
            const groupId = document.getElementById('feeGroupFilter')?.value;
            setDropdownValue('feeSectionFilter', '', 'Select Section');
            setDropdownValue('feeSessionFilter', '', 'Select Session');
            if (!classId) return;
            const params = { class_id: classId };
            if (groupId) params.group_id = groupId;
            try {
                const res = await axios.get('/api/get-school-sections', { params });
                populateDropdown('feeSectionFilterMenu', res.data.data || [], 'id', 'section_name');
            } catch (e) { console.error(e); }
            loadFeeSessionFilter();
        }

        async function loadFeeSessionFilter() {
            const classId = document.getElementById('feeClassFilter')?.value;
            const groupId = document.getElementById('feeGroupFilter')?.value;
            const sectionId = document.getElementById('feeSectionFilter')?.value;
            setDropdownValue('feeSessionFilter', '', 'Select Session');
            if (!classId) return;
            const params = { class_id: classId };
            if (groupId) params.group_id = groupId;
            if (sectionId) params.section_id = sectionId;
            try {
                const res = await axios.get('/api/school-sessions', { params });
                const sessions = res.data.data || [];
                const uniqueYears = [...new Set(sessions.map(s => s.session_year).filter(Boolean))];
                const yearItems = uniqueYears.map(y => ({ id: y, session_year: y }));
                populateDropdown('feeSessionFilterMenu', yearItems, 'id', 'session_year');
            } catch (e) { console.error(e); }
        }

        function fetchStudentFees(page = 1) {
            currentPage = page;
            const search = document.getElementById('feeSearch')?.value || document.getElementById('feeSearchMobile')?.value || '';
            axios.get('/api/student-fees', {
                params: {
                    search,
                    page,
                    class_id: currentFilterClass,
                    group_id: currentFilterGroup,
                    section_id: currentFilterSection,
                    session_id: currentFilterSession,
                    fee_type_name: currentFilterFeeType,
                    status: currentFilterStatus,
                }
            })
            .then(res => {
                const items = res.data.data || [];
                const meta = res.data;
                const tbody = document.getElementById('feeTableBody');
                tbody.innerHTML = '';
                if (items.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="10" class="border border-gray-300 px-3 py-10 text-center text-gray-500">No student fees found.</td></tr>`;
                    document.getElementById('paginationInfo').innerText = '0 of 0';
                    document.getElementById('paginationControls').innerHTML = '';
                    return;
                }
                items.forEach((item, index) => {
                    const sl = meta.from ? meta.from + index : index + 1;
                    const student = item.student || {};
                    const studentName = student.student_name || 'N/A';
                    const studentIdNumber = student.student_id_number || 'N/A';
                    const className = student.school_class?.class_name || student.class_name || 'N/A';
                    const totalPaid = item.total_paid || 0;
                    const remainingDue = item.remaining_due || 0;
                    const statusClass = `status-${item.status || 'pending'}`;
                    tbody.innerHTML += `
                        <tr class="hover:bg-gray-50">
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">${sl}</td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                <div class="donate-cell-scroll" title="${studentName}">${studentName}</div>
                            </td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                <div class="donate-cell-scroll" title="${studentIdNumber}">${studentIdNumber}</div>
                            </td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                <div class="donate-cell-scroll" title="${className}">${className}</div>
                            </td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                <div class="donate-cell-scroll" title="${item.fee_type_name || '-'}">${item.fee_type_name || '-'}</div>
                            </td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                <div class="donate-cell-scroll" title="${item.fee_name || '-'}">${item.fee_name || '-'}</div>
                            </td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${parseFloat(item.base_amount).toFixed(2)}</td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${totalPaid.toFixed(2)}</td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${remainingDue.toFixed(2)}</td>
                            <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                <span class="status-badge ${statusClass}">${item.status || 'pending'}</span>
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
            prevBtn.onclick = () => fetchStudentFees(meta.current_page - 1);
            controls.appendChild(prevBtn);
            for (let i = 1; i <= meta.last_page; i++) {
                const btn = document.createElement('button');
                btn.className = `pagination-btn ${meta.current_page === i ? 'active' : ''}`;
                btn.innerText = i;
                btn.onclick = () => fetchStudentFees(i);
                controls.appendChild(btn);
            }
            const nextBtn = document.createElement('button');
            nextBtn.className = 'pagination-btn';
            nextBtn.innerHTML = '<i class="mdi mdi-chevron-right"></i>';
            nextBtn.disabled = meta.current_page === meta.last_page;
            nextBtn.onclick = () => fetchStudentFees(meta.current_page + 1);
            controls.appendChild(nextBtn);
        }

        function showEl(id) { const el = document.getElementById(id); if (el) el.style.display = 'block'; }
        function hideEl(id) { const el = document.getElementById(id); if (el) el.style.display = 'none'; }

        document.addEventListener('DOMContentLoaded', function() {
            loadFeeFilterOptions();

            document.getElementById('feeSearch')?.addEventListener('input', () => fetchStudentFees(1));
            document.getElementById('feeSearchMobile')?.addEventListener('input', () => fetchStudentFees(1));

            document.getElementById('feeClassFilter')?.addEventListener('change', function() {
                currentFilterClass = this.value || '';
                loadFeeGroupFilter();
            });

            document.getElementById('feeGroupFilter')?.addEventListener('change', function() {
                currentFilterGroup = this.value || '';
                currentFilterSection = '';
                currentFilterSession = '';
                loadFeeSectionFilter();
            });

            document.getElementById('feeSectionFilter')?.addEventListener('change', function() {
                currentFilterSection = this.value || '';
                currentFilterSession = '';
                loadFeeSessionFilter();
            });

            document.getElementById('feeSessionFilter')?.addEventListener('change', function() {
                currentFilterSession = this.value || '';
            });

            document.getElementById('btnFilter')?.addEventListener('click', () => {
                loadFeeGroupFilter();
                document.getElementById('filterModal')?.classList.remove('hidden');
            });

            document.getElementById('resetFilter')?.addEventListener('click', () => {
                setDropdownValue('feeClassFilter', '', 'Select Class');
                setDropdownValue('feeGroupFilter', '', 'Select Group');
                setDropdownValue('feeSectionFilter', '', 'Select Section');
                setDropdownValue('feeSessionFilter', '', 'Select Session');
                setDropdownValue('feeTypeFilter', '', 'Select Fee Type');
                setDropdownValue('statusFilter', '', 'Select Status');
                currentFilterClass = '';
                currentFilterGroup = '';
                currentFilterSection = '';
                currentFilterSession = '';
                currentFilterFeeType = '';
                currentFilterStatus = '';
                currentPage = 1;
                fetchStudentFees(1);
                document.getElementById('filterModal')?.classList.add('hidden');
            });

            document.getElementById('applyFilter')?.addEventListener('click', () => {
                const classInput = document.getElementById('feeClassFilter');
                currentFilterClass = classInput ? classInput.value : '';
                const groupInput = document.getElementById('feeGroupFilter');
                currentFilterGroup = groupInput ? groupInput.value : '';
                const sectionInput = document.getElementById('feeSectionFilter');
                currentFilterSection = sectionInput ? sectionInput.value : '';
                const sessionInput = document.getElementById('feeSessionFilter');
                currentFilterSession = sessionInput ? sessionInput.value : '';
                const feeTypeInput = document.getElementById('feeTypeFilter');
                currentFilterFeeType = feeTypeInput ? feeTypeInput.value : '';
                const statusInput = document.getElementById('statusFilter');
                currentFilterStatus = statusInput ? statusInput.value : '';
                currentPage = 1;
                fetchStudentFees(1);
                document.getElementById('filterModal')?.classList.add('hidden');
            });

            document.getElementById('btnRestoreDesktop')?.addEventListener('click', () => {
                document.getElementById('feeSearch').value = '';
                currentFilterClass = '';
                currentFilterGroup = '';
                currentFilterSection = '';
                currentFilterSession = '';
                currentFilterFeeType = '';
                currentFilterStatus = '';
                currentPage = 1;
                fetchStudentFees(1);
            });

            document.getElementById('btnRestoreMobile')?.addEventListener('click', () => {
                document.getElementById('feeSearchMobile').value = '';
                currentFilterClass = '';
                currentFilterGroup = '';
                currentFilterSection = '';
                currentFilterSession = '';
                currentFilterFeeType = '';
                currentFilterStatus = '';
                currentPage = 1;
                fetchStudentFees(1);
            });

            document.querySelectorAll('[role="dialog"]').forEach(dialog => {
                dialog.addEventListener('click', function(e) {
                    if (e.target === this) this.classList.add('hidden');
                });
            });
        });

        fetchStudentFees();
    </script>
@endsection
