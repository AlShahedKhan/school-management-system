<script>
    let expenseSearchTimer = null;
    const getExpenseSearchValue = () => {
        const desktopSearch = document.getElementById('expenseSearch');
        const mobileSearch = document.getElementById('expenseSearchMobile');
        const activeElement = document.activeElement;
        if (activeElement === mobileSearch) {
            return mobileSearch.value;
        }
        if (activeElement === desktopSearch) {
            return desktopSearch.value;
        }
        return desktopSearch?.value || mobileSearch?.value || '';
    };
    const reloadExpensePage = () => {
        const url = new URL('{{ route('school.expense') }}', window.location.origin);
        const search = getExpenseSearchValue().trim();
        if (search) {
            url.searchParams.set('search', search);
        }
        window.location.href = url.toString();
    };
    const bindExpenseSearch = (input, peer) => {
        if (!input) {
            return;
        }
        input.addEventListener('input', () => {
            if (peer) {
                peer.value = input.value;
            }
            clearTimeout(expenseSearchTimer);
            expenseSearchTimer = setTimeout(() => {
                reloadExpensePage();
            }, 450);
        });
    };
    bindExpenseSearch(
        document.getElementById('expenseSearch'),
        document.getElementById('expenseSearchMobile')
    );
    bindExpenseSearch(
        document.getElementById('expenseSearchMobile'),
        document.getElementById('expenseSearch')
    );
</script>