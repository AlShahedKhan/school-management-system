@extends('layouts.school')

@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        .spinner { border: 2px solid #e5e7eb; border-top: 2px solid #2563eb; border-radius: 50%; width: 16px; height: 16px; animation: spin 0.8s linear infinite; display: inline-block; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .badge-due { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; padding: 2px 6px; font-weight: 800; text-transform: uppercase; font-size: 9px; }

        .student-id-pill { background: #f1f5f9; color: #475569; padding: 2px 4px; border-radius: 4px; font-family: monospace; font-weight: 600; }
        .table-card { border: 1px solid #e2e8f0; background: #ffffff; border-radius: 0; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03); width: 100%; overflow: hidden; border-left: none; border-right: none; }
        .table-responsive { width: 100% !important; overflow-x: auto !important; display: block !important; background: white !important; padding: 15px !important; }
        .pagination-container { display: flex !important; align-items: center !important; justify-content: space-between !important; padding: 0.6rem 1rem !important; background: #ffffff !important; border: 1px solid #d1d5db !important; border-top: none !important; min-height: 48px !important; }
        #paginationInfo { color: #94a3b8 !important; font-weight: 500 !important; }
        .pagination-btn { height: 26px !important; min-width: 26px !important; display: inline-flex !important; align-items: center !important; justify-content: center !important; padding: 0 8px !important; font-size: 10px !important; font-weight: 800 !important; border: 1px solid #e2e8f0 !important; background: white !important; color: #64748b !important; cursor: pointer !important; border-radius: 0 !important; text-transform: uppercase !important; transition: all 0.1s ease !important; }
        .pagination-btn.active { background: #2563eb !important; color: white !important; border-color: #2563eb !important; }
        .pagination-btn:hover:not(:disabled):not(.active) { border-color: #2563eb !important; color: #2563eb !important; background: #f8fafc !important; }
        .pagination-btn:disabled { opacity: 0.4 !important; cursor: not-allowed !important; background: #f1f5f9 !important; }
        .form-input-fixed { width: 100%; border: 1px solid #cbd5e1 !important; padding: .5rem .7rem; border-radius: 0; font-size: .85rem; background: #fff; outline: none; transition: border-color 0.2s; }
        .form-input-fixed:focus { border-color: #2563eb !important; }
        @media print { body * { visibility: hidden; } #printArea, #printArea * { visibility: visible; } #printArea { position: absolute; left: 0; top: 0; width: 100%; } .no-print { display: none !important; } }
        @media (max-width: 768px) { .pagination-container { padding: 0.3rem 0.5rem; min-height: 35px; } .pagination-btn { min-width: 22px; height: 20px; padding: 0 4px; font-size: 9px; } #paginationInfo { font-size: 8px !important; } #paginationControls { gap: 2px; } }
    </style>

    <div class="main-view-container">
        <div class="max-w-full mx-auto w-full">
            @include('school.fees.due-collection.partials.header')
            @include('school.fees.due-collection.partials.table')
            @include('school.partials.export-dropdown')
        </div>
    </div>

    @include('school.fees.due-collection.partials.filter-modal')
    @include('school.fees.due-collection.partials.pay-modal')

    <script>
        const API_URL = "{{ url('api/school-due-list') }}";
        const FEE_STATUS_MAP = @json(config('feestatus'));
        let currentPage = 1;
        let masterRecords = [];
        let allStudents = [];
        let allClasses = [];

        const STATUS_OPTIONS = [
            { id: 'due', label: 'Due' },
            { id: 'due_partial', label: 'Due Partial' },
            { id: 'over_due', label: 'Over Due' },
            { id: 'over_due_partial', label: 'Over Due Partial' },
        ];

        let currentFilters = {
            class: '', group: '', section: '', session: '', student: '', status: ''
        };

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
            if (labelEl) labelEl.textContent = label || (labelEl.dataset.placeholder || 'Select...');
        }

        function toTitleCase(str) {
            if (!str) return 'N/A';
            return str.toLowerCase().split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
        }

        async function loadLedger(page = 1) {
            currentPage = page;
            const search = document.getElementById('masterSearch').value;
            const tbody = document.getElementById('masterLedgerBody');
            try {
                const params = { search, page };
                if (currentFilters.class) params.class = currentFilters.class;
                if (currentFilters.group) params.group = currentFilters.group;
                if (currentFilters.section) params.section = currentFilters.section;
                if (currentFilters.session) params.session = currentFilters.session;
                if (currentFilters.student) params.student = currentFilters.student;
                if (currentFilters.status) params.status = currentFilters.status;

                const response = await axios.get(API_URL, { params });
                const result = response.data;
                masterRecords = result.data || [];
                populateFilterOptions(masterRecords);
                renderTable(result);
            } catch (error) {
                console.error(error);
                if (typeof Toastify !== "undefined") {
                    Toastify({ text: "Failed to load data", style: { background: "#dc2626" } }).showToast();
                }
            }
        }

        function populateFilterOptions(records) {
            populateDropdown('classFilterMenu', allClasses.map(c => ({ id: c, class_name: c })), 'id', 'class_name');
            setDropdownValue('classFilter', currentFilters.class, currentFilters.class || 'Select Class');
            populateDropdown('groupFilterMenu', [], 'id', 'group_name');
            populateDropdown('sectionFilterMenu', [], 'id', 'section_name');
            populateDropdown('sessionFilterMenu', [], 'id', 'session_year');
            populateDropdown('studentFilterMenu', [], 'id', 'student_name');
            populateDropdown('statusFilterMenu', STATUS_OPTIONS, 'id', 'label');
            setDropdownValue('statusFilter', currentFilters.status, currentFilters.status ? STATUS_OPTIONS.find(s => s.id === currentFilters.status)?.label : 'All Statuses');
            updateCascadingFilters();
        }

        function updateCascadingFilters() {
            const selectedClass = document.getElementById('classFilter').value;
            const selectedGroup = document.getElementById('groupFilter').value;
            const selectedSection = document.getElementById('sectionFilter').value;
            const selectedSession = currentFilters.session;

            setDropdownValue('groupFilter', currentFilters.group, currentFilters.group || 'Select Group');
            setDropdownValue('sectionFilter', currentFilters.section, currentFilters.section || 'Select Section');
            setDropdownValue('sessionFilter', currentFilters.session, currentFilters.session || 'Select Session');
            setDropdownValue('studentFilter', currentFilters.student, currentFilters.student || 'Select Student');
            populateDropdown('groupFilterMenu', [], 'id', 'group_name');
            populateDropdown('sectionFilterMenu', [], 'id', 'section_name');
            populateDropdown('sessionFilterMenu', [], 'id', 'session_year');
            populateDropdown('studentFilterMenu', [], 'id', 'student_name');

            if (selectedClass) {
                const filteredStudents = allStudents.filter(s => s.class_name === selectedClass);
                const uniqueGroups = [...new Set(filteredStudents.map(s => s.group_name))].filter(Boolean);
                populateDropdown('groupFilterMenu', uniqueGroups.map(g => ({ id: g, group_name: g })), 'id', 'group_name');
            }
            if (selectedClass && selectedGroup) {
                const filteredStudents = allStudents.filter(s => s.class_name === selectedClass && s.group_name === selectedGroup);
                const uniqueSections = [...new Set(filteredStudents.map(s => s.section_name))].filter(Boolean);
                populateDropdown('sectionFilterMenu', uniqueSections.map(s => ({ id: s, section_name: s })), 'id', 'section_name');
            }
            if (selectedClass && selectedGroup && selectedSection) {
                const filteredStudents = allStudents.filter(s => s.class_name === selectedClass && s.group_name === selectedGroup && s.section_name === selectedSection);
                const uniqueSessions = [...new Set(filteredStudents.map(s => s.session_year))].filter(Boolean);
                populateDropdown('sessionFilterMenu', uniqueSessions.map(s => ({ id: s, session_year: s })), 'id', 'session_year');
            } else {
                let filteredStudents = selectedClass ? allStudents.filter(s => s.class_name === selectedClass) : allStudents;
                if (selectedGroup) filteredStudents = filteredStudents.filter(s => s.group_name === selectedGroup);
                const uniqueSessions = [...new Set(filteredStudents.map(s => s.session_year))].filter(Boolean);
                populateDropdown('sessionFilterMenu', uniqueSessions.map(s => ({ id: s, session_year: s })), 'id', 'session_year');
            }
            if (selectedClass && selectedGroup && selectedSection && selectedSession) {
                const filteredStudents = allStudents.filter(s =>
                    s.class_name === selectedClass && s.group_name === selectedGroup &&
                    s.section_name === selectedSection && s.session_year === selectedSession
                ).sort((a, b) => a.student_name.localeCompare(b.student_name));
                const items = filteredStudents.map(s => ({ id: s.id, student_name: s.student_id_number + ' - ' + s.student_name }));
                populateDropdown('studentFilterMenu', items, 'id', 'student_name');
            }
        }

        function renderTable(apiResult) {
            const tbody = document.getElementById('masterLedgerBody');
            tbody.innerHTML = '';
            const filtered = masterRecords;
            if (filtered.length === 0) {
                tbody.innerHTML = '<tr><td colspan="17" class="border border-gray-300 px-3 py-10 text-center text-gray-500">No dues found.</td></tr>';
                document.getElementById('paginationInfo').innerText = '0 of 0';
                const container = document.getElementById('paginationControls');
                container.innerHTML = '';
                return;
            }
            filtered.forEach((record, index) => {
                const due = parseFloat(record.total_due) || 0;
                const overdue = parseFloat(record.overdue_penalty) || 0;
                const sl = ((apiResult.current_page - 1) * apiResult.per_page) + (index + 1);
                let overdueDisplay = '';
                if (overdue > 0) {
                    overdueDisplay = `<span class="badge-due">৳${overdue.toLocaleString()}</span>`;
                } else if (record.has_alert_penalty) {
                    overdueDisplay = `<span class="text-yellow-500" title="Exceeded Last Date"><i class="mdi mdi-alert-circle-outline"></i> !</span>`;
                } else {
                    overdueDisplay = `<span class="text-gray-300">৳0</span>`;
                }
                tbody.innerHTML += `
                    <tr class="hover:bg-gray-50">
                        <td class="h-8 border border-gray-300 px-3 text-center">${sl}</td>
                        <td class="h-8 border border-gray-300 px-3">${record.student_id_number}</td>
                        <td class="h-8 border border-gray-300 px-3">${toTitleCase(record.student_name)}</td>
                        <td class="h-8 border border-gray-300 px-3">${toTitleCase(record.class)}</td>
                        <td class="h-8 border border-gray-300 px-3">${toTitleCase(record.group)}</td>
                        <td class="h-8 border border-gray-300 px-3">${toTitleCase(record.section)}</td>
                        <td class="h-8 border border-gray-300 px-3">${record.session || 'N/A'}</td>
                        <td class="h-8 border border-gray-300 px-3">${toTitleCase(record.fees_type)}</td>
                        <td class="h-8 border border-gray-300 px-3">${toTitleCase(record.fee_name)}</td>
                        <td class="h-8 border border-gray-300 px-3">৳${parseFloat(record.total_payable).toLocaleString()}</td>
                        <td class="h-8 border border-gray-300 px-3">৳${parseFloat(record.total_amount).toLocaleString()}</td>
                        <td class="h-8 border border-gray-300 px-3 font-semibold">৳${due.toLocaleString()}</td>
                        <td class="h-8 border border-gray-300 px-3">${overdueDisplay}</td>
                        <td class="h-8 border border-gray-300 px-3">${record.display_last_pay_date}</td>
                        <td class="h-8 border border-gray-300 px-3">${record.display_due_date}</td>
                        <td class="h-8 border border-gray-300 px-3">
                            <span class="status-badge status-${record.status}">${FEE_STATUS_MAP[record.status]?.label || record.status}</span>
                        </td>
                        <td class="h-8 border border-gray-300 px-3 text-center no-print">
                            <div class="flex justify-center items-center">
                                <button type="button" onclick='openPayModal(${JSON.stringify(record).replace(/'/g, "&#39;")})' class="flex h-6 w-[14px] items-center justify-center text-gray-600 transition-colors hover:bg-gray-100 hover:text-blue-600">
                                    <i class="far fa-credit-card text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>`;
            });
            document.getElementById('paginationInfo').innerText = `${apiResult.to || 0} of ${apiResult.total || 0}`;
            renderPaginationControls(apiResult.current_page, apiResult.last_page);
        }

        function renderPaginationControls(current, last) {
            const container = document.getElementById('paginationControls');
            container.innerHTML = '';
            const prevBtn = document.createElement('button');
            prevBtn.className = 'pagination-btn';
            prevBtn.innerHTML = '<i class="mdi mdi-chevron-left"></i>';
            prevBtn.disabled = current === 1;
            prevBtn.onclick = () => loadLedger(current - 1);
            container.appendChild(prevBtn);
            for (let i = 1; i <= last; i++) {
                if (i === 1 || i === last || (i >= current - 1 && i <= current + 1)) {
                    const pageBtn = document.createElement('button');
                    pageBtn.className = `pagination-btn ${i === current ? 'active' : ''}`;
                    pageBtn.innerText = i;
                    pageBtn.onclick = () => loadLedger(i);
                    container.appendChild(pageBtn);
                }
            }
            const nextBtn = document.createElement('button');
            nextBtn.className = 'pagination-btn';
            nextBtn.innerHTML = '<i class="mdi mdi-chevron-right"></i>';
            nextBtn.disabled = current === last || last === 0;
            nextBtn.onclick = () => loadLedger(current + 1);
            container.appendChild(nextBtn);
        }

        function applyClientFilters() {
            currentFilters.class = document.getElementById('classFilter').value;
            currentFilters.group = document.getElementById('groupFilter').value;
            currentFilters.section = document.getElementById('sectionFilter').value;
            currentFilters.session = document.getElementById('sessionFilter').value;
            currentFilters.student = document.getElementById('studentFilter').value;
            currentFilters.status = document.getElementById('statusFilter').value;
            updateStatusFilterButton(currentFilters.status);
            loadLedger(1);
        }

        const STATUS_FILTER_LABEL = {
            '': 'Status',
            'due': 'Due',
            'due_partial': 'Due Partial',
            'over_due': 'Over Due',
            'over_due_partial': 'Over Due Partial',
        };

        function updateStatusFilterButton(status) {
            const label = document.querySelector('#btnStatusFilter [data-dropdown-label], #btnStatusFilter span');
            const text = STATUS_FILTER_LABEL[status] || 'Status';
            if (label) {
                label.textContent = text;
                if (status && status !== '') label.classList.add('text-gray-900');
                else label.classList.remove('text-gray-900');
            }
        }

        function applyHeaderStatusFilter(status) {
            currentFilters.status = status || '';
            updateStatusFilterButton(currentFilters.status);
            loadLedger(1);
        }

        function resetClientFilters() {
            currentFilters = { class: '', group: '', section: '', session: '', student: '', status: '' };
            setDropdownValue('classFilter', '', 'Select Class');
            setDropdownValue('groupFilter', '', 'Select Group');
            setDropdownValue('sectionFilter', '', 'Select Section');
            setDropdownValue('sessionFilter', '', 'Select Session');
            setDropdownValue('studentFilter', '', 'Select Student');
            setDropdownValue('statusFilter', '', 'All Statuses');
            updateStatusFilterButton('');
            populateDropdown('groupFilterMenu', [], 'id', 'group_name');
            populateDropdown('sectionFilterMenu', [], 'id', 'section_name');
            populateDropdown('sessionFilterMenu', [], 'id', 'session_year');
            populateDropdown('studentFilterMenu', [], 'id', 'student_name');
            populateDropdown('statusFilterMenu', STATUS_OPTIONS, 'id', 'label');
            loadLedger(1);
        }

        function openPayModal(record) {
            document.getElementById('modalPaymentId').value = record.payment_id;
            const dueVal = parseFloat(record.total_due) || 0;
            const overdueVal = parseFloat(record.overdue_penalty) || 0;
            document.getElementById('rawDueVal').value = dueVal;
            document.getElementById('rawOverdueVal').value = overdueVal;
            populateDropdown('modalPayTypeMenu', [{ id: 'due', label: 'Due' }, { id: 'overdue', label: 'Overdue' }], 'id', 'label');
            populateDropdown('modalPayMethodMenu', [{ id: 'cash', label: 'Cash' }, { id: 'bank', label: 'Bank' }], 'id', 'label');
            setDropdownValue('modalPayType', overdueVal > 0 ? 'overdue' : 'due', overdueVal > 0 ? 'Overdue' : 'Due');
            handlePayTypeChange();
            document.getElementById('paymentModal').classList.remove('hidden');
        }

        function handlePayTypeChange() {
            const type = document.getElementById('modalPayType').value;
            const dueVal = parseFloat(document.getElementById('rawDueVal').value) || 0;
            const overdueVal = parseFloat(document.getElementById('rawOverdueVal').value) || 0;
            const targetVal = (type === 'overdue') ? overdueVal : dueVal;
            document.getElementById('modalTotal').value = targetVal;
            document.getElementById('modalPayAmount').value = targetVal;
            calculateRemaining();
        }

        function calculateRemaining() {
            const total = parseFloat(document.getElementById('modalTotal').value) || 0;
            const paying = parseFloat(document.getElementById('modalPayAmount').value) || 0;
            document.getElementById('modalRemaining').value = (total - paying).toFixed(2);
        }

        function closeModal() { document.getElementById('paymentModal').classList.add('hidden'); }

        function checkMethod(val) {
            if (val === 'bank') {
                Swal.fire({ title: 'Notice', text: 'Bank gateway is currently under maintenance.', icon: 'info', confirmButtonColor: '#2563eb' });
                setDropdownValue('modalPayMethod', 'cash', 'Cash');
            }
        }

        async function submitPayment() {
            const id = document.getElementById('modalPaymentId').value;
            const amount = document.getElementById('modalPayAmount').value;
            const method = document.getElementById('modalPayMethod').value;
            const date = document.getElementById('modalPayDate').value;
            if (!amount || amount <= 0) {
                Toastify({ text: "Invalid amount", style: { background: "#dc2626" } }).showToast();
                return;
            }
            const btn = document.getElementById('saveBtn');
            const spinner = document.getElementById('saveBtnSpinner');
            const btnText = document.getElementById('saveBtnText');
            btn.disabled = true;
            spinner?.classList.remove('hidden');
            if (btnText) btnText.textContent = 'Processing...';
            try {
                await axios.post(`{{ url('api/due-list/pay') }}`, {
                    school_student_fee_id: id, type_amount: amount, pay_method: method, pay_date: date,
                });
                Toastify({ text: "Payment Successful", style: { background: "#10b981" } }).showToast();
                closeModal();
                loadLedger(currentPage);
            } catch (error) {
                Swal.fire('Error', 'Payment failed to process.', 'error');
            } finally {
                btn.disabled = false;
                spinner?.classList.add('hidden');
                if (btnText) btnText.textContent = 'Confirm';
            }
        }

        async function loadStudents() {
            try {
                const res = await axios.get('/api/school/students?all=true');
                allStudents = Array.isArray(res.data) ? res.data : res.data.data;
            } catch (e) { console.error("Students retrieval failed", e); }
        }

        async function loadClasses() {
            try {
                const res = await axios.get('/api/classes');
                const classesData = Array.isArray(res.data) ? res.data : res.data.data;
                allClasses = classesData.map(c => c.class_name).filter(Boolean);
                allClasses.sort((a, b) => {
                    const numA = parseInt(a.replace(/\D/g, ''), 10) || 0;
                    const numB = parseInt(b.replace(/\D/g, ''), 10) || 0;
                    return numA - numB;
                });
            } catch (e) { console.error("Classes retrieval failed", e); }
        }

        let searchTimer;
        document.getElementById('masterSearch').oninput = () => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => loadLedger(1), 500);
        };

        window.onload = async () => {
            await loadClasses();
            await loadStudents();
            loadLedger(1);
        };

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('classFilter').addEventListener('change', function() {
                currentFilters.class = this.value;
                currentFilters.group = '';
                currentFilters.section = '';
                setDropdownValue('groupFilter', '', 'Select Group');
                setDropdownValue('sectionFilter', '', 'Select Section');
                setDropdownValue('sessionFilter', '', 'Select Session');
                setDropdownValue('studentFilter', '', 'Select Student');
                populateDropdown('groupFilterMenu', [], 'id', 'group_name');
                populateDropdown('sectionFilterMenu', [], 'id', 'section_name');
                populateDropdown('sessionFilterMenu', [], 'id', 'session_year');
                populateDropdown('studentFilterMenu', [], 'id', 'student_name');
                updateCascadingFilters();
            });
            document.getElementById('groupFilter').addEventListener('change', function() {
                currentFilters.group = this.value;
                currentFilters.section = '';
                setDropdownValue('sectionFilter', '', 'Select Section');
                setDropdownValue('sessionFilter', '', 'Select Session');
                setDropdownValue('studentFilter', '', 'Select Student');
                populateDropdown('sectionFilterMenu', [], 'id', 'section_name');
                populateDropdown('sessionFilterMenu', [], 'id', 'session_year');
                populateDropdown('studentFilterMenu', [], 'id', 'student_name');
                updateCascadingFilters();
            });
            document.getElementById('sectionFilter').addEventListener('change', function() {
                currentFilters.section = this.value;
                setDropdownValue('sessionFilter', '', 'Select Session');
                setDropdownValue('studentFilter', '', 'Select Student');
                populateDropdown('sessionFilterMenu', [], 'id', 'session_year');
                populateDropdown('studentFilterMenu', [], 'id', 'student_name');
                updateCascadingFilters();
            });
            document.getElementById('sessionFilter').addEventListener('change', function() {
                currentFilters.session = this.value;
                setDropdownValue('studentFilter', '', 'Select Student');
                populateDropdown('studentFilterMenu', [], 'id', 'student_name');
                updateCascadingFilters();
            });

            const toggleModal = (id, show) => {
                const el = document.getElementById(id);
                if (el) el.classList.toggle('hidden', !show);
            };

            document.getElementById('btnFilter')?.addEventListener('click', async () => {
                await loadStudents();
                populateFilterOptions(masterRecords);
                toggleModal('filterModal', true);
            });

            const headerStatusMap = {
                statusFilterAll: '',
                statusFilterDue: 'due',
                statusFilterDuePartial: 'due_partial',
                statusFilterOverDue: 'over_due',
                statusFilterOverDuePartial: 'over_due_partial',
            };
            Object.entries(headerStatusMap).forEach(([id, value]) => {
                document.getElementById(id)?.addEventListener('click', () => applyHeaderStatusFilter(value));
            });
            document.getElementById('resetFilter')?.addEventListener('click', () => {
                resetClientFilters();
                toggleModal('filterModal', false);
            });
            document.getElementById('applyFilter')?.addEventListener('click', () => {
                applyClientFilters();
                toggleModal('filterModal', false);
            });

            document.getElementById('exportPdf')?.addEventListener('click', () => {
                window.open('/school/due-list/pdf', '_blank');
            });

            document.getElementById('exportExcel')?.addEventListener('click', () => {
                window.open('/school/due-list/excel', '_blank');
            });

            document.getElementById('exportPrint')?.addEventListener('click', () => {
                window.print();
            });

            document.getElementById('modalPayType')?.addEventListener('change', handlePayTypeChange);
            document.getElementById('modalPayMethod')?.addEventListener('change', function() {
                if (this.value) checkMethod(this.value);
            });
            document.getElementById('modalPayAmount')?.addEventListener('input', calculateRemaining);



            document.querySelectorAll('[role="dialog"]').forEach(dialog => {
                dialog.addEventListener('click', function(e) {
                    if (e.target === this) this.classList.add('hidden');
                });
            });

            document.getElementById('masterSearchMobile')?.addEventListener('input', function() {
                document.getElementById('masterSearch').value = this.value;
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => loadLedger(1), 500);
            });

            document.getElementById('btnResetSearch')?.addEventListener('click', () => {
                document.getElementById('masterSearch').value = '';
                document.getElementById('masterSearchMobile').value = '';
                resetClientFilters();
                loadLedger(1);
            });
        });
    </script>

    @include('school.academic.class.partials.class-modal')
    @include('school.academic.group.partials.group-modal')
    @include('school.academic.section.partials.section-modal')
    @include('school.academic.session.partials.session-modal')

    @include('school.academic.class.partials.js.modal-open')
    @include('school.academic.group.partials.js.modal-open')
    @include('school.academic.section.partials.js.modal-open')
    @include('school.academic.session.partials.js.modal-open')

    @include('school.academic.class.partials.js.modal-submit')
    @include('school.academic.group.partials.js.modal-submit')
    @include('school.academic.section.partials.js.modal-submit')
    @include('school.academic.session.partials.js.modal-submit')
@endsection
