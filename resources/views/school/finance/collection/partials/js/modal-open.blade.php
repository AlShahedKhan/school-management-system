<script>
    const expenseModal = document.getElementById('collectionModal');
    document.getElementById('openCollectionModal').addEventListener('click', () => {
        expenseModal.classList.remove('hidden');
    });
    document.getElementById('closeCollectionModal').addEventListener('click', () => {
        expenseModal.classList.add('hidden');
    });
</script>