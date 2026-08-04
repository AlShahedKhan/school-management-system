<script>
    function setDropdownValue(id, value, placeholder = 'Select...') {
        const input = document.getElementById(id);
        if (!input) return;
        input.value = value || '';

        const root = input.closest('[data-dropdown-select]');
        if (!root) return;

        const label = root.querySelector('[data-dropdown-select-label]');
        let selectedText = placeholder;

        root.querySelectorAll('[data-dropdown-select-option]').forEach(opt => {
            const isSelected = (opt.dataset.value || '') === String(value || '');
            opt.classList.toggle('bg-slate-100', isSelected);
            opt.classList.toggle('text-slate-900', isSelected);
            opt.classList.toggle('text-slate-800', !isSelected);
            opt.setAttribute('aria-selected', String(isSelected));
            if (isSelected) {
                selectedText = opt.textContent.trim();
            }
        });

        if (label) label.textContent = selectedText;
    }

    // Submit Form
    const payrollForm = document.getElementById('payrollForm');
    if (payrollForm) {
        payrollForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const submitBtn = document.getElementById('submitPayrollBtn');
            if (submitBtn) submitBtn.disabled = true;

            const payrollId = document.getElementById('payroll_id')?.value;
            const formData = new FormData(this);

            let apiUrl = '{{ url('/api/payrolls') }}';
            if (payrollId) {
                apiUrl = '{{ url('/api/payrolls') }}/' + payrollId;
                formData.append('_method', 'PUT');
            }

            axios.post(apiUrl, formData, {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(res => {
                Toastify({
                    text: payrollId ? "Payroll Updated Successfully!" : "Payroll Saved Successfully!",
                    gravity: "top",
                    position: "right",
                    style: {
                        background: "#10b981"
                    }
                }).showToast();

                document.getElementById('payrollModal')?.classList.add('hidden');
                setTimeout(() => window.location.reload(), 500);
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
            }).finally(() => {
                if (submitBtn) submitBtn.disabled = false;
            });
        });
    }

    // Edit Payroll
    function editPayroll(id) {
        axios.get('{{ url('/api/payrolls') }}/' + id)
            .then(res => {
                const payroll = res.data.data || res.data;
                document.getElementById('payroll_id').value = payroll.id;

                const type = payroll.type || (payroll.teacher_id ? 'teacher' : 'employee');
                const radio = document.querySelector(`input[name="type"][value="${type}"]`);
                if (radio) radio.checked = true;
                if (typeof toggleStaffType === 'function') toggleStaffType(type);

                if (type === 'teacher') {
                    setDropdownValue('payrollTeacherId', payroll.teacher_id, 'Select Teacher...');
                    if (typeof fetchStaffDetails === 'function') fetchStaffDetails('teacher', payroll.teacher_id);
                } else {
                    setDropdownValue('payrollEmployeeId', payroll.employee_id, 'Select Employee...');
                    if (typeof fetchStaffDetails === 'function') fetchStaffDetails('employee', payroll.employee_id);
                }

                setDropdownValue('payrollPayType', payroll.pay_type || 'running', 'Select Pay Type...');
                setDropdownValue('payrollMonth', String(payroll.receive_month || ''), 'Select Month...');
                setDropdownValue('payrollYear', String(payroll.receive_year || ''), 'Select Year...');

                let rDate = payroll.receive_date || '';
                if (rDate.includes('T')) rDate = rDate.split('T')[0];
                document.getElementById('payrollReceiveDate').value = rDate;

                document.getElementById('payrollAmount').value = payroll.receive_amount || '';
                setDropdownValue('payrollPaymentMethod', payroll.payment_method || 'cash', 'Payment Method...');
                document.getElementById('payrollNote').value = payroll.note || '';

                const titleEl = document.querySelector('.payroll-register-modal-title');
                if (titleEl) titleEl.textContent = 'Edit Payroll Record';

                document.getElementById('payrollModal')?.classList.remove('hidden');
            })
            .catch(err => {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load payroll record.'
                });
            });
    }

    // Delete Payroll
    function deletePayroll(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently remove this payroll record!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete('{{ url('/api/payrolls') }}/' + id, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(() => {
                    Toastify({
                        text: "Payroll record deleted successfully!",
                        gravity: "top",
                        position: "right",
                        style: {
                            background: "#ef4444"
                        }
                    }).showToast();
                    setTimeout(() => window.location.reload(), 500);
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
</script>
