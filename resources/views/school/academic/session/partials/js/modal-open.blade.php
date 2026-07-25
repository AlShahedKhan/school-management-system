<script>
    function openSessionModal(title) {
        document.getElementById('sessionForm').reset();
        document.getElementById('record_id').value = '';
        document.getElementById('sessionModalTitle').innerText = typeof title === 'string' ? title : 'Add Session';
        document.getElementById('sessionModal').classList.remove('hidden');
        setDropdownValue('sessionFormClass', '', 'Select Class');
        setDropdownValue('sessionFormGroup', '', 'Select Group');
        setDropdownValue('sessionFormSection', '', 'Select Section');
        populateDropdown('sessionFormGroupMenu', [], 'id', 'group_name');
        populateDropdown('sessionFormSectionMenu', [], 'id', 'section_name');
        loadSessionClassSelect();
    }
    function closeSessionModal() {
        document.getElementById('sessionModal').classList.add('hidden');
    }
    document.getElementById('openSessionModalBtn')?.addEventListener('click', openSessionModal);
    const closeSessionModalBtn = document.getElementById('closeSessionModal');
    if (closeSessionModalBtn) closeSessionModalBtn.addEventListener('click', closeSessionModal);
</script>
