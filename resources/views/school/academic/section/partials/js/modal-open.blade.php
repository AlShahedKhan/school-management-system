<script>
    function openSectionModal(title) {
        document.getElementById('sectionForm').reset();
        document.getElementById('section_record_id').value = '';
        document.getElementById('sectionModalTitle').innerText = typeof title === 'string' ? title : 'Add Section';
        document.getElementById('sectionModal').classList.remove('hidden');
        setDropdownValue('sectionClassSelect', '', 'Select Class');
        setDropdownValue('sectionGroupSelect', '', 'Select Group');
        loadSectionClassSelect();
        populateDropdown('sectionGroupSelectMenu', [], 'id', 'group_name');
    }
    function closeSectionModal() {
        document.getElementById('sectionModal').classList.add('hidden');
    }
    document.getElementById('openSectionModalBtn')?.addEventListener('click', openSectionModal);
    const closeSectionModalBtn = document.getElementById('closeSectionModal');
    if (closeSectionModalBtn) closeSectionModalBtn.addEventListener('click', closeSectionModal);
</script>
