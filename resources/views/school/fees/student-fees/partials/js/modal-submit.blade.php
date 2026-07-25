<script>
    document.getElementById('feeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        clearFeeErrors();
        const rawId = document.getElementById('fee_id').value;
        const formData = new FormData(this);
        formData.append('_method', 'PUT');
        const apiUrl = `{{ url('/api/student-fees') }}/${rawId}`;
        axios.post(apiUrl, formData, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'multipart/form-data'
            }
        })
        .then(() => {
            Toastify({
                text: 'Fee Updated Successfully!',
                gravity: 'top',
                position: 'right',
                style: { background: '#10b981' }
            }).showToast();
            document.getElementById('feeModal').classList.add('hidden');
            fetchFees(currentPage);
        })
        .catch(error => {
            if (error.response?.status === 422) {
                showFeeErrors(error.response.data.errors);
                return;
            }
            Swal.fire({
                icon: 'error',
                title: 'Update Failed',
                text: error.response?.data?.message || 'Something went wrong.'
            });
        });
    });
</script>
