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
            @include('school.fees.discount.partials.header')
            @include('school.fees.discount.partials.table')
        </div>
    </div>

    <x-modal.form
        id="discountFilterModal"
        form-id="discountFilterForm"
        title="Discount Filter"
        close-button-id="discountResetFilter"
        action="#"
        method="GET"
        :enctype="null"
        class="discount-filter-modal"
        panel-class="custom-scrollbar mx-auto my-auto w-full max-w-[288px] overflow-visible border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.24)] md:max-w-[480px]"
    >
        <x-input.dropdown-select
            id="discountClassFilter"
            name="class_id"
            placeholder="Select Class"
            :value="request('class_id')"
            :options="[]"
            add-button-id="openClassFromDiscountFilter"
            add-button-label="Add class"
            add-button-target="classModal"
        />
        <x-input.dropdown-select
            id="discountGroupFilter"
            name="group_id"
            placeholder="Select Group"
            :value="request('group_id')"
            :options="[]"
            add-button-id="openGroupFromDiscountFilter"
            add-button-label="Add group"
            add-button-target="groupModal"
        />
        <x-input.dropdown-select
            id="discountSectionFilter"
            name="section_id"
            placeholder="Select Section"
            :value="request('section_id')"
            :options="[]"
            add-button-id="openSectionFromDiscountFilter"
            add-button-label="Add section"
            add-button-target="sectionModal"
        />
        <x-input.dropdown-select
            id="discountSessionFilter"
            name="session_id"
            placeholder="Select Session"
            :value="request('session_id')"
            :options="[]"
            add-button-id="openSessionFromDiscountFilter"
            add-button-label="Add session"
            add-button-target="sessionModal"
        />

        <x-slot:footer>
            <div class="grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 py-3">
                <x-button.secondary id="discountResetFilter" type="button" class="w-full">Reset</x-button.secondary>
                <x-button.primary id="discountApplyFilter" type="button" class="w-full">Apply</x-button.primary>
            </div>
        </x-slot:footer>
    </x-modal.form>

    @include('school.fees.discount.partials.discount-modal')
    @include('school.academic.class.partials.class-modal')
    @include('school.academic.group.partials.group-modal')
    @include('school.academic.section.partials.section-modal')
    @include('school.academic.session.partials.session-modal')
    @include('school.fees.discount.partials.js.modal-open')
    @include('school.academic.class.partials.js.modal-open')
    @include('school.academic.group.partials.js.modal-open')
    @include('school.academic.section.partials.js.modal-open')
    @include('school.academic.session.partials.js.modal-open')
    @include('school.fees.discount.partials.js.error-validation')
    @include('school.academic.class.partials.js.error-validation')
    @include('school.academic.group.partials.js.error-validation')
    @include('school.academic.section.partials.js.error-validation')
    @include('school.academic.session.partials.js.error-validation')
    @include('school.fees.discount.partials.js.modal-submit')
    @include('school.academic.class.partials.js.modal-submit')
    @include('school.academic.group.partials.js.modal-submit')
    @include('school.academic.section.partials.js.modal-submit')
    @include('school.academic.session.partials.js.modal-submit')
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
            if (labelEl) labelEl.textContent = label || (labelEl.dataset.placeholder || 'Select...');
        }

        function setDropdownValueFromMenu(inputId, value) {
            const input = document.getElementById(inputId);
            if (input) input.value = value;
            const menu = document.getElementById(inputId + 'Menu');
            const label = document.querySelector('#' + inputId + 'Button [data-dropdown-select-label]');
            if (label) {
                const opt = menu?.querySelector('[data-value="' + value + '"]');
                label.textContent = opt ? opt.textContent.trim() : (label.dataset.placeholder || 'Select...');
            }
        }

        function formatDate(dateString) {
            if (!dateString) return '-';
            const d = new Date(dateString);
            if (isNaN(d.getTime())) return dateString;
            const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
            return d.getDate() + '-' + months[d.getMonth()] + '-' + d.getFullYear();
        }

        function showEl(id) { const el = document.getElementById(id); if (el) el.style.display = 'block'; }
        function hideEl(id) { const el = document.getElementById(id); if (el) el.style.display = 'none'; }

        function populateStaticMenu(menuId, options) {
            const menu = document.querySelector(`#${menuId}`);
            if (!menu) return;
            menu.innerHTML = '';
            options.forEach(({ value, label }) => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'dropdown-select-option m-0 flex min-h-6 w-full items-center border-0 bg-white px-3 py-1 text-left text-[11px] font-normal leading-tight transition-colors hover:bg-slate-100 text-slate-800';
                btn.dataset.value = String(value);
                btn.textContent = label;
                btn.setAttribute('role', 'option');
                btn.setAttribute('aria-selected', 'false');
                btn.setAttribute('data-dropdown-select-option', '');
                btn.addEventListener('click', function() {
                    const root = menu.closest('[data-dropdown-select]');
                    const input = root.querySelector('[data-dropdown-select-input]');
                    const labelEl = root.querySelector('[data-dropdown-select-label]');
                    input.value = this.dataset.value || '';
                    labelEl.textContent = this.textContent.trim();
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

        function runCalc() {
            const base = parseFloat(document.getElementById('beforeDiscount')?.value) || 0;
            const val = parseFloat(document.getElementById('discountValue')?.value) || 0;
            const discType = document.getElementById('discountType')?.value || 'Fixed';
            const discAmt = discType === 'Percentage' ? (base * val / 100) : val;
            document.getElementById('discount_amount').value = discAmt.toFixed(2);
            document.getElementById('afterDiscount').value = (base - discAmt).toFixed(2);
        }

        async function loadDiscountClassSelect(selectedId = null) {
            try {
                const res = await axios.get('/api/get-school-classes');
                const data = res.data.data || [];
                populateDropdown('discountClassMenu', data, 'id', 'class_name');
                if (selectedId) {
                    const item = data.find(c => String(c.id) === String(selectedId));
                    if (item) setDropdownValue('discountClass', item.id, item.class_name);
                }
            } catch (e) { console.error(e); }
        }

        async function loadDiscountGroupSelect(selectedId = null) {
            const classId = document.querySelector('#discountClass')?.value;
            const params = classId ? { class_id: classId } : {};
            try {
                const res = await axios.get('/api/get-school-groups', { params });
                const data = res.data.data || [];
                populateDropdown('discountGroupMenu', data, 'id', 'group_name');
                if (selectedId) {
                    const item = data.find(g => String(g.id) === String(selectedId));
                    if (item) setDropdownValue('discountGroup', item.id, item.group_name);
                }
            } catch (e) { console.error(e); }
        }

        async function loadDiscountSectionSelect(selectedId = null) {
            const classId = document.querySelector('#discountClass')?.value;
            const groupId = document.querySelector('#discountGroup')?.value;
            const params = {};
            if (classId) params.class_id = classId;
            if (groupId) params.group_id = groupId;
            try {
                const res = await axios.get('/api/get-school-sections', { params });
                const data = res.data.data || [];
                populateDropdown('discountSectionMenu', data, 'id', 'section_name');
                if (selectedId) {
                    const item = data.find(s => String(s.id) === String(selectedId));
                    if (item) setDropdownValue('discountSection', item.id, item.section_name);
                }
            } catch (e) { console.error(e); }
        }

        async function loadDiscountSessionSelect(selectedId = null) {
            const classId = document.querySelector('#discountClass')?.value;
            const groupId = document.querySelector('#discountGroup')?.value;
            const sectionId = document.querySelector('#discountSection')?.value;
            const params = {};
            if (classId) params.class_id = classId;
            if (groupId) params.group_id = groupId;
            if (sectionId) params.section_id = sectionId;
            try {
                const res = await axios.get('/api/school-sessions', { params });
                const data = res.data.data || [];
                populateDropdown('discountSessionMenu', data, 'id', 'session_year');
                if (selectedId) {
                    const item = data.find(s => String(s.id) === String(selectedId));
                    if (item) setDropdownValue('discountSession', item.id, item.session_year);
                }
            } catch (e) { console.error(e); }
        }

        function resetDiscountStudentPicker() {
            const list = document.getElementById('discount_student_list');
            if (list) {
                list.innerHTML = '<tr><td colspan="4" class="p-4 text-center text-slate-400">Select class, group, section and session to load students.</td></tr>';
            }
            const wrapper = document.getElementById('discount_student_list_all_wrapper');
            if (wrapper) wrapper.style.display = 'none';
            const allCb = document.getElementById('discount_student_list_all');
            if (allCb) allCb.checked = false;
            const count = document.getElementById('discount_student_count');
            if (count) count.classList.add('hidden');
            document.getElementById('discount_student_ids').value = '';
        }

        function getSelectedStudentIds() {
            return Array.from(document.querySelectorAll('#discount_student_list .food-student-select:checked'))
                .map(cb => cb.value);
        }

        function reloadDiscountDependentData() {
            const scope = document.getElementById('discountStudentScope')?.value || 'all';
            if (scope === 'single' || scope === 'multiple') {
                loadDiscountStudents(scope);
            } else if (scope === 'all') {
                loadDiscountFeeTypes();
            }
        }

        function handleDiscountStudentScopeChange() {
            const scope = document.getElementById('discountStudentScope').value;
            const studentDiv = document.getElementById('div_discount_students');
            if (scope === 'single' || scope === 'multiple') {
                studentDiv.style.display = 'block';
                document.getElementById('discount_student_list_select_label').textContent = 'Select';
                loadDiscountStudents(scope);
            } else {
                studentDiv.style.display = 'none';
                resetDiscountStudentPicker();
                document.getElementById('discount_student_ids').value = '';
                loadDiscountFeeTypes();
            }
        }

        async function loadDiscountStudents(type, selectedIds = []) {
            const classId = document.querySelector('#discountClass')?.value;
            const sessionId = document.querySelector('#discountSession')?.value;
            const groupId = document.querySelector('#discountGroup')?.value;
            const sectionId = document.querySelector('#discountSection')?.value;
            if (!classId || !sessionId) return;
            const params = { class_id: classId, session_id: sessionId };
            if (groupId) params.group_id = groupId;
            if (sectionId) params.section_id = sectionId;
            const isMultiple = type === 'multiple';
            const tbody = document.getElementById('discount_student_list');
            const selectAllWrapper = document.getElementById('discount_student_list_all_wrapper');
            const selectAllCb = document.getElementById('discount_student_list_all');
            if (selectAllWrapper) selectAllWrapper.style.display = isMultiple ? 'flex' : 'none';
            if (selectAllCb) selectAllCb.checked = false;
            tbody.innerHTML = '<tr><td colspan="4" class="p-4 text-center text-slate-400"><i class="fas fa-spinner fa-spin mr-1"></i> Loading students...</td></tr>';
            try {
                const res = await axios.get('/api/get-school-students', { params });
                const data = res.data.data || res.data || [];
                tbody.innerHTML = '';
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="p-4 text-center text-slate-400">No active students found.</td></tr>';
                    const count = document.getElementById('discount_student_count');
                    if (count) count.classList.add('hidden');
                    return;
                }
                const inputType = isMultiple ? 'checkbox' : 'radio';
                const inputName = isMultiple ? 'discount_student_ids[]' : 'discount_student_id';
                data.forEach((student, index) => {
                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-slate-50 transition-colors';
                    tr.innerHTML =
                        '<td class="p-2 text-slate-500 font-normal">' + (index + 1) + '</td>' +
                        '<td class="p-2 font-semibold text-slate-700">' + (student.student_id_number || '-') + '</td>' +
                        '<td class="p-2 text-slate-600">' + (student.student_name || student.name || 'Student #' + student.id) + '</td>' +
                        '<td class="p-2 text-center"><input type="' + inputType + '" name="' + inputName + '" value="' + student.id + '" class="food-student-select w-4 h-4 cursor-pointer accent-blue-600" ' + (selectedIds.includes(String(student.id)) ? 'checked' : '') + '></td>';
                    tbody.appendChild(tr);
                });
                const count = document.getElementById('discount_student_count');
                if (count) {
                    count.textContent = data.length + ' student(s) available';
                    count.classList.remove('hidden');
                }
                document.getElementById('discount_student_ids').value = getSelectedStudentIds().join(',');
            } catch (e) {
                tbody.innerHTML = '<tr><td colspan="4" class="p-4 text-center text-red-500">Failed to load students.</td></tr>';
                console.error(e);
            }
        }

        function toggleStudentSelectionAll(master) {
            const wrapper = master.closest('[id$="_all_wrapper"]');
            const tbodyId = wrapper ? wrapper.id.replace('_all_wrapper', '') : null;
            const scope = tbodyId ? '#' + tbodyId : '#discount_student_list';
            document.querySelectorAll(scope + ' .food-student-select').forEach(function(el) {
                if (el.type === 'checkbox') el.checked = master.checked;
            });
            document.getElementById('discount_student_ids').value = getSelectedStudentIds().join(',');
        }

        function resetDiscountFeeType() {
            const menu = document.getElementById('discountFeeTypeMenu');
            if (menu) menu.innerHTML = '';
            const input = document.getElementById('discountFeeType');
            if (input) input.value = '';
            const label = document.querySelector('#discountFeeTypeButton [data-dropdown-select-label]');
            if (label) label.textContent = label.dataset.placeholder || 'Select Fee Type';
            document.getElementById('beforeDiscount').value = '';
            document.getElementById('afterDiscount').value = '';
            document.getElementById('discount_amount').value = '';
        }

        function setFeeTypeAmount() {
            const input = document.getElementById('discountFeeType');
            const opt = document.querySelector('#discountFeeTypeMenu [data-value="' + input.value + '"]');
            document.getElementById('beforeDiscount').value = opt ? (opt.dataset.amount || 0) : '';
            runCalc();
        }

        async function loadDiscountFeeTypes(selectedId = null) {
            const classId = document.querySelector('#discountClass')?.value;
            const sessionId = document.querySelector('#discountSession')?.value;
            const groupId = document.querySelector('#discountGroup')?.value;
            const sectionId = document.querySelector('#discountSection')?.value;
            const menu = document.getElementById('discountFeeTypeMenu');
            if (!menu) return;
            menu.innerHTML = '';
            if (!classId || !sessionId) {
                menu.innerHTML = '<div class="px-3 py-1 text-slate-400">Select class and session to load fee types.</div>';
                return;
            }
            const params = { class_id: classId, session_id: sessionId, all: true };
            if (groupId) params.group_id = groupId;
            if (sectionId) params.section_id = sectionId;
            menu.innerHTML = '<div class="px-3 py-1 text-slate-400"><i class="fas fa-spinner fa-spin mr-1"></i> Loading fee types...</div>';
            try {
                const res = await axios.get('/api/fee-templates', { params });
                const templates = (res.data.data || []).filter(t => t.fee_type_name !== 'Promote');
                menu.innerHTML = '';
                if (templates.length === 0) {
                    menu.innerHTML = '<div class="px-3 py-1 text-slate-400">No fee types found.</div>';
                    return;
                }
                templates.forEach(t => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'dropdown-select-option m-0 flex min-h-6 w-full items-center border-0 bg-white px-3 py-1 text-left text-[11px] font-normal leading-tight transition-colors hover:bg-slate-100 text-slate-800';
                    btn.dataset.value = String(t.id);
                    btn.dataset.amount = t.amount || 0;
                    btn.textContent = (t.fee_type_name || '-') + ' - ' + (t.fee_name || '') + ' (' + (t.amount || 0) + ')';
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
                if (selectedId) {
                    const selOpt = menu.querySelector('[data-value="' + selectedId + '"]');
                    if (selOpt) {
                        setDropdownValueFromMenu('discountFeeType', selectedId);
                        document.getElementById('beforeDiscount').value = selOpt.dataset.amount || 0;
                        runCalc();
                    }
                }
            } catch (e) {
                menu.innerHTML = '<div class="px-3 py-1 text-red-500">Failed to load fee types.</div>';
                console.error(e);
            }
        }

        async function loadDiscountGrades(selectedId = null) {
            try {
                const res = await axios.get('/api/school-exam-grades', { params: { all: true } });
                const grades = res.data.data || [];
                grades.sort((a, b) => (parseFloat(b.grade_point) || 0) - (parseFloat(a.grade_point) || 0));
                populateDropdown('discountMinGradeMenu', grades, 'grade_name', 'grade_name');
                if (selectedId) {
                    const item = grades.find(g => String(g.grade_name) === String(selectedId));
                    if (item) setDropdownValue('discountMinGrade', item.grade_name, item.grade_name);
                }
            } catch (e) { console.error(e); }
        }

        function handleDiscountScopeChange() {
            const scope = document.getElementById('discountScope').value || 'session';
            const examDiv = document.getElementById('div_discount_exam_grade');
            const beforeDiv = document.getElementById('div_before_discount');
            const afterDiv = document.getElementById('div_after_discount');
            if (scope === 'exam') {
                if (examDiv) examDiv.style.display = 'block';
                if (beforeDiv) beforeDiv.style.display = 'block';
                if (afterDiv) afterDiv.style.display = 'block';
                loadDiscountGrades();
                loadDiscountFeeTypes();
            } else {
                if (examDiv) examDiv.style.display = 'none';
                if (beforeDiv) beforeDiv.style.display = 'block';
                if (afterDiv) afterDiv.style.display = 'block';
                setDropdownValue('discountMinGrade', '', 'Select Minimum Qualifying Grade');
                loadDiscountFeeTypes();
            }
        }

        function setDiscountScope(scope) {
            const scopeSelect = document.getElementById('discountScope');
            if (scopeSelect && scope) {
                scopeSelect.value = scope;
                const label = document.querySelector('#discountScopeButton [data-dropdown-select-label]');
                if (label) label.textContent = scope === 'exam' ? 'Exam' : 'Session';
            }
            handleDiscountScopeChange();
        }

        async function loadDiscountFilterOptions() {
            try {
                const classRes = await axios.get('/api/get-school-classes');
                populateDropdown('discountClassFilterMenu', classRes.data.data || [], 'id', 'class_name');
            } catch (err) { console.error('Failed to load filter options:', err); }
        }

        async function loadDiscountGroupFilterByClass(classId) {
            setDropdownValue('discountGroupFilter', '', 'Select Group');
            setDropdownValue('discountSectionFilter', '', 'Select Section');
            setDropdownValue('discountSessionFilter', '', 'Select Session');
            populateDropdown('discountSectionFilterMenu', [], 'id', 'section_name');
            populateDropdown('discountSessionFilterMenu', [], 'id', 'session_year');
            if (!classId) { populateDropdown('discountGroupFilterMenu', [], 'id', 'group_name'); return; }
            try {
                const res = await axios.get('/api/get-school-groups', { params: { class_id: classId } });
                const groups = res.data.data || [];
                const seen = new Set();
                const unique = groups.filter(g => { const k = g.group_name; return seen.has(k) ? false : seen.add(k); });
                populateDropdown('discountGroupFilterMenu', unique, 'id', 'group_name');
            } catch (e) { console.error(e); }
        }

        async function loadDiscountSectionFilterByGroup(classId, groupId) {
            setDropdownValue('discountSectionFilter', '', 'Select Section');
            setDropdownValue('discountSessionFilter', '', 'Select Session');
            populateDropdown('discountSessionFilterMenu', [], 'id', 'session_year');
            if (!classId) { populateDropdown('discountSectionFilterMenu', [], 'id', 'section_name'); return; }
            const params = { class_id: classId };
            if (groupId) params.group_id = groupId;
            try {
                const res = await axios.get('/api/get-school-sections', { params });
                const sections = res.data.data || [];
                const seen = new Set();
                const unique = sections.filter(s => { const k = s.section_name; return seen.has(k) ? false : seen.add(k); });
                populateDropdown('discountSectionFilterMenu', unique, 'id', 'section_name');
            } catch (e) { console.error(e); }
        }

        async function loadDiscountSessionFilterBySection(classId, groupId, sectionId) {
            setDropdownValue('discountSessionFilter', '', 'Select Session');
            if (!classId) { populateDropdown('discountSessionFilterMenu', [], 'id', 'session_year'); return; }
            const params = { class_id: classId };
            if (groupId) params.group_id = groupId;
            if (sectionId) params.section_id = sectionId;
            try {
                const res = await axios.get('/api/school-sessions', { params });
                const sessions = res.data.data || [];
                const years = [...new Set(sessions.map(s => s.session_year).filter(Boolean))];
                populateDropdown('discountSessionFilterMenu', years.map(y => ({ id: y, session_year: y })), 'id', 'session_year');
            } catch (e) { console.error(e); }
        }

        function fetchDiscounts(page = 1) {
            currentPage = page;
            const search = document.getElementById('discountSearch')?.value || document.getElementById('discountSearchMobile')?.value || '';
            axios.get('/api/fee-discounts', {
                params: {
                    search,
                    page,
                    class_id: currentFilterClass,
                    group_id: currentFilterGroup,
                    section_id: currentFilterSection,
                    session_id: currentFilterSession,
                }
            })
            .then(res => {
                const items = res.data.data || [];
                const meta = res.data;
                const tbody = document.getElementById('discountTableBody');
                tbody.innerHTML = '';
                if (items.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="16" class="border border-gray-300 px-3 py-10 text-center text-gray-500">No discounts configured.</td></tr>`;
                    document.getElementById('paginationInfo').innerText = '0 of 0';
                    document.getElementById('paginationControls').innerHTML = '';
                    return;
                }
                items.forEach((item, index) => {
                    const sl = meta.from ? meta.from + index : index + 1;
                    const scopeLabel = (item.discount_scope || 'session') === 'exam' ? 'Exam' : 'Session';
                    const discDisplay = item.discount_type === 'Percentage'
                        ? item.discount_value + '%'
                        : (scopeLabel === 'Exam' ? item.discount_value : (item.discount_amount ?? item.discount_value));
                    tbody.innerHTML += `<tr class="hover:bg-gray-50">
                        <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">${sl}</td>
                        <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${scopeLabel}</td>
                        <td class="h-8 whitespace-nowrap border border-gray-300 px-3"><div class="donate-cell-scroll" title="${item.school_class?.class_name || '-'}">${item.school_class?.class_name || '-'}</div></td>
                        <td class="h-8 whitespace-nowrap border border-gray-300 px-3"><div class="donate-cell-scroll" title="${item.school_group?.group_name || 'General'}">${item.school_group?.group_name || 'General'}</div></td>
                        <td class="h-8 whitespace-nowrap border border-gray-300 px-3"><div class="donate-cell-scroll" title="${item.school_section?.section_name || '-'}">${item.school_section?.section_name || '-'}</div></td>
                        <td class="h-8 whitespace-nowrap border border-gray-300 px-3"><div class="donate-cell-scroll" title="${item.school_session?.session_year || '-'}">${item.school_session?.session_year || '-'}</div></td>
                        <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${item.student?.student_id_number || '-'}</td>
                        <td class="h-8 whitespace-nowrap border border-gray-300 px-3"><div class="donate-cell-scroll">${item.student?.student_name || '-'}</div></td>
                        <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${item.fee_type?.fee_type_name || '-'}</td>
                        <td class="h-8 whitespace-nowrap border border-gray-300 px-3"><div class="donate-cell-scroll">${item.fee_name || '-'}</div></td>
                        <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${scopeLabel === 'Exam' ? '-' : (item.before_discount ?? '-')}</td>
                        <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${item.discount_type}</td>
                        <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${discDisplay}</td>
                        <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${scopeLabel === 'Exam' ? '-' : (item.after_discount ?? '-')}</td>
                        <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${scopeLabel === 'Exam' ? (item.minimum_grade || '-') : '-'}</td>
                        <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                            <div class="flex h-6 w-full items-center justify-center -space-x-[3px]">
                                <button type="button" title="Edit" onclick="editDiscount(${item.id})" class="flex h-6 w-[14px] items-center justify-center text-gray-600 hover:text-blue-600"><i class="far fa-edit text-xs"></i></button>
                                <button type="button" title="Delete" onclick="deleteDiscount(${item.id})" class="flex h-6 w-[14px] items-center justify-center text-gray-600 hover:text-red-600"><i class="far fa-trash-alt text-xs"></i></button>
                            </div>
                        </td>
                    </tr>`;
                });
                renderDiscountPagination(meta);
            })
            .catch(err => console.error('Load Error:', err));
        }

        function renderDiscountPagination(meta) {
            const controls = document.getElementById('paginationControls');
            document.getElementById('paginationInfo').innerText = `${meta.to || 0} of ${meta.total}`;
            controls.innerHTML = '';
            const prevBtn = document.createElement('button');
            prevBtn.className = 'pagination-btn';
            prevBtn.innerHTML = '<i class="mdi mdi-chevron-left"></i>';
            prevBtn.disabled = meta.current_page === 1;
            prevBtn.onclick = () => fetchDiscounts(meta.current_page - 1);
            controls.appendChild(prevBtn);
            for (let i = 1; i <= meta.last_page; i++) {
                const btn = document.createElement('button');
                btn.className = `pagination-btn ${meta.current_page === i ? 'active' : ''}`;
                btn.innerText = i;
                btn.onclick = () => fetchDiscounts(i);
                controls.appendChild(btn);
            }
            const nextBtn = document.createElement('button');
            nextBtn.className = 'pagination-btn';
            nextBtn.innerHTML = '<i class="mdi mdi-chevron-right"></i>';
            nextBtn.disabled = meta.current_page === meta.last_page;
            nextBtn.onclick = () => fetchDiscounts(meta.current_page + 1);
            controls.appendChild(nextBtn);
        }

        function editDiscount(id) {
            axios.get('/api/fee-discounts/' + id)
                .then(res => {
                    const item = res.data;
                    document.getElementById('edit_id').value = item.id;
                    document.getElementById('discountModalTitle').innerText = 'Edit Discount';

                    loadDiscountClassSelect(item.class_id);
                    setTimeout(() => {
                        loadDiscountGroupSelect(item.group_id);
                        setTimeout(() => {
                            loadDiscountSectionSelect(item.section_id);
                            setTimeout(() => {
                                loadDiscountSessionSelect(item.session_id);
                                setTimeout(() => {
                                    setDiscountScope(item.discount_scope || 'session');
                                    const hasStudent = !!item.student_id;
                                    if (hasStudent) {
                                        setDropdownValue('discountStudentScope', 'single', 'Single Student');
                                        document.getElementById('div_discount_students').style.display = 'block';
                                        loadDiscountStudents('single', [String(item.student_id)]);
                                    }
                                    setTimeout(() => {
                                        loadDiscountFeeTypes(item.fee_type_id ? String(item.fee_type_id) : null);
                                        if ((item.discount_scope || 'session') === 'exam') {
                                            loadDiscountGrades(item.minimum_grade);
                                        }
                                        setTimeout(() => {
                                            setDropdownValueFromMenu('discountType', item.discount_type);
                                            document.getElementById('discountValue').value = item.discount_value;
                                            document.getElementById('beforeDiscount').value = item.fee_type?.amount ?? item.before_discount ?? '';
                                            runCalc();
                                            document.getElementById('discountModal').classList.remove('hidden');
                                        }, 200);
                                    }, 300);
                                }, 200);
                            }, 300);
                        }, 300);
                    }, 300);
                })
                .catch(() => Swal.fire('Error', 'Failed to load discount data.', 'error'));
        }

        function deleteDiscount(id) {
            Swal.fire({
                title: 'Delete Discount?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it'
            }).then(r => {
                if (r.isConfirmed) {
                    axios.delete('/api/fee-discounts/' + id)
                        .then(() => {
                            Toastify({ text: 'Discount Deleted', style: { background: '#ef4444' } }).showToast();
                            fetchDiscounts(currentPage);
                        })
                        .catch(() => Swal.fire('Error', 'Could not delete discount.', 'error'));
                }
            });
        }

        function handleDiscountTypeChange() {
            const val = document.getElementById('discountType').value;
            if (val === 'Percentage') {
                document.getElementById('discountValue').placeholder = 'Enter %';
            } else {
                document.getElementById('discountValue').placeholder = 'Enter amount';
            }
            runCalc();
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadDiscountFilterOptions();

            populateStaticMenu('discountTypeMenu', [
                { value: 'Fixed', label: 'Fixed Amount' },
                { value: 'Percentage', label: 'Percentage %' },
            ]);

            document.getElementById('discountSearch')?.addEventListener('input', () => fetchDiscounts(1));
            document.getElementById('discountSearchMobile')?.addEventListener('input', () => fetchDiscounts(1));

            document.getElementById('discountType')?.addEventListener('change', handleDiscountTypeChange);
            document.getElementById('discountValue')?.addEventListener('input', runCalc);

            document.getElementById('discountClassFilter')?.addEventListener('change', function() {
                currentFilterClass = this.value || '';
                loadDiscountGroupFilterByClass(this.value);
            });

            document.getElementById('discountGroupFilter')?.addEventListener('change', function() {
                currentFilterGroup = this.value || '';
                currentFilterSection = '';
                currentFilterSession = '';
                const classInput = document.getElementById('discountClassFilter');
                const classId = classInput ? classInput.value : '';
                loadDiscountSectionFilterByGroup(classId, this.value);
            });

            document.getElementById('discountSectionFilter')?.addEventListener('change', function() {
                currentFilterSection = this.value || '';
                currentFilterSession = '';
                const classInput = document.getElementById('discountClassFilter');
                const classId = classInput ? classInput.value : '';
                const groupInput = document.getElementById('discountGroupFilter');
                const groupId = groupInput ? groupInput.value : '';
                loadDiscountSessionFilterBySection(classId, groupId, this.value);
            });

            document.getElementById('discountSessionFilter')?.addEventListener('change', function() {
                currentFilterSession = this.value || '';
            });

            document.getElementById('btnFilter')?.addEventListener('click', () => {
                document.getElementById('discountFilterModal')?.classList.remove('hidden');
            });

            document.getElementById('discountResetFilter')?.addEventListener('click', () => {
                setDropdownValue('discountClassFilter', '', 'Select Class');
                setDropdownValue('discountGroupFilter', '', 'Select Group');
                setDropdownValue('discountSectionFilter', '', 'Select Section');
                setDropdownValue('discountSessionFilter', '', 'Select Session');
                populateDropdown('discountGroupFilterMenu', [], 'id', 'group_name');
                populateDropdown('discountSectionFilterMenu', [], 'id', 'section_name');
                populateDropdown('discountSessionFilterMenu', [], 'id', 'session_year');
                currentFilterClass = '';
                currentFilterGroup = '';
                currentFilterSection = '';
                currentFilterSession = '';
                currentPage = 1;
                fetchDiscounts(1);
                document.getElementById('discountFilterModal')?.classList.add('hidden');
            });

            document.getElementById('discountApplyFilter')?.addEventListener('click', () => {
                const classInput = document.getElementById('discountClassFilter');
                currentFilterClass = classInput ? classInput.value : '';
                const groupInput = document.getElementById('discountGroupFilter');
                currentFilterGroup = groupInput ? groupInput.value : '';
                const sectionInput = document.getElementById('discountSectionFilter');
                currentFilterSection = sectionInput ? sectionInput.value : '';
                const sessionInput = document.getElementById('discountSessionFilter');
                currentFilterSession = sessionInput ? sessionInput.value : '';
                currentPage = 1;
                fetchDiscounts(1);
                document.getElementById('discountFilterModal')?.classList.add('hidden');
            });

            document.getElementById('btnRestoreDesktop')?.addEventListener('click', () => {
                document.getElementById('discountSearch').value = '';
                currentFilterClass = '';
                currentFilterGroup = '';
                currentFilterSection = '';
                currentFilterSession = '';
                currentPage = 1;
                fetchDiscounts(1);
            });

            document.getElementById('btnRestoreMobile')?.addEventListener('click', () => {
                document.getElementById('discountSearchMobile').value = '';
                currentFilterClass = '';
                currentFilterGroup = '';
                currentFilterSection = '';
                currentFilterSession = '';
                currentPage = 1;
                fetchDiscounts(1);
            });

            document.getElementById('discountClass')?.addEventListener('change', function() {
                setDropdownValue('discountGroup', '', 'Select Group');
                setDropdownValue('discountSection', '', 'Select Section');
                setDropdownValue('discountSession', '', 'Select Session');
                setDropdownValue('discountMinGrade', '', 'Select Minimum Qualifying Grade');
                populateDropdown('discountGroupMenu', [], 'id', 'group_name');
                populateDropdown('discountSectionMenu', [], 'id', 'section_name');
                populateDropdown('discountSessionMenu', [], 'id', 'session_year');
                resetDiscountFeeType();
                resetDiscountStudentPicker();
                runCalc();
                if (this.value) loadDiscountGroupSelect();
            });

            document.getElementById('discountGroup')?.addEventListener('change', function() {
                setDropdownValue('discountSection', '', 'Select Section');
                setDropdownValue('discountSession', '', 'Select Session');
                setDropdownValue('discountMinGrade', '', 'Select Minimum Qualifying Grade');
                populateDropdown('discountSectionMenu', [], 'id', 'section_name');
                populateDropdown('discountSessionMenu', [], 'id', 'session_year');
                resetDiscountFeeType();
                resetDiscountStudentPicker();
                runCalc();
                if (this.value) loadDiscountSectionSelect();
            });

            document.getElementById('discountSection')?.addEventListener('change', function() {
                setDropdownValue('discountSession', '', 'Select Session');
                setDropdownValue('discountMinGrade', '', 'Select Minimum Qualifying Grade');
                populateDropdown('discountSessionMenu', [], 'id', 'session_year');
                resetDiscountFeeType();
                resetDiscountStudentPicker();
                runCalc();
                if (this.value) loadDiscountSessionSelect();
            });

            document.getElementById('discountSession')?.addEventListener('change', function() {
                resetDiscountFeeType();
                resetDiscountStudentPicker();
                runCalc();
                reloadDiscountDependentData();
            });

            document.getElementById('discountScope')?.addEventListener('change', handleDiscountScopeChange);
            document.getElementById('discountStudentScope')?.addEventListener('change', handleDiscountStudentScopeChange);

            document.getElementById('discount_student_list')?.addEventListener('change', function(e) {
                if (e.target.classList.contains('food-student-select')) {
                    document.getElementById('discount_student_ids').value = getSelectedStudentIds().join(',');
                    loadDiscountFeeTypes();
                }
            });

            document.getElementById('discountFeeType')?.addEventListener('change', setFeeTypeAmount);

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

        fetchDiscounts();
    </script>
@endsection
