<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btnFilter = document.getElementById('btnFilter');
        const filterModal = document.getElementById('filterModal');
        const applyFilter = document.getElementById('applyFilter');
        const resetFilter = document.getElementById('resetFilter');
        const expenseFilterForm = document.getElementById('expenseFilterForm');
        if (btnFilter && filterModal) {
            btnFilter.addEventListener('click', () => {
                filterModal.classList.remove('hidden');
                filterModal.classList.add('flex');
            });
        }
        if (applyFilter && expenseFilterForm) {
            applyFilter.addEventListener('click', () => {
                expenseFilterForm.submit();
            });
        }
        if (resetFilter) {
            resetFilter.addEventListener('click', () => {
                window.location.href = '{{ route('school.expense') }}';
            });
        }
    });
</script>