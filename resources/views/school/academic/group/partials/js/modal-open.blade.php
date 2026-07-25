<script>
    function openGroupModal(title) {
        document.getElementById('groupForm').reset();
        document.getElementById('group_id').value = '';
        document.getElementById('groupModalTitle').innerText = typeof title === 'string' ? title : 'Add Group';
        document.getElementById('groupModal').classList.remove('hidden');
        setDropdownValue('groupClassSelect', '', 'Select Class');
        loadGroupClassSelect();
    }
    function closeGroupModal() {
        document.getElementById('groupModal').classList.add('hidden');
    }
    document.getElementById('openGroupModalBtn')?.addEventListener('click', openGroupModal);
    const closeGroupModalBtn = document.getElementById('closeGroupModal');
    if (closeGroupModalBtn) closeGroupModalBtn.addEventListener('click', closeGroupModal);
</script>
