<script>
    function loadSectionClassSelect(selectedId = null) {
        return axios.get('/api/get-school-classes').then(res => {
            const data = res.data.data || [];
            populateDropdown('sectionClassSelectMenu', data, 'id', 'class_name');

            if (selectedId) {
                const item = data.find(c => String(c.id) === String(selectedId));
                if (item) {
                    setDropdownValue('sectionClassSelect', item.id, item.class_name);
                }
            }
        }).catch(err => console.error('Class dropdown error:', err));
    }

    function loadSectionGroupSelect(classId = null, selectedId = null) {
        const params = classId ? { class_id: classId } : {};

        return axios.get('/api/get-school-groups', { params }).then(res => {
            const data = res.data.data || [];
            populateDropdown('sectionGroupSelectMenu', data, 'id', 'group_name');

            if (selectedId) {
                const item = data.find(g => String(g.id) === String(selectedId));
                if (item) {
                    setDropdownValue('sectionGroupSelect', item.id, item.group_name);
                }
            }
        }).catch(err => console.error('Group dropdown error:', err));
    }

    function openSectionModal(title, selectedClassId = null, selectedGroupId = null) {
        document.getElementById('sectionForm').reset();
        document.getElementById('section_record_id').value = '';
        document.getElementById('sectionModalTitle').innerText = typeof title === 'string' ? title : 'Add Section';
        document.getElementById('sectionModal').classList.remove('hidden');
        setDropdownValue('sectionClassSelect', '', 'Select Class');
        setDropdownValue('sectionGroupSelect', '', 'Select Group');
        loadSectionClassSelect(selectedClassId);
        populateDropdown('sectionGroupSelectMenu', [], 'id', 'group_name');

        if (selectedClassId) {
            loadSectionGroupSelect(selectedClassId, selectedGroupId);
        }
    }

    function closeSectionModal() {
        const sectionModalElement = document.getElementById('sectionModal');
        const returnModalId = sectionModalElement?.dataset.returnModalId || null;

        sectionModalElement?.classList.add('hidden');

        if (returnModalId) {
            document.getElementById(returnModalId)?.classList.remove('hidden');
            delete sectionModalElement.dataset.returnModalId;
        }
    }

    document.getElementById('openSectionModalBtn')?.addEventListener('click', openSectionModal);
    const closeSectionModalBtn = document.getElementById('closeSectionModal');
    if (closeSectionModalBtn) closeSectionModalBtn.addEventListener('click', closeSectionModal);

    document.addEventListener('click', function (event) {
        const groupAddButton = event.target.closest('[data-dropdown-add-target="groupModal"]');

        if (!groupAddButton) {
            return;
        }

        const sourceDropdown = groupAddButton.closest('[data-dropdown-select]')
            ?.querySelector('[data-dropdown-select-input]');

        if (sourceDropdown?.id !== 'sectionGroupSelect') {
            return;
        }

        const classId = document.getElementById('sectionClassSelect')?.value || '';

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
    }, true);

    document.addEventListener('school:dropdown-add-modal-opened', function (event) {
        const { targetModalId, sourceDropdownId } = event.detail || {};

        if (targetModalId !== 'sectionModal') {
            return;
        }

        document.getElementById('sectionForm')?.reset();
        document.getElementById('section_record_id').value = '';
        document.getElementById('sectionModalTitle').innerText = 'Add Section';
        setDropdownValue('sectionClassSelect', '', 'Select Class');
        setDropdownValue('sectionGroupSelect', '', 'Select Group');
        populateDropdown('sectionGroupSelectMenu', [], 'id', 'group_name');

        const sourceMap = {
            examFormSection: ['examFormClass', 'examFormGroup'],
            examSectionFilter: ['examClassFilter', 'examGroupFilter'],
            sessionFormSection: ['sessionFormClass', 'sessionFormGroup'],
            section_name: ['class_name', 'group_name'],
        };
        const [classInputId, groupInputId] = sourceMap[sourceDropdownId] || [];
        let selectedClassId = null;
        let selectedGroupId = null;

        if (classInputId) {
            const classInput = document.getElementById(classInputId);
            if (classInput) {
                selectedClassId = classInput.dataset.dropdownSelectInput !== undefined
                    ? getSelectedDataId(classInput)
                    : classInput.value || null;
            }
        }

        if (groupInputId) {
            const groupInput = document.getElementById(groupInputId);
            if (groupInput) {
                selectedGroupId = groupInput.dataset.dropdownSelectInput !== undefined
                    ? getSelectedDataId(groupInput)
                    : groupInput.value || null;
            }
        }

        loadSectionClassSelect(selectedClassId);
        
        if (selectedClassId) {
            loadSectionGroupSelect(selectedClassId, selectedGroupId);
        }
    });

    document.addEventListener('school:class-saved', async function (event) {
        const { classItem, returnModalId, isNew } = event.detail || {};

        if (returnModalId !== 'sectionModal' || !isNew || !classItem?.id) {
            return;
        }

        event.preventDefault();

        try {
            await loadSectionClassSelect(classItem.id);
            setDropdownValue('sectionClassSelect', classItem.id, classItem.class_name);
            setDropdownValue('sectionGroupSelect', '', 'Select Group');
            populateDropdown('sectionGroupSelectMenu', [], 'id', 'group_name');
        } finally {
            document.getElementById('sectionModal')?.classList.remove('hidden');
        }
    });

    document.addEventListener('school:group-saved', async function (event) {
        const { groupItem, returnModalId, isNew } = event.detail || {};

        if (returnModalId !== 'sectionModal' || !isNew || !groupItem?.id || !groupItem?.class_id) {
            return;
        }

        event.preventDefault();

        try {
            await loadSectionClassSelect(groupItem.class_id);
            await loadSectionGroupSelect(groupItem.class_id, groupItem.id);
        } finally {
            document.getElementById('sectionModal')?.classList.remove('hidden');
        }
    });

    document.getElementById('sectionClassSelect')?.addEventListener('change', function () {
        setDropdownValue('sectionGroupSelect', '', 'Select Group');
        populateDropdown('sectionGroupSelectMenu', [], 'id', 'group_name');

        if (this.value) {
            loadSectionGroupSelect(this.value);
        }
    });
</script>
