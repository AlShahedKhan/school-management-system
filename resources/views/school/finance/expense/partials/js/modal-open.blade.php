<script>
    const expenseModal = document.getElementById('expenseModal');
    document.getElementById('openExpenseModal').addEventListener('click', () => {
        expenseModal.classList.remove('hidden');
    });
    document.getElementById('closeExpenseModal').addEventListener('click', () => {
        expenseModal.classList.add('hidden');
    });
</script>