<script>
    document.getElementById('feeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        clearFeeErrors();

        const feeType = document.getElementById('fee_type_name').value;
        if (feeType === 'Fine') {
            const fineVal = document.getElementById('fine_fee_name_select').value;
            document.getElementById('fee_name_input').value = fineVal || '';
        }

        const recordId = document.getElementById('record_id').value;
        const formData = new FormData(this);
        if (recordId) {
            formData.append('_method', 'PUT');
        }
        const apiUrl = recordId
            ? `{{ url('/api/fee-templates') }}/${recordId}`
            : `{{ url('/api/fee-templates') }}`;
        axios.post(apiUrl, formData, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'multipart/form-data'
            }
        })
        .then(() => {
            Toastify({
                text: 'Fee Template Saved Successfully!',
                gravity: 'top',
                position: 'right',
                style: { background: '#10b981' }
            }).showToast();
            document.getElementById('feeModal').classList.add('hidden');
            setTimeout(() => { window.location.reload(); }, 500);
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
