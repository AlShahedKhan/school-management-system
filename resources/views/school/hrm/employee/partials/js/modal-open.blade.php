<script>
    const expenseModal = document.getElementById('employeeModal');
    document.getElementById('openEmployeeModal').addEventListener('click', () => {
        expenseModal.classList.remove('hidden');
    });
    document.getElementById('closeEmployeeModal').addEventListener('click', () => {
        expenseModal.classList.add('hidden');
    });
</script>