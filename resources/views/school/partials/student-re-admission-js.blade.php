<script>
    const localApi = axios.create({
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    });
    let globalClasses = [];
    let globalGroups = [];

    // --- DYNAMIC DROPDOWN POPULATION HELPER ---
    function populateDropdownSelect(id, options, selectedValue, placeholder = 'Select...') {
        const input = document.getElementById(id);
        const button = document.getElementById(id + 'Button');
        const label = button ? button.querySelector('[data-dropdown-select-label]') : null;
        const menu = document.getElementById(id + 'Menu');

        if (!input || !menu) return;

        let menuHtml = '';
        let selectedText = placeholder;

        options.forEach(opt => {
            const isSelected = String(opt.value) === String(selectedValue);
            if (isSelected) {
                selectedText = opt.label;
            }
            menuHtml += `
                <button
                    type="button"
                    class="dropdown-select-option m-0 flex min-h-6 w-full items-center border-0 bg-white px-3 py-1 text-left text-[11px] font-normal leading-tight transition-colors hover:bg-slate-100 ${isSelected ? 'bg-slate-100 text-slate-900' : 'text-slate-800'}"
                    data-value="${opt.value}"
                    role="option"
                    aria-selected="${isSelected ? 'true' : 'false'}"
                    data-dropdown-select-option
                >
                    ${opt.label}
                </button>
            `;
        });

        menu.innerHTML = menuHtml;
        input.value = selectedValue || '';
        if (label) {
            label.textContent = selectedText;
        }

        menu.querySelectorAll('[data-dropdown-select-option]').forEach(option => {
            option.addEventListener('click', () => {
                input.value = option.dataset.value || '';
                if (label) {
                    label.textContent = option.textContent.trim();
                }

                menu.querySelectorAll('[data-dropdown-select-option]').forEach(item => {
                    const isSel = item === option;
                    item.classList.toggle('bg-slate-100', isSel);
                    item.classList.toggle('text-slate-900', isSel);
                    item.classList.toggle('text-slate-800', !isSel);
                    item.setAttribute('aria-selected', String(isSel));
                });

                // Close the dropdown menu
                const root = input.closest('[data-dropdown-select]');
                if (root) {
                    root.classList.remove('is-open');
                }
                menu.classList.add('hidden');
                if (button) {
                    button.setAttribute('aria-expanded', 'false');
                    const icon = button.querySelector('i');
                    if (icon) {
                        icon.classList.remove('rotate-180');
                    }
                }

                input.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });
    }

    function populateDropdown(elemId, data, valKey, labelKey, defaultText, selectedValue = '') {
        const options = data.map(item => ({ value: item[valKey], label: item[labelKey] }));
        populateDropdownSelect(elemId, options, selectedValue, defaultText);
    }
        async function loadSourceDependencies() {
        const classId = document.getElementById('from_class').value;
        populateDropdown('from_group', [], 'id', 'group_name', 'Select Group');
        populateDropdown('from_section', [], 'id', 'section_name', 'Select Section');
        populateDropdown('from_session', [], 'id', 'session_year', 'Select Session');
        if (!classId) return;
        const filteredGroups = globalGroups.filter(g => g.class_id == classId);
        populateDropdown('from_group', filteredGroups, 'id', 'group_name', 'Select Group');
        loadSourceSections();
    }
    async function loadSourceSections() {
        const classId = document.getElementById('from_class').value;
        const groupId = document.getElementById('from_group').value;
        populateDropdown('from_section', [], 'id', 'section_name', 'Select Section');
        if (!classId) {
            loadSourceSessions();
            return;
        }
        try {
            const params = { class_id: classId };
            if (groupId) params.group_id = groupId;
            const res = await localApi.get('/api/get-school-sections', { params });
            populateDropdown('from_section', res.data.data || [], 'id', 'section_name', 'Select Section');
        } catch (e) {
            console.error("Section load failed", e);
        }
        loadSourceSessions();
    }
    async function loadSourceSessions() {
        const classId = document.getElementById('from_class').value;
        const groupId = document.getElementById('from_group').value;
        const sectionId = document.getElementById('from_section').value;
        populateDropdown('from_session', [], 'id', 'session_year', 'Select Session');
        if (!classId) return;
        try {
            const params = { class_id: classId };
            if (groupId) params.group_id = groupId;
            if (sectionId) params.section_id = sectionId;
            const res = await localApi.get('/api/get-school-sessions', { params });
            populateDropdown('from_session', res.data.data || [], 'id', 'session_year', 'Select Session');
        } catch (e) {
            console.error("Session load failed", e);
        }
        loadDestSessions();
    }
    async function loadDestSessions() {
        const classId = document.getElementById('from_class').value;
        const groupId = document.getElementById('from_group').value;
        const sectionId = document.getElementById('from_section').value;
        populateDropdown('to_session', [], 'id', 'session_year', 'Select Target Session');
        if (!classId) return;
        try {
            const params = { class_id: classId };
            if (groupId) params.group_id = groupId;
            if (sectionId) params.section_id = sectionId;
            const res = await localApi.get('/api/get-school-sessions', { params });
            populateDropdown('to_session', res.data.data || [], 'id', 'session_year', 'Select Target Session');
        } catch (e) {
            console.error("Session load failed", e);
        }
    }
    async function loadStudentsList() {
        const classId = document.getElementById('from_class').value;
        const groupId = document.getElementById('from_group').value;
        const sectionId = document.getElementById('from_section').value;
        const sessionId = document.getElementById('from_session').value;
        populateDropdown('readmit_student', [], 'id', 'student_name', 'Select Student');
        if (!classId || !sectionId || !sessionId) return;
        try {
            const res = await localApi.get('/api/school/readmit/students', {
                params: {
                    class_id: classId,
                    group_id: groupId,
                    section_id: sectionId,
                    session_id: sessionId
                }
            });
            const students = res.data.data || [];
            const studentOptions = students.map(s => ({
                value: s.id,
                label: `${s.student_name} (${s.student_id_number})`
            }));
            populateDropdownSelect('readmit_student', studentOptions, '', 'Select Student');
        } catch (e) {
            console.error("Load students error", e);
        }
    }
    async function loadReadmitFees() {
        const classId = document.getElementById('from_class').value;
        const sessionId = document.getElementById('to_session').value;
        const feeInput = document.getElementById('readmit_fee');
        if (!classId || !sessionId) {
            feeInput.value = '';
            return;
        }
        feeInput.value = 'Loading...';
        try {
            const res = await localApi.get('/api/fee-templates', {
                params: {
                    class_id: classId,
                    session_id: sessionId,
                    search: 'Admission',
                    all: 1
                }
            });
            const fees = res.data.data;
            const match = fees && fees.length > 0 ? fees[0] : null;
            feeInput.value = match ? match.amount : 'No fee defined';
        } catch (e) {
            console.error("Fee Load Error:", e);
            feeInput.value = 'Error';
        }
    }
    async function submitReadmit() {
        const studentId = document.getElementById('readmit_student').value;
        const toSection = document.getElementById('from_section').value;
        const toSession = document.getElementById('to_session').value;
        const readmitDate = document.getElementById('readmit_date').value;
        const readmitFee = document.getElementById('readmit_fee').value;
        Swal.fire({
            title: 'Confirm Re-Admission?',
            text: 'You are re-admitting the student in the same class.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Re-Admit'
        }).then(async (result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing Re-Admission...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                try {
                    await localApi.post('/api/school/readmit', {
                        student_ids: [studentId],
                        to_group: document.getElementById('from_group').value,
                        to_section: toSection,
                        to_session: toSession,
                        readmit_date: readmitDate,
                        readmit_fee: readmitFee
                    });
                    Swal.fire('Success', 'congratulation your re-admission has been success fully.',
                        'success').then(() => {
                        window.location.href = '/school/students';
                    });
                } catch (err) {
                    Swal.fire('Failed', err.response?.data?.message ||
                        'Failed to process student re-admission.', 'error');
                }
            }
        });
    }
    async function loadHistory(page = 1) {
        const tbody = document.getElementById('historyTableBody');
        if (!tbody) return;
        tbody.innerHTML =
            '<tr><td colspan="10" class="p-6 text-center text-gray-400">Loading history log...</td></tr>';
        const searchVal = document.getElementById('historySearch')?.value || '';
        try {
            const res = await localApi.get('/api/school/readmit/history', {
                params: {
                    search: searchVal,
                    page
                }
            });
            const records = res.data.data || [];
            const meta = res.data;
            tbody.innerHTML = '';
            if (records.length === 0) {
                tbody.innerHTML =
                    '<tr><td colspan="10" class="p-6 text-center text-gray-400">No re-admission history found.</td></tr>';
                document.getElementById('historyPaginationInfo').textContent = '0 of 0';
                document.getElementById('historyPaginationControls').innerHTML = '';
                return;
            }
            records.forEach((r, idx) => {
                const fromAcademic =
                    `${r.from_session}<br><span class="text-[10px] text-gray-400">Sec: ${r.from_section}${r.from_group?', Grp: '+r.from_group:''}</span>`;
                const toAcademic =
                    `${r.to_session}<br><span class="text-[10px] text-gray-400">Sec: ${r.to_section}${r.to_group?', Grp: '+r.to_group:''}</span>`;
                const readmitDate = r.readmission_date.split(' ')[0];
                tbody.innerHTML += `
<tr class="hover:bg-slate-50 transition-colors">
<td class="p-3 text-gray-500 font-normal">${(meta.from||0)+idx}</td>
<td class="p-3 text-gray-800 font-semibold">${r.student_name}</td>
<td class="p-3 font-semibold text-gray-600 font-mono">${r.student_id_number}</td>
<td class="p-3 font-semibold text-gray-700 font-mono">${r.from_admission_id||r.student_id_number}</td>
<td class="p-3 font-semibold text-gray-700 font-mono">${r.to_admission_id}</td>
<td class="p-3 text-gray-800 font-semibold">${r.class}</td>
<td class="p-3 text-gray-600">${fromAcademic}</td>
<td class="p-3 text-gray-600">${toAcademic}</td>
<td class="p-3 text-gray-500">${readmitDate}</td>
<td class="p-3 font-semibold text-blue-600">${parseFloat(r.readmission_fee).toFixed(2)}</td>
</tr>
`;
            });
            document.getElementById('historyPaginationInfo').textContent =
                `${meta.from||0} to ${meta.to||0} of ${meta.total||0}`;
            renderPagination(meta);
        } catch (e) {
            console.error("Load history error", e);
            tbody.innerHTML =
                '<tr><td colspan="10" class="p-6 text-center text-red-500">Failed to load history list.</td></tr>';
        }
    }
    function renderPagination(meta) {
        const controls = document.getElementById('historyPaginationControls');
        if (!controls) return;
        controls.innerHTML = '';
        if (meta.last_page <= 1) return;
        const prevBtn = document.createElement('button');
        prevBtn.className =
            'px-2 py-1 text-xs border border-gray-200 hover:bg-slate-50 text-gray-600 disabled:opacity-40';
        prevBtn.innerHTML = '<i class="fas fa-chevron-left"></i>';
        prevBtn.disabled = meta.current_page === 1;
        prevBtn.addEventListener('click', () => loadHistory(meta.current_page - 1));
        controls.appendChild(prevBtn);
        for (let i = 1; i <= meta.last_page; i++) {
            const pageBtn = document.createElement('button');
            pageBtn.className =
                `px-3 py-1 text-xs border ${i===meta.current_page?'bg-blue-600 text-white border-blue-600':'border-gray-200 hover:bg-slate-50 text-gray-600'}`;
            pageBtn.textContent = i;
            pageBtn.addEventListener('click', () => loadHistory(i));
            controls.appendChild(pageBtn);
        }
        const nextBtn = document.createElement('button');
        nextBtn.className =
            'px-2 py-1 text-xs border border-gray-200 hover:bg-slate-50 text-gray-600 disabled:opacity-40';
        nextBtn.innerHTML = '<i class="fas fa-chevron-right"></i>';
        nextBtn.disabled = meta.current_page === meta.last_page;
        nextBtn.addEventListener('click', () => loadHistory(meta.current_page + 1));
        controls.appendChild(nextBtn);
    }
    document.addEventListener('DOMContentLoaded', async () => {
        try {
            document.getElementById('readmitModal')?.classList.remove('hidden');
            const [classRes, groupRes] = await Promise.all([
                localApi.get('/api/get-school-classes'),
                localApi.get('/api/get-school-groups')
            ]);
            globalClasses = classRes.data.data || [];
            globalGroups = groupRes.data.data || [];
            populateDropdown('from_class', globalClasses, 'id', 'class_name', 'Select Class');
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('readmit_date').value = today;
            document.getElementById('from_class')?.addEventListener('change', loadSourceDependencies);
            document.getElementById('from_group')?.addEventListener('change', loadSourceSections);
            document.getElementById('from_section')?.addEventListener('change', loadSourceSessions);
            document.getElementById('from_session')?.addEventListener('change', loadStudentsList);
            document.getElementById('to_session')?.addEventListener('change', loadReadmitFees);
            loadHistory(1);
        } catch (e) {
            console.error("Failed loading academic metadata", e);
        }
        document.getElementById('closeReadmitModal')?.addEventListener('click', () => {
            window.location.href = '/school/students';
        });
        document.getElementById('readmitForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const feeValue = document.getElementById('readmit_fee').value.trim();
            if (!feeValue || feeValue === 'No fee defined' || feeValue === 'Error' || feeValue === 'Loading...' || parseFloat(feeValue) <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Re-Admission Fee Required',
                    text: 'You cannot proceed because no admission fee template is defined for this class/session.',
                    confirmButtonColor: '#2563eb'
                });
                return;
            }
            submitReadmit();
        });
        document.getElementById('btnCreateFeeTemplateReadmit')?.addEventListener('click', () => {
            const classId = document.getElementById('from_class').value;
            const sessionId = document.getElementById('to_session').value;
            if (!classId || !sessionId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Selection Required',
                    text: 'Please select both Class and Target Session first.',
                    confirmButtonColor: '#2563eb'
                });
                return;
            }
            document.getElementById('feeTemplateModal').classList.remove('hidden');
            const titleEl = document.getElementById('feeTemplateModalTitle');
            if (titleEl) titleEl.textContent = 'Create Re-Admission Fee';
            const feeNameInput = document.getElementById('feeNameInput');
            if (feeNameInput) feeNameInput.value = 'Re-Admission Fee';
            const payDateInput = document.getElementById('feePayDateInput');
            if (payDateInput && !payDateInput.value) {
                payDateInput.value = new Date().toISOString().split('T')[0];
            }
        });
        document.getElementById('closeFeeTemplateModal')?.addEventListener('click', () => {
            document.getElementById('feeTemplateModal').classList.add('hidden');
        });
        document.getElementById('feeTemplateForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const classId = document.getElementById('from_class').value;
            const sessionId = document.getElementById('to_session').value;
            const groupId = document.getElementById('from_group').value;
            const sectionId = document.getElementById('from_section').value;
            const feeName = document.getElementById('feeNameInput').value;
            const amount = document.getElementById('feeAmountInput').value;
            const payDate = document.getElementById('feePayDateInput').value;
            Swal.fire({
                title: 'Saving Fee Template...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            localApi.post('/api/fee-templates', {
                    class_id: classId,
                    session_id: sessionId,
                    group_id: groupId,
                    section_id: sectionId,
                    fee_type_name: 'Admission',
                    fee_name: feeName,
                    amount: amount,
                    pay_date: payDate
                })
                .then(() => {
                    Swal.close();
                    document.getElementById('feeTemplateModal').classList.add('hidden');
                    document.getElementById('feeTemplateForm').reset();
                    loadReadmitFees();
                })
                .catch(err => {
                    Swal.close();
                    let errorMsg = 'Failed to create fee template.';
                    if (err.response && err.response.data.message) {
                        errorMsg = err.response.data.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMsg
                    });
                });
        });
    });
</script>
