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
    }
    async function loadDestDependencies() {
        const classId = document.getElementById('to_class').value;
        populateDropdown('to_group', [], 'id', 'group_name', 'Select Group');
        populateDropdown('to_section', [], 'id', 'section_name', 'Select Section');
        populateDropdown('to_session', [], 'id', 'session_year', 'Select Session');
        document.getElementById('promote_fee').value = '';
        if (!classId) return;
        const filteredGroups = globalGroups.filter(g => g.class_id == classId);
        populateDropdown('to_group', filteredGroups, 'id', 'group_name', 'Select Group');
        loadDestSections();
    }
    async function loadDestSections() {
        const classId = document.getElementById('to_class').value;
        const groupId = document.getElementById('to_group').value;
        populateDropdown('to_section', [], 'id', 'section_name', 'Select Section');
        document.getElementById('promote_fee').value = '';
        if (!classId) {
            loadDestSessions();
            return;
        }
        try {
            const params = { class_id: classId };
            if (groupId) params.group_id = groupId;
            const res = await localApi.get('/api/get-school-sections', { params });
            populateDropdown('to_section', res.data.data || [], 'id', 'section_name', 'Select Section');
        } catch (e) {
            console.error("Section load failed", e);
        }
        loadDestSessions();
    }
    async function loadDestSessions() {
        const classId = document.getElementById('to_class').value;
        const groupId = document.getElementById('to_group').value;
        const sectionId = document.getElementById('to_section').value;
        populateDropdown('to_session', [], 'id', 'session_year', 'Select Session');
        if (!classId) return;
        try {
            const params = { class_id: classId };
            if (groupId) params.group_id = groupId;
            if (sectionId) params.section_id = sectionId;
            const res = await localApi.get('/api/get-school-sessions', { params });
            populateDropdown('to_session', res.data.data || [], 'id', 'session_year', 'Select Session');
        } catch (e) {
            console.error("Session load failed", e);
        }
    }
    async function fetchPromoteFeeFromTemplate() {
        const classId = document.getElementById('to_class').value;
        const groupId = document.getElementById('to_group').value;
        const sectionId = document.getElementById('to_section').value;
        const sessionId = document.getElementById('to_session').value;
        const feeInput = document.getElementById('promote_fee');
        if (!classId || !sessionId) {
            feeInput.value = '';
            return;
        }
        feeInput.value = 'Loading...';
        try {
            const params = { class_id: classId, session_id: sessionId, search: 'Promote', all: 1 };
            if (groupId) params.group_id = groupId;
            if (sectionId) params.section_id = sectionId;
            const res = await localApi.get('/api/fee-templates', { params });
            const templates = res.data.data || [];
            const match = templates.find(t =>
                t.fee_type_name.toLowerCase().includes('promote') ||
                t.fee_name.toLowerCase().includes('promote')
            );
            feeInput.value = match ? match.amount : 'No fee defined';
        } catch (e) {
            console.error("Promote fee load failed", e);
            feeInput.value = 'Error';
        }
    }
    async function loadStudentsList() {
        const fromClass = document.getElementById('from_class').value;
        const fromSection = document.getElementById('from_section').value;
        const fromSession = document.getElementById('from_session').value;
        const tbody = document.getElementById('studentSelectionBody');
        if (!fromClass || !fromSection || !fromSession) {
            tbody.innerHTML = '<tr><td colspan="4" class="p-4 text-center text-slate-400">Please select Source Academic Details first.</td></tr>';
            return;
        }
        tbody.innerHTML = '<tr><td colspan="4" class="p-4 text-center text-slate-400"><i class="fas fa-spinner fa-spin mr-1"></i> Loading students...</td></tr>';
        document.getElementById('selectAllCheckbox').checked = false;
        try {
            const res = await localApi.get('/api/school/promote/students', {
                params: {
                    class_id: fromClass,
                    group_id: document.getElementById('from_group').value || null,
                    section_id: fromSection,
                    session_id: fromSession
                }
            });
            const students = res.data.data || [];
            tbody.innerHTML = '';
            if (students.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="p-4 text-center text-slate-400">No active students found in this source class.</td></tr>';
                return;
            }
            students.forEach((s, index) => {
                tbody.innerHTML += `
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="p-2 text-slate-500 font-normal">${index + 1}</td>
                        <td class="p-2 font-semibold text-slate-700">${s.student_id_number}</td>
                        <td class="p-2 text-slate-600">${s.student_name}</td>
                        <td class="p-2 text-center">
                            <input type="checkbox" name="student_selection" value="${s.id}" class="w-4 h-4 cursor-pointer accent-blue-600">
                        </td>
                    </tr>`;
            });
        } catch (e) {
            console.error("Student fetch failed", e);
            tbody.innerHTML = '<tr><td colspan="4" class="p-4 text-center text-red-500">Failed to load students.</td></tr>';
        }
    }
    function toggleSelectAll(master) {
        const checkboxes = document.querySelectorAll('input[name="student_selection"]');
        checkboxes.forEach(cb => cb.checked = master.checked);
    }
    function getSelectedStudentIds() {
        const checkboxes = document.querySelectorAll('input[name="student_selection"]:checked');
        const ids = [];
        checkboxes.forEach(cb => ids.push(parseInt(cb.value)));
        return ids;
    }
    async function submitPromote() {
        const toClass = document.getElementById('to_class').value;
        const toSection = document.getElementById('to_section').value;
        const toSession = document.getElementById('to_session').value;
        const promoteFee = document.getElementById('promote_fee').value;
        const studentIds = getSelectedStudentIds();
        if (studentIds.length === 0) {
            Swal.fire('Selection Required', 'Please select at least one student to promote.', 'warning');
            return;
        }
        const fromClass = document.getElementById('from_class').value;
        const fromSession = document.getElementById('from_session').value;
        if (fromClass == toClass && fromSession == toSession) {
            Swal.fire('Promotion Error', 'Source and destination Class & Session cannot be the same.', 'warning');
            return;
        }
        const data = {
            student_ids: studentIds,
            to_class: toClass,
            to_group: document.getElementById('to_group').value || null,
            to_section: toSection,
            to_session: toSession,
            promote_date: document.getElementById('promote_date').value,
            promote_fee: parseFloat(promoteFee) || 0
        };
        Swal.fire({
            title: 'Confirm Promotion',
            text: `Are you sure you want to promote ${data.student_ids.length} student(s) to the new class structure?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Promote Now'
        }).then(async (result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Processing...',
                    text: 'Promoting students and generating SMS notifications.',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                try {
                    await localApi.post('/api/school/promote', data);
                    Swal.close();
                    Swal.fire({
                        title: 'Success!',
                        text: 'Students promoted successfully and notifications dispatched.',
                        icon: 'success',
                        confirmButtonColor: '#10b981'
                    }).then(() => {
                        window.location.href = '/school/students';
                    });
                } catch (e) {
                    Swal.close();
                    Swal.fire('Promotion Failed', e.response?.data?.message || 'Failed to execute student promotion.', 'error');
                }
            }
        });
    }
    async function handleToSessionChange() {
        await fetchPromoteFeeFromTemplate();
    }
    document.addEventListener('DOMContentLoaded', async () => {
        try {
            document.getElementById('promoteModal')?.classList.remove('hidden');
            const [classRes, groupRes] = await Promise.all([
                localApi.get('/api/get-school-classes'),
                localApi.get('/api/get-school-groups')
            ]);
            globalClasses = classRes.data.data || [];
            globalGroups = groupRes.data.data || [];
            populateDropdown('from_class', globalClasses, 'id', 'class_name', 'Select Class');
            populateDropdown('to_class', globalClasses, 'id', 'class_name', 'Select Class');
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('promote_date').value = today;
        } catch (e) {
            console.error("Metadata load failed", e);
        }
        document.getElementById('from_class')?.addEventListener('change', loadSourceDependencies);
        document.getElementById('from_group')?.addEventListener('change', loadSourceSections);
        document.getElementById('from_section')?.addEventListener('change', loadSourceSessions);
        document.getElementById('from_session')?.addEventListener('change', loadStudentsList);
        document.getElementById('to_class')?.addEventListener('change', loadDestDependencies);
        document.getElementById('to_group')?.addEventListener('change', () => { loadDestSections(); fetchPromoteFeeFromTemplate(); });
        document.getElementById('to_section')?.addEventListener('change', () => { loadDestSessions(); fetchPromoteFeeFromTemplate(); });
        document.getElementById('to_session')?.addEventListener('change', handleToSessionChange);
        document.getElementById('closePromoteModal')?.addEventListener('click', () => {
            window.location.href = '/school/students';
        });
        document.getElementById('promoteForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const feeValue = document.getElementById('promote_fee').value.trim();
            if (!feeValue || feeValue === 'No fee defined' || feeValue === 'Error' || feeValue === 'Loading...' || parseFloat(feeValue) <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Promotion Fee Required',
                    text: 'You cannot proceed because no promotion fee template is defined for this class/session.',
                    confirmButtonColor: '#2563eb'
                });
                return;
            }
            submitPromote();
        });
        document.getElementById('btnCreateFeeTemplatePromote')?.addEventListener('click', () => {
            const classId = document.getElementById('to_class').value;
            const sessionId = document.getElementById('to_session').value;
            if (!classId || !sessionId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Selection Required',
                    text: 'Please select Destination Class and Session first.',
                    confirmButtonColor: '#2563eb'
                });
                return;
            }
            document.getElementById('feeTemplateModal').classList.remove('hidden');
            const titleEl = document.getElementById('feeTemplateModalTitle');
            if (titleEl) titleEl.textContent = 'Create Promote Fee';
            const feeNameInput = document.getElementById('feeNameInput');
            if (feeNameInput) feeNameInput.value = 'Promote Fee';
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
            const classId = document.getElementById('to_class').value;
            const sessionId = document.getElementById('to_session').value;
            const groupId = document.getElementById('to_group').value;
            const sectionId = document.getElementById('to_section').value;
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
                    fee_type_name: 'Promote',
                    fee_name: feeName,
                    amount: amount,
                    pay_date: payDate
                })
                .then(() => {
                    Swal.close();
                    document.getElementById('feeTemplateModal').classList.add('hidden');
                    document.getElementById('feeTemplateForm').reset();
                    handleToSessionChange();
                })
                .catch(err => {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: err.response?.data?.message || 'Failed to create fee template.'
                    });
                });
        });
    });
</script>
