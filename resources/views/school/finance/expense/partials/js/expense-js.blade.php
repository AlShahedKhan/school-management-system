<script>
    const expenseModal = document.getElementById('expenseModal');
    const expenseForm = document.getElementById('expenseForm');
    let currentPage = 1;

    function setFormEditable(editable) {
        const inputs = expenseForm.querySelectorAll('input:not([type="hidden"]), textarea');
        inputs.forEach(input => {
            input.readOnly = !editable;
            if (!editable) {
                input.classList.add('bg-gray-100', 'cursor-not-allowed');
            } else {
                input.classList.remove('bg-gray-100', 'cursor-not-allowed');
            }
        });
    }

    function enableFormEditing() {
        setFormEditable(true);
        const dateInput = document.querySelector('#expenseForm input[name="date"]');
        if (dateInput) dateInput.focus();
    }

    // Open Add Modal
    document.getElementById('openExpenseModal')?.addEventListener('click', () => {
        expenseForm.reset();
        document.getElementById('expense_id').value = '';
        
        const todayStr = new Date().toISOString().split('T')[0];

        const dateEl = document.querySelector('#expenseForm input[name="date"]');
        if (dateEl) dateEl.value = todayStr;

        setFormEditable(true);

        const titleEl = document.getElementById('expenseModalTitle');
        if (titleEl) titleEl.textContent = 'Add Expense';

        const enableEditBtn = document.getElementById('enableEditBtn');
        if (enableEditBtn) enableEditBtn.classList.add('hidden');

        const submitBtn = document.getElementById('submitExpenseBtn');
        if (submitBtn) submitBtn.textContent = 'Save';

        const footer = document.getElementById('expenseModalFooter');
        if (footer) footer.className = 'grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 pt-3 pb-4';

        expenseModal.classList.remove('hidden');
    });

    // Close Modal
    document.getElementById('closeExpenseModal')?.addEventListener('click', () => {
        expenseModal.classList.add('hidden');
    });

    // Edit Expense
    function editExpense(id) {
        axios.get('{{ url('/api/expenses') }}/' + id)
            .then(res => {
                const exp = res.data.data || res.data;
                document.getElementById('expense_id').value = exp.id;
                
                let dateVal = exp.date || '';
                if (dateVal.includes('T')) dateVal = dateVal.split('T')[0];
                document.querySelector('#expenseForm input[name="date"]').value = dateVal;

                const invEl = document.querySelector('#expenseForm input[name="invoice_no"]');
                if (invEl) invEl.value = exp.invoice_no || '';

                document.querySelector('#expenseForm input[name="expense_reason"]').value = exp.expense_reason || exp.name || '';
                
                const detailsEl = document.querySelector('#expenseForm input[name="details"]') || document.querySelector('#expenseForm textarea[name="details"]');
                if (detailsEl) detailsEl.value = exp.details || '';

                document.querySelector('#expenseForm input[name="amount"]').value = exp.amount || '';

                // Initial state: Not editable
                setFormEditable(false);

                const titleEl = document.getElementById('expenseModalTitle');
                if (titleEl) titleEl.textContent = 'Edit Expense';

                const enableEditBtn = document.getElementById('enableEditBtn');
                if (enableEditBtn) enableEditBtn.classList.remove('hidden');

                const submitBtn = document.getElementById('submitExpenseBtn');
                if (submitBtn) submitBtn.textContent = 'Save';

                const footer = document.getElementById('expenseModalFooter');
                if (footer) footer.className = 'grid grid-cols-3 gap-2 border-slate-200 bg-white px-6 pt-3 pb-4';

                expenseModal.classList.remove('hidden');
            })
            .catch(err => {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load expense details.'
                });
            });
    }

    // Delete Expense with SweetAlert2
    function deleteExpense(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete('{{ url('/api/expenses') }}/' + id, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(() => {
                    Toastify({
                        text: "Expense deleted successfully!",
                        gravity: "top",
                        position: "right",
                        style: {
                            background: "#ef4444"
                        }
                    }).showToast();
                    if (!expenseModal.classList.contains('hidden')) {
                        expenseModal.classList.add('hidden');
                    }
                    fetchExpenses(currentPage);
                }).catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Delete Failed',
                        text: err.response?.data?.message || 'Something went wrong.'
                    });
                });
            }
        });
    }

    // Submit Form
    expenseForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const expId = document.getElementById('expense_id').value;
        const formData = new FormData(this);

        let apiUrl = '{{ url('/api/expenses') }}';

        if (expId) {
            apiUrl = '{{ url('/api/expenses') }}/' + expId;
            formData.append('_method', 'PUT');
        }

        axios.post(apiUrl, formData, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(res => {
            Toastify({
                text: expId ? "Expense Updated Successfully!" : "Expense Registered Successfully!",
                gravity: "top",
                position: "right",
                style: {
                    background: "#10b981"
                }
            }).showToast();

            expenseModal.classList.add('hidden');
            fetchExpenses(currentPage);
        }).catch(err => {
            let errorMsg = 'Action failed';
            if (err.response && err.response.data && err.response.data.errors) {
                errorMsg = Object.values(err.response.data.errors).flat().join('\n');
            } else if (err.response && err.response.data && err.response.data.message) {
                errorMsg = err.response.data.message;
            }
            Swal.fire({
                icon: 'error',
                title: 'Submission Failed',
                text: errorMsg
            });
        });
    });

    // Fetch Expenses AJAX
    function fetchExpenses(page = 1) {
        currentPage = page;
        const tbody = document.getElementById('expenseTableBody');
        const searchInput = document.getElementById('expenseSearch') || document.getElementById('searchDesktop') || document.querySelector('input[name="search"]');
        const searchVal = searchInput ? searchInput.value : '';

        axios.get('{{ url('/api/expenses') }}', {
            params: {
                page: page,
                search: searchVal
            }
        }).then(res => {
            if (!tbody) {
                window.location.reload();
                return;
            }
            const expensesData = res.data.data || res.data;
            const meta = res.data.meta || {
                current_page: res.data.current_page || 1,
                per_page: res.data.per_page || 30,
                from: res.data.from || 1
            };

            tbody.innerHTML = '';
            if (expensesData.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" class="border border-gray-300 px-3 py-10 text-center text-gray-500">No expenses found.</td></tr>`;
                return;
            }

            expensesData.forEach((exp, index) => {
                const sl = (meta.from || 1) + index;
                let dateVal = exp.date || '-';
                if (dateVal.includes('T')) dateVal = dateVal.split('T')[0];

                const invoiceNo = exp.invoice_no || '-';
                const reason = exp.expense_reason || exp.name || '-';
                const details = exp.details || '-';

                const amountFormatted = parseFloat(exp.amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                tbody.innerHTML += `
                    <tr class="hover:bg-gray-50">
                        <td class="h-8 border border-gray-300 px-3 text-center">${sl}</td>
                        <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">${dateVal}</td>
                        <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center font-medium">${invoiceNo}</td>
                        <td class="h-8 border border-gray-300 px-3">
                            <div class="donate-cell-scroll" title="${reason}">${reason}</div>
                        </td>
                        <td class="h-8 border border-gray-300 px-3">
                            <div class="donate-cell-scroll" title="${details}">${details}</div>
                        </td>
                        <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-right font-medium">৳${amountFormatted}</td>
                        <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center min-w-[90px]">
                            <div class="flex h-6 w-full items-center justify-center space-x-1">
                                <button type="button" onclick="editExpense(${exp.id})" class="text-blue-600 hover:text-blue-800 p-1" title="Edit Expense">
                                    <i class="far fa-edit text-xs"></i>
                                </button>
                                <button type="button" onclick="deleteExpense(${exp.id})" class="text-red-600 hover:text-red-800 p-1" title="Delete Expense">
                                    <i class="far fa-trash-alt text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }).catch(err => {
            console.error("Fetch expenses failed", err);
        });
    }

    // Search input binding
    const searchEl = document.getElementById('expenseSearch') || document.getElementById('searchDesktop') || document.querySelector('input[name="search"]');
    if (searchEl) {
        let searchTimer;
        searchEl.addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => fetchExpenses(1), 400);
        });
    }

    // Filter Modal Event Handlers
    const filterModal = document.getElementById('filterModal');
    const btnFilter = document.getElementById('btnFilter');
    const closeFilterModal = document.getElementById('closeFilterModal');
    const resetFilter = document.getElementById('resetFilter');
    const applyFilter = document.getElementById('applyFilter');
    const expenseFilterForm = document.getElementById('expenseFilterForm');

    if (btnFilter && filterModal) {
        btnFilter.addEventListener('click', () => {
            filterModal.classList.remove('hidden');
        });
    }

    if (closeFilterModal && filterModal) {
        closeFilterModal.addEventListener('click', () => {
            filterModal.classList.add('hidden');
        });
    }

    if (resetFilter) {
        resetFilter.addEventListener('click', () => {
            window.location.href = '{{ route('school.expense') }}';
        });
    }

    if (applyFilter && expenseFilterForm) {
        applyFilter.addEventListener('click', () => {
            expenseFilterForm.submit();
        });
    }
</script>
