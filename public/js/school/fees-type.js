const feesApi = axios.create({
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    }
});

let currentPage = 1;
let examList = [];
let classesList = [];
let groupsList = [];
let sectionsList = [];

async function preloadData() {
    try {
        const results = await Promise.allSettled([
            feesApi.get('/api/get-school-classes'),
            feesApi.get('/api/get-school-exams'),
            feesApi.get('/api/get-school-groups'),
            feesApi.get('/api/get-school-sections')
        ]);

        classesList = results[0].status === 'fulfilled' ? (results[0].value.data.data || []) : [];
        examList = results[1].status === 'fulfilled' ? (results[1].value.data.data || []) : [];
        groupsList = results[2].status === 'fulfilled' ? (results[2].value.data.data || []) : [];
        sectionsList = results[3].status === 'fulfilled' ? (results[3].value.data.data || []) : [];

        populateDropdown('class_id', classesList, 'id', 'class_name', 'Select Class');
        populateDropdown('exam_id', examList, 'id', 'exam_name', 'Select Exam');
        populateDropdownSelect('classFilter', classesList, 'id', 'class_name', 'All Classes');
        populateDropdownSelect('groupFilter', [], 'id', 'group_name', 'All Groups');
        populateDropdownSelect('sectionFilter', [], 'id', 'section_name', 'All Sections');
        populateDropdownSelect('sessionFilter', [], 'id', 'session_year', 'All Sessions');

        fetchTemplates(1);

    } catch (e) {
        console.error("Data pre-loading failed", e);
        fetchTemplates(1);
    }
}

function populateDropdown(elemId, data, valKey, textKey, defaultText) {
    const s = document.getElementById(elemId);
    if (!s) return;
    let html = `<option value="">${defaultText}</option>`;
    data.forEach(item => html += `<option value="${item[valKey]}">${item[textKey]}</option>`);
    s.innerHTML = html;
}

function attachDropdownSelectOptionListeners(root) {
    if (!root) return;
    const input = root.querySelector('[data-dropdown-select-input]');
    const label = root.querySelector('[data-dropdown-select-label]');
    const menu = root.querySelector('[data-dropdown-select-menu]');
    const button = root.querySelector('[data-dropdown-select-button]');
    const icon = button?.querySelector('i');
    if (!input || !label || !menu || !button) return;

    const setOpen = (open) => {
        root.classList.toggle('is-open', open);
        menu.classList.toggle('hidden', !open);
        button.setAttribute('aria-expanded', String(open));
        icon?.classList.toggle('rotate-180', open);
    };

    menu.querySelectorAll('[data-dropdown-select-option]').forEach((option) => {
        option.addEventListener('click', () => {
            const value = option.dataset.value || '';
            input.value = value;
            label.textContent = option.textContent.trim() || label.dataset.placeholder || '';

            menu.querySelectorAll('[data-dropdown-select-option]').forEach((item) => {
                const isSelected = item === option;
                item.classList.toggle('bg-slate-100', isSelected);
                item.classList.toggle('text-slate-900', isSelected);
                item.classList.toggle('text-slate-800', !isSelected);
                item.setAttribute('aria-selected', String(isSelected));
            });

            setOpen(false);
            input.dispatchEvent(new Event('change', { bubbles: true }));
        });
    });
}

function populateDropdownSelect(elemId, data, valueKey, textKey, defaultText, selectedValue = null) {
    const input = document.getElementById(elemId);
    if (!input) return;
    const root = input.closest('[data-dropdown-select]');
    const menu = root?.querySelector('[data-dropdown-select-menu]');
    const label = root?.querySelector('[data-dropdown-select-label]');
    if (!menu || !label) return;

    const currentValue = selectedValue === null ? input.value : selectedValue;
    let html = `<button type="button" class="dropdown-select-option m-0 flex min-h-6 w-full items-center border-0 bg-white px-3 py-1 text-left text-[11px] font-normal leading-tight transition-colors hover:bg-slate-100 text-slate-800" data-value="" role="option" aria-selected="false" data-dropdown-select-option>${defaultText}</button>`;
    data.forEach(item => {
        const value = item[valueKey] ?? '';
        const display = item[textKey] ?? '';
        const isSelected = String(value) === String(currentValue);
        html += `<button type="button" class="dropdown-select-option m-0 flex min-h-6 w-full items-center border-0 bg-white px-3 py-1 text-left text-[11px] font-normal leading-tight transition-colors hover:bg-slate-100 ${isSelected ? 'bg-slate-100 text-slate-900' : 'text-slate-800'}" data-value="${value}" role="option" aria-selected="${isSelected}" data-dropdown-select-option>${display}</button>`;
    });

    menu.innerHTML = html;
    const selectedItem = currentValue ? data.find(item => String(item[valueKey]) === String(currentValue)) : null;
    input.value = currentValue ? currentValue : '';
    label.textContent = currentValue
        ? (selectedItem ? selectedItem[textKey] : defaultText)
        : (label.dataset.placeholder || defaultText);

    attachDropdownSelectOptionListeners(root);
}

async function handleFilterClassChange(isInitial = false) {
    const classId = document.getElementById('classFilter').value;
    const filteredGroups = groupsList.filter(g => g.class_id == classId);
    const currentGroupId = document.getElementById('groupFilter').value;
    const preserveGroupId = filteredGroups.some(g => String(g.id) === String(currentGroupId)) ? currentGroupId : '';
    const selectedGroupId = preserveGroupId || (isInitial && filteredGroups.length > 0 ? filteredGroups[0].id : '');
    populateDropdownSelect('groupFilter', filteredGroups, 'id', 'group_name', 'All Groups', selectedGroupId);
    await handleFilterGroupChange(isInitial);
}

