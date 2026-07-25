<script>
    const expenseModal = document.getElementById('expenseModal');
    const expenseForm = document.getElementById('expenseForm');
    let currentPage = 1;

    const monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];

    function setDropdownValue(id, value, placeholder = 'Select...') {
        const input = document.getElementById(id);
        const label = document.querySelector(`#${id}Button [data-dropdown-select-label]`);
        const menu = document.getElementById(`${id}Menu`);
        if (input) input.value = value || '';

        let selectedText = placeholder;
        if (menu) {
            menu.querySelectorAll('[data-dropdown-select-option]').forEach(opt => {
                const isSelected = (opt.dataset.value || '') === String(value || '');
                opt.classList.toggle('bg-slate-100', isSelected);
                opt.classList.toggle('text-slate-900', isSelected);
                opt.classList.toggle('text-slate-800', !isSelected);
                opt.setAttribute('aria-selected', String(isSelected));
                if (isSelected) {
                    selectedText = opt.textContent.trim();
                }
            });
        }
        if (label) label.textContent = selectedText;
    }

    function setDropdownDisabled(id, disabled) {
        const btn = document.getElementById(`${id}Button`);
        if (btn) {
            btn.disabled = disabled;
            btn.classList.toggle('pointer-events-none', disabled);
            btn.classList.toggle('bg-gray-100', disabled);
            btn.classList.toggle('opacity-75', disabled);
        }
    }

    function setFormEditable(editable) {
        const inputs = expenseForm.querySelectorAll('input:not([type="hidden"])');
        inputs.forEach(input => {
            input.readOnly = !editable;
            if (!editable) {
                input.classList.add('bg-gray-100', 'cursor-not-allowed');
            } else {
                input.classList.remove('bg-gray-100', 'cursor-not-allowed');
            }
        });
        setDropdownDisabled('expenseMonth', !editable);
        setDropdownDisabled('expenseYear', !editable);
    }

    function enableFormEditing() {
        setFormEditable(true);
        const dateInput = document.querySelector('#expenseForm input[name="date"]');
        if (dateInput) dateInput.focus();
    }

    // Auto update Month & Year when Date changes
    const expenseDateInput = document.querySelector('#expenseForm input[name="date"]');
    if (expenseDateInput) {
        expenseDateInput.addEventListener('change', function() {
            if (this.value) {
                const d = new Date(this.value);
                if (!isNaN(d.getTime())) {
                    const monthName = monthNames[d.getMonth()];
                    const yearStr = String(d.getFullYear());
                    setDropdownValue('expenseMonth', monthName, 'Select Month...');
                    setDropdownValue('expenseYear', yearStr, 'Select Year...');
                }
            }
        });
    }

    // Open Add Modal
    document.getElementById('openExpenseModal')?.addEventListener('click', () => {
        expenseForm.reset();
        document.getElementById('expense_id').value = '';
        
        const todayStr = new Date().toISOString().split('T')[0];
        const todayDate = new Date();
        const curMonth = monthNames[todayDate.getMonth()];
        const curYear = String(todayDate.getFullYear());

        const dateEl = document.querySelector('#expenseForm input[name="date"]');
        if (dateEl) dateEl.value = todayStr;

        setDropdownValue('expenseMonth', curMonth, 'Select Month...');
        setDropdownValue('expenseYear', curYear, 'Select Year...');

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

                document.querySelector('#expenseForm input[name="expense_reason"]').value = exp.expense_reason || exp.name || '';
                document.querySelector('#expenseForm input[name="amount"]').value = exp.amount || '';

                let mVal = exp.month;
                let yVal = exp.year;
                if (!mVal && dateVal) {
                    const d = new Date(dateVal);
                    if (!isNaN(d.getTime())) {
                        mVal = monthNames[d.getMonth()];
                        yVal = String(d.getFullYear());
                    }
                }

                setDropdownValue('expenseMonth', mVal || '', 'Select Month...');
                setDropdownValue('expenseYear', yVal || '', 'Select Year...');

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

                let monthVal = exp.month;
                let yearVal = exp.year;
                if (!monthVal && dateVal !== '-') {
                    const d = new Date(dateVal);
                    if (!isNaN(d.getTime())) {
                        monthVal = monthNames[d.getMonth()];
                        yearVal = String(d.getFullYear());
                    }
                }

                const amountFormatted = parseFloat(exp.amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                tbody.innerHTML += `
                    <tr class="hover:bg-gray-50">
                        <td class="h-8 border border-gray-300 px-3 text-center">${sl}</td>
                        <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">${dateVal}</td>
                        <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">${yearVal || '-'}</td>
                        <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">${monthVal || '-'}</td>
                        <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-right font-medium">৳${amountFormatted}</td>
                        <td class="h-8 border border-gray-300 px-3 font-medium">
                            <div class="donate-cell-scroll" title="${exp.expense_reason || exp.name}">${exp.expense_reason || exp.name}</div>
                        </td>
                        <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
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
</script>
