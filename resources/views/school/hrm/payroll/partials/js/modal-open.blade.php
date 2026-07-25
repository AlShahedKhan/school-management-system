<script>
    const expenseModal = document.getElementById('payrollModal');
    document.getElementById('openPayrollModal').addEventListener('click', () => {
        expenseModal.classList.remove('hidden');
    });
    document.getElementById('closePayrollModal').addEventListener('click', () => {
        expenseModal.classList.add('hidden');
    });
</script>