async function handleFilterGroupChange(isInitial = false) {
    const groupId = document.getElementById('groupFilter').value;
    const filteredSections = groupId ? sectionsList.filter(sec => sec.group_id == groupId) : [];
    const currentSectionId = document.getElementById('sectionFilter').value;
    const preserveSectionId = filteredSections.some(sec => String(sec.id) === String(currentSectionId)) ? currentSectionId : '';
    const selectedSectionId = preserveSectionId || (isInitial && filteredSections.length > 0 ? filteredSections[0].id : '');
    populateDropdownSelect('sectionFilter', filteredSections, 'id', 'section_name', 'All Sections', selectedSectionId);
    await handleFilterSectionChange(isInitial);
}

async function handleFilterSectionChange(isInitial = false) {
    const classId = document.getElementById('classFilter').value;
    const groupId = document.getElementById('groupFilter').value;
    const sectionId = document.getElementById('sectionFilter').value;
    if (!classId) {
        populateDropdownSelect('sessionFilter', [], 'id', 'session_year', 'All Sessions');
        if (isInitial) fetchTemplates(1);
        return;
    }
    try {
        const res = await feesApi.get('/api/get-school-sessions', {
            params: {
                class_id: classId,
                group_id: groupId,
                section_id: sectionId
            }
        });
        const sessions = res.data.data || [];
        const selectedSessionId = isInitial && sessions.length > 0 ? sessions[0].id : '';
        populateDropdownSelect('sessionFilter', sessions, 'id', 'session_year', 'All Sessions', selectedSessionId);
    } catch (e) {
        console.error(e);
    }
    if (isInitial) fetchTemplates(1);
}

function checkAcademicFieldsReady() {
    const classVal = document.getElementById('class_id').value;
    const sessionVal = document.getElementById('session_id').value;
    return classVal && sessionVal;
}

function toggleFeeTypeEnabled() {
    const feeType = document.getElementById('fee_type_name');
    const ready = checkAcademicFieldsReady();
    feeType.disabled = !ready;
    feeType.style.opacity = ready ? '1' : '0.5';
    feeType.style.cursor = ready ? 'pointer' : 'not-allowed';
    if (!ready) {
        feeType.value = '';
        document.getElementById('fine_fee_name_select').classList.add('hidden');
        document.getElementById('fee_name_input').classList.remove('hidden');
        const feeWrapper3 = document.getElementById('fee_name_wrapper');
        const fineWrapper3 = document.getElementById('fine_fee_wrapper');
        if (feeWrapper3) feeWrapper3.classList.remove('hidden');
        if (fineWrapper3) fineWrapper3.classList.add('hidden');
        const feeWrapper = document.getElementById('fee_name_wrapper');
        const fineWrapper = document.getElementById('fine_fee_wrapper');
        if (feeWrapper) feeWrapper.classList.remove('hidden');
        if (fineWrapper) fineWrapper.classList.add('hidden');
        resetFoodFields();
        ['div_fee_name', 'div_exam_name', 'div_pay_date', 'div_due_day', 'div_food_type', 'div_food_students', 'div_amount']
            .forEach(f => { const el = document.getElementById(f); if (el) el.style.display = 'none'; });
        syncFineLabels();
    }
}

function getSessionYear() {
    const sessionSelect = document.getElementById('session_id');
    const selectedOption = sessionSelect.options[sessionSelect.selectedIndex];
    return selectedOption ? selectedOption.textContent.trim() : '';
}

// ---------------------------------------------------------------------------
// Modernized: data-driven config replaces the long if/else-if chain.
// Adding a new fee type suffix now only requires a new entry in FEE_TYPE_CONFIG.
// ---------------------------------------------------------------------------
const FEE_TYPE_CONFIG = {
    Admission: { suffix: 'Admission fee', readOnly: true,  forceUpdate: true },
    Tuition:   { suffix: 'Tuition fee',   readOnly: false, forceUpdate: false },
    Food:      { suffix: 'Food fee',      readOnly: false, forceUpdate: false },
    Session:   { suffix: 'Session fee',   readOnly: false, forceUpdate: false },
    Promote:   { suffix: 'Promote fee',   readOnly: false, forceUpdate: false }, // Modified on 2026-07-07: Handle Promote fee naming logic
};

function updateFeeNameFromSession() {
    const type = document.getElementById('fee_type_name').value;
    const sessionYear = getSessionYear();
    const feeNameInput = document.getElementById('fee_name_input');
    const config = FEE_TYPE_CONFIG[type];

    // Handles Admission / Tuition / Food / Session / Promote
    if (config && sessionYear) {
        const newValue = `${sessionYear} ${config.suffix}`;
        const shouldUpdate = config.forceUpdate
            || !feeNameInput.value
            || feeNameInput.value.endsWith(` ${config.suffix}`);

        if (shouldUpdate) feeNameInput.value = newValue;
        feeNameInput.readOnly = config.readOnly;
        return;
    }

    // Fine is a special case: it applies even without a sessionYear
    if (type === 'Fine') {
        const suffix = ' Late fee';
        if (!feeNameInput.value || feeNameInput.value.endsWith(suffix)) {
            feeNameInput.value = sessionYear ? sessionYear + suffix : 'Late Fee';
        }
        feeNameInput.readOnly = false;
        return;
    }

    // Any other type (that isn't Admission) unlocks the input
    if (type !== 'Admission') {
        feeNameInput.readOnly = false;
    }
}

function loadGroups(selectedGroupId = null) {
    const classId = document.getElementById('class_id').value;

    if (!selectedGroupId) {
        document.getElementById('group_id').innerHTML = '<option value="">No Group</option>';
        document.getElementById('section_id').innerHTML = '<option value="">No Section</option>';
        document.getElementById('session_id').innerHTML = '<option value="">Select Session</option>';
        document.getElementById('fee_type_name').value = '';
        document.getElementById('fee_name_input').value = '';
        document.getElementById('amount').value = '';
        document.getElementById('pay_date').value = '';
        document.getElementById('exam_id').innerHTML = '<option value="">Select Exam</option>';
        document.getElementById('fine_fee_name_select').classList.add('hidden');
        document.getElementById('fee_name_input').classList.remove('hidden');
        const feeWrapper2 = document.getElementById('fee_name_wrapper');
        const fineWrapper2 = document.getElementById('fine_fee_wrapper');
        if (feeWrapper2) feeWrapper2.classList.remove('hidden');
        if (fineWrapper2) fineWrapper2.classList.add('hidden');
        resetFoodFields();
        ['div_fee_name', 'div_exam_name', 'div_pay_date', 'div_due_day', 'div_food_type', 'div_food_students', 'div_amount']
            .forEach(f => { const el = document.getElementById(f); if (el) el.style.display = 'none'; });
        syncFineLabels();
    }

    const s = document.getElementById('group_id');
    let html = '<option value="">No Group</option>';
    groupsList.filter(g => g.class_id == classId).forEach(g => {
        html += `<option value="${g.id}" ${selectedGroupId == g.id ? 'selected' : ''}>${g.group_name}</option>`;
    });
    s.innerHTML = html;
    loadSections();
}

function loadSections(selectedSectionId = null) {
    const groupId = document.getElementById('group_id').value;
    const s = document.getElementById('section_id');
    let html = '<option value="">No Section</option>';
    if (groupId) {
        sectionsList.filter(sec => sec.group_id == groupId).forEach(sec => {
            html += `<option value="${sec.id}" ${selectedSectionId == sec.id ? 'selected' : ''}>${sec.section_name}</option>`;
        });
    }
    s.innerHTML = html;
    loadSessions();
}

async function loadSessions(selectedSessionId = null) {
    const classId = document.getElementById('class_id').value;
    const groupId = document.getElementById('group_id').value;
    const sectionId = document.getElementById('section_id').value;
    const s = document.getElementById('session_id');

    if (!classId) {
        s.innerHTML = '<option value="">Select Session</option>';
        return;
    }

    try {
        const res = await feesApi.get('/api/get-school-sessions', {
            params: {
                class_id: classId,
                group_id: groupId,
                section_id: sectionId
            }
        });
        const sessions = res.data.data || [];
        let html = '<option value="">Select Session</option>';
        sessions.forEach(sess => {
            html += `<option value="${sess.id}" ${selectedSessionId == sess.id ? 'selected' : ''}>${sess.session_year}</option>`;
        });
        s.innerHTML = html;
    } catch (e) {
        console.error("Session load failed", e);
    }
    toggleFeeTypeEnabled();
    updateFeeNameFromSession();
}

document.getElementById('session_id').addEventListener('change', function() {
    toggleFeeTypeEnabled();
    updateFeeNameFromSession();
});

function fetchTemplates(page = 1) {
    currentPage = page;
    const search = document.getElementById('feeSearch').value;
    const filters = {
        class_id: document.getElementById('classFilter').value,
        group_id: document.getElementById('groupFilter').value,
        section_id: document.getElementById('sectionFilter').value,
        session_id: document.getElementById('sessionFilter').value,
        search: search,
        page: page
    };

    feesApi.get('/api/fee-templates', { params: filters })
        .then(res => {
            const meta = res.data;
            const items = res.data.data || [];

            const tbody = document.getElementById('feeTableBody');

            if (items.length === 0) {
                tbody.innerHTML =
                    '<tr><td colspan="10" class="text-center py-6 text-gray-400">No fee templates found.</td></tr>';
                renderPagination(meta);
                return;
            }

            function payDateDisplay(item) {
                const monthlyTypes = ['Tuition', 'Fine', 'Food'];
                if (monthlyTypes.includes(item.fee_type_name) && item.due_day) {
                    return `Every Month ${item.due_day}-day`;
                }
                if (item.pay_date) {
                    return formatDateWithMonth(item.pay_date);
                }
                return '---';
            }

            let html = '';
            items.forEach((item, index) => {
                let detailName = item.fee_name || item.fee_type_name || 'N/A';

                html += `
                <tr class="hover:bg-slate-50 transition-colors text-[11px]">
                    <td>${(meta.from || 0) + index}</td>
                    <td class="text-gray-700">${item.school_class?.class_name || 'N/A'}</td>
                    <td class="text-gray-600">${item.school_group?.group_name || 'N/A'}</td>
                    <td class="text-gray-600">${item.school_section?.section_name || 'N/A'}</td>
                    <td class="text-gray-600">${item.school_session?.session_year || 'N/A'}</td>
                    <td class="text-gray-600">${item.fee_type_name}</td>
                    <td class="text-gray-600">${detailName}</td>
                    <td class="text-gray-700">${parseFloat(item.amount).toFixed(2)}</td>
                    <td class="text-gray-500">${payDateDisplay(item)}</td>
                    <td class="text-center">
                        <div class="flex justify-center gap-3">
                            <button onclick="generateFees(${item.id}, event)" class="action-icon-btn text-green-500" title="Generate Fees"><i class="fas fa-play" style="font-size: 13px;"></i></button>
                            <button onclick="editTemplate(${item.id})" class="action-icon-btn text-blue-500"><i class="far fa-edit" style="font-size: 15px;"></i></button>
                            <button onclick="deleteTemplate(${item.id})" class="action-icon-btn text-red-400"><i class="far fa-trash-alt" style="font-size: 15px;"></i></button>
                        </div>
                    </td>
                </tr>`;
            });
            tbody.innerHTML = html;
            renderPagination(meta);
        }).catch(() => {
            document.getElementById('feeTableBody').innerHTML = '<tr><td colspan="11" class="text-center py-6 text-red-400">Error loading data.</td></tr>';
        });
}

