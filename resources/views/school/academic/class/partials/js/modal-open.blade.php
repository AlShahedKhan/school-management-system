<script>
    function openClassModal(title) {
        document.getElementById('classForm').reset();
        document.getElementById('class_id').value = '';
        document.getElementById('classModalTitle').innerText = typeof title === 'string' ? title : 'Add Class';
        document.getElementById('classModal').classList.remove('hidden');
    }

    function closeClassModal() {
        const modal = document.getElementById('classModal');
        const returnModalId = modal?.dataset.returnModalId;

        modal?.classList.add('hidden');

        if (returnModalId) {
            document.getElementById(returnModalId)?.classList.remove('hidden');
            delete modal.dataset.returnModalId;
        }
    }

    document.getElementById('openClassModalBtn')?.addEventListener('click', openClassModal);

    const closeClassModalBtn = document.getElementById('closeClassModal');
    if (closeClassModalBtn) {
        closeClassModalBtn.addEventListener('click', closeClassModal);
    }
</script>
