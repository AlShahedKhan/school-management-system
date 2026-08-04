<script>
    const payrollModal = document.getElementById('payrollModal');
    
    document.getElementById('openPayrollModal').addEventListener('click', () => {
        payrollModal.classList.remove('hidden');
        resetPayrollForm();
    });
    
    document.getElementById('closePayrollModal').addEventListener('click', () => {
        payrollModal.classList.add('hidden');
    });

    // Helper function to reset custom dropdown select
    function resetDropdownSelect(id) {
        const input = document.getElementById(id);
        if (!input) return;
        
        const root = input.closest('[data-dropdown-select]');
        if (!root) return;
        
        const label = root.querySelector('[data-dropdown-select-label]');
        const placeholder = label ? label.dataset.placeholder : 'Select...';
        
        input.value = '';
        if (label) {
            label.textContent = placeholder;
        }
        
        root.querySelectorAll('[data-dropdown-select-option]').forEach((item) => {
            item.classList.remove('bg-slate-100', 'text-slate-900');
            item.classList.add('text-slate-800');
            item.setAttribute('aria-selected', 'false');
        });
    }

    function resetPayrollForm() {
        const payrollIdEl = document.getElementById('payroll_id');
        if (payrollIdEl) payrollIdEl.value = '';

        const titleEl = document.querySelector('.payroll-register-modal-title');
        if (titleEl) titleEl.textContent = 'Pay Salary / Payroll';

        // Reset radio button to employee
        const employeeRadio = document.querySelector('input[name="type"][value="employee"]');
        if (employeeRadio) {
            employeeRadio.checked = true;
            toggleStaffType('employee');
        }
        
        resetDropdownSelect('payrollEmployeeId');
        resetDropdownSelect('payrollTeacherId');
        
        // Hide details card and reset values
        const detailsCard = document.getElementById('staffDetailsCard');
        if (detailsCard) detailsCard.classList.add('hidden');
        
        const dueText = document.getElementById('cardDue');
        if (dueText) dueText.textContent = '৳0.00';
        
        const overdueText = document.getElementById('cardOverdue');
        if (overdueText) overdueText.textContent = '৳0.00';
        
        const totalDueText = document.getElementById('cardTotalDue');
        if (totalDueText) totalDueText.textContent = '৳0.00';
        
        // Reset inputs
        const amountInput = document.getElementById('payrollAmount');
        if (amountInput) amountInput.value = '';
        
        const noteInput = document.getElementById('payrollNote');
        if (noteInput) noteInput.value = '';
    }

    function toggleStaffType(type) {
        const empWrapper = document.getElementById('employeeSelectWrapper');
        const teachWrapper = document.getElementById('teacherSelectWrapper');
        const detailsCard = document.getElementById('staffDetailsCard');

        if (type === 'teacher') {
            empWrapper.classList.add('hidden');
            teachWrapper.classList.remove('hidden');
            resetDropdownSelect('payrollEmployeeId');
        } else {
            teachWrapper.classList.add('hidden');
            empWrapper.classList.remove('hidden');
            resetDropdownSelect('payrollTeacherId');
        }
        
        if (detailsCard) detailsCard.classList.add('hidden');
    }

    // Toggle on radio button click
    document.querySelectorAll('input[name="type"]').forEach((radio) => {
        radio.addEventListener('change', (e) => {
            toggleStaffType(e.target.value);
        });
    });

    // Fetch details on change
    function fetchStaffDetails(type, id) {
        if (!id) {
            document.getElementById('staffDetailsCard').classList.add('hidden');
            return;
        }

        axios.get(`/api/payrolls/staff-details?type=${type}&id=${id}`)
            .then((response) => {
                const data = response.data;
                document.getElementById('cardDesignation').textContent = data.designation;
                document.getElementById('cardMobile').textContent = data.mobile;
                document.getElementById('cardSalary').textContent = '৳' + Number(data.salary).toLocaleString('en-US', { minimumFractionDigits: 2 });
                document.getElementById('cardDue').textContent = '৳' + Number(data.due).toLocaleString('en-US', { minimumFractionDigits: 2 });
                document.getElementById('cardOverdue').textContent = '৳' + Number(data.overdue).toLocaleString('en-US', { minimumFractionDigits: 2 });
                document.getElementById('cardTotalDue').textContent = '৳' + Number(data.total_due).toLocaleString('en-US', { minimumFractionDigits: 2 });
                
                document.getElementById('staffDetailsCard').classList.remove('hidden');
                
                // Auto-fill pay amount with due amount (default to total due if there is overdue, otherwise monthly salary)
                const amountInput = document.getElementById('payrollAmount');
                if (amountInput) {
                    amountInput.value = data.total_due > 0 ? data.total_due : data.salary;
                }
            })
            .catch((error) => {
                console.error('Error fetching staff details:', error);
            });
    }

    // Listen to changes on custom dropdowns
    const empInput = document.getElementById('payrollEmployeeId');
    if (empInput) {
        empInput.addEventListener('change', (e) => {
            fetchStaffDetails('employee', e.target.value);
        });
    }

    const teachInput = document.getElementById('payrollTeacherId');
    if (teachInput) {
        teachInput.addEventListener('change', (e) => {
            fetchStaffDetails('teacher', e.target.value);
        });
    }

    // Payment Method validation listener (Gateway Unavailable for Bank)
    const payMethodInput = document.getElementById('payrollPaymentMethod');
    if (payMethodInput) {
        payMethodInput.addEventListener('change', function () {
            if (this.value === 'bank') {
                Swal.fire({
                    title: 'Gateway Unavailable',
                    text: 'Bank payment is not available right now. Please pay with cash.',
                    icon: 'info',
                    confirmButtonColor: '#2563eb',
                    confirmButtonText: 'Understood'
                });
                if (typeof setDropdownValue === 'function') {
                    setDropdownValue('payrollPaymentMethod', 'cash', 'Cash');
                } else {
                    this.value = 'cash';
                }
            }
        });
    }
</script>