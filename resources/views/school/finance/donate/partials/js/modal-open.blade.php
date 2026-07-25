<script>
    const expenseModal = document.getElementById('donateModal');
    document.getElementById('openDonateModal').addEventListener('click', () => {
        expenseModal.classList.remove('hidden');
    });
    document.getElementById('closeDonateModal').addEventListener('click', () => {
        expenseModal.classList.add('hidden');
    });
</script>