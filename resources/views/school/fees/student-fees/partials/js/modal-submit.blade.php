<script>
    document.getElementById('feeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        clearFeeErrors();

        const recordId = document.getElementById('record_id').value;
        const formData = new FormData(this);
        if (recordId) {
            formData.append('_method', 'PUT');
        }
        const apiUrl = recordId
            ? `{{ url('/api/student-fees') }}/${recordId}`
            : `{{ url('/api/student-fees') }}`;
        axios.post(apiUrl, formData, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'multipart/form-data'
            }
        })
        .then(() => {
            Toastify({
                text: 'Student Fee Updated Successfully!',
                gravity: 'top',
                position: 'right',
                style: { background: '#10b981' }
            }).showToast();
            document.getElementById('feeModal').classList.add('hidden');
            fetchStudentFees(currentPage);
        })
        .catch(error => {
            if (error.response?.status === 422) {
                showFeeErrors(error.response.data.errors);
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
