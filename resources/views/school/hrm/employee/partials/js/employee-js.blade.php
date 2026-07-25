<script>
    const employeeModal = document.getElementById('employeeModal');
    const employeeForm = document.getElementById('employeeForm');
    let currentPage = 1;

    function sanitizeEnglishDigits(value) {
        return value.replace(/[^0-9]/g, '');
    }

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
        const inputs = employeeForm.querySelectorAll('input:not([type="hidden"])');
        inputs.forEach(input => {
            input.readOnly = !editable;
            if (!editable) {
                input.classList.add('bg-gray-100', 'cursor-not-allowed');
            } else {
                input.classList.remove('bg-gray-100', 'cursor-not-allowed');
            }
        });
        setDropdownDisabled('employeePayDate', !editable);
        setDropdownDisabled('employeeStatus', !editable);
    }

    function enableFormEditing() {
        setFormEditable(true);
        const nameInput = document.querySelector('#employeeForm input[name="name"]');
        if (nameInput) nameInput.focus();
    }

    const mobileInput = document.querySelector('#employeeForm input[name="mobile_number"]');
    if (mobileInput) {
        mobileInput.addEventListener('input', function() {
            this.value = sanitizeEnglishDigits(this.value);
        });
    }

    // Open Add Modal
    document.getElementById('openEmployeeModal')?.addEventListener('click', () => {
        employeeForm.reset();
        document.getElementById('employee_id').value = '';
        setDropdownValue('employeePayDate', '', 'Select Pay Date...');
        setDropdownValue('employeeStatus', 'active', 'Active');

        setFormEditable(true);

        const titleEl = document.getElementById('employeeModalTitle');
        if (titleEl) titleEl.textContent = 'Employee Registration';
        
        const enableEditBtn = document.getElementById('enableEditBtn');
        if (enableEditBtn) enableEditBtn.classList.add('hidden');

        const submitBtn = document.getElementById('submitEmployeeBtn');
        if (submitBtn) submitBtn.textContent = 'Save';

        const footer = document.getElementById('employeeModalFooter');
        if (footer) footer.className = 'grid grid-cols-2 gap-3 border-slate-200 bg-white px-6 pt-3 pb-4';

        employeeModal.classList.remove('hidden');
    });

    // Close Modal
    document.getElementById('closeEmployeeModal')?.addEventListener('click', () => {
        employeeModal.classList.add('hidden');
    });

    // Edit Employee
    function editEmployee(id) {
        axios.get('{{ url('/api/employees') }}/' + id)
            .then(res => {
                const emp = res.data.data || res.data;
                document.getElementById('employee_id').value = emp.id;
                document.querySelector('#employeeForm input[name="name"]').value = emp.name || '';
                document.querySelector('#employeeForm input[name="designation"]').value = emp.designation || '';
                document.querySelector('#employeeForm input[name="mobile_number"]').value = emp.mobile_number || '';
                document.querySelector('#employeeForm input[name="salary_amount"]').value = emp.salary_amount || '';
                
                let startDate = emp.salary_start_date || '';
                if (startDate.includes('T')) startDate = startDate.split('T')[0];
                document.querySelector('#employeeForm input[name="salary_start_date"]').value = startDate;

                setDropdownValue('employeePayDate', emp.pay_date || '', 'Select Pay Date...');

                const stVal = typeof emp.employee_status === 'object' ? emp.employee_status.value : emp.employee_status;
                setDropdownValue('employeeStatus', stVal || 'active', 'Active');

                // Initial state: Not editable
                setFormEditable(false);

                const titleEl = document.getElementById('employeeModalTitle');
                if (titleEl) titleEl.textContent = 'Edit Employee';

                const enableEditBtn = document.getElementById('enableEditBtn');
                if (enableEditBtn) enableEditBtn.classList.remove('hidden');

                const submitBtn = document.getElementById('submitEmployeeBtn');
                if (submitBtn) submitBtn.textContent = 'Save';

                const footer = document.getElementById('employeeModalFooter');
                if (footer) footer.className = 'grid grid-cols-3 gap-2 border-slate-200 bg-white px-6 pt-3 pb-4';

                employeeModal.classList.remove('hidden');
            })
            .catch(err => {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load employee details.'
                });
            });
    }

    // Delete Employee with SweetAlert2
    function deleteEmployee(id) {
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
                axios.delete('{{ url('/api/employees') }}/' + id, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(() => {
                    Toastify({
                        text: "Employee deleted successfully!",
                        gravity: "top",
                        position: "right",
                        style: {
                            background: "#ef4444"
                        }
                    }).showToast();
                    if (!employeeModal.classList.contains('hidden')) {
                        employeeModal.classList.add('hidden');
                    }
                    fetchEmployees(currentPage);
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
    employeeForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const empId = document.getElementById('employee_id').value;
        const formData = new FormData(this);

        let apiUrl = '{{ url('/api/employees') }}';

        if (empId) {
            apiUrl = '{{ url('/api/employees') }}/' + empId;
            formData.append('_method', 'PUT');
        }

        axios.post(apiUrl, formData, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(res => {
            Toastify({
                text: empId ? "Employee Updated Successfully!" : "Employee Registered Successfully!",
                gravity: "top",
                position: "right",
                style: {
                    background: "#10b981"
                }
            }).showToast();

            employeeModal.classList.add('hidden');
            fetchEmployees(currentPage);
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

    // Fetch Employees AJAX
    function fetchEmployees(page = 1) {
        currentPage = page;
        const tbody = document.getElementById('employeeTableBody');
        const searchInput = document.getElementById('employeeSearch') || document.getElementById('searchDesktop') || document.querySelector('input[name="search"]');
        const searchVal = searchInput ? searchInput.value : '';

        axios.get('{{ url('/api/employees') }}', {
            params: {
                page: page,
                search: searchVal
            }
        }).then(res => {
            if (!tbody) {
                window.location.reload();
                return;
            }
            const employeesData = res.data.data || res.data;
            const meta = res.data.meta || {
                current_page: res.data.current_page || 1,
                per_page: res.data.per_page || 30,
                from: res.data.from || 1
            };

            tbody.innerHTML = '';
            if (employeesData.length === 0) {
                tbody.innerHTML = `<tr><td colspan="9" class="border border-gray-300 px-3 py-10 text-center text-gray-500">No employees found.</td></tr>`;
                return;
            }

            employeesData.forEach((emp, index) => {
                const sl = (meta.from || 1) + index;
                const statusVal = typeof emp.employee_status === 'object' ? emp.employee_status.value : emp.employee_status;
                const statusLabel = typeof emp.employee_status === 'object' ? emp.employee_status.label : (statusVal ? statusVal.charAt(0).toUpperCase() + statusVal.slice(1) : 'Active');
                const statusClass = (statusVal || '').toLowerCase() === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';
                
                let startDate = emp.salary_start_date || '-';
                if (startDate.includes('T')) startDate = startDate.split('T')[0];

                const salaryFormatted = parseFloat(emp.salary_amount || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                tbody.innerHTML += `
                    <tr class="hover:bg-gray-50">
                        <td class="h-8 border border-gray-300 px-3 text-center">${sl}</td>
                        <td class="h-8 border border-gray-300 px-3 font-medium">
                            <div class="donate-cell-scroll" title="${emp.name}">${emp.name}</div>
                        </td>
                        <td class="h-8 border border-gray-300 px-3">${emp.designation || '-'}</td>
                        <td class="h-8 whitespace-nowrap border border-gray-300 px-3">
                            <a href="tel:${emp.mobile_number}" class="inline-block text-blue-500" style="text-decoration: none !important;">${emp.mobile_number || '-'}</a>
                        </td>
                        <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-right font-medium">৳${salaryFormatted}</td>
                        <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">${startDate}</td>
                        <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                            <span class="rounded px-2 py-0.5 text-[10px] font-semibold ${statusClass}">${statusLabel}</span>
                        </td>
                        <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">${emp.pay_date || '-'}</td>
                        <td class="h-8 whitespace-nowrap border border-gray-300 px-3 text-center">
                            <div class="flex h-6 w-full items-center justify-center space-x-1">
                                <button type="button" onclick="editEmployee(${emp.id})" class="text-blue-600 hover:text-blue-800 p-1" title="Edit ${emp.name}">
                                    <i class="far fa-edit text-xs"></i>
                                </button>
                                <button type="button" onclick="deleteEmployee(${emp.id})" class="text-red-600 hover:text-red-800 p-1" title="Delete ${emp.name}">
                                    <i class="far fa-trash-alt text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }).catch(err => {
            console.error("Fetch employees failed", err);
        });
    }

    // Search input binding
    const searchEl = document.getElementById('employeeSearch') || document.getElementById('searchDesktop') || document.querySelector('input[name="search"]');
    if (searchEl) {
        let searchTimer;
        searchEl.addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => fetchEmployees(1), 400);
        });
    }
</script>