function getFrequencyForType(type) {
    if (['Tuition', 'Monthly', 'Food', 'Fine'].includes(type)) return 'monthly';
    if (type === 'Exams') return 'per_exam';
    return 'one_time';
}

function populateDueDaySelect() {
    const select = document.getElementById('due_day');
    let html = '<option value="">Select Day</option>';
    for (let i = 1; i <= 31; i++) {
        html += `<option value="${i}">Every Month ${i}-day</option>`;
    }
    select.innerHTML = html;
}

function resetFoodFields() {
    document.getElementById('food_type').value = '';
    document.getElementById('food_student_ids').innerHTML = '';
    document.getElementById('food_student_count').classList.add('hidden');
}

function handleFineFeeName() {
    const type = document.getElementById('fee_type_name').value;
    const input = document.getElementById('fee_name_input');
    const select = document.getElementById('fine_fee_name_select');
    const feeWrapper = document.getElementById('fee_name_wrapper');
    const fineWrapper = document.getElementById('fine_fee_wrapper');
    // For 'Fine': show both boxes and clear the select. Otherwise: input only.
    const isFine = type === 'Fine';
    input ? input.classList.remove('hidden') : null;
    select ? select.classList.toggle('hidden', !isFine) : null;
    feeWrapper ? feeWrapper.classList.remove('hidden') : null;
    fineWrapper ? fineWrapper.classList.toggle('hidden', !isFine) : null;
    select ? (select.value = isFine ? '' : select.value) : null;

    syncFineLabels();
}

// Ensure labels show/hide when toggling between fee name input and fine select
function syncFineLabels() {
    const inputLabel = document.querySelector('label[for="fee_name_input"]');
    const selectLabel = document.querySelector('label[for="fine_fee_name_select"]');
    const input = document.getElementById('fee_name_input');
    const select = document.getElementById('fine_fee_name_select');

    const inputVisible = input && !input.classList.contains('hidden');
    const selectVisible = select && !select.classList.contains('hidden');

    // Ternary picks the classList method ('add' or 'remove') to call for each label.
    selectLabel && selectLabel.classList[selectVisible ? 'remove' : 'add']('hidden');
    inputLabel && inputLabel.classList[(selectVisible && !inputVisible) ? 'add' : 'remove']('hidden');
}

// Initial sync on load
document.readyState === 'loading'
    ? document.addEventListener('DOMContentLoaded', syncFineLabels)
    : syncFineLabels();

async function loadFoodStudents() {
    const classId = document.getElementById('class_id').value;
    const groupId = document.getElementById('group_id').value;
    const sectionId = document.getElementById('section_id').value;
    const sessionId = document.getElementById('session_id').value;

    if (!classId || !sessionId) return;

    try {
        const res = await feesApi.get('/api/school/students', {
            params: {
                class_id: classId,
                group_id: groupId || null,
                section_id: sectionId || null,
                session_id: sessionId,
                all: true
            }
        });
        const students = Array.isArray(res.data) ? res.data : res.data.data || [];
        const select = document.getElementById('food_student_ids');
        let html = '';
        students.forEach(s => {
            html += `<option value="${s.id}">${s.student_name} (${s.student_id_number || ''})</option>`;
        });
        select.innerHTML = html;

        if (document.getElementById('food_type').value === 'all') {
            select.querySelectorAll('option').forEach(o => o.selected = true);
            updateFoodStudentCount();
        }
    } catch (e) {
        console.error("Failed to load students for food fee", e);
    }
}

function updateFoodStudentCount() {
    const select = document.getElementById('food_student_ids');
    const count = select.selectedOptions.length;
    const countDiv = document.getElementById('food_student_count');
    if (count > 0) {
        countDiv.textContent = count + ' student(s) selected';
        countDiv.classList.remove('hidden');
    } else {
        countDiv.classList.add('hidden');
    }
}

function handleFoodTypeChange() {
    const type = document.getElementById('food_type').value;
    const select = document.getElementById('food_student_ids');
    const countDiv = document.getElementById('food_student_count');
    const container = document.getElementById('div_food_students');

    const isAll = type === 'all';
    const isPicker = type === 'single' || type === 'multiple';

    container.style.display = (isAll || isPicker) ? 'block' : 'none';
    select.style.display = isPicker ? 'block' : 'none';

    isPicker && (type === 'multiple'
        ? select.setAttribute('multiple', 'multiple')
        : select.removeAttribute('multiple'));

    (isAll || isPicker) && select.querySelectorAll('option').forEach(o => o.selected = isAll);

    isAll ? updateFoodStudentCount() : countDiv.classList.add('hidden');
}

function handleFeeTypeChange() {
    const type = document.getElementById('fee_type_name').value;
    const fields = ['div_fee_name', 'div_exam_name', 'div_pay_date', 'div_due_day', 'div_food_type', 'div_food_students'];
    fields.forEach(f => {
        const el = document.getElementById(f);
        if (el) el.style.display = 'none';
    });

    if (!type) return;

    document.getElementById('fee_name_input').readOnly = false;
    document.getElementById('fee_name_input').placeholder = 'Monthly Tuition Fee';
    document.getElementById('pay_date').readOnly = false;
    resetFoodFields();
    handleFineFeeName();

    const frequency = getFrequencyForType(type);
    document.getElementById('frequency').value = frequency;

    if (frequency === 'monthly') {
        document.getElementById('div_fee_name').style.display = 'block';
        document.getElementById('div_due_day').style.display = 'block';
        document.getElementById('div_pay_date').style.display = 'none';
        document.getElementById('due_day_label').textContent = 'Every Month Payment Day';

        if (type === 'Food') {
            document.getElementById('div_food_type').style.display = 'block';
            document.getElementById('fee_name_input').placeholder = 'Monthly Food Fee';
            updateFeeNameFromSession();
            loadFoodStudents();
        } else if (type === 'Tuition') {
            updateFeeNameFromSession();
        } else if (type === 'Fine') {
            document.getElementById('fee_name_input').placeholder = 'Late Fee';
            updateFeeNameFromSession();
        }
    } else if (frequency === 'one_time') {
        document.getElementById('div_fee_name').style.display = 'block';
        document.getElementById('div_pay_date').style.display = 'block';
        document.getElementById('div_due_day').style.display = 'none';
        document.getElementById('pay_date_label').textContent = 'Last Payment Date';
        const today = new Date();
        const y = today.getFullYear();
        const m = String(today.getMonth() + 1).padStart(2, '0');
        const d = String(today.getDate()).padStart(2, '0');
        document.getElementById('pay_date').value = `${y}-${m}-${d}`;
        if (type === 'Admission') {
            document.getElementById('div_exam_name').style.display = 'none';
            document.getElementById('fee_name_input').placeholder = '2025-2026 Admission fee';
            updateFeeNameFromSession();
        } else if (type === 'Session') {
            document.getElementById('fee_name_input').placeholder = sessionYearText() + ' Session fee';
            updateFeeNameFromSession();
        } else if (type === 'Promote') { // Modified on 2026-07-07: Handle Promote fee type placeholder
            document.getElementById('fee_name_input').placeholder = sessionYearText() + ' Promote fee';
            updateFeeNameFromSession();
        }
    } else if (frequency === 'per_exam') {
        document.getElementById('div_exam_name').style.display = 'block';
        document.getElementById('div_pay_date').style.display = 'block';
        document.getElementById('div_due_day').style.display = 'none';
        document.getElementById('pay_date_label').textContent = 'Last Payment Date';
        document.getElementById('pay_date').value = '';
        document.getElementById('pay_date').readOnly = true;
        document.getElementById('fee_name_input').placeholder = 'Auto-set from selected exam';
        document.getElementById('fee_name_input').value = '';
        loadFilteredExams();
    }

    document.getElementById('div_amount').style.display = 'block';
}

function sessionYearText() {
    const sessionSelect = document.getElementById('session_id');
    const opt = sessionSelect.options[sessionSelect.selectedIndex];
    return opt ? opt.textContent.trim() : '';
}

async function loadFilteredExams(selectedExamId = null) {
    const classId = document.getElementById('class_id').value;
    const groupId = document.getElementById('group_id').value;
    const sectionId = document.getElementById('section_id').value;
    const sessionId = document.getElementById('session_id').value;

    const examSelect = document.getElementById('exam_id');
    examSelect.innerHTML = '<option value="">Loading...</option>';

    const classObj = classesList.find(c => c.id == classId);
    const groupObj = groupsList.find(g => g.id == groupId);
    const sectionObj = sectionsList.find(s => s.id == sectionId);

    const sessionOption = document.querySelector(`#session_id option[value="${sessionId}"]`);
    const sessionName = sessionOption ? sessionOption.textContent.trim() : '';

    const params = {};
    if (classObj) params.class_name = classObj.class_name;
    if (groupObj) params.group_name = groupObj.group_name;
    if (sectionObj) params.section_name = sectionObj.section_name;
    if (sessionName) params.session_name = sessionName;

    try {
        const res = await feesApi.get('/api/get-school-exams', { params });
        const exams = res.data.data || [];

        let html = '<option value="">Select Exam</option>';
        if (exams.length === 0) {
            html = '<option value="">No exams found for this class/session</option>';
        } else {
            exams.forEach(ex => {
                html += `<option value="${ex.id}" ${selectedExamId == ex.id ? 'selected' : ''}>${ex.exam_name}</option>`;
            });
        }
        examSelect.innerHTML = html;
    } catch (e) {
        console.error('Exam load failed', e);
        examSelect.innerHTML = '<option value="">Failed to load exams</option>';
    }
}

async function handleExamDateFromRoutine() {
    const examId = document.getElementById('exam_id').value;
    if (!examId) {
        document.getElementById('pay_date').value = '';
        return;
    }

    const classId = document.getElementById('class_id').value;
    const groupId = document.getElementById('group_id').value;
    const sectionId = document.getElementById('section_id').value;
    const sessionId = document.getElementById('session_id').value;

    const classObj = classesList.find(c => c.id == classId);
    const groupObj = groupsList.find(g => g.id == groupId);
    const sectionObj = sectionsList.find(s => s.id == sectionId);

    const sessionOption = document.querySelector(`#session_id option[value="${sessionId}"]`);
    const sessionName = sessionOption ? sessionOption.textContent.trim() : '';

    const examOption = document.querySelector(`#exam_id option[value="${examId}"]`);
    const examName = examOption ? examOption.textContent.trim() : '';

    const params = {};
    if (classObj) params.class_name = classObj.class_name;
    if (groupObj) params.group_name = groupObj.group_name;
    if (sectionObj) params.section_name = sectionObj.section_name;
    if (sessionName) params.session_name = sessionName;
    if (examName) params.exam_name = examName;
    params.per_page = 1;

    try {
        const res = await feesApi.get('/api/school-exam-routines', { params });
        const routinesData = res.data.data || [];
        if (routinesData.length > 0) {
            const examDate = routinesData[0].exam_date;
            if (examDate) {
                const d = new Date(examDate);
                d.setDate(d.getDate() - 1);
                const year = d.getFullYear();
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                document.getElementById('pay_date').value = `${year}-${month}-${day}`;
            } else {
                document.getElementById('pay_date').value = '';
                Swal.fire({
                    title: 'Exam Routine Required',
                    text: 'Please create the exam routine first, then select an exam with a scheduled date.',
                    icon: 'warning',
                    confirmButtonColor: '#2563eb'
                });
            }
        } else {
            document.getElementById('pay_date').value = '';
            Swal.fire({
                title: 'Exam Routine Required',
                text: 'Please create the exam routine first, then select an exam with a scheduled date.',
                icon: 'warning',
                confirmButtonColor: '#2563eb'
            });
        }
    } catch (e) {
        console.error("Exam routine fetch failed", e);
        document.getElementById('pay_date').value = '';
        Swal.fire({
            title: 'Exam Routine Required',
            text: 'Please create the exam routine first, then select an exam with a scheduled date.',
            icon: 'warning',
            confirmButtonColor: '#2563eb'
        });
    }
}

document.getElementById('exam_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    if (selectedOption && selectedOption.value) {
        document.getElementById('fee_name_input').value = selectedOption.textContent.trim();
    }
    handleExamDateFromRoutine();
});

document.getElementById('feeForm').onsubmit = function(e) {
    e.preventDefault();
    const rawId = document.getElementById('fee_id').value;
    const saveBtn = document.getElementById('saveBtn');
    const targetClassId = document.getElementById('class_id').value;

    const feeType = document.getElementById('fee_type_name').value;
    let feeName = document.getElementById('fee_name_input').value || null;
    if (feeType === 'Fine') {
        feeName = document.getElementById('fine_fee_name_select').value || null;
    }

    const studentIdsSelect = document.getElementById('food_student_ids');
    const selectedStudentIds = Array.from(studentIdsSelect.selectedOptions).map(o => o.value);

    const data = {
        class_id: targetClassId,
        group_id: document.getElementById('group_id').value || null,
        section_id: document.getElementById('section_id').value || null,
        session_id: document.getElementById('session_id').value,
        fee_type_name: feeType,
        frequency: document.getElementById('frequency').value,
        fee_name: feeName,
        exam_id: document.getElementById('exam_id').value || null,
        due_day: document.getElementById('due_day').value || null,
        amount: document.getElementById('amount').value,
        pay_date: document.getElementById('pay_date').value,
        food_type: document.getElementById('food_type').value || null,
        student_ids: selectedStudentIds.length > 0 ? selectedStudentIds : null,
    };

    if (data.fee_type_name === 'Exams' && !data.pay_date) {
        Swal.fire({
            title: 'Exam Routine Required',
            text: 'Please create the exam routine first, then select an exam with a scheduled date.',
            icon: 'warning',
            confirmButtonColor: '#2563eb'
        });
        saveBtn.disabled = false;
        return;
    }

    saveBtn.disabled = true;

    const req = rawId
        ? feesApi.put(`/api/fee-templates/${rawId}`, data)
        : feesApi.post('/api/fee-templates', data);

    req.then((res) => {
        Toastify({
            text: res.data.message || "Data Saved Successfully",
            style: { background: "#10b981" }
        }).showToast();
        closeFeeModal();
        document.getElementById('classFilter').value = targetClassId;
        handleFilterClassChange(false).then(() => fetchTemplates(1));
    }).catch(err => {
        const errors = err.response?.data?.errors;
        const message = err.response?.data?.message || 'Verification failed';
        if (errors) {
            const formatted = Object.values(errors).flat().join('<br>');
            Swal.fire({
                title: 'Error',
                html: formatted,
                icon: 'error'
            });
        } else {
            Swal.fire('Error', message, 'error');
        }
    }).finally(() => saveBtn.disabled = false);
};

async function editTemplate(id) {
    try {
        const res = await feesApi.get(`/api/fee-templates/${id}`);
        const item = res.data;
        document.getElementById('feeForm').reset();
        document.getElementById('fee_id').value = item.id;
        const modalTitle = document.getElementById('feeModalTitle');
        if (modalTitle) {
            modalTitle.innerText = 'Edit Fee Template';
        }
        document.getElementById('class_id').value = item.class_id || '';

        loadGroups(item.group_id);
        loadSections(item.section_id);
        await loadSessions(item.session_id);

        document.getElementById('fee_type_name').value = item.fee_type_name || '';
        document.getElementById('amount').value = item.amount || '';
        document.getElementById('pay_date').value = toDateInputValue(item.pay_date);
        document.getElementById('fee_name_input').value = item.fee_name || '';
        document.getElementById('due_day').value = item.due_day || '';

        document.getElementById('fee_name_input').readOnly = false;

        const frequency = item.frequency || getFrequencyForType(item.fee_type_name);
        document.getElementById('frequency').value = frequency;
        document.getElementById('fine_fee_name_select').classList.add('hidden');
        document.getElementById('fee_name_input').classList.remove('hidden');

        if (item.fee_type_name === 'Exams' || frequency === 'per_exam') {
            document.getElementById('fee_name_input').readOnly = false;
            ['div_fee_name', 'div_exam_name', 'div_pay_date', 'div_due_day', 'div_food_type', 'div_food_students'].forEach(f => {
                const el = document.getElementById(f);
                if (el) el.style.display = 'none';
            });
            document.getElementById('div_exam_name').style.display = 'block';
            document.getElementById('div_amount').style.display = 'block';
            document.getElementById('div_pay_date').style.display = 'block';
            document.getElementById('pay_date_label').textContent = 'Last Payment Date';
            document.getElementById('pay_date').readOnly = true;
            await loadFilteredExams(item.exam_id);
            document.getElementById('exam_id').dispatchEvent(new Event('change'));
        } else if (frequency === 'monthly') {
            ['div_fee_name', 'div_exam_name', 'div_pay_date', 'div_due_day', 'div_food_type', 'div_food_students'].forEach(f => {
                const el = document.getElementById(f);
                if (el) el.style.display = 'none';
            });
            document.getElementById('div_fee_name').style.display = 'block';
            document.getElementById('div_amount').style.display = 'block';
            document.getElementById('div_due_day').style.display = 'block';
            document.getElementById('div_pay_date').style.display = 'none';
            document.getElementById('due_day_label').textContent = 'Every Month Payment Day';

            if (item.fee_type_name === 'Food') {
                document.getElementById('div_food_type').style.display = 'block';
                await loadFoodStudents();
                document.getElementById('food_type').value = item.food_type || '';
                handleFoodTypeChange();
                updateFeeNameFromSession();
                const storedIds = item.student_ids || [];
                if (storedIds.length > 0) {
                    const opts = document.getElementById('food_student_ids').options;
                    for (let i = 0; i < opts.length; i++) {
                        if (storedIds.includes(String(opts[i].value)) || storedIds.includes(opts[i].value)) {
                            opts[i].selected = true;
                        }
                    }
                    updateFoodStudentCount();
                }
            } else if (item.fee_type_name === 'Fine') {
                document.getElementById('fine_fee_name_select').value = item.fee_name || '';
                handleFineFeeName();
            } else if (item.fee_type_name === 'Tuition') {
                updateFeeNameFromSession();
            }
        } else {
            handleFeeTypeChange();
            item.pay_date && (document.getElementById('pay_date').value = toDateInputValue(item.pay_date));
            if (item.fee_type_name === 'Fine') {
                document.getElementById('fine_fee_name_select').value = item.fee_name || '';
                handleFineFeeName();
            }
        }

        document.getElementById('feeModal').classList.remove('hidden');
    } catch (e) {
        Swal.fire('Error', 'Failed to fetch record.', 'error');
    }
}

const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

// Formats a date string for a `<input type="date">` value (YYYY-MM-DD).
// Replaces the previous nested if/else in editTemplate with ternaries.
function toDateInputValue(dateString) {
    if (!dateString) return '';
    const d = new Date(dateString);
    return !isNaN(d.getTime())
        ? `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`
        : dateString.split('T')[0];
}

function formatDateWithMonth(dateString) {
    if (!dateString) return '---';
    const d = new Date(dateString);
    if (isNaN(d.getTime())) return '---';
    return `${d.getDate()}-${monthNames[d.getMonth()]}-${d.getFullYear()}`;
}

function generateFees(id, e) {
    const btn = e?.target?.closest?.('button');
    if (btn) {
        if (btn.dataset.processing === 'true') return;
        btn.dataset.processing = 'true';
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    }

    const resetBtn = () => {
        if (btn) {
            btn.dataset.processing = 'false';
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-play" style="font-size: 13px;"></i>';
        }
    };

    Swal.fire({
        title: 'Generate student fee records?',
        text: 'This will queue fee generation for this template. Existing records will be skipped.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        confirmButtonText: 'Yes, generate'
    }).then((result) => {
        result.isConfirmed
            ? feesApi.post(`/api/fee-templates/${id}/generate-fees`).then(res => {
                resetBtn();
                Toastify({
                    text: res.data.message || 'Fee generation queued.',
                    style: { background: "#10b981" }
                }).showToast();
            }).catch(err => {
                resetBtn();
                err.response?.status === 429
                    ? Swal.fire('Already Queued', err.response.data.message || 'This template is already being processed.', 'info')
                    : Swal.fire('Error', err.response?.data?.message || 'Failed to queue.', 'error');
            })
            : resetBtn();
    });
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const d = new Date(dateString);
    if (!isNaN(d.getTime())) {
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${day}/${month}/${year}`;
    }
    const parts = dateString.split('T')[0];
    if (!parts) return dateString;
    const [year, month, day] = parts.split('-');
    return (year && month && day) ? `${day}/${month}/${year}` : dateString;
}

function deleteTemplate(id) {
    Swal.fire({
        title: 'Delete this fee template?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        confirmButtonText: 'Yes, delete it'
    }).then((result) => {
        if (result.isConfirmed) {
            feesApi.delete(`/api/fee-templates/${id}`).then(() => {
                Toastify({
                    text: "Deleted Successfully",
                    style: { background: "#ef4444" }
                }).showToast();
                fetchTemplates(currentPage);
            });
        }
    });
}

function renderPagination(meta) {
    const info = document.getElementById('paginationInfo');
    const controls = document.getElementById('paginationControls');
    info.innerText = `${meta.to || 0} of ${meta.total || 0}`;
    controls.innerHTML = '';
    if (!meta.total || meta.total === 0) return;

    const createBtn = (content, page, active = false, disabled = false) => {
        const btn = document.createElement('button');
        btn.className = `pagination-btn ${active ? 'active' : ''}`;
        btn.innerHTML = content;
        if (disabled) btn.disabled = true;
        if (!disabled && !active) btn.onclick = () => fetchTemplates(page);
        return btn;
    };

    const btnGroup = document.createElement('div');
    btnGroup.className = 'flex items-center gap-1';
    btnGroup.appendChild(createBtn('<i class="mdi mdi-chevron-left text-lg"></i>', meta.current_page - 1, false, meta.current_page === 1));

    let startPage = Math.max(1, meta.current_page - 2);
    let endPage = Math.min(meta.last_page, startPage + 4);
    if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);

    for (let i = startPage; i <= endPage; i++) {
        btnGroup.appendChild(createBtn(i, i, i === meta.current_page));
    }

    btnGroup.appendChild(createBtn('<i class="mdi mdi-chevron-right text-lg"></i>', meta.current_page + 1, false,
        meta.current_page === meta.last_page));
    controls.appendChild(btnGroup);
}

function openFeeModal() {
    document.getElementById('feeForm').reset();
    document.getElementById('fee_id').value = '';
    const modalTitle = document.getElementById('feeModalTitle');
    if (modalTitle) {
        modalTitle.innerText = 'Add New Fee Template';
    }
    document.getElementById('group_id').innerHTML = '<option value="">No Group</option>';
    document.getElementById('section_id').innerHTML = '<option value="">No Section</option>';
    document.getElementById('session_id').innerHTML = '<option value="">Select Session</option>';
    document.getElementById('exam_id').innerHTML = '<option value="">Select Exam</option>';
    document.getElementById('pay_date').value = '';
    document.getElementById('fee_name_input').value = '';
    document.getElementById('fee_name_input').readOnly = false;
    document.getElementById('fee_name_input').classList.remove('hidden');
    document.getElementById('fine_fee_name_select').classList.add('hidden');
    const feeWrapper4 = document.getElementById('fee_name_wrapper');
    const fineWrapper4 = document.getElementById('fine_fee_wrapper');
    if (feeWrapper4) feeWrapper4.classList.remove('hidden');
    if (fineWrapper4) fineWrapper4.classList.add('hidden');
    resetFoodFields();
    populateDueDaySelect();
    ['div_fee_name', 'div_exam_name', 'div_pay_date', 'div_due_day', 'div_food_type', 'div_food_students', 'div_amount']
        .forEach(f => { const el = document.getElementById(f); if (el) el.style.display = 'none'; });
    toggleFeeTypeEnabled();
    document.getElementById('feeModal').classList.remove('hidden');
}

function closeFeeModal() {
    document.getElementById('feeModal').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('btnFilter').addEventListener('click', () => document.getElementById(
        'filterModal').classList.remove('hidden'));
    document.getElementById('applyFilter').addEventListener('click', () => {
        fetchTemplates(1);
        document.getElementById('filterModal').classList.add('hidden');
    });
    document.getElementById('resetFilter').addEventListener('click', () => {
        document.getElementById('classFilter').value = '';
        populateDropdownSelect('groupFilter', [], 'id', 'group_name', 'All Groups');
        populateDropdownSelect('sectionFilter', [], 'id', 'section_name', 'All Sections');
        populateDropdownSelect('sessionFilter', [], 'id', 'session_year', 'All Sessions');
        fetchTemplates(1);
        document.getElementById('filterModal').classList.add('hidden');
    });

    document.getElementById('btnExport').addEventListener('click', () => document.getElementById('exportModal').classList.remove('hidden'));
    document.getElementById('closeExport').addEventListener('click', () => document.getElementById('exportModal').classList.add('hidden'));

    const closeFeeModalBtn = document.getElementById('closeFeeModal');
    if (closeFeeModalBtn) {
        closeFeeModalBtn.addEventListener('click', () => closeFeeModal());
    }

    document.getElementById('classFilter').addEventListener('change', () => handleFilterClassChange(false));
    document.getElementById('groupFilter').addEventListener('change', () => handleFilterGroupChange(false));
    document.getElementById('sectionFilter').addEventListener('change', () => handleFilterSectionChange(false));

    document.getElementById('class_id').addEventListener('change', () => loadGroups());
    document.getElementById('group_id').addEventListener('change', () => loadSections());
    document.getElementById('section_id').addEventListener('change', () => loadSessions());
    document.getElementById('session_id').addEventListener('change', () => updateFeeNameFromSession());
    document.getElementById('fee_type_name').addEventListener('change', () => handleFeeTypeChange());

    document.getElementById('feeSearch').addEventListener('input', () => fetchTemplates(1));

    document.getElementById('food_student_ids').addEventListener('change', function() {
        if (document.getElementById('food_type').value !== 'all') {
            updateFoodStudentCount();
        }
    });

    populateDueDaySelect();

    window.onclick = function(event) {
        if (event.target.classList.contains('premium-modal')) event.target.classList.add('hidden');
    };
});

preloadData();