<script>
    document.getElementById('expenseForm').addEventListener('submit', function(e) {
        e.preventDefault();
        clearExpenseErrors();
        const expenseId = document.getElementById('expense_id').value;
        const formData = new FormData(this);
        if (expenseId) {
            formData.append('_method', 'PUT');
        }
        const apiUrl = expenseId
            ? `{{ url('/api/expenses') }}/${expenseId}`
            : `{{ url('/api/expenses') }}`;
        axios.post(apiUrl, formData, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'multipart/form-data'
            }
        })
        .then(() => {
            Toastify({
                text: "Expense Saved Successfully!",
                gravity: "top",
                position: "right",
                style: {
                    background: "#10b981"
                }
            }).showToast();

            expenseModal.classList.add('hidden');

            setTimeout(() => {
                window.location.reload();
            }, 500);
        })
        .catch(error => {
            if (error.response?.status === 422) {
                showExpenseErrors(error.response.data.errors);
                return;
            }

            Swal.fire({
                icon: 'error',
                title: 'Submission Failed',
                text: error.response?.data?.message || 'Something went wrong.'
            });
        });
    });
</script>