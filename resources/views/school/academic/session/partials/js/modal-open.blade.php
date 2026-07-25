<script>
    function calculateSession() {
        const startInput = document.getElementById('start_date');
        const endInput = document.getElementById('end_date');
        const sessionYearInput = document.getElementById('session_year');
        const totalDaysInput = document.getElementById('total_days');
        const remainingDaysInput = document.getElementById('remaining_days');

        if (!startInput || !endInput || !sessionYearInput || !totalDaysInput || !remainingDaysInput) {
            return;
        }

        const startVal = startInput.value;
        const endVal = endInput.value;

        sessionYearInput.value = '';
        totalDaysInput.value = '';
        remainingDaysInput.value = '';

        if (!startVal || !endVal) {
            return;
        }

        const start = new Date(`${startVal}T00:00:00`);
        const end = new Date(`${endVal}T00:00:00`);

        if (Number.isNaN(start.getTime()) || Number.isNaN(end.getTime()) || end < start) {
            return;
        }

        const sYear = start.getFullYear();
        const eYearShort = String(end.getFullYear()).slice(-2);
        const total = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const remaining = Math.ceil((end - today) / (1000 * 60 * 60 * 24));

        sessionYearInput.value = `${sYear}-${eYearShort}`;
        totalDaysInput.value = total > 0 ? total : 0;
        remainingDaysInput.value = remaining > 0 ? remaining : 0;
    }

    function loadSessionClassSelect(selectedId = null) {
        return axios.get('/api/get-school-classes').then(res => {
            const data = res.data.data || [];
            populateDropdown('sessionFormClassMenu', data, 'id', 'class_name');

            if (selectedId) {
                const item = data.find(c => String(c.id) === String(selectedId));
                if (item) {
                    setDropdownValue('sessionFormClass', item.id, item.class_name);
                }
            }
        }).catch(err => console.error('Class dropdown error:', err));
    }

    function loadSessionGroupSelect(selectedClassId = null, selectedGroupId = null) {
        const classId = selectedClassId || document.querySelector('#sessionFormClass')?.value;
        const params = classId ? { class_id: classId } : {};

        return axios.get('/api/get-school-groups', { params }).then(res => {
            const data = res.data.data || [];
            populateDropdown('sessionFormGroupMenu', data, 'id', 'group_name');

            if (selectedGroupId) {
                const item = data.find(g => String(g.id) === String(selectedGroupId));
                if (item) {
                    setDropdownValue('sessionFormGroup', item.id, item.group_name);
                }
            }
        }).catch(err => console.error('Group dropdown error:', err));
    }

    function loadSessionSectionSelect(selectedClassId = null, selectedGroupId = null, selectedSectionId = null) {
        const classId = selectedClassId || document.querySelector('#sessionFormClass')?.value;
        const groupId = selectedGroupId || document.querySelector('#sessionFormGroup')?.value;
        const params = {};

        if (classId) params.class_id = classId;
        if (groupId) params.group_id = groupId;

        return axios.get('/api/get-school-sections', { params }).then(res => {
            const data = res.data.data || [];
            populateDropdown('sessionFormSectionMenu', data, 'id', 'section_name');

            if (selectedSectionId) {
                const item = data.find(s => String(s.id) === String(selectedSectionId));
                if (item) {
                    setDropdownValue('sessionFormSection', item.id, item.section_name);
                }
            }
        }).catch(err => console.error('Section dropdown error:', err));
    }

    function openSessionModal(title, selectedClassId = null, selectedGroupId = null, selectedSectionId = null) {
        document.getElementById('sessionForm').reset();
        document.getElementById('record_id').value = '';
        document.getElementById('sessionModalTitle').innerText = typeof title === 'string' ? title : 'Add Session';
        document.getElementById('sessionModal').classList.remove('hidden');
        setDropdownValue('sessionFormClass', '', 'Select Class');
        setDropdownValue('sessionFormGroup', '', 'Select Group');
        setDropdownValue('sessionFormSection', '', 'Select Section');
        populateDropdown('sessionFormGroupMenu', [], 'id', 'group_name');
        populateDropdown('sessionFormSectionMenu', [], 'id', 'section_name');
        loadSessionClassSelect(selectedClassId);

        if (selectedClassId) {
            loadSessionGroupSelect(selectedClassId, selectedGroupId);
        }

        if (selectedClassId && selectedGroupId) {
            loadSessionSectionSelect(selectedClassId, selectedGroupId, selectedSectionId);
        }
    }

    function closeSessionModal() {
        const sessionModalElement = document.getElementById('sessionModal');
        const returnModalId = sessionModalElement?.dataset.returnModalId || null;

        sessionModalElement?.classList.add('hidden');

        if (returnModalId) {
            document.getElementById(returnModalId)?.classList.remove('hidden');
            delete sessionModalElement.dataset.returnModalId;
        }
    }

    document.getElementById('openSessionModalBtn')?.addEventListener('click', openSessionModal);
    const closeSessionModalBtn = document.getElementById('closeSessionModal');
    if (closeSessionModalBtn) closeSessionModalBtn.addEventListener('click', closeSessionModal);

    document.addEventListener('click', function (event) {
        const groupAddButton = event.target.closest('[data-dropdown-add-target="groupModal"]');
        const sectionAddButton = event.target.closest('[data-dropdown-add-target="sectionModal"]');

        if (!groupAddButton && !sectionAddButton) {
            return;
        }

        const sourceDropdown = (groupAddButton || sectionAddButton).closest('[data-dropdown-select]')
            ?.querySelector('[data-dropdown-select-input]');

        if (sourceDropdown?.id === 'sessionFormGroup') {
            const classId = document.getElementById('sessionFormClass')?.value || '';

            if (classId) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();

            Toastify({
                text: 'Please select class first.',
                gravity: 'top',
                position: 'right',
                style: { background: '#f59e0b' },
            }).showToast();
            return;
        }

        if (sourceDropdown?.id === 'sessionFormSection') {
            const classId = document.getElementById('sessionFormClass')?.value || '';
            const groupId = document.getElementById('sessionFormGroup')?.value || '';

            if (classId && groupId) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();

            Toastify({
                text: 'Please select class and group first.',
                gravity: 'top',
                position: 'right',
                style: { background: '#f59e0b' },
            }).showToast();
        }
    }, true);

    document.addEventListener('school:dropdown-add-modal-opened', function (event) {
        const { targetModalId, sourceDropdownId } = event.detail || {};

        if (targetModalId !== 'sessionModal') {
            return;
        }

        document.getElementById('sessionForm')?.reset();
        document.getElementById('record_id').value = '';
        document.getElementById('sessionModalTitle').innerText = 'Add Session';
        setDropdownValue('sessionFormClass', '', 'Select Class');
        setDropdownValue('sessionFormGroup', '', 'Select Group');
        setDropdownValue('sessionFormSection', '', 'Select Section');
        populateDropdown('sessionFormGroupMenu', [], 'id', 'group_name');
        populateDropdown('sessionFormSectionMenu', [], 'id', 'section_name');

        const sourceMap = {
            examFormSession: ['examFormClass', 'examFormGroup', 'examFormSection'],
            examSessionFilter: ['examClassFilter', 'examGroupFilter', 'examSectionFilter'],
            sessionYearFilter: ['sessionClassFilter', 'sessionGroupFilter', 'sessionSectionFilter'],
        };
        const [classInputId, groupInputId, sectionInputId] = sourceMap[sourceDropdownId] || [];
        const selectedClassId = document.getElementById(classInputId || '')?.value || null;
        const selectedGroupId = document.getElementById(groupInputId || '')?.value || null;
        const selectedSectionId = document.getElementById(sectionInputId || '')?.value || null;

        loadSessionClassSelect(selectedClassId);

        if (selectedClassId) {
            loadSessionGroupSelect(selectedClassId, selectedGroupId);
        }

        if (selectedClassId && selectedGroupId) {
            loadSessionSectionSelect(selectedClassId, selectedGroupId, selectedSectionId);
        }
    });

    document.addEventListener('school:class-saved', async function (event) {
        const { classItem, returnModalId, isNew } = event.detail || {};

        if (returnModalId !== 'sessionModal' || !isNew || !classItem?.id) {
            return;
        }

        event.preventDefault();

        try {
            await loadSessionClassSelect(classItem.id);
            setDropdownValue('sessionFormClass', classItem.id, classItem.class_name);
            setDropdownValue('sessionFormGroup', '', 'Select Group');
            setDropdownValue('sessionFormSection', '', 'Select Section');
            populateDropdown('sessionFormGroupMenu', [], 'id', 'group_name');
            populateDropdown('sessionFormSectionMenu', [], 'id', 'section_name');
        } finally {
            document.getElementById('sessionModal')?.classList.remove('hidden');
        }
    });

    document.addEventListener('school:group-saved', async function (event) {
        const { groupItem, returnModalId, isNew } = event.detail || {};

        if (returnModalId !== 'sessionModal' || !isNew || !groupItem?.id || !groupItem?.class_id) {
            return;
        }

        event.preventDefault();

        try {
            await loadSessionClassSelect(groupItem.class_id);
            await loadSessionGroupSelect(groupItem.class_id, groupItem.id);
            setDropdownValue('sessionFormSection', '', 'Select Section');
            populateDropdown('sessionFormSectionMenu', [], 'id', 'section_name');
        } finally {
            document.getElementById('sessionModal')?.classList.remove('hidden');
        }
    });

    document.addEventListener('school:section-saved', async function (event) {
        const { sectionItem, returnModalId, isNew } = event.detail || {};

        if (
            returnModalId !== 'sessionModal'
            || !isNew
            || !sectionItem?.id
            || !sectionItem?.class_id
            || !sectionItem?.group_id
        ) {
            return;
        }

        event.preventDefault();

        try {
            await loadSessionClassSelect(sectionItem.class_id);
            await loadSessionGroupSelect(sectionItem.class_id, sectionItem.group_id);
            await loadSessionSectionSelect(sectionItem.class_id, sectionItem.group_id, sectionItem.id);
        } finally {
            document.getElementById('sessionModal')?.classList.remove('hidden');
        }
    });

    document.getElementById('sessionFormClass')?.addEventListener('change', function () {
        setDropdownValue('sessionFormGroup', '', 'Select Group');
        setDropdownValue('sessionFormSection', '', 'Select Section');
        populateDropdown('sessionFormGroupMenu', [], 'id', 'group_name');
        populateDropdown('sessionFormSectionMenu', [], 'id', 'section_name');

        if (this.value) {
            loadSessionGroupSelect(this.value);
        }
    });

    document.getElementById('sessionFormGroup')?.addEventListener('change', function () {
        const classId = document.getElementById('sessionFormClass')?.value || '';
        setDropdownValue('sessionFormSection', '', 'Select Section');
        populateDropdown('sessionFormSectionMenu', [], 'id', 'section_name');

        if (classId && this.value) {
            loadSessionSectionSelect(classId, this.value);
        }
    });

    document.getElementById('start_date')?.addEventListener('change', calculateSession);
    document.getElementById('end_date')?.addEventListener('change', calculateSession);
</script>
