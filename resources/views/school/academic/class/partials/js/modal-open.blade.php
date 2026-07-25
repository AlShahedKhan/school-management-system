<script>
    function openClassModal(title) {
        document.getElementById('classForm').reset();
        document.getElementById('class_id').value = '';
        document.getElementById('classModalTitle').innerText = typeof title === 'string' ? title : 'Add Class';
        document.getElementById('classModal').classList.remove('hidden');
    }

    function closeClassModal() {
        document.getElementById('classModal').classList.add('hidden');
    }

    document.getElementById('openClassModalBtn')?.addEventListener('click', openClassModal);

    const closeClassModalBtn = document.getElementById('closeClassModal');
    if (closeClassModalBtn) {
        closeClassModalBtn.addEventListener('click', closeClassModal);
    }
</script>
