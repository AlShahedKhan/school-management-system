<script>
    function loadGroupClassSelect(selectedId = null) {
        return axios.get('/api/get-school-classes').then(res => {
            const data = res.data.data || [];
            populateDropdown('groupClassSelectMenu', data, 'id', 'class_name');

            if (selectedId) {
                const item = data.find(c => String(c.id) === String(selectedId));
                if (item) {
                    setDropdownValue('groupClassSelect', item.id, item.class_name);
                }
            }
        }).catch(err => console.error('Class dropdown error:', err));
    }

    function openGroupModal(title, selectedClassId = null) {
        document.getElementById('groupForm').reset();
        document.getElementById('group_id').value = '';
        document.getElementById('groupModalTitle').innerText = typeof title === 'string' ? title : 'Add Group';
        document.getElementById('groupModal').classList.remove('hidden');
        setDropdownValue('groupClassSelect', '', 'Select Class');
        loadGroupClassSelect(selectedClassId);
    }

    function closeGroupModal() {
        const groupModalElement = document.getElementById('groupModal');
        const returnModalId = groupModalElement?.dataset.returnModalId || null;

        groupModalElement?.classList.add('hidden');

        if (returnModalId) {
            document.getElementById(returnModalId)?.classList.remove('hidden');
            delete groupModalElement.dataset.returnModalId;
        }
    }

    document.getElementById('openGroupModalBtn')?.addEventListener('click', openGroupModal);
    const closeGroupModalBtn = document.getElementById('closeGroupModal');
    if (closeGroupModalBtn) closeGroupModalBtn.addEventListener('click', closeGroupModal);

    document.addEventListener('school:dropdown-add-modal-opened', function (event) {
        const { targetModalId, sourceDropdownId } = event.detail || {};

        if (targetModalId !== 'groupModal') {
            return;
        }

        document.getElementById('groupForm')?.reset();
        document.getElementById('group_id').value = '';
        document.getElementById('groupModalTitle').innerText = 'Add Group';
        setDropdownValue('groupClassSelect', '', 'Select Class');

        const sourceClassMap = {
            examFormGroup: 'examFormClass',
            m_group: 'm_class',
            examGroupFilter: 'examClassFilter',
            sectionGroupSelect: 'sectionClassSelect',
            sessionFormGroup: 'sessionFormClass',
            group_name: 'class_name',
        };
        const sourceClassInputId = sourceClassMap[sourceDropdownId] || '';
        let selectedClassId = null;

        if (sourceClassInputId) {
            const sourceClassInput = document.getElementById(sourceClassInputId);
            if (sourceClassInput) {
                selectedClassId = sourceClassInput.dataset.dropdownSelectInput !== undefined
                    ? getSelectedDataId(sourceClassInput)
                    : sourceClassInput.value || null;
            }
        }

        loadGroupClassSelect(selectedClassId);
    });

    document.addEventListener('school:class-saved', async function (event) {
        const { classItem, returnModalId, isNew } = event.detail || {};

        if (returnModalId !== 'groupModal' || !isNew || !classItem?.id) {
            return;
        }

        event.preventDefault();

        try {
            await loadGroupClassSelect(classItem.id);
            setDropdownValue('groupClassSelect', classItem.id, classItem.class_name);
        } finally {
            document.getElementById('groupModal')?.classList.remove('hidden');
        }
    });
</script>
