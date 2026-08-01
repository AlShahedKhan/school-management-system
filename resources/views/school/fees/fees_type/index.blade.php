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
            @include('school.fees.fees_type.partials.header')
            @include('school.fees.fees_type.partials.table')
        </div>
    </div>

    <x-modal.form
        id="filterModal"
        form-id="feeFilterForm"
        title="Fee Filter"
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
            add-button-id="openClassFromFeeFilter"
            add-button-label="Add class"
            add-button-target="classModal"
        />
        <x-input.dropdown-select
            id="feeGroupFilter"
            name="group_id"
            placeholder="Select Group"
            :value="request('group_id')"
            :options="[]"
            add-button-id="openGroupFromFeeFilter"
            add-button-label="Add group"
            add-button-target="groupModal"
        />
        <x-input.dropdown-select
            id="feeSectionFilter"
            name="section_id"
            placeholder="Select Section"
            :value="request('section_id')"
            :options="[]"
            add-button-id="openSectionFromFeeFilter"
            add-button-label="Add section"
            add-button-target="sectionModal"
        />
        <x-input.dropdown-select
            id="feeSessionFilter"
            name="session_id"
            placeholder="Select Session"
            :value="request('session_id')"
            :options="[]"
            add-button-id="openSessionFromFeeFilter"
            add-button-label="Add session"
            add-button-target="sessionModal"
        />

        <x-slot:footer>
            <div class="grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 py-3">
                <x-button.secondary id="resetFilter" type="button" class="w-full">Reset</x-button.secondary>
                <x-button.primary id="applyFilter" type="button" class="w-full">Apply</x-button.primary>
            </div>
        </x-slot:footer>
    </x-modal.form>

    @include('school.fees.fees_type.partials.fees-type-modal')
    @include('school.academic.class.partials.class-modal')
    @include('school.academic.group.partials.group-modal')
    @include('school.academic.section.partials.section-modal')
    @include('school.academic.session.partials.session-modal')
    @include('school.fees.fees_type.partials.js.modal-open')
    @include('school.academic.class.partials.js.modal-open')
    @include('school.academic.group.partials.js.modal-open')
    @include('school.academic.section.partials.js.modal-open')
    @include('school.academic.session.partials.js.modal-open')
    @include('school.fees.fees_type.partials.js.error-validation')
    @include('school.academic.class.partials.js.error-validation')
    @include('school.academic.group.partials.js.error-validation')
    @include('school.academic.section.partials.js.error-validation')
    @include('school.academic.session.partials.js.error-validation')
    @include('school.fees.fees_type.partials.js.modal-submit')
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
            if (labelEl) labelEl.textContent = label;
        }

        function formatDate(dateString) {
            if (!dateString) return '-';
            const d = new Date(dateString);
            if (isNaN(d.getTime())) return dateString;
            const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
            return d.getDate() + '-' + months[d.getMonth()] + '-' + d.getFullYear();
        }

        function getSessionLabel() {
            const label = document.querySelector('#feeFormSessionButton [data-dropdown-select-label]');
            return label ? label.textContent.trim() : '';
        }

        function autoGenerateFeeName(type) {
            const input = document.getElementById('fee_name_input');
            if (!input) return;
            const sessionLabel = getSessionLabel();
            const session = sessionLabel && sessionLabel !== 'Select Session' ? sessionLabel : '';
            if (type === 'Admission') {
                input.value = session ? 'Admission-' + session : 'Admission';
                input.readOnly = true;
            } else if (type === 'Promote') {
                input.value = session ? 'Promote-' + session : 'Promote';
                input.readOnly = true;
            } else {
                input.readOnly = false;
            }
        }

        function handleFeeTypeChange() {
            const type = document.getElementById('fee_type_name').value;
            resetConditionalFields();
            const fields = {
                'Exams': ['div_exam_name', 'div_amount', 'div_pay_date'],
                'Food': ['div_food_type', 'div_fee_name', 'div_amount', 'div_due_day'],
                'Session': ['div_fee_name', 'div_amount', 'div_pay_date'],
                'Admission': ['div_fee_name', 'div_amount', 'div_pay_date'],
                'Promote': ['div_fee_name', 'div_amount', 'div_pay_date'],
                'Tuition': ['div_fee_name', 'div_amount', 'div_due_day'],
            };

            (fields[type] || []).forEach(showEl);

            const nameWrapper = document.getElementById('fee_name_wrapper');
            if (type === 'Tuition') {
                const fr = document.getElementById('frequency');
                if (fr) { fr.value = 'monthly'; }
            } else if (type === 'Food') {
                const fr = document.getElementById('frequency');
                if (fr) { fr.value = 'monthly'; }
            } else if (type === 'Exams') {
                loadExams();
                const fr = document.getElementById('frequency');
                if (fr) { fr.value = 'per_exam'; }
            } else if (type === 'Session') {
                const fr = document.getElementById('frequency');
                if (fr) { fr.value = 'one_time'; }
            } else if (type) {
                const fr = document.getElementById('frequency');
                if (fr) { fr.value = 'one_time'; }
            }

            if (type === 'Exams') {
                hideEl('div_fee_name');
                const nameInput = document.getElementById('fee_name_input');
                if (nameInput) { nameInput.value = ''; nameInput.readOnly = true; }
            }

            autoGenerateFeeName(type);

            if (type === 'Exams') {
                const nameInput = document.getElementById('fee_name_input');
                if (nameInput) nameInput.readOnly = true;
            }
        }

        function handleFoodTypeChange() {
            const foodType = document.getElementById('food_type').value;
            const studentDiv = document.getElementById('div_food_students');
            if (foodType === 'single' || foodType === 'multiple') {
                studentDiv.style.display = 'block';
                document.getElementById('food_student_list_select_label').textContent = foodType === 'single' ? 'Select' : 'Select';
                loadStudentsForFood(foodType);
            } else {
                studentDiv.style.display = 'none';
                document.getElementById('food_student_list').innerHTML = '<tr><td colspan="4" class="p-4 text-center text-slate-400">Please select academic details first.</td></tr>';
                const count = document.getElementById('food_student_count');
                if (count) count.classList.add('hidden');
            }
        }

        function loadExams() {
            const classId = document.querySelector('#feeFormClass')?.value;
            const groupId = document.querySelector('#feeFormGroup')?.value;
            const sectionId = document.querySelector('#feeFormSection')?.value;
            const sessionId = document.querySelector('#feeFormSession')?.value;
            const params = {};
            if (classId) params.class_id = classId;
            if (groupId) params.group_id = groupId;
            if (sectionId) params.section_id = sectionId;
            if (sessionId) params.session_id = sessionId;
            axios.get('/api/school-exam-names', { params }).then(res => {
                const data = res.data.data || res.data || [];
                populateDropdown('exam_idMenu', data, 'id', 'exam_name');
            }).catch(() => {});
        }

        function handleExamSelect() {
            const examId = document.getElementById('exam_id').value;
            if (!examId) {
                document.getElementById('fee_name_input').value = '';
                return;
            }
            axios.get('/api/school-exam-names/' + examId).then(res => {
                const exam = res.data.data || res.data;
                if (!exam) return;
                document.getElementById('fee_name_input').value = exam.exam_name || '';
                if (exam.exam_end_date) {
                    document.getElementById('pay_date').value = exam.exam_end_date;
                }
            }).catch(() => {});
        }

        function loadStudentsForFood(type) {
            const classId = document.querySelector('#feeFormClass')?.value;
            const sessionId = document.querySelector('#feeFormSession')?.value;
            if (!classId || !sessionId) return;
            const isMultiple = type === 'multiple';
            const tbody = document.getElementById('food_student_list');
            const selectAllWrapper = document.getElementById('food_student_list_all_wrapper');
            const selectAllCb = document.getElementById('food_student_list_all');
            if (selectAllWrapper) selectAllWrapper.style.display = isMultiple ? 'flex' : 'none';
            if (selectAllCb) selectAllCb.checked = false;
            tbody.innerHTML = '<tr><td colspan="4" class="p-4 text-center text-slate-400"><i class="fas fa-spinner fa-spin mr-1"></i> Loading students...</td></tr>';
            axios.get('/api/get-school-students', { params: { class_id: classId, session_id: sessionId } }).then(res => {
                const data = res.data.data || res.data || [];
                tbody.innerHTML = '';
                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="p-4 text-center text-slate-400">No active students found.</td></tr>';
                    const count = document.getElementById('food_student_count');
                    if (count) count.classList.add('hidden');
                    return;
                }
                const inputType = isMultiple ? 'checkbox' : 'radio';
                const inputName = isMultiple ? 'student_ids[]' : 'student_id';
                data.forEach((student, index) => {
                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-slate-50 transition-colors';
                    tr.innerHTML =
                        '<td class="p-2 text-slate-500 font-normal">' + (index + 1) + '</td>' +
                        '<td class="p-2 font-semibold text-slate-700">' + (student.student_id_number || '-') + '</td>' +
                        '<td class="p-2 text-slate-600">' + (student.student_name || student.name || 'Student #' + student.id) + '</td>' +
                        '<td class="p-2 text-center"><input type="' + inputType + '" name="' + inputName + '" value="' + student.id + '" class="food-student-select w-4 h-4 cursor-pointer accent-blue-600"></td>';
                    tbody.appendChild(tr);
                });
                const count = document.getElementById('food_student_count');
                if (count) {
                    count.textContent = data.length + ' student(s) available';
                    count.classList.remove('hidden');
                }
            }).catch(() => {
                tbody.innerHTML = '<tr><td colspan="4" class="p-4 text-center text-red-500">Failed to load students.</td></tr>';
            });
        }

        function toggleAllFoodStudents(master) {
            document.querySelectorAll('.food-student-select').forEach(function(el) {
                if (el.type === 'checkbox') el.checked = master.checked;
            });
        }

        async function loadFeeGroupFilterByClass(classId) {
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
                setDropdownValue('feeGroupFilter', '', 'Select Group');
                populateDropdown('feeGroupFilterMenu', uniqueGroups, 'id', 'group_name');
                setDropdownValue('feeSectionFilter', '', 'Select Section');
                populateDropdown('feeSectionFilterMenu', [], 'id', 'section_name');
                setDropdownValue('feeSessionFilter', '', 'Select Session');
                populateDropdown('feeSessionFilterMenu', [], 'id', 'session_year');
            } catch (err) {
                console.error('Failed to load group filter options:', err);
            }
        }

        async function loadFeeSectionFilterByGroup(classId, groupId) {
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
                populateDropdown('feeSectionFilterMenu', uniqueSections, 'id', 'section_name');
                setDropdownValue('feeSessionFilter', '', 'Select Session');
                populateDropdown('feeSessionFilterMenu', [], 'id', 'session_year');
            } catch (err) {
                console.error('Failed to load section filter options:', err);
            }
        }

        async function loadFeeSessionFilterBySection(classId, groupId, sectionId) {
            try {
                const params = {};
                if (classId) params.class_id = classId;
                if (groupId) params.group_id = groupId;
                if (sectionId) params.section_id = sectionId;
                const sessionRes = await axios.get('/api/school-sessions', { params });
                const sessions = sessionRes.data.data || [];
                const uniqueYears = [...new Set(sessions.map(s => s.session_year).filter(Boolean))];
                const yearItems = uniqueYears.map(y => ({ id: y, session_year: y }));
                populateDropdown('feeSessionFilterMenu', yearItems, 'id', 'session_year');
            } catch (err) {
                console.error('Failed to load session filter options:', err);
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

        function loadFeeClassSelect(selectedId = null) {
            axios.get('/api/get-school-classes').then(res => {
                const data = res.data.data || [];
                populateDropdown('feeFormClassMenu', data, 'id', 'class_name');
                if (selectedId) {
                    const item = data.find(c => String(c.id) === String(selectedId));
                    if (item) setDropdownValue('feeFormClass', item.id, item.class_name);
                }
            }).catch(err => console.error("Class dropdown error:", err));
        }

        function loadFeeGroupSelect(selectedId = null) {
            const classId = document.querySelector('#feeFormClass')?.value;
            const params = classId ? { class_id: classId } : {};
            axios.get('/api/get-school-groups', { params }).then(res => {
                const data = res.data.data || [];
                populateDropdown('feeFormGroupMenu', data, 'id', 'group_name');
                if (selectedId) {
                    const item = data.find(g => String(g.id) === String(selectedId));
                    if (item) setDropdownValue('feeFormGroup', item.id, item.group_name);
                }
            }).catch(err => console.error("Group dropdown error:", err));
        }

        function loadFeeSectionSelect(selectedId = null) {
            const classId = document.querySelector('#feeFormClass')?.value;
            const groupId = document.querySelector('#feeFormGroup')?.value;
            const params = {};
            if (classId) params.class_id = classId;
            if (groupId) params.group_id = groupId;
            axios.get('/api/get-school-sections', { params }).then(res => {
                const data = res.data.data || [];
                populateDropdown('feeFormSectionMenu', data, 'id', 'section_name');
                if (selectedId) {
                    const item = data.find(s => String(s.id) === String(selectedId));
                    if (item) setDropdownValue('feeFormSection', item.id, item.section_name);
                }
            }).catch(err => console.error("Section dropdown error:", err));
        }

        function loadFeeSessionSelect(selectedId = null) {
            const classId = document.querySelector('#feeFormClass')?.value;
            const groupId = document.querySelector('#feeFormGroup')?.value;
            const sectionId = document.querySelector('#feeFormSection')?.value;
            const params = {};
            if (classId) params.class_id = classId;
            if (groupId) params.group_id = groupId;
            if (sectionId) params.section_id = sectionId;
            axios.get('/api/school-sessions', { params }).then(res => {
                const data = res.data.data || [];
                populateDropdown('feeFormSessionMenu', data, 'id', 'session_year');
                if (selectedId) {
                    const item = data.find(s => String(s.id) === String(selectedId));
                    if (item) setDropdownValue('feeFormSession', item.id, item.session_year);
                }
            }).catch(err => console.error("Session dropdown error:", err));
        }

        function fetchFeeTemplates(page = 1) {
            currentPage = page;
            const search = document.getElementById('feeSearch')?.value || document.getElementById('feeSearchMobile')?.value || '';
            axios.get('/api/fee-templates', { params: { search, page, class_id: currentFilterClass, group_id: currentFilterGroup, section_id: currentFilterSection, session_id: currentFilterSession } })
                .then(res => {
                    const templates = res.data.data || [];
                    const meta = res.data;
                    const tbody = document.getElementById('feeTableBody');
                    tbody.innerHTML = '';
                    if (templates.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="10" class="border border-gray-300 px-3 py-10 text-center text-gray-500">No fee templates configured.</td></tr>`;
                        document.getElementById('paginationInfo').innerText = '0 of 0';
                        document.getElementById('paginationControls').innerHTML = '';
                        return;
                    }
                    templates.forEach((item, index) => {
                        const sl = meta.from ? meta.from + index : index + 1;
                        tbody.innerHTML += `
                            <tr class="hover:bg-gray-50">
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">${sl}</td>
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                    <div class="donate-cell-scroll" title="${item.school_class ? item.school_class.class_name : '-'}">${item.school_class ? item.school_class.class_name : '-'}</div>
                                </td>
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                    <div class="donate-cell-scroll" title="${item.school_group ? item.school_group.group_name : 'General'}">${item.school_group ? item.school_group.group_name : 'General'}</div>
                                </td>
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                    <div class="donate-cell-scroll" title="${item.school_section ? item.school_section.section_name : '-'}">${item.school_section ? item.school_section.section_name : '-'}</div>
                                </td>
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                    <div class="donate-cell-scroll" title="${item.school_session ? item.school_session.session_year : '-'}">${item.school_session ? item.school_session.session_year : '-'}</div>
                                </td>
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                    <div class="donate-cell-scroll" title="${item.fee_type_name || '-'}">${item.fee_type_name || '-'}</div>
                                </td>
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                                    <div class="donate-cell-scroll" title="${item.fee_name || '-'}">${item.fee_name || '-'}</div>
                                </td>
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${item.amount || 0}</td>
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3">${item.pay_date ? formatDate(item.pay_date) : (item.due_day ? 'Every&nbspMonth&nbspDay&nbsp;' + item.due_day : '-')}</td>
                                <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                                    <div class="flex h-6 w-full items-center justify-center -space-x-[3px]">
                                        <button type="button" title="Edit" aria-label="Edit" onclick="editFeeTemplate(${item.id})" class="flex h-6 w-[14px] items-center justify-center text-gray-600 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 hover:bg-gray-100 hover:text-blue-600 focus-visible:ring-blue-500">
                                            <i class="far fa-edit text-xs" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" title="Delete" aria-label="Delete" onclick="deleteFeeTemplate(${item.id})" class="flex h-6 w-[14px] items-center justify-center text-gray-600 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 hover:bg-gray-100 hover:text-red-600 focus-visible:ring-red-500">
                                            <i class="far fa-trash-alt text-xs" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>`;
                    });
                    renderFeePagination(meta);
                })
                .catch(err => console.error('Load Error:', err));
        }

        function renderFeePagination(meta) {
            const controls = document.getElementById('paginationControls');
            document.getElementById('paginationInfo').innerText = `${meta.to || 0} of ${meta.total}`;
            controls.innerHTML = '';
            const prevBtn = document.createElement('button');
            prevBtn.className = 'pagination-btn';
            prevBtn.innerHTML = '<i class="mdi mdi-chevron-left"></i>';
            prevBtn.disabled = meta.current_page === 1;
            prevBtn.onclick = () => fetchFeeTemplates(meta.current_page - 1);
            controls.appendChild(prevBtn);
            for (let i = 1; i <= meta.last_page; i++) {
                const btn = document.createElement('button');
                btn.className = `pagination-btn ${meta.current_page === i ? 'active' : ''}`;
                btn.innerText = i;
                btn.onclick = () => fetchFeeTemplates(i);
                controls.appendChild(btn);
            }
            const nextBtn = document.createElement('button');
            nextBtn.className = 'pagination-btn';
            nextBtn.innerHTML = '<i class="mdi mdi-chevron-right"></i>';
            nextBtn.disabled = meta.current_page === meta.last_page;
            nextBtn.onclick = () => fetchFeeTemplates(meta.current_page + 1);
            controls.appendChild(nextBtn);
        }

        function editFeeTemplate(id) {
            axios.get('/api/fee-templates/' + id)
                .then(res => {
                    const item = res.data;
                    document.getElementById('record_id').value = item.id;
                    document.getElementById('feeModalTitle').innerText = 'Update Fee Template';

                    loadFeeClassSelect(item.class_id);
                    setTimeout(() => {
                        loadFeeGroupSelect(item.group_id);
                        setTimeout(() => {
                            loadFeeSectionSelect(item.section_id);
                            setTimeout(() => {
                                loadFeeSessionSelect(item.session_id);
                                setDropdownValueFromMenu('fee_type_name', item.fee_type_name || '');
                                if (item.fee_type_name) {
                                    handleFeeTypeChange();
                                }
                                setTimeout(() => {
                                    document.getElementById('amount').value = item.amount || '';
                                    document.getElementById('fee_name_input').value = item.fee_name || '';
                                    if (item.pay_date) {
                                        const d = new Date(item.pay_date);
                                        if (!isNaN(d.getTime())) {
                                            document.getElementById('pay_date').value = d.toISOString().split('T')[0];
                                        } else {
                                            document.getElementById('pay_date').value = item.pay_date;
                                        }
                                    }
                                    if (item.frequency) {
                                        setDropdownValueFromMenu('frequency', item.frequency);
                                    }
                                    if (item.due_day) {
                                        setDropdownValueFromMenu('due_day', String(item.due_day));
                                    }
                                    if (item.exam_id) {
                                        setDropdownValueFromMenu('exam_id', String(item.exam_id));
                                        handleExamSelect();
                                    }
                                    if (item.food_type) {
                                        setDropdownValueFromMenu('food_type', item.food_type);
                                        handleFoodTypeChange();
                                    }
                                }, 200);
                                document.getElementById('feeModal').classList.remove('hidden');
                            }, 300);
                        }, 300);
                    }, 300);
                })
                .catch(() => Swal.fire('Error', 'Failed to load fee template data.', 'error'));
        }

        function deleteFeeTemplate(id) {
            Swal.fire({
                title: 'Delete Fee Template?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it'
            }).then(r => {
                if (r.isConfirmed) {
                    axios.delete('/api/fee-templates/' + id)
                        .then(() => {
                            Toastify({ text: 'Fee Template Deleted', style: { background: '#ef4444' } }).showToast();
                            fetchFeeTemplates(currentPage);
                        })
                        .catch(() => Swal.fire('Error', 'Could not delete fee template.', 'error'));
                }
            });
        }

        function showEl(id) { const el = document.getElementById(id); if (el) el.style.display = 'block'; }
        function hideEl(id) { const el = document.getElementById(id); if (el) el.style.display = 'none'; }
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
        function resetConditionalFields() {
            document.querySelectorAll('.hidden-field').forEach(el => el.style.display = 'none');
            hideEl('div_frequency');
            const fn = document.getElementById('fee_name_input');
            if (fn) { fn.value = ''; fn.readOnly = false; }
            const amt = document.getElementById('amount');
            if (amt) amt.value = '';
            const pd = document.getElementById('pay_date');
            if (pd) pd.value = '';
            const dd = document.getElementById('due_day');
            if (dd) { dd.value = ''; resetDropdownLabel('due_day'); }
            const fsl = document.getElementById('food_student_list');
            if (fsl) fsl.innerHTML = '';
            const fsc = document.getElementById('food_student_count');
            if (fsc) fsc.classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            loadFeeFilterOptions();

            document.getElementById('openFeeModalBtn')?.addEventListener('click', openFeeModal);
            const closeFeeModalBtn = document.getElementById('closeFeeModal');
            if (closeFeeModalBtn) closeFeeModalBtn.addEventListener('click', closeFeeModal);
            document.getElementById('feeSearch')?.addEventListener('input', () => fetchFeeTemplates(1));
            document.getElementById('feeSearchMobile')?.addEventListener('input', () => fetchFeeTemplates(1));

            document.getElementById('fee_type_name')?.addEventListener('change', handleFeeTypeChange);
            document.getElementById('food_type')?.addEventListener('change', handleFoodTypeChange);
            document.getElementById('exam_id')?.addEventListener('change', handleExamSelect);

            document.getElementById('feeClassFilter')?.addEventListener('change', function() {
                loadFeeGroupFilterByClass(this.value);
            });

            document.getElementById('feeGroupFilter')?.addEventListener('change', function() {
                const classInput = document.getElementById('feeClassFilter');
                const classId = classInput ? classInput.value : '';
                loadFeeSectionFilterByGroup(classId, this.value);
            });

            document.getElementById('feeSectionFilter')?.addEventListener('change', function() {
                const classInput = document.getElementById('feeClassFilter');
                const classId = classInput ? classInput.value : '';
                const groupInput = document.getElementById('feeGroupFilter');
                const groupId = groupInput ? groupInput.value : '';
                loadFeeSessionFilterBySection(classId, groupId, this.value);
            });

            function toggleFeeTypeDisabled() {
                const classVal = document.getElementById('feeFormClass')?.value;
                const sessionVal = document.getElementById('feeFormSession')?.value;
                const wrapper = document.getElementById('fee_type_wrapper');
                const btn = wrapper?.querySelector('[data-dropdown-select-button]');
                if (!classVal || !sessionVal) {
                    document.getElementById('fee_type_name').value = '';
                    const label = wrapper?.querySelector('[data-dropdown-select-label]');
                    if (label) label.textContent = 'Select Fee Type';
                    if (btn) { btn.disabled = true; btn.style.opacity = '0.5'; btn.style.cursor = 'not-allowed'; }
                } else {
                    if (btn) { btn.disabled = false; btn.style.opacity = '1'; btn.style.cursor = 'pointer'; }
                }
            }

            document.getElementById('feeFormClass')?.addEventListener('change', function() {
                setDropdownValue('feeFormGroup', '', 'Select Group');
                setDropdownValue('feeFormSection', '', 'Select Section');
                setDropdownValue('feeFormSession', '', 'Select Session');
                populateDropdown('feeFormGroupMenu', [], 'id', 'group_name');
                populateDropdown('feeFormSectionMenu', [], 'id', 'section_name');
                populateDropdown('feeFormSessionMenu', [], 'id', 'session_year');
                if (this.value) loadFeeGroupSelect();
                toggleFeeTypeDisabled();
                reloadExamsForCurrentType();
            });

            document.getElementById('feeFormGroup')?.addEventListener('change', function() {
                setDropdownValue('feeFormSection', '', 'Select Section');
                setDropdownValue('feeFormSession', '', 'Select Session');
                populateDropdown('feeFormSectionMenu', [], 'id', 'section_name');
                populateDropdown('feeFormSessionMenu', [], 'id', 'session_year');
                if (this.value) loadFeeSectionSelect();
                reloadExamsForCurrentType();
            });

            document.getElementById('feeFormSection')?.addEventListener('change', function() {
                setDropdownValue('feeFormSession', '', 'Select Session');
                populateDropdown('feeFormSessionMenu', [], 'id', 'session_year');
                if (this.value) loadFeeSessionSelect();
                reloadExamsForCurrentType();
            });

            document.getElementById('feeFormSession')?.addEventListener('change', function() {
                toggleFeeTypeDisabled();
                const type = document.getElementById('fee_type_name')?.value;
                if (type === 'Promote') autoGenerateFeeName(type);
                reloadExamsForCurrentType();
            });

            function reloadExamsForCurrentType() {
                const type = document.getElementById('fee_type_name')?.value;
                if (type === 'Exams') loadExams();
            }

            document.getElementById('btnFilter')?.addEventListener('click', () => {
                document.getElementById('filterModal')?.classList.remove('hidden');
            });
            document.getElementById('resetFilter')?.addEventListener('click', () => {
                setDropdownValue('feeClassFilter', '', 'Select Class');
                setDropdownValue('feeGroupFilter', '', 'Select Group');
                setDropdownValue('feeSectionFilter', '', 'Select Section');
                setDropdownValue('feeSessionFilter', '', 'Select Session');
                populateDropdown('feeGroupFilterMenu', [], 'id', 'group_name');
                populateDropdown('feeSectionFilterMenu', [], 'id', 'section_name');
                populateDropdown('feeSessionFilterMenu', [], 'id', 'session_year');
                currentFilterClass = '';
                currentFilterGroup = '';
                currentFilterSection = '';
                currentFilterSession = '';
                currentPage = 1;
                fetchFeeTemplates(1);
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
                currentPage = 1;
                fetchFeeTemplates(1);
                document.getElementById('filterModal')?.classList.add('hidden');
            });
            document.getElementById('btnRestoreDesktop')?.addEventListener('click', () => {
                document.getElementById('feeSearch').value = '';
                currentFilterClass = '';
                currentFilterGroup = '';
                currentFilterSection = '';
                currentFilterSession = '';
                currentPage = 1;
                fetchFeeTemplates(1);
            });
            document.getElementById('btnRestoreMobile')?.addEventListener('click', () => {
                document.getElementById('feeSearchMobile').value = '';
                currentFilterClass = '';
                currentFilterGroup = '';
                currentFilterSection = '';
                currentFilterSession = '';
                currentPage = 1;
                fetchFeeTemplates(1);
            });
            document.querySelectorAll('[role="dialog"]').forEach(dialog => {
                dialog.addEventListener('click', function(e) {
                    if (e.target === this) this.classList.add('hidden');
                });
            });

            document.addEventListener('click', function(e) {
                const btn = e.target.closest('[data-dropdown-add-target]');
                if (btn) {
                    const map = { classModal: 'Add Class', groupModal: 'Add Group', sectionModal: 'Add Section', sessionModal: 'Add Session', feeModal: 'Add Fee Template' };
                    const title = map[btn.dataset.dropdownAddTarget];
                    if (title) {
                        const el = document.getElementById(btn.dataset.dropdownAddTarget + 'Title');
                        if (el) el.innerText = title;
                    }
                }
            });
        });

        fetchFeeTemplates();
    </script>
@endsection